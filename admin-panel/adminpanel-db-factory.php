<?php
namespace UltimateEmailValidator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Database Settings Manager
 *
 * @since 1.0
 * @author Oxibug
 *
 */
class AdminPanel_DBFactory {

    /**
     * An instance of the class
     *
     * @since 1.0
     *
     * @var AdminPanel_DBFactory
     *
     */
    private static $_instance = null;

    /**
     * Admin global components 
     * 
     * @var mixed
     */
    private $ap_globals = null;


    /**
     * Instantiate in WordPress action [ init ] in [ class-pas-superadmin.php ]
     *
     * NOTE: is_admin() applied in Super_Admin class
     *
     * @since 1.0
     *
     * @return AdminPanel_DBFactory
     *
     */
    public static function instance() {

        if( is_null( self::$_instance ) ) {

            self::$_instance = new self;
            
            self::$_instance->set_globals();

        }

        return self::$_instance;

    }


    /**
     * Silent Constructor
     *
     * */
    private function __construct() { }


    private function set_globals() {
        
        if( ! self::$_instance->ap_globals ) {
            self::$_instance->ap_globals = Admin_Components::instance();
        }

    }

    /**
     * Return the DB option by the page ID
     * 
     * @param   string  $admin_page_id - Accepted values: {plugin_settings | pg_client_plugin} 
     * 
     * @return  \null|string
     * 
     * @since   1.0.0
     * @access  public
     * 
     */
    public function get_db_option( $admin_page_id ) {
        
        self::$_instance->set_globals();

        if( array_key_exists( $admin_page_id, self::$_instance->ap_globals->admin_pages_db_option_keys ) ) {
            
            return self::$_instance->ap_globals->admin_pages_db_option_keys[ $admin_page_id ];

        }

        return null;

    }


    /**
     * Get page settings by the page ID
     * 
     * @param   string  $admin_page_id - Accepted values: {plugin_settings} 
     * @param   string  $use_site_meta - Force to get site meta saved options - For frontend pages ONLY - 
     * 
     * Ref: AdminPanel_DBManager::get_settings to know about this key
     * 
     * @return  array|null
     * 
     * @since   1.0.0
     * @access  public
     * 
     */
    public function get_settings_by_page( $admin_page_id, $use_site_meta = false ) {

        $db_option = self::$_instance->get_db_option( $admin_page_id );

        if( ! $db_option ) {
            return null;
        }

        return AdminPanel_DBManager::instance()->get_settings( array(

            'use_site_meta'     => $use_site_meta,    /* This plugin has {Network: True} Option and should activated through Network ONLY? */
            'db_option'         => $db_option,

        ) );

    }


    /**
     * Search for an option inside tab options array and return fallback value if not exist
     * 
     * @param mixed $tab 
     * @param mixed $option 
     * @param mixed $fallback 
     * @return mixed
     */
    public function get_tab_option( $tab = null, $option = '', $fallback = null ) {
        
        if( is_null( $tab ) || empty( $tab ) ) {
            return $fallback;
        }

        if( ! array_key_exists( $option, $tab ) ) {
            return $fallback;
        }

        return $tab[ $option ];

    }

}