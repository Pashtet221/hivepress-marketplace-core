<?php
/**
 * Standalone contract checks for the listing category image REST API.
 *
 * Run with: php tests/listing-category-image-api-contract.php
 */

define( 'ABSPATH', __DIR__ );

class WP_Error {
	public $code;
	public $data;

	public function __construct( $code, $message, $data ) {
		$this->code = $code;
		$this->data = $data;
	}
}

class WP_REST_Request implements ArrayAccess {
	private $parameters;
	private $json;

	public function __construct( $id, $json = null ) {
		$this->parameters = array( 'id' => $id );
		$this->json       = $json;
	}

	public function get_json_params() {
		return $this->json;
	}

	public function offsetExists( $offset ) {
		return isset( $this->parameters[ $offset ] );
	}

	public function offsetGet( $offset ) {
		return $this->parameters[ $offset ];
	}

	public function offsetSet( $offset, $value ) {}
	public function offsetUnset( $offset ) {}
}

class WP_REST_Server {
	const READABLE = 'GET';
	const EDITABLE = 'POST, PUT, PATCH';
}

$GLOBALS['listinghive_test_caps']  = array();
$GLOBALS['listinghive_test_meta']  = array();
$GLOBALS['listinghive_test_routes'] = array();

function add_action( $hook, $callback ) {
	$GLOBALS['listinghive_test_actions'][ $hook ] = $callback;
}

function register_rest_route( $namespace, $route, $arguments ) {
	$GLOBALS['listinghive_test_routes'][ $namespace . $route ] = $arguments;
}

function current_user_can( $capability ) {
	return ! empty( $GLOBALS['listinghive_test_caps'][ $capability ] );
}

function get_current_user_id() {
	return 1;
}

function get_taxonomy() {
	return (object) array( 'cap' => (object) array( 'manage_terms' => 'manage_listing_categories' ) );
}

function get_term( $id, $taxonomy ) {
	if ( 7 !== $id || 'hp_listing_category' !== $taxonomy ) {
		return null;
	}

	return (object) array( 'term_id' => 7, 'name' => 'Cranes', 'slug' => 'cranes' );
}

function get_post( $id ) {
	if ( 22 === $id ) {
		return (object) array( 'ID' => 22, 'post_type' => 'attachment', 'post_title' => 'Crane image', 'post_mime_type' => 'image/jpeg' );
	}

	if ( 23 === $id ) {
		return (object) array( 'ID' => 23, 'post_type' => 'attachment', 'post_title' => 'Document', 'post_mime_type' => 'application/pdf' );
	}

	return null;
}

function wp_attachment_is_image( $id ) {
	return 22 === $id;
}

function update_term_meta( $term_id, $key, $value ) {
	$GLOBALS['listinghive_test_meta'][ $term_id ][ $key ] = $value;
	return true;
}

function delete_term_meta( $term_id, $key ) {
	unset( $GLOBALS['listinghive_test_meta'][ $term_id ][ $key ] );
	return true;
}

function get_term_meta( $term_id, $key ) {
	return isset( $GLOBALS['listinghive_test_meta'][ $term_id ][ $key ] ) ? $GLOBALS['listinghive_test_meta'][ $term_id ][ $key ] : '';
}

function get_post_meta( $id, $key ) {
	return '_wp_attachment_image_alt' === $key ? 'Crane' : '';
}

function get_the_title( $post ) {
	return $post->post_title;
}

function get_post_mime_type( $post ) {
	return $post->post_mime_type;
}

function wp_get_attachment_url( $id ) {
	return 'https://example.test/uploads/' . $id . '.jpg';
}

function absint( $value ) {
	return abs( (int) $value );
}

function is_wp_error( $value ) {
	return $value instanceof WP_Error;
}

function rest_ensure_response( $value ) {
	return $value;
}

function listinghive_test_assert( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: {$message}\n" );
		exit( 1 );
	}
}

require dirname( __DIR__ ) . '/includes/listing-category-image-api.php';

listinghive_register_listing_category_image_route();
$route = $GLOBALS['listinghive_test_routes']['codex-bridge/v1/hivepress/listing-categories/(?P<id>\d+)/image'];
listinghive_test_assert( 'GET' === $route[0]['methods'], 'GET route must be registered.' );
listinghive_test_assert( 'listinghive_category_image_can_read' === $route[0]['permission_callback'], 'GET must have its permission callback.' );
listinghive_test_assert( 'PATCH' === $route[1]['methods'], 'Only PATCH must be registered for writes.' );
listinghive_test_assert( 'listinghive_category_image_can_write' === $route[1]['permission_callback'], 'PATCH must have its permission callback.' );

$GLOBALS['listinghive_test_caps'] = array( 'use_codex_bridge' => true, 'manage_listing_categories' => true );
listinghive_test_assert( true === listinghive_category_image_can_write(), 'A fully authorized user must be allowed.' );
$GLOBALS['listinghive_test_caps'] = array( 'use_codex_bridge' => true );
listinghive_test_assert( is_wp_error( listinghive_category_image_can_write() ), 'Taxonomy management capability must be required.' );
$GLOBALS['listinghive_test_caps'] = array( 'use_codex_bridge' => true, 'manage_listing_categories' => true );
listinghive_test_assert( is_wp_error( listinghive_get_listing_category_image( new WP_REST_Request( 99 ) ) ), 'A term outside the taxonomy must be rejected.' );

$invalid_attachment = listinghive_update_listing_category_image( new WP_REST_Request( 7, array( 'image_id' => 99 ) ) );
listinghive_test_assert( is_wp_error( $invalid_attachment ) && 404 === $invalid_attachment->data['status'], 'A missing attachment must return 404.' );
$not_image = listinghive_update_listing_category_image( new WP_REST_Request( 7, array( 'image_id' => 23 ) ) );
listinghive_test_assert( is_wp_error( $not_image ) && 400 === $not_image->data['status'], 'A non-image attachment must return 400.' );
$extra_field = listinghive_update_listing_category_image( new WP_REST_Request( 7, array( 'image_id' => 22, 'title' => 'No' ) ) );
listinghive_test_assert( is_wp_error( $extra_field ), 'Fields other than image_id must be rejected.' );

$saved = listinghive_update_listing_category_image( new WP_REST_Request( 7, array( 'image_id' => 22 ) ) );
listinghive_test_assert( 22 === $saved['image_id'] && 'Crane' === $saved['image']['alt'], 'The image metadata must be saved and returned.' );
$removed = listinghive_update_listing_category_image( new WP_REST_Request( 7, array( 'image_id' => 0 ) ) );
listinghive_test_assert( 0 === $removed['image_id'] && null === $removed['image'], 'image_id 0 must remove the image.' );

echo "Listing category image API contract checks passed.\n";
