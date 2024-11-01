<?php
namespace UltimateEmailValidator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Instantiated in {init} action
 *
 * All End User functions
 *
 * This class MUST in all sites so it MUST NOT pass the test of current blog,
 * Because the page like Login and Registration does NOT belongs to any site
 *
 * @version 1.0
 *
 * @author Oxibug
 *
 */
class Jocker_SuperJocker {

    private static $_instance = null;

    /**
     * All Globals
     *
     * @var Components
     *
     */
    public $loc_globals = null;


    /**
     * The Plugin Settings
     *
     * @var array|null
     *
     */
    public $plugin_settings = null;


    /**
     *
     *
     * @return Jocker_SuperJocker
     */
    public static function instance() {

        if( is_null( self::$_instance ) ) {

            self::$_instance = new self;


            self::$_instance->set_globals();

            $plugin_settings = self::$_instance->plugin_settings;

            if( ! is_array( $plugin_settings ) || empty( $plugin_settings ) ) {
                // Plugin Disabled
                return self::$_instance;
            }

            if( array_key_exists('defender_api_keys', $plugin_settings) &&
                array_key_exists('sw_disable_defender', $plugin_settings['defender_api_keys']) &&
                HelperFactory::instance()->cast_bool( $plugin_settings['defender_api_keys']['sw_disable_defender'] ) ) {

                    // Plugin Disabled
                    return self::$_instance;

            }

            // Reach Here? APIs is Enabled
            self::$_instance->core_actions();

        }

        return self::$_instance;

    }

    /**
     * Silent Constructor
     *
     * */
    private function __construct() { }


    /**
     * Set globals and get plugin settings saved in DB
     *
     * If no data saved in DB, Get the defaults values using the same scenario of {AdminPanel Backend Functions}
     * by using filter {PLUGIN_MAIN_SLUG/filters/admin/page/settings/map}
     *
     * @since   1.0.0
     * @access  private
     */
    private function set_globals() {

        if( ! self::$_instance->loc_globals ) {
            self::$_instance->loc_globals = Components::instance();
        }

        if( ! self::$_instance->plugin_settings ) {


            self::$_instance->plugin_settings = AdminPanel_DBFactory::instance()->get_settings_by_page( 'plugin_settings', FALSE );


            if( ! is_array( self::$_instance->plugin_settings ) || empty( self::$_instance->plugin_settings ) ) {

                self::$_instance->plugin_settings = self::$_instance->get_plugin_settings_defaults();

            }
            else {

                /* Fix for new added options */
                $defaults = self::$_instance->get_plugin_settings_defaults();

                foreach( $defaults as $tab_id => $tab_elements ) {

                    /*
                     * A new tab added
                     * */
                    if( ! array_key_exists( $tab_id, self::$_instance->plugin_settings ) ) {
                        self::$_instance->plugin_settings[ $tab_id ] = $tab_elements;

                        continue;
                    }

                    /*
                     * If we reach here
                     * the tab already exist BUT we may add a new element inside it
                     * */
                    foreach( $tab_elements as $ele_id => $ele_value ) {

                        if( ! array_key_exists( $ele_id, self::$_instance->plugin_settings[ $tab_id ] ) ) {

                            self::$_instance->plugin_settings[ $tab_id ][ $ele_id ] = $ele_value;
                            continue;

                        }

                    }

                }

            }

        }

    }


    public function get_plugin_settings_defaults() {

        /*
         * Add filter [ PLUGIN_MAIN_SLUG/filters/admin/page/settings/map ]
         *
         * */
        AdminPanel_PageMap::instance();

        $plugin_settings_map = apply_filters( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/filters/admin/page/settings/map' ), array() );

        return Admin_ElementsFactory::_Collect_Defaults( $plugin_settings_map );

    }


    /**
     * Add actions for NON Admin pages
     *
     * @since 1.0
     *
     */
    private function core_actions() {

        Filter_WP_Functions::instance();

        /**
         * IF plugin override {buddypress} registration form
         *
         * @see https://buddypress.org/support/topic/registration-default-wordpress-registration/
         *
         * */
        if( class_exists('BuddyPress') && has_action( 'bp_init', 'bp_core_wpsignup_redirect' ) ) {

            /* BuddyPress */
            Form_XLib_BuddyPress::instance();

        }
        else {

            Form_WP_Registration::instance();

        }


        Form_WP_UserUpdateOwnProfile::instance();

        Form_WP_Comment::instance();

        /* WooCommerce */
        Form_XLib_WooCommerce::instance();

        /* MailChimp */
        Form_XLib_MailChimp::instance();


        /* Contact Form 7 */
        Form_XLib_CF7::instance();

        /* Gravity Forms */
        Form_XLib_GravityForms::instance();

        /* Ninja Forms */
        Form_XLib_NinjaForms::instance();

    }




}