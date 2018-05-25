<?php

    add_action( 'wp_ajax_nopriv_trim_image', 'trim_image_by_url' );
    add_action( 'wp_ajax_trim_image', 'trim_image_by_url' );
    function trim_image_by_url($img_url){

        // check for token
        if ( ! isset($_REQUEST['url']) ){
            echo json_encode(array( 'error' => 'Invalid URL' ));
            exit;
        }

        $url = urldecode($_REQUEST['url']);
        $im = new Imagick($url);

        // add white border (this ensures trim will only trim white)
        $im->borderImage('white', 15, 15);

        /* Trim the image. */
        $im->trimImage(10000);

        /* Ouput the image */
        header("Content-Type: image/" . $im->getImageFormat());
        echo $im;
        exit;

    }

/*
 * The following code has been taken from
 * the media_sideload_image() reference page
 * https://codex.wordpress.org/Function_Reference/media_sideload_image
 *
 * @Returns image ID on success, or false on failure
 *
 */
    function sp_sideLoad($url, $post_id, $name = '') {
        if ( ! function_exists('download_url') ) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
        }

        // get name from file
        if ( empty($name) ){
            $name = basename( $url );
        }

        // sideload image, return ID
        $id = media_sideload_image($url, $post_id, '', 'id');

        // If error storing permanently, delete and return false
        if ( is_wp_error($id) ) {
            return false;
        }

        return $id;
	}

