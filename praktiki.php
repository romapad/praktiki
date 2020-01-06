<?php

/**

Plugin Name: Praktiki 
Plugin URI: https://romapad.ru/plugins/praktiki/ 
Description: Плагин позволяет отображать рандомно посты с утренними практиками. Для сайта "Письменные практики"
Version: 1.0 
Author: Romapad 
Author URI: https://romapad.ru/ 

Плагин позволяет отображать рандомно посты с утренними практиками. Для сайта "Письменные практики" 

Generate by Plugin Maker ~ http://codecanyon.net/item/wordpress-plugin-maker-freelancer-version/13581496

**/

# Exit if accessed directly
if (!defined("ABSPATH"))
{
	exit;
}

# Constant

/**
 * Exec Mode
 **/
define("PRK_EXEC",true);

/**
 * Plugin Base File
 **/
define("PRK_PATH",dirname(__FILE__));

/**
 * Plugin Base Directory
 **/
define("PRK_DIR",basename(PRK_PATH));

/**
 * Plugin Base URL
 **/
define("PRK_URL",plugins_url("/",__FILE__));

/**
 * Plugin Version
 **/
define("PRK_VERSION","1.0"); 

/**
 * Debug Mode
 **/
define("PRK_DEBUG",false);  //change false for distribution

if(file_exists(PRK_PATH . "/includes/templater.praktiki.inc.php")){
	require_once(PRK_PATH . "/includes/templater.praktiki.inc.php");
}

/**
 * Base Class Plugin
 * @author Romapad
 *
 * @access public
 * @version 1.0
 * @package Praktiki
 *
 **/

class Praktiki
{

	/**
	 * Instance of a class
	 * @access public
	 * @return void
	 **/

	function __construct()
	{
		add_action("plugins_loaded", array($this, "prk_textdomain")); //load language/textdomain
		add_action("wp_enqueue_scripts",array($this,"prk_enqueue_scripts")); //add js
		add_action("wp_enqueue_scripts",array($this,"prk_enqueue_styles")); //add css
		add_action("widgets_init", array($this, "prk_widget_praktiki_init")); //init widget
		add_action("init", array($this, "prk_post_type_praktiki_init")); // register a praktiki post type.
		add_action("after_setup_theme", array($this, "prk_image_size")); // register image size.
		add_filter("image_size_names_choose", array($this, "prk_image_sizes_choose")); // image size choose.
		add_filter('manage_praktiki_posts_columns', 'prk_admin_columns');
		add_filter('manage_praktiki_posts_custom_column', 'prk_admin_custom_columns', 10, 2);
		if(is_admin()){
			add_action("add_meta_boxes",array($this,"prk_metabox_cources_link")); //add metabox Ссылка на курсы
			add_action("save_post",array($this,"prk_metabox_cources_link_save")); //save metabox Ссылка на курсы data
			add_action("admin_enqueue_scripts",array($this,"prk_admin_enqueue_scripts")); //add js for admin
		}

		function prk_admin_columns($columns) {
			$columns = array_slice($columns, 0, 1, true) + array("img" => "Изображение") + array_slice($columns, 1, count($columns) - 1, true);
			  return $columns;
		}
		
		function prk_admin_custom_columns($column_name, $post_id) {
			if( $column_name == 'img' ) {
				  echo get_the_post_thumbnail($post_id, 'thumbnail');
			 }
			 return $column_name;
		}			
	}


	/**
	 * Loads the plugin's translated strings
	 * @link http://codex.wordpress.org/Function_Reference/load_plugin_textdomain
	 * @access public
	 * @return void
	 **/
	public function prk_textdomain()
	{
		load_plugin_textdomain("praktiki", false, PRK_DIR . "/languages");
	}


