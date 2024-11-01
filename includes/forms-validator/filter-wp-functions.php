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
class Filter_WP_Functions {

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
     * Take in instance of the class
     *
     * @return Filter_WP_Functions
     */
    public static function instance() {

        if( is_null( self::$_instance ) ) {

            self::$_instance = new self;

            self::$_instance->plugin_settings = Jocker_SuperJocker::instance()->plugin_settings;


            self::$_instance->is_email_filtered                 = HelperFactory::instance()->cast_bool( self::$_instance->plugin_settings['wp_functions']['sw_wp_filter_func_is_email'] );
            self::$_instance->is_email_address_unsafe_filtered  = HelperFactory::instance()->cast_bool( self::$_instance->plugin_settings['wp_functions']['sw_wp_filter_func_is_email_address_unsafe'] );


            self::$_instance->core_actions();
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

        if( self::$_instance->is_email_filtered ) {
            add_filter( 'is_email',                 array( &$this, 'filter_is_email' ), 10, 3 );
        }


        if( self::$_instance->is_email_address_unsafe_filtered ) {
            add_filter( 'is_email_address_unsafe',  array( &$this, 'filter_is_email_address_unsafe' ), 10, 2 );
        }


    }


    /**
     * Validate Email Address in function {is_email}
     *
     * NOTE: This Override WordPress and BuddyPress Registration Custom Messages
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
             * Different Than {is_email_address_unsafe}
             *
             * This Means is not an email
             *
             * */
            return false;

        }

        return true;

    }


    /**
     * Validate Email Address in function {is_email}
     *
     * NOTE: This Override WordPress and BuddyPress Registration Custom Messages
     *
     * @param bool   $is_email_address_unsafe Whether the email address is "unsafe". Default false.
     * @param string $user_email              User email address.
     *
     * @since   1.0.0
     * @access  public
     */
    public function filter_is_email_address_unsafe( $is_email_address_unsafe, $user_email ) {

        /*
         * is_email is already done
         *
         * */
		$is_valid_email = EmailValidator::instance()->is_valid_email( $user_email );


        if ( is_wp_error( $is_valid_email ) || ( FALSE === $is_valid_email ) ) {

            /**
             * Email Address is Un-Safe
             * */
            $is_email_address_unsafe = true;
            return true;

        }

        $is_email_address_unsafe = false;
        return false;

    }

}