<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

class Ramadan_2026_Endpoints {
    public function __construct() {
        add_action( 'rest_api_init', [ $this, 'add_api_routes' ] );
    }

    private function can_publish(){
        return current_user_can( 'publish_landings' ) || current_user_can( 'manage_dt' );
    }

    public function add_api_routes() {
        $namespace = 'ramadan-2026';
        register_rest_route(
            $namespace, '/install', [
                'methods'  => 'POST',
                'callback' => [ $this, 'dt_ramadan_install_content' ],
                'permission_callback' => function(){
                    return $this->can_publish();
                },
            ]
        );
        register_rest_route(
            $namespace, '/delete', [
                'methods'  => 'POST',
                'callback' => [ $this, 'dt_ramadan_delete_content' ],
                'permission_callback' => function(){
                    return $this->can_publish();
                },
            ]
        );
    }

    public function dt_ramadan_install_content( WP_REST_Request $request ){
        $params = $request->get_params();

        $default_content = $params['default_content'] ? 'en_US' : null;
        $campaign_id = $params['campaign_id'] ?? null;
        if ( empty( $campaign_id ) ){
            return new WP_Error( __METHOD__, 'Missing campaign ID', [ 'status' => 400 ] );
        }

        P4_Ramadan_2026_Content::install_content(
            $params['lang'] ?? 'en_US',
            [
                'in_location' => $params['in_location'] ?? '[in location]',
                'of_location' => $params['of_location'] ?? '[of location]',
                'location' => $params['location'] ?? '[location]',
                'ppl_group'   => $params['ppl_group'] ?? '[people group]',
            ],
            $default_content,
            $campaign_id
        );

        return true;
    }

    public function dt_ramadan_delete_content( WP_REST_Request $request ){
        $params = $request->get_params();
        $campaign_id = $params['campaign_id'] ?? null;
        if ( empty( $campaign_id ) ){
            return new WP_Error( __METHOD__, 'Missing campaign ID', [ 'status' => 400 ] );
        }

        if ( empty( $params['lang'] ) ){
            return new WP_Error( __METHOD__, 'Missing language code', [ 'status' => 400 ] );
        }
        global $wpdb;
        $posts_query = $wpdb->get_results( $wpdb->prepare("
            SELECT ID FROM $wpdb->posts t1
            INNER JOIN $wpdb->postmeta t1m ON ( t1m.post_ID = t1.ID and t1m.meta_key = 'post_language' )
            INNER JOIN $wpdb->postmeta t2m ON ( t2m.post_ID = t1.ID and t2m.meta_key = 'linked_campaign' AND t2m.meta_value = %d )
            WHERE t1m.meta_value = %s
            AND ( t1.post_status = 'publish' OR t1.post_status = 'future' )
            AND t1.post_type = 'landing'
        ", $campaign_id, esc_sql( $params['lang'] ) ), ARRAY_A );

        $post_ids = array_map( function( $post ){
            return esc_sql( $post['ID'] );
        }, $posts_query );
        $post_ids = implode( ',', $post_ids );

        //$post_ids are escaped
        $wpdb->query(  $wpdb->prepare( "
            DELETE pm FROM $wpdb->postmeta pm
            WHERE pm.post_id IN ( %1s )
        ", $post_ids ) );
        $wpdb->query(  $wpdb->prepare( "
            DELETE p FROM $wpdb->posts p
            WHERE p.ID IN ( %1s )
        ", $post_ids ) );

        return true;
    }
};
new Ramadan_2026_Endpoints();


