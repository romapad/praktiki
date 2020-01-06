<?php
/**
 * Widget (Утренние практики)
 *
**/

# Exit if accessed directly
if(!defined("PRK_EXEC")){
	die();
}


 /**
  * Add widget Утренние практики
  * 
  * @package Praktiki
  * @author Romapad
  * @version 1.0
  * @access public
  * 
  * Generate by Plugin Maker ~ http://codecanyon.net/item/wordpress-plugin-maker-freelancer-version/13581496
  */
class Praktiki_Widget extends WP_Widget {

	/**
	 * Option Plugin
	 * @access private
	 **/
	private $options;

	/**
	* Register widget with WordPress.
	*/
	function __construct() {
		parent::__construct(
		"praktiki", // Base ID
		__("Утренние практики","praktiki"), // Name
		array("description" => __("показ кнопки перехода на страницу практик в сайдбаре", "praktiki"),) // Args
		);
		$this->options = get_option("praktiki_plugins"); // get current option
	}

	/**
	* Front-end display of widget.
	*
	* @see WP_Widget::widget()
	*
	* @param array $args     Widget arguments.
	* @param array $instance Saved values from database.
	*/
	public function widget( $args, $instance ){
		//TODO: WIDGET OPTION VARIABLE
		/**
		* @var string $instance["title"] - get widget title
		* @var string $instance["praktki_img"] - get widget option Картинка
		* @var string $instance["praktiki_link"] - get widget option Ссылка на страницу
		**/
		
		echo $args["before_widget"];


		if (!empty($instance["praktiki_link"])){
			if (!empty($instance["praktki_img"])){
				echo '<style>.praktiki-widget {
					display: flex;
					min-height: 150px;
					width: 100%;
					box-sizing: border-box;
					align-items: center;
					flex-direction: column;
					justify-content: center;
					padding: 1em;
					height: 100%;
					text-align: center;
					background: -webkit-linear-gradient(to right, rgba(213, 88, 200, 0.7) 0%, rgba(36, 210, 146, 0.7) 100%), url('. $instance["praktki_img"] .') no-repeat center center; 
					background: linear-gradient(to right, rgba(213, 88, 200, 0.7) 0%, rgba(36, 210, 146, 0.7) 100%), url('. $instance["praktki_img"] .') no-repeat center center;
					background-size: cover;
				}
				.praktiki-widget h3 {
					color: #fff;
					text-transform: uppercase;
				}
				</style>';
				echo '<a href="'. $instance["praktiki_link"] .'" class="praktiki-widget">';
				if (!empty($instance["title"])){
					echo $args["before_title"]. apply_filters("widget_title", $instance["title"] ). $args["after_title"];
				} else {
					echo 'Ежедневные утренние практики';
				}
				echo '</a>';
			} else {
				echo '<a href="'. $instance["praktiki_link"] .'">';
				if (!empty($instance["title"])){
					echo $args["before_title"]. apply_filters("widget_title", $instance["title"] ). $args["after_title"];
				} else {
					echo 'Ежедневные утренние практики';
				}
				echo '</a>';				
			}
		}

		echo $args["after_widget"];
	}

	/**
	* Back-end widget form.
	*
	* @see WP_Widget::form()
	*
	* @param array $instance Previously saved values from database.
	*/
	public function form( $instance ) {
		// Create Title
		$title = ! empty( $instance["title"] ) ? $instance["title"] : __("Утренние практики", "praktiki");
		echo "<p>";
		echo '<label for="'. $this->get_field_id("title" ).'">'. __("Title:") .'</label>';
		echo '<input class="widefat" id="'.  $this->get_field_id("title") .'" name="'. $this->get_field_name("title").'" type="text" value="' . esc_attr( $title ) . '">';
		echo "</p>";
		
		
		/**
		 * CREATE WPMEDIA - PRAKTKI_IMG
		 */
		$praktki_img = ! empty( $instance["praktki_img"] ) ? $instance["praktki_img"] : "";
		wp_enqueue_media();
		/**
		 * Create HTML using wp-color-picker
		 * @see https://codex.wordpress.org/Function_Reference/wp-color-picker
		*/
	
		$praktki_img_preview = "<img />";
		if($praktki_img!=""){
			$praktki_img_preview = '<img src="' . esc_attr( $praktki_img ) . '" style="max-width:100%;"/>';
		}
		echo "<p>";
		echo '<label for="'. $this->get_field_id("praktki_img" ).'">'. __("Картинка", "praktiki") .'</label>';
		echo "</p>";
		echo "<p>";
		echo '<div id="'.  $this->get_field_id("praktki_img_preview") .'">' . $praktki_img_preview. '</div>';
		echo '<input class="widefat praktki_img" id="'.  $this->get_field_id("praktki_img") .'" name="'. $this->get_field_name("praktki_img").'" type="hidden" value="' . esc_attr( $praktki_img ) . '" />';
		echo '<a class="button button-default praktiki_praktki_img_upload" data-input="#'.  $this->get_field_id("praktki_img") .'" data-preview="#'.  $this->get_field_id("praktki_img") .'_preview">'. __("Select Image", "praktiki") .'</a> ';
		echo '<a class="button button-default praktiki_praktki_img_remove" data-input="#'.  $this->get_field_id("praktki_img") .'" data-preview="#'.  $this->get_field_id("praktki_img") .'_preview">'. __("Remove", "praktiki") .'</a> ';
		echo "</p>";
		
		
		/**
		 * CREATE TEXT - PRAKTIKI_LINK
		 */
		$praktiki_link = ! empty( $instance["praktiki_link"] ) ? $instance["praktiki_link"] : "";
		echo "<p>";
		echo '<label for="'. $this->get_field_id("praktiki_link" ).'">'. __("Ссылка на страницу", "praktiki") .'</label>';
		echo '<input class="widefat" id="'.  $this->get_field_id("praktiki_link") .'" name="'. $this->get_field_name("praktiki_link").'" type="text" value="' . esc_attr( $praktiki_link ) . '" />';
		echo "</p>";
	}

	/**
	* Sanitize widget form values as they are saved.
	*
	* @see WP_Widget::update()
	*
	* @param array $new_instance Values just sent to be saved.
	* @param array $old_instance Previously saved values from database.
	*
	* @return array Updated safe values to be saved.
	*/
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance["title"] = ( ! empty( $new_instance["title"] ) ) ? strip_tags( $new_instance["title"] ) : "";
		$instance["praktki_img"] = (!empty($new_instance["praktki_img"] ) ) ? strip_tags($new_instance["praktki_img"]) : "";
		$instance["praktiki_link"] = (!empty($new_instance["praktiki_link"] ) ) ? strip_tags($new_instance["praktiki_link"]) : "";
		
		return $instance;
	}
	
}  
