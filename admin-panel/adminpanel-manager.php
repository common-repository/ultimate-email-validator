<?php

namespace UltimateEmailValidator;


if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly.
}

/**
 * Triggered through action {init} but after check {is_admin} page
 * 
 * Page Settings Manager
 *
 * @version 1.0.0
 * @author  Oxibug
 */
class AdminPanel_Manager
{
    /**
     * An instance of the class
     * 
     * @since 1.0
     * 
     * @var AdminPanel_Manager
     * 
     */
    private static  $_instance = null ;
    /**
     * Main globals
     * 
     * @var Components
     * 
     */
    private  $loc_globals ;
    /**
     * All Admin Pages globals
     * 
     * @var Admin_Components
     * 
     */
    private  $ap_globals ;
    /**
     * We in our admin pages
     * 
     * @var boolean
     */
    private  $is_plugin_admin_page ;
    /**
     * Current page
     * 
     * @var string
     */
    private  $current_admin_page ;
    /**
     * capability
     * Permission name of user who can use admin page
     * 
     * @var mixed
     */
    private  $capability ;
    /**
     * Instantiate in WordPress action [ init ] in [ class-pas-superadmin.php ]
     * 
     * NOTE: is_admin() applied in Super_Admin class
     * 
     * @since 1.0
     * 
     * @return AdminPanel_Manager
     * 
     */
    public static function instance()
    {
        
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
            self::$_instance->ap_globals = Admin_Components::instance();
            self::$_instance->loc_globals = self::$_instance->ap_globals->loc_globals;
            self::$_instance->is_plugin_admin_page = self::$_instance->check_qryarg_page( $ref_current_page );
            self::$_instance->current_admin_page = $ref_current_page;
            if ( self::$_instance->is_plugin_admin_page ) {
                if ( $ref_current_page === self::$_instance->ap_globals->apid_settings ) {
                    /* AJAX Instance */
                    AdminPanel_DB_Save::instance();
                }
            }
            self::$_instance->capability = self::$_instance->ap_globals->apcap_panel;
            self::$_instance->addMenuPageHooks();
        }
        
