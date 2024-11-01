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
class AdminPanel_DBManager {

    /**
     * An instance of the class
     *
     * @since 1.0
     *
     * @var AdminPanel_DBManager
     *
     */
    private static $_instance = null;

    /**
     * All global components 
     * 
     * @var mixed
     */
    private $gl_components = null;


    /**
     * Instantiate in WordPress action [ init ] in [ class-pas-superadmin.php ]
     *
     * NOTE: is_admin() applied in Super_Admin class
     *
     * @since 1.0
     *
     * @return AdminPanel_DBManager
     *
     */
    public static function instance() {

        if( is_null( self::$_instance ) ) {

            self::$_instance = new self;

            /*
             * Get global Components
             *
             * DO NOT use Engine
             *
             * */
            self::$_instance->gl_components = Components::instance();

        }

        return self::$_instance;

    }


    /**
     * Silent Constructor
     *
     * */
    private function __construct() { }


    private function _get_mu_correct_options( $blog_id, $db_option_id ) {

        switch_to_blog( $blog_id );

        $plugin_settings = get_option( $db_option_id, null );

        restore_current_blog();

        return $plugin_settings;

    }
    
    private function _get_mu_correct_transient( $blog_id, $db_option_id, $delete_after_get = false ) {

        switch_to_blog( $blog_id );

        $plugin_settings = get_transient( $db_option_id );

        if( $plugin_settings && $delete_after_get ) {
            delete_transient( $db_option_id );
        }

        restore_current_blog();

        return ( FALSE !== $plugin_settings ) ? maybe_unserialize( $plugin_settings ) : null;

    }


    /**
     * Get the plugin admin settings from the correct table depending on {$network} status which mean
     * The plugin activated in Multisite Network level
     *
     * @param array     $args_user The following keys
     * 
     * use_site_meta        : bool          - If TRUE: the option will be get from {wp_site_meta} - This for ULTIMATE_EMAIL_VALIDATOR_MULTISITE_ACTIVE_ON -> {all} only
     * db_option            : string        - The Database option name
     *
     * @return  mixed
     *
     * @since   1.0.0
     * @access  public
     * 
     */
    public function get_settings( $args_user = '' ) {

        $my_globals = self::$_instance->gl_components;

        $args_default = array(

            'use_site_meta' => false,   /* If TRUE: the option will be get from {wp_site_meta} */
            'db_option'     => null,

        );

        $args = wp_parse_args( $args_user, $args_default );

        $is_network_level = Components::instance()->is_network_level();

        /*
         * Not Multisite or Not Active as Network
         * 
         * */
        if( ! $my_globals->is_multisite || 
            ! $is_network_level ) {
            return get_option( $args['db_option'], null );
        }

        /* NOW: Network Level is TRUE */

        switch( ULTIMATE_EMAIL_VALIDATOR_MULTISITE_ACTIVE_ON ) {
            
            case 'all': {
                    
                    if ( is_network_admin() ) {
                        return get_site_option( $args['db_option'], null );
                    }
                    else {
                        /* Frontend or admin */
                        return ( $args['use_site_meta'] ) ? get_site_option( $args['db_option'], null ) : get_option( $args['db_option'], null );
                    }

                }

            case 'network_admin': {

                    /* Network Admin will save only in site_meta so retrieve the site_meta from Admin or Frontend */
                    return get_site_option( $args['db_option'], null );

                }

            case 'defined_blog': {
                    return self::$_instance->_get_mu_correct_options( $my_globals->mu_defined_blog, $args['db_option'] );
                }

            case 'all_blogs_but_network':
            default: {
                    return get_option( $args['db_option'], null );
                }

        }
        
    }


    /**
     * Return the right Update method
     *
     * @param   array   $args_user - The following Keys
     * 
     * db_option            :   string      - The Database option name
     * value                :   mixed       - The Database value
     *
     * @return  bool
     *
     * @since   1.0.0
     * @access  public
     */
    public function update_settings( $args_user = '' ) {

        $my_globals = self::$_instance->gl_components;

        $args_default = array(
            'db_option'     => null,
            'value'         => null
        );

        $args = wp_parse_args( $args_user, $args_default );

        $is_network_level = Components::instance()->is_network_level();

        /*
         * Not Multisite or Not Active as Network
         * 
         * */
        if( ! $my_globals->is_multisite || 
            ! $is_network_level ) {
            return update_option( $args['db_option'], wp_unslash( $args['value'] ) );
        }
        

        switch( ULTIMATE_EMAIL_VALIDATOR_MULTISITE_ACTIVE_ON ) {
            
            case 'all':
            case 'network_admin': {
                    return ( is_network_admin() ) ? update_site_option( $args['db_option'], wp_unslash( $args['value'] ) ) : update_option( $args['db_option'], wp_unslash( $args['value'] ) );
                }

            case 'all_blogs_but_network':
            case 'defined_blog':
            default: {
                    return update_option( $args['db_option'], wp_unslash( $args['value'] ) );
                }

        }
        
    }


