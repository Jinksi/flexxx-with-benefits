<?php

////////////////////////////////////////////////////////
/// Login Logo
function my_login_logo() { ?>
  <style type="text/css">
    #login h1 a {
      background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/logo.svg);
      margin: 0 auto;
      height: 150px;
      width: 80%;
      background-size: contain;
    }
  </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );
function my_login_logo_url() {
  return home_url();
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
  return get_bloginfo( 'title' );
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );

/////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////// ENQUEUE

//CSS auto version
add_action( 'wp_enqueue_scripts', 'flex_non_cached_stylesheet' );
function flex_non_cached_stylesheet(){
  wp_enqueue_style(
    'style-main',
    get_stylesheet_directory_uri().'/style.css',
    array(),
    filemtime( get_stylesheet_directory().'/style.css' )
  );

  wp_enqueue_script('jquery');

  wp_enqueue_script(
    'mainjs',
    get_template_directory_uri().'/js/main.min.js',
    null,
    null,
		true
  );
}

/////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////// Yoast
// Move metabox to bottom
add_filter( 'wpseo_metabox_prio', function() { return 'low';});

// Removes the Yoast columns from pages & posts
function prefix_remove_yoast_columns( $columns ) {
  unset( $columns['wpseo-score'] );
  unset( $columns['wpseo-title'] );
  unset( $columns['wpseo-metadesc'] );
  unset( $columns['wpseo-focuskw'] );
  return $columns;
}
add_filter ( 'manage_edit-post_columns',    'prefix_remove_yoast_columns' );
add_filter ( 'manage_edit-page_columns',    'prefix_remove_yoast_columns' );
// add_filter ( 'manage_edit-{post_type}_columns',    'prefix_remove_yoast_columns' );

/////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////// WP-LOGIN Autofocus Fix
add_action("login_form", "kill_wp_attempt_focus");
function kill_wp_attempt_focus() {
    global $error;
    $error = TRUE;
}

/////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////Default Functions

/////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////// Title Tag Support
function theme_slug_setup() {
   add_theme_support( 'title-tag' );
}
add_action( 'after_setup_theme', 'theme_slug_setup' );

add_filter( 'gform_enable_field_label_visibility_settings', '__return_true' );
add_filter( 'gform_confirmation_anchor', '__return_true' );

///////////////////////////////////////////////////////
// No pingbacks for security
// http://blog.sucuri.net/2014/03/more-than-162000-wordpress-sites-used-for-distributed-denial-of-service-attack.html
add_filter( 'xmlrpc_methods', function( $methods ) {
  unset( $methods['pingback.ping'] );
  return $methods;
} );

//// Nice url
function niceurl($url) {
	$url = str_replace('http://', '', $url);
	$url = str_replace('https://', '', $url);
	$url = str_replace('www.', '', $url);
	$url = rtrim($url, "/");
    return $url;
}
// Usage
// niceurl('http://google.com.au/');
/// outputs -> google.com.au

///////////////////////////////////////////////////////

// add_image_size( '100x100', 100, 100, true );
add_image_size( '100w', 100, 0, false );
// add_image_size( '200x200', 200, 200, true );
add_image_size( '200w', 200, 0, false );
// add_image_size( '400x400', 400, 400, true );
add_image_size( '400w', 400, 0, false );
// add_image_size( '600x600', 600, 600, true );
add_image_size( '600w', 600, 0, false );
// add_image_size( '800x800', 800, 800, true );
add_image_size( '800w', 800, 0, false );
// add_image_size( '1000x1000', 1000, 1000, true );
// add_image_size( '1000w', 1000, 0, false );
// add_image_size( '1200x1200', 1200, 1200, true );
add_image_size( '1200w', 1200, 0, false );
// add_image_size( '1500x1500', 1500, 1500, true );
// add_image_size( '1500w', 1500, 0, false );
// add_image_size( '1800x1800', 1800, 1800, true );
add_image_size( '1800w', 1800, 0, false );

//////////////////////////////////////////////////////
// IMAGE QUALITY
function gpp_jpeg_quality_callback($arg) {

    return (int)75; // change 100 to whatever you prefer, but don't go below 60

}
add_filter('jpeg_quality', 'gpp_jpeg_quality_callback');

