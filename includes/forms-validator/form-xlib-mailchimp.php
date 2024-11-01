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
class Form_XLib_MailChimp {

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
     * @return Form_XLib_MailChimp
     */
    public static function instance() {

        if( is_null( self::$_instance ) ) {

            self::$_instance = new self;


            self::$_instance->plugin_settings = Jocker_SuperJocker::instance()->plugin_settings;


            if( TRUE === HelperFactory::instance()->cast_bool( self::$_instance->plugin_settings['mailchimp']['sw_mc4wp_validate_email'] ) ) {

                self::$_instance->user_error_message = self::$_instance->plugin_settings['mailchimp']['txt_mc4wp_form_message'];

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

        /**
         * First Step: Add Our Custom Messages
         * */
        add_filter( 'mc4wp_form_messages',      array( $this, 'custom_messages' ), 10, 2 );


        add_filter( 'mc4wp_form_errors',        array( $this, 'validate' ), 10, 2 );

    }



    /**
     * Validate Email Address in function {is_email}
     *
     * @param   array       $messages
     * @param   \MC4WP_Form $form
     *
     * @see     Function: {load_messages} in Class MC4WP_Form
     *
     * @since   1.0.0
     * @access  public
     */
    public function custom_messages( $messages, $form ) {

        return array_merge( $messages, array(

            ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_BTE_API_Error'            => array(
                'type' => 'error',
                'text' => esc_html__( 'API Error', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
            ),

            ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_disposable_email'            => array(
                'type' => 'error',
                'text' => self::$_instance->user_error_message,
            ),

        ) );

    }


    /**
     * Validate Email Address in function {is_email}
     *
     * @param   array $errors
     * @param   \MC4WP_Form $form
     *
     * @see     Function: validate() in Class MC4WP_Form
     *
     * @since   1.0.0
     * @access  public
     */
    public function validate( $errors, $form ) {

        /* DO NOT Continue if is already invalid */
        if( isset( $errors['invalid_email'] ) ) {
            return $errors;
        }

        /**
         * If we reach here, The email is good and not empty
         * Check if disposable
         * */
        $user_email = $form->data['EMAIL'];


        $is_valid_email = EmailValidator::instance()->is_valid_email( $user_email );


        if ( is_wp_error( $is_valid_email ) ) {

            $errors[] = ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_BTE_API_Error';

        }
        elseif( FALSE === $is_valid_email ) {

            $errors[] = ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_disposable_email';

        }


        return $errors;

    }

}