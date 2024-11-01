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
class Form_XLib_BuddyPress {

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
     * @return Form_XLib_BuddyPress
     */
    public static function instance() {

        if( is_null( self::$_instance ) ) {

            self::$_instance = new self;

            self::$_instance->plugin_settings = Jocker_SuperJocker::instance()->plugin_settings;

            if( TRUE === HelperFactory::instance()->cast_bool( self::$_instance->plugin_settings['buddypress']['sw_bp_validate_email_on_registration'] ) ) {

                self::$_instance->user_error_message = self::$_instance->plugin_settings['buddypress']['txt_bp_registration_form_message'];

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

        /* Override the WordPress Core */
        add_filter( 'wpmu_validate_user_signup',        array( &$this, 'bp_validate' ) );

        /* For All ( Multisite & Single ) */
        add_filter( 'bp_core_validate_user_signup',     array( &$this, 'bp_validate' ) );
        
    }


    /**
     * BuddyPress Validate
     *
     * @param string    $result         An array of three keys { user_name | user_email | errors }
     *
     * @since   1.0.0
     * @access  public
     */
    public function bp_validate( $result ) {

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