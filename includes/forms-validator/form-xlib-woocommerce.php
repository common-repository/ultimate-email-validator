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
class Form_XLib_WooCommerce {

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
     * The User's Custom Error Message On Updating His Own Profile
     *
     * @var string
     */
    private $user_updating_profile_error_message = '';




    /*
     * Silent Constructor
     *
     * */
    public function __construct() { }


    /**
     * Take in instance of the class
     *
     * @return Form_XLib_WooCommerce
     */
    public static function instance() {

        if( is_null( self::$_instance ) ) {

            self::$_instance = new self;


            self::$_instance->plugin_settings = Jocker_SuperJocker::instance()->plugin_settings;


            if( TRUE === HelperFactory::instance()->cast_bool( self::$_instance->plugin_settings['woocommerce']['sw_wc_validate_email_on_registration'] ) ) {

                self::$_instance->user_error_message = self::$_instance->plugin_settings['woocommerce']['txt_wc_registration_form_message'];

                self::$_instance->user_updating_profile_error_message = self::$_instance->plugin_settings['woocommerce']['txt_wc_update_profile_message'];

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
         * Triggered before call function {wc_create_new_customer}
         *
         * @see Function: process_registration
         *
         * */
        add_filter( 'woocommerce_process_registration_errors',     array( &$this, 'validate' ), 10, 4 );

        /**
         * Validate email address while update profile from WooCommerce
         *
         * */
        add_action( 'woocommerce_save_account_details_errors',     array( &$this, 'validate_update_profile' ), 10, 2 );

    }


    /**
     * Validate Email Address in function {is_email}
     *
     * @param   \WP_Error   $validation_error
     * @param   string      $username
     * @param   string      $password
     * @param   string      $user_email
     *
     * @see     https://stackoverflow.com/questions/22089228/custom-registration-fields-in-woocommerce-not-validating
     *
     * @since   1.0.0
     * @access  public
     */
    public function validate( $validation_error, $username, $password, $user_email ) {

        $is_valid_email = EmailValidator::instance()->is_valid_email( $user_email );


        if ( is_wp_error( $is_valid_email ) ) {

            $error_msg      = $is_valid_email->get_error_message();

            $validation_error->add( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_BTE_API_error' ), $error_msg );
            return $validation_error;

        }
        elseif( FALSE === $is_valid_email ) {

            $validation_error->add( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_disposable_email' ), self::$_instance->user_error_message );
            return $validation_error;

        }

        return $validation_error;

    }


    /**
     * Validate Email Address in function {is_email}
     *
     * @param   \WP_Error   $errors  Errors
     * @param   \stdClass   $user               The User's object
     *
     * @see     do_action_ref_array | class-wc-form-handler Function: save_account_details
     *
     * @since   1.0.0
     * @access  public
     */
    public function validate_update_profile( &$errors, &$user ) {

        $is_valid_email = EmailValidator::instance()->is_valid_email( $user->user_email );


        if ( is_wp_error( $is_valid_email ) ) {

            $error_msg      = $is_valid_email->get_error_message();

            $errors->add( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_BTE_API_error' ), $error_msg );

        }
        elseif( FALSE === $is_valid_email ) {

            $errors->add( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_disposable_email' ), self::$_instance->user_updating_profile_error_message );

        }

    }

}