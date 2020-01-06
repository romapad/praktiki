<?php

/**
 * Metabox (Ссылка на курсы)
 *
**/

# Exit if accessed directly
if(!defined("PRK_EXEC")){
	die();
}


 /**
  * Dispaly back-end metabox cources_link
  * 
  * @package Praktiki
  * @author Romapad
  * @version 1.0
  * @access public
  * 
  * Generate by Plugin Maker ~ http://codecanyon.net/item/wordpress-plugin-maker-freelancer-version/13581496
  */

class CourcesLink_Metabox{


	/**
	 * Option Plugin
	 * @access private
	 **/
	private $options;

	/**
	 * Instance of a class
	 * 
	 * @access public
	 * @return void
	 **/

	function __construct(){
		$this->options = get_option("praktiki_plugins"); // get current option

	}

	/**
	 * Create Metabox Markup
	 * 
	 * @param mixed $post
	 * @access public
	 * @return void
	 **/

	public function Markup($post){

		// TODO: EDIT HTML METABOX Ссылка на курсы
		if(PRK_DEBUG==true){
			$file_info = null; 
			$file_info .= "<p>You can edit the file below to fix the metabox</p>" ; 
			$file_info .= "<div>" ; 
			$file_info .= "<pre style=\"color:rgba(255,0,0,1);padding:3px;margin:0px;background:rgba(255,0,0,0.1);border:1px solid rgba(255,0,0,0.5);font-size:11px;font-family:monospace;white-space:pre-wrap;\">%s:%s</pre>" ; 
			$file_info .= "</div>" ; 
			printf($file_info,__FILE__,__LINE__);
		}
		/**
		* You can make HTML tag for Metabox Ссылка на курсы here
		**/

		echo "<h4>Письменные практики</h4>";
		printf("<table class=\"form-table\">");


		// Use get_post_meta to retrieve an existing value from the database.
		$current_prk_postmeta_cources_page= get_post_meta($post->ID, "_prk_postmeta_cources_page", true);

		/** Display the form cources_page, using the current value. **/
		printf("<tr><th scope=\"row\"><label for=\"prk_postmeta_cources_page\">%s</label></th><td><input class=\"prk-form-control\" type=\"text\" id=\"prk_postmeta_cources_page\" name=\"prk_postmeta_cources_page\" value=\"%s\" placeholder=\"/courses/\" /></td></tr>",__("Ссылка на страницу курсов", "praktiki"), esc_attr($current_prk_postmeta_cources_page));
		echo "</table>";
	}
}
