<?php

/**
 * Plugin Name: Ultimate Email Validator
 * Description: Safeguard Your Website: Block Disposable and Temporary Emails Across Registration, Comments, Contact Form 7, Mailchimp, Woocommerce, and More!
 * Plugin URI:  https://oxibug.com/plugins/ultimate-email-validator
 * Version:     2.2.0
 * Author:      Oxibug
 * Author URI:  https://oxibug.com/
 * Text Domain: ultimate-email-validator
 * Network:     true
 *
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly.
}


if ( function_exists( 'ultimate_email_validator_fs' ) ) {
    ultimate_email_validator_fs()->set_basename( false, __FILE__ );
} else {
    
    if ( !function_exists( 'ultimate_email_validator_fs' ) ) {
        // Create a helper function for easy SDK access.
        function ultimate_email_validator_fs()
        {
            global  $ultimate_email_validator_fs ;
            
            if ( !isset( $ultimate_email_validator_fs ) ) {
                // Activate multisite network integration.
                if ( !defined( 'WP_FS__PRODUCT_5875_MULTISITE' ) ) {
                    define( 'WP_FS__PRODUCT_5875_MULTISITE', true );
                }
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $ultimate_email_validator_fs = fs_dynamic_init( array(
                    'id'             => '5875',
                    'slug'           => 'ultimate-email-validator',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_09238f3409bd6ee0db0d38c30e0ad',
                    'is_premium'     => false,
                    'premium_suffix' => 'Basic',
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'menu'           => array(
                    'slug'    => 'ultiemvld_settings',
                    'support' => false,
                    'account' => true,
                    'network' => true,
                ),
                    'is_live'        => true,
                ) );
            }
            
            return $ultimate_email_validator_fs;
        }
        
        // Init Freemius.
        ultimate_email_validator_fs();
        // Signal that SDK was initiated.
        do_action( 'ultimate_email_validator_fs_loaded' );
    }

}

if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_VERSION' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_VERSION', '2.2.0' );
}
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_JSCSS_VERSION' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_JSCSS_VERSION', '2.1.0' );
}
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_DB_VERSION' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_DB_VERSION', '1.0.0' );
}
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR__FILE__' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR__FILE__', __FILE__ );
}
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_PLUGIN_URL' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_PLUGIN_URL', plugins_url( '', ULTIMATE_EMAIL_VALIDATOR__FILE__ ) );
}
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_PLUGIN_PATH' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_PLUGIN_PATH', plugin_dir_path( ULTIMATE_EMAIL_VALIDATOR__FILE__ ) );
}
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_PLUGIN_BASENAME' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_PLUGIN_BASENAME', plugin_basename( ULTIMATE_EMAIL_VALIDATOR__FILE__ ) );
}
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_PLUGIN_MAIN_FILE_PATH' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_PLUGIN_MAIN_FILE_PATH', 'ultimate-email-validator/ultimate-email-validator.php' );
}
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_PLUGIN_MAIN_FILE_NAME' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_PLUGIN_MAIN_FILE_NAME', 'ultimate-email-validator.php' );
}
/**
 * Text Domain
 * */
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN', 'ultimate-email-validator' );
}
/**
 * Main Slug
 *
 * VI: used in extensions
 * */
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN', 'ultimate_email_validator' );
}
/*
 * The root blog constant id that the developer MUST add in {wp_config.php} before activate
 *
 * Example: define( 'ULTIMATE_EMAIL_VALIDATOR_ROOT_BLOG_ID', 2 );
 *
 * The plugin will create tables in that blog {2}
 *
 * */
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_MU_USE_IN_ONE_BLOG' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_MU_USE_IN_ONE_BLOG', false );
}
/*
 * Special Case - The user will use the value {ULTIMATE_EMAIL_VALIDATOR_ROOT_BLOG}
 * as a constant in his {wp-config} to pass the BLOG ID
 *
 * VI: used in extensions
 * */
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_ROOT_BLOG_ID' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_ROOT_BLOG_ID', 'ULTIMATE_EMAIL_VALIDATOR_ROOT_BLOG' );
}
/*
 * Database Settings Values
 *
 * */
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_DBKEY_ACTIVATING_SETTINGS' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_DBKEY_ACTIVATING_SETTINGS', ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_db_admin_activate' );
}
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_DBKEY_ADMIN_NOTICES' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_DBKEY_ADMIN_NOTICES', ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_db_admin_notices' );
}
/**
 * AJAX variables slug and security
 * Security Nonce for all admin pages {Options Panel} and new {CPT}
 * */
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_AJAX_NONCE_ACTION' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_AJAX_NONCE_ACTION', ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_global_security' );
}
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_SLUG_AJAX_VARS' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_SLUG_AJAX_VARS', 'ajvar_ultimate_email_validator' );
}
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_SLUG_ERROR' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_SLUG_ERROR', ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_error' );
}
/*
 * Admin Page Settings Strings
 *
 * ap: Admin Panel
 *
 * apele: Admin Panel Element
 *
 * */
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_APID_SLUG' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_APID_SLUG', 'ultiemvld' );
}
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_ADMIN_PAGE_OPTION_GROUP' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_ADMIN_PAGE_OPTION_GROUP', ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_ap' );
}
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_ADMIN_PAGE_ID' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_ADMIN_PAGE_ID', ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_settings' );
}
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_ADMIN_PAGE_CON_PREFIX' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_ADMIN_PAGE_CON_PREFIX', ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_apele' );
}
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_ADMIN_MENU_TITLE' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_ADMIN_MENU_TITLE', esc_html__( 'Ultimate Email Validator', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) );
}
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_ADMIN_PAGE_TITLE' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_ADMIN_PAGE_TITLE', esc_html__( 'Ultimate Email Validator Settings', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) );
}
/*
 * Database Settings Values
 *
 * */
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_DBKEY_ACTIVATING_SETTINGS' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_DBKEY_ACTIVATING_SETTINGS', ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_db_admin_activate' );
}
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_DBKEY_ADMIN_NOTICES' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_DBKEY_ADMIN_NOTICES', ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_db_admin_notices' );
}
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_DBKEY_ADMIN_SAVE_PAGE_SUCCESS' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_DBKEY_ADMIN_SAVE_PAGE_SUCCESS', ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_db_admin_save_success' );
}
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_DBKEY_ADMIN_SAVE_PAGE_FAIL' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_DBKEY_ADMIN_SAVE_PAGE_FAIL', ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_db_admin_save_fail' );
}
/**
 * all              | Network and all other sites -
 *
 * -- Functions
 *
 * {AdminPanel_DBManager::get_settings}
 * {Admin_DBFactory::get_settings_by_page} use arg {use_site_meta} to get saved options from site_meta table
 *
 * ------------------------------------------------
 *
 * network_admin    | TGMPA WON'T Work for that option because of TGMPA limitiations
 *
 * ------------------------------------------------
 *
 * defined_blog     | MUST ADD {Network: true} in plugin details Otherwise will generate unexpected output
 *
 * ------------------------------------------------
 *
 * all_blogs_but_network
 *
 * ------------------------------------------------
 *
 * @var
 *
 * */