        return self::$_instance;
    }
    
    /** 
     * Silent Constructor  
     * 
     * */
    private function __construct()
    {
    }
    
    private function check_qryarg_page( &$ref_page = null )
    {
        if ( !isset( $_GET['page'] ) ) {
            return false;
        }
        $page = sanitize_text_field( $_GET['page'] );
        
        if ( in_array( $page, self::$_instance->ap_globals->admin_pages_ids ) ) {
            $ref_page = $page;
            return true;
        }
        
        return false;
    }
    
    /**
     * Start hooking admin menus function for both Network and Signle site admin
     * 
     * @since 1.0
     * 
     */
    private function addMenuPageHooks()
    {
        /*
         * Check Current user capability
         * 
         * */
        if ( !current_user_can( self::$_instance->capability ) ) {
            return;
        }
        add_action( 'admin_menu', array( &$this, '_settings_menu' ) );
        add_action( 'network_admin_menu', array( &$this, '_settings_menu' ) );
        /*
         * Check after {admin_menu} action
         * 
         * */
        if ( !self::$_instance->is_plugin_admin_page ) {
            return;
        }
        /*
         * This is our page
         * Draw the current page elements
         * */
        AdminPanel_BackendFactory::instance( $_GET['page'] );
    }
    
    /**
     * Draw admin menu
     * 
     * @since 1.0
     * 
     */
    public function _settings_menu()
    {
        $parent_page_id = self::$_instance->ap_globals->apid_settings;
        $toplevel_menu = add_menu_page(
            ULTIMATE_EMAIL_VALIDATOR_ADMIN_PAGE_TITLE,
            ULTIMATE_EMAIL_VALIDATOR_ADMIN_MENU_TITLE,
            self::$_instance->capability,
            $parent_page_id,
            array( &$this, 'main_settings_page_html' ),
            'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAyMS4wLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHdpZHRoPSI5NnB4IiBoZWlnaHQ9Ijk2cHgiIHZpZXdCb3g9IjAgMCA5NiA5NiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgOTYgOTY7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+DQoJLnN0MHtkaXNwbGF5Om5vbmU7ZmlsbDojRjJGMkYyO30NCgkuc3Qxe2ZpbGw6IzdDQjM0Mjt9DQoJLnN0MntmaWxsOiNGRkZGRkY7fQ0KCS5zdDN7ZmlsbDojRjJGMkYyO30NCgkuc3Q0e2ZpbGw6bm9uZTtzdHJva2U6I0NDQ0NDQztzdHJva2Utd2lkdGg6MC41O3N0cm9rZS1taXRlcmxpbWl0OjEwO30NCgkuc3Q1e2ZpbGw6IzRENEQ0RDt9DQo8L3N0eWxlPg0KPGc+DQoJPHBhdGggZD0iTTkxLDQ4QzkxLDI0LjMsNzEuNyw1LDQ4LDVDMjQuMyw1LDUsMjQuMyw1LDQ4YzAsMjMuNywxOS4zLDQzLDQzLDQzYzMuNiwwLDcuMS0wLjQsMTAuNS0xLjNjLTEuMy0wLjctMi40LTEuOC0yLjktMy4zDQoJCWMtMC42LTEuNS0wLjUtMy4xLDAuMi00LjZjMC4xLTAuMSwwLjEtMC4yLDAuMi0wLjRjLTIuNiwwLjYtNS4yLDAuOS04LDAuOUMyOSw4Mi40LDEzLjYsNjcsMTMuNiw0OEMxMy42LDI5LDI5LDEzLjYsNDgsMTMuNg0KCQlTODIuNCwyOSw4Mi40LDQ4YzAsNy45LTIuNywxNS4zLTcuMiwyMS4xbC05LjktOS45YzIuMS0zLjIsMy4zLTcuMSwzLjMtMTEuMmMwLTQtMS4xLTcuNy0zLjEtMTAuOWwtNS44LDguMUM1OS45LDQ2LjEsNjAsNDcsNjAsNDgNCgkJYzAsNi42LTUuNCwxMi0xMiwxMmMtNi42LDAtMTItNS40LTEyLTEyYzAtNi42LDUuNC0xMiwxMi0xMmM0LDAsNy42LDIsOS44LDUuMWw2LTYuMmMtMy44LTQuNS05LjUtNy40LTE1LjgtNy40DQoJCWMtMTEuNCwwLTIwLjYsOS4zLTIwLjYsMjAuNmMwLDExLjQsOS4zLDIwLjYsMjAuNiwyMC42YzQuMSwwLDgtMS4yLDExLjItMy4zbDkuOSw5LjlsMCwwbDMuMiwzLjJoMGwwLjEsMC4xbDAsMA0KCQljMS43LDEuNSw0LjMsMS41LDUuOS0wLjFjMC4xLTAuMSwwLjItMC4yLDAuMy0wLjNDODYuMyw3MC4zLDkxLDU5LjcsOTEsNDh6Ii8+DQoJPGc+DQoJCTxwYXRoIGNsYXNzPSJzdDEiIGQ9Ik02MS4yLDgwLjFjLTAuNSwwLTAuOSwwLjEtMS40LDAuMmMwLDAtMC4xLDAtMC4xLDBjMCwwLDAsMC0wLjEsMGMtMi4yLDAuOC0zLjMsMy4zLTIuNSw1LjUNCgkJCWMwLjYsMS43LDIuMywyLjgsNCwyLjhjMC41LDAsMS0wLjEsMS41LTAuM2MyLjItMC44LDMuNC0zLjMsMi42LTUuNUM2NC42LDgxLjEsNjMsODAuMSw2MS4yLDgwLjF6Ii8+DQoJCTxwYXRoIGNsYXNzPSJzdDEiIGQ9Ik02Ny44LDI3LjljLTAuMywwLTAuNiwwLjItMC43LDAuNEw0OCw0OGwtNC4yLTVjLTAuMy0wLjQtMC44LTAuNy0xLjQtMC43Yy0xLDAtMS43LDAuOC0xLjcsMS43DQoJCQljMCwwLjMsMC4xLDAuNSwwLjIsMC43bDQuOCw5LjdjMC40LDAuOSwxLjMsMS41LDIuMywxLjVjMC45LDAsMS42LTAuNCwyLjEtMS4xbDE4LjQtMjUuN2MwLjEtMC4xLDAuMS0wLjMsMC4xLTAuNA0KCQkJQzY4LjYsMjguMyw2OC4yLDI3LjksNjcuOCwyNy45eiIvPg0KCTwvZz4NCjwvZz4NCjwvc3ZnPg0K',
            90
        );
        $submenu_toplevel = add_submenu_page(
            $parent_page_id,
            ULTIMATE_EMAIL_VALIDATOR_ADMIN_PAGE_TITLE,
            esc_html__( 'Settings', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
            self::$_instance->capability,
            $parent_page_id,
            array( &$this, 'main_settings_page_html' )
        );
        add_action( "load-{$toplevel_menu}", array( &$this, '_menuTopLevel' ) );
        add_action( "load-{$submenu_toplevel}", array( &$this, '_menuTopLevel' ) );
        /** 
         * For Extensions Settings 
         * 
         * @param   string  $parent_page_id     The parent menu ID to add submenus underneath it
         * @param   string  $toplevel_menu      Use with action {load-TOPLEVEL_MENU_ID} to include CSS & Scripts
         * 
         * */
        do_action( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/actions/admin/admin_menu', $parent_page_id, $toplevel_menu );
    }
    
    /**
     * Enqueue styles & scripts for main panel page
     * 
     * @since 1.0
     * 
     */
    public function _menuTopLevel()
    {
        $this->init_scripts();
    }
    
    /**
     * Enqueue styles & scripts for Documentation panel page
     * 
     * @since 1.0
     * 
     */
    public function _client_init_load()
    {
        // Include The current page PHP files
        $this->init_scripts();
    }
    
    /**
     * Load Styles and Scripts used in Plugin's settings admin side
     * 
     * @since 1.0
     * 
     */
    public function init_scripts()
    {
        $ajax_url = self::$_instance->loc_globals->url_ajax;
        $paths = self::$_instance->loc_globals->paths_abs;
        $script_ids = self::$_instance->loc_globals->script_ids;
        /* Font Awesome v4.7 */
        wp_enqueue_style(
            ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/styles/vectoricons/fa',
            sprintf( '%s/vector-icons/font-awesome/font-awesome.css', $paths['assets']['addons'] ),
            array(),
            ULTIMATE_EMAIL_VALIDATOR_JSCSS_VERSION,
            'all'
        );
        /* Font Awesome v4.7 */
        wp_enqueue_style(
            ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/styles/bs-core',
            sprintf( '%s/thecore/grid.min.css', $paths['assets']['addons'] ),
            array(),
            ULTIMATE_EMAIL_VALIDATOR_JSCSS_VERSION,
            'all'
        );
        /* Animated */
        wp_enqueue_style(
            ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/styles/xlib/animate',
            sprintf( '%s/animate.min.css', $paths['assets']['addons'] ),
            array(),
            ULTIMATE_EMAIL_VALIDATOR_JSCSS_VERSION,
            'all'
        );
        /* Select2 and it Themes */
        wp_enqueue_style(
            ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/styles/xlib/select2',
            sprintf( '%s/select2/select2.min.css', $paths['assets']['addons'] ),
            array(),
            ULTIMATE_EMAIL_VALIDATOR_JSCSS_VERSION,
            'all'
        );
        wp_enqueue_style(
            ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/styles/xlib/select2/theme/bs4',
            sprintf( '%s/select2/select2-bootstrap4.min.css', $paths['assets']['addons'] ),
            array( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/styles/xlib/select2' ),
            ULTIMATE_EMAIL_VALIDATOR_JSCSS_VERSION,
            'all'
        );
        /* QTip - For Repeaters */
        wp_enqueue_style(
            ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/styles/xlib/qtip',
            sprintf( '%s/qtip/qtip.min.css', $paths['assets']['addons'] ),
            array(),
            ULTIMATE_EMAIL_VALIDATOR_JSCSS_VERSION,
            'all'
        );
        /* Bootstrap Color Picker */
        wp_enqueue_style(
            ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/styles/xlib/bs-colorpicker',
            sprintf( '%s/bs-colorpicker/bs-colorpicker.min.css', $paths['assets']['addons'] ),
            array(),
            ULTIMATE_EMAIL_VALIDATOR_JSCSS_VERSION,
            'all'
        );
        wp_enqueue_style(
            ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/styles/admin/core',
            sprintf( '%s/css/core.css', $paths['assets']['admin-backend'] ),
            array(),
            ULTIMATE_EMAIL_VALIDATOR_JSCSS_VERSION,
            'all'
        );
        wp_enqueue_style(
            ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/styles/admin/options-panel',
            sprintf( '%s/css/options-panel.css', $paths['assets']['admin-backend'] ),
            array( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/styles/admin/core' ),
            ULTIMATE_EMAIL_VALIDATOR_JSCSS_VERSION,
            'all'
        );
        // Underscore.js
        if ( !wp_script_is( 'underscore', 'enqueued' ) ) {
            wp_enqueue_script( 'underscore' );
        }
        if ( !wp_script_is( 'wp-util', 'enqueued' ) ) {
            wp_enqueue_script( 'wp-util' );
        }
        if ( !wp_script_is( 'jquery-ui-core' ) ) {
            wp_enqueue_script( 'jquery-ui-core' );
        }
        if ( !wp_script_is( 'jquery-ui-sortable' ) ) {
            wp_enqueue_script( 'jquery-ui-sortable' );
        }
        if ( !wp_script_is( 'jquery-ui-spinner' ) ) {
            wp_enqueue_script( 'jquery-ui-spinner' );
        }
        if ( !wp_script_is( 'jquery-ui-slider' ) ) {
            wp_enqueue_script( 'jquery-ui-slider' );
        }
        if ( function_exists( 'wp_enqueue_media' ) ) {
            wp_enqueue_media();
        }
        /* All External Libraries Scripts Merged */
        wp_enqueue_script(
            $script_ids['general']['addons'],
            sprintf( '%s/xlib-scripts.min.js', $paths['assets']['addons'] ),
            array( 'jquery' ),
            ULTIMATE_EMAIL_VALIDATOR_JSCSS_VERSION,
            true
        );
        /**
         * 
         * Enqueue the main javascript file [ admin-backend/js/core.js ]
         * 
         * */
        wp_register_script(
            $script_ids['admin']['core'],
            sprintf( '%s/js/core.js', $paths['assets']['admin-backend'] ),
            array( $script_ids['general']['addons'] ),
            ULTIMATE_EMAIL_VALIDATOR_JSCSS_VERSION,
            true
        );
        wp_localize_script( $script_ids['admin']['core'], ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_ajax', array(
            'ajax_url'        => esc_url( $ajax_url ),
            'global_security' => wp_create_nonce( ULTIMATE_EMAIL_VALIDATOR_AJAX_NONCE_ACTION ),
            'is_rtl'          => ( is_rtl() ? true : false ),
            'admin'           => array(
            'is_network_plugin'           => self::$_instance->loc_globals->is_network_plugin,
            'is_network_plugin_only'      => self::$_instance->loc_globals->is_network_plugin_only,
            'is_network_plugin_and_admin' => self::$_instance->loc_globals->is_network_plugin_and_admin,
            'admin_page_ele_prefix'       => ULTIMATE_EMAIL_VALIDATOR_ADMIN_PAGE_CON_PREFIX,
            'action_prefix'               => ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN,
            'action_result'               => array(
            'save'      => array(
            'success' => esc_html__( 'Settings Saved!', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
            'fail'    => esc_html__( 'Saving Failed!', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
        ),
            'restore'   => array(
            'success' => esc_html__( 'Settings Restored!', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
            'fail'    => esc_html__( 'Restoring Failed!', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
        ),
            'importing' => array(
            'success' => esc_html__( 'Settings Imported!', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
            'fail'    => esc_html__( 'Importing Failed!', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
        ),
        ),
        ),
            'elements'        => array(
            'media' => array(
            'single' => array(
            'header_title' => esc_html__( 'Upload Media File', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
        ),
        ),
        ),
            'confirm'         => array(
            'delete_item' => esc_html__( 'Are you sure you want to delete this item?', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
        ),
            'errors'          => array(
            'code_100' => esc_html__( 'Security Error! Reload the page and try again.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
            'code_200' => esc_html__( 'Fetal Error: Please, Check your internet connection or Reload the page and try again', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
        ),
        ) );
        wp_enqueue_script( $script_ids['admin']['core'] );
        wp_enqueue_script(
            $script_ids['admin']['options-panel'],
            sprintf( '%s/js/options-panel.js', $paths['assets']['admin-backend'] ),
            array( $script_ids['admin']['core'] ),
            ULTIMATE_EMAIL_VALIDATOR_JSCSS_VERSION,
            true
        );
    }
    
    /**
     * Start drawing the main form and listen on controls that will be
     * 
     * Create in the backend factory [ adminpanel-backend-factory.php ]
     * 
     * @since 1.0
     * 
     * @return void
     * 
     */
    public function main_settings_page_html()
    {
        // check user capabilities
        if ( !current_user_can( self::$_instance->capability ) ) {
            return;
        }
        ?>


        <div class="wrap css-oxibug-uev-main-wrp page-settings clearfix">
            
            <?php 
        Admin_Components::instance()->display_last_save_notices();
        ?>


            <h1><?php 
        echo  esc_html( get_admin_page_title() ) ;
        ?></h1>
            
            <form method="post" action="<?php 
        echo  esc_url( self::$_instance->ap_globals->apurl_settings ) ;
        ?>" class="form-wrp">
            
                <div class="srpset-plugin-settings-loading hidden">
                    <i class="srpset-loading absolute large spin"></i>
                </div>

                <div class="srpset-plugin-settings-result hidden">
                    <div class="result-inner">
                        
                        <div class="sec-inner sec-icon">
                            <i class="srpset-trigger-icon"></i>
                        </div>

                        <div class="sec-inner sec-text">
                            <p></p>
                        </div>

                    </div>
                </div>

            <?php 
        /* 
         * output security fields for the registered setting "wporg_options" 
         * 
         * Located in [ sections/class-pas-section-general.php ]
         * 
         * */
        settings_fields( self::$_instance->ap_globals->apog_settings );
        /*
         * output setting sections and their fields
         * 
         * (sections are registered for "srpset-options", each field is registered to a specific section)
         * 
         * */
        do_settings_sections( self::$_instance->ap_globals->apid_settings );
        $buttons_names = array(
            'reset'  => Admin_Components::instance()->element_name(
            'reset',
            -1,
            null,
            '__oxibug__'
        ),
            'submit' => Admin_Components::instance()->element_name(
            'submit',
            -1,
            null,
            '__oxibug__'
        ),
        );
        ?>


                <div class="form-element-wrp page-actions">
                    
                    <p class="reset-settings">
                        <?php 
        submit_button(
            esc_html__( 'Reset Settings', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
            'button-warning',
            $buttons_names['reset'],
            false,
            null
        );
        ?>
                    </p>

                    <p class="save-settings">
                        <?php 
        submit_button(
            esc_html__( 'Save Settings', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
            'button-success',
            $buttons_names['submit'],
            false,
            null
        );
        ?>
                    </p>

                </div>

            </form>

        </div>

    <?php 
    }

}