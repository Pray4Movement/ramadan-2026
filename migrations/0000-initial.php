<?php
declare(strict_types=1);
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * Legacy migration placeholder.
 */

require_once( 'abstract.php' );

/**
 * Class DT_R2026_Migration_0000
 */
class DT_R2026_Migration_0000 extends DT_R2026_Migration {

    public static function switch_locale( $lang_code = null ){
        add_filter( 'determine_locale', function ( $locale ) use ( $lang_code ){
            if ( !empty( $lang_code ) ){
                return $lang_code;
            }
            return $lang_code;
        } );
        unload_textdomain( 'ramadan-2026' );
        load_textdomain( 'ramadan-2026', Ramadan_2026::get_dir_path() . 'languages/ramadan-2026-' . $lang_code . '.mo' );
    }
    /**
     * @throws \Exception  Got error when creating table $name.
     */
    public function up() {
        global $wpdb;
        //get campaigns with a day 27 prayer fuel
        $day_27_fuel_posts = $wpdb->get_results( "
            SELECT p.ID, p.post_content, pm2.meta_value as post_language
            FROM $wpdb->posts p
            JOIN $wpdb->postmeta pm ON ( pm.post_id = p.ID AND pm.meta_key = 'day' AND pm.meta_value = '27' ) 
            LEFT JOIN $wpdb->postmeta pm2 ON ( pm2.post_id = p.ID AND pm2.meta_key = 'post_language' )
            AND p.post_type = 'landing'
        ", ARRAY_A );

        foreach ( $day_27_fuel_posts as $post ) {
            $post_lang = $post['post_language'];
            self::switch_locale( $post_lang ?? 'en_US' );
            $post_content = $post['post_content'];
            $post_id = $post['ID'];

            $new_day_27 = [
                __( '“So whatever you wish that others would do to you, do also to them, for this is the Law and the Prophets. Enter by the narrow gate. For the gate is wide and the way is easy that leads to destruction, and those who enter by it are many. For the gate is narrow and the way is hard that leads to life, and those who find it are few." (Matthew 7:12-14) ESV', 'ramadan-2026' ),
                __( 'For those praying today who live in this area, take a moment to imagine what it would look like if everyone would do to others what they would have done unto them. Pray for those Kingdom realities to come.', 'ramadan-2026' ) . "\n\n" . __( 'For those praying today who do not live in this area, pray for Christians to bring meals to the sick, give rides to the carless, listen well to those with burdens, visit those in the hospital, and to share with those in need. May the name of Jesus be exalted in these actions.', 'ramadan-2026' ),
                __( 'We pray today for believers who have chosen the hard, narrow way that leads to life. When they are tempted to be jealous of those who get to "do as they like" on the easy, wide way, may they stop and remember the cross. Thank you, Jesus, for coming to reveal God and the way of the cross that loves and sacrifices. We pray that believers will deliberately choose to honor others, to forgive, to offer hospitality, and to serve with humility, giving their lives for Jesus\' sake, calling others to the narrow way.', 'ramadan-2026' ),
                __( 'Adil sat in the back of an old church building in the middle of the week, full of questions about Christianity.  It was forbidden for him to come when foreign Christians gathered there for worship, but he came when a small group practiced for choir. Tears streamed down his face, and he said apologetically, "I feel such peace here. I can\'t stop crying." Then he asked, "Is it okay for me to choose Jesus and still continue [in this sin that I love]?" A brother told him that we all come as we are to God, and he is the one who cleanses us, but he tells us to "Repent, for the kingdom of God is near."  Adil wanted life but was unwilling to turn away from the wide way of destruction to the narrow, hard, holy way. Lord, we pray for those who want you but want their old life, too. Please, stir their souls to repent and live.', 'ramadan-2026' ),
                __( 'Pray that God would give grace to the church in this region to live out Jesus\' Golden Rule, “Do to Others”,  teaching with one another. Pray for simple house churches to model this kind of love to each other and those around them. Pray for believers to proactively serve, visit the sick, listen to the wounded, grieve with the hurting, and show compassion to the weak...instead of waiting to be served, visited, or listened to.
As they do, lead them to people who they could help form new house churches and Bible studies that would replicate such a way of life for God\'s glory.', 'ramadan-2026' ),
                __( 'Places of Community

Lead Christians in this region to find ways to serve their community and gain creative access to Muslim families around them. Pray that as Christians love Muslims the way they want to be loved, that Muslim families would want to know more about how they are fulfilling \'the Law and the Prophets\'.
Pray for Christians to stand out in their community for their unselfish love. Pray they would find people of peace who would gather family or friends to read the Bible together and learn more about the source of this unselfish love.
Pray for community leaders to be softened to the Gospel and desire their community to be shaped by Jesus\' teachings of love for enemies, serving others, and selflessness.', 'ramadan-2026' ),
                __( 'Jesus, you are the way, the truth, and the life. No one comes to the Father except through you (John 14:6). Tonight, as Muslims break their fast, go to the mosque, and do their prayers, we pray for dissatisfaction to grow in the sufficiency of their righteous acts. Whether through a dream or vision or talking with a Christian, give many Muslims in this area the chance to hear that "I am the door. If anyone enters by me, he will be saved and will go in and out and find pasture." (John 10:9) As they learn more about you, guide them to engage with friends and family so that they can learn about you together.', 'ramadan-2026' ),
            ];

            $old_day_27 = [
                __( '“So whatever you wish that others would do to you, do also to them, for this is the Law and the Prophets. Enter by the narrow gate. For the gate is wide and the way is easy that leads to destruction, and those who enter by it are many. For the gate is narrow and the way is hard that leads to life, and those who find it are few." (Matthew 7:12-14) ESV', 'ramadan-2026' ),
                __( 'For those praying today who live in this area, take a moment to imagine what it would look like if everyone would do to others what they would have done unto them. Pray for those Kingdom realities to come.', 'ramadan-2026' ),
                __( 'For those praying today who do not live in this area, pray for Christians to bring meals to the sick, give rides to the carless, listen well to those with burdens, visit those in the hospital, and to share with those in need. May the name of Jesus be exalted in these actions.', 'ramadan-2026' ),
                __( 'We pray today for believers who have chosen the hard, narrow way that leads to life. When they are tempted to be jealous of those who get to "do as they like" on the easy, wide way, may they stop and remember the cross. Thank you, Jesus, for coming to reveal God and the way of the cross that loves and sacrifices. We pray that believers will deliberately choose to honor others, to forgive, to offer hospitality, and to serve with humility, giving their lives for Jesus\' sake, calling others to the narrow way.', 'ramadan-2026' ),
                __( 'Adil sat in the back of an old church building in the middle of the week, full of questions about Christianity.  It was forbidden for him to come when foreign Christians gathered there for worship, but he came when a small group practiced for choir. Tears streamed down his face, and he said apologetically, "I feel such peace here. I can\'t stop crying." Then he asked, "Is it okay for me to choose Jesus and still continue [in this sin that I love]?" A brother told him that we all come as we are to God, and he is the one who cleanses us, but he tells us to "Repent, for the kingdom of God is near."  Adil wanted life but was unwilling to turn away from the wide way of destruction to the narrow, hard, holy way. Lord, we pray for those who want you but want their old life, too. Please, stir their souls to repent and live.', 'ramadan-2026' ),
                __( 'Pray that God would give grace to the church in this region to live out Jesus\' Golden Rule, “Do to Others”,  teaching with one another. Pray for simple house churches to model this kind of love to each other and those around them. Pray for believers to proactively serve, visit the sick, listen to the wounded, grieve with the hurting, and show compassion to the weak...instead of waiting to be served, visited, or listened to.
As they do, lead them to people who they could help form new house churches and Bible studies that would replicate such a way of life for God\'s glory.', 'ramadan-2026' ),
                __( 'Places of Community

Lead Christians in this region to find ways to serve their community and gain creative access to Muslim families around them. Pray that as Christians love Muslims the way they want to be loved, that Muslim families would want to know more about how they are fulfilling \'the Law and the Prophets\'.
Pray for Christians to stand out in their community for their unselfish love. Pray they would find people of peace who would gather family or friends to read the Bible together and learn more about the source of this unselfish love.
Pray for community leaders to be softened to the Gospel and desire their community to be shaped by Jesus\' teachings of love for enemies, serving others, and selflessness.', 'ramadan-2026' ),
            ];

            $has_been_modified = false;
            foreach ( $old_day_27 as $key => $old_content ) {
                $old_ctnt = wp_kses_post( $old_content );
                $old_ctnt = nl2br( $old_ctnt );
                if ( !strpos( $post_content, $old_ctnt ) ) {
                    $has_been_modified = true;
                }
            }
            if ( $has_been_modified ) {
                continue;
            }
            $d = $new_day_27;
            $fields = [];
            $content = [
                '<!-- wp:heading {"level":3} -->',
                '<h3><strong>' . __( 'Pray as the Lord leads you as you read today\'s verse', 'ramadan-2026' ) . '</strong></h3>',
                '<!-- /wp:heading -->',

                '<!-- wp:paragraph -->',
                '<p>' . wp_kses_post( P4_Ramadan_2026_Content::ramadan_format_message( $d[0], $fields ) ) . '</p>',
                '<!-- /wp:paragraph -->',

                '<!-- wp:heading {"level":3} -->',
                '<h3><strong>' . __( 'Praying with insight', 'ramadan-2026' ) . '</strong></h3>',
                '<!-- /wp:heading -->',
                '<!-- wp:paragraph {"style":{"typography":{"fontSize":"11px"}}} -->',
//                    '<p style="font-size:11px"><em>' . esc_html( P4_Ramadan_2026_Content::ramadan_format_message( __( 'Each of us who comes to Christ must repent of and renounce every pact, promise, or identity we held before faith in Christ. Join us in praying for our brothers and sisters in Christ from a Muslim background as they repent of their former identity as Muslims. This prayer is inspired by chapter 7 and 8 of Liberty to the Captives by Mark Durie', 'ramadan-2026' ), $fields ) ) . '</em></p>',
                '<!-- /wp:paragraph -->',

                '<!-- wp:paragraph -->',
                '<p>' . wp_kses_post( P4_Ramadan_2026_Content::ramadan_format_message( $d[1], $fields ) ) . '</p>',
                '<!-- /wp:paragraph -->',

                '<!-- wp:heading {"level":3} -->',
                '<h3><strong>' . __( 'For believers', 'ramadan-2026' ) . '</strong></h3>',
                '<!-- /wp:heading -->',

                '<!-- wp:paragraph -->',
                '<p>' . wp_kses_post( P4_Ramadan_2026_Content::ramadan_format_message( $d[2], $fields ) ) . '</p>',
                '<!-- /wp:paragraph -->',

                '<!-- wp:heading {"level":3} -->',
                '<h3><strong>' . __( 'For the lost', 'ramadan-2026' ) . '</strong></h3>',
                '<!-- /wp:heading -->',

                '<!-- wp:paragraph -->',
                '<p>' . wp_kses_post( P4_Ramadan_2026_Content::ramadan_format_message( $d[3], $fields ) ) . '</p>',
                '<!-- /wp:paragraph -->',

                '<!-- wp:heading {"level":3} -->',
                '<h3><strong>' . __( 'For the church', 'ramadan-2026' ) . '</strong></h3>',
                '<!-- /wp:heading -->',

                '<!-- wp:paragraph -->',
                '<p>' . wp_kses_post( P4_Ramadan_2026_Content::ramadan_format_message( $d[4], $fields ) ) . '</p>',
                '<!-- /wp:paragraph -->',


                '<!-- wp:heading {"level":3} -->',
                '<h3><strong>' . __( 'For areas of influence', 'ramadan-2026' ) . '</strong></h3>',
                '<!-- /wp:heading -->',

                '<!-- wp:paragraph -->',
                '<p>' . wp_kses_post( P4_Ramadan_2026_Content::ramadan_format_message( $d[5], $fields ) ) . '</p>',
                '<!-- /wp:paragraph -->',

                '<!-- wp:heading {"level":3} -->',
                '<h3><strong>' . __( 'Declaration of the scripture', 'ramadan-2026' ) . '</strong></h3>',
                '<!-- /wp:heading -->',

                '<!-- wp:paragraph -->',
                '<p>' . wp_kses_post( P4_Ramadan_2026_Content::ramadan_format_message( $d[6], $fields ) ) . '</p>',
                '<!-- /wp:paragraph -->',
            ];

            //update post content
            $post_content = implode( "\n", $content );
            $wpdb->update( $wpdb->posts, [ 'post_content' => $post_content ], [ 'ID' => $post_id ] );
        }
    }

    /**
     * @throws \Exception  Got error when dropping table $name.
     */
    public function down() {
    }

    /**
     * @return array
     */
    public function get_expected_tables(): array {
        return [];
    }

    /**
     * Test function
     */
    public function test() {
    }
}