if ( !defined( 'ULTIMATE_EMAIL_VALIDATOR_MULTISITE_ACTIVE_ON' ) ) {
    define( 'ULTIMATE_EMAIL_VALIDATOR_MULTISITE_ACTIVE_ON', 'network_admin' );
}
/**
 * Test Engine before start
 * --
 * Global variable is very important to save notices
 * because of some notices like {is_plugin_active_for_network} function
 * checks a database field {active_sitewide_plugins} which is removed after check
 * So when we check again in action {ultimate_email_validator_engine_notices} returns NULL
 *
 * @var     array|null
 * */
$ultimate_email_validator_server_test = ultimate_email_validator_engine_test();

if ( is_null( $ultimate_email_validator_server_test ) ) {
    /* 1. Set Paths Needed for Including */
    require_once ULTIMATE_EMAIL_VALIDATOR_PLUGIN_PATH . 'includes/paths.php';
    /* 2. Start The Engine */
    require_once ULTIMATE_EMAIL_VALIDATOR_PLUGIN_PATH . 'includes/engine.php';
} else {
    add_action( 'admin_notices', 'ultimate_email_validator_engine_notices', 0 );
    add_action( 'network_admin_notices', 'ultimate_email_validator_engine_notices', 0 );
    add_action( 'admin_init', 'ultimate_email_validator_engine_failed' );
}

