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
class Form_XLib_GravityForms {

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


    /**
     * Take in instance of the class
     *
     * @return Form_XLib_GravityForms
     */
    public static function instance() {

        if( is_null( self::$_instance ) ) {

            self::$_instance = new self;

            self::$_instance->plugin_settings = Jocker_SuperJocker::instance()->plugin_settings;

            if( TRUE === HelperFactory::instance()->cast_bool( self::$_instance->plugin_settings['gravity_forms']['sw_gravityforms_validate_email'] ) ) {

                self::$_instance->user_error_message = self::$_instance->plugin_settings['gravity_forms']['txt_gravityforms_form_message'];

                self::$_instance->core_actions();

            }
        }

        return self::$_instance;

    }


    /*
     * Silent Constructor
     *
     * */
    public function __construct() { }

    /**
     * Trigger the appropriate action
     *
     * @since   1.0.0
     * @access  public
     */
    private function core_actions() {

        add_filter( 'gform_field_validation',     array( $this, 'validate' ), 10, 4 );

    }



    /**
     * Validate Email Address in function {is_email}
     *
     * @param   array           $result
     * @param   string|array    $value
     * @param   \stdClass    $form
     * @param   \stdClass    $field
     *
     * @see     https://docs.gravityforms.com/gform_field_validation/
     *
     * @since   1.0.0
     * @access  public
     */
    public function validate( $result, $value, $form, $field ) {

        /**
         * Check is already valid in the Gravity Forms Field
         * Then trigger our additional operations and override the result
         * 
         * */
        if( $field->get_input_type() === 'email' && $result['is_valid'] ) {

            $user_email = $value;

            /*
             * is_email is already done
             *
             * */
            $is_valid_email = EmailValidator::instance()->is_valid_email( $user_email );


            if ( is_wp_error( $is_valid_email ) ) {

                $error_msg      = $is_valid_email->get_error_message();

                $result['is_valid'] = false;
                /**
                 * Response Error
                 * */
                $result['message'] = esc_html( $error_msg );

            }
            elseif( FALSE === $is_valid_email ) {

                $result['is_valid'] = false;
                $result['message'] = wp_kses_post( self::$_instance->user_error_message );

            }

        }

        return $result;

    }

}