<?php
$root = dirname( __DIR__ );
$main = file_get_contents( $root . '/includes/class-cwb-plugin.php' );
$hp   = file_get_contents( $root . '/includes/class-cwb-hivepress-api.php' );
$required_routes = array(
	'/taxonomies', '/taxonomies/(?P<taxonomy>', '/hivepress/listing-attributes',
	'/hivepress/listings/schema', '/hivepress/listings', '/hivepress/vendors', '/users', '/media',
);
foreach ( $required_routes as $route ) {
	if ( false === strpos( $hp, $route ) ) {
		fwrite( STDERR, "Missing route: {$route}\n" ); exit( 1 );
	}
}
foreach ( array( 'wp_insert_term(', 'wp_update_term(', 'wp_set_object_terms(', 'wp_insert_post(', 'wp_update_post(' ) as $api ) {
	if ( false === strpos( $hp, $api ) ) { fwrite( STDERR, "Missing WordPress API: {$api}\n" ); exit( 1 ); }
}
if ( preg_match( '/\$wpdb|->query\s*\(|->get_results\s*\(/i', $hp ) ) {
	fwrite( STDERR, "HivePress API must not use direct SQL.\n" ); exit( 1 );
}
if ( false === strpos( $main, "publish_codex_bridge_content" ) || false === strpos( $hp, "cwb_publish_forbidden" ) ) {
	fwrite( STDERR, "Explicit publication capability is missing.\n" ); exit( 1 );
}
foreach ( array( 'password', 'application_password' ) as $secret ) {
	if ( false !== strpos( "'email' =>", $hp ) ) { fwrite( STDERR, "Unsafe user field exposed.\n" ); exit( 1 ); }
}
echo "Bridge contract checks passed.\n";