/**
 * Check If the server and settings compatible with plugin
 *
 * Error Codes:
 *
 *
 * - 800: PHP Version is older than 5.3
 *
 * @return array|null
 *
 */
function ultimate_email_validator_engine_test()
{
    $min_ver = array(
        'php' => '5.6',
        'wp'  => '5.3.0',
    );
    $notices = array();
    if ( !function_exists( 'is_plugin_active_for_network' ) ) {
        require_once preg_replace( '/$\\//', '', ABSPATH ) . 'wp-admin/includes/plugin.php';
    }
    $network_plugin = function_exists( 'is_multisite' ) && is_multisite() && (is_plugin_active_for_network( ULTIMATE_EMAIL_VALIDATOR_PLUGIN_MAIN_FILE_PATH ) || is_network_only_plugin( ULTIMATE_EMAIL_VALIDATOR_PLUGIN_MAIN_FILE_PATH ));
    if ( $network_plugin ) {
        if ( 'defined_blog' == ULTIMATE_EMAIL_VALIDATOR_MULTISITE_ACTIVE_ON ) {
            
            if ( defined( ULTIMATE_EMAIL_VALIDATOR_ROOT_BLOG_ID ) ) {
                
                if ( is_numeric( $defined_blog_id = constant( ULTIMATE_EMAIL_VALIDATOR_ROOT_BLOG_ID ) ) ) {
                    if ( FALSE === get_blog_details( $defined_blog_id, false ) ) {
                        $notices[] = sprintf( esc_html__( '- The blog ID you entered in the constant [%s] in {wp-config.php} is not a part of your network sites.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), ULTIMATE_EMAIL_VALIDATOR_ROOT_BLOG_ID );
                    }
                } else {
                    /* NOT a Number */
                    $notices[] = sprintf( esc_html__( '- The blog ID you entered in the constant [%s] in {wp-config.php} is not a number.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), ULTIMATE_EMAIL_VALIDATOR_ROOT_BLOG_ID );
                }
            
            } else {
                /* NOT defined */
                $notices[] = sprintf( esc_html__( '- (Ultimate Email Validator) is a network only plugin and it requires a defined constant [%s] to be added in your {wp-config.php} with Blog ID.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), ULTIMATE_EMAIL_VALIDATOR_ROOT_BLOG_ID );
            }
        
        }
    }
    if ( version_compare( PHP_VERSION, $min_ver['php'], '<' ) ) {
        $notices[] = sprintf( esc_html__( '- Requires PHP Version %s (or higher) to function properly. Please upgrade or contact your administrator for help.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), $min_ver['php'] );
    }
    if ( version_compare( get_bloginfo( 'version' ), $min_ver['wp'], '<' ) ) {
        $notices[] = sprintf( esc_html__( '- Requires WordPress Version %s (or higher) to function properly. Please upgrade or contact your administrator for help.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), $min_ver['wp'] );
    }
    
    if ( !empty($notices) ) {
        $header = array( wp_kses_post( sprintf( __( '<b>(Ultimate Email Validator)</b><br/>', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) ) ) );
        $footer = array( wp_kses_post( __( '<br/><b>The Plugin has been auto-deactivated.</b>', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) ) );
        return array_merge( $header, $notices, $footer );
    }
    
    return null;
}

function ultimate_email_validator_engine_notices()
{
    global  $ultimate_email_validator_server_test ;
    $notices = $ultimate_email_validator_server_test;
    $output = '';
    if ( !is_null( $notices ) ) {
        $output = '<div class="error notice is-dismissible"><p>' . implode( '<br/>', $notices ) . '</p></div>';
    }
    if ( isset( $_GET['activate'] ) ) {
        unset( $_GET['activate'] );
    }
    echo  wp_kses_post( $output ) ;
}

function ultimate_email_validator_engine_failed()
{
    deactivate_plugins( ULTIMATE_EMAIL_VALIDATOR_PLUGIN_BASENAME );
}