	/**
	 * Add Metabox (cources_link)
	 * 
	 * @link http://codex.wordpress.org/Function_Reference/add_meta_box
	 * @param mixed $hooks
	 * @access public
	 * @return void
	 **/
	public function prk_metabox_cources_link($hook)
	{
		global $post;
		$pageTemplate = get_post_meta($post->ID, '_wp_page_template', true);
		if($pageTemplate == "praktiki-template.php" )
		//$allowed_hook = array("page"); //limit meta box to certain page
		//if(in_array($hook, $allowed_hook))
		{
			add_meta_box("prk_metabox_cources_link", __("Ссылка на курсы", "praktiki"),array($this,"prk_metabox_cources_link_callback"),$hook,"normal","high");
		}
	}


	/**
	 * Create metabox markup (cources_link)
	 * 
	 * @param mixed $post
	 * @access public
	 * @return void
	 **/
	public function prk_metabox_cources_link_callback($post)
	{
		// Add a nonce field so we can check for it later.
		wp_nonce_field("prk_metabox_cources_link_save","prk_metabox_cources_link_nonce");
		if(file_exists(PRK_PATH . "/includes/metabox.cources_link.inc.php")){
			require_once(PRK_PATH . "/includes/metabox.cources_link.inc.php");
			$cources_link_metabox = new CourcesLink_Metabox();
			$cources_link_metabox->Markup($post);
		}
	}


	/**
	 *
	 * Save the meta when the post is saved.
	 * Praktiki::prk_metabox_cources_link_save()
	 * @param int $post_id The ID of the post being saved.
	 *
	**/
	public function prk_metabox_cources_link_save($post_id)
	{

		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if (!isset($_POST["prk_metabox_cources_link_nonce"]))
			return $post_id;
		$nonce = $_POST["prk_metabox_cources_link_nonce"];

		// Verify that the nonce is valid.
		if(!wp_verify_nonce($nonce, "prk_metabox_cources_link_save"))
			return $post_id;

		// If this is an autosave, our form has not been submitted,
		// so we don't want to do anything.
		if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
			return $post_id;

		// Check the user's permissions.
		if ("page" == $_POST["post_type"])
		{
			if (!current_user_can("edit_page", $post_id))
				return $post_id;
		} else
		{
			if (!current_user_can("edit_post", $post_id))
				return $post_id;
		}

		/* OK, its safe for us to save the data now. */

		// Sanitize the user input.
		//text
		$cources_page = sanitize_text_field($_POST["prk_postmeta_cources_page"] );

		// Update the meta field.
		update_post_meta($post_id, "_prk_postmeta_cources_page", $cources_page);

	}




	/**
	 * Insert javascripts for back-end
	 * 
	 * @link http://codex.wordpress.org/Function_Reference/wp_enqueue_script
	 * @param object $hooks
	 * @access public
	 * @return void
	 **/
	public function prk_admin_enqueue_scripts($hooks)
	{
		if (function_exists("get_current_screen")) {
			$screen = get_current_screen();
		}else{
			$screen = $hooks;
		}
			wp_enqueue_script("prk_admin_widget", PRK_URL . "assets/js/prk_admin_widget.js", array("jquery"),"1.0",true );
	
		// limit page only page
		if(( in_array($hooks,array("page")))||( in_array($screen->post_type,array("page")))){
			wp_enqueue_script("prk_admin_metabox", PRK_URL . "assets/js/prk_admin_metabox.js", array("jquery","thickbox"),"1.0",true );
		}
	}


	/**
	 * Insert javascripts for front-end
	 * 
	 * @link http://codex.wordpress.org/Function_Reference/wp_enqueue_script
	 * @param object $hooks
	 * @access public
	 * @return void
	 **/
	public function prk_enqueue_scripts($hooks)
	{
			wp_enqueue_script("prk_main", PRK_URL . "assets/js/prk_main.js", array("jquery"),"1.0",true );
	}


	/**
	 * Insert CSS for back-end
	 * 
	 * @link http://codex.wordpress.org/Function_Reference/wp_register_style
	 * @link http://codex.wordpress.org/Function_Reference/wp_enqueue_style
	 * @param object $hooks
	 * @access public
	 * @return void
	 **/
	public function prk_admin_enqueue_styles($hooks)
	{
		if (function_exists("get_current_screen")) {
			$screen = get_current_screen();
		}else{
			$screen = $hooks;
		}
		// register css
		wp_register_style("prk_metabox", PRK_URL . "assets/css/prk_admin_metabox.css",array(),"1.0" );
	
		// limit page
		if(( in_array($hooks,array("page")))||( in_array($screen->post_type,array("page")))){
			wp_enqueue_style("prk_metabox");
		}
	}


