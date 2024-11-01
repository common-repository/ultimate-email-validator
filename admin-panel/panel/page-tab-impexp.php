<?php
namespace UltimateEmailValidator;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * 
 * Collect all controls of section [ general ]
 *
 * @version 1.0
 * 
 * @author Oxibug
 * 
 */
class AdminPanelTab_ImportExport {
    
    /**
     * Local instance to save the the class's object 
     * after instantiated
     * 
     * @var AdminPanelTab_ImportExport
     */
    private static $_instance = null;
    
    /**
     * Instantiate an object from class
     * 
     * @return AdminPanelTab_ImportExport
     */
    public static function instance() {
        
        if( is_null( self::$_instance ) ) {
            
            self::$_instance = new self;
            
        }

        return self::$_instance;

    }

    /**
     * Silent Constructor  
     * 
     * */
    private function __construct() { }

    /*
     * 'default_group' => 'styling',
     * 'main'          => esc_html__( 'Main', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
     * 'styling'       => esc_html__( 'Styling', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
     * 'extended'      => esc_html__( 'Extended', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
     * 
     * */

    public function Elements() {
        
        /**
         * [text_export] and [text_import] Used in JS and CSS
         * 
         * [text_export] field used to output Export text 
         * If you need to change you MUST change in [class-dc-ps-backend-factory] too.
         * 
         * */

        return array(
              
            array( 'id'     => 'text_export',
                'title'     => esc_html__('Export', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => esc_html__('Use this text to import settings in other website', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                
                'type'      => 'textarea',
                'default'   => '',
                'params'    => array(
                
                    'placeholder'   => esc_html__('No saved fields to export', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                    'locked'        => true
                
                )

            ),


            array( 'id'     => 'text_import',
                'title'     => esc_html__('Import', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => esc_html__('Paste an import text from another settings', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                

                'type'      => 'textarea',
                'default'   => '',
                'params'    => array(
                
                    'placeholder'   => esc_html__('Paste the text to export old settings', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                    'locked'        => false
                
                )

            ),


        );

    }
    
}