///////////////////////////////////////////////////////
//Add an excerpt box for pages
if ( function_exists('add_post_type_support') ){
  add_action('init', 'add_page_excerpts');
  function add_page_excerpts(){
    add_post_type_support( 'page', 'excerpt' );
  }
}

///////////////////////////////////////////////////////
//Remove admin tool bar
function my_function_admin_bar(){
  return 0;
}
add_filter( 'show_admin_bar' , 'my_function_admin_bar');


///////////////////////////////////////////////////////
//Remove links from images in WYSIWYG
function wpb_imagelink_setup() {
	$image_set = get_option( 'image_default_link_type' );

	if ($image_set !== 'none') {
		update_option('image_default_link_type', 'none');
	}
}
add_action('admin_init', 'wpb_imagelink_setup', 10);

///////////////////////////////////////////////////////
// Check for no robots
add_action( 'admin_notices', 'my_admin_notice' );
function my_admin_notice(){
     if ( !get_option('blog_public') ){
          //echo '<div class="updated" ><p>Warning</p></div>';
          echo '<div class="error"><p>Search engines are blocked</p></div>';
     }
}

///////////////////////////////////////////////////////
// Custom menu
add_action('init', 'register_custom_menu');
function register_custom_menu() {
	register_nav_menu('main', 'Main Menu');
	register_nav_menu('foot', 'Footer Menu');
}

///////////////////////////////////////////////////////
// Admin menu customisation

function custom_menu_page_removing() {
	global $current_user, $submenu, $menu;
	if($current_user->ID === 1) return; // ignore if user ID 1*

  remove_menu_page( 'themes.php' );
  remove_menu_page( 'options-general.php' );
  remove_menu_page( 'customize.php' );
  remove_menu_page( 'theme-editor.php' );
  remove_menu_page( 'tools.php' );
  remove_menu_page( 'plugins.php' );
  remove_menu_page( 'edit.php?post_type=acf-field-group' );
  remove_menu_page( 'edit.php?post_type=page' );
  remove_menu_page( 'edit-comments.php' );
  remove_menu_page( 'index.php' );

	// Move Menus
	if(isset($submenu['themes.php'])) {
		foreach($submenu['themes.php'] as $key => $item) {
			if($item[2] === 'nav-menus.php') {
				unset($submenu['themes.php'][$key]);
			}
		}
	}
	add_menu_page( __('Menus'), __('Menus'), 'delete_others_pages', 'nav-menus.php', '', 'dashicons-list-view', 61 );

	// Add Pages to top of menu
	add_menu_page( __('Pages'), __('Pages'), 'delete_others_pages', 'edit.php?post_type=page', '', 'dashicons-admin-page', 4 );

}
add_action( 'admin_menu', 'custom_menu_page_removing' );

function remove_admin_bar_links() {
  global $wp_admin_bar, $current_user;
	if($current_user->ID === 1) return; // ignore if user ID 1

  // $wp_admin_bar->remove_menu('wp-logo');          // Remove the WordPress logo
  // $wp_admin_bar->remove_menu('about');            // Remove the about WordPress link
  // $wp_admin_bar->remove_menu('wporg');            // Remove the WordPress.org link
  // $wp_admin_bar->remove_menu('documentation');    // Remove the WordPress documentation link
  $wp_admin_bar->remove_menu('support-forums');   // Remove the support forums link
  // $wp_admin_bar->remove_menu('feedback');         // Remove the feedback link
  // $wp_admin_bar->remove_menu('site-name');        // Remove the site name menu
  // $wp_admin_bar->remove_menu('view-site');        // Remove the view site link
  $wp_admin_bar->remove_menu('updates');          // Remove the updates link
  $wp_admin_bar->remove_menu('comments');         // Remove the comments link
  $wp_admin_bar->remove_menu('new-content');      // Remove the content link
  $wp_admin_bar->remove_menu('my-account');       // Remove the user details tab
  $wp_admin_bar->remove_menu('itsec_admin_bar_menu');       // iThemes
}
add_action( 'wp_before_admin_bar_render', 'remove_admin_bar_links' );

/////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////// Woo specific Functions

if ( class_exists( 'WooCommerce' ) ){
  require_once('woo-functions.php');
}

?>
