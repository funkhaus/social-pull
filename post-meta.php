<?php

/*
 * Add custom metabox to the new/edit page
 */
    function sp2016_add_metaboxes(){

        add_meta_box("sp2016_social_status", "Social Post Type", "sp2016_social_status", "sp-social", "normal", "low");

    }
	add_action('add_meta_boxes', 'sp2016_add_metaboxes');

	// Build media meta box
	function sp2016_social_status() {
		global $post;

        ?>
            <div class="custom-meta social-status">
                <label for="social-status">Select post type:</label>
                <select id="social-status" name="_custom_social_status" id="">
                    <option <?php selected('0', $post->_custom_social_status); ?> default value="0">none</option>
                    <option <?php selected('twitter', $post->_custom_social_status); ?> value="twitter">twitter</option>
                    <option <?php selected('facebook', $post->_custom_social_status); ?> value="facebook">facebook</option>
                    <option <?php selected('instagram', $post->_custom_social_status); ?> value="instagram">instagram</option>
                </select>
                <br/>

            </div>

        	<div class="custom-meta">
				<label for="image-url">Image URL for this post:</label>
				<input id="image-url" class="short" title="" name="_custom_image_url" type="text" value="<?php echo $post->_custom_image_url; ?>">
				<br/>

        	</div>

        	<div class="custom-meta">
				<label for="external-url">External URL for this post:</label>
				<input id="external-url" class="short" title="" name="_custom_external_url" type="text" value="<?php echo $post->_custom_external_url; ?>">
				<br/>

        	</div>

        	<div class="custom-meta">
				<label for="original-url">Original external URL for this post (can't change):</label>
				<input id="original-url" class="short" title="" name="_custom_original_url" type="text" value="<?php echo $post->_custom_original_url; ?>" disabled>
				<br/>

        	</div>

		<?php
	}

/*
 * Save the metabox vaule
 */
    function sp2016_save_metabox($post_id){

        // check autosave
        if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
            return $post_id;
        }

        if( ! empty($_POST["_custom_social_status"]) ) {
	        update_post_meta($post_id, "_custom_social_status", $_POST["_custom_social_status"]);
        }
        if( ! empty($_POST["_custom_image_url"]) ) {
	        update_post_meta($post_id, "_custom_image_url", $_POST["_custom_image_url"]);
        }
        if( ! empty($_POST["_custom_external_url"]) ) {
            update_post_meta($post_id, "_custom_external_url", $_POST["_custom_external_url"]);
        }

    }
    add_action('save_post', 'sp2016_save_metabox');

?>