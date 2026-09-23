<?php
/**
 * Yandex Maps integration for HivePress Geolocation.
 *
 * @package ListingHive
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Adds Yandex Maps to the existing HivePress settings screen.
 *
 * @param array $settings HivePress settings configuration.
 * @return array
 */
function listinghive_add_yandex_maps_settings( $settings ) {
	$provider_field =& $settings['geolocation']['sections']['restrictions']['fields']['geolocation_provider'];

	if ( isset( $provider_field['options'] ) ) {
		$provider_field['options']['yandex'] = esc_html__( 'Yandex Maps', 'listinghive' );
	}

	$settings['integrations']['sections']['yandex_maps'] = [
		'title'  => esc_html__( 'Yandex Maps', 'listinghive' ),
		'_order' => 50,
		'fields' => [
			'yandex_maps_api_key' => [
				'label'       => hivepress()->translator->get_string( 'api_key' ),
				'description' => esc_html__( 'Used by the Yandex Maps JavaScript API for maps, address suggestions and geocoding.', 'listinghive' ),
				'type'        => 'text',
				'max_length'  => 256,
				'_order'      => 10,
			],
		],
	];

	return $settings;
}
add_filter( 'hivepress/v1/settings', 'listinghive_add_yandex_maps_settings', 100 );

/**
 * Replaces only the Geolocation browser adapter when Yandex is selected.
 *
 * HivePress Geolocation has provider-specific branches rather than a provider
 * registry. The public scripts configuration filter is therefore the safest
 * extension point available without changing the extension itself.
 *
 * @param array $scripts HivePress scripts configuration.
 * @return array
 */
function listinghive_add_yandex_maps_script( $scripts ) {
	if ( 'yandex' !== get_option( 'hp_geolocation_provider' ) || empty( $scripts['geolocation'] ) ) {
		return $scripts;
	}

	$script_path                       = get_template_directory() . '/assets/js/yandex-maps.js';
	$scripts['geolocation']['src']     = get_template_directory_uri() . '/assets/js/yandex-maps.js';
	$scripts['geolocation']['version'] = file_exists( $script_path ) ? filemtime( $script_path ) : wp_get_theme()->get( 'Version' );
	$scripts['geolocation']['deps']    = [ 'hivepress-core', 'jquery-ui-autocomplete' ];
	$scripts['geolocation']['data']    = [
		'apiKey'   => sanitize_text_field( get_option( 'hp_yandex_maps_api_key', '' ) ),
		'apiURL'   => 'https://api-maps.yandex.ru/2.1/',
		'language' => listinghive_get_yandex_maps_language(),
	];

	// These Google-only adapters otherwise pull in the Google Maps handle.
	unset( $scripts['geocomplete'], $scripts['markerclustererplus'], $scripts['markerspiderfier'] );

	return $scripts;
}
add_filter( 'hivepress/v1/scripts', 'listinghive_add_yandex_maps_script', 100 );

/**
 * Prevents the extension's default Google API request when Yandex is active.
 */
function listinghive_disable_google_maps_script() {
	if ( 'yandex' !== get_option( 'hp_geolocation_provider' ) ) {
		return;
	}

	wp_dequeue_script( 'google-maps' );
	wp_deregister_script( 'google-maps' );
}
add_action( 'wp_enqueue_scripts', 'listinghive_disable_google_maps_script', 2 );
add_action( 'admin_enqueue_scripts', 'listinghive_disable_google_maps_script', 2 );

/**
 * Maps the WordPress locale to a locale accepted by Yandex Maps API 2.1.
 *
 * @return string
 */
function listinghive_get_yandex_maps_language() {
	$locale    = str_replace( '-', '_', determine_locale() );
	$languages = [
		'ru_RU',
		'en_US',
		'en_RU',
		'uk_UA',
		'tr_TR',
	];

	if ( in_array( $locale, $languages, true ) ) {
		return $locale;
	}

	return 0 === strpos( $locale, 'ru_' ) ? 'ru_RU' : 'en_US';
}

/**
 * Region generation is hard-wired to Google/Mapbox server responses in the
 * installed Geolocation extension. Disable that optional feature for Yandex
 * so saving a listing never sends an invalid Google request.
 *
 * @param mixed $default_value Value passed to the pre-option filter.
 * @return mixed
 */
function listinghive_disable_yandex_region_generation( $default_value ) {
	if ( 'yandex' === get_option( 'hp_geolocation_provider' ) ) {
		return false;
	}

	return $default_value;
}
add_filter( 'pre_option_hp_geolocation_generate_regions', 'listinghive_disable_yandex_region_generation' );
