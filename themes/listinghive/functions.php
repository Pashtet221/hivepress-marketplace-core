<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Include the theme framework.
require_once __DIR__ . '/vendor/hivepress/hivetheme/hivetheme.php';

// Load the Yandex Maps adapter for HivePress Geolocation.
require_once __DIR__ . '/includes/yandex-maps.php';

// Replace the imported demo navigation with the marketplace navigation.
require_once __DIR__ . '/includes/header-menu.php';
