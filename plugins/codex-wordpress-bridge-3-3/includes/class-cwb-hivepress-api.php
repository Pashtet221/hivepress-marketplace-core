<?php
/** HivePress-specific, schema-aware REST resources. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class CWB_HivePress_API {
	private $bridge;

	public function __construct( CWB_Plugin $bridge ) {
		$this->bridge = $bridge;
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public function register_routes() {
		$read  = array( 'permission_callback' => array( $this, 'can_read' ) );
		$write = array( 'permission_callback' => array( $this, 'can_write' ) );

		$this->route( '/taxonomies', 'GET', 'get_taxonomies', $read );
		$this->route( '/taxonomies/(?P<taxonomy>[a-z0-9_-]+)/terms', 'GET', 'get_terms', $read );
		$this->route( '/taxonomies/(?P<taxonomy>[a-z0-9_-]+)/terms', 'POST', 'create_term', $write );
		$this->route( '/taxonomies/(?P<taxonomy>[a-z0-9_-]+)/terms/(?P<id>\d+)', 'GET', 'get_term', $read );
		$this->route( '/taxonomies/(?P<taxonomy>[a-z0-9_-]+)/terms/(?P<id>\d+)', 'PATCH', 'update_term', $write );

		$this->route( '/hivepress/listing-attributes', 'GET', 'get_attributes', $read );
		$this->route( '/hivepress/listing-attributes', 'POST', 'create_attribute', $write );
		$this->route( '/hivepress/listing-attributes/(?P<id>\d+)', 'GET', 'get_attribute', $read );
		$this->route( '/hivepress/listing-attributes/(?P<id>\d+)', 'PATCH', 'update_attribute', $write );
		$this->route( '/hivepress/listing-attributes/(?P<id>\d+)/options', 'GET', 'get_options', $read );
		$this->route( '/hivepress/listing-attributes/(?P<id>\d+)/options', 'POST', 'create_option', $write );
		$this->route( '/hivepress/listing-attributes/(?P<id>\d+)/options/(?P<option_id>\d+)', 'PATCH', 'update_option', $write );

		$this->route( '/hivepress/listings/schema', 'GET', 'listing_schema', $read );
		$this->route( '/hivepress/listings', 'GET', 'get_listings', $read );
		$this->route( '/hivepress/listings', 'POST', 'create_listing', $write );
		$this->route( '/hivepress/listings/(?P<id>\d+)', 'GET', 'get_listing', $read );
		$this->route( '/hivepress/listings/(?P<id>\d+)', 'PATCH', 'update_listing', $write );

		$this->route( '/hivepress/vendors', 'GET', 'get_vendors', $read );
		$this->route( '/hivepress/vendors', 'POST', 'create_vendor', $write );
		$this->route( '/hivepress/vendors/(?P<id>\d+)', 'GET', 'get_vendor', $read );
		$this->route( '/hivepress/vendors/(?P<id>\d+)', 'PATCH', 'update_vendor', $write );
		$this->route( '/users', 'GET', 'get_users', $read );
		$this->route( '/media', 'GET', 'get_media', $read );
		$this->route( '/media/(?P<id>\d+)', 'GET', 'get_media_item', $read );
	}

	private function route( $path, $methods, $callback, $extra ) {
		register_rest_route( CWB_Plugin::REST_NAMESPACE, $path, array_merge( array( 'methods' => $methods, 'callback' => array( $this, $callback ) ), $extra ) );
	}

	public function can_read() { return current_user_can( CWB_Plugin::CAPABILITY ); }
	public function can_write() { return current_user_can( CWB_Plugin::CAPABILITY ); }

	private function error( $code, $message, $status = 400 ) { return new WP_Error( $code, $message, array( 'status' => $status ) ); }
	private function json( WP_REST_Request $request ) { $data = $request->get_json_params(); return is_array( $data ) ? $data : array(); }
	private function hp_ready() { return post_type_exists( 'hp_listing' ) && post_type_exists( 'hp_listing_attribute' ); }
	private function post_cap( $post_type, $operation, $id = 0 ) {
		$object = get_post_type_object( $post_type );
		if ( ! $object ) return false;
		if ( 'create' === $operation ) return current_user_can( $object->cap->create_posts );
		return $id && current_user_can( 'edit_post', $id );
	}

	private function allowed_taxonomy( $name ) {
		$taxonomy = get_taxonomy( sanitize_key( $name ) );
		if ( ! $taxonomy ) return $this->error( 'cwb_taxonomy_not_found', 'Taxonomy is not registered.', 404 );
		$allowed_types = array( 'page', 'post', 'plugin', 'wpds-case', 'service' );
		foreach ( get_post_types( array(), 'names' ) as $type ) if ( 0 === strpos( $type, 'hp_' ) ) $allowed_types[] = $type;
		if ( ! array_intersect( (array) $taxonomy->object_type, $allowed_types ) ) return $this->error( 'cwb_taxonomy_forbidden', 'Taxonomy is outside the Bridge post type allowlist.', 403 );
		return $taxonomy;
	}

	public function get_taxonomies() {
		$items = array();
		foreach ( get_taxonomies( array(), 'objects' ) as $taxonomy ) {
			if ( is_wp_error( $this->allowed_taxonomy( $taxonomy->name ) ) ) continue;
			$manage = ! empty( $taxonomy->cap->manage_terms ) && current_user_can( $taxonomy->cap->manage_terms );
			$items[] = array(
				'name' => $taxonomy->name, 'label' => $taxonomy->label,
				'object_types' => array_values( (array) $taxonomy->object_type ), 'hierarchical' => (bool) $taxonomy->hierarchical,
				'capabilities' => (array) $taxonomy->cap, 'hivepress' => 0 === strpos( $taxonomy->name, 'hp_' ),
				'writable' => $manage, 'supported_operations' => $manage ? array( 'list', 'get', 'create', 'update' ) : array( 'list', 'get' ),
			);
		}
		return rest_ensure_response( array( 'items' => $items ) );
	}

	private function term_data( $term ) {
		$link = get_term_link( $term );
		return array( 'id' => (int) $term->term_id, 'taxonomy' => $term->taxonomy, 'name' => $term->name, 'slug' => $term->slug, 'description' => $term->description, 'parent' => (int) $term->parent, 'count' => (int) $term->count, 'url' => is_wp_error( $link ) ? null : $link );
	}

	public function get_terms( WP_REST_Request $request ) {
		$taxonomy = $this->allowed_taxonomy( $request['taxonomy'] ); if ( is_wp_error( $taxonomy ) ) return $taxonomy;
		$page = max( 1, absint( $request->get_param( 'page' ) ?: 1 ) ); $per_page = min( 100, max( 1, absint( $request->get_param( 'per_page' ) ?: 20 ) ) );
		$args = array( 'taxonomy' => $taxonomy->name, 'hide_empty' => filter_var( $request->get_param( 'hide_empty' ), FILTER_VALIDATE_BOOLEAN ), 'number' => $per_page, 'offset' => ( $page - 1 ) * $per_page );
		foreach ( array( 'search', 'slug' ) as $key ) if ( null !== $request->get_param( $key ) && '' !== $request->get_param( $key ) ) $args[ $key ] = sanitize_text_field( $request->get_param( $key ) );
		if ( null !== $request->get_param( 'parent' ) && '' !== $request->get_param( 'parent' ) ) $args['parent'] = absint( $request->get_param( 'parent' ) );
		$terms = get_terms( $args ); if ( is_wp_error( $terms ) ) return $terms;
		$count_args = $args; unset( $count_args['number'], $count_args['offset'] ); $count_args['fields'] = 'count'; $total = (int) get_terms( $count_args );
		return rest_ensure_response( array( 'items' => array_map( array( $this, 'term_data' ), $terms ), 'total' => $total, 'total_pages' => (int) ceil( $total / $per_page ), 'page' => $page ) );
	}

	public function get_term( WP_REST_Request $request ) {
		$taxonomy = $this->allowed_taxonomy( $request['taxonomy'] ); if ( is_wp_error( $taxonomy ) ) return $taxonomy;
		$term = get_term( absint( $request['id'] ), $taxonomy->name ); if ( ! $term || is_wp_error( $term ) ) return $this->error( 'cwb_term_not_found', 'Term not found.', 404 );
		return rest_ensure_response( $this->term_data( $term ) );
	}

	private function validate_term_write( $taxonomy, $data, $term_id = 0 ) {
		if ( empty( $taxonomy->cap->manage_terms ) || ! current_user_can( $taxonomy->cap->manage_terms ) ) return $this->error( 'cwb_cannot_manage_terms', 'Insufficient capability to manage this taxonomy.', 403 );
		if ( isset( $data['parent'] ) && absint( $data['parent'] ) ) {
			if ( ! $taxonomy->hierarchical ) return $this->error( 'cwb_parent_not_supported', 'This taxonomy is not hierarchical.' );
			$parent = get_term( absint( $data['parent'] ), $taxonomy->name );
			if ( ! $parent || is_wp_error( $parent ) ) return $this->error( 'cwb_invalid_parent', 'Parent term does not belong to this taxonomy.' );
			if ( $term_id && ( $term_id === (int) $parent->term_id || in_array( $term_id, get_ancestors( $parent->term_id, $taxonomy->name, 'taxonomy' ), true ) ) ) return $this->error( 'cwb_parent_cycle', 'Parent would create a hierarchy cycle.' );
		}
		if ( ! empty( $data['slug'] ) ) {
			$existing = get_term_by( 'slug', sanitize_title( $data['slug'] ), $taxonomy->name );
			if ( $existing && (int) $existing->term_id !== (int) $term_id ) return $this->error( 'cwb_duplicate_term_slug', 'A term with this slug already exists.', 409 );
		}
		return true;
	}

	public function create_term( WP_REST_Request $request ) {
		$taxonomy = $this->allowed_taxonomy( $request['taxonomy'] ); if ( is_wp_error( $taxonomy ) ) return $taxonomy; $data = $this->json( $request );
		if ( empty( $data['name'] ) ) return $this->error( 'cwb_term_name_required', 'name is required.' );
		$valid = $this->validate_term_write( $taxonomy, $data ); if ( is_wp_error( $valid ) ) return $valid;
		$args = array(); foreach ( array( 'slug', 'description' ) as $key ) if ( isset( $data[ $key ] ) ) $args[ $key ] = 'slug' === $key ? sanitize_title( $data[ $key ] ) : sanitize_textarea_field( $data[ $key ] ); if ( isset( $data['parent'] ) ) $args['parent'] = absint( $data['parent'] );
		$result = wp_insert_term( sanitize_text_field( $data['name'] ), $taxonomy->name, $args ); if ( is_wp_error( $result ) ) return $result;
		$this->bridge->log_change( 'create_term', $taxonomy->name, $result['term_id'], '', null, $data );
		return new WP_REST_Response( $this->term_data( get_term( $result['term_id'], $taxonomy->name ) ), 201 );
	}

	public function update_term( WP_REST_Request $request ) {
		$taxonomy = $this->allowed_taxonomy( $request['taxonomy'] ); if ( is_wp_error( $taxonomy ) ) return $taxonomy; $id = absint( $request['id'] ); $term = get_term( $id, $taxonomy->name ); if ( ! $term || is_wp_error( $term ) ) return $this->error( 'cwb_term_not_found', 'Term not found.', 404 );
		$data = array_intersect_key( $this->json( $request ), array_flip( array( 'name', 'slug', 'description', 'parent' ) ) ); $valid = $this->validate_term_write( $taxonomy, $data, $id ); if ( is_wp_error( $valid ) ) return $valid;
		$args = array(); foreach ( $data as $key => $value ) $args[ $key ] = 'parent' === $key ? absint( $value ) : ( 'slug' === $key ? sanitize_title( $value ) : ( 'description' === $key ? sanitize_textarea_field( $value ) : sanitize_text_field( $value ) ) );
		$result = wp_update_term( $id, $taxonomy->name, $args ); if ( is_wp_error( $result ) ) return $result; $this->bridge->log_change( 'update_term', $taxonomy->name, $id, '', $this->term_data( $term ), $data );
		return rest_ensure_response( $this->term_data( get_term( $id, $taxonomy->name ) ) );
	}

	private function attribute_taxonomy( $post ) {
		if ( ! $post || 'hp_listing_attribute' !== $post->post_type || 'select' !== get_post_meta( $post->ID, 'hp_edit_field_type', true ) ) return '';
		$name = function_exists( 'hivepress' ) && isset( hivepress()->attribute ) ? hivepress()->attribute->get_attribute_name( $post->post_name, 'listing' ) : substr( sanitize_key( $post->post_name ), 0, 21 );
		return 'hp_listing_' . $name;
	}

	private function ensure_attribute_taxonomy( $post ) {
		$taxonomy = $this->attribute_taxonomy( $post ); if ( ! $taxonomy ) return '';
		if ( ! taxonomy_exists( $taxonomy ) ) register_taxonomy( $taxonomy, 'hp_listing', array( 'hierarchical' => true, 'public' => false, 'show_ui' => true, 'rewrite' => false, 'labels' => array( 'name' => $post->post_title, 'singular_name' => $post->post_title ) ) );
		return $taxonomy;
	}

	private function option_items( $taxonomy ) {
		if ( ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) return array(); $terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false, 'number' => 0 ) ); if ( is_wp_error( $terms ) ) return array(); return array_map( array( $this, 'term_data' ), $terms );
	}

	private function attribute_data( $post ) {
		$taxonomy = $this->ensure_attribute_taxonomy( $post );
		$nullable_number = function ( $key ) use ( $post ) { $value = get_post_meta( $post->ID, $key, true ); return '' === $value ? null : (float) $value; };
		return array(
			'id' => (int) $post->ID, 'label' => $post->post_title, 'slug' => $post->post_name, 'status' => $post->post_status,
			'field_type' => get_post_meta( $post->ID, 'hp_edit_field_type', true ) ?: 'text', 'editable' => (bool) get_post_meta( $post->ID, 'hp_editable', true),
			'required' => (bool) get_post_meta( $post->ID, 'hp_edit_field_required', true ), 'filterable' => (bool) get_post_meta( $post->ID, 'hp_filterable', true ),
			'searchable' => (bool) get_post_meta( $post->ID, 'hp_indexable', true ), 'search_field_type' => get_post_meta( $post->ID, 'hp_search_field_type', true ), 'sortable' => (bool) get_post_meta( $post->ID, 'hp_sortable', true ),
			'display_format' => (string) get_post_meta( $post->ID, 'hp_display_format', true ), 'display_areas' => array_values( array_filter( (array) get_post_meta( $post->ID, 'hp_display_areas', true ) ) ),
			'decimals' => (int) get_post_meta( $post->ID, 'hp_edit_field_decimals', true ), 'min_value' => $nullable_number( 'hp_edit_field_min_value' ), 'max_value' => $nullable_number( 'hp_edit_field_max_value' ),
			'category_ids' => array_map( 'intval', wp_get_post_terms( $post->ID, 'hp_listing_category', array( 'fields' => 'ids' ) ) ), 'option_taxonomy' => $taxonomy ?: null, 'options' => $this->option_items( $taxonomy ),
		);
	}

	public function get_attributes() { $posts = get_posts( array( 'post_type' => 'hp_listing_attribute', 'post_status' => array( 'publish', 'draft', 'pending', 'private' ), 'posts_per_page' => -1, 'orderby' => 'menu_order title', 'order' => 'ASC' ) ); return rest_ensure_response( array( 'items' => array_map( array( $this, 'attribute_data' ), $posts ) ) ); }
	public function get_attribute( WP_REST_Request $request ) { $post = get_post( absint( $request['id'] ) ); if ( ! $post || 'hp_listing_attribute' !== $post->post_type ) return $this->error( 'cwb_attribute_not_found', 'Listing attribute not found.', 404 ); return rest_ensure_response( $this->attribute_data( $post ) ); }

	private function save_attribute( $id, array $data ) {
		$type = isset( $data['field_type'] ) ? sanitize_key( $data['field_type'] ) : ( $id ? get_post_meta( $id, 'hp_edit_field_type', true ) : 'text' ); if ( ! in_array( $type, array( 'text', 'number', 'select' ), true ) ) return $this->error( 'cwb_invalid_field_type', 'field_type must be text, number, or select.' );
		$status = isset( $data['status'] ) ? sanitize_key( $data['status'] ) : 'publish'; if ( 'publish' === $status && ! $this->bridge->publish_allowed( 'hp_listing_attribute' ) ) return $this->error( 'cwb_publish_forbidden', 'Publishing requires publish_codex_bridge_content.', 403 );
		$postarr = array( 'post_type' => 'hp_listing_attribute', 'post_status' => $status ); if ( $id ) $postarr['ID'] = $id; if ( isset( $data['label'] ) ) $postarr['post_title'] = sanitize_text_field( $data['label'] ); if ( isset( $data['slug'] ) ) $postarr['post_name'] = sanitize_title( $data['slug'] );
		$result = $id ? wp_update_post( wp_slash( $postarr ), true ) : wp_insert_post( wp_slash( $postarr ), true ); if ( is_wp_error( $result ) ) return $result; $id = (int) $result;
		$map = array( 'editable' => 'hp_editable', 'required' => 'hp_edit_field_required', 'filterable' => 'hp_filterable', 'searchable' => 'hp_indexable', 'sortable' => 'hp_sortable', 'display_format' => 'hp_display_format', 'display_areas' => 'hp_display_areas', 'decimals' => 'hp_edit_field_decimals', 'min_value' => 'hp_edit_field_min_value', 'max_value' => 'hp_edit_field_max_value' );
		update_post_meta( $id, 'hp_edit_field_type', $type );
		foreach ( $map as $input => $meta ) if ( array_key_exists( $input, $data ) ) { $value = $data[ $input ]; if ( in_array( $input, array( 'editable', 'required', 'filterable', 'searchable', 'sortable' ), true ) ) $value = $value ? '1' : ''; elseif ( 'display_areas' === $input ) $value = array_values( array_intersect( array_map( 'sanitize_key', (array) $value ), array( 'view_block_primary', 'view_block_secondary', 'view_block_ternary', 'view_page_primary', 'view_page_secondary', 'view_page_ternary' ) ) ); elseif ( 'decimals' === $input ) $value = min( 6, absint( $value ) ); elseif ( in_array( $input, array( 'min_value', 'max_value' ), true ) ) $value = '' === $value || null === $value ? '' : (float) $value; else $value = wp_kses_post( $value ); update_post_meta( $id, $meta, $value ); }
		if ( array_key_exists( 'search_field_type', $data ) ) update_post_meta( $id, 'hp_search_field_type', sanitize_key( $data['search_field_type'] ) );
		if ( isset( $data['category_ids'] ) ) { $categories = array_values( array_filter( array_map( 'absint', (array) $data['category_ids'] ) ) ); foreach ( $categories as $category ) if ( ! term_exists( $category, 'hp_listing_category' ) ) return $this->error( 'cwb_invalid_category', 'category_ids contains a term outside hp_listing_category.' ); $set = wp_set_object_terms( $id, $categories, 'hp_listing_category', false ); if ( is_wp_error( $set ) ) return $set; }
		$this->clear_hp_cache( $id, 'hp_listing_attribute' ); $post = get_post( $id ); $taxonomy = $this->ensure_attribute_taxonomy( $post );
		if ( isset( $data['options'] ) ) { if ( 'select' !== $type ) return $this->error( 'cwb_options_require_select', 'options are only valid for select attributes.' ); foreach ( (array) $data['options'] as $option ) { if ( ! is_array( $option ) || empty( $option['name'] ) ) return $this->error( 'cwb_invalid_option', 'Every option requires name.' ); $slug = ! empty( $option['slug'] ) ? sanitize_title( $option['slug'] ) : sanitize_title( $option['name'] ); $existing = get_term_by( 'slug', $slug, $taxonomy ); if ( $existing ) wp_update_term( $existing->term_id, $taxonomy, array( 'name' => sanitize_text_field( $option['name'] ) ) ); else { $created = wp_insert_term( sanitize_text_field( $option['name'] ), $taxonomy, array( 'slug' => $slug ) ); if ( is_wp_error( $created ) ) return $created; } } }
		$this->bridge->log_change( $id === (int) $result && isset( $postarr['ID'] ) ? 'update_attribute' : 'create_attribute', 'hp_listing_attribute', $id, '', null, $data ); return $this->attribute_data( get_post( $id ) );
	}

	public function create_attribute( WP_REST_Request $request ) { if ( ! $this->post_cap( 'hp_listing_attribute', 'create' ) ) return $this->error( 'cwb_cannot_create_attribute', 'Insufficient capability.', 403 ); $data = $this->json( $request ); if ( empty( $data['label'] ) || empty( $data['slug'] ) ) return $this->error( 'cwb_attribute_required_fields', 'label and slug are required.' ); if ( get_page_by_path( sanitize_title( $data['slug'] ), OBJECT, 'hp_listing_attribute' ) ) return $this->error( 'cwb_duplicate_attribute_slug', 'An attribute with this slug already exists.', 409 ); $saved = $this->save_attribute( 0, $data ); return is_wp_error( $saved ) ? $saved : new WP_REST_Response( $saved, 201 ); }
	public function update_attribute( WP_REST_Request $request ) { $id = absint( $request['id'] ); $post = get_post( $id ); if ( ! $post || 'hp_listing_attribute' !== $post->post_type ) return $this->error( 'cwb_attribute_not_found', 'Listing attribute not found.', 404 ); if ( ! $this->post_cap( 'hp_listing_attribute', 'edit', $id ) ) return $this->error( 'cwb_cannot_edit_attribute', 'Insufficient capability.', 403 ); return rest_ensure_response( $this->save_attribute( $id, $this->json( $request ) ) ); }

	public function get_options( WP_REST_Request $request ) { $post = get_post( absint( $request['id'] ) ); if ( ! $post || 'hp_listing_attribute' !== $post->post_type ) return $this->error( 'cwb_attribute_not_found', 'Listing attribute not found.', 404 ); $taxonomy = $this->ensure_attribute_taxonomy( $post ); if ( ! $taxonomy ) return $this->error( 'cwb_not_select_attribute', 'This is not a select attribute.' ); return rest_ensure_response( array( 'taxonomy' => $taxonomy, 'items' => $this->option_items( $taxonomy ) ) ); }
	public function create_option( WP_REST_Request $request ) { $post = get_post( absint( $request['id'] ) ); if ( ! $post || 'hp_listing_attribute' !== $post->post_type ) return $this->error( 'cwb_attribute_not_found', 'Listing attribute not found.', 404 ); if ( ! $this->post_cap( 'hp_listing_attribute', 'edit', $post->ID ) ) return $this->error( 'cwb_cannot_edit_attribute', 'Insufficient capability.', 403 ); $taxonomy = $this->ensure_attribute_taxonomy( $post ); if ( ! $taxonomy ) return $this->error( 'cwb_not_select_attribute', 'This is not a select attribute.' ); $data = $this->json( $request ); if ( empty( $data['name'] ) ) return $this->error( 'cwb_option_name_required', 'name is required.' ); $slug = ! empty( $data['slug'] ) ? sanitize_title( $data['slug'] ) : sanitize_title( $data['name'] ); if ( get_term_by( 'slug', $slug, $taxonomy ) ) return $this->error( 'cwb_duplicate_option_slug', 'An option with this slug already exists.', 409 ); $result = wp_insert_term( sanitize_text_field( $data['name'] ), $taxonomy, array( 'slug' => $slug, 'description' => sanitize_textarea_field( $data['description'] ?? '' ), 'parent' => absint( $data['parent'] ?? 0 ) ) ); if ( is_wp_error( $result ) ) return $result; $this->bridge->log_change( 'create_attribute_option', $taxonomy, $result['term_id'], '', null, $data ); return new WP_REST_Response( $this->term_data( get_term( $result['term_id'], $taxonomy ) ), 201 ); }
	public function update_option( WP_REST_Request $request ) { $post = get_post( absint( $request['id'] ) ); if ( ! $post || 'hp_listing_attribute' !== $post->post_type ) return $this->error( 'cwb_attribute_not_found', 'Listing attribute not found.', 404 ); if ( ! $this->post_cap( 'hp_listing_attribute', 'edit', $post->ID ) ) return $this->error( 'cwb_cannot_edit_attribute', 'Insufficient capability.', 403 ); $taxonomy = $this->ensure_attribute_taxonomy( $post ); $term = $taxonomy ? get_term( absint( $request['option_id'] ), $taxonomy ) : null; if ( ! $term || is_wp_error( $term ) ) return $this->error( 'cwb_option_not_found', 'Option does not belong to this attribute.', 404 ); $data = array_intersect_key( $this->json( $request ), array_flip( array( 'name', 'slug', 'description', 'parent' ) ) ); $valid = $this->validate_term_write( get_taxonomy( $taxonomy ), $data, $term->term_id ); if ( is_wp_error( $valid ) ) return $valid; foreach ( $data as $key => $value ) $data[ $key ] = 'parent' === $key ? absint( $value ) : ( 'slug' === $key ? sanitize_title( $value ) : ( 'description' === $key ? sanitize_textarea_field( $value ) : sanitize_text_field( $value ) ) ); $result = wp_update_term( $term->term_id, $taxonomy, $data ); if ( is_wp_error( $result ) ) return $result; $this->bridge->log_change( 'update_attribute_option', $taxonomy, $term->term_id, '', $this->term_data( $term ), $data ); return rest_ensure_response( $this->term_data( get_term( $term->term_id, $taxonomy ) ) ); }

	private function clear_hp_cache( $id, $type ) { clean_post_cache( $id ); if ( function_exists( 'hivepress' ) && isset( hivepress()->cache ) ) hivepress()->cache->clear_post_cache( $id, $type ); }
	private function validate_attachment( $id ) { return $id && 'attachment' === get_post_type( $id ) && wp_attachment_is_image( $id ); }

	public function listing_schema() {
		$attributes = $this->get_attributes()->get_data()['items']; $price = null; foreach ( $attributes as $attribute ) if ( 'price' === $attribute['slug'] ) $price = array( 'attribute_id' => $attribute['id'], 'field' => 'attributes.price', 'meta_key' => 'hp_price', 'type' => $attribute['field_type'] );
		return rest_ensure_response( array( 'post_type' => 'hp_listing', 'category_taxonomy' => 'hp_listing_category', 'vendor_post_type' => 'hp_vendor', 'system_fields' => array( 'title', 'slug', 'content', 'status', 'category_id', 'author_id', 'vendor_id', 'featured_media', 'gallery_media' ), 'attributes' => array_map( function( $a ) { return array_merge( $a, array( 'internal_field' => 'hp_' . $a['slug'], 'expected_value' => 'select' === $a['field_type'] ? 'option term ID' : ( 'number' === $a['field_type'] ? 'number' : 'string' ) ) ); }, $attributes ), 'price_field' => $price, 'location_support' => array( 'available' => defined( 'HIVEPRESS_GEOLOCATION_VERSION' ) || class_exists( '\\HivePress\\Components\\Geolocation' ), 'fields' => array( 'hp_location', 'hp_latitude', 'hp_longitude' ) ), 'image_support' => array( 'featured_media' => true, 'gallery_media' => true, 'gallery_limit' => 10 ), 'author_vendor_assignment' => array( 'author' => 'post_author/user ID', 'vendor' => 'post_parent/hp_vendor ID', 'vendor_user' => 'vendor post_author' ), 'allowed_statuses' => array( 'draft', 'pending', 'private', 'publish' ), 'publish_allowed' => $this->bridge->publish_allowed( 'hp_listing' ) ) );
	}

	private function attribute_posts() { $items = array(); foreach ( get_posts( array( 'post_type' => 'hp_listing_attribute', 'post_status' => 'publish', 'posts_per_page' => -1 ) ) as $post ) $items[ $post->post_name ] = $post; return $items; }
	private function apply_listing_data( $id, array $data ) {
		if ( array_key_exists( 'price', $data ) ) {
			if ( ! isset( $data['attributes'] ) || ! is_array( $data['attributes'] ) ) $data['attributes'] = array();
			$data['attributes']['price'] = $data['price'];
		}
		if ( isset( $data['category_id'] ) ) { $category = absint( $data['category_id'] ); if ( $category && ! term_exists( $category, 'hp_listing_category' ) ) return $this->error( 'cwb_invalid_listing_category', 'category_id is not an hp_listing_category term.' ); $result = wp_set_object_terms( $id, $category ? array( $category ) : array(), 'hp_listing_category', false ); if ( is_wp_error( $result ) ) return $result; $this->bridge->log_change( 'assign_listing_category', 'hp_listing', $id, 'hp_listing_category', null, $category ); }
		if ( isset( $data['attributes'] ) ) { if ( ! is_array( $data['attributes'] ) ) return $this->error( 'cwb_invalid_attributes', 'attributes must be an object.' ); $attributes = $this->attribute_posts(); foreach ( $data['attributes'] as $slug => $value ) { $slug = sanitize_title( $slug ); if ( ! isset( $attributes[ $slug ] ) ) return $this->error( 'cwb_unknown_attribute', 'Unknown listing attribute: ' . $slug ); $attribute = $attributes[ $slug ]; $type = get_post_meta( $attribute->ID, 'hp_edit_field_type', true ); if ( 'select' === $type ) { $taxonomy = $this->ensure_attribute_taxonomy( $attribute ); $option = get_term( absint( $value ), $taxonomy ); if ( ! $option || is_wp_error( $option ) ) return $this->error( 'cwb_invalid_attribute_option', 'Option ID does not belong to attribute ' . $slug ); $result = wp_set_object_terms( $id, array( (int) $option->term_id ), $taxonomy, false ); if ( is_wp_error( $result ) ) return $result; } else { if ( 'number' === $type ) { if ( ! is_numeric( $value ) ) return $this->error( 'cwb_invalid_number', $slug . ' must be numeric.' ); $decimals = min( 6, absint( get_post_meta( $attribute->ID, 'hp_edit_field_decimals', true ) ) ); $value = round( (float) $value, $decimals ); } else $value = sanitize_textarea_field( (string) $value ); update_post_meta( $id, 'hp_' . sanitize_key( $slug ), $value ); } $this->bridge->log_change( 'assign_listing_attribute', 'hp_listing', $id, $slug, null, $value ); } }
		if ( array_key_exists( 'location', $data ) ) { if ( ! ( defined( 'HIVEPRESS_GEOLOCATION_VERSION' ) || class_exists( '\\HivePress\\Components\\Geolocation' ) ) ) return $this->error( 'cwb_location_unavailable', 'HivePress Geolocation is not active.', 501 ); update_post_meta( $id, 'hp_location', sanitize_text_field( $data['location'] ) ); }
		if ( isset( $data['coordinates'] ) && is_array( $data['coordinates'] ) ) { foreach ( array( 'latitude', 'longitude' ) as $key ) if ( isset( $data['coordinates'][ $key ] ) ) update_post_meta( $id, 'hp_' . $key, (float) $data['coordinates'][ $key ] ); }
		if ( array_key_exists( 'featured_media', $data ) ) { $media = absint( $data['featured_media'] ); if ( $media && ! $this->validate_attachment( $media ) ) return $this->error( 'cwb_invalid_featured_media', 'featured_media must be an image attachment.' ); $media ? set_post_thumbnail( $id, $media ) : delete_post_thumbnail( $id ); }
		if ( isset( $data['gallery_media'] ) ) { $gallery = array_values( array_unique( array_filter( array_map( 'absint', (array) $data['gallery_media'] ) ) ) ); if ( count( $gallery ) > 10 ) return $this->error( 'cwb_gallery_limit', 'gallery_media supports at most 10 images.' ); foreach ( $gallery as $media ) { if ( ! $this->validate_attachment( $media ) ) return $this->error( 'cwb_invalid_gallery_media', 'Every gallery_media ID must be an image attachment.' ); wp_update_post( array( 'ID' => $media, 'post_parent' => $id ) ); update_post_meta( $media, 'hp_parent_field', 'images' ); } }
		$this->clear_hp_cache( $id, 'hp_listing' ); return true;
	}

	private function save_listing( $id, array $data ) {
		$status = isset( $data['status'] ) ? sanitize_key( $data['status'] ) : ( $id ? get_post_status( $id ) : 'draft' ); if ( ! in_array( $status, array( 'draft', 'pending', 'private', 'publish' ), true ) ) return $this->error( 'cwb_invalid_status', 'Unsupported listing status.' ); if ( 'publish' === $status && ! $this->bridge->publish_allowed( 'hp_listing' ) ) return $this->error( 'cwb_publish_forbidden', 'Publishing requires publish_codex_bridge_content.', 403 );
		$vendor_id = isset( $data['vendor_id'] ) ? absint( $data['vendor_id'] ) : ( $id ? wp_get_post_parent_id( $id ) : 0 ); if ( $vendor_id && 'hp_vendor' !== get_post_type( $vendor_id ) ) return $this->error( 'cwb_invalid_vendor', 'vendor_id must reference hp_vendor.' );
		$author_id = isset( $data['author_id'] ) ? absint( $data['author_id'] ) : ( $vendor_id ? (int) get_post_field( 'post_author', $vendor_id ) : get_current_user_id() ); if ( ! get_user_by( 'id', $author_id ) ) return $this->error( 'cwb_invalid_author', 'author_id does not reference a user.' ); if ( $vendor_id && (int) get_post_field( 'post_author', $vendor_id ) !== $author_id ) return $this->error( 'cwb_vendor_author_mismatch', 'author_id must match the vendor user.' );
		$postarr = array( 'post_type' => 'hp_listing', 'post_status' => $status, 'post_author' => $author_id, 'post_parent' => $vendor_id ); if ( $id ) $postarr['ID'] = $id; $fields = array( 'title' => 'post_title', 'slug' => 'post_name', 'content' => 'post_content', 'description' => 'post_content' ); foreach ( $fields as $input => $field ) if ( array_key_exists( $input, $data ) ) $postarr[ $field ] = 'post_name' === $field ? sanitize_title( $data[ $input ] ) : ( 'post_content' === $field ? wp_kses_post( $data[ $input ] ) : sanitize_text_field( $data[ $input ] ) );
		$result = $id ? wp_update_post( wp_slash( $postarr ), true ) : wp_insert_post( wp_slash( $postarr ), true ); if ( is_wp_error( $result ) ) return $result; $id = (int) $result; $applied = $this->apply_listing_data( $id, $data ); if ( is_wp_error( $applied ) ) return $applied; $this->bridge->log_change( isset( $postarr['ID'] ) ? 'update_listing' : 'create_listing', 'hp_listing', $id, '', null, $data ); return $this->listing_data( get_post( $id ) );
	}
	public function create_listing( WP_REST_Request $request ) { if ( ! $this->post_cap( 'hp_listing', 'create' ) ) return $this->error( 'cwb_cannot_create_listing', 'Insufficient capability.', 403 ); $data = $this->json( $request ); if ( empty( $data['title'] ) ) return $this->error( 'cwb_listing_title_required', 'title is required.' ); $saved = $this->save_listing( 0, $data ); return is_wp_error( $saved ) ? $saved : new WP_REST_Response( $saved, 201 ); }
	public function update_listing( WP_REST_Request $request ) { $id = absint( $request['id'] ); if ( 'hp_listing' !== get_post_type( $id ) ) return $this->error( 'cwb_listing_not_found', 'Listing not found.', 404 ); if ( ! $this->post_cap( 'hp_listing', 'edit', $id ) ) return $this->error( 'cwb_cannot_edit_listing', 'Insufficient capability.', 403 ); return rest_ensure_response( $this->save_listing( $id, $this->json( $request ) ) ); }

	private function user_data( $id ) { $user = get_user_by( 'id', $id ); return $user ? array( 'id' => (int) $user->ID, 'display_name' => $user->display_name, 'slug' => $user->user_nicename, 'roles' => array_values( $user->roles ) ) : null; }
	private function listing_data( $post ) {
		$category_terms = wp_get_post_terms( $post->ID, 'hp_listing_category' ); $category = ! is_wp_error( $category_terms ) && $category_terms ? $this->term_data( end( $category_terms ) ) : null; $vendor = $post->post_parent && 'hp_vendor' === get_post_type( $post->post_parent ) ? $this->vendor_data( get_post( $post->post_parent ) ) : null; $values = array();
		foreach ( $this->attribute_posts() as $slug => $attribute ) { $type = get_post_meta( $attribute->ID, 'hp_edit_field_type', true ); $raw = null; $display = null; $option_id = null; if ( 'select' === $type ) { $terms = wp_get_object_terms( $post->ID, $this->ensure_attribute_taxonomy( $attribute ) ); if ( ! is_wp_error( $terms ) && $terms ) { $option_id = (int) $terms[0]->term_id; $raw = $option_id; $display = $terms[0]->name; } } else { $raw = get_post_meta( $post->ID, 'hp_' . sanitize_key( $slug ), true ); if ( '' === $raw ) $raw = null; $display = $raw; if ( 'number' === $type && null !== $raw ) { $raw = (float) $raw; $display = number_format_i18n( $raw, absint( get_post_meta( $attribute->ID, 'hp_edit_field_decimals', true ) ) ); } } $values[ $slug ] = array( 'attribute_id' => (int) $attribute->ID, 'label' => $attribute->post_title, 'slug' => $slug, 'type' => $type, 'raw_value' => $raw, 'display_value' => $display, 'option_id' => $option_id ); }
		$price = isset( $values['price'] ) ? $values['price']['raw_value'] : null; $formatted = null; if ( null !== $price && function_exists( 'hivepress' ) && isset( hivepress()->woocommerce ) ) $formatted = hivepress()->woocommerce->format_price( $price ); $gallery = array(); foreach ( get_attached_media( 'image', $post->ID ) as $media ) if ( 'images' === get_post_meta( $media->ID, 'hp_parent_field', true ) ) $gallery[] = $this->media_data( $media );
		return array( 'id' => (int) $post->ID, 'title' => $post->post_title, 'slug' => $post->post_name, 'status' => $post->post_status, 'url' => get_permalink( $post ), 'permalink' => get_permalink( $post ), 'content' => $post->post_content, 'category' => $category, 'author' => $this->user_data( $post->post_author ), 'vendor' => $vendor, 'price' => null === $price ? null : (float) $price, 'formatted_price' => $formatted, 'location' => get_post_meta( $post->ID, 'hp_location', true ) ?: null, 'coordinates' => array( 'latitude' => (float) get_post_meta( $post->ID, 'hp_latitude', true ), 'longitude' => (float) get_post_meta( $post->ID, 'hp_longitude', true ) ), 'featured_media' => get_post_thumbnail_id( $post->ID ) ? $this->media_data( get_post( get_post_thumbnail_id( $post->ID ) ) ) : null, 'gallery_media' => $gallery, 'attributes' => $values, 'created' => mysql2date( DATE_ATOM, $post->post_date_gmt ?: $post->post_date ), 'modified' => mysql2date( DATE_ATOM, $post->post_modified_gmt ?: $post->post_modified ) );
	}
	public function get_listing( WP_REST_Request $request ) { $post = get_post( absint( $request['id'] ) ); if ( ! $post || 'hp_listing' !== $post->post_type ) return $this->error( 'cwb_listing_not_found', 'Listing not found.', 404 ); return rest_ensure_response( $this->listing_data( $post ) ); }
	public function get_listings( WP_REST_Request $request ) { $page = max( 1, absint( $request->get_param( 'page' ) ?: 1 ) ); $per = min( 100, max( 1, absint( $request->get_param( 'per_page' ) ?: 20 ) ) ); $query = new WP_Query( array( 'post_type' => 'hp_listing', 'post_status' => sanitize_key( $request->get_param( 'status' ) ?: 'any' ), 's' => sanitize_text_field( $request->get_param( 'search' ) ?: '' ), 'posts_per_page' => $per, 'paged' => $page ) ); return rest_ensure_response( array( 'items' => array_map( array( $this, 'listing_data' ), $query->posts ), 'total' => (int) $query->found_posts, 'total_pages' => (int) $query->max_num_pages, 'page' => $page ) ); }

	private function vendor_data( $post ) { return array( 'id' => (int) $post->ID, 'name' => $post->post_title, 'slug' => $post->post_name, 'description' => $post->post_content, 'status' => $post->post_status, 'url' => get_permalink( $post ), 'user_id' => (int) $post->post_author, 'user' => $this->user_data( $post->post_author ), 'featured_media' => get_post_thumbnail_id( $post->ID ) ? $this->media_data( get_post( get_post_thumbnail_id( $post->ID ) ) ) : null, 'created' => mysql2date( DATE_ATOM, $post->post_date_gmt ?: $post->post_date ), 'modified' => mysql2date( DATE_ATOM, $post->post_modified_gmt ?: $post->post_modified ) ); }
	private function save_vendor( $id, array $data ) { $user_id = isset( $data['user_id'] ) ? absint( $data['user_id'] ) : ( $id ? (int) get_post_field( 'post_author', $id ) : 0 ); if ( ! $user_id && ! empty( $data['create_demo_user'] ) ) { if ( ! current_user_can( 'create_users' ) ) return $this->error( 'cwb_cannot_create_demo_user', 'create_users capability is required.', 403 ); $login = 'codex-demo-' . strtolower( wp_generate_password( 8, false, false ) ); $user_id = wp_insert_user( array( 'user_login' => $login, 'user_email' => $login . '@example.invalid', 'user_pass' => wp_generate_password( 32, true, true ), 'display_name' => sanitize_text_field( $data['name'] ?? 'Demo Vendor' ), 'role' => 'subscriber' ) ); if ( is_wp_error( $user_id ) ) return $user_id; } if ( ! $user_id || ! get_user_by( 'id', $user_id ) ) return $this->error( 'cwb_vendor_user_required', 'Use an existing user_id or create_demo_user=true.' ); $status = isset( $data['status'] ) ? sanitize_key( $data['status'] ) : 'publish'; if ( 'publish' === $status && ! $this->bridge->publish_allowed( 'hp_vendor' ) ) return $this->error( 'cwb_publish_forbidden', 'Publishing requires publish_codex_bridge_content.', 403 ); $postarr = array( 'post_type' => 'hp_vendor', 'post_status' => $status, 'post_author' => $user_id ); if ( $id ) $postarr['ID'] = $id; foreach ( array( 'name' => 'post_title', 'slug' => 'post_name', 'description' => 'post_content' ) as $input => $field ) if ( isset( $data[ $input ] ) ) $postarr[ $field ] = 'post_name' === $field ? sanitize_title( $data[ $input ] ) : ( 'post_content' === $field ? wp_kses_post( $data[ $input ] ) : sanitize_text_field( $data[ $input ] ) ); $result = $id ? wp_update_post( wp_slash( $postarr ), true ) : wp_insert_post( wp_slash( $postarr ), true ); if ( is_wp_error( $result ) ) return $result; if ( isset( $data['featured_media'] ) ) { $media = absint( $data['featured_media'] ); if ( $media && ! $this->validate_attachment( $media ) ) return $this->error( 'cwb_invalid_featured_media', 'featured_media must be an image attachment.' ); $media ? set_post_thumbnail( $result, $media ) : delete_post_thumbnail( $result ); } $this->clear_hp_cache( $result, 'hp_vendor' ); $this->bridge->log_change( $id ? 'update_vendor' : 'create_vendor', 'hp_vendor', $result, '', null, array_diff_key( $data, array( 'password' => true ) ) ); return $this->vendor_data( get_post( $result ) ); }
	public function create_vendor( WP_REST_Request $request ) { if ( ! $this->post_cap( 'hp_vendor', 'create' ) ) return $this->error( 'cwb_cannot_create_vendor', 'Insufficient capability.', 403 ); $data = $this->json( $request ); if ( empty( $data['name'] ) ) return $this->error( 'cwb_vendor_name_required', 'name is required.' ); if ( isset( $data['password'] ) ) return $this->error( 'cwb_password_forbidden', 'Passwords are never accepted by this endpoint.' ); $saved = $this->save_vendor( 0, $data ); return is_wp_error( $saved ) ? $saved : new WP_REST_Response( $saved, 201 ); }
	public function update_vendor( WP_REST_Request $request ) { $id = absint( $request['id'] ); if ( 'hp_vendor' !== get_post_type( $id ) ) return $this->error( 'cwb_vendor_not_found', 'Vendor not found.', 404 ); if ( ! $this->post_cap( 'hp_vendor', 'edit', $id ) ) return $this->error( 'cwb_cannot_edit_vendor', 'Insufficient capability.', 403 ); $data = $this->json( $request ); if ( isset( $data['password'] ) || isset( $data['create_demo_user'] ) ) return $this->error( 'cwb_vendor_user_update_forbidden', 'Passwords and demo user creation are not accepted on update.' ); return rest_ensure_response( $this->save_vendor( $id, $data ) ); }
	public function get_vendor( WP_REST_Request $request ) { $post = get_post( absint( $request['id'] ) ); if ( ! $post || 'hp_vendor' !== $post->post_type ) return $this->error( 'cwb_vendor_not_found', 'Vendor not found.', 404 ); return rest_ensure_response( $this->vendor_data( $post ) ); }
	public function get_vendors( WP_REST_Request $request ) { $posts = get_posts( array( 'post_type' => 'hp_vendor', 'post_status' => sanitize_key( $request->get_param( 'status' ) ?: 'any' ), 'posts_per_page' => min( 100, max( 1, absint( $request->get_param( 'per_page' ) ?: 20 ) ) ), 's' => sanitize_text_field( $request->get_param( 'search' ) ?: '' ) ) ); return rest_ensure_response( array( 'items' => array_map( array( $this, 'vendor_data' ), $posts ) ) ); }

	public function get_users() { $items = array(); $listing = get_post_type_object( 'hp_listing' ); $vendor = get_post_type_object( 'hp_vendor' ); foreach ( get_users( array( 'fields' => 'all' ) ) as $user ) $items[] = array_merge( $this->user_data( $user->ID ), array( 'capabilities' => array( 'create_listing' => $listing ? user_can( $user, $listing->cap->create_posts ) : false, 'edit_listings' => $listing ? user_can( $user, $listing->cap->edit_posts ) : false, 'create_vendor' => $vendor ? user_can( $user, $vendor->cap->create_posts ) : false, 'edit_vendors' => $vendor ? user_can( $user, $vendor->cap->edit_posts ) : false ) ) ); return rest_ensure_response( array( 'items' => $items ) ); }
	private function media_data( $post ) { $metadata = wp_get_attachment_metadata( $post->ID ); return array( 'id' => (int) $post->ID, 'title' => $post->post_title, 'alt' => get_post_meta( $post->ID, '_wp_attachment_image_alt', true ), 'caption' => $post->post_excerpt, 'description' => $post->post_content, 'mime_type' => $post->post_mime_type, 'width' => isset( $metadata['width'] ) ? (int) $metadata['width'] : null, 'height' => isset( $metadata['height'] ) ? (int) $metadata['height'] : null, 'filesize' => isset( $metadata['filesize'] ) ? (int) $metadata['filesize'] : null, 'url' => wp_get_attachment_url( $post->ID ), 'source_url' => get_post_meta( $post->ID, '_cwb_source_url', true ) ?: wp_get_attachment_url( $post->ID ), 'parent' => (int) $post->post_parent, 'created' => mysql2date( DATE_ATOM, $post->post_date_gmt ?: $post->post_date ), 'modified' => mysql2date( DATE_ATOM, $post->post_modified_gmt ?: $post->post_modified ) ); }
	public function get_media_item( WP_REST_Request $request ) { $post = get_post( absint( $request['id'] ) ); if ( ! $post || 'attachment' !== $post->post_type ) return $this->error( 'cwb_media_not_found', 'Media attachment not found.', 404 ); return rest_ensure_response( $this->media_data( $post ) ); }
	public function get_media( WP_REST_Request $request ) { $page = max( 1, absint( $request->get_param( 'page' ) ?: 1 ) ); $per = min( 100, max( 1, absint( $request->get_param( 'per_page' ) ?: 20 ) ) ); $args = array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => $per, 'paged' => $page, 's' => sanitize_text_field( $request->get_param( 'search' ) ?: '' ) ); if ( $request->get_param( 'mime_type' ) ) $args['post_mime_type'] = sanitize_mime_type( $request->get_param( 'mime_type' ) ); if ( null !== $request->get_param( 'parent' ) && '' !== $request->get_param( 'parent' ) ) $args['post_parent'] = absint( $request->get_param( 'parent' ) ); $query = new WP_Query( $args ); return rest_ensure_response( array( 'items' => array_map( array( $this, 'media_data' ), $query->posts ), 'total' => (int) $query->found_posts, 'total_pages' => (int) $query->max_num_pages, 'page' => $page ) ); }
}
