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
class Form_WP_Comment {

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
     * @return Form_WP_Comment
     */
    public static function instance() {

        if( is_null( self::$_instance ) ) {

            self::$_instance = new self;

            self::$_instance->plugin_settings = Jocker_SuperJocker::instance()->plugin_settings;


            self::$_instance->is_email_filtered                 = HelperFactory::instance()->cast_bool( self::$_instance->plugin_settings['wp_functions']['sw_wp_filter_func_is_email'] );
            self::$_instance->is_email_address_unsafe_filtered  = HelperFactory::instance()->cast_bool( self::$_instance->plugin_settings['wp_functions']['sw_wp_filter_func_is_email_address_unsafe'] );


            /**
             * Trigger Only if the client choose to not filter {is_email} function
             * to avoid {remove_filter} after done
             *
             * */
            if( ( ! self::$_instance->is_email_filtered ) && 
                ( TRUE === HelperFactory::instance()->cast_bool( self::$_instance->plugin_settings['wp_forms']['sw_wp_validate_email_on_post_comment'] ) ) ) {

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

        add_action( 'pre_comment_on_post',  array( &$this, 'hook_is_email_before_post_comment' ) );
        add_action( 'comment_post',         array( &$this, 'unhook_is_email_after_post_comment' ), 10, 3 );

    }

    /**
     * Fires before a comment is posted.
     *
     * @param int $comment_post_ID Post ID.
     *
     */
    public function hook_is_email_before_post_comment( $comment_post_ID ) {

        add_filter( 'is_email',             array( &$this, 'validate' ), 10, 3 );

    }

    /**
     * Fires immediately after a comment is inserted into the database.
     *
     * @param int        $comment_ID       The comment ID.
     * @param int|string $comment_approved 1 if the comment is approved, 0 if not, 'spam' if spam.
     * @param array      $commentdata      Comment data.
     *
     */
    public function unhook_is_email_after_post_comment( $comment_ID, $comment_approved, $commentdata ) {

        remove_filter( 'is_email',          array( &$this, 'validate' ), 15 );

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
    public function validate( $is_email, $user_email, $context ) {

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

}