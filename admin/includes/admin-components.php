<?php
namespace UltimateEmailValidator;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Admin Panel Manager short summary.
 *
 * @author  Oxibug 
 * @version 1.0.0
 */
class Admin_Components {

    /**
     * An instance of the class
     *
     * @since 1.0
     *
     * @var Admin_Components
     *
     */
    private static $_instance = null;


    /**
     * The main components globals
     *
     * @var     Components
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public $loc_globals;

    /**
     * The admin notices to show to the clients
     *
     * @var     array
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public $notices;
    

    /**
     * The seller's server URL
     *
     * @var     string|array
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public $server_url;

    /**
     * The seller's server URL with REST API
     *
     * @var     string|array
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public $server_rest_url;

    /**
     * Admin Pages IDs
     *
     * @var     array
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public $admin_pages_ids;
    

    /**
     * Admin Pages Database Keys
     *
     * @var     array
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public $admin_pages_db_option_keys;
    
    /**
     * Admin CPT Pages IDs that appear in 
     * query string {page} and used to determine
     * our pages
     *
     * @var     array
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public $admin_cpt_pages_ids;


    /**
     * Admin Page ID - Parent - The Main Slug
     *
     * @var     string
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public $apid_parent;


    /**
     * Admin Page ID - Page Settings
     *
     * NOTE: Used in CSS and JS
     * 
     * @var     string
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public $apid_settings;

    /**
     * Admin Page Options Group - Page Settings
     *
     * @var     string
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public $apog_settings;


    /**
     * Admin Page - Page URL
     *
     * @var     string
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public $apurl_settings;
    
    /**
     * Admin Page ID - Page Licenses
     *
     * NOTE: Used in CSS and JS
     * 
     * @var     string
     * @since   1.0.0
     * @access  public
     *
     */
    public $apid_client_plugin;


