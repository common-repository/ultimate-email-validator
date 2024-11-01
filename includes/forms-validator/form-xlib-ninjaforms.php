<?php
namespace UltimateEmailValidator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Form - Contact Form 7
 *
 * @author  Oxibug
 * @version 1.0.0
 */
class Form_XLib_NinjaForms {

    private static $_instance = null;


    /**
     * The Plugin Settings Saved in DB, Otherwise the default values
     * @var array|null
     */
    public $plugin_settings = null;

    /**
     * The User's Custom Error Message
     *
     * @var string
     */
    private $user_error_message = '';



    /*
     * Silent Constructor
     *
     * */
    public function __construct() { }


    /**
     * Take in instance of the class
     *
     * @return Form_XLib_NinjaForms
     */
    public static function instance() {

        if( is_null( self::$_instance ) ) {

            self::$_instance = new self;


            self::$_instance->plugin_settings = Jocker_SuperJocker::instance()->plugin_settings;


            if( TRUE === HelperFactory::instance()->cast_bool( self::$_instance->plugin_settings['ninja_forms']['sw_ninjaforms_validate_email'] ) ) {

                self::$_instance->user_error_message = self::$_instance->plugin_settings['ninja_forms']['txt_ninjaforms_form_message'];

                self::$_instance->core_actions();

            }


        }

        return self::$_instance;

    }


    /**
     * Trigger the appropriate action
     *
     * @since   1.0.0
     * @access  public
     */
    private function core_actions() {

        add_filter( 'ninja_forms_submit_data',     array( $this, 'validate' ) );

    }



    /**
     * Validate Email Address in function {is_email}
     *
     * @param   array    $field_settings
     *
     * @see     https://developer.ninjaforms.com/codex/custom-server-side-validation/
     * @see     http://developer.ninjaforms.com/codex/
     * @see     https://developer.ninjaforms.com/codex/submission-processing-hooks/
     *
     * @since   1.0.0
     * @access  public
     */
    public function validate( $form_data ) {
        
        if( empty( $form_data ) ) {
            return $form_data;
        }

        $errors = array(
            'errors'    => array(
                'fields'    => array()
            )
        );

        foreach( $form_data['fields'] as $field ) {

            if( ( ! isset( $field['value'] ) ) || 
                ( ! filter_var( $field['value'], FILTER_VALIDATE_EMAIL ) ) ) {

                continue;

            }

            $field_id   = $field['id'];
            $user_email = $field['value'];


            $is_valid_email = EmailValidator::instance()->is_valid_email( $user_email );


            if ( is_wp_error( $is_valid_email ) ) {

                $error_msg      = $is_valid_email->get_error_message();

                $errors['errors']['fields'][ $field_id ] = $error_msg;

            }
            elseif( FALSE === $is_valid_email ) {

                $errors['errors']['fields'][ $field_id ] = self::$_instance->user_error_message;
                
            }

        }

        /* Print out errors */
        if( ! empty( $errors['errors']['fields'] ) ) {
            $form_data['errors'] = $errors['errors'];            
        }

        return $form_data;

    }

}