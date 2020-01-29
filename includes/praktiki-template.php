<?php
/*
 * Template Name: Morning Practices
 * Description: A Page Template with a darker design.
 */

 # Exit if accessed directly
if(!defined("PRK_EXEC")){
	die();
} ?>
<!DOCTYPE html>
<html lang="ru-RU">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>

<?php $posts_args = array(
    "posts_per_page" => 1, 
    "post_status"    => "publish", 
    "post_type"      => "praktiki", 
    "orderby"        => "rand",
);

$content = '<div class="prk-panel">';

$_praktikis = get_posts($posts_args);
foreach($_praktikis as $_praktiki){

    /** get attachment **/
    $attachment_id = get_post_thumbnail_id($_praktiki->ID); //get attachment id 

    /** get thumbnail */
    $image_thumbnail_src = wp_get_attachment_image_src($attachment_id,"full");  
    $image_caption = get_the_post_thumbnail_caption($_praktiki->ID);
        
    //var_dump($_praktiki); // remove comment for display all properties

    if($image_thumbnail_src[0] != ""){ 
        $content .='<div class="praktiki" style="background-image: url('. $image_thumbnail_src[0] .');">';
        $content .='<h4><div>' . $_praktiki->post_title . '</div></h4>';  

        if ( is_plugin_active( 'easy-yandex-share/easy-yandex-share.php' ) ){
            $content .= '<div class="ya-share2" data-services="facebook,vkontakte,odnoklassniki,moimir,viber,whatsapp,telegram" data-counter="" data-url="'. esc_url( get_permalink( get_the_ID() ) ) .'" data-title="'. get_the_title( get_the_ID() ) .'" data-limit="7" data-lang="en"></div>';
        }        

        $content .= '<div class="btn-group btn-group-sm" role="group" aria-label="Кнопки для практик">';
        $content .= '<a href="' . get_permalink(get_the_ID()) . '" class="btn btn-success"  role="button">Попробовать другой вариант</a>';
        $content .= '<a href="/" class="btn btn-success" role="button">Вернуться на главную</a> ';
        $content .= '<a href="' . strip_tags(get_post_meta(get_the_ID(), '_prk_postmeta_cources_page', true)) . '" class="btn btn-success" role="button">Перейти к курсам</a> ';
        $content .= '</div>';

        $content .='</div>'; 
    }   
    $content .= '<div class="credits"><small>Фотограф: ' . $image_caption . '</small></div>';
    
}

$content .= "</div>" ;

echo $content;