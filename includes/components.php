<?php
namespace UltimateEmailValidator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Trigger in {init} or after
 *
 * @version 1.0
 *
 * @author Oxibug
 *
 */
class Components {

    private static $_instance = null;
    

    /**
     * Plugin Settings in page {_APID_SLUG__settings}
     *
     * @var array|null
     *
     */
    public $plugin_settings = null;


    /**
     * Supported Defender Partners
     * 
     * - Partners:
     * 
     * > block-temporary-email
     * > quickemailverification
     * 
     * @var array
     */
    public $defender_partners = [
        'block_temporary_email',
        'quick_email_verification',
    ];

    public $user_id;

    public $url_admin;

    /**
     * AJAX URL
     *
     * admin_url( 'admin-ajax.php' )
     *
     * @var string
     *
     */
    public $url_ajax;


    /**
     * Absolute paths for most used directories in both
     * admin and frontend pages
     *
     * NOTE: All paths without trailing slash
     *
     * array(
     *
     *      {FOLDER}    => array(
     *
     *          'main'              => Main Path
     *          'Nested Folder'     => Main Path + '/Nested'
     *
     *      )
     *
     * )
     *
     * @var mixed
     */
    public $paths_abs = array();


    /**
     * All Scripts IDs for both admin and frontend pages
     *
     * array(
     *      {general}     => array(
     *          - {Script - ex. addons}   => SLUG + '/scripts/general/addons'
     *      )
     *
     *      {admin}     => array(
     *          - {Script - ex. core}   => SLUG + '/scripts/admin/core'
     *      )
     *
     *      {frontend}  => array(
     *          - {Script - ex. core}   => SLUG + '/scripts/frontend/core'
     *      )
     *
     * )
     *
     *
     * @var array
     */
    public $script_ids = array();

    /**
     * All script IDs need {defer} and {async} attrbutes
     *
     * @var mixed
     *
     */
    public $defer_scripts = array();
    public $async_scripts = array();


    public $is_multisite;

    public $mu_network_plugin;

    public $mu_current_blog;

    public $mu_defined_blog;

    /**
     * Whether to use {network_admin_url} or {admin_url}
     * 
     * @var bool
     */
    public $mu_use_network_admin_url;

    /**
     * Multisite: Compare ( Current Blog === Defined Blog )
     * 
     * Not Multisite: Always TRUE
     * 
     * @var     bool
     * @access  public
     */
    public $is_correct_blog_in_admin;

    /**
     * Multisite: Compare ( Current Blog === Defined Blog )
     * 
     * Not Multisite: Always TRUE
     * 
     * @var     bool
     * @access  public
     */
    public $is_correct_blog_in_everywhere;


    /**
     * The custom meta fields added to user meta data
     * 
     * @var array
     * 
     */
    public $user_meta_fields_ids;


    /**
     * The custom meta fields added to bbPress
     * 
     * @var array
     * 
     */
    public $bbp_meta_fields_ids;

    /**
     *
     * Represents the plugin state if it activated in Network Plugins level
     *
     * ATTENTION: Use this variable after [ register_activation_hook ] and [ register_deactivation_hook ] WordPress Hooks Otherwise
     * Use the local function static [ check_if_network_plugin ]
     *
     * @var bool
     *
     */
    public $is_network_plugin = false;


    /**
     *
     * Represents the plugin is designed to be activated in network level ONLY
     *
     * ATTENTION: Use this variable after [ register_activation_hook ] and [ register_deactivation_hook ] WordPress Hooks Otherwise
     * Use the local function static [ check_if_network_plugin ]
     *
     * @var bool
     *
     */
    public $is_network_plugin_only = false;


    /**
     *
     * Represents the plugin state if it activated in Network Plugins level and apply [ is_network_admin() ] function
     *
     * ATTENTION: Use this variable after [ register_activation_hook ] and [ register_deactivation_hook ] WordPress Hooks Otherwise
     * Use the local function static [ check_if_network_plugin_and_admin ]
     *
     * @var bool
     *
     */
    public $is_network_plugin_and_admin = false;



    /**
     * ACTION: {after_setup_theme}
     *
     * Represents whether the plugin listen on new updates or not
     *
     * NOTE: For developers use through filter [ PLUGIN_MAIN_SLUG/developer/user_can/upgrade_plugin ]
     *
     * @since 1.0
     *
     * @var bool
     *
     */
    public $is_plugin_upgradable = true;

    /**
     * ACTION: {after_setup_theme}
     *
     * Represents whether the ordinary user can adjust turn on/off page parts
     *
     * NOTE: For developers use through filter [ PLUGIN_MAIN_SLUG/developer/user_can/adjust_plugin ]
     *
     * @since 1.0
     *
     * @var bool
     *
     */
    public $is_plugin_adjustable = true;


    /**
     * Slugs
     * @var mixed
     */
    public $element_slugs = array();
    

