<?php

namespace UltimateEmailValidator;


if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly.
}

/**
 * Fire Up the entire plugin after pass all activation tests
 *
 * @version 1.0
 *
 * @author Oxibug
 *
 */
class AutoLoader
{
    public static  $_instance = null ;
    const  ALIASES_DEPRECATION_RANGE = 0.2 ;
    private  $class_map = array() ;
    private  $class_aliases = array() ;
    public static function instance()
    {
        
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
            /*
             * Collect Class Map and Fire Up
             *
             * */
            self::$_instance->core_actions();
        }
        
        return self::$_instance;
    }
    
    /**
     * Silent
     *
     */
    private function __construct()
    {
    }
    
    private function set_public_class_map()
    {
        /* MUST - Class Name => File Path */
        $includes = [
            'IgnitionHelper'               => Paths::instance()->path( 'INCLUDES_DIR', '/ignition-helper.php' ),
            'Components'                   => Paths::instance()->path( 'INCLUDES_DIR', '/components.php' ),
            'HelperFactory'                => Paths::instance()->path( 'INCLUDES_DIR', '/helper-factory.php' ),
            'KsesFactory'                  => Paths::instance()->path( 'INCLUDES_DIR', '/kses-factory.php' ),
            'SanitizationFactory'          => Paths::instance()->path( 'INCLUDES_DIR', '/sanitization-factory.php' ),
            'DateTimeFactory'              => Paths::instance()->path( 'INCLUDES_DIR', '/datetime-factory.php' ),
            'SVGFactory'                   => Paths::instance()->path( 'INCLUDES_DIR', '/svg-factory.php' ),
            'AJAX_Operations'              => Paths::instance()->path( 'INCLUDES_DIR', '/ajax-operations.php' ),
            'EmailValidator'               => Paths::instance()->path( 'INCLUDES_DIR', '/email-validator/email-validator.php' ),
            'Server_BlockTemporaryEmail'   => Paths::instance()->path( 'INCLUDES_DIR', '/email-validator/server-block-temporary-email.php' ),
            'Server_QuickEmailVeification' => Paths::instance()->path( 'INCLUDES_DIR', '/email-validator/server-quick-email-verification.php' ),
            'Filter_WP_Functions'          => Paths::instance()->path( 'INCLUDES_DIR', '/forms-validator/filter-wp-functions.php' ),
            'Form_WP_Registration'         => Paths::instance()->path( 'INCLUDES_DIR', '/forms-validator/form-wp-registration.php' ),
            'Form_WP_UserUpdateOwnProfile' => Paths::instance()->path( 'INCLUDES_DIR', '/forms-validator/form-wp-user-update-profile.php' ),
            'Form_WP_Comment'              => Paths::instance()->path( 'INCLUDES_DIR', '/forms-validator/form-wp-comment.php' ),
            'Form_XLib_BuddyPress'         => Paths::instance()->path( 'INCLUDES_DIR', '/forms-validator/form-xlib-buddypress.php' ),
            'Form_XLib_WooCommerce'        => Paths::instance()->path( 'INCLUDES_DIR', '/forms-validator/form-xlib-woocommerce.php' ),
            'Form_XLib_MailChimp'          => Paths::instance()->path( 'INCLUDES_DIR', '/forms-validator/form-xlib-mailchimp.php' ),
            'Form_XLib_CF7'                => Paths::instance()->path( 'INCLUDES_DIR', '/forms-validator/form-xlib-cf7.php' ),
            'Form_XLib_GravityForms'       => Paths::instance()->path( 'INCLUDES_DIR', '/forms-validator/form-xlib-gravityforms.php' ),
            'Form_XLib_NinjaForms'         => Paths::instance()->path( 'INCLUDES_DIR', '/forms-validator/form-xlib-ninjaforms.php' ),
            'Admin_Components'             => Paths::instance()->path( 'ADMIN_DIR', '/includes/admin-components.php' ),
            'Admin_ElementsRepeaterMapper' => Paths::instance()->path( 'ADMIN_DIR', '/elements-factory/admin-elements-repeater-mapper.php' ),
            'Admin_ElementsFactory'        => Paths::instance()->path( 'ADMIN_DIR', '/elements-factory/admin-elements-factory.php' ),
            'Admin_ElementsTemplates'      => Paths::instance()->path( 'ADMIN_DIR', '/elements-factory/admin-elements-templates.php' ),
            'AdminPanel_DBManager'         => Paths::instance()->path( 'ADMIN_PANEL_DIR', '/adminpanel-db-manager.php' ),
            'AdminPanel_DBFactory'         => Paths::instance()->path( 'ADMIN_PANEL_DIR', '/adminpanel-db-factory.php' ),
            'AdminPanel_Manager'           => Paths::instance()->path( 'ADMIN_PANEL_DIR', '/adminpanel-manager.php' ),
            'AdminPanel_BackendFactory'    => Paths::instance()->path( 'ADMIN_PANEL_DIR', '/adminpanel-backend-factory.php' ),
            'AdminPanel_DB_Save'           => Paths::instance()->path( 'ADMIN_PANEL_DIR', '/adminpanel-db-save.php' ),
            'AdminPanel_PageMap'           => Paths::instance()->path( 'ADMIN_PANEL_DIR', '/panel/page-map.php' ),
            'AdminPanelTab_Admin'          => Paths::instance()->path( 'ADMIN_PANEL_DIR', '/panel/page-tab-admin.php' ),
            'AdminPanelTab_ImportExport'   => Paths::instance()->path( 'ADMIN_PANEL_DIR', '/panel/page-tab-impexp.php' ),
            'Jocker_SuperJocker'           => Paths::instance()->path( 'JOCKER_DIR', '/super-jocker.php' ),
        ];
        return $includes;
    }
    
    private function set_admin_class_map()
    {
        return array();
    }
    
    private function set_frontend_class_map()
    {
        /* MUST - Class Name => File Path */
        return array();
    }
    
    /**
     * Run autoloader by PHP function {spl_autoload_register}
     *
     * Register a function as `__autoload()` implementation.
     *
     * @since 1.0.0
     *
     * @access private
     *
     */
    private function core_actions()
    {
        self::$_instance->class_map = self::$_instance->set_public_class_map();
        
        if ( is_admin() ) {
            /* Admin Pages Classes */
            $temp_classmap = self::$_instance->class_map;
            $admin_temp_classmap = self::$_instance->set_admin_class_map();
            self::$_instance->class_map = array_merge( $temp_classmap, $admin_temp_classmap );
        } else {
            /* Frontend Classes */
            $temp_classmap = self::$_instance->class_map;
            $frontend_temp_classmap = self::$_instance->set_frontend_class_map();
            self::$_instance->class_map = array_merge( $temp_classmap, $frontend_temp_classmap );
        }
        
        spl_autoload_register( array( &$this, 'autoload' ) );
    }
    
    /**
     * Load class.
     *
     * For a given class name, require the class file.
     *
     * @since 1.0.0
     * @access private
     * @static
     *
     * @param string $relative_class_name Class name.
     */
    private function load_class( $relative_class_name )
    {
        
        if ( array_key_exists( $relative_class_name, self::$_instance->class_map ) ) {
            $filename = self::$_instance->class_map[$relative_class_name];
        } else {
            $filename = strtolower( preg_replace( array( '/([a-z])([A-Z])/', '/_/', '/\\\\/' ), array( '$1-$2', '-', DIRECTORY_SEPARATOR ), $relative_class_name ) );
            $filename = ULTIMATE_EMAIL_VALIDATOR_PLUGIN_PATH . $filename . '.php';
        }
        
        if ( is_readable( $filename ) ) {
            require $filename;
        }
    }
    
    /**
     * Autoload.
     *
     * For a given class, check if it exist and load it.
     *
     * @since 1.0.0
     * @access public
     *
     * @param string $class Class name.
     *
     */
    public function autoload( $class )
    {
        if ( 0 !== strpos( $class, __NAMESPACE__ . '\\' ) ) {
            return;
        }
        $relative_class_name = preg_replace( '/^' . __NAMESPACE__ . '\\\\/', '', $class );
        /* Class has Aliases */
        $alias_data = null;
        $has_class_alias = array_key_exists( $relative_class_name, self::$_instance->class_aliases );
        // Backward Compatibility: Save old class name for set an alias after the new class is loaded
        
        if ( $has_class_alias ) {
            $alias_data = self::$_instance->class_aliases[$relative_class_name];
            $relative_class_name = $alias_data['replacement'];
        }
        
        $final_class_name = __NAMESPACE__ . '\\' . $relative_class_name;
        if ( !class_exists( $final_class_name ) ) {
            self::$_instance->load_class( $relative_class_name );
        }
        
        if ( $has_class_alias ) {
            class_alias( $final_class_name, $class );
            preg_match( '/^[0-9]+\\.[0-9]+/', ULTIMATE_EMAIL_VALIDATOR_VERSION, $current_version_as_float );
            $current_version_as_float = (double) $current_version_as_float[0];
            preg_match( '/^[0-9]+\\.[0-9]+/', $alias_data['version'], $alias_version_as_float );
            $alias_version_as_float = (double) $alias_version_as_float[0];
            if ( $current_version_as_float - $alias_version_as_float >= self::ALIASES_DEPRECATION_RANGE ) {
                _deprecated_file( $class, $alias_data['version'], $final_class_name );
            }
        }
    
    }

}