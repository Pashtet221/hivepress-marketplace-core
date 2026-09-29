<?php
/**
 * Listing category image assignments.
 *
 * @package ListingHive
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Assign imported category artwork after HivePress registers its taxonomy.
 *
 * The option makes this a one-time content migration. It is only set after all
 * categories and attachments are available, so delayed media imports are safe.
 */
function listinghive_assign_listing_category_images() {
	$migration = 'listinghive_category_images_20260929';

	if ( get_option( $migration ) || ! taxonomy_exists( 'hp_listing_category' ) ) {
		return;
	}

	$assignments = [
		'avtokrany'                => 'avtokrany-kategoriya',
		'gusenichnye-ekskavatory' => 'gusenichnye-ekskavatory-kategoriya',
		'frontalnye-pogruzchiki'   => 'frontalnye-pogruzchiki-kategoriya',
	];

	foreach ( $assignments as $category_slug => $attachment_slug ) {
		$category   = get_term_by( 'slug', $category_slug, 'hp_listing_category' );
		$attachments = get_posts(
			[
				'name'           => $attachment_slug,
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			]
		);

		if ( ! $category || ! $attachments || ! wp_attachment_is_image( $attachments[0] ) ) {
			return;
		}

		update_term_meta( $category->term_id, 'hp_image', $attachments[0] );
	}

	update_option( $migration, 1, false );
}
add_action( 'init', 'listinghive_assign_listing_category_images', 100 );