    /**
     * Admin Page Options Group - Page Licenses
     *
     * @var     string
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public $apog_client_plugin;


    /**
     * Admin Page (Client Plugin) - Page URL
     *
     * @var     string
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public $apurl_client_plugin;


    /**
     * Very Important
     * 
     * Admin Page Custom Post Type - Type => Title key value array 
     *
     * Types (Keys) are used in Function names, Action Slugs JS and Other
     * 
     * @var     array
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public $apcpt_types;
    
    /**
     * Very Important
     * 
     * An array of {actions} used in the Query strings for 
     * Admin Page Custom Post Type
     * 
     * edit | duplicate | delete | view
     * 
     * @var     array
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public $apcpt_actions = array(
        'view',
        'edit',
        'duplicate',
        'delete'
    );

    /**
     * Very Important
     * 
     * An array of {actions} used in the Query strings for 
     * Admin Page Custom Post Type
     * 
     * edit | duplicate | delete | view
     * 
     * @var     array
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public $apcpt_actions_need_pkid = array(
        'view',
        'edit',
        'duplicate',
        'delete'
    );
    
    /**
     * Very Important
     * 
     * An array of all {actions} NOT for from query strings
     * BUT to create nonces
     * 
     * add | edit | duplicate | delete | view | query
     * 
     * @var     array
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public $apcpt_actions_nonces = array(
        'add',
        'edit',
        'duplicate',
        'delete',
        'view',
        'query'
    );

    /**
     * Very Important
     * 
     * An array of arrays to add multiple taxonomies
     * 
     * - Array Keys
     * 
     *  id              (string) | Unique taxonomy id - Used with function {register_taxonomy}
     *  object_type     (string) | the object type like the custom post type but we can use without create a CPT
     *  args            (array) | The args used in {register_taxonomy} function
     *
     *  init_term       (null | array) | An array to be used with function {wp_insert_term} 
     *  to check if this term is exist and add a new one
     *  array(
     *      name        | the term name
     *      args        | The args of {wp_insert_term} function
     *  )
     *  
     * @var     array
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public $apcpt_taxonomy;
    
    /**
     * Very Important
     * 
     * A key-value pair array
     * 
     * {item_cat} Taxonomy Key for private use => Array(
     * 
     *      cpt_id          | The ID of CPT for this taxonomy
     *      cpt_title       | the CPT title
     *      tax_name        | The registered taxonomy ID
     *      tax_objtype     | The fake object type or the CPT type
     *      
     *      unremovable_terms | array(
     *      -    uncategorized
     *      )
     *      
     *      default_term    | (String) | The name for the default term [uncategorized]
     *
     *      wp_args         | array of all WordPress needed args and {labels}
     * )
     * 
     * @var     array
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public $apcpt_taxonomy_ids;

    /**
     * Admin Page Custom Post Type - Page Parent 
     *
     * id           | Full Page ID - {_APID_SLUG_PAGE_SLUG}
     * type         | Type is very important for switch-case - example - {seller} | {item} ... etc
     * page_title   | The page title
     * menu_title   | Menu title
     * group        | The group of page elements
     * buttons      | an array of {submit} {search} - the name of form submitting buttons
     * url          | Managed by {add_query_args} and need to {esc_url_raw} before write it
     * 
     * nonces       | array ( 'add', 'view', 'edit', 'delete' ) keys
     * 
     * labels       | An array of all labels just like WordPress labels args
     * 
     * @var     array
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public $apcpt_parent;

    /**
     * Admin Page Custom Post Type - Page Seller
     *
     * id           | Full Page ID - {_APID_SLUG_PAGE_SLUG}
     * type         | Type is very important for switch-case - example - {seller} | {item} ... etc
     * page_title   | The page title
     * menu_title   | Menu title
     * group        | The group of page elements
     * buttons      | an array of {submit} {search} - the name of form submitting buttons
     * url          | Managed by {add_query_args} and need to {esc_url_raw} before write it
     * 
     * nonces       | array ( 'add', 'view', 'edit', 'delete' ) keys
     * 
     * labels       | An array of all labels just like WordPress labels args
     * 
     * @var     array
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public $apcpt_seller;

    /**
     * Admin Page Custom Post Type - Page Item
     *
     * id           | Full Page ID - {_APID_SLUG_PAGE_SLUG}
     * type         | Type is very important for switch-case - example - {seller} | {item} ... etc
     * page_title   | The page title
     * menu_title   | Menu title
     * group        | The group of page elements
     * buttons      | an array of {submit} {search} - the name of form submitting buttons
     * url          | Managed by {add_query_args} and need to {esc_url_raw} before write it
     * 
     * nonces       | array ( 'add', 'view', 'edit', 'delete' ) keys
     * 
     * labels       | An array of all labels just like WordPress labels args
     * 
     * @var     array
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public $apcpt_item;


    /**
     * Admin Page Custom Post Type - Page Item Categories
     *
     * id           | Full Page ID - {_APID_SLUG_PAGE_SLUG}
     * type         | Type is very important for switch-case - example - {seller} | {item} ... etc
     * page_title   | The page title
     * menu_title   | Menu title
     * group        | The group of page elements
     * buttons      | an array of {submit} {search} - the name of form submitting buttons
     * url          | Managed by {add_query_args} and need to {esc_url_raw} before write it
     * 
     * nonces       | array ( 'add', 'view', 'edit', 'delete' ) keys
     * 
     * labels       | An array of all labels just like WordPress labels args
     * 
     * @var     array
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public $apcpt_tax_itemcat;
    
    /**
     * Admin Page Custom Post Type - Page Item Secret Content
     *
     * id           | Full Page ID - {_APID_SLUG_PAGE_SLUG}
     * type         | Type is very important for switch-case - example - {seller} | {item} ... etc
     * page_title   | The page title
     * menu_title   | Menu title
     * group        | The group of page elements
     * buttons      | an array of {submit} {search} - the name of form submitting buttons
     * url          | Managed by {add_query_args} and need to {esc_url_raw} before write it
     * 
     * nonces       | array ( 'add', 'view', 'edit', 'delete' ) keys
     * 
     * labels       | An array of all labels just like WordPress labels args
     * 
     * @var     array
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public $apcpt_item_scrt;
    
    /**
     * Admin Page Custom Post Type - Page User
     *
     * id           | Full Page ID - {_APID_SLUG_PAGE_SLUG}
     * type         | Type is very important for switch-case - example - {seller} | {item} ... etc
     * page_title   | The page title
     * menu_title   | Menu title
     * group        | The group of page elements
     * url          | Managed by {add_query_args} and need to {esc_url_raw} before write it
     * buttons      | an array of {submit} {search} - the name of form submitting buttons
     * 
     * nonces       | array ( 'add', 'view', 'edit', 'delete' ) keys
     * 
     * labels       | An array of all labels just like WordPress labels args
     * 
     * @var     array
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public $apcpt_user;
    
    /**
     * Admin Page Custom Post Type - Page License
     *
     * id           | Full Page ID - {_APID_SLUG_PAGE_SLUG}
     * type         | Type is very important for switch-case - example - {seller} | {item} ... etc
     * page_title   | The page title
     * menu_title   | Menu title
     * group        | The group of page elements
     * url          | Managed by {add_query_args} and need to {esc_url_raw} before write it
     * buttons      | an array of {submit} {search} - the name of form submitting buttons
     * 
     * nonces       | array ( 'add', 'view', 'edit', 'delete' ) keys
     * 
     * labels       | An array of all labels just like WordPress labels args
     * 
     * @var     array
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public $apcpt_license;


    /**
     * Capability for option pages
     * 
     * @var     string
     * 
     * @since   1.0.0
     * @access  public
     */
    public $apcap_panel;
    
    
    /**
     * Capability for CPT pages
     * 
     * menu     | Who can see Data in dashboard menu
     * add      | Ability to    ADD     new CPT
     * 
     * edit | duplicate     | Ability to    EDIT | DUPLICATE   CPT
     * 
     * delete   | Ability to    DELETE  CPT
     * view     | Ability to    VIEW    CPT
     * query    | Ability to    QUERY   CPT
     * 
     * @var     array
     * 
     * @since   1.0.0
     * @access  public
     */
    public $apcap_cpt;

    
    /**
     * Instantiate in WordPress action [ init ] in [ class-pasclient-superadmin.php ]
     *
     * NOTE: is_admin() applied in PAS_Client_Super_Admin class
     *
     * @since 1.0
     *
     * @return Admin_Components
     *
     */
    public static function instance() {

        if( is_null( self::$_instance ) ) {

            self::$_instance = new self;

            self::$_instance->loc_globals = Components::instance();


            self::$_instance->set_admin_pages_ids();

        }

        return self::$_instance;

    }


