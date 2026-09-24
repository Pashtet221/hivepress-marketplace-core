<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class CWB_Plugin {
	const REST_NAMESPACE = 'codex-bridge/v1';
	const ROLE           = 'codex_content_manager';
	const CAPABILITY     = 'use_codex_bridge';
	const PUBLISH_CAPABILITY = 'publish_codex_bridge_content';
	const TABLE_SUFFIX   = 'codex_bridge_log';

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		require_once CWB_DIR . 'includes/class-cwb-hivepress-api.php';
		new CWB_HivePress_API( $this );
		add_action( 'init', array( __CLASS__, 'refresh_role_capabilities' ), 99 );
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public static function activate() {
		self::create_role();
		self::create_log_table();
	}

	public static function deactivate() {
		// Роль и журнал намеренно сохраняются, чтобы не ломать пользователя и историю.
	}

	private static function create_role() {
		$capabilities = array(
			'read'                 => true,
			'edit_posts'           => true,
			'edit_published_posts' => true,
			'edit_others_posts'    => true,
			'edit_private_posts'   => true,
			'publish_posts'        => true,
			'edit_pages'           => true,
			'edit_published_pages' => true,
			'edit_others_pages'    => true,
			'edit_private_pages'   => true,
			'publish_pages'        => true,
			'manage_categories'    => true,
			'upload_files'         => true,
			self::CAPABILITY       => true,
			self::PUBLISH_CAPABILITY => true,
		);

		$role = get_role( self::ROLE );
		if ( ! $role ) {
			$role = add_role(
				self::ROLE,
				'Codex Content Manager',
				$capabilities
			);
		}

		if ( $role ) {
			foreach ( $capabilities as $capability => $grant ) {
				$role->add_cap( $capability, $grant );
			}
		}

		$administrator = get_role( 'administrator' );
		if ( $administrator ) {
			$administrator->add_cap( self::CAPABILITY );
			$administrator->add_cap( self::PUBLISH_CAPABILITY );
		}
	}

	/**
	 * Обновляет права роли после регистрации кастомных типов записей.
	 * Это важно для уже активированного плагина: повторная активация не требуется.
	 */
	public static function refresh_role_capabilities() {
		self::create_role();

		$role = get_role( self::ROLE );
		if ( ! $role ) {
			return;
		}

		// Добавляем capabilities всех типов записей, разрешённых Bridge, включая CPT service.
		foreach ( self::instance()->allowed_post_types() as $post_type_name ) {
			$post_type = get_post_type_object( $post_type_name );
			if ( ! $post_type || empty( $post_type->cap ) ) {
				continue;
			}

			foreach ( (array) $post_type->cap as $capability ) {
				if ( is_string( $capability ) && '' !== $capability ) {
					$role->add_cap( $capability, true );
				}
			}

			$taxonomies = get_object_taxonomies( $post_type_name, 'objects' );
			foreach ( $taxonomies as $taxonomy ) {
				if ( empty( $taxonomy->cap ) ) {
					continue;
				}
				foreach ( (array) $taxonomy->cap as $capability ) {
					if ( is_string( $capability ) && '' !== $capability ) {
						$role->add_cap( $capability, true );
					}
				}
			}
		}
	}

	private static function create_log_table() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table_name      = $wpdb->prefix . self::TABLE_SUFFIX;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table_name} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			operation_id varchar(64) NOT NULL,
			user_id bigint(20) unsigned NOT NULL DEFAULT 0,
			action varchar(64) NOT NULL,
			object_type varchar(32) NOT NULL DEFAULT '',
			object_id bigint(20) unsigned NOT NULL DEFAULT 0,
			field_name varchar(191) NOT NULL DEFAULT '',
			old_value longtext NULL,
			new_value longtext NULL,
			created_at datetime NOT NULL,
			PRIMARY KEY  (id),
			KEY operation_id (operation_id),
			KEY object_id (object_id),
			KEY created_at (created_at)
		) {$charset_collate};";

		dbDelta( $sql );
	}

	public function register_routes() {
		register_rest_route(
			self::REST_NAMESPACE,
			'/health',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'health' ),
				'permission_callback' => array( $this, 'can_read' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE,
			'/posts',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'list_posts' ),
					'permission_callback' => array( $this, 'can_read' ),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_post' ),
					'permission_callback' => array( $this, 'can_write' ),
				),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE,
			'/post-types',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'list_post_types' ),
				'permission_callback' => array( $this, 'can_read' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE,
			'/posts/(?P<id>\d+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_post' ),
					'permission_callback' => array( $this, 'can_read_post' ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_post' ),
					'permission_callback' => array( $this, 'can_edit_post' ),
				),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE,
			'/posts/(?P<id>\d+)/acf',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_acf' ),
					'permission_callback' => array( $this, 'can_read_post' ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_acf' ),
					'permission_callback' => array( $this, 'can_edit_post' ),
				),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE,
			'/posts/(?P<id>\d+)/seo',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_seo' ),
					'permission_callback' => array( $this, 'can_read_post' ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_seo' ),
					'permission_callback' => array( $this, 'can_edit_post' ),
				),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE,
			'/media/upload',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'upload_media' ),
				'permission_callback' => array( $this, 'can_upload_media' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE,
			'/media/sideload',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'sideload_media' ),
				'permission_callback' => array( $this, 'can_upload_media' ),
			)
		);



		register_rest_route(
			self::REST_NAMESPACE,
			'/screenshot/capture',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'capture_screenshot' ),
				'permission_callback' => array( $this, 'can_upload_media' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE,
			'/posts/(?P<id>\d+)/thumbnail',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'set_post_thumbnail_route' ),
				'permission_callback' => array( $this, 'can_edit_post' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE,
			'/links/scan',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'scan_links' ),
				'permission_callback' => array( $this, 'can_read' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE,
			'/links/replace',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'replace_links' ),
				'permission_callback' => array( $this, 'can_write' ),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE,
			'/audit',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_audit' ),
				'permission_callback' => array( $this, 'can_read' ),
			)
		);
	}

	public function can_read() {
		return current_user_can( self::CAPABILITY );
	}

	public function can_write() {
		return current_user_can( self::CAPABILITY );
	}

	public function can_upload_media() {
		return $this->can_write() && current_user_can( 'upload_files' );
	}

	public function can_read_post( WP_REST_Request $request ) {
		$post_id = absint( $request['id'] );
		return $this->can_read() && $post_id && current_user_can( 'read_post', $post_id );
	}

	public function can_edit_post( WP_REST_Request $request ) {
		$post_id = absint( $request['id'] );
		return $this->can_write() && $post_id && current_user_can( 'edit_post', $post_id );
	}

	public function health() {
		return rest_ensure_response(
			array(
				'ok'             => true,
				'version'        => CWB_VERSION,
				'wordpress'      => get_bloginfo( 'version' ),
				'acf_available'  => function_exists( 'get_fields' ) && function_exists( 'update_field' ),
				'allowed_types'  => $this->allowed_post_types(),
				'current_user'   => get_current_user_id(),
				'publish_allowed'=> $this->publish_allowed(),
				'publish_post_types' => $this->publish_allowed() ? array_values( array_intersect( array( 'hp_listing', 'hp_listing_attribute', 'hp_vendor' ), $this->allowed_post_types() ) ) : array(),
				'seo'            => array(
					'endpoint' => '/posts/{id}/seo',
					'fields'   => array( 'rank_math_title', 'rank_math_description', 'rank_math_focus_keyword', 'rank_math_canonical_url', 'rank_math_robots' ),
				),
				'screenshot_worker'=> array(
					'exec_enabled' => function_exists( 'exec' ),
					'node' => $this->find_binary( 'node' ),
					'npm' => $this->find_binary( 'npm' ),
				),
			)
		);
	}


	private function find_binary( $name ) {
		if ( ! function_exists( 'exec' ) ) return false;
		$out = array(); $code = 1;
		exec( 'command -v ' . escapeshellarg( $name ) . ' 2>/dev/null', $out, $code );
		return 0 === $code && ! empty( $out[0] ) ? sanitize_text_field( $out[0] ) : false;
	}

	private function allowed_post_types() {
		$types = array( 'page', 'post', 'plugin', 'wpds-case', 'service' );

		// HivePress and its extensions register their models as hp_* post types.
		// Discover them at runtime so newly installed extensions work without a
		// Bridge release or a site-specific allowlist filter.
		foreach ( get_post_types( array(), 'names' ) as $post_type ) {
			if ( 0 === strpos( $post_type, 'hp_' ) ) {
				$types[] = $post_type;
			}
		}

		return array_values( array_unique( array_map( 'sanitize_key', (array) apply_filters( 'cwb_allowed_post_types', $types ) ) ) );
	}

	public function list_post_types() {
		$items = array();
		foreach ( $this->allowed_post_types() as $name ) {
			$object = get_post_type_object( $name );
			if ( ! $object ) {
				continue;
			}

			$items[] = array(
				'name'       => $name,
				'label'      => $object->label,
				'hivepress'  => 0 === strpos( $name, 'hp_' ),
				'hierarchical' => (bool) $object->hierarchical,
				'taxonomies' => array_values( get_object_taxonomies( $name, 'names' ) ),
				'supports'   => array_keys( (array) get_all_post_type_supports( $name ) ),
			);
		}

		return rest_ensure_response( array( 'items' => $items ) );
	}

	public function publish_allowed( $post_type = '' ) {
		if ( ! current_user_can( self::PUBLISH_CAPABILITY ) ) {
			return false;
		}
		$publish_types = array( 'hp_listing', 'hp_listing_attribute', 'hp_vendor' );
		return ! $post_type || in_array( sanitize_key( $post_type ), $publish_types, true );
	}

	private function normalize_post_type( $post_type ) {
		$post_type = sanitize_key( $post_type );
		return 'wp-plugins' === $post_type ? 'plugin' : $post_type;
	}

	private function assert_allowed_post_type( $post_type ) {
		$post_type = $this->normalize_post_type( $post_type );
		if ( ! in_array( $post_type, $this->allowed_post_types(), true ) ) {
			return new WP_Error( 'cwb_forbidden_post_type', 'Этот тип записи не разрешён.', array( 'status' => 403 ) );
		}
		return true;
	}

	private function normalize_status( $status, $existing_status = 'draft', $post_type = '' ) {
		$status = sanitize_key( (string) $status );
		if ( '' === $status ) {
			return $existing_status;
		}
		if ( 'publish' === $status && ! $this->publish_allowed( $post_type ) ) {
			return 'draft';
		}
		$allowed = array( 'draft', 'pending', 'private', 'publish' );
		return in_array( $status, $allowed, true ) ? $status : $existing_status;
	}

	public function list_posts( WP_REST_Request $request ) {
		$post_type = $this->normalize_post_type( $request->get_param( 'post_type' ) ?: 'page' );
		$allowed   = $this->assert_allowed_post_type( $post_type );
		if ( is_wp_error( $allowed ) ) {
			return $allowed;
		}

		$per_page = min( 100, max( 1, absint( $request->get_param( 'per_page' ) ?: 20 ) ) );
		$page     = max( 1, absint( $request->get_param( 'page' ) ?: 1 ) );
		$status   = sanitize_key( $request->get_param( 'status' ) ?: 'any' );
		$search   = sanitize_text_field( $request->get_param( 'search' ) ?: '' );

		$query = new WP_Query(
			array(
				'post_type'      => $post_type,
				'post_status'    => $status,
				's'              => $search,
				'posts_per_page' => $per_page,
				'paged'          => $page,
				'orderby'        => 'modified',
				'order'          => 'DESC',
			)
		);

		$items = array_map( array( $this, 'prepare_post_summary' ), $query->posts );

		return rest_ensure_response(
			array(
				'items'       => $items,
				'total'       => (int) $query->found_posts,
				'total_pages' => (int) $query->max_num_pages,
				'page'        => $page,
			)
		);
	}

	public function get_post( WP_REST_Request $request ) {
		$post = get_post( absint( $request['id'] ) );
		if ( ! $post ) {
			return new WP_Error( 'cwb_not_found', 'Запись не найдена.', array( 'status' => 404 ) );
		}
		$allowed = $this->assert_allowed_post_type( $post->post_type );
		if ( is_wp_error( $allowed ) ) {
			return $allowed;
		}
		return rest_ensure_response( $this->prepare_post_full( $post ) );
	}

	public function create_post( WP_REST_Request $request ) {
		$data      = $request->get_json_params();
		$post_type = $this->normalize_post_type( isset( $data['post_type'] ) ? $data['post_type'] : 'page' );
		$allowed   = $this->assert_allowed_post_type( $post_type );
		if ( is_wp_error( $allowed ) ) {
			return $allowed;
		}

		$post_type_object = get_post_type_object( $post_type );
		if ( ! $post_type_object ) {
			return new WP_Error( 'cwb_invalid_post_type', 'Тип записи не зарегистрирован.', array( 'status' => 400 ) );
		}

		if ( ! current_user_can( $post_type_object->cap->create_posts ) ) {
			return new WP_Error( 'cwb_cannot_create', 'Недостаточно прав для создания записи.', array( 'status' => 403 ) );
		}

		$postarr = array(
			'post_type'    => $post_type,
			'post_title'   => sanitize_text_field( $data['title'] ?? '' ),
			'post_name'    => sanitize_title( $data['slug'] ?? '' ),
			'post_content' => wp_kses_post( $data['content'] ?? '' ),
			'post_excerpt' => wp_kses_post( $data['excerpt'] ?? '' ),
			'post_status'  => $this->normalize_status( $data['status'] ?? 'draft', 'draft', $post_type ),
			'post_parent'  => absint( $data['parent'] ?? 0 ),
			'menu_order'   => intval( $data['menu_order'] ?? 0 ),
		);

		$post_id = wp_insert_post( wp_slash( $postarr ), true );
		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		if ( ! empty( $data['template'] ) && 'page' === $post_type ) {
			update_post_meta( $post_id, '_wp_page_template', sanitize_text_field( $data['template'] ) );
		}

		if ( ! empty( $data['featured_media'] ) ) {
			$thumbnail_result = $this->assign_featured_media( $post_id, absint( $data['featured_media'] ) );
			if ( is_wp_error( $thumbnail_result ) ) {
				return $thumbnail_result;
			}
		}

		$this->log_change( 'create_post', $post_type, $post_id, '', null, $postarr );

		if ( ! empty( $data['acf'] ) && is_array( $data['acf'] ) ) {
			$acf_result = $this->apply_acf_updates( $post_id, $data['acf'] );
			if ( is_wp_error( $acf_result ) ) {
				return $acf_result;
			}
		}

		$hivepress_result = $this->apply_hivepress_data( $post_id, $post_type, $data );
		if ( is_wp_error( $hivepress_result ) ) {
			return $hivepress_result;
		}

		return new WP_REST_Response( $this->prepare_post_full( get_post( $post_id ) ), 201 );
	}

	public function update_post( WP_REST_Request $request ) {
		$post_id = absint( $request['id'] );
		$post    = get_post( $post_id );
		if ( ! $post ) {
			return new WP_Error( 'cwb_not_found', 'Запись не найдена.', array( 'status' => 404 ) );
		}
		$allowed = $this->assert_allowed_post_type( $post->post_type );
		if ( is_wp_error( $allowed ) ) {
			return $allowed;
		}

		$data = $request->get_json_params();
		wp_save_post_revision( $post_id );

		$update = array( 'ID' => $post_id );
		$map    = array(
			'title'      => 'post_title',
			'slug'       => 'post_name',
			'content'    => 'post_content',
			'excerpt'    => 'post_excerpt',
			'parent'     => 'post_parent',
			'menu_order' => 'menu_order',
		);

		foreach ( $map as $input => $field ) {
			if ( ! array_key_exists( $input, $data ) ) {
				continue;
			}
			if ( in_array( $input, array( 'content', 'excerpt' ), true ) ) {
				$update[ $field ] = wp_kses_post( $data[ $input ] );
			} elseif ( 'slug' === $input ) {
				$update[ $field ] = sanitize_title( $data[ $input ] );
			} elseif ( in_array( $input, array( 'parent', 'menu_order' ), true ) ) {
				$update[ $field ] = intval( $data[ $input ] );
			} else {
				$update[ $field ] = sanitize_text_field( $data[ $input ] );
			}
		}

		if ( array_key_exists( 'status', $data ) ) {
			$update['post_status'] = $this->normalize_status( $data['status'], $post->post_status, $post->post_type );
		}

		if ( count( $update ) > 1 ) {
			$before = $this->prepare_post_full( $post );
			$result = wp_update_post( wp_slash( $update ), true );
			if ( is_wp_error( $result ) ) {
				return $result;
			}
			$this->log_change( 'update_post', $post->post_type, $post_id, '', $before, $update );
		}

		if ( array_key_exists( 'template', $data ) && 'page' === $post->post_type ) {
			$old = get_page_template_slug( $post_id );
			$new = sanitize_text_field( $data['template'] );
			update_post_meta( $post_id, '_wp_page_template', $new );
			$this->log_change( 'update_template', $post->post_type, $post_id, '_wp_page_template', $old, $new );
		}

		if ( array_key_exists( 'featured_media', $data ) ) {
			$attachment_id = absint( $data['featured_media'] );
			if ( 0 === $attachment_id ) {
				delete_post_thumbnail( $post_id );
				$this->log_change( 'remove_thumbnail', $post->post_type, $post_id, '_thumbnail_id', get_post_thumbnail_id( $post_id ), 0 );
			} else {
				$thumbnail_result = $this->assign_featured_media( $post_id, $attachment_id );
				if ( is_wp_error( $thumbnail_result ) ) {
					return $thumbnail_result;
				}
			}
		}

		if ( ! empty( $data['acf'] ) && is_array( $data['acf'] ) ) {
			$acf_result = $this->apply_acf_updates( $post_id, $data['acf'] );
			if ( is_wp_error( $acf_result ) ) {
				return $acf_result;
			}
		}

		$hivepress_result = $this->apply_hivepress_data( $post_id, $post->post_type, $data );
		if ( is_wp_error( $hivepress_result ) ) {
			return $hivepress_result;
		}

		return rest_ensure_response( $this->prepare_post_full( get_post( $post_id ) ) );
	}

	private function apply_hivepress_data( $post_id, $post_type, array $data ) {
		if ( 0 !== strpos( $post_type, 'hp_' ) ) {
			return true;
		}

		if ( isset( $data['hivepress_meta'] ) ) {
			if ( ! is_array( $data['hivepress_meta'] ) ) {
				return new WP_Error( 'cwb_invalid_hivepress_meta', 'hivepress_meta должен быть JSON-объектом.', array( 'status' => 400 ) );
			}

			foreach ( $data['hivepress_meta'] as $key => $value ) {
				$key = sanitize_key( $key );
				if ( 0 !== strpos( $key, 'hp_' ) || $this->is_sensitive_meta_key( $key ) ) {
					return new WP_Error( 'cwb_forbidden_hivepress_meta', 'Недопустимое HivePress meta-поле: ' . $key, array( 'status' => 400 ) );
				}

				$old = get_post_meta( $post_id, $key, true );
				$new = $this->sanitize_meta_value( $value );
				if ( null === $value || '' === $value ) {
					delete_post_meta( $post_id, $key );
				} else {
					update_post_meta( $post_id, $key, $new );
				}
				$this->log_change( 'update_hivepress_meta', $post_type, $post_id, $key, $old, get_post_meta( $post_id, $key, true ) );
			}
		}

		if ( isset( $data['taxonomies'] ) ) {
			if ( ! is_array( $data['taxonomies'] ) ) {
				return new WP_Error( 'cwb_invalid_taxonomies', 'taxonomies должен быть JSON-объектом.', array( 'status' => 400 ) );
			}

			foreach ( $data['taxonomies'] as $taxonomy => $terms ) {
				$taxonomy = sanitize_key( $taxonomy );
				if ( 0 !== strpos( $taxonomy, 'hp_' ) || ! is_object_in_taxonomy( $post_type, $taxonomy ) ) {
					return new WP_Error( 'cwb_invalid_hivepress_taxonomy', 'Таксономия не относится к этому типу записи: ' . $taxonomy, array( 'status' => 400 ) );
				}
				$taxonomy_object = get_taxonomy( $taxonomy );
				if ( ! $taxonomy_object || ! current_user_can( $taxonomy_object->cap->assign_terms ) ) {
					return new WP_Error( 'cwb_cannot_assign_terms', 'Недостаточно прав для назначения терминов: ' . $taxonomy, array( 'status' => 403 ) );
				}

				$term_ids = array_values( array_filter( array_map( 'absint', (array) $terms ) ) );
				$result   = wp_set_object_terms( $post_id, $term_ids, $taxonomy, false );
				if ( is_wp_error( $result ) ) {
					return $result;
				}
				$this->log_change( 'update_hivepress_terms', $post_type, $post_id, $taxonomy, null, $term_ids );
			}
		}

		return true;
	}

	private function sanitize_meta_value( $value ) {
		if ( is_array( $value ) ) {
			return array_map( array( $this, 'sanitize_meta_value' ), $value );
		}
		if ( is_bool( $value ) || is_int( $value ) || is_float( $value ) ) {
			return $value;
		}
		return sanitize_textarea_field( (string) $value );
	}

	private function is_sensitive_meta_key( $key ) {
		return (bool) preg_match( '/(?:password|passwd|secret|token|api[_-]?key|private[_-]?key|application[_-]?password)/i', $key );
	}

	public function upload_media( WP_REST_Request $request ) {
		$files = $request->get_file_params();
		if ( empty( $files['file'] ) || empty( $files['file']['tmp_name'] ) ) {
			return new WP_Error( 'cwb_missing_file', 'Передайте файл в multipart-поле file.', array( 'status' => 400 ) );
		}

		$file = $files['file'];
		$validation = $this->validate_media_file( $file['tmp_name'], $file['name'], isset( $file['size'] ) ? (int) $file['size'] : 0 );
		if ( is_wp_error( $validation ) ) {
			return $validation;
		}

		$hash = hash_file( 'sha256', $file['tmp_name'] );
		$duplicate = $this->find_attachment_by_meta( '_cwb_file_hash', $hash );
		if ( $duplicate ) {
			$this->maybe_assign_uploaded_media( $duplicate, $request );
			return rest_ensure_response( array( 'duplicate' => true, 'media' => $this->prepare_media( $duplicate ) ) );
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$post_id = absint( $request->get_param( 'post_id' ) );
		$attachment_id = media_handle_sideload(
			array(
				'name'     => sanitize_file_name( $file['name'] ),
				'tmp_name' => $file['tmp_name'],
				'error'    => 0,
				'size'     => (int) $file['size'],
			),
			$post_id,
			sanitize_text_field( $request->get_param( 'description' ) ?: '' )
		);

		if ( is_wp_error( $attachment_id ) ) {
			return $attachment_id;
		}

		update_post_meta( $attachment_id, '_cwb_file_hash', $hash );
		$this->update_attachment_fields( $attachment_id, $request );
		$assign = $this->maybe_assign_uploaded_media( $attachment_id, $request );
		if ( is_wp_error( $assign ) ) {
			return $assign;
		}
		$this->log_change( 'upload_media', 'attachment', $attachment_id, '', null, $this->prepare_media( $attachment_id ) );

		return new WP_REST_Response( array( 'duplicate' => false, 'media' => $this->prepare_media( $attachment_id ) ), 201 );
	}

	public function sideload_media( WP_REST_Request $request ) {
		$data = $request->get_json_params();
		$url  = esc_url_raw( isset( $data['url'] ) ? $data['url'] : '' );
		if ( ! $url || ! wp_http_validate_url( $url ) ) {
			return new WP_Error( 'cwb_invalid_url', 'Передайте корректный публичный HTTP/HTTPS URL изображения.', array( 'status' => 400 ) );
		}

		$duplicate = $this->find_attachment_by_meta( '_cwb_source_url', $url );
		if ( $duplicate ) {
			$this->maybe_assign_uploaded_media( $duplicate, $request );
			return rest_ensure_response( array( 'duplicate' => true, 'media' => $this->prepare_media( $duplicate ) ) );
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$tmp = download_url( $url, 30 );
		if ( is_wp_error( $tmp ) ) {
			return new WP_Error( 'cwb_download_failed', $tmp->get_error_message(), array( 'status' => 400 ) );
		}

		$path = wp_parse_url( $url, PHP_URL_PATH );
		$name = sanitize_file_name( basename( $path ?: 'image.jpg' ) );
		if ( ! $name || false === strpos( $name, '.' ) ) {
			$name = 'image.jpg';
		}
		$size = file_exists( $tmp ) ? filesize( $tmp ) : 0;
		$validation = $this->validate_media_file( $tmp, $name, $size );
		if ( is_wp_error( $validation ) ) {
			@unlink( $tmp );
			return $validation;
		}

		$hash = hash_file( 'sha256', $tmp );
		$hash_duplicate = $this->find_attachment_by_meta( '_cwb_file_hash', $hash );
		if ( $hash_duplicate ) {
			@unlink( $tmp );
			update_post_meta( $hash_duplicate, '_cwb_source_url', $url );
			$this->maybe_assign_uploaded_media( $hash_duplicate, $request );
			return rest_ensure_response( array( 'duplicate' => true, 'media' => $this->prepare_media( $hash_duplicate ) ) );
		}

		$attachment_id = media_handle_sideload(
			array( 'name' => $name, 'tmp_name' => $tmp, 'error' => 0, 'size' => $size ),
			absint( isset( $data['post_id'] ) ? $data['post_id'] : 0 ),
			sanitize_text_field( isset( $data['description'] ) ? $data['description'] : '' )
		);
		if ( is_wp_error( $attachment_id ) ) {
			@unlink( $tmp );
			return $attachment_id;
		}

		update_post_meta( $attachment_id, '_cwb_source_url', $url );
		update_post_meta( $attachment_id, '_cwb_file_hash', $hash );
		$this->update_attachment_fields( $attachment_id, $request );
		$assign = $this->maybe_assign_uploaded_media( $attachment_id, $request );
		if ( is_wp_error( $assign ) ) {
			return $assign;
		}
		$this->log_change( 'sideload_media', 'attachment', $attachment_id, 'source_url', null, $url );

		return new WP_REST_Response( array( 'duplicate' => false, 'media' => $this->prepare_media( $attachment_id ) ), 201 );
	}


	public function capture_screenshot( WP_REST_Request $request ) {
		$data = $request->get_json_params();
		$url = esc_url_raw( isset( $data['url'] ) ? $data['url'] : '' );
		if ( ! $url || ! $this->is_public_http_url( $url ) ) {
			return new WP_Error( 'cwb_invalid_capture_url', 'Разрешены только публичные HTTP/HTTPS URL.', array( 'status' => 400 ) );
		}
		if ( ! function_exists( 'exec' ) ) {
			return new WP_Error( 'cwb_exec_disabled', 'На сервере отключён exec(). Screenshot worker требует Node.js/Playwright.', array( 'status' => 501 ) );
		}

		$uploads = wp_upload_dir();
		if ( ! empty( $uploads['error'] ) ) {
			return new WP_Error( 'cwb_upload_dir_error', $uploads['error'], array( 'status' => 500 ) );
		}
		$runtime = trailingslashit( $uploads['basedir'] ) . 'codex-bridge-runtime';
		if ( ! wp_mkdir_p( $runtime ) ) {
			return new WP_Error( 'cwb_runtime_dir_failed', 'Не удалось создать runtime-папку screenshot worker.', array( 'status' => 500 ) );
		}
		$outfile = trailingslashit( $runtime ) . 'capture-' . wp_generate_uuid4() . '.webp';
		$worker = CWB_DIR . 'server/capture.mjs';
		$bootstrap = CWB_DIR . 'server/bootstrap.sh';

		$width = max( 320, min( 2560, absint( isset( $data['width'] ) ? $data['width'] : 1440 ) ) );
		$height = max( 480, min( 3000, absint( isset( $data['height'] ) ? $data['height'] : 1200 ) ) );
		$max_height = max( 0, min( 12000, absint( isset( $data['max_height'] ) ? $data['max_height'] : 3000 ) ) );
		$selector = isset( $data['selector'] ) ? sanitize_text_field( $data['selector'] ) : '';
		$full_page = ! isset( $data['full_page'] ) || (bool) $data['full_page'];
		$quality = max( 45, min( 92, absint( isset( $data['quality'] ) ? $data['quality'] : 78 ) ) );

		$cmd = 'bash ' . escapeshellarg( $bootstrap ) . ' ' . escapeshellarg( $runtime ) . ' && ' .
			'node ' . escapeshellarg( $worker ) .
			' --runtime=' . escapeshellarg( $runtime ) .
			' --url=' . escapeshellarg( $url ) .
			' --output=' . escapeshellarg( $outfile ) .
			' --width=' . escapeshellarg( (string) $width ) .
			' --height=' . escapeshellarg( (string) $height ) .
			' --max-height=' . escapeshellarg( (string) $max_height ) .
			' --quality=' . escapeshellarg( (string) $quality ) .
			' --full-page=' . escapeshellarg( $full_page ? '1' : '0' );
		if ( $selector ) {
			$cmd .= ' --selector=' . escapeshellarg( $selector );
		}
		$cmd .= ' 2>&1';

		$output = array();
		$exit_code = 0;
		@set_time_limit( 180 );
		exec( $cmd, $output, $exit_code );
		if ( 0 !== $exit_code || ! file_exists( $outfile ) ) {
			@unlink( $outfile );
			return new WP_Error( 'cwb_capture_failed', 'Screenshot worker завершился с ошибкой: ' . implode( "\n", array_slice( $output, -12 ) ), array( 'status' => 502 ) );
		}

		$validation = $this->validate_media_file( $outfile, 'capture.webp', filesize( $outfile ) );
		if ( is_wp_error( $validation ) ) {
			@unlink( $outfile );
			return $validation;
		}
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$slug = ! empty( $data['filename'] ) ? sanitize_file_name( $data['filename'] ) : sanitize_file_name( wp_parse_url( $url, PHP_URL_HOST ) . '-screenshot' );
		if ( '.webp' !== strtolower( substr( $slug, -5 ) ) ) $slug .= '.webp';
		$attachment_id = media_handle_sideload( array( 'name' => $slug, 'tmp_name' => $outfile, 'error' => 0, 'size' => filesize( $outfile ) ), absint( isset( $data['post_id'] ) ? $data['post_id'] : 0 ), sanitize_text_field( isset( $data['description'] ) ? $data['description'] : '' ) );
		if ( is_wp_error( $attachment_id ) ) {
			@unlink( $outfile );
			return $attachment_id;
		}
		update_post_meta( $attachment_id, '_cwb_source_url', $url );
		update_post_meta( $attachment_id, '_cwb_file_hash', hash_file( 'sha256', get_attached_file( $attachment_id ) ) );
		$request->set_param( 'source_url', $url );
		$this->update_attachment_fields( $attachment_id, $request );
		$assign = $this->maybe_assign_uploaded_media( $attachment_id, $request );
		if ( is_wp_error( $assign ) ) return $assign;
		$media = $this->prepare_media( $attachment_id );
		$media['gutenberg'] = '<!-- wp:image {"id":' . (int) $attachment_id . ',"sizeSlug":"full","linkDestination":"none"} -->\n<figure class="wp-block-image size-full"><img src="' . esc_url( $media['url'] ) . '" alt="' . esc_attr( $media['alt'] ) . '" class="wp-image-' . (int) $attachment_id . '"/></figure>\n<!-- /wp:image -->';
		$this->log_change( 'capture_screenshot', 'attachment', $attachment_id, 'source_url', null, $url );
		return new WP_REST_Response( array( 'ok' => true, 'media' => $media ), 201 );
	}

	private function is_public_http_url( $url ) {
		$parts = wp_parse_url( $url );
		if ( empty( $parts['scheme'] ) || empty( $parts['host'] ) || ! in_array( strtolower( $parts['scheme'] ), array( 'http', 'https' ), true ) ) return false;
		$host = strtolower( $parts['host'] );
		if ( in_array( $host, array( 'localhost', 'localhost.localdomain' ), true ) || '.local' === substr( $host, -6 ) ) return false;
		$ips = array();
		if ( filter_var( $host, FILTER_VALIDATE_IP ) ) $ips[] = $host;
		else {
			$a = @dns_get_record( $host, DNS_A | DNS_AAAA );
			if ( is_array( $a ) ) foreach ( $a as $row ) { if ( ! empty( $row['ip'] ) ) $ips[] = $row['ip']; if ( ! empty( $row['ipv6'] ) ) $ips[] = $row['ipv6']; }
		}
		if ( empty( $ips ) ) return false;
		foreach ( $ips as $ip ) if ( false === filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) return false;
		return true;
	}

	public function set_post_thumbnail_route( WP_REST_Request $request ) {
		$data = $request->get_json_params();
		$post_id = absint( $request['id'] );
		$attachment_id = absint( isset( $data['attachment_id'] ) ? $data['attachment_id'] : 0 );
		if ( 0 === $attachment_id ) {
			$old = get_post_thumbnail_id( $post_id );
			delete_post_thumbnail( $post_id );
			$this->log_change( 'remove_thumbnail', get_post_type( $post_id ), $post_id, '_thumbnail_id', $old, 0 );
			return rest_ensure_response( array( 'post_id' => $post_id, 'featured_media' => 0 ) );
		}
		$result = $this->assign_featured_media( $post_id, $attachment_id );
		if ( is_wp_error( $result ) ) {
			return $result;
		}
		return rest_ensure_response( array( 'post_id' => $post_id, 'featured_media' => $attachment_id, 'media' => $this->prepare_media( $attachment_id ) ) );
	}

	private function validate_media_file( $tmp_name, $original_name, $size ) {
		$max_size = (int) apply_filters( 'cwb_max_media_size', 10 * MB_IN_BYTES );
		if ( ! file_exists( $tmp_name ) || $size < 1 ) {
			return new WP_Error( 'cwb_empty_file', 'Файл пуст или недоступен.', array( 'status' => 400 ) );
		}
		if ( $size > $max_size ) {
			return new WP_Error( 'cwb_file_too_large', 'Размер изображения превышает разрешённый лимит.', array( 'status' => 413, 'max_bytes' => $max_size ) );
		}
		$allowed_mimes = array( 'jpg|jpeg|jpe' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp' );
		$checked = wp_check_filetype_and_ext( $tmp_name, sanitize_file_name( $original_name ), $allowed_mimes );
		if ( empty( $checked['type'] ) || empty( $checked['ext'] ) || ! in_array( $checked['type'], $allowed_mimes, true ) ) {
			return new WP_Error( 'cwb_invalid_media_type', 'Разрешены только JPG, PNG, GIF и WebP.', array( 'status' => 415 ) );
		}
		return true;
	}

	private function update_attachment_fields( $attachment_id, WP_REST_Request $request ) {
		$data = $request->get_json_params();
		$params = array_merge( is_array( $data ) ? $data : array(), $request->get_params() );
		$update = array( 'ID' => $attachment_id );
		if ( ! empty( $params['title'] ) ) $update['post_title'] = sanitize_text_field( $params['title'] );
		if ( isset( $params['caption'] ) ) $update['post_excerpt'] = wp_kses_post( $params['caption'] );
		if ( isset( $params['description'] ) ) $update['post_content'] = wp_kses_post( $params['description'] );
		if ( count( $update ) > 1 ) wp_update_post( wp_slash( $update ) );
		if ( isset( $params['alt'] ) ) update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $params['alt'] ) );
		if ( ! empty( $params['source_url'] ) ) update_post_meta( $attachment_id, '_cwb_source_url', esc_url_raw( $params['source_url'] ) );
	}

	private function maybe_assign_uploaded_media( $attachment_id, WP_REST_Request $request ) {
		$data = $request->get_json_params();
		$params = array_merge( is_array( $data ) ? $data : array(), $request->get_params() );
		$post_id = absint( isset( $params['post_id'] ) ? $params['post_id'] : 0 );
		$set_featured = ! empty( $params['set_featured'] );
		if ( ! $post_id || ! $set_featured ) return true;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return new WP_Error( 'cwb_cannot_edit_post', 'Недостаточно прав для назначения миниатюры.', array( 'status' => 403 ) );
		return $this->assign_featured_media( $post_id, $attachment_id );
	}

	private function assign_featured_media( $post_id, $attachment_id ) {
		if ( 'attachment' !== get_post_type( $attachment_id ) || ! wp_attachment_is_image( $attachment_id ) ) {
			return new WP_Error( 'cwb_invalid_attachment', 'Указанный attachment_id не является изображением.', array( 'status' => 400 ) );
		}
		$old = get_post_thumbnail_id( $post_id );
		if ( ! set_post_thumbnail( $post_id, $attachment_id ) ) {
			return new WP_Error( 'cwb_thumbnail_failed', 'Не удалось назначить изображение миниатюрой.', array( 'status' => 500 ) );
		}
		$this->log_change( 'set_thumbnail', get_post_type( $post_id ), $post_id, '_thumbnail_id', $old, $attachment_id );
		return true;
	}

	private function find_attachment_by_meta( $key, $value ) {
		$ids = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'fields' => 'ids', 'posts_per_page' => 1, 'meta_key' => $key, 'meta_value' => $value ) );
		return $ids ? (int) $ids[0] : 0;
	}

	private function prepare_media( $attachment_id ) {
		$metadata = wp_get_attachment_metadata( $attachment_id );
		$file_path = get_attached_file( $attachment_id );
		return array(
			'id' => (int) $attachment_id,
			'url' => wp_get_attachment_url( $attachment_id ),
			'mime_type' => get_post_mime_type( $attachment_id ),
			'title' => get_the_title( $attachment_id ),
			'alt' => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
			'caption' => wp_get_attachment_caption( $attachment_id ),
			'width' => isset( $metadata['width'] ) ? (int) $metadata['width'] : 0,
			'height' => isset( $metadata['height'] ) ? (int) $metadata['height'] : 0,
			'filesize' => $file_path && file_exists( $file_path ) ? (int) filesize( $file_path ) : 0,
			'source_url' => get_post_meta( $attachment_id, '_cwb_source_url', true ),
		);
	}

	public function get_acf( WP_REST_Request $request ) {
		if ( ! function_exists( 'get_field_objects' ) ) {
			return new WP_Error( 'cwb_acf_missing', 'ACF не установлен или не активен.', array( 'status' => 501 ) );
		}
		$post_id = absint( $request['id'] );
		$objects = get_field_objects( $post_id, true, false );
		return rest_ensure_response(
			array(
				'post_id' => $post_id,
				'fields'  => $objects ? array_values( $objects ) : array(),
			)
		);
	}

	public function update_acf( WP_REST_Request $request ) {
		$post_id = absint( $request['id'] );
		$data    = $request->get_json_params();
		$fields  = isset( $data['fields'] ) && is_array( $data['fields'] ) ? $data['fields'] : $data;
		$result  = $this->apply_acf_updates( $post_id, $fields );
		if ( is_wp_error( $result ) ) {
			return $result;
		}
		return $this->get_acf( $request );
	}

	private function apply_acf_updates( $post_id, array $fields ) {
		if ( ! function_exists( 'update_field' ) || ! function_exists( 'get_field_object' ) ) {
			return new WP_Error( 'cwb_acf_missing', 'ACF не установлен или не активен.', array( 'status' => 501 ) );
		}

		$results = array();
		foreach ( $fields as $selector => $value ) {
			$selector = sanitize_text_field( (string) $selector );
			$field    = get_field_object( $selector, $post_id, false, false );
			if ( ! $field ) {
				return new WP_Error(
					'cwb_acf_field_not_found',
					'ACF-поле не найдено: ' . $selector,
					array( 'status' => 400 )
				);
			}

			$old     = get_field( $field['key'], $post_id, false );
			$updated = update_field( $field['key'], $value, $post_id );
			if ( false === $updated && $old !== $value ) {
				return new WP_Error(
					'cwb_acf_update_failed',
					'Не удалось обновить ACF-поле: ' . $selector,
					array( 'status' => 500 )
				);
			}
			$this->log_change( 'update_acf', get_post_type( $post_id ), $post_id, $field['name'], $old, $value );
			$results[ $field['name'] ] = true;
		}
		return $results;
	}

	public function get_seo( WP_REST_Request $request ) {
		$post_id = absint( $request['id'] );
		$post    = get_post( $post_id );
		if ( ! $post ) {
			return new WP_Error( 'cwb_not_found', 'Запись не найдена.', array( 'status' => 404 ) );
		}
		$allowed = $this->assert_allowed_post_type( $post->post_type );
		if ( is_wp_error( $allowed ) ) {
			return $allowed;
		}
		return rest_ensure_response( $this->prepare_seo_bundle( $post ) );
	}

	public function update_seo( WP_REST_Request $request ) {
		$post_id = absint( $request['id'] );
		$post    = get_post( $post_id );
		if ( ! $post ) {
			return new WP_Error( 'cwb_not_found', 'Запись не найдена.', array( 'status' => 404 ) );
		}
		$allowed = $this->assert_allowed_post_type( $post->post_type );
		if ( is_wp_error( $allowed ) ) {
			return $allowed;
		}

		$data = $request->get_json_params();
		if ( ! is_array( $data ) ) {
			return new WP_Error( 'cwb_invalid_json', 'Ожидается JSON-объект.', array( 'status' => 400 ) );
		}

		$forbidden = array( 'id', 'slug', 'url', 'post_type', 'status', 'title', 'content', 'excerpt', 'parent', 'menu_order', 'template', 'author', 'featured_media' );
		$forbidden_found = array_values( array_intersect( array_keys( $data ), $forbidden ) );
		if ( $forbidden_found ) {
			return new WP_Error(
				'cwb_seo_forbidden_fields',
				'SEO PATCH не может изменять поля записи: ' . implode( ', ', $forbidden_found ),
				array( 'status' => 400, 'forbidden_fields' => $forbidden_found )
			);
		}

		$map = array(
			'rank_math_title'         => 'rank_math_title',
			'rank_math_description'   => 'rank_math_description',
			'rank_math_focus_keyword' => 'rank_math_focus_keyword',
			'rank_math_canonical_url' => 'rank_math_canonical_url',
			'rank_math_robots'        => 'rank_math_robots',
			'seo_title'               => 'rank_math_title',
			'seo_description'         => 'rank_math_description',
			'focus_keyword'           => 'rank_math_focus_keyword',
			'canonical_url'           => 'rank_math_canonical_url',
			'robots'                  => 'rank_math_robots',
		);

		$unknown = array_values( array_diff( array_keys( $data ), array_keys( $map ) ) );
		if ( $unknown ) {
			return new WP_Error(
				'cwb_seo_unknown_fields',
				'Неизвестные SEO-поля: ' . implode( ', ', $unknown ),
				array( 'status' => 400, 'allowed_fields' => array_keys( $map ) )
			);
		}

		wp_save_post_revision( $post_id );
		$changed = array();
		foreach ( $data as $input_key => $value ) {
			$meta_key = $map[ $input_key ];
			$old      = get_post_meta( $post_id, $meta_key, true );
			$new      = $this->sanitize_seo_value( $meta_key, $value );
			if ( is_wp_error( $new ) ) {
				return $new;
			}

			if ( '' === $new || array() === $new ) {
				delete_post_meta( $post_id, $meta_key );
			} else {
				update_post_meta( $post_id, $meta_key, $new );
			}

			$after = get_post_meta( $post_id, $meta_key, true );
			if ( $after !== $old ) {
				$this->log_change( 'update_seo', $post->post_type, $post_id, $meta_key, $old, $after );
				$changed[] = $meta_key;
			}
		}

		$fresh = get_post( $post_id );
		$result = $this->prepare_seo_bundle( $fresh );
		$result['changed_fields'] = array_values( array_unique( $changed ) );
		return rest_ensure_response( $result );
	}

	private function sanitize_seo_value( $meta_key, $value ) {
		if ( 'rank_math_canonical_url' === $meta_key ) {
			if ( null === $value || '' === trim( (string) $value ) ) {
				return '';
			}
			$url = esc_url_raw( (string) $value, array( 'http', 'https' ) );
			if ( ! $url ) {
				return new WP_Error( 'cwb_invalid_canonical', 'canonical_url должен быть корректным HTTP/HTTPS URL.', array( 'status' => 400 ) );
			}
			return $url;
		}

		if ( 'rank_math_robots' === $meta_key ) {
			if ( null === $value || '' === $value ) {
				return array();
			}
			$robots = is_array( $value ) ? $value : preg_split( '/[\\s,]+/', (string) $value );
			$allowed = array( 'index', 'noindex', 'follow', 'nofollow', 'noarchive', 'nosnippet', 'noimageindex' );
			$robots = array_values( array_unique( array_filter( array_map( 'sanitize_key', $robots ) ) ) );
			$invalid = array_values( array_diff( $robots, $allowed ) );
			if ( $invalid ) {
				return new WP_Error( 'cwb_invalid_robots', 'Недопустимые robots-директивы: ' . implode( ', ', $invalid ), array( 'status' => 400, 'allowed' => $allowed ) );
			}
			if ( in_array( 'index', $robots, true ) && in_array( 'noindex', $robots, true ) ) {
				return new WP_Error( 'cwb_conflicting_robots', 'Нельзя одновременно передавать index и noindex.', array( 'status' => 400 ) );
			}
			if ( in_array( 'follow', $robots, true ) && in_array( 'nofollow', $robots, true ) ) {
				return new WP_Error( 'cwb_conflicting_robots', 'Нельзя одновременно передавать follow и nofollow.', array( 'status' => 400 ) );
			}
			return $robots;
		}

		return sanitize_text_field( is_scalar( $value ) ? (string) $value : '' );
	}

	private function prepare_seo_bundle( $post ) {
		$post_id = (int) $post->ID;
		$robots  = get_post_meta( $post_id, 'rank_math_robots', true );
		if ( ! is_array( $robots ) ) {
			$robots = $robots ? array_values( array_filter( preg_split( '/[\\s,]+/', (string) $robots ) ) ) : array();
		}
		$canonical = (string) get_post_meta( $post_id, 'rank_math_canonical_url', true );

		return array(
			'post' => $this->prepare_post_full( $post ),
			'seo' => array(
				'rank_math_available'     => defined( 'RANK_MATH_VERSION' ) || class_exists( 'RankMath\\Helper' ),
				'rank_math_title'         => (string) get_post_meta( $post_id, 'rank_math_title', true ),
				'rank_math_description'   => (string) get_post_meta( $post_id, 'rank_math_description', true ),
				'rank_math_focus_keyword' => (string) get_post_meta( $post_id, 'rank_math_focus_keyword', true ),
				'rank_math_canonical_url' => $canonical,
				'rank_math_robots'        => $robots,
				'canonical_fallback'      => $canonical ? $canonical : get_permalink( $post_id ),
				'robots_fallback'         => $robots ? $robots : array( 'index', 'follow' ),
				'h1_candidates'           => $this->extract_h1_candidates( $post ),
			),
			'acf' => $this->prepare_acf_values( $post_id ),
			'frontend_meta' => $this->prepare_frontend_meta( $post_id ),
			'public_html_check' => array(
				'url' => get_permalink( $post_id ),
				'note' => 'Итоговые canonical, robots и H1 нужно дополнительно проверить в публичном HTML: тема и Rank Math могут применять глобальные настройки и фильтры.',
			),
		);
	}

	private function prepare_acf_values( $post_id ) {
		if ( ! function_exists( 'get_field_objects' ) ) {
			return array( 'available' => false, 'fields' => array() );
		}
		$objects = get_field_objects( $post_id, true, true );
		$fields = array();
		if ( is_array( $objects ) ) {
			foreach ( $objects as $field ) {
				$name = isset( $field['name'] ) ? sanitize_key( $field['name'] ) : '';
				$is_sensitive = (bool) preg_match( '/(?:password|passwd|secret|token|api[_-]?key|private[_-]?key|application[_-]?password)/i', $name );
				$expose = (bool) apply_filters( 'cwb_expose_acf_field', ! $is_sensitive, $field, $post_id );
				if ( ! $expose ) {
					continue;
				}
				$fields[] = array(
					'key'   => isset( $field['key'] ) ? $field['key'] : '',
					'name'  => isset( $field['name'] ) ? $field['name'] : '',
					'label' => isset( $field['label'] ) ? $field['label'] : '',
					'type'  => isset( $field['type'] ) ? $field['type'] : '',
					'value' => isset( $field['value'] ) ? $field['value'] : null,
				);
			}
		}
		return array( 'available' => true, 'fields' => $fields );
	}

	private function prepare_frontend_meta( $post_id ) {
		$keys = array(
			'rank_math_title',
			'rank_math_description',
			'rank_math_focus_keyword',
			'rank_math_canonical_url',
			'rank_math_robots',
		);
		$post_type = get_post_type( $post_id );
		if ( function_exists( 'get_registered_meta_keys' ) && $post_type ) {
			$registered = get_registered_meta_keys( 'post', $post_type );
			foreach ( (array) $registered as $registered_key => $args ) {
				if ( ! empty( $args['show_in_rest'] ) && 0 !== strpos( $registered_key, '_' ) ) {
					$keys[] = $registered_key;
				}
			}
		}
		$keys = array_values( array_unique( array_map( 'sanitize_key', (array) apply_filters( 'cwb_frontend_meta_keys', $keys, $post_id ) ) ) );
		$result = array();
		foreach ( $keys as $key ) {
			if ( 0 === strpos( $key, '_' ) ) {
				continue;
			}
			$result[ $key ] = get_post_meta( $post_id, $key, true );
		}
		return $result;
	}

	private function extract_h1_candidates( $post ) {
		$candidates = array();
		if ( preg_match_all( '/<h1\\b[^>]*>(.*?)<\\/h1>/is', (string) $post->post_content, $matches ) ) {
			foreach ( $matches[1] as $match ) {
				$text = trim( wp_strip_all_tags( $match ) );
				if ( '' !== $text ) {
					$candidates[] = $text;
				}
			}
		}
		$title = trim( wp_strip_all_tags( get_the_title( $post ) ) );
		if ( '' !== $title ) {
			$candidates[] = $title;
		}
		return array_values( array_unique( $candidates ) );
	}

	public function scan_links( WP_REST_Request $request ) {
		$data = $request->get_json_params();
		$old  = isset( $data['old'] ) ? trim( (string) $data['old'] ) : '';
		if ( '' === $old ) {
			return new WP_Error( 'cwb_missing_old', 'Передайте строку old.', array( 'status' => 400 ) );
		}
		return rest_ensure_response( $this->find_link_matches( $old, $data['post_types'] ?? null ) );
	}

	public function replace_links( WP_REST_Request $request ) {
		$data = $request->get_json_params();
		$old  = isset( $data['old'] ) ? trim( (string) $data['old'] ) : '';
		$new  = isset( $data['new'] ) ? trim( (string) $data['new'] ) : '';
		if ( '' === $old || '' === $new ) {
			return new WP_Error( 'cwb_missing_values', 'Передайте строки old и new.', array( 'status' => 400 ) );
		}

		$matches = $this->find_link_matches( $old, $data['post_types'] ?? null );
		if ( empty( $data['apply'] ) ) {
			return rest_ensure_response( array( 'preview' => true, 'matches' => $matches ) );
		}

		$updated = array();
		foreach ( $matches['items'] as $item ) {
			$post_id = (int) $item['id'];
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				continue;
			}
			$post = get_post( $post_id );
			wp_save_post_revision( $post_id );
			$old_content = $post->post_content;
			$new_content = str_replace( $old, $new, $old_content );
			if ( $new_content !== $old_content ) {
				$result = wp_update_post( wp_slash( array( 'ID' => $post_id, 'post_content' => $new_content ) ), true );
				if ( ! is_wp_error( $result ) ) {
					$this->log_change( 'replace_link', $post->post_type, $post_id, 'post_content', $old_content, $new_content );
					$updated[] = $post_id;
				}
			}
		}

		return rest_ensure_response(
			array(
				'preview'     => false,
				'updated_ids' => $updated,
				'updated'     => count( $updated ),
			)
		);
	}

	private function find_link_matches( $needle, $requested_types = null ) {
		$types = $requested_types ? array_map( 'sanitize_key', (array) $requested_types ) : $this->allowed_post_types();
		$types = array_values( array_intersect( $types, $this->allowed_post_types() ) );
		$query = new WP_Query(
			array(
				'post_type'              => $types,
				'post_status'            => array( 'publish', 'draft', 'pending', 'private' ),
				'posts_per_page'         => -1,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);
		$items = array();
		foreach ( $query->posts as $post ) {
			$count = substr_count( $post->post_content, $needle );
			if ( $count < 1 ) {
				continue;
			}
			$items[] = array(
				'id'         => (int) $post->ID,
				'post_type'  => $post->post_type,
				'title'      => get_the_title( $post ),
				'status'     => $post->post_status,
				'occurrences'=> $count,
				'url'        => get_permalink( $post ),
			);
		}
		return array(
			'needle'      => $needle,
			'total_posts' => count( $items ),
			'items'       => $items,
		);
	}

	public function get_audit( WP_REST_Request $request ) {
		global $wpdb;
		$table = $wpdb->prefix . self::TABLE_SUFFIX;
		$limit = min( 200, max( 1, absint( $request->get_param( 'limit' ) ?: 50 ) ) );
		$rows  = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} ORDER BY id DESC LIMIT %d", $limit ), ARRAY_A );
		foreach ( $rows as &$row ) {
			$row['old_value'] = maybe_unserialize( $row['old_value'] );
			$row['new_value'] = maybe_unserialize( $row['new_value'] );
		}
		return rest_ensure_response( array( 'items' => $rows ) );
	}

	private function prepare_post_summary( $post ) {
		return array(
			'id'        => (int) $post->ID,
			'post_type' => $post->post_type,
			'title'     => get_the_title( $post ),
			'slug'      => $post->post_name,
			'status'    => $post->post_status,
			'modified'  => get_post_modified_time( DATE_ATOM, false, $post ),
			'url'       => get_permalink( $post ),
			'edit_url'  => get_edit_post_link( $post->ID, 'raw' ),
		);
	}

	private function prepare_post_full( $post ) {
		$data = $this->prepare_post_summary( $post );
		$data['content']    = $post->post_content;
		$data['excerpt']    = $post->post_excerpt;
		$data['parent']     = (int) $post->post_parent;
		$data['menu_order'] = (int) $post->menu_order;
		$data['template']   = 'page' === $post->post_type ? get_page_template_slug( $post->ID ) : '';
		$data['author']     = (int) $post->post_author;
		$data['featured_media'] = (int) get_post_thumbnail_id( $post->ID );
		if ( $data['featured_media'] ) {
			$data['featured_media_data'] = $this->prepare_media( $data['featured_media'] );
		}
		$data['acf'] = $this->prepare_acf_values( $post->ID );
		$data['frontend_meta'] = $this->prepare_frontend_meta( $post->ID );
		if ( 0 === strpos( $post->post_type, 'hp_' ) ) {
			$data['hivepress_meta'] = $this->prepare_hivepress_meta( $post->ID );
			$data['taxonomies']     = $this->prepare_hivepress_taxonomies( $post->ID, $post->post_type );
		}
		return $data;
	}

	private function prepare_hivepress_meta( $post_id ) {
		$result = array();
		foreach ( get_post_meta( $post_id ) as $key => $values ) {
			if ( 0 !== strpos( $key, 'hp_' ) || $this->is_sensitive_meta_key( $key ) ) {
				continue;
			}

			$value = count( $values ) > 1 ? $values : reset( $values );
			if ( is_array( $value ) ) {
				$value = array_map( 'maybe_unserialize', $value );
			} else {
				$value = maybe_unserialize( $value );
			}
			$result[ $key ] = $value;
		}

		return (array) apply_filters( 'cwb_hivepress_meta', $result, $post_id );
	}

	private function prepare_hivepress_taxonomies( $post_id, $post_type ) {
		$result = array();
		foreach ( get_object_taxonomies( $post_type, 'names' ) as $taxonomy ) {
			if ( 0 !== strpos( $taxonomy, 'hp_' ) ) {
				continue;
			}

			$terms = wp_get_object_terms( $post_id, $taxonomy );
			if ( is_wp_error( $terms ) ) {
				continue;
			}
			$result[ $taxonomy ] = array_map(
				function ( $term ) {
					return array(
						'id'     => (int) $term->term_id,
						'name'   => $term->name,
						'slug'   => $term->slug,
						'parent' => (int) $term->parent,
					);
				},
				$terms
			);
		}

		return $result;
	}

	public function log_change( $action, $object_type, $object_id, $field_name, $old_value, $new_value ) {
		global $wpdb;
		$table = $wpdb->prefix . self::TABLE_SUFFIX;
		$operation_id = 'op_' . gmdate( 'Ymd_His' ) . '_' . wp_generate_password( 6, false, false );
		$wpdb->insert(
			$table,
			array(
				'operation_id' => $operation_id,
				'user_id'      => get_current_user_id(),
				'action'       => sanitize_key( $action ),
				'object_type'  => sanitize_key( $object_type ),
				'object_id'    => absint( $object_id ),
				'field_name'   => sanitize_text_field( $field_name ),
				'old_value'    => maybe_serialize( $old_value ),
				'new_value'    => maybe_serialize( $new_value ),
				'created_at'   => current_time( 'mysql', true ),
			),
			array( '%s', '%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s' )
		);
		return $operation_id;
	}
}
