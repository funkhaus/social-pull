<?php

	/*
	 * admin Scripts and styles for plugin
	 */
	function funkstagram_admin_style() {
		wp_register_style( 'sp2016_css', sp_pd() . '/css/sp2016.admin.css' );
		wp_register_script( 'sp2016_js', sp_pd() . '/js/sp2016.admin.js' );
		if ( is_admin() ) {
			wp_enqueue_style( 'sp2016_css');
			wp_enqueue_script( 'sp2016_js');
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
                            <th scope="row"><label for="sp2016_twitter_key">Custom Token:</label></th>
                            <td>
                                <input name="sp2016_custom_token" type="text" title="Custom Token" id="sp2016_custom_token" value="<?php echo get_option('sp2016_custom_token'); ?>">
                                <p class="description">Create a custom secure token. Any random string of ASCII characters.</p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label>Webhook URL:</label></th>
                            <td>
                                <?php if ( get_option('sp2016_custom_token') ): ?>
                                    <code><?php echo site_url('/wp-admin/admin-ajax.php?action=import_social_post&token=' . get_option('sp2016_custom_token')); ?></code>
                                <?php else: ?>
                                    <p>You must first set a token.</p>
                                <?php endif; ?>
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
        register_setting('sp2016_settings', 'sp2016_custom_token');
    }
    add_action('admin_init', 'sp2016_settings_init');

    function sp2016_add_settings() {
        add_submenu_page( 'tools.php', 'Social Pull', 'Social Pull', 'manage_options', 'sp2016_settings', 'sp2016_settings_page' );
    }
    add_action('admin_menu','sp2016_add_settings');

?>