<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * Class Ramadan_2026_Menu
 */
class Ramadan_2026_Menu {

    public $token = 'ramadan_2026';
    public $page_title = 'Ramadan 2026';

    private static $_instance = null;

    /**
     * Ramadan_2026_Menu Instance
     *
     * Ensures only one instance of Ramadan_2026_Menu is loaded or can be loaded.
     *
     * @since 0.1.0
     * @static
     * @return Ramadan_2026_Menu instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()


    /**
     * Constructor function.
     * @access  public
     * @since   0.1.0
     */
    public function __construct() {

        $this->page_title = 'ramadan-2026';

        add_action( 'dt_prayer_campaigns_admin_install_fuel', [ 'Ramadan_2026_Tab_General', 'content' ] );
    } // End __construct()
}
Ramadan_2026_Menu::instance();

/**
 * Class Ramadan_2026_Tab_General
 */
class Ramadan_2026_Tab_General {
    public static function content() {
        self::main_column();
    }

    public static function main_column() {
        $languages_manager = new DT_Campaign_Languages();
        $campaign = DT_Campaign_Landing_Settings::get_campaign();
        $languages = $languages_manager->get_enabled_languages( $campaign['ID'] );

        $installed_languages = get_available_languages( Ramadan_2026::$plugin_dir .'languages/' );

        global $wpdb;
        $installed_langs_query = $wpdb->get_results( $wpdb->prepare("
            SELECT pm.meta_value, count(*) as count
            FROM $wpdb->posts p
            LEFT JOIN $wpdb->postmeta pm ON ( p.ID = pm.post_id AND meta_key = 'post_language' )
            INNER JOIN $wpdb->postmeta pm2 ON ( p.ID = pm2.post_id AND pm2.meta_key = 'linked_campaign' AND pm2.meta_value = %d )
            WHERE post_type = 'landing' and ( post_status = 'publish' or post_status = 'future')    
            GROUP BY pm.meta_value
        ", $campaign['ID'] ), ARRAY_A );
        $installed_langs = [];
        foreach ( $installed_langs_query as $result ){
            if ( $result['meta_value'] === null ){
                $result['meta_value'] = 'en_US';
            }
            if ( !isset( $installed_langs[$result['meta_value']] ) ){
                $installed_langs[$result['meta_value']] = 0;
            }
            $installed_langs[$result['meta_value']] += $result['count'];
        }


        $prayer_fuel_ready = [ 'en_US', 'ar', 'fr_FR', 'pt_BR', 'es_ES', 'id_ID' ];
        ?>
        <table class="widefat striped">
            <thead>
                <tr>
                    <th>Ramadan 2026 Prayer Fuel</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <p>
                            This is the prayer fuel created for the Ramadan 2026 campaign.
                        </p>
                        <p>
                            Installing prayer fuel will create a post for each day. They will be visible here:
                            <a href="<?php echo esc_html( home_url( 'prayer/list' ) ); ?>" target="_blank">Prayer Fuel List</a>
                        </p>
                        <p>
                            Ramadan prayer fuel is available in multiple languages.
                            <a href="https://prayer.tools/docs/translation/#2-toc-title" target="_blank">Help us translate it into your language here.</a>
                        </p>
                        <table class="">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Install</th>
                                    <th>Install in English</th>
                                    <th>Installed Posts</th>
                                    <th>Delete All</th>
                                </tr>
                            </thead>
                            <tbody>

                            <?php foreach ( $languages as $code => $language ):
                                $fuel_available = $code === 'en_US' || in_array( 'ramadan-2026-' . $code, $installed_languages );
                                ?>

                                <tr>
                                    <td><?php echo esc_html( $language['flag'] ) ?> <?php echo esc_html( $language['english_name'] ) ?></td>
                                    <td>
                                        <button class="button install-2026-ramadan-content" value="<?php echo esc_html( $code ) ?>" <?php disabled( !$fuel_available || !in_array( $code, $prayer_fuel_ready ) ) ?>>
                                            Install prayer fuel in <?php echo esc_html( $language['flag'] ) ?>
                                        </button>
                                    </td>
                                    <td>
                                        <button class="button install-2026-ramadan-content" value="<?php echo esc_html( $code ) ?>" data-default="true" >
                                            Install prayer fuel in English
                                        </button>
                                    </td>
                                    <td><?php echo esc_html( $installed_langs[$code] ?? 0 ); ?></td>
                                    <td>
                                        <button class="button delete-ramadan-content" value="<?php echo esc_html( $code ) ?>" <?php disabled( ( $installed_langs[$code] ?? 0 ) === 0 ) ?>>
                                            Delete all prayer fuel in <?php echo esc_html( $language['flag'] ) ?>
                                        </button>
                                    </td>

                                </tr>

                            <?php endforeach; ?>
                            </tbody>
                        </table>

                    </td>
                </tr>
            </tbody>
        </table>
        <div id="ramadan-2026-dialog" title="Install Prayer Fuel" style="display: none">
            <form id="ramadan-2026-install-form">
                <h3>Install Ramadan Prayer Fuel in <span class="ramadan-new-language">French</span></h3>
                <br>
                <p>
                    This will create a post for day of the month of Ramadan.
                </p>
                <button class="button" type="submit" id="ramadan-install-language">
                    Install Prayer Fuel in <span class="ramadan-new-language">French</span> <img class="ramadan-install-spinner" style="height:15px; vertical-align: middle; display: none" src="<?php echo esc_html( get_template_directory_uri() . '/spinner.svg' ) ?>"/>
                </button>
                <p>
    <!--                Please review the posts here: link @todo-->
                </p>
            </form>
        </div>

        <div id="ramadan-2026-delete-fuel" title="Delete Fuel">
            <p>Are you sure you want to delete Prayer Fuel in <span class="ramadan-new-language">French</span></p>
            <button class="button button-primary" id="confirm-ramadan-delete">Delete
                <img class="ramadan-delete-spinner" style="height:15px; vertical-align: middle; display: none" src="<?php echo esc_html( get_template_directory_uri() . '/spinner.svg' ) ?>"/>
            </button>
            <button class="button" id="ramadan-close-delete">Cancel</button>
        </div>

        <script type="application/javascript">
            let languages_2026 = <?php echo json_encode( $languages ) ?>;

            jQuery(document).ready(function ($){
                let code = null;
                let default_content = false
                $( "#ramadan-2026-dialog" ).dialog({ autoOpen: false, minWidth: 600 }).show()
                $( "#ramadan-2026-delete-fuel" ).dialog({ autoOpen: false });

                $('.install-2026-ramadan-content').on('click', function (){
                    $( "#ramadan-2026-dialog" ).dialog( "open" );
                    code = $(this).val();
                    default_content = $(this).data('default');
                    if ( default_content ){
                        $('.ramadan-new-language').html('English')
                    } else {
                        $('.ramadan-new-language').html(languages_2026[code]?.label || code)
                    }
                })

                $('#ramadan-2026-install-form').on('submit', function (e){
                    e.preventDefault()
                    let in_location = $('#ramadan-in-location-input').val();
                    let of_location = $('#ramadan-of-location-input').val();
                    let location = $('#ramadan-location-input').val();
                    let ppl_group = $('#ramadan-people-group-input').val();

                    $('.ramadan-install-spinner').show()
                    $.ajax({
                        type: 'POST',
                        contentType: 'application/json; charset=utf-8',
                        dataType: 'json',
                        url: "<?php echo esc_url( rest_url() ) ?>ramadan-2026/install",
                        beforeSend: (xhr) => {
                            xhr.setRequestHeader("X-WP-Nonce",'<?php echo esc_attr( wp_create_nonce( 'wp_rest' ) ) ?>');
                        },
                        data: JSON.stringify({
                            campaign_id: <?php echo esc_html( $campaign['ID'] ) ?>,
                            in_location,
                            of_location,
                            location,
                            ppl_group,
                            lang: code,
                            default_content: !!default_content
                        })
                    }).then(()=>{
                        // $('.ramadan-install-spinner').hide()
                        window.location.reload()
                    })
                })

                let delete_code = null
                $('.delete-ramadan-content').on('click', function (){
                    delete_code = $(this).val();

                    $('.ramadan-new-language').html(languages_2026[delete_code]?.label || delete_code)
                    $( "#ramadan-2026-delete-fuel" ).dialog( "open" );
                })
                $('#ramadan-close-delete').on('click', function (){
                    $( "#ramadan-2026-delete-fuel" ).dialog( "close" );
                })
                $('#confirm-ramadan-delete').on('click', function (){
                    $('.ramadan-delete-spinner').show()
                    $.ajax({
                        type: 'POST',
                        contentType: 'application/json; charset=utf-8',
                        dataType: 'json',
                        url: "<?php echo esc_url( rest_url() ) ?>ramadan-2026/delete",
                        beforeSend: (xhr) => {
                            xhr.setRequestHeader("X-WP-Nonce",'<?php echo esc_attr( wp_create_nonce( 'wp_rest' ) ) ?>');
                        },
                        data: JSON.stringify({
                            campaign_id: <?php echo esc_html( $campaign['ID'] ) ?>,
                            lang: delete_code,
                        })
                    }).then(()=>{
                        // $('.ramadan-delete-spinner').hide()
                        window.location.reload()
                    })
                })
            })


        </script>

        <br>
        <!-- End Box -->
        <?php
    }

    public static function right_column() {
        ?>
        <!-- Box -->
        <table class="widefat striped">
            <thead>
                <tr>
                    <th>Information</th>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    Content
                </td>
            </tr>
            </tbody>
        </table>
        <br>
        <!-- End Box -->
        <?php
    }
}

