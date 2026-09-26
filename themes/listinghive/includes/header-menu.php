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

/**
 * Adds the user's favorites and messages to the header navigation.
 *
 * These links are generated at render time because their counters are
 * different for every signed-in user. HivePress prepares both values in the
 * request context before the header is rendered.
 *
 * @param string   $items Menu items HTML.
 * @param stdClass $args  Menu arguments.
 * @return string
 */
function listinghive_add_account_header_menu_items( $items, $args ) {
	if ( ! is_user_logged_in() || empty( $args->theme_location ) || 'header' !== $args->theme_location || ! function_exists( 'hivepress' ) ) {
		return $items;
	}

	$menu_items = [];
	$routes     = [
		[
			'name'  => 'listings_favorite_page',
			'label' => 'Избранное',
			'count' => count( (array) hivepress()->request->get_context( 'favorite_ids', [] ) ),
			'class' => 'favorites',
		],
		[
			'name'  => 'messages_thread_page',
			'label' => 'Сообщения',
			'count' => absint( hivepress()->request->get_context( 'message_unread_count' ) ),
			'class' => 'messages',
		],
	];

	foreach ( $routes as $route ) {
		if ( ! hivepress()->router->get_route( $route['name'] ) ) {
			continue;
		}

		$classes = [
			'menu-item',
			'menu-item--account-' . $route['class'],
		];

		if ( $route['name'] === hivepress()->router->get_current_route_name() ) {
			$classes[] = 'current-menu-item';
		}

		$menu_items[] = sprintf(
			'<li class="%1$s"><a href="%2$s"><span>%3$s</span><small class="header-menu__count" aria-label="%4$s">%5$d</small></a></li>',
			esc_attr( implode( ' ', $classes ) ),
			esc_url( hivepress()->router->get_url( $route['name'] ) ),
			esc_html( $route['label'] ),
			esc_attr( sprintf( '%s: %d', $route['label'], $route['count'] ) ),
			$route['count']
		);
	}

	if ( $menu_items ) {
		$items .= implode( '', $menu_items );
	}

	return $items;
}
add_filter( 'wp_nav_menu_items', 'listinghive_add_account_header_menu_items', 10, 2 );