	/**
	 * Insert CSS for front-end
	 * 
	 * @link http://codex.wordpress.org/Function_Reference/wp_register_style
	 * @link http://codex.wordpress.org/Function_Reference/wp_enqueue_style
	 * @param object $hooks
	 * @access public
	 * @return void
	 **/
	public function prk_enqueue_styles($hooks)
	{
		// register css
		wp_register_style("prk_main", PRK_URL . "assets/css/prk_main.css",array(),"1.0" );
		global $post;
		$pageTemplate = get_post_meta($post->ID, '_wp_page_template', true);
		if($pageTemplate == "praktiki-template.php" )
		//$allowed_hook = array("page"); //limit meta box to certain page
		//if(in_array($hook, $allowed_hook))
		{
			wp_enqueue_style("prk_main");
		}
	}


	/**
	 * Register new widget (praktiki)
	 *
	 * @access public
	 * @return void
	 **/
	public function prk_widget_praktiki_init()
	{
		if(file_exists(PRK_PATH . "/includes/widget.praktiki.inc.php")){
			require_once(PRK_PATH . "/includes/widget.praktiki.inc.php");
			register_widget("Praktiki_Widget");
		}
	} 


	/**
	 * Register custom post types (praktiki)
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 * @access public
	 * @return void
	 **/

	public function prk_post_type_praktiki_init()
	{

		$labels = array(
			'name' => _x('Практики', 'post type general name', 'praktiki'),
			'singular_name' => _x('Практика', 'post type singular name', 'praktiki'),
			'menu_name' => _x('Утренние практики', 'admin menu', 'praktiki'),
			'name_admin_bar' => _x('Практики', 'add new on admin bar', 'praktiki'),
			'add_new' => _x('Добавить новую', 'book', 'praktiki'),
			'add_new_item' => __('Добавить новую практику', 'praktiki'),
			'new_item' => __('Новая практика', 'praktiki'),
			'edit_item' => __('Редактировать', 'praktiki'),
			'view_item' => __('Просмотреть', 'praktiki'),
			'all_items' => __('Все практики', 'praktiki'),
			'search_items' => __('Поиск', 'praktiki'),
			'parent_item_colon' => __('Родительская практика', 'praktiki'),
			'not_found' => __('Не найдено', 'praktiki'),
			'not_found_in_trash' => __('В корзине не найдено', 'praktiki'));

			$supports = array('title','editor','thumbnail');

			$args = array(
				'labels' => $labels,
				'description' => __('', 'praktiki'),
				'public' => true,
				'menu_icon' => 'dashicons-smiley',
				'publicly_queryable' => false,
				'show_ui' => true,
				'show_in_menu' => true,
				'query_var' => true,
				'rewrite' => array('slug' => 'praktiki'),
				'capability_type' => 'post',
				'has_archive' => true,
				'hierarchical' => true,
				'menu_position' => null,
				'show_in_rest' => true,
				'rest_base' => 'praktiki',
				'taxonomies' => array(), // array('category', 'post_tag','page-category'),
				'supports' => $supports);

			register_post_type('praktiki', $args);

	}


	/**
	 * Register a new image size.
	 * @link http://codex.wordpress.org/Function_Reference/add_image_size
	 * @access public
	 * @return void
	 **/
	public function prk_image_size()
	{
		add_image_size("prk_praktiki", 660, 660, true);
	}


	/**
	 * Choose a image size.
	 * @access public
	 * @param mixed $sizes
	 * @return void
	 **/
	public function prk_image_sizes_choose($sizes)
	{
		$custom_sizes = array(
				"prk_praktiki"=>"Картинка для практик",
		);
		return array_merge($sizes,$custom_sizes);
	}

}

new Praktiki();