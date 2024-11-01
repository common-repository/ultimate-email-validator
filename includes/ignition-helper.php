<?php
namespace UltimateEmailValidator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0
 *
 * @package    Oxibug Plugin Settings
 *
 * @author     Oxibug
 *
 */
class IgnitionHelper {

    private static $_instance = null;

    private $hourly_event_action;

    /**
     * Contains the core plugin settings
     *
     * @var array
     *
     */
    private $plugin_core_options = array();


    public static function instance() {

        if( is_null( self::$_instance ) ) {

            self::$_instance = new self();

            self::$_instance->hourly_event_action = ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/actions/admin/events/hourly';

            self::$_instance->init();

        }

        return self::$_instance;

    }

    /**
     * Silent Constructor
     *
     * */
    private function __construct() { }

    /**
     * All registeration hooks
     *
     * CRON Job: See Backups Folder for the full example
     *
     * @return void
     */
    private function init() {

        /*
         * Create Necessary tables in activate
         *
         * */
        register_activation_hook( ULTIMATE_EMAIL_VALIDATOR__FILE__, array( &$this, 'activate' ) );

        register_deactivation_hook( ULTIMATE_EMAIL_VALIDATOR__FILE__, array( &$this, 'deactivate' ) );


        if( ! is_admin() ) {
            return;
        }

        add_action( 'wpmu_new_blog', array( &$this, 'activate_new_blog' ) );

    }


	/**
     * Do activation
	 *
     * @return void
     *
	 * @since   1.0.0
     * @access  Through function: - register_activation_hook
	 */
	public function activate( $network_wide ) {

        $is_multisite = is_multisite();

        if( $is_multisite ) {

            if( $network_wide ) {

                self::$_instance->do_activate_functions( $is_multisite, $network_wide );

                /*
                 * Create Tables
                 *
                 * While activating we made multiple agressive tests
                 * to check that the ROOT_BLOG constant is defined in {wp-config}
                 * so No Worries
                 *
                 * */
                if( 'defined_blog' == ULTIMATE_EMAIL_VALIDATOR_MULTISITE_ACTIVE_ON ) {

                    $defined_blog_id = constant( ULTIMATE_EMAIL_VALIDATOR_ROOT_BLOG_ID );

                    switch_to_blog( $defined_blog_id );

                    self::$_instance->do_activate_in_correct_blog( $is_multisite, $network_wide );

                    restore_current_blog();

                }

            }
            else {

                self::$_instance->do_activate_functions( $is_multisite, $network_wide );

            }

        }
        else {

            self::$_instance->do_activate_functions( $is_multisite, $network_wide );

            self::$_instance->do_activate_in_correct_blog( $is_multisite, $network_wide );


        }


        flush_rewrite_rules();

    }


    private function set_after_activate_notices( $notices, $is_multisite = FALSE, $is_network_wide = FALSE ) {

        $final_notices = $default = array(
            'updated'   => array(),
            'error'     => array()
        );

        if( $is_multisite ) {

            if( $is_network_wide ) {
                $final_notices = get_site_option( ULTIMATE_EMAIL_VALIDATOR_DBKEY_ADMIN_NOTICES, $default );
            }
            else {
                $final_notices = get_option( ULTIMATE_EMAIL_VALIDATOR_DBKEY_ADMIN_NOTICES, $default );
            }
        }
        else {
            $final_notices = get_option( ULTIMATE_EMAIL_VALIDATOR_DBKEY_ADMIN_NOTICES, $default );
        }


        if( ! empty( $final_notices ) ) {

            foreach( $final_notices as $status => $status_notices ) {

                if( empty( $notices[ $status ] ) ) {
                    continue;
                }

                $final_notices[ $status ] = array_merge( $final_notices[ $status ], $notices[ $status ] );

            }

        }


        /*
         * Update with right function
         *
         * */
        if( $is_multisite ) {
            if( $is_network_wide ) {
                update_site_option( ULTIMATE_EMAIL_VALIDATOR_DBKEY_ADMIN_NOTICES, $final_notices );
            }
            else {
                update_option( ULTIMATE_EMAIL_VALIDATOR_DBKEY_ADMIN_NOTICES, $final_notices );
            }
        }
        else {
            update_option( ULTIMATE_EMAIL_VALIDATOR_DBKEY_ADMIN_NOTICES, $final_notices );
        }

    }

