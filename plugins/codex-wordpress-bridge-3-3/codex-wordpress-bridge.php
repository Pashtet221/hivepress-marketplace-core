<?php
/**
 * Plugin Name: Codex WordPress Bridge
 * Description: Безопасный REST-мост для управления страницами, записями и ACF из Codex или другого доверенного клиента.
 * Version: 0.7.0
 * Author: WP DevStudio
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Text Domain: codex-wordpress-bridge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CWB_VERSION', '0.7.0' );
define( 'CWB_FILE', __FILE__ );
define( 'CWB_DIR', plugin_dir_path( __FILE__ ) );

require_once CWB_DIR . 'includes/class-cwb-plugin.php';

register_activation_hook( __FILE__, array( 'CWB_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'CWB_Plugin', 'deactivate' ) );

CWB_Plugin::instance();
