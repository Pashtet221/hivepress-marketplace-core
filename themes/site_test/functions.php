<?php
if ( ! defined( 'ABSPATH' ) ) exit;
add_action('after_setup_theme',function(){ add_theme_support('woocommerce'); add_theme_support('post-thumbnails'); add_theme_support('title-tag'); });
add_action('wp_enqueue_scripts',function(){
$u=get_template_directory_uri(); $d=get_template_directory(); $v=wp_get_theme()->get('Version');
wp_enqueue_style('theme-style',get_stylesheet_uri(),[],$v);
wp_enqueue_style('layout-css-1','https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',[],null);
wp_enqueue_style('layout-css-2','https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css',[],null);
wp_enqueue_style('layout-css-3',$u.'/static/style.css',[],null);
wp_enqueue_style('layout-css-4','https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap',[],null);
wp_enqueue_style('layout-css-5','https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css',[],null);
wp_enqueue_script('layout-js-1','https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',[],null,true);
});