    /**
     * Summary of instance
     * @return Components
     */
    public static function instance() {

        if( is_null( self::$_instance ) ) {

            self::$_instance = new self;

            /* From developer engine */
            self::$_instance->is_plugin_adjustable = apply_filters( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/filters/developer/user_can/adjust_plugin' ), true );

            self::$_instance->is_plugin_upgradable = apply_filters( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/filters/developer/user_can/upgrade_plugin' ), true );

            self::$_instance->user_id = get_current_user_id();

            self::$_instance->element_slugs = array(

                'cpt'       => array(
                    'main-wrp'      => 'css-oxibug-uev-main-wrp', /* Ued in CSS */
                    'main-inner'    => 'css-oxibug-uev-main-wrp', /* Ued in CSS */
                    'meta'          => 'ultiemvld'            /* Ued in CSS */
                ),

                'user'      => array(
                    'meta'  => 'ultiemvld'
                ),

                'bbp'       => array(
                    'meta'  => 'ultiemvld'
                )
            );

            /* Set User Custom Meta Fields IDs */
            self::$_instance->set_user_custom_meta_fields();
            

            self::$_instance->url_admin = admin_url();

            self::$_instance->url_ajax = admin_url( 'admin-ajax.php' );


            $assets_path = Paths::instance()->assets_path();
            
            $fs_premium_path = Paths::instance()->fs_premium_abs_path();

            self::$_instance->paths_abs = array(

                'assets'    => array(

                    'main'              => $assets_path,
                    'addons'            => $assets_path . '/addons',
                    'admin-backend'     => $assets_path . '/admin-backend',

                ),

                'fs-premium'    => [
                    'assets'    => [
                        'admin-backend'    => $fs_premium_path . '/assets/admin-backend',
                    ]
                ],
            );

            self::$_instance->script_ids = array(

                'general'   => array(

                    'addons'    => ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/scripts/general/addons' ),

                ),

                'admin'     => array(

                    'core'              => ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/scripts/admin/core' ),
                    'options-panel'     => ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/scripts/admin/options-panel' ),
                    'cpt'               => ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/scripts/admin/cpt' ),

                ),

                'cpt'     => array(

                    'core'      => ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/scripts/cpt/core' )

                ),

                'frontend'      => array(

                    'core'      => ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/scripts/frontend/core' ),
                    'recaptcha' => ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/scripts/g-recaptcha' )

                ),

                'fs-premium' => [
                    'repeater'   => ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/scripts/fs-premium/admin/repeater' ),
                ],

            );

            self::$_instance->defer_scripts = array(

                ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/scripts/addons' ),
                ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/scripts/frontend/core' ),
                ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/scripts/g-recaptcha' )

            );

            self::$_instance->async_scripts = array(

            );


            /* Multisite */
            self::$_instance->set_multisite_vars();
            

        }

        return self::$_instance;

    }


    /*
     * Silent Constructor
     *
     * */
    private function __construct() { }

    /**
     * Set User Custom meta fields IDs
     * 
     * @access  private
     * @since   1.0.0
     * 
     */
    private function set_user_custom_meta_fields() {
        
        $ele_slug = self::$_instance->element_slugs['user']['meta'];

        /**
         * Key => Value array
         * Example: 'is_support_team'   => sprintf( '%s_is_support_team', $ele_slug )
         * */
        self::$_instance->user_meta_fields_ids = array(
          
        );

    }


    private function set_multisite_vars() {

        /*
         * @Multisite
         *
         * After passing the main test
         * Assign the active blog to the public variable
         *
         * */
        self::$_instance->is_network_plugin = self::$_instance->check_if_network_plugin();

        self::$_instance->is_network_plugin_only = self::$_instance->check_if_network_plugin_only();

        self::$_instance->is_network_plugin_and_admin = self::$_instance->check_if_network_plugin_and_admin();


        self::$_instance->is_multisite = is_multisite();


        if( ! self::$_instance->is_multisite || ! self::$_instance->is_network_plugin ) {

            /* NOT Multisite - Always TRUE */
            self::$_instance->is_correct_blog_in_admin = true;

        }
        else {
            
            self::$_instance->mu_network_plugin = self::$_instance->is_network_level();

            self::$_instance->mu_current_blog = get_current_blog_id();
            
            self::$_instance->mu_defined_blog = -1;
            
            

            self::$_instance->is_correct_blog_in_admin      = self::$_instance->mu_correct_blog_test( TRUE );
            self::$_instance->is_correct_blog_in_everywhere = self::$_instance->mu_correct_blog_test( FALSE );
            

            self::$_instance->mu_use_network_admin_url = self::$_instance->mu_use_network_admin_url();


            if( TRUE === self::$_instance->is_correct_blog_in_admin ) {

                self::$_instance->mu_defined_blog = self::$_instance->mu_current_blog;
                
            }
            
        }

    }
        

    
    /**
     * Multisite
     *
     * Required a constant to be active in Multisite
     *
     * @return boolean
     *
     */
    private function mu_correct_blog_test( $in_admin_side = TRUE ) {
      
        switch( ULTIMATE_EMAIL_VALIDATOR_MULTISITE_ACTIVE_ON ) {
            
            case 'all': {
                    return ( $in_admin_side ) ? is_admin() : true;
                }
            case 'network_admin': {
                    return ( $in_admin_side ) ? ( self::$_instance->is_network_level() && is_network_admin() ) : self::$_instance->is_network_level();
                }
            case 'all_blogs_but_network': {
                    
                    if( $in_admin_side ) {
                        return ( self::$_instance->is_network_level() && is_network_admin() ) ? false : true;
                    }
                    else {
                        return ( self::$_instance->is_network_level() ) ? false : true;
                    }

                }

            case 'defined_blog': {

                    $current_blog = get_current_blog_id();

                    /*
                     * DO NOT use quotes for this constant which
                     * is represents the constant name that must provided by
                     * developer in {wp-config.php}
                     *
                     * */
                    $defined_blog = ( defined(ULTIMATE_EMAIL_VALIDATOR_ROOT_BLOG_ID) && ( is_numeric(constant(ULTIMATE_EMAIL_VALIDATOR_ROOT_BLOG_ID)) ) && ( intval(constant(ULTIMATE_EMAIL_VALIDATOR_ROOT_BLOG_ID)) > 0 ) ) ? constant( ULTIMATE_EMAIL_VALIDATOR_ROOT_BLOG_ID ) : 0;

                    $main_test = ( (int) $current_blog === (int) $defined_blog );

                    return ( $in_admin_side ) ? ( $main_test && is_admin() ) : $main_test;

                }

        }

        /* ELSE */
        return false;

    }

    
    /**
     * Multisite
     *
     * Whether to use {network_admin_url} | {admin_url}
     *
     * @return  boolean
     *
     * @since   1.0.0
     * @access  private
     */
    private function mu_use_network_admin_url() {
      
        switch( ULTIMATE_EMAIL_VALIDATOR_MULTISITE_ACTIVE_ON ) {
            
            case 'all':
            case 'network_admin': {
                    return ( self::$_instance->is_network_level() && is_network_admin() );
                } 

            case 'all_blogs_but_network':
            case 'defined_blog': {
                    return false;
                }

        }

        /* ELSE */
        return false;

    }
    

    /**
     *
     * Check is the plugin activate throuh Multisite WordPress
     *
     * NOTE: [ plugins.php ] included in main class to use [ is_plugin_active_for_network ] and [ is_network_only_plugin ] functions
     *
     *
     * @since 1.0
     *
     * @return boolean
     *
     */
    public function is_network_level() {

        return ( function_exists('is_multisite') && is_multisite() && ( is_plugin_active_for_network( ULTIMATE_EMAIL_VALIDATOR_PLUGIN_MAIN_FILE_PATH ) || is_network_only_plugin( ULTIMATE_EMAIL_VALIDATOR_PLUGIN_MAIN_FILE_PATH ) ) );

    }


    /**
     *
     * Check is the plugin activate throuh Multisite WordPress
     *
     * NOTE: [ plugins.php ] included in [ PLUGIN_MAIN_FILE_NAME.php ] to use [ is_plugin_active_for_network ] and [ is_network_only_plugin ] functions
     *
     *
     * @since 1.0
     *
     * @return boolean
     *
     */
    public function check_if_network_plugin() {

        return ( function_exists('is_multisite') && is_multisite() &&
            ( is_plugin_active_for_network( ULTIMATE_EMAIL_VALIDATOR_PLUGIN_MAIN_FILE_PATH ) || is_network_only_plugin( ULTIMATE_EMAIL_VALIDATOR_PLUGIN_MAIN_FILE_PATH ) ) );

    }


    /**
     *
     * Check is the plugin is network plugin ONLY
     *
     * NOTE: [ plugins.php ] included in [ PLUGIN_MAIN_FILE_NAME.php ] to use [ is_plugin_active_for_network ] and [ is_network_only_plugin ] functions
     *
     *
     * @since 1.0
     *
     * @return boolean
     *
     */
    public function check_if_network_plugin_only() {

        return ( function_exists('is_multisite') && is_multisite() && is_network_only_plugin( ULTIMATE_EMAIL_VALIDATOR_PLUGIN_MAIN_FILE_PATH ) );

    }


    /**
     *
     * Check is the plugin activate throuh Multisite WordPress and is_network_admin
     *
     * NOTE: [ plugins.php ] included in [ PLUGIN_MAIN_FILE_NAME.php ] to use [ is_plugin_active_for_network ] and [ is_network_only_plugin ] functions
     *
     *
     * @since 1.0
     *
     * @return boolean
     *
     */
    public function check_if_network_plugin_and_admin() {

        return ( function_exists('is_multisite') && is_multisite()
            && ( is_plugin_active_for_network( ULTIMATE_EMAIL_VALIDATOR_PLUGIN_MAIN_FILE_PATH ) || is_network_only_plugin( ULTIMATE_EMAIL_VALIDATOR_PLUGIN_MAIN_FILE_PATH ) )
            && is_network_admin() );

    }
    
}