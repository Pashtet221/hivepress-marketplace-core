<?php
/**
 * Header menu migration.
 *
 * @package ListingHive
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Replaces ListingHive's imported demo menu with links relevant to the site.
 *
 * The migration deliberately runs only while the menu still contains the
 * English demo labels. Once migrated, the menu remains fully editable under
 * Appearance > Menus and subsequent administrator changes are not overwritten.
 */
function listinghive_migrate_header_menu() {
	if ( '1' === get_theme_mod( 'listinghive_header_menu_version' ) ) {
		return;
	}

	$locations = get_nav_menu_locations();
	$menu_id   = isset( $locations['header'] ) ? absint( $locations['header'] ) : 0;
	$menu      = $menu_id ? wp_get_nav_menu_object( $menu_id ) : wp_get_nav_menu_object( 'Header' );

	if ( ! $menu ) {
		return;
	}

	$menu_items = wp_get_nav_menu_items( $menu->term_id );
	$demo_titles = [
		'Home',
		'Listings',
		'Lessons',
		'Pages',
		'Vendor Page',
		'Dropdown Menu',
		'Blog',
		'First Item',
		'Second Item',
		'Third Item',
	];
	$has_demo_item = false;

	foreach ( (array) $menu_items as $menu_item ) {
		if ( in_array( $menu_item->title, $demo_titles, true ) ) {
			$has_demo_item = true;
			break;
		}
	}

	if ( ! $has_demo_item ) {
		return;
	}

	foreach ( (array) $menu_items as $menu_item ) {
		wp_delete_post( $menu_item->ID, true );
	}

	$pages = get_posts(
		[
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'post_name__in'  => [ 'home', 'listings', 'blog' ],
		]
	);
	$pages_by_slug = [];

	foreach ( $pages as $page ) {
		$pages_by_slug[ $page->post_name ] = $page;
	}

	$items = [
		[ 'title' => 'Главная', 'slug' => 'home' ],
		[ 'title' => 'Каталог спецтехники', 'slug' => 'listings' ],
		[ 'title' => 'Статьи', 'slug' => 'blog' ],
	];

	foreach ( $items as $position => $item ) {
		if ( empty( $pages_by_slug[ $item['slug'] ] ) ) {
			continue;
		}

		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			[
				'menu-item-object-id' => $pages_by_slug[ $item['slug'] ]->ID,
				'menu-item-object'    => 'page',
				'menu-item-type'      => 'post_type',
				'menu-item-title'     => $item['title'],
				'menu-item-position'  => $position + 1,
				'menu-item-status'    => 'publish',
			]
		);
	}

	if ( empty( $locations['header'] ) || (int) $locations['header'] !== (int) $menu->term_id ) {
		$locations['header'] = (int) $menu->term_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}

	set_theme_mod( 'listinghive_header_menu_version', '1' );
}
add_action( 'init', 'listinghive_migrate_header_menu', 30 );
