<?php
namespace UltimateEmailValidator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Form - Registration
 *
 * @author  Oxibug
 * @version 1.0.0
 */
class Form_WP_Registration {


    private static $_instance = null;


    /**
     * The Plugin Settings Saved in DB, Otherwise the default values
     * @var array|null
     */
    public $plugin_settings = null;


    /**
     * Whether the user choose to permanently filter 
     * WordPress function {is_email}
     *
     * @var bool
     */
    private $is_email_filtered = false;

    /**
     * Whether the user choose to permanently filter 
     * WordPress function {is_email_address_unsafe}
     *
     * @var bool
     */
    private $is_email_address_unsafe_filtered = false;

    /**
     * The User's Custom Error Message
     *
     * @var string
     */
    private $user_error_message = '';


    /**
     * Take in instance of the class
     * @return Form_WP_Registration
     */
    public static function instance() {

        if( is_null( self::$_instance ) ) {

            self::$_instance = new self;

            self::$_instance->plugin_settings = Jocker_SuperJocker::instance()->plugin_settings;


            self::$_instance->is_email_filtered                 = HelperFactory::instance()->cast_bool( self::$_instance->plugin_settings['wp_functions']['sw_wp_filter_func_is_email'] );
            self::$_instance->is_email_address_unsafe_filtered  = HelperFactory::instance()->cast_bool( self::$_instance->plugin_settings['wp_functions']['sw_wp_filter_func_is_email_address_unsafe'] );


            if( TRUE === HelperFactory::instance()->cast_bool( self::$_instance->plugin_settings['wp_forms']['sw_wp_validate_email_on_registration'] ) ) {

                self::$_instance->user_error_message = self::$_instance->plugin_settings['wp_forms']['txt_wp_registration_form_message'];

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

        /* Single WordPress */
        add_filter( 'registration_errors',          array( &$this, 'validate' ), 10, 3 );

        /* Multisite */
        add_filter( 'wpmu_validate_user_signup',    array( &$this, 'mu_validate' ) );

    }

    /**
     * Validate Email Address
     *
     * @param \WP_Error $errors                 The errors found
     * @param string    $sanitized_user_login   The user login
     * @param string    $user_email             The entered Email Address
     *
     * @since   1.0.0
     * @access  public
     */
    public function validate( $errors, $sanitized_user_login, $user_email ) {

		$is_valid_email = EmailValidator::instance()->is_valid_email( $user_email );

		if ( is_wp_error( $is_valid_email ) ) {

            $loc_error  = $is_valid_email->get_error_code();
            $loc_msg    = $is_valid_email->get_error_message();

            $errors->add( $loc_error, $loc_msg );

		}
        elseif( FALSE === $is_valid_email ) {

            $errors->add( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_invalid_email' ), wp_kses_post( $this->user_error_message ) );

        }

		return $errors;

    }


    /**
     * BuddyPress Validate
     *
     * @param string    $result         An array of three keys { user_name | user_email | errors }
     *
     * @since   1.0.0
     * @access  public
     */
    public function mu_validate( $result ) {

        if( ! is_wp_error( $result['errors'] ) ) {
            $result['errors'] = new \WP_Error();
        }


        /*
         * is_email is already done
         *
         * */
		$is_valid_email = EmailValidator::instance()->is_valid_email( $result['user_email'] );


        if ( is_wp_error( $is_valid_email ) ) {

            $loc_msg    = $is_valid_email->get_error_message();

            /*
             * VI Case: We MUST use error code {user_email}
             * See function - bp_core_screen_signup
             *
             * */
            $result['errors']->add( 'user_email', $loc_msg );

        }
        elseif( FALSE === $is_valid_email ) {

            /*
             * VI Case: We MUST use error code {user_email}
             * See function - bp_core_screen_signup
             *
             * */
            $result['errors']->add( 'user_email', wp_kses_post( $this->user_error_message ) );

        }


        return $result;

    }

}