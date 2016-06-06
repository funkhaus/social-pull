<?php

	/*
	 * admin Scripts and styles for plugin
	 */
	function funkstagram_admin_style() {
		wp_register_style( 'funk_css', sp_pd() . '/css/funkstagram.admin.css' );
		wp_register_script( 'tagit_js', sp_pd() . '/js/jquery.tagsinput.min.js' );
		wp_register_script( 'funk_js', sp_pd() . '/js/funkstagram.admin.js' );
		if ( is_admin() ) {
			wp_enqueue_style( 'funk_css');
			wp_enqueue_script( 'tagit_js');
			wp_enqueue_script( 'funk_js');
		}
	}
	// add_action( 'admin_init', 'funkstagram_admin_style' );

    /* Call Settings Page */
    function sp2016_settings_page() { ?>

        <div class="wrap">
            <h2>Social Pull Options</h2>
            <form action="options.php" method="post" id="sp2016_settings">
                <?php settings_fields('sp2016_settings'); ?>
                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row"><label for="sp2016_twitter_key">Twitter Key:</label></th>
                            <td>
                                <input name="sp2016_twitter_key" type="text" title="Twitter Key" id="sp2016_twitter_key" value="<?php echo get_option('sp2016_twitter_key'); ?>">
                                <!-- <p class="description"></p> -->
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label for="sp2016_twitter_secret">Twitter Secret:</label></th>
                            <td>
                                <input name="sp2016_twitter_secret" type="text" title="Twitter Secret" id="sp2016_twitter_secret" value="<?php echo get_option('sp2016_twitter_secret'); ?>">
                                <!-- <p class="description"></p> -->
                            </td>
                        </tr>
                    </tbody>
                </table>
				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
				</p>
            </form>
        </div><!-- END Wrap -->

        <?php
    }

    /* Save Takeover Settings */
    function sp2016_settings_init(){
        register_setting('sp2016_settings', 'sp2016_twitter_key');
        register_setting('sp2016_settings', 'sp2016_twitter_secret');
    }
    add_action('admin_init', 'sp2016_settings_init');

    function sp2016_add_settings() {
        add_submenu_page( 'tools.php', 'Social Pull', 'Social Pull', 'manage_options', 'sp2016_settings', 'sp2016_settings_page' );
    }
    add_action('admin_menu','sp2016_add_settings');

?>