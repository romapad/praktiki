/**
 * @Author :Romapad
 * @Author URL : https://romapad.ru/
 * @License : GNU GPL v2
 * @License URL: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @Package : Praktiki
 * @Version : 1.0
**/


(function($)
{

	/**
	 * PRAKTKI_IMG
	 **/
	
	// wp media
	$(function(){
		$("body").on("click",".praktiki_praktki_img_upload",function(event){
			event.preventDefault();
			var media_praktki_img_upload ;
			var media_praktki_img_input = $(this).attr("data-input");
			var media_praktki_img_preview = $(this).attr("data-preview");
			if ( media_praktki_img_upload ) {
				media_praktki_img_upload.open();
				return;
			}
			// Create a new media frame
			media_praktki_img_upload = wp.media({
				title: "Select or Upload Media",
				button: {
					text: "Use this media"
				},
				multiple: false  // Set to true to allow multiple files to be selected
			});
			media_praktki_img_upload.on("select", function() {
				var attachment = media_praktki_img_upload.state().get("selection").first().toJSON();
				$(media_praktki_img_input).val(attachment.url);
				$(media_praktki_img_input).trigger("change");
				$(media_praktki_img_preview).find("img").replaceWith("<img src=\"" + attachment.url + "\" style=\"max-width:100%;\"/>");
			});
			media_praktki_img_upload.open();
		});
		$("body").on("click",".praktiki_praktki_img_remove",function(event){
			event.preventDefault();
			var media_praktki_img_input = $(this).attr("data-input");
			var media_praktki_img_preview = $(this).attr("data-preview");
				$(media_praktki_img_input).val("");
				$(media_praktki_img_input).trigger("change");
				$(media_praktki_img_preview).find("img").replaceWith("<img style=\"max-width:100%;\"/>");
		});
		// trick load again after save
		$(document).ajaxComplete(function(){
		});
	});
	

	/**
	 * PRAKTIKI_LINK
	 **/
})(jQuery);
