<?php

namespace UltimateEmailValidator;

use  Freemius_Abstract ;
/**
 * Fire Up the entire plugin after pass all activation tests
 *
 * @version 1.0
 *
 * @author Oxibug
 *
 */
class Engine
{
    /**
     * Static instance of the main plugin class
     *
     * @var Engine
     *
     * @since 1.0.0
     * @access public
     *
     */
    public static  $_instance = null ;
    /**
     * Globals Variables Object
     *
     * @var Components
     */
    public  $globals ;
    /**
     * Compare the current blog with (Ultimate Email Validator) defined blog
     * Check we are in the correct blog in admin side
     * For Single Site is always true
     *
     * @var bool
     */
    private  $is_correct_blog_in_admin = false ;
    /**
     * Compare the current blog with (Ultimate Email Validator) defined blog
     * Check we are in the correct blog in admin or frontend
     * For Single Site is always true
     *
     * @var bool
     */
    private  $is_correct_blog_in_everywhere = false ;
    /**
     * Clone.
     *
     * Disable class cloning and throw an error on object clone.
     *
     * The whole idea of the singleton design pattern is that there is a single
     * object. Therefore, we don't want the object to be cloned.
     *
     * Cloning instances of the class is forbidden.
     *
     * @since   1.0.0
     * @access  public
     */
    public function __clone()
    {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), '1.0.0' );
    }
    
    /**
     * Wakeup.
     *
     * Disable unserializing of the class.
     * Unserializing instances of the class is forbidden.
     *
     * @since   1.0.0
     * @access  public
     */
    public function __wakeup()
    {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), '1.0.0' );
    }
    
    /**
     * Start the plugin instance
     * 
     * @return  Engine
     */
    public static function start()
    {
        
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
            /**
             * The plugin has loaded.
             *
             * Fires when the entire plugin fully loaded and instantiated.
             *
             * @since 1.0
             *
             */
            do_action( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/loaded' );
        }
        
        return self::$_instance;
    }
    
    private function __construct()
    {
        /**
         * Include to use dbDelta in creating tables
         *
         * Before [ register_activation_hook ]
         *
         * */
        require_once Paths::instance()->path( 'WP_ROOT', 'wp-admin/includes/upgrade.php' );
        if ( !function_exists( 'get_filesystem_method' ) ) {
            require_once Paths::instance()->path( 'WP_ROOT', 'wp-admin/includes/file.php' );
        }
        /*
         * Includes all Classes Maps
         *
         * */
        $this->fireup_autoloader();
        /*
         * Start Engine Helper
         *
         * Activation & DeActivation Plugin functions
         *
         * */
        IgnitionHelper::instance();
        $this->is_correct_blog_in_admin = $this->correct_blog_test( TRUE );
        $this->is_correct_blog_in_everywhere = $this->correct_blog_test( FALSE );
        $this->core_actions();
    }
    
    /**
     * Fireup Autoloader Class
     *
     * Include all Classes Needed for all pages
     *
     * @since 1.0.0
     * @access private
     *
     */
    private function fireup_autoloader()
    {
        require_once Paths::instance()->path( 'INCLUDES_DIR', '/autoloader.php' );
        AutoLoader::instance();
    }
    
    /**
     * Multisite
     *
     * (Ultimate Email Validator): Required a constant to be active in Multisite
     *
     * @param   $in_admin_side
     * 
     * @return  boolean
     *
     * @since   1.0.0
     * @access  private
     */
    private function correct_blog_test( $in_admin_side = TRUE )
    {
        $network_level = is_plugin_active_for_network( ULTIMATE_EMAIL_VALIDATOR_PLUGIN_MAIN_FILE_PATH ) || is_network_only_plugin( ULTIMATE_EMAIL_VALIDATOR_PLUGIN_MAIN_FILE_PATH );
        if ( !is_multisite() || !$network_level ) {
            return ( $in_admin_side ? is_admin() : true );
        }
        switch ( ULTIMATE_EMAIL_VALIDATOR_MULTISITE_ACTIVE_ON ) {
            case 'all':
                return ( $in_admin_side ? is_admin() : true );
            case 'network_admin':
                return ( $in_admin_side ? $network_level && is_network_admin() : $network_level );
            case 'all_blogs_but_network':
                
                if ( $in_admin_side ) {
                    return ( $network_level && is_network_admin() ? false : true );
                } else {
                    return ( $network_level ? false : true );
                }
            
            case 'defined_blog':
                $current_blog = get_current_blog_id();
                /*
                 * DO NOT use quotes for this constant which
                 * is represents the constant name that must provided by
                 * developer in {wp-config.php}
                 *
                 * */
                $defined_blog = ( defined( ULTIMATE_EMAIL_VALIDATOR_ROOT_BLOG_ID ) && is_numeric( constant( ULTIMATE_EMAIL_VALIDATOR_ROOT_BLOG_ID ) ) && intval( constant( ULTIMATE_EMAIL_VALIDATOR_ROOT_BLOG_ID ) ) > 0 ? constant( ULTIMATE_EMAIL_VALIDATOR_ROOT_BLOG_ID ) : 0 );
                $main_test = (int) $current_blog === (int) $defined_blog;
                return ( $in_admin_side ? $main_test && is_admin() : $main_test );
        }
        /* ELSE */
        return false;
    }
    
    private function core_actions()
    {
        /* Filter */
        add_filter(
            'plugin_row_meta',
            array( &$this, 'append_metalinks' ),
            10,
            2
        );
        /*
         * Order: 01
         * Before {init} and contains all class instances
         * need to trigger {init} action inside
         *
         * */
        add_action( 'plugins_loaded', array( &$this, 'after_plugins_loaded' ) );
        /*
         * Order: 02
         * Before {init}
         * NOTE: The current user still not authenticated
         *
         * */
        add_action( 'after_setup_theme', array( &$this, 'after_theme_loaded' ) );
        /*
         * Order: 03
         * Set All at {init} action
         *
         * Avoid: {Notice: Trying to get property of non-object in ..\admin-panel\adminpanel-ignition.php on line 128}
         *
         * */
        add_action( 'init', array( &$this, 'init' ) );
        /*
         * Order: 04
         * REST API
         * */
        /* Order: 05
         *
         * VI: After WP objects is setu up Action: {wp}
         * - {wp_loaded} is WRONG
         * - Before {wp_enqueue_script}
         *
         * Instantiate:
         * - FrontEnd class and trigger actions inside it
         *
         * */
        add_action( 'wp', array( &$this, 'after_wp_obj_load' ) );
        /*
         * Admin ONLY actions
         * MUST NOT pass current blog test to display
         * notice in main network site
         *
         * */
        
        if ( is_multisite() ) {
            if ( $this->is_correct_blog_in_admin ) {
                add_action( 'admin_notices', array( &$this, 'view_admin_notices' ) );
            }
            add_action( 'network_admin_notices', array( &$this, 'view_network_admin_notices' ) );
        } else {
            add_action( 'admin_notices', array( &$this, 'view_admin_notices' ) );
        }
        
        /*
         * Multisite Check
         *
         * == Stop Instantiating ==
         * If we aren't in the target defined blog
         *
         * */
        if ( !$this->is_correct_blog_in_everywhere ) {
            return;
        }
        /* Modify Footer */
        add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ) );
        /* TGMPA Include Here */
    }
    
    /**
     * Triggered after {plugins_loaded} action and before {init} to be able
     * to use {init} action inside those class instances
     *
     * @since 1.0.0
     *
     *
     */
    public function after_plugins_loaded()
    {
        if ( !$this->is_correct_blog_in_everywhere ) {
            return;
        }
        /* Translations */
        $lan_dir = Paths::instance()->path( 'APP_DIR', '/languages/' );
        load_plugin_textdomain( ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN, false, $lan_dir );
        /* DB Upgrade - Future Update Tables */
        /* Shortcodes */
    }
    
    /**
     * Triggered through action {after_setup_theme}
     *
     * Suitable for
     *
     * 1. Change Admin Panel filters
     * 2. include developer's customized file with his elements
     *
     * -------------------------
     * -- Developer's Filters --
     * -------------------------
     *
     * SLUG/filters/developer/user_can/adjust_plugin
     * SLUG/filters/developer/user_can/upgrade_plugin
     * SLUG/filters/developer/cpt/item_scrt/element/content
     *
     *
     * -------------------------
     * -- Filters Order --
     * -------------------------
     *
     * 1. add_filter
     * 2. apply_filters
     *
     * So we MUST include developer's theme file then
     * instantiate local class has filters
     *
     * After:
     * -- plugins_loaded
     *
     * Before:
     * -- init
     *
     * @see https://codex.wordpress.org/Plugin_API/Action_Reference
     * @see https://codex.wordpress.org/Plugin_API/Action_Reference/after_setup_theme
     *
     * @since 1.0.0
     */
    public function after_theme_loaded()
    {
        if ( !$this->is_correct_blog_in_everywhere ) {
            return;
        }
        /*
         * VI: Apply Filters in this step
         *
         * */
        $this->globals = Components::instance();
    }
    
    /**
     * Trigger in {init} action
     *
     * @see https://codex.wordpress.org/Plugin_API/Action_Reference
     * @see https://codex.wordpress.org/Plugin_API/Action_Reference/init
     *
     * @return  void
     * @since   1.0.0
     * @access  Action: init
     */
    public function init()
    {
        /**
         * The first action in {init}
         * 
         * @param   bool    $is_correct_blog_in_admin
         * @param   bool    $is_correct_blog_in_everywhere
         * 
         * */
        do_action( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/actions/admin/init/first', $this->is_correct_blog_in_admin, $this->is_correct_blog_in_everywhere );
        /*
         * VI: Order 2 - Set Globals again
         * to avoid errors in {wp-login.php}
         *
         * after Current User authenticated
         *
         * Set Components Objects
         *
         * == Plugin Settings still NULL ==
         *
         * @todo In case we need set the current user ID in the globals
         *
         * */
        $this->globals = Components::instance();
        /*
         * Super Admin Panel
         * DO NOT Return here, JUST instantiate super admin to draw menus
         * 
         * DO NOT Use is_admin() or {is_correct_blog_in_admin} because of AJAX 
         * Classes need to instantite everywhere
         * 
         * */
        if ( $this->is_correct_blog_in_everywhere ) {
            $this->superadmin_init();
        }
        /*
         * The last instance in all sites is the SuperJocker
         *
         * It MUST instantiate over all sites and (Admin and Non-Admin) Pages
         *
         * After globals: to use constant and blog id
         *
         * FIX: for WordPress actions like
         * -- wp_login
         * -- deleted_user
         *
         * NOTE: DO NOT get saved plugin_settings inside those classes
         * Because we still do not know what the active blog is Unless is
         * Network Plugin and Network Admin
         *
         * */
        $this->instantiate_super_jocker_classes();
        $db_plugin_settings = Jocker_SuperJocker::instance()->plugin_settings;
        /**
         * The first action in {init}
         * 
         * @param   bool        $is_correct_blog_in_admin
         * @param   bool        $is_correct_blog_in_everywhere
         * @param   array|null  $db_plugin_settings             The saved or default settings of plugin settings
         * 
         * */
        do_action(
            ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/actions/admin/init/after/super_jocker',
            $this->is_correct_blog_in_admin,
            $this->is_correct_blog_in_everywhere,
            $db_plugin_settings
        );
        /* The following need Current Blog test */
        if ( !$this->is_correct_blog_in_everywhere ) {
            return;
        }
    }
    
    /**
     * Fire REST API
     *
     * MUST fired in action {rest_api_init}
     *
     * @since 1.0.0
     * @access public
     */
    public function init_rest_api()
    {
        /* In case {rest_api} did action before {init} */
        if ( !$this->globals ) {
            $this->globals = Components::instance();
        }
        /* Start REST Server to include Controllers Versions */
    }
    
    /**
     * Start Admin Panel Instances
     *
     * MUST pass Admin Pages & Current Blog Test
     *
     * @since   1.0.0
     * @access  Through action {init}
     */
    private function superadmin_init()
    {
        /**
         * Instantiate AJAX for all Pages 
         * Check {is_correct_blog_in_admin} and instantiate the extension AdminPanel manager
         * 
         * @param   bool    $is_correct_blog_in_admin
         * @param   bool    $is_correct_blog_in_everywhere
         * 
         * */
        do_action( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/actions/admin/init/superadmin', $this->is_correct_blog_in_admin, $this->is_correct_blog_in_everywhere );
        if ( $this->is_correct_blog_in_admin ) {
            /* 
             * Instantiate {admin_init} classes here like {Admin_WP_Actions}
             * Settings Panel Manager 
             * */
            AdminPanel_Manager::instance();
        }
    }
    
    /**
     * Before check Current Blog
     *
     * @since   1.0.0
     * @access  private
     */
    private function instantiate_super_jocker_classes()
    {
        Jocker_SuperJocker::instance();
    }
    
    /**
     * Instantiate in {wp} action
     *
     * @since   1.0.0
     * @access  Action: wp: After WP object is set up (ref array)
     */
    public function after_wp_obj_load()
    {
        if ( !is_admin() ) {
            /* Frontend Classes */
        }
    }
    
    /**
     * Append meta links into plugin's section in [ plugins.php ] page
     *
     * @param   array   $links
     * @param   string  $file
     *
     * @since   1.0.0
     */
    public function append_metalinks( $links, $file )
    {
        
        if ( FALSE !== strpos( $file, ULTIMATE_EMAIL_VALIDATOR_PLUGIN_MAIN_FILE_NAME ) ) {
            $appended_links = apply_filters( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/filters/admin/after_activate/ext_links', array( '<a href="' . 'https://' . 'oxibug.com/kb/ultimate-email-validator" target="_blank">' . esc_html__( 'Documentation', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) . '</a>', '<a href="' . 'https://' . 'oxibug.com/support" target="_blank">' . esc_html__( 'Submit a Ticket', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) . '</a>' ) );
            $links = array_merge( $links, $appended_links );
        }
        
        return $links;
    }
    
    /**
     * View admin notices after activate plugin for each blog in Multisite
     *
     * NOTE: delete [ ULTIMATE_EMAIL_VALIDATOR_DBKEY_ADMIN_NOTICES ] from DB after activate
     *
     * @since 1.0
     *
     */
    public function view_admin_notices()
    {
        $output = '';
        $notices = get_option( ULTIMATE_EMAIL_VALIDATOR_DBKEY_ADMIN_NOTICES, array() );
        /*
         * Add notices through filter
         * If you need to modify this array You MUST {add_filter} in {init} action
         *
         * == Array Keys ==
         *
         *  updated     => array of notices
         *  error       => array of notices
         *
         * ==================
         * */
        $new_notices = apply_filters( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/filters/admin/after_activate/notices', $notices );
        
        if ( !empty($new_notices) ) {
            foreach ( $new_notices as $status => $status_notices ) {
                if ( empty($status_notices) ) {
                    continue;
                }
                $output .= '<div class="' . $status . ' notice is-dismissible"><p>' . implode( '<br/>', $status_notices ) . '</p></div>';
            }
            echo  wp_kses_post( $output ) ;
            /* Delete Activiating notices only */
            delete_option( ULTIMATE_EMAIL_VALIDATOR_DBKEY_ADMIN_NOTICES );
        }
    
    }
    
    /**
     * View Network admin notices after activate plugin for each blog in Multisite
     *
     * NOTE: delete [ ULTIMATE_EMAIL_VALIDATOR_DBKEY_ADMIN_NOTICES ] from DB after activate
     *
     * @since 1.0
     *
     */
    public function view_network_admin_notices()
    {
        $output = '';
        $notices = get_site_option( ULTIMATE_EMAIL_VALIDATOR_DBKEY_ADMIN_NOTICES, array() );
        /*
         * Add notices through filter
         * If you need to modify this array You MUST {add_filter} in {init} action
         *
         * == Array Keys ==
         *
         *  updated     => array of notices
         *  error       => array of notices
         *
         * ==================
         * */
        $new_notices = apply_filters( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/filters/admin/after_activate/notices', $notices );
        
        if ( !empty($new_notices) ) {
            foreach ( $new_notices as $status => $status_notices ) {
                if ( empty($status_notices) ) {
                    continue;
                }
                $output .= '<div class="' . $status . ' notice is-dismissible"><p>' . implode( '<br/>', $status_notices ) . '</p></div>';
            }
            echo  wp_kses_post( $output ) ;
            delete_site_option( ULTIMATE_EMAIL_VALIDATOR_DBKEY_ADMIN_NOTICES );
        }
    
    }
    
    /**
     * Modify Footer's signature text
     * 
     * @param   string  $footer_text 
     * 
     * @return  string
     * 
     * @since   1.0.0
     * @access  public
     */
    public function admin_footer_text( $footer_text )
    {
        $plugin_pages = Admin_Components::instance()->admin_pages_ids;
        /* 
         * DO NOT use {get_current_screen} WordPress function because
         * 1. It returns a slug before the page name 
         * 2. We using $_GET in all our checks before
         * 
         * */
        if ( !isset( $_GET['page'] ) || empty($_GET['page']) ) {
            return KsesFactory::instance()->kses_more( $footer_text, array(
                'a' => true,
            ) );
        }
        $qryget_page = sanitize_text_field( $_GET['page'] );
        $wp_repository_plugin_url_rating = sprintf( 'https://wordpress.org/support/plugin/%s/reviews/?rate=5#new-post', 'ultimate-email-validator' );
        if ( in_array( $qryget_page, $plugin_pages, TRUE ) ) {
            $footer_text = sprintf( __( 'If you like <strong>Ultimate Email Validator</strong> 
                please leave us a <a href="%s" target="_blank" class="oxibug-rating-link">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating. A huge thanks in advance!', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), esc_url( $wp_repository_plugin_url_rating ) );
        }
        return KsesFactory::instance()->kses_more( $footer_text, array(
            'a' => true,
        ) );
    }

}
/*
 * Start Plugin
 *
 * */
Engine::start();