/*
 * @Description: Ajax endpoint to receive a new social post
 */
    add_action( 'wp_ajax_nopriv_import_social_post', 'import_social_post' );
    add_action( 'wp_ajax_import_social_post', 'import_social_post' );
    function import_social_post(){

        // set header
        header('Content-Type: application/json');

        // check for token
        if ( ! isset($_REQUEST['token']) || !get_option('sp2016_custom_token') || $_REQUEST['token'] !== get_option('sp2016_custom_token') ){
            echo json_encode(array( 'error' => 'Invalid authorization' ));
            exit;
        }

        // get JSON body
        $data_source = json_decode(file_get_contents('php://input'), true);

        $is_instagram_poller = isset($data_source['source']) && $data_source['source'] == 'instagram_poller';
        $is_instagram_native = isset($data_source['user']['username']) && isset($data_source['images']['standard_resolution']['url']);

        // detect data type
        $type = false;
        if ( isset($data_source['retweet_count']) ) $type = 'twitter';
        if ( $is_instagram_native || $is_instagram_poller ) $type = 'instagram';
        if ( isset($data_source['status_type']) ) $type = 'facebook';

        // any data in the request?
        if ( empty($data_source) ){
            echo json_encode(array( 'error' => 'No Data', 'print' => print_r($data_source, true) ));
            exit;
        }

        // failed type test, email me
        if ( !$type ){
            wp_mail( 'john@funkhaus.us', 'PB: Type test failed', print_r(json_encode($data_source), true));
        }

        // setup meta
        $meta_args = array(
            '_custom_social_status' => ''
        );

        // setup args to create post
        $args = array(
            'post_author'       => '',
            'post_content'      => '',
            'post_title'        => '',
            'post_status'       => 'pending',
            'post_type'         => 'sp-social'
        );

        $success = false;

        // check type
        switch ( $type ){

            // is twitter type
            case 'twitter' :

                // validate params
                if ( !isset($data_source['user__screen_name']) || !isset($data_source['user__profile_image_url']) || !isset($data_source['text']) ){
                    echo json_encode(array( 'error' => 'Must set user, image, and text' ));
                    exit;
                }

                $user = strtolower( $data_source['user__screen_name'] );

                // find user with
                $target_user = get_users(array('meta_key' => '_sp2016_twitter_handle', 'meta_value' => $user));
                $target_user = reset($target_user);

                // no user? Default to null
                $target_user_id = $target_user ? $target_user->ID : null;

                // set social media status and custom image
                $meta_args['_custom_social_status'] = $type;
                $meta_args['_custom_image_url'] = $data_source['user__profile_image_url'];
                $meta_args['_custom_external_url'] = $data_source['url'];

                // set args for post
                $args['post_author'] = $target_user_id;
                $args['post_content'] = $data_source['text'];
                $args['post_title'] = 'Tweet: ' . substr($data_source['text'], 0, 35) . '...';
                $args['post_category'] = array($target_user->_sp2016_cat_id);
                $args['meta_input'] = $meta_args;

                // create post
                $success = wp_insert_post($args);

                break;

            // is facebook type
            case 'facebook' :

                // validate params
                if ( !isset($data_source['from__id']) || !isset($data_source['picture']) || !isset($data_source['message']) ){
                    echo json_encode(array( 'error' => 'Must set user, image, and text' ));
                    exit;
                }

                // $mailer = json_encode($_POST, JSON_PRETTY_PRINT);
                // wp_mail( 'john@funkhaus.us', 'Raw FB JSON', $mailer );

                // find user with
                $target_user = get_users(array('meta_key' => '_sp2016_facebook_id', 'meta_value' => $data_source['from__id']));
                $target_user = reset($target_user);

                // no user? Default to null
                $target_user_id = $target_user ? $target_user->ID : null;

                // set social media status and custom image
                $meta_args['_custom_social_status'] = $type;
                $meta_args['_custom_image_url'] = $data_source['picture'];
                $meta_args['_custom_external_url'] = $data_source['link'];

                // set args for post
                $args['post_author'] = $target_user_id;
                $args['post_content'] = $data_source['message'];
                $args['post_title'] = substr($data_source['message'], 0, 45) . '...';
                $args['post_category'] = array($target_user->_sp2016_cat_id);
                $args['meta_input'] = $meta_args;

                // create post
                $success = wp_insert_post($args);

                // if created...
                if ( $success ){

                    // get created post
                    $created_post = get_post($success);

                    $full_image = 'http://graph.facebook.com/' . $data_source['object_id'] .'/picture';

                    // sideload image into media library
                    $image_id = sp_sideLoad($full_image, $success, $data_source['object_id']);

                    // if image created successfully...
                    if ( $image_id ){

                        // get attachment object
                        $created_attachment = get_post($image_id);

                        // set loaded image as thumbnail
                        set_post_thumbnail($created_post, $image_id);

                    }

                }

                break;

            // is instagram type
            case 'instagram' :

                // validate params
                if ( !isset($data_source['user']['username']) || !isset($data_source['images']['standard_resolution']['url']) ){
                    echo json_encode(array( 'error' => 'Must set user and image' ));
                    exit;
                }

                // set user
                $user = strtolower( $data_source['user']['username'] );

                // validate type
                if ( !isset($data_source['type']) || $data_source['type'] !== 'image' ){
                    echo json_encode(array( 'error' => 'Must be an image to import' ));
                    exit;
                }

                // find user with
                $target_user = get_users(array('meta_key' => '_sp2016_instagram_handle', 'meta_value' => $user));
                $target_user = reset($target_user);

                // no user? Default to null
                $target_user_id = $target_user ? $target_user->ID : null;

                // set social media status and custom image
                $meta_args['_custom_social_status'] = $type;
                $meta_args['_custom_image_url'] = $data_source['images']['standard_resolution']['url'];
                $meta_args['_custom_external_url'] = $data_source['link'];

                // set args for post
                $args['post_author'] = $target_user_id;
                $args['post_content'] = $data_source['caption']['text'];
                $args['post_title'] = 'Gram: ' . $data_source['id'];
                $args['post_category'] = array($target_user->_sp2016_cat_id);
                $args['meta_input'] = $meta_args;

                // if caption wasn't found, try a different key
                if ( empty($args['post_content']) && isset($data_source['caption']) ) $args['post_content'] = $data_source['caption']['text'];

                // create post
                $success = wp_insert_post($args);

                // if created...
                if ( $success ){

                    // get created post
                    $created_post = get_post($success);

                    // sideload image into media library
                    $image_id = sp_sideLoad($data_source['images']['standard_resolution']['url'], $success, $data_source['id']);

                    // if image created successfully...
                    if ( $image_id ){

                        // get attachment object
                        $created_attachment = get_post($image_id);

                        // set loaded image as thumbnail
                        set_post_thumbnail($created_post, $image_id);

                    }

                }

                break;

        }

        // wasn't successful?
        if ( !$success ){
            echo json_encode(array( 'error' => 'Something went wrong' ));
            exit;
        }

        update_post_meta($success, 'sp2016_insta_alldata', $data_source);

        // output success message
        echo json_encode(array( 'message' => 'success!', 'createdPost' => $success ));
        exit;
    }