    /**
     * Update the correct DB transient method
     *
     * @param array $args_user - The following Keys
     * 
     * db_option            :   string      - The Database option name
     *
     * value                :   mixed       - The Database value - We'll apply maybe_serialize then wp_unslash it
     * exp                  :   int         - Expiration in seconds
     * @return  bool
     *
     * @since   1.0.0
     * @access  public
     * 
     */
    public function update_transient( $args = '' ) {
        
        $my_globals = self::$_instance->gl_components;

        $args_default = array(

            'db_option'             => null,
            'value'                 => null,

            'exp'                   => MINUTE_IN_SECONDS
        );

        $args = wp_parse_args( $args, $args_default );

        $unslashed_value = wp_unslash( $args['value'] );


        $is_network_level = Components::instance()->is_network_level();

        /*
         * Not Multisite or Not Active as Network
         * 
         * */
        if( ! $my_globals->is_multisite || 
            ! $is_network_level ) {
            return set_transient( $args['db_option'], maybe_serialize( $unslashed_value ), $args['exp'] );
        }


        switch( ULTIMATE_EMAIL_VALIDATOR_MULTISITE_ACTIVE_ON ) {
            
            case 'all':
            case 'network_admin': {
                    return ( is_network_admin() ) ? set_site_transient( $args['db_option'], maybe_serialize( $unslashed_value ), $args['exp'] ) : set_transient( $args['db_option'], maybe_serialize( $unslashed_value ), $args['exp'] );
                }

            case 'all_blogs_but_network':
            case 'defined_blog':
            default: {
                    return set_transient( $args['db_option'], maybe_serialize( $unslashed_value ), $args['exp'] );
                }

        }
        
    }


    /**
     * Get the plugin admin settings from the correct table depending on {$network} status which mean
     * The plugin activated in Multisite Network level
     *
     * @param   array   $args_user - The following keys
     * 
     * db_option            : string        - The Database option name
     * delete_after_get     : bool          - Whether delete this option after get or leave in the DB until expiration
     * 
     * @return  mixed
     *
     * @since   1.0.0
     * @access  public
     * 
     */
    public function get_transient( $args = '' ) {

        $my_globals = self::$_instance->gl_components;

        $args_default = array(

            'db_option'                 => null,
            'delete_after_get'          => false
        );

        $args = wp_parse_args( $args , $args_default );

        
        $is_network_level = Components::instance()->is_network_level();

        /*
         * Not Multisite or Not Active as Network
         * 
         * */
        if( ! $my_globals->is_multisite || 
            ! $is_network_level ) {

            if( FALSE === ( $value = get_transient( $args['db_option'] ) ) ) {
                return null;
            }

            if( $args['delete_after_get'] ) {
                delete_transient( $args['db_option'] );
            }

            return maybe_unserialize( $value );
        }


        switch( ULTIMATE_EMAIL_VALIDATOR_MULTISITE_ACTIVE_ON ) {
            
            case 'all':
            case 'network_admin': {
                    
                    $value = ( is_network_admin() ) ? get_site_transient( $args['db_option'] ) : get_transient( $args['db_option'] );

                    if( FALSE === $value ) {
                        return null;
                    }

                    if( $args['delete_after_get'] ) {
                        ( is_network_admin() ) ? delete_site_transient( $args['db_option'] ) : delete_transient( $args['db_option'] );
                    }

                    return maybe_unserialize( $value );

                }
            
            case 'defined_blog': {
                    return self::$_instance->_get_mu_correct_transient( $my_globals->mu_defined_blog, $args['db_option'], $args['delete_after_get'] );
                }

            case 'all_blogs_but_network':
            default: {

                    if( FALSE === ( $value = get_transient( $args['db_option'] ) ) ) {
                        return null;
                    }

                    if( $args['delete_after_get'] ) {
                        delete_transient( $args['db_option'] );
                    }

                    return maybe_unserialize( $value );

                }

        }


    }

}