    /**
     * Do some actions after activating plugin
     *
     * Add Welcome message with some notices to describe some steps for the Ordinary Users and Developers as well.
     *
     * @since   1.0
     * @access  public
     */
    public function do_activate_functions( $is_multisite = FALSE, $is_network_wide = FALSE ) {

        /**
         * After activating notices
         *
         * @var array
         * */
        $notices = array(
            'updated'      => array(
                wp_kses_post( __( 'Howdy, Welcome to <b>(Ultimate Email Validator)</b>,<br/> Stop registration, update user profile, post comments or using contact forms with temporary or disposable email addresses using a completely free API.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) ),
            ),
            'error'         => array()
        );

        /*
         * Save notices in DB to display in action {admin_notices} after activate
         *
         * */
        self::$_instance->set_after_activate_notices( $notices, $is_multisite, $is_network_wide );

    }


    /**
     * Return summary of tables existance
     *
     * @param   array   $tables_before_create   The tables statuses - BEFORE - trigger Create Tables function
     * @param   array   $tables_after_create    The tables statuses - AFTER - trigger Create Tables function
     *
     * @return  array
     *
     * @since   1.0.0
     * @access  private
     */
    private function get_tables_creation_summary( $tables_before_create, $tables_after_create ) {

        $notices            = array(
            'updated'   => array(),
            'error'     => array(),
        );

        if( ( FALSE !== $tables_before_create ) && ! empty( $tables_before_create ) ) {

            foreach( $tables_before_create as $tbl_name => $status ) {

                /* Impossible */
                if( ! isset( $tables_after_create[ $tbl_name ] ) ) {
                    continue;
                }


                if( FALSE === HelperFactory::instance()->cast_bool( $status ) ) {

                    if( TRUE === HelperFactory::instance()->cast_bool( $tables_after_create[ $tbl_name ] ) ) {
                        $notices['updated'][] = wp_kses_post( sprintf( __( 'Table: <b>%s</b> successfully created.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), $tbl_name ) );
                    }
                    else {
                        $notices['error'][] = wp_kses_post( sprintf( __( 'Failed to create table: <b>%s</b>.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), $tbl_name ) );
                    }

                }
                else {
                    $notices['updated'][] = wp_kses_post( sprintf( __( 'Table: <b>%s</b> already exist.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), $tbl_name ) );
                }

            }

        }


        if( ! empty( $notices['updated'] ) ) {

            $notices['updated'] = array_merge( array(
                wp_kses_post( sprintf( __( '<b>%s</b> Tables: <hr/>', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), count( $notices['updated'] ) ) )
            ), $notices['updated'] );

        }

        if( ! empty( $notices['error'] ) ) {

            $notices['error'] = array_merge( array(
                wp_kses_post( sprintf( __( '<b>%s</b> Tables: <hr/>', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), count( $notices['error'] ) ) )
            ), $notices['error'] );

        }

        return $notices;

    }

    /**
     * Triggered ONLY in the defined correct blog
     *
     * @param   bool    $is_multisite
     * @param   bool    $is_network_wide
     *
     * @return  void
     */
    public function do_activate_in_correct_blog( $is_multisite = FALSE, $is_network_wide = FALSE ) {

        /* 01- CREATE CUSTOM TABLES HERE */


        /* 02- IN CASE YOU NEED TO DISPLAY THE TABLES CREATED - USE the private function {get_tables_creation_summary} */


        /* == Cancel displaying created tables == */
        $notices    = array(
            'updated'   => array(),
            'error'     => array(),
        );

        /*
         * Save notices in DB to display in action {admin_notices} after activate
         *
         * */
        self::$_instance->set_after_activate_notices( $notices, $is_multisite, $is_network_wide );

    }


    public function deactivate( $network_wide ) {

        $is_multisite = is_multisite();

        if( $is_multisite ) {

            if( $network_wide ) {

                self::$_instance->do_deactivate_functions( $is_multisite, $network_wide );

                /*
                 * Create Tables
                 *
                 * While activating we made multiple agressive tests
                 * to check that the ROOT_BLOG constant is defined in {wp-config}
                 * so No Worries
                 *
                 * */
                if( 'defined_blog' == ULTIMATE_EMAIL_VALIDATOR_MULTISITE_ACTIVE_ON ) {

                    $defined_blog_id = constant( ULTIMATE_EMAIL_VALIDATOR_ROOT_BLOG_ID );

                    switch_to_blog( $defined_blog_id );

                    self::$_instance->do_deactivate_in_correct_blog( $is_multisite, $network_wide );

                    restore_current_blog();

                }
            }
            else {

                self::$_instance->do_deactivate_functions( $is_multisite, $network_wide );

            }

        }
        else {

            self::$_instance->do_deactivate_functions( $is_multisite, $network_wide );

            self::$_instance->do_deactivate_in_correct_blog( $is_multisite, $network_wide );
        }

        flush_rewrite_rules();

	}


    /**
     *
     * Do some actions after de-activating plugin
     *
     * Remove option [ ULTIMATE_EMAIL_VALIDATOR_DBKEY_ADMIN_NOTICES ]
     *
     * @since   1.0.0
     * @access  public
     */
    public function do_deactivate_functions( $is_multisite = FALSE, $is_network_wide = FALSE ) {

        /* Delete Temporary Options */
        if( $is_multisite ) {
            if( $is_network_wide ) {
                delete_site_option( ULTIMATE_EMAIL_VALIDATOR_DBKEY_ACTIVATING_SETTINGS );
            }
            else {
                delete_option( ULTIMATE_EMAIL_VALIDATOR_DBKEY_ACTIVATING_SETTINGS );
            }
        }
        else {
            delete_option( ULTIMATE_EMAIL_VALIDATOR_DBKEY_ACTIVATING_SETTINGS );
        }

    }




    /**
     * Do deactivate functions in the correct blog only
     * Like clear Cron Jobs {wp_clear_scheduled_hook}
     *
     * @since   1.0.0
     * @access  public
     */
    public function do_deactivate_in_correct_blog( $is_multisite = FALSE, $is_network_wide = FALSE ) {

        return $is_multisite;

    }


    /**
     * NOT Working
     *
     * Add main plugin settings into DB after the WordPress is fully loaded
     *
     * Through WordPress action [ wp_loaded ]
     *
     * @since   1.0.0
     * @access  public
     */
    public function after_wp_loaded() {

        if( Engine::$_instance->globals->is_network_plugin && is_network_admin() ) {

            update_site_option( ULTIMATE_EMAIL_VALIDATOR_DBKEY_ACTIVATING_SETTINGS, $this->plugin_core_options );

        }
        else {

            update_option( ULTIMATE_EMAIL_VALIDATOR_DBKEY_ACTIVATING_SETTINGS, $this->plugin_core_options );

        }

    }



    /**
     * NOT Working
     *
     * This function sets the local variable [ $plugin_core_options ] with the suitable table data
     *
     * Using [ get_site_option ] function for network plugin otherwise [ get_option ]
     *
     * NOTE: saving DB values through [ wp_loaded ] action using local function [ after_wp_loaded ]
     *
     * @since   1.0.0
     * @access  private
     */
    private function get_plugin_core_options() {

        $default_options = array(
            'active' => true
        );

        // 1. Multisite
        if( self::$is_network_plugin_and_admin ) {
            $this->plugin_core_options = get_site_option( ULTIMATE_EMAIL_VALIDATOR_DBKEY_ACTIVATING_SETTINGS, $default_options );
        }

        // 2. Not Network Admin Pages || Not Multisite
        if( 0 == count( $this->plugin_core_options ) ) {
            $this->plugin_core_options = get_option( ULTIMATE_EMAIL_VALIDATOR_DBKEY_ACTIVATING_SETTINGS, $default_options );
        }

    }


    /**
     *
     * Switch to the new blog added to the current site
     *
     * Through the WordPress action [ wpmu_new_blog ]
     *
     * @param   int $blog_id
     *
     * @return  void
     *
     * @since   1.0.0
     * @access  public
     */
    public function activate_new_blog( $blog_id ) {

        /*
         * [did_action] Retrieve the number of times an action is fired
         *
         * Make sure the action [wpmu_new_blog] fired once
         *
         * */
        if( 1 !== did_action('wpmu_new_blog') ) {
            return;
        }

        if( 'defined_blog' == ULTIMATE_EMAIL_VALIDATOR_MULTISITE_ACTIVE_ON ) {

            switch_to_blog( $blog_id );

            self::$_instance->do_activate_functions();

            restore_current_blog();

        }

    }


}