<?php
/**
 * REST API for managing HivePress listing category images through Codex Bridge.
 *
 * @package ListingHive
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Create an error response for the listing category image endpoint.
 *
 * @param string $code    Error code.
 * @param string $message Error message.
 * @param int    $status  HTTP status.
 * @return WP_Error
 */
function listinghive_category_image_error( $code, $message, $status = 400 ) {
	return new WP_Error( $code, $message, array( 'status' => $status ) );
}

/**
 * Check the shared Bridge capability.
 *
 * @return true|WP_Error
 */
function listinghive_category_image_can_read() {
	if ( current_user_can( 'use_codex_bridge' ) ) {
		return true;
	}

	if ( ! get_current_user_id() ) {
		return listinghive_category_image_error( 'listinghive_bridge_authentication_required', 'Authentication is required.', 401 );
	}

	return listinghive_category_image_error( 'listinghive_bridge_access_forbidden', 'The current user cannot use Codex Bridge.', 403 );
}

/**
 * Check Bridge and taxonomy management capabilities.
 *
 * @return true|WP_Error
 */
function listinghive_category_image_can_write() {
	$allowed = listinghive_category_image_can_read();

	if ( is_wp_error( $allowed ) ) {
		return $allowed;
	}

	$taxonomy = get_taxonomy( 'hp_listing_category' );

	if ( ! $taxonomy || empty( $taxonomy->cap->manage_terms ) || ! current_user_can( $taxonomy->cap->manage_terms ) ) {
		return listinghive_category_image_error( 'listinghive_cannot_manage_listing_categories', 'The current user cannot manage listing categories.', 403 );
	}

	return true;
}

/**
 * Get a listing category from a REST request.
 *
 * @param WP_REST_Request $request REST request.
 * @return WP_Term|WP_Error
 */
function listinghive_get_listing_category_for_image( WP_REST_Request $request ) {
	$term = get_term( absint( $request['id'] ), 'hp_listing_category' );

	if ( ! $term || is_wp_error( $term ) ) {
		return listinghive_category_image_error( 'listinghive_listing_category_not_found', 'Listing category not found.', 404 );
	}

	return $term;
}

/**
 * Format a listing category and its image for the REST API.
 *
 * @param WP_Term $term Listing category term.
 * @return array
 */
function listinghive_get_listing_category_image_data( $term ) {
	$image_id = absint( get_term_meta( $term->term_id, 'hp_image', true ) );
	$image    = null;

	if ( $image_id ) {
		$attachment = get_post( $image_id );

		if ( $attachment && 'attachment' === $attachment->post_type ) {
			$image = array(
				'title'     => get_the_title( $attachment ),
				'alt'       => (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true ),
				'mime_type' => (string) get_post_mime_type( $attachment ),
				'url'       => wp_get_attachment_url( $image_id ),
			);
		}
	}

	return array(
		'category_id'   => (int) $term->term_id,
		'category_name' => $term->name,
		'category_slug' => $term->slug,
		'image_id'      => $image_id,
		'image'         => $image,
	);
}

/**
 * Read a listing category image.
 *
 * @param WP_REST_Request $request REST request.
 * @return WP_REST_Response|WP_Error
 */
function listinghive_get_listing_category_image( WP_REST_Request $request ) {
	$term = listinghive_get_listing_category_for_image( $request );

	if ( is_wp_error( $term ) ) {
		return $term;
	}

	return rest_ensure_response( listinghive_get_listing_category_image_data( $term ) );
}

/**
 * Update or remove a listing category image.
 *
 * @param WP_REST_Request $request REST request.
 * @return WP_REST_Response|WP_Error
 */
function listinghive_update_listing_category_image( WP_REST_Request $request ) {
	$term = listinghive_get_listing_category_for_image( $request );

	if ( is_wp_error( $term ) ) {
		return $term;
	}

	$data = $request->get_json_params();

	if ( is_wp_error( $data ) || ! is_array( $data ) ) {
		return listinghive_category_image_error( 'listinghive_invalid_json', 'The request body must contain a valid JSON object.' );
	}

	if ( array( 'image_id' ) !== array_keys( $data ) ) {
		return listinghive_category_image_error( 'listinghive_invalid_image_fields', 'Only the image_id field is allowed.' );
	}

	$image_id = $data['image_id'];

	if ( ! is_int( $image_id ) || $image_id < 0 ) {
		return listinghive_category_image_error( 'listinghive_invalid_image_id', 'image_id must be a non-negative integer.' );
	}

	if ( $image_id ) {
		$attachment = get_post( $image_id );

		if ( ! $attachment || 'attachment' !== $attachment->post_type ) {
			return listinghive_category_image_error( 'listinghive_attachment_not_found', 'Attachment not found.', 404 );
		}

		if ( ! wp_attachment_is_image( $image_id ) ) {
			return listinghive_category_image_error( 'listinghive_attachment_not_image', 'image_id must reference an image attachment.' );
		}

		update_term_meta( $term->term_id, 'hp_image', $image_id );
	} else {
		delete_term_meta( $term->term_id, 'hp_image' );
	}

	$saved_image_id = absint( get_term_meta( $term->term_id, 'hp_image', true ) );

	if ( $saved_image_id !== $image_id ) {
		return listinghive_category_image_error( 'listinghive_image_save_failed', 'The listing category image could not be saved.', 500 );
	}

	return rest_ensure_response( listinghive_get_listing_category_image_data( $term ) );
}

/**
 * Register the listing category image route in the Bridge namespace.
 */
function listinghive_register_listing_category_image_route() {
	register_rest_route(
		'codex-bridge/v1',
		'/hivepress/listing-categories/(?P<id>\d+)/image',
		array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => 'listinghive_get_listing_category_image',
				'permission_callback' => 'listinghive_category_image_can_read',
			),
			array(
				'methods'             => 'PATCH',
				'callback'            => 'listinghive_update_listing_category_image',
				'permission_callback' => 'listinghive_category_image_can_write',
			),
		)
	);
}
add_action( 'rest_api_init', 'listinghive_register_listing_category_image_route' );