    /**
     * Get the Admin Page ID ONLY
     * 
     * Example: {ULTIMATE_EMAIL_VALIDATOR_APID_SLUG}_$page
     * 
     * @param   string          $page           | settings | clientplugin
     * @param   array           $query_args     | Any other query args
     * 
     * @return  string
     * 
     * @since   1.0.0
     * @access  public
     * 
     */
    public function admin_page_get_id( $page = '' ) {
        
        return sprintf( '%s_%s', ULTIMATE_EMAIL_VALIDATOR_APID_SLUG, $page );

    }


    /**
     * Collect the full Admin Page URL by page, 
     * 
     * @param   string          $page           | settings | clientplugin
     * @param   array           $query_args     | Any other query args
     * 
     * @return  string
     * 
     * @since   1.0.0
     * @access  public
     * 
     */
    public function admin_page_get_full_url( $page, $query_args = '') {
        
        $page_id = self::$_instance->admin_page_get_id( $page );

        $default_args = array(
            'page'    => $page_id,
        );
        
        $query_args = wp_parse_args( $query_args, $default_args );

        return self::$_instance->get_admin_php_url( $query_args );

    }


    private function set_admin_pages_ids() {
                
        self::$_instance->apcap_panel = ( ( self::$_instance->loc_globals->is_multisite && self::$_instance->loc_globals->is_network_plugin ) ) ? 'manage_network_options' : 'manage_options';
        
        /* Page - Settings */
        self::$_instance->apid_parent = self::$_instance->admin_page_get_id( 'settings' );
        
        self::$_instance->apid_settings = self::$_instance->admin_page_get_id( 'settings' );
        self::$_instance->apog_settings = self::$_instance->apid_settings . '_group';
        
        self::$_instance->apurl_settings = self::$_instance->admin_page_get_full_url( 'settings' );
        

        /* Page - Plugin Client */
        self::$_instance->apid_client_plugin = self::$_instance->admin_page_get_id( 'clientplugin' );
        self::$_instance->apog_client_plugin = self::$_instance->apid_client_plugin . '_group';
        self::$_instance->apurl_client_plugin = self::$_instance->admin_page_get_full_url( 'clientplugin' );
        
        /*
         * == Last Operation ==
         * 
         * Collect all Pages IDs in an array
         * 
         * */
        self::$_instance->admin_pages_ids = apply_filters( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/filters/admin/pages/ids' ), array(
          
            self::$_instance->apid_settings,
            self::$_instance->apid_client_plugin,
            
        ) );

        
        self::$_instance->admin_pages_db_option_keys = apply_filters( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/filters/admin/pages/db_option_keys' ), array(
            
            'plugin_settings'   => ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_db_admin_settings',
            'pg_client_plugin'  => ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_db_pg_clientplugin_settings'

        ) );


    }


    /**
     * Return the CPT page ID by type
     * All CPT pages types in varaible {Admin_Components::apcpt_types}
     * -
     * The final format: ULTIMATE_EMAIL_VALIDATOR_APID_SLUG . '_cpt_' . $type
     * 
     * @param   string $type - [seller] [item] [item_scrt] [user] [license]
     * @return  string
     * 
     * @since   1.0.0
     * @access  public
     * 
     */
    public function get_cpt_page_id( $type ) {
        return ULTIMATE_EMAIL_VALIDATOR_APID_SLUG . '_cpt_' . $type;
    }
    

    /**
     * Return the {admin.php} full url with additional query args
     * 
     * NOTE: The url in this stage not escaped
     * 
     * @param   array|null $query_args 
     * 
     * @return  string
     */
    public function get_admin_php_url( $query_args = null ) {
        
        $my_globals = self::$_instance->loc_globals;

        if( ! $my_globals ) {
            $my_globals = Components::instance();
        }

        if( $my_globals->mu_use_network_admin_url ) {
            
            if( ! $query_args ) {
                return network_admin_url( '/admin.php' );
            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            return add_query_arg( $query_args, network_admin_url( '/admin.php' ) );

        }
        else {

            if( ! $query_args ) {
                return admin_url( '/admin.php' );
            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            return add_query_arg( $query_args, admin_url( '/admin.php' ) );

        }

    }


    public function cpt_get_action_url( $action, $query_args ) {
        
        if( ! $action ) {
            return self::$_instance->get_admin_php_url();
        }
        
        $default_args = array();

        /* Order {page} as a first arg */
        if( array_key_exists('page', $query_args) ) {
            $default_args['page'] = $query_args['page'];

            unset( $query_args['page'] );
        }

        $default_args['action'] = $action;
                
        $query_args = wp_parse_args( $query_args, $default_args );

        return self::$_instance->get_admin_php_url( $query_args );

    }
    
    /**
     * Return QUERY action full URL with
     * -
     * 1. page
     * 2. action
     * 2. search_by
     * 3. search_by_val
     * -
     * in Query String to use it in DB query functions
     * 
     * @param   string  $cpt_type 
     * @param   string  $search_by 
     * @param   string  $search_value 
     * 
     * @return string
     */
    public function cpt_get_query_action_url( $cpt_type, $search_by, $search_value ) {
                
        $page_id = self::$_instance->get_cpt_page_id( $cpt_type );

        $args = array(
            'page'          => $page_id,
            'query_by'      => $search_by,
            'query_value'   => $search_value
        );

        return self::$_instance->cpt_get_action_url( 'query', $args );
        
    }

    /**
     * Collect the full CPT URL by type, action and additional query_args
     * 
     * @param   string          $cpt_type       | seller | item | item_scrt | user | license
     * @param   string|null     $action         | view | edit | delete 
     * @param   array           $query_args     | Any other query args
     * 
     * @return  string
     * 
     * @since   1.0.0
     * @access  public
     * 
     */
    public function cpt_get_full_url( $cpt_type, $action = null, $query_args ) {
        
        $page_id = self::$_instance->get_cpt_page_id( $cpt_type );

        $default_args = array(
            'page'    => $page_id,
        );
        
        if( $action ) {
            $default_args['action'] = $action;
        }

        $query_args = wp_parse_args( $query_args, $default_args );

        return self::$_instance->get_admin_php_url( $query_args );

    }


    /**
     * Check $_GET {action} query string and return
     * 
     * 1. Not Set: return {add}
     * 2. Otherwise, Check the {apcpt_actions} for avalable actions
     * 3. Return Null
     * 
     * @return \null|string
     */
    public function cpt_get_current_page_action() {
        
        if( ! isset( $_GET['action'] ) ) {
            return 'add';
        }

        if( isset( $_GET['action'] ) && in_array( $_GET['action'], self::$_instance->apcpt_actions ) ) {
            return sanitize_text_field( $_GET['action'] );
        }

        return null;
    }
    

    /**
     * Return the Shortcode ID according to parameters
     * -
     * The final format: ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_page' . $type {Example: _APID_SLUG_PAGE_page}
     * 
     * @param   string  $id         The shortcode ID without slug to be .. _APID_SLUG_PAGE_{page}
     * 
     * @return  string
     * 
     * @since   1.0.0
     * @access  public
     */
    public function get_shortcode_id( $id ) {
        return sprintf( '%s_%s', ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN, $id );
    }

        

    /**
     * Check and Return option from plugin settings
     * -----
     * VI NOTE: For Checkbox Elements
     *  - You MUST Cast the result because it sometimes return true while it false
     * 
     * @param mixed $plugin_settings
     * @param mixed $tab
     * @param mixed $option
     * @param mixed $default_if_not_exist
     * 
     * @return mixed
     * 
     */
    public function settings_get_option_value( $plugin_settings = null, $tab = '', $option = '', $default_if_not_exist = null ) {

        if( ! $plugin_settings ) {
            return $default_if_not_exist;
        }

        if( array_key_exists( $tab, $plugin_settings ) && 
            array_key_exists( $option, $plugin_settings[ $tab ] ) ) {

            return $plugin_settings[ $tab ][ $option ];

        }

        return $default_if_not_exist;

    }


    /**
     * Options Panel: Check {tab} and {field} value
     * 
     * @param   null|array   $plugin_settings
     * @param   string  $tab
     * @param   string  $option
     *
     * @return  string|null
     *
     * @since   1.0.0
     * @access  public
     */
    public function settings_get_userpage_url( $plugin_settings, $tab, $option ) {

        if( is_null( $option_value = self::$_instance->settings_get_option_value( $plugin_settings, $tab, $option, null ) ) ) {
            return null;
        }

        if( is_numeric( $option_value ) && 
            ( intval( $option_value ) > 0 ) &&
            ( ! is_null( $obj_page = get_post( $plugin_settings[ $tab ][ $option ] ) ) ) ) {
            
            if( $obj_page instanceof \WP_Post ) {
                return get_permalink( $obj_page );
            }

            return null;

        }

        return null;

    }


    /**
     * Return Login/Register pages in array with Keys
     * 
     *  -   login
     *  -   register
     * 
     * @param   array   $plugin_settings 
     * 
     * @return  array
     * 
     * @since   1.0.0
     * @access  public
     */
    public function get_backend_pages_urls( $plugin_settings = null ) {
        
        $scheme = is_ssl();

        $url_login  = wp_login_url();
        $url_reg    = site_url( '/wp-login.php?action=register', $scheme );

        $db_sw_enable_custom_backend    = self::$_instance->settings_get_option_value( $plugin_settings, 'admin', 'sw_enable_custom_backend_pages', false );
        $db_sw_enable_custom_backend    = HelperFactory::instance()->cast_bool( $db_sw_enable_custom_backend );

        if( FALSE === $db_sw_enable_custom_backend ) {
            
            return array(
                'login'     => $url_login,
                'register'  => $url_reg
            );

        }


        $db_login_slug                  = self::$_instance->settings_get_option_value( $plugin_settings, 'admin', 'txt_login_slug', null );
        $db_register_slug               = self::$_instance->settings_get_option_value( $plugin_settings, 'admin', 'txt_register_slug', null );

        if( ! is_null( $db_login_slug ) && ! empty( $db_login_slug ) ) {
            /* @See {wp_login_url} */
            $url_login      = site_url( $db_login_slug, 'login' );
        }
        
        if( ! is_null( $db_register_slug ) && ! empty( $db_register_slug ) ) {
            $url_reg    = site_url( $db_register_slug );
        }

        return array(
        
            'login'     => $url_login,
            'register'  => $url_reg

        );

    }


    /**
     * Return the structure of Admin Panel {Element Name}
     *
     * VI NOTES: 
     * 1. You can use $group_id for tabs -- SLUG[Tab_ID][Element_ID]
     * 2. In [admin-backend] -> js files there're local functions to
     * collect elements' names and IDs and the structure MUST be like this
     * 
     * - - - - - 
     * Example
     * - - - - -
     *
     * Element: METAS_SLUG [element_id]
     *
     * Repeater: METAS_SLUG [element_id] [1] [control_id_inside_repeater]
     *
     * @param string $element_id
     *
     * @param integer $additional_index
     *
     * @param string $additional_id
     * 
     * @param string $group_id
     *
     * @return string
     *
     * @since 1.0
     *
     */
    public function element_name( $element_id, $additional_index = -1, $additional_id = null, $group_id = null ) {

        $element_slug = ULTIMATE_EMAIL_VALIDATOR_ADMIN_PAGE_CON_PREFIX;

        $use_group = false;

        if( ( ! is_null( $group_id ) ) && ( ! empty( $group_id ) ) ) {
            $use_group = true;
        }

        if( ( intval( $additional_index ) > 0 ) && ( ! is_null( $additional_id ) ) ) {

            if( $use_group ) {
                /* Repeater Grouped */
                return sprintf( '%1$s[%2$s][%3$s][%4$d][%5$s]', $element_slug, $group_id, $element_id, $additional_index, $additional_id );
            }
            else {
                /* Repeater */
                return sprintf( '%1$s[%2$s][%3$d][%4$s]', $element_slug, $element_id, $additional_index, $additional_id );
            }
        }


        if( $use_group ) {
            return sprintf( '%1$s[%2$s][%3$s]', $element_slug, sanitize_text_field( $group_id ), sanitize_text_field( $element_id ) );
        }


        return sprintf( '%1$s[%2$s]', $element_slug, sanitize_text_field( $element_id ) );

    }


    /**
     * Return the structure of Page Meta {element_id}
     *
     * Example
     * - - - - -
     *
     * Element: {SLUG_} .....
     *
     * Fold:    {fold_} {SLUG_} .....
     *
     *
     * @param   string $start_with
     * @param   string $element_id
     * @param   string $end_with
     *
     * @return  string
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public function element_id( $start_with = '', $element_id, $end_with = '', $group_id = null ) {

        $final_id = array(
            ULTIMATE_EMAIL_VALIDATOR_ADMIN_PAGE_CON_PREFIX
        );

        if( ( ! is_null( $group_id ) ) && ( ! empty( $group_id ) ) ) {
            $final_id[] = sanitize_text_field( $group_id );
        }

        if( ! empty( $start_with ) ) {
            $final_id[] = sanitize_text_field( $start_with );
        }

        if( ! empty( $element_id ) ) {
            $final_id[] = sanitize_text_field( $element_id );
        }

        if( ! empty( $end_with ) ) {
            $final_id[] = sanitize_text_field( $end_with );
        }

        return join( '_', $final_id );

    }


    public function draw_required_hidden_fields( $args = '' ) {
        
        $args_defaults = array(
            
            /* The page ID - Exactly like used in {admin_menu} function while creating it */
            'page'          => null,    
            /* panel | cpt */
            'page_type'     => 'panel',
            /* Array */
            'map'           => null,
            /* Array */
            'defaults'      => null,

            'additional'    => null
        );

        $args = wp_parse_args( $args, $args_defaults );
        
        $hidden_elements = array(
            array(
                'id'    => self::$_instance->element_id( '', 'page', '', '__oxibug__' ),
                'name'  => self::$_instance->element_name( 'page', -1, null, '__oxibug__' ),
                'value' => $args['page']
            ),
            array(
                'id'    => self::$_instance->element_id( '', 'page_type', '', '__oxibug__' ),
                'name'  => self::$_instance->element_name( 'page_type', -1, null, '__oxibug__' ),
                'value' => $args['page_type']
            )
        );

        if( ! is_null( $args['defaults'] ) ) {            
            $hidden_elements[] = array(
                'id'    => self::$_instance->element_id( '', 'defaults', '', '__oxibug__' ),
                'name'  => self::$_instance->element_name( 'defaults', -1, null, '__oxibug__' ),
                'value' => HelperFactory::instance()->maybe_base64_encode( $args['defaults'] )
            );            
        }


        if( ! is_null( $args['map'] ) ) {            
            $hidden_elements[] = array(
                'id'    => self::$_instance->element_id( '', 'elements', '', '__oxibug__' ),
                'name'  => self::$_instance->element_name( 'elements', -1, null, '__oxibug__' ),
                'value' => HelperFactory::instance()->maybe_base64_encode( $args['map'] )
            );
        }
        
        if( is_array( $args['additional'] ) ) {
          
            foreach( $args['additional'] as $id => $options ) {
                
                $value = $options['value'];

                if( array_key_exists( 'convert_to_base64', $options ) && ( TRUE === filter_var( $options['convert_to_base64'], FILTER_VALIDATE_BOOLEAN ) ) ) {
                    $value = HelperFactory::instance()->maybe_base64_encode( $options['value'] );
                }

                $hidden_elements[] = array(
                    'id'    => self::$_instance->element_id( '', $options['id'], '', '__oxibug__' ),
                    'name'  => self::$_instance->element_name( $options['id'], -1, null, '__oxibug__' ),
                    'value' => $value
                );

            }
            
        } ?>
        
        <div class="hdn-globals hidden">

            <?php $form_nonce_name = self::$_instance->element_name( '_security_nonce', -1, null, '__oxibug__' );
            wp_nonce_field( ULTIMATE_EMAIL_VALIDATOR_AJAX_NONCE_ACTION, $form_nonce_name, true, true );
            
            /* Custom Hidden Fields */
            foreach( $hidden_elements as $el_hdn ) { ?>
                <input type="hidden" 
                       id="<?php echo sanitize_text_field( $el_hdn['id'] ); ?>" 
                       name="<?php echo sanitize_text_field( $el_hdn['name'] ); ?>"
                       value="<?php echo esc_html( $el_hdn['value'] ); ?>" />
            <?php } ?>

        </div>

    <?php }



    private function _check_element_valid( $element ) {
        if( ! $element ) {
            return false;
        }

        if( ( ! array_key_exists('id', $element) ) || 
            ( ! array_key_exists( 'type', $element ) ) || 
            ( ! ( $element['id'] ) ) ||
            ( ! ( $element['type'] ) ) ) {
            
            return false;

        }

        return true;
    }


    private function _get_multiple_elements_control_map( $repeater_elements ) {
        
        $elements_in_rep = array();

        foreach( $repeater_elements as $rep_element ) {

            if( ! self::$_instance->_check_element_valid( $rep_element ) ) {
                continue;
            }

            $elements_in_rep[] = array(
                'type'          => $rep_element['type'],
                'id'            => $rep_element['id']
            );

        } /* Repeater Elements */

        return (array)$elements_in_rep;


    }

    public function get_elements_map( $elements_map, $has_sections = false ) {
        
        $final_elements = array();


        if( ! $has_sections ) {
            
            foreach( $elements_map as $element ) {

                if( ! self::$_instance->_check_element_valid( $element ) ) {
                    continue;
                }

                switch( $element['type'] ) {

                    case 'label': {
                            /* We need elements we could save in DB */
                            continue 2;
                        }

                    default: {

                            $args = array(
                                'type'          => $element['type'],
                                'id'            => $element['id'],
                                'title'         => isset( $element['title'] ) ? $element['title'] : ''
                            );


                            if( array_key_exists( 'params', $element ) &&
                                array_key_exists( 'required', $element['params'] ) && ( TRUE === $element['params']['required'] ) && 
                                array_key_exists( 'required_condition', $element['params'] ) &&
                                array_key_exists( 'value', $element['params']['required_condition'] ) &&
                                array_key_exists( 'compare', $element['params']['required_condition'] ) ) {
                                
                                $compare_operator = HelperFactory::instance()->check_compare_operator( $element['params']['required_condition']['compare'] );

                                $args['required'] = true;
                                $args['required_condition'] = array(
                                    'value'     => $element['params']['required_condition']['value'],
                                    'compare'   => $compare_operator,
                                );

                            }
                            

                            $final_elements[] = $args;

                        } break;

                    case 'repeater': {

                            if( array_key_exists( 'controls', $element ) &&
                                is_array( $element['controls'] ) ) {

                                $final_elements[] = array(
                                    'type'          => $element['type'],
                                    'id'            => $element['id'],
                                    'title'         => isset( $element['title'] ) ? $element['title'] : '',
                                    'elements'      => self::$_instance->_get_multiple_elements_control_map( $element['controls'] )
                                );

                            }

                        } break;
                          
                          
                    case 'group': {

                        if( array_key_exists( 'controls', $element ) &&
                            is_array( $element['controls'] ) ) {

                            $final_elements[] = array(
                                'type'          => $element['type'],
                                'id'            => $element['id'],
                                'title'         => isset( $element['title'] ) ? $element['title'] : '',
                                'elements'      => self::$_instance->_get_multiple_elements_control_map( $element['controls'] )
                            );

                        }

                    } break;

                }

            } /* Main Elements */

        }
        else {

            if( ! array_key_exists( 'sections', $elements_map ) ) {
                return null;
            }

            foreach( $elements_map['sections'] as $tab_key => $tab_elements ) {

                if( ! array_key_exists( 'controls', $tab_elements ) || ! is_array( $tab_elements['controls'] ) ) {
                    continue;
                }

                foreach( $tab_elements['controls'] as $element ) {

                    if( ! self::$_instance->_check_element_valid( $element ) ) {
                        continue;
                    }

                    switch( $element['type'] ) {

                        case 'label': {
                                /* We need elements we could save in DB */
                                continue 2;
                            }

                        default: {

                                $args = array(
                                    'type'          => $element['type'],
                                    'id'            => $element['id'],
                                    'title'         => isset( $element['title'] ) ? $element['title'] : '',
                                );

                                if( array_key_exists( 'params', $element ) &&
                                array_key_exists( 'required', $element['params'] ) && ( TRUE === $element['params']['required'] ) && 
                                array_key_exists( 'required_condition', $element['params'] ) &&
                                array_key_exists( 'value', $element['params']['required_condition'] ) &&
                                array_key_exists( 'compare', $element['params']['required_condition'] ) ) {
                                
                                    $compare_operator = HelperFactory::instance()->check_compare_operator( $element['params']['required_condition']['compare'] );

                                    $args['required'] = true;
                                    $args['required_condition'] = array(
                                        'value'     => $element['params']['required_condition']['value'],
                                        'compare'   => $compare_operator,
                                    );

                                }
                                

                                $final_elements[ $tab_key ][] = $args;

                            } break;

                        case 'repeater': {
                                
                                if( array_key_exists( 'controls', $element ) && 
                                    is_array( $element['controls'] ) ) {
                                    
                                    $final_elements[ $tab_key ][] = array(
                                        'type'          => $element['type'],
                                        'id'            => $element['id'],
                                        'title'         => isset( $element['title'] ) ? $element['title'] : '',
                                        'elements'      => self::$_instance->_get_multiple_elements_control_map( $element['controls'] )
                                    );

                                } /* Check Repeater */

                            } break;


                        case 'group': {

                            if( array_key_exists( 'controls', $element ) &&
                                is_array( $element['controls'] ) ) {

                                $final_elements[] = array(
                                    'type'          => $element['type'],
                                    'id'            => $element['id'],
                                    'title'         => isset( $element['title'] ) ? $element['title'] : '',
                                    'elements'      => self::$_instance->_get_multiple_elements_control_map( $element['controls'] )
                                );

                            }

                        } break;

                    }
                    
                } /* Main Elements */

            }

        }


        if( count( $final_elements ) > 0 ) {
            return (array)$final_elements;
        }

        return null;

    }


    
    /**
     * Get all available sections which only have controls
     *
     * @since 1.0
     *
     * @param array $map_array Represents the entire page array passed throug [ add_settings_field ] function
     *
     * @return array
     *
     */
    public function get_map_sections_and_titles( $map_array ) {

        $output = array();

        if( ( is_array( $map_array['sections'] ) ) && ( count( $map_array['sections'] ) > 0 ) ) {

            foreach ( $map_array['sections']  as $key => $value) {

                if( ( is_array( $value ) ) && array_key_exists( 'title', $value )
                    && ( array_key_exists( 'controls', $value ) ) && ( is_array( $value['controls'] ) ) && ( count( $value['controls'] ) > 0 ) ) {


                    $output[ $key ] = wp_kses( $value['title'], $this->tab_title_allowed_html );
                    

                }

            }

        }

        return $output;

    }


    /**
     * Set the last save action notices in DB using transient operation
     * 
     * @param   array|null  $notices 
     * @param   string      $status     Accept [ success | fail ] to save in a separate fields in DB
     * 
     * @return  void
     * 
     * @since   1.0.0
     * @access  public
     * 
     */
    public function set_save_notices( $notices = null, $status = 'success' ) {
        
        if( is_null( $notices ) ) {
            return;
        }
        
        $dbkey = '';

        if( 'success' === $status ) {
            $dbkey = ULTIMATE_EMAIL_VALIDATOR_DBKEY_ADMIN_SAVE_PAGE_SUCCESS;
        }
        else {
            $dbkey = ULTIMATE_EMAIL_VALIDATOR_DBKEY_ADMIN_SAVE_PAGE_FAIL;
        }
        
        AdminPanel_DBManager::instance()->update_transient( array(

            //'admin_only'            => false,   /* If TRUE: We MUST check {net_plugin_net_admin} - It's Very aggressive check becuase it'll determine if we can access this option in Frontend or not */
            //'net_plugin_net_admin'  => null,    /*  */
            
            'db_option'             => $dbkey,
            'value'                 => $notices,

            'exp'                   => MINUTE_IN_SECONDS
        ) );

    }


    /**
     * Display last save action notices and delete the transient
     * 
     * Using function {draw_note_html} 
     * which using our {KsesFactory} class to allow print {a} 
     * With some additional attributes {href} and {download} to use in special cases
     * 
     * DO NOT Use {wp_kses_post}
     * 
     * @return  void
     * 
     * @since   1.0.0
     * @access  public
     * 
     */
    public function display_last_save_notices() {
        
        $notices_to_display = array();

        $db_keys = array(
            'success'   => ULTIMATE_EMAIL_VALIDATOR_DBKEY_ADMIN_SAVE_PAGE_SUCCESS,
            'fail'      => ULTIMATE_EMAIL_VALIDATOR_DBKEY_ADMIN_SAVE_PAGE_FAIL
        );
        
        foreach( $db_keys as $status_key => $db_key ) {

            if( is_null( $notices = AdminPanel_DBManager::instance()->get_transient( array(

                //'admin_only'                => false,   /* If TRUE: the option will be saved in {wp_site_meta} so it's NOT suitable for options used by {Frontend Users} */
                //'network_admin_only'        => false,   /* It means that the function will check {is_network_admin <-Means-> (wp-admin/network) page} and {network_plugin}, Otherwise will check {network_plugin} only */
                //'net_plugin_net_admin'      => null,    /* Added to fix check [is_network_admin] through AJAX .. It always failed and return false */

                'db_option'                 => $db_key,
                'delete_after_get'          => true

            ) ) ) ) {
                
                continue;

            }
            
            // $notices_to_display[] = $db_key;

            $status = 'error';
            if( 'success' === $status_key ) {
                $status = 'updated';
            }


            foreach( $notices as $code => $msg ) {

                $notices_to_display[] = array(
                    
                    'code'      => $code,
                    'msg'       => $msg,
                    'status'    => $status
                    
                );

            }
            
        }
        

        if( count( $notices_to_display ) <= 0 ) {
            return;
        }
        
        /* Echo Notices */
        foreach( $notices_to_display as $notice ) {
                
            self::$_instance->draw_note_html( $notice['code'], $notice['msg'], $notice['status'] );

        }

    }


    /**
     * Echo custom messages to solve some cases cannot get Database transient field
     * Because the function triggered so early
     * 
     * Using function {draw_note_html} 
     * which using our {KsesFactory} class to allow print {a} 
     * With some additional attributes {href} and {download} to use in special cases
     * 
     * DO NOT Use {wp_kses_post}
     * 
     * @param   array   $notices    A code => msg array
     * @param   string  $status     { fail | success }
     * 
     * @return  void
     * 
     * @since   1.0.0
     * @access  public
     * 
     */
    public function echo_notices( $notices = array(), $user_status = '' ) {
        
        $notices_to_display = array();
        
        $status = 'error';
        if( 'success' === (string) $user_status ) {
            $status = 'updated';
        }
        
        foreach( $notices as $code => $msg ) {

            $notices_to_display[] = array(
                
                'code'      => $code,
                'msg'       => $msg,
                'status'    => $status
                
            );

        }

        if( count( $notices_to_display ) <= 0 ) {
            return;
        }
        
        /* Echo Notices */
        foreach( $notices_to_display as $notice ) {
            
            self::$_instance->draw_note_html( $notice['code'], $notice['msg'], $notice['status'] );

        }

    }


    /**
     * Pass message with status {error | success} to appear
     * 
     * @param   int|string  $code 
     * @param   string      $message 
     * @param   string      $status     error | success
     * 
     * @since   1.0.0
     * @access  public
     */
    public function draw_note_html( $code, $message, $status ) {
        
        $cls = array(
            'oxibug-notice',
            'notice',
            $status
        );

        $cls[] = sprintf( 'note-code-%s', $code ); ?>

        <div class="<?php echo join( ' ', $cls ); ?>">
            <p>
                <strong><?php echo KsesFactory::instance()->kses_more( $message, array(
                    'a'     => true
                ) ); ?></strong>
            </p>
        </div>

    <?php }


    public function die_with_message( $message = null ) {
        
        $cls = array(
            'died-notice',
        ); ?>

        <div class="<?php echo join( ' ', $cls ); ?>">
            <p>
                <strong><?php echo KsesFactory::instance()->kses_more( $message, array(
                    'a'     => true
                ) ); ?></strong>
            </p>
        </div>

    <?php wp_die();
    
    }



    /**
     * Check required user inputs against the required values in elements map
     * 
     * @param   array $user_inputs
     * @param   array $elements_map
     * 
     * @return  \array|null
     * 
     * @since   1.0.0
     * @access  public
     * 
     */
    public function check_required_fields( $user_inputs, $elements_map, $grouped_with_tabs = TRUE ) {
        
        $final = array();

        if( $grouped_with_tabs ) {

            foreach( $elements_map as $tab_key => $tab_elements ) {
                
                if( ! array_key_exists( $tab_key, $user_inputs ) ) {
                    continue;
                }
                
                foreach( $tab_elements as $ele ) {
                    
                    /* 
                     * This is enough because we did an aggressive check
                     * while collecting {required} and {required_condition}
                     * while calling {elements_map} function
                     * 
                     * */
                    if( ! array_key_exists('required_condition', $ele) ) {
                        continue;
                    }

                    $live_value = isset( $user_inputs[ $tab_key ][ $ele['id'] ] ) ? $user_inputs[ $tab_key ][ $ele['id'] ] : null;

                    $required_value = $ele['required_condition']['value'];
                    $compare_operator = $ele['required_condition']['compare'];


                    if( in_array( $ele['type'], array( 'text', 'textarea', 'select' ) ) ) {
                        
                        if( ! HelperFactory::instance()->compare_required_value( $live_value, $required_value, $compare_operator ) ) {
                            
                            $final[] = $user_inputs[ $tab_key ][ $ele['title'] ];

                            continue;
                        }

                    }
                    

                }

            }
            
        }
        else {

            /* The user inputs do not have tabs */
            foreach( $elements_map as $ele ) {
                
                /* 
                 * This is enough because we did an aggressive check
                 * while collecting {required} and {required_condition}
                 * while calling {elements_map} function
                 * 
                 * */
                if( ! array_key_exists('required_condition', $ele) ) {
                    continue;
                }

                $live_value = isset( $user_inputs[ $ele['id'] ] ) ? $user_inputs[ $ele['id'] ] : null;

                $required_value = $ele['required_condition']['value'];
                $compare_operator = $ele['required_condition']['compare'];


                if( in_array( $ele['type'], array( 'text', 'textarea', 'select' ) ) ) {
                    
                    if( ! HelperFactory::instance()->compare_required_value( $live_value, $required_value, $compare_operator ) ) {
                        
                        $final[] = $ele['title'];

                        continue;
                    }

                }

                
            }

        }


        if( count( $final ) > 0 ) {
            return (array) $final;
        }

        return null;

    }


    
    public function get_user_purchases_meta_key( $seller_id, $buyer_id ) {

        return sprintf('seller-%d-buyer-%d', $seller_id, $buyer_id );

    }



}