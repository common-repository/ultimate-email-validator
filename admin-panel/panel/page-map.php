<?php
namespace UltimateEmailValidator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * 
 * Prepare and create the entire map of the Settings page
 *
 * @version 1.0
 * 
 * @author Oxibug
 * 
 */
class AdminPanel_PageMap {
    
    private static $_instance = null;
    

    public static function instance() {
        
        if( is_null( self::$_instance ) ) {
            
            self::$_instance = new self;

            self::$_instance->core_actions();

        }

        return self::$_instance;

    }


    /** 
     * Silent Constructor 
     * 
     * */
    private function __construct() { }


    /**
     * Start collecting map of the page
     * 
     * @since 1.0
     * 
     */
    private function core_actions() {
        
        add_filter( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/filters/admin/page/settings/map' ), array( &$this, 'build_page_map_admin_settings' ) );
        
    }


    /**
     * Return an array of all page's map included controls array 
     * 
     * This will done through filter [ 'PLUGIN_MAIN_SLUG/filters/admin/page/settings/map' ]
     * 
     * @since 1.0
     * 
     * @param array $array REQUIRED for the apply_filters
     * 
     * @return array[]
     * 
     */
    public function build_page_map_admin_settings( $array ) {
        
        $map_elements = array(
            
            'settings' => array(
              
                'header' => array(
                
                    'title'         => esc_html__( '(Ultimate Email Validator) Plugin Settings', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
                    'description'   => esc_html__( '(Ultimate Email Validator) Plugin Decsription', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
                    'image'         => Paths::instance()->assets_path( '/admin-backend/images/banner.svg' ),
                    'type'          => 'svg', /* Keys: text | image | svg */
                ),

                'panel_style' => 'vert-tabs',
                
            ),

            'sections' => apply_filters( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/filters/admin/page/settings/map/sections' ), array(

                'defender_api_keys'       => array(
                
                    'title'     => SVGFactory::instance()->api('#fff') . esc_html__( 'Defender API Keys', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),

                    'controls'  => AdminPanelTab_Admin::instance()->tab_defender_api_keys()

                ),

                'wp_functions'       => array(
                
                    'title'     => SVGFactory::instance()->wordpress('#fff') . esc_html__( 'WordPress Functions', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),

                    'controls'  => AdminPanelTab_Admin::instance()->tab_wordpress_functions()

                ),

                'wp_forms'       => array(
                
                    'title'     => SVGFactory::instance()->wordpress('#fff') . esc_html__( 'WordPress Forms', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
                    
                    'controls'  => AdminPanelTab_Admin::instance()->tab_wordpress_forms()

                ),

                'buddypress'    => array(
                
                    'title'     => SVGFactory::instance()->buddypress('#d84800') . esc_html__( 'BuddyPress', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
                    
                    'controls'  => AdminPanelTab_Admin::instance()->tab_buddypress()

                ),

                'woocommerce'    => array(
                
                    'title'     => SVGFactory::instance()->woocommerce('#fff') . esc_html__( 'WooCommerce', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
                    
                    'controls'  => AdminPanelTab_Admin::instance()->tab_woocommerce()

                ),

                'mailchimp'    => array(
                
                    'title'     => SVGFactory::instance()->mailchimp('#fff') . esc_html__( 'MailChimp', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
                    
                    'controls'  => AdminPanelTab_Admin::instance()->tab_mailchimp()

                ),
                
                'cf7'           => array(
                
                    'title'     => SVGFactory::instance()->contact_form_7('#fff') . esc_html__( 'Contact Form 7', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
                    
                    'controls'  => AdminPanelTab_Admin::instance()->tab_contactform7()

                ),
                
                'gravity_forms'     => array(
                
                    'title'     => SVGFactory::instance()->gravity_forms('#fff') . esc_html__( 'Gravity Forms', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
                    
                    'controls'  => AdminPanelTab_Admin::instance()->tab_gravityforms()

                ),
                
                'ninja_forms'       => array(
                
                    'title'     => SVGFactory::instance()->ninja_forms('#fff') . esc_html__( 'Ninja Forms', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
                    
                    'controls'  => AdminPanelTab_Admin::instance()->tab_ninjaforms()

                ),
                           
            ) )
            
        );


        /*
         * Append [import_export] key outisde the filter
         * to be then last tab and to not changable
         * 
         * WARNING
         * 
         * [import_export] keyword! used in javascript and css
         * 
         * For special ajax button and its styles
         * 
         * */
        $map_elements['sections']['import_export'] = array(
                
            'title'     => wp_kses_post( __( '<i class="fa fa-upload" aria-hidden="true"></i>Import / Export', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) ),

            'groups'    => array(),

            'controls'  => AdminPanelTab_ImportExport::instance()->Elements()
                
        );

        return $map_elements;

    }

        
}