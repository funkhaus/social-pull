<?php

/*
 * Add custom metabox to the user profile page in WordPress
 */
	add_action( 'show_user_profile', 'sp2016_user_meta' );
	add_action( 'edit_user_profile', 'sp2016_user_meta' );
    function sp2016_user_meta( $user ) {

        $args = array(
        	'show_option_none'   => 'none',
        	'orderby'            => 'ID',
        	'order'              => 'ASC',
        	'hide_empty'         => 0,
        	'selected'           => $user->_sp2016_cat_id,
        	'name'               => '_sp2016_cat_id',
        	'id'                 => 'user-category',
        	'class'              => 'postform',
        	'taxonomy'           => 'category'
        );

        ?>

        <h3>User Social Media</h3>

        <table class="form-table">

            <tr>
                <th><label for="twitter-data">Twitter Handle</label></th>
                <td>
                    <input type="text" name="_sp2016_twitter_handle" id="twitter-handle" value="<?php echo $user->_sp2016_twitter_handle; ?>" class="regular-text">
                </td>
            </tr>

            <tr>
                <th><label for="instagram-data">Instagram Handle</label></th>
                <td>
                    <input type="text" name="_sp2016_instagram_handle" id="instagram-handle" value="<?php echo $user->_sp2016_instagram_handle; ?>" class="regular-text">
                </td>
            </tr>

            <tr>
                <th><label for="facebook-data">Facebook ID</label></th>
                <td>
                    <input type="text" name="_sp2016_facebook_id" id="facebook-handle" value="<?php echo $user->_sp2016_facebook_id; ?>" class="regular-text">
                </td>
            </tr>

            <tr>
                <th><label for="user-category">User Category</label></th>
                <td>
                    <?php wp_dropdown_categories($args); ?>
                </td>
            </tr>

        </table>

    <?php }

/*
 * Save the metabox vaules
 */
    add_action( 'personal_options_update', 'sp2016_save_user_meta' );
    add_action( 'edit_user_profile_update', 'sp2016_save_user_meta' );
    function sp2016_save_user_meta( $user_id ) {
        // Abort if user not allowed to edit
        if ( ! current_user_can( 'edit_user', $user_id ) ) {
            return false;
        }

        // Update active cart ID
        if( isset($_POST['_sp2016_twitter_handle']) ) {
            update_user_meta( $user_id, '_sp2016_twitter_handle', strtolower($_POST['_sp2016_twitter_handle']));
        }
        if( isset($_POST['_sp2016_instagram_handle']) ) {
            update_user_meta( $user_id, '_sp2016_instagram_handle', strtolower($_POST['_sp2016_instagram_handle']));
        }
        if( isset($_POST['_sp2016_facebook_id']) ) {
            update_user_meta( $user_id, '_sp2016_facebook_id', $_POST['_sp2016_facebook_id']);
        }
        if( isset($_POST['_sp2016_cat_id']) ) {
            update_user_meta( $user_id, '_sp2016_cat_id', $_POST['_sp2016_cat_id']);
        }
    }

?>