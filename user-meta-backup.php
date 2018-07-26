<?php

// load twitter API
require 'twitteroauth/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

/*
 * Add custom metabox to the user profile page in WordPress
 */
	add_action( 'show_user_profile', 'sp2016_user_meta' );
	add_action( 'edit_user_profile', 'sp2016_user_meta' );
    function sp2016_user_meta( $user ) { 

        // start twitter connection
        $tw_connection = new TwitterOAuth(get_option('sp2016_twitter_key'), get_option('sp2016_twitter_secret'));

        // get request token
        $tw_request_token = $tw_connection->oauth('oauth/request_token');

        // if oauth tokens have not been set
        if ( !isset( $_SESSION['oauth_token'] ) && !isset( $_SESSION['oauth_token_secret'] ) ){
    
            $_SESSION['oauth_token'] = $tw_request_token['oauth_token'];
            $_SESSION['oauth_token_secret'] = $tw_request_token['oauth_token_secret'];
        }

        // generate twitter oAuth URL
        $tw_url = $tw_connection->url('oauth/authorize', array('oauth_token' => $tw_request_token['oauth_token']));

        // get user's twitter data
        $twitter_data = get_user_meta($user->ID, '_sp2016_twitter_data', true);

        // if disconnecting
        if ( isset($_REQUEST['disconnect_twitter']) && $_REQUEST['disconnect_twitter'] ){
            delete_user_meta($user->ID, '_sp2016_twitter_data');
            $twitter_data = false;
        }

        // if incoming oAuth token
        if ( isset($_REQUEST['oauth_token']) && isset($_REQUEST['oauth_verifier']) && isset($_SESSION['oauth_token_secret']) ){

            $tw_connection = new TwitterOAuth(get_option('sp2016_twitter_key'), get_option('sp2016_twitter_secret'), $_REQUEST['oauth_token'], $_SESSION['oauth_token_secret']);
            $twitter_data = $tw_connection->oauth("oauth/access_token", ["oauth_verifier" => $_REQUEST['oauth_verifier']]);

            // update user twitter data
            update_user_meta( $user->ID, '_sp2016_twitter_data', $twitter_data);
        }

        ?>

        <h3>User Social Media</h3>

		<table class="form-table">

			<tr>
				<th><label for="twitter-data">Twitter Profile</label></th>
                <td>
    				<?php if ( empty($twitter_data) ): ?>
                        <a href="<?php echo $tw_url; ?>" class="button button-secondary">Connect Twitter</a>
                    <?php else : ?>
                        <?php 
                            $tw_connection = new TwitterOAuth(get_option('sp2016_twitter_key'), get_option('sp2016_twitter_secret'), $twitter_data['oauth_token'], $twitter_data['oauth_token_secret']); 
                            $tw_account = $tw_connection->get('users/show', ['user_id' => $twitter_data['user_id']]);
                        ?>
                        <img src="<?php echo $tw_account->profile_image_url; ?>" alt="" />
                        <a href="<?php echo get_permalink() . '?disconnect_twitter=1'; ?>" class="button button-secondary">Disconnect Twitter</a>
    				<?php endif; ?>
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

		//Update active cart ID
		if( isset($_POST['_sp2016_twitter_data']) ) {
			update_user_meta( $user_id, '_sp2016_twitter_data', $_POST['_sp2016_twitter_data']);
		}
	}

?>