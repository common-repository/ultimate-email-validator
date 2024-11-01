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
class Form_XLib_CF7 {

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
     * @return Form_XLib_CF7
     */
    public static function instance() {

        if( is_null( self::$_instance ) ) {

            self::$_instance = new self;

            self::$_instance->plugin_settings = Jocker_SuperJocker::instance()->plugin_settings;

            if( TRUE === HelperFactory::instance()->cast_bool( self::$_instance->plugin_settings['cf7']['sw_cf7_validate_email'] ) ) {

                self::$_instance->user_error_message = self::$_instance->plugin_settings['cf7']['txt_cf7_form_message'];

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

        add_filter( 'wpcf7_validate_email',     array( $this, 'validate' ), 20, 2 );
        add_filter( 'wpcf7_validate_email*',    array( $this, 'validate' ), 20, 2 );

    }



    /**
     * Validate Email Address in function {is_email}
     *
     * @param   \WPCF7_Validation     $result
     * @param   \WPCF7_FormTag        $tag        ($context)
     *
     * @see     https://contactform7.com/2015/03/28/custom-validation/
     *
     * @since   1.0.0
     * @access  public
     */
    public function validate( $result, $tag ) {

        if( ! class_exists('\WPCF7_FormTag') ) {
            return $result;
        }

        $user_email = null;

        $tag = new \WPCF7_FormTag( $tag );

        if( ! isset( $tag->name ) ) {
            return $result;
        }

        if ( ( 'email' == (string) $tag->type ) || ( 'email*' == (string) $tag->type ) ) {

            if( ! isset( $_POST[ $tag->name ] ) ) {
                return $result;
            }

            $user_email = sanitize_email( $_POST[ $tag->name ] );

        }

        if( is_null( $user_email ) ) {
            return $result;
        }

        /*
         * is_email is already done
         *
         * */
		$is_valid_email = EmailValidator::instance()->is_valid_email( $user_email );


        if ( is_wp_error( $is_valid_email ) ) {

            $loc_error_msg      = $is_valid_email->get_error_message();

            /**
             * Response Error
             * */
            $result->invalidate( $tag, esc_html( $loc_error_msg ) );

        }
        elseif( FALSE === $is_valid_email ) {
            $result->invalidate( $tag, wp_kses_post( self::$_instance->user_error_message ) );
        }

        return $result;

    }

}