<?php
namespace UltimateEmailValidator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Do AJAX saving 
 *
 * @version 1.0
 * 
 * @author Oxibug
 * 
 */

class AdminPanel_DB_Save {

    /**
     * An instance of the class
     * 
     * @since 1.0
     * 
     * @var AdminPanel_DB_Save
     * 
     */
    private static $_instance = null;


    /**
     * All Admin Pages globals
     * 
     * @var Admin_Components
     * 
     */
    private $ap_globals;
    
    /**
     * All globals
     * 
     * @var Components
     * 
     */
    private $loc_globals;
    

    /**
     * Instantiate in WordPress action [ init ] in [ class-pas-superadmin.php ]
     * 
     * NOTE: is_admin() applied in Super_Admin class
     * 
     * @since 1.0
     * 
     * @return AdminPanel_DB_Save
     * 
     */
    public static function instance() {
        
        if( is_null( self::$_instance ) ) {
            
            self::$_instance = new self;

            self::$_instance->ap_globals = Admin_Components::instance();

            self::$_instance->loc_globals = self::$_instance->ap_globals->loc_globals;
            
            self::$_instance->save_action();

        }

        return self::$_instance;

    }


    /** 
     * Silent Constructor 
     * 
     * */
    private function __construct() { }
    

    /**
     * Start save actions
     * 
     * @since 1.0
     * 
     */
    public function save_action() {

        if( ! isset( $_POST[ ULTIMATE_EMAIL_VALIDATOR_ADMIN_PAGE_CON_PREFIX ] ) ) {
            /* Do NOT Die Here - JUST Return to make other pages works */
            return;
        }

        if( ! current_user_can( self::$_instance->ap_globals->apcap_panel ) ) {
            wp_die( esc_html__( 'Sorry, You do not have permission to save this page!', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) );
        }
        
        $sendback = wp_get_referer();
        $db_option = null;
        
        /* Current URL Equals _wp_http_referer */
        if( ! $sendback ) {
            
            /* DO NOT use this for redirect */    
            $current_page = wp_get_raw_referer();

            if( FALSE !== strpos( $current_page, self::$_instance->ap_globals->apid_settings ) ) {
                $db_option = AdminPanel_DBFactory::instance()->get_db_option( 'plugin_settings' );
            }
            
        }
        else {
            if( wp_safe_redirect( $sendback ) ) {
                exit;
            }
        }


        if( is_null( $db_option ) ) {
            return;
        }

        $session_data = wp_unslash( $_POST[ ULTIMATE_EMAIL_VALIDATOR_ADMIN_PAGE_CON_PREFIX ] );

        /**
         * All built-in data in {__oxibug__} key
         * 
         * @var array|null
         * 
         * */
        $session_admin = null;
        $txt_import = null;

        if( array_key_exists( '__oxibug__', $session_data ) ) {
            $session_admin = $session_data['__oxibug__'];
            unset( $session_data['__oxibug__'] );
        }

        if( is_null( $session_admin ) ) {
            wp_die( esc_html__( 'Invalid Request! Session Admin is missed, Please reload the page an try again', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) );
        }

        if( ! is_admin() || ! wp_verify_nonce( $session_admin['_security_nonce'], ULTIMATE_EMAIL_VALIDATOR_AJAX_NONCE_ACTION ) ) {
            wp_die( esc_html__( 'Invalid Request! Please reload the page an try again', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) );
        }
        
        if( array_key_exists( 'import_export', $session_data ) ) {
            $txt_import = $session_data['import_export']['text_import'];
            unset( $session_data['import_export'] );
        }
          
        
        $elements_map = array_key_exists( 'elements', $session_admin ) ? HelperFactory::instance()->maybe_base64_decode( $session_admin['elements'] ) : null;

        if( isset( $elements_map['import_export'] ) ) {
            unset( $elements_map['import_export'] );
        }

        if( is_null( $elements_map ) ) {
            wp_die( esc_html__( 'This Page has no elements to save', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) );
        }

        $is_network_plugin = self::$_instance->loc_globals->is_network_plugin;
        $is_network_plugin_only = self::$_instance->loc_globals->is_network_plugin_only;
        $is_network_plugin_and_admin = self::$_instance->loc_globals->is_network_plugin_and_admin;

        $action = 'save';
        if( array_key_exists('reset', $session_admin) ) {
            /* On click on RESET button The key [submit] will replaced with [reset] key instead */
            $action = 'reset';
        }

        $sanitized_user_inputs = null;
        $success_msgs = $fail_msgs = array();


        /* Save Options */
        switch( $action ) {
                
            case 'save': {
                
                if( ! empty( $txt_import ) ) {

                    if( is_null( $imported_values = HelperFactory::instance()->maybe_base64_decode( $txt_import ) ) ) {
                        
                        Admin_Components::instance()->set_save_notices( array(
                            400     => esc_html__( 'The import text you enetered cannot be saved!', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN )
                        ), 'fail' );

                        return;
                    }

                    /* Save Import Text */
                    $sanitized_user_inputs = SanitizationFactory::instance()->sanitize_user_inputs( $imported_values, $elements_map, TRUE );

                    $success_msgs = array(
                        200     => esc_html__( 'Import Successfully!', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN )
                    );
                    
                    $fail_msgs = array(
                        400     => esc_html__( 'Import Failed!', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN )
                    );
                    
                }
                else {                    
                    $sanitized_user_inputs = SanitizationFactory::instance()->sanitize_user_inputs( $session_data, $elements_map, TRUE );
                    
                    $success_msgs = array(
                        200     => esc_html__( 'Settings Saved!', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN )
                    );
                    
                    $fail_msgs = array(
                        400     => esc_html__( 'Save Operation Failed, Or you did not made any changes!', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN )
                    );
                }

            } break;

            case 'reset': {
                    
                $defaults = array_key_exists( 'defaults', $session_admin ) ? HelperFactory::instance()->maybe_base64_decode( $session_admin['defaults'] ) : null;
                
                if( is_null( $defaults ) ) {
                    
                    Admin_Components::instance()->set_save_notices( array(
                        400     => esc_html__( 'Defaults data not available!', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN )
                    ), 'fail' );

                    return;
                }

                $sanitized_user_inputs = SanitizationFactory::instance()->sanitize_user_inputs( $defaults, $elements_map, TRUE );
                
                $success_msgs = array(
                    200     => esc_html__( 'Settings Restored to the default data!', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN )
                );
                    
                $fail_msgs = array(
                    400     => esc_html__( 'Restoring Operation Failed Or already restored!', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN )
                );

            } break;

        }
        

        if( is_null( $sanitized_user_inputs ) ) {
            wp_die( esc_html__( 'This Page has no elements to save', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) );
        }
            
        // wp_die( print_r($sanitized_user_inputs) );

        /*
         * Everything is OK .. Let's Save
         * 
         * */
        if( AdminPanel_DBManager::instance()->update_settings( array(
              
            'db_option'             => $db_option,
            'value'                 => wp_unslash( $sanitized_user_inputs )

        ) ) ) {

            Admin_Components::instance()->set_save_notices( $success_msgs, 'success' );
            
        }
        else {
            Admin_Components::instance()->set_save_notices( $fail_msgs, 'fail' );
        }
               

    }

}