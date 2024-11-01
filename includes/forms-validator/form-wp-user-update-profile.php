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
class Form_WP_UserUpdateOwnProfile {

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
     *
     * @return Form_WP_UserUpdateOwnProfile
     */
    public static function instance() {

        if( is_null( self::$_instance ) ) {

            self::$_instance = new self;

            self::$_instance->plugin_settings = Jocker_SuperJocker::instance()->plugin_settings;

            self::$_instance->is_email_filtered                 = HelperFactory::instance()->cast_bool( self::$_instance->plugin_settings['wp_functions']['sw_wp_filter_func_is_email'] );
            self::$_instance->is_email_address_unsafe_filtered  = HelperFactory::instance()->cast_bool( self::$_instance->plugin_settings['wp_functions']['sw_wp_filter_func_is_email_address_unsafe'] );


            if( TRUE === HelperFactory::instance()->cast_bool( self::$_instance->plugin_settings['wp_forms']['sw_wp_validate_email_on_update_profile'] ) ) {

                self::$_instance->user_error_message = self::$_instance->plugin_settings['wp_forms']['txt_wp_update_profile_message'];

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

    public function __currentscreen() {

        $screen = get_current_screen();
        
        if($screen->id !== 'profile') {
            return;
        }

        /**
         * Fired after hook {personal_options_update} to filter {is_email} function
         * and Un-Hook the filter after in the action {user_profile_update_errors}
         *
         * */
        if( 0 === did_action('personal_options_update') ) {

            /**
             * WordPress:   User can modify his email from Dashboard Profile page {is_admin} applied
             * 
             * BuddyPress:  USer can modify his email from BuddyPress profile page so check {bp_is_my_profile}
             * @see         https://buddypress.org/support/topic/is-profile-check/
             * 
             * For Extra Libraries, Add Check here in the future
             *
             * @var bool
             * */
            $is_profile_page = ( is_admin() || ( function_exists('bp_is_my_profile') && bp_is_my_profile() ) );

            if( ! $this->is_email_filtered ) {
                add_filter( 'is_email',             array( &$this, 'filter_is_email' ), 10, 3 );
                remove_filter( 'is_email',          array( &$this, 'filter_is_email' ), 15 );      /* Priority is MUST */
            }

            /**
             * Fired after
             *
             * 1. personal_options_update
             * 2. edit_user_profile_update
             *
             * */
            add_action( 'user_profile_update_errors',   array( &$this, 'validate_update_profile' ), 10, 3 );

        }

    }
    /**
     * Trigger the appropriate action
     *
     * @since   1.0.0
     * @access  public
     */
    private function core_actions() {

        add_action('current_screen', [$this, '__currentscreen']);

    }


    /**
     * Validate Email Address in function {is_email}
     *
     * @param bool   $is_email      Whether the email address has passed the is_email() checks. Default false.
     * @param string $user_email    The email address being checked.
     * @param string $context       Context under which the email was tested.
     *
     * @since   1.0.0
     * @access  public
     */
    public function filter_is_email( $is_email, $user_email, $context ) {

        /*
         * is_email is already done
         *
         * */
		$is_valid_email = EmailValidator::instance()->is_valid_email( $user_email );

        if ( is_wp_error( $is_valid_email ) || ( FALSE === $is_valid_email ) ) {

            /**
             * Email Address is Un-Safe
             * */
            return false;

        }

        return true;

    }



    /**
     * Fires after
     *
     * 1. personal_options_update
     * 2. edit_user_profile_update
     *
     * @param   \WP_Error     $errors     WP_Error object (passed by reference).
     * @param   bool          $update     Whether this is a user update.
     * @param   \stdClass     $user       User object (passed by reference).
     *
     * @since   1.0.0
     * @access  public
     */
    public function validate_update_profile( $errors, $update, $user ) {


        if( ! is_wp_error( $errors ) ) {
            $errors = new \WP_Error();
        }


        $user_email = $user->user_email;

        $is_valid_email = EmailValidator::instance()->is_valid_email( $user_email );

        if ( is_wp_error( $is_valid_email ) ) {

            $error_msg      = $is_valid_email->get_error_message();
            $errors->add( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_BTE_API_error' ), $error_msg );

        }
        elseif( FALSE === $is_valid_email ) {

            $errors->add( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_disposable_email' ), wp_kses_post( self::$_instance->user_error_message ) );

        }



        return $errors;

    }

}