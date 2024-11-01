<?php
namespace UltimateEmailValidator;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 *
 * Collect all controls of section [ general ]
 *
 * @version 1.0
 *
 * @author Oxibug
 *
 */
class AdminPanelTab_Admin {

    /**
     * Local instance to save the the class's object
     * after instantiated
     *
     * @var AdminPanelTab_Admin
     */
    private static $_instance = null;


    public $loc_components = [];


    /**
     * Instantiate an object from class
     *
     * @return AdminPanelTab_Admin
     */
    public static function instance() {

        if( is_null( self::$_instance ) ) {

            self::$_instance = new self;

            self::$_instance->loc_components = Components::instance();
        }

        return self::$_instance;

    }

    /**
     * Silent Constructor
     *
     * */
    private function __construct() { }

    public function tab_defender_api_keys() {
        
        return array(

            array(
                'id'     => 'sw_disable_defender',
                'title'     => esc_html__('Disable Defender?', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => __('If you check this option, we won\'t verify users\' emails during registration and other processes.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),

                'type'      => 'checkbox',
                'default'   => false,
                'params'    => array(
                    'priority'  => 10,
                )

            ),

            array(
                'id'     => 'sw_disable_defender_when_reach_limit',
                'title'     => esc_html__('Disable Defender When Reaching Daily/Monthly Limit?', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => __('If you enable this option, user email verification will be skipped during registration and other processes if your API usage reaches the daily/monthly limit. Otherwise, clients will not be able to register until the limit is increased.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),

                'type'      => 'checkbox',
                'default'   => false,
                'params'    => array(
                    'priority'  => 20,
                )
            ),

            array(
                'id'     => 'ddl_validate_by_vendor',
                'title'     => esc_html__('Validate Emails By Certain API', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => esc_html__('You have the option to choose a single vendor for API usage, or you can select [All] to re-verify only VALID emails. Example: If "Block Temporary Email" responds with VALID, the email will undergo verification with the next API. However, if it\'s marked INVALID, it will be rejected from the first API.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),

                'type'      => 'select',
                'default'   => 'all',
                'options'   => [
                    'all'   => esc_attr__('Use All', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                    'quick_email_verification'   => esc_attr__('Quick Email Verification', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                    'block_temporary_email'   => esc_attr__('Block Temporary Email', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                ],
                'params'    => array(
                    'priority'  => 30,
                )
            ),

            array(
                'id'     => 'txt_quick_email_verification_api_key',
                'title'     => esc_html__('[Quick Email Verification] - API KEY', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => wp_kses_post( sprintf( __('You have to register in <a href="%s" target="_blank">Quick Email Verification</a> and get an API Key to be able to check emails.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), 'https://quickemailverification.com/' ) ),

                'type'      => 'text',
                'default'   => '',
                'params'    => array(
                    'priority'  => 30,
                )
            ),

            array(
                'id'     => 'sw_quick_email_verification_send_to_unsafe',
                'title'     => esc_html__('[Quick Email Verification] - Accept "Unsafe to Send" E-Mail Addresses? [Disable is Recommended]', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => __('If you enable this option, Users can register with emails marked as "Unsafe to Send" by Quick Email Verification API, Example: Try to validate this [petermcilrath9@roll.kranso.com] by Quick Email Verification, You will notice is\'s accepted and not disposable but is "Not Safe to Send".', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),

                'type'      => 'checkbox',
                'default'   => false,
                'params'    => array(
                    'priority'  => 20,
                )
            ),


            array(
                'id'     => 'txt_block_temp_email_api_key',
                'title'     => esc_html__('[Block Temporary Email] - API KEY', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => wp_kses_post( sprintf( __('You have to register in <a href="%s" target="_blank">Block Temporary Email</a> and get an API Key to be able to check emails.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), 'https://block-temporary-email.com' ) ),

                'type'      => 'text',
                'default'   => '',
                'params'    => array(
                    'priority'  => 30,
                )
            ),
        );

    }

    public function tab_wordpress_functions() {

        return array(

            array( 'id'     => 'sw_wp_filter_func_is_email',
                'title'     => esc_html__('Filter {is_email} function permanently?', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => wp_kses_post( sprintf( __('If you checked that option it will affect all functions use {%1$s} function,<br/><br/>
                    The default is {unchecked} to display your custom message, Otherwise you\'ll notice that the custom message will not be displayed because the function {%1$s} fired early.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), 'is_email' ) ),

                'type'      => 'checkbox',
                'default'   => false,
                'params'    => array(
                    'priority'  => 10,
                )

            ),

            array( 'id'     => 'sw_wp_filter_func_is_email_address_unsafe',
                'title'     => esc_html__('Filter {is_email_address_unsafe} function permanently?', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => wp_kses_post( sprintf( __('If you checked that option it will affect all functions use {%1$s} function,<br/><br/>
                    The default is {unchecked} to display your custom message, Otherwise you\'ll notice that the custom message will not be displayed because the function {%1$s} fired early.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), 'is_email_address_unsafe' ) ),

                'type'      => 'checkbox',
                'default'   => false,
                'params'    => array(
                    'priority'  => 20,
                )

            ),

        );

    }


    public function tab_wordpress_forms() {

        return array(


            array( 'id'     => 'sw_wp_validate_email_on_registration',
                'title'     => esc_html__('Validate the user email in the registration form?', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => wp_kses_post( __('Check the user email before registration done and display the following message if he use a disposable email.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) ),

                'type'      => 'checkbox',
                'default'   => true,
                'params'    => array(
                    'priority'  => 30,
                )

            ),
            array( 'id'     => 'txt_wp_registration_form_message',
                'title'     => esc_html__('WordPress Registration Form - Error Message.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => wp_kses_post( sprintf( __( 'Enter a message to notify your user that he cannot use a disposable email address.<br/><br/>
                    That will be affected if you choose to filter the following functions.<br/>%s.<br/>%s.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), 'is_email', 'is_email_address_unsafe' ) ),

                'type'      => 'textarea',
                'default'   => wp_kses_post( __( 'You can\'t use that email address to sign up because it\'s marked as disposable. Please use a real email address.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) ),
                'params'    => array(
                    'priority'  => 40,
                )

            ),

            array( 'id'     => 'sw_wp_validate_email_on_post_comment',
                'title'     => esc_html__('Validate the user email on post a comment?', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => wp_kses_post( __('Check the user email before the comment is posted. (The WordPress Default message will be displayed).', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) ),

                'type'      => 'checkbox',
                'default'   => true,
                'params'    => array(
                    'priority'  => 50,
                )

            ),

            array( 'id'     => 'sw_wp_validate_email_on_update_profile',
                'title'     => esc_html__('Validate the user email on update user profile?', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => wp_kses_post( __('Check the user email before the user profile updated. (The WordPress Default message will be displayed).', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) ),

                'type'      => 'checkbox',
                'default'   => true,
                'params'    => array(
                    'priority'  => 60,
                )

            ),
            array( 'id'     => 'txt_wp_update_profile_message',
                'title'     => esc_html__('Update Profile Form - Error Message.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => wp_kses_post( sprintf( __( 'Enter a message to notify your user that he cannot use a disposable email address.<br/><br/>
                    That will be affected if you choose to filter the following functions.<br/>%s.<br/>%s.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), 'is_email', 'is_email_address_unsafe' ) ),

                'type'      => 'textarea',
                'default'   => wp_kses_post( __( 'You can\'t replace your email address with a disposable one. Please use a real email address.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) ),
                'params'    => array(
                    'priority'  => 70,
                )

            ),


        );

    }


    public function tab_buddypress() {

        return array(

            array( 'id'     => 'sw_bp_validate_email_on_registration',
                'title'     => esc_html__('Validate the user email in the BuddyPress registration form?', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => wp_kses_post( __('Check the user email before registration done and display the following message if he use a disposable email.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) ),

                'type'      => 'checkbox',
                'default'   => true,
                'params'    => array(
                    'priority'  => 10,
                )

            ),

            array( 'id'     => 'txt_bp_registration_form_message',
                'title'     => esc_html__('BuddyPress Registration Form - Error Message.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => wp_kses_post( sprintf( __( 'Enter a message to notify your user that he cannot use a disposable email address.<br/><br/>
                    That will be affected if you choose to filter the following functions.<br/>%s.<br/>%s.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), 'is_email', 'is_email_address_unsafe' ) ),

                'type'      => 'textarea',
                'default'   => wp_kses_post( __( 'You can\'t use that email address to sign up because it\'s marked as disposable. Please use a real email address.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) ),
                'params'    => array(
                    'priority'  => 20,
                )

            ),

        );

    }


    /**
     * The elements array of WooCommerce
     *
     * @return  array
     *
     * @since   1.0.0
     * @access  public
     */
    public function tab_woocommerce() {

        return array(

            array( 'id'     => 'sw_wc_validate_email_on_registration',
                'title'     => esc_html__('Validate the user email in the (WooCommerce) form?', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => wp_kses_post( __('Check the user email before registration done and display the following message if he use a disposable email.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) ),

                'type'      => 'checkbox',
                'default'   => true,
                'params'    => array(
                    'priority'  => 10,
                )

            ),

            array( 'id'     => 'txt_wc_registration_form_message',
                'title'     => esc_html__('Using Disposable Email Address Error Message.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => wp_kses_post( sprintf( __( 'Enter a message to notify your user that he cannot use a disposable email address.<br/><br/>
                    That will be affected if you choose to filter the following functions.<br/>%s.<br/>%s.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), 'is_email', 'is_email_address_unsafe' ) ),

                'type'      => 'textarea',
                'default'   => wp_kses_post( __( 'You can\'t use that email address to sign up because it\'s marked as disposable. Please use a real email address.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) ),
                'params'    => array(
                    'priority'  => 20,
                )

            ),

            array( 'id'     => 'txt_wc_update_profile_message',
                'title'     => esc_html__('Update Profile Form - Error Message.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => wp_kses_post( sprintf( __( 'Enter a message to notify your user that he cannot use a disposable email address.<br/><br/>
                    That will be affected if you choose to filter the following functions.<br/>%s.<br/>%s.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), 'is_email', 'is_email_address_unsafe' ) ),

                'type'      => 'textarea',
                'default'   => wp_kses_post( __( 'You can\'t replace your email address with a disposable one. Please use a real email address.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) ),
                'params'    => array(
                    'priority'  => 20,
                )

            ),

        );

    }


    /**
     * The elements array of Contact Form 7
     *
     * @return  array
     *
     * @since   1.0.0
     * @access  public
     */
    public function tab_contactform7() {

        return array(

            array( 'id'     => 'sw_cf7_validate_email',
                'title'     => esc_html__('Validate the user email in the (Contact Form 7) form?', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => wp_kses_post( __('Check the user email before registration done and display the following message if he use a disposable email.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) ),

                'type'      => 'checkbox',
                'default'   => true,
                'params'    => array(
                    'priority'  => 10,
                )

            ),

            array( 'id'     => 'txt_cf7_form_message',
                'title'     => esc_html__('Using Disposable Email Address Error Message.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => esc_html__( 'Enter a message to notify your user that he cannot use a disposable email address.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),

                'type'      => 'textarea',
                'default'   => wp_kses_post( __( 'Your email is identified as disposable. Please provide a valid email address so we can stay in touch.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) ),
                'params'    => array(
                    'priority'  => 20,
                )

            ),

        );

    }

    /**
     * The elements array of MailChimp
     *
     * @return  array
     *
     * @since   1.0.0
     * @access  public
     */
    public function tab_mailchimp() {

        return array(

            array( 'id'     => 'sw_mc4wp_validate_email',
                'title'     => esc_html__('Validate the user email in the (MailChimp) form?', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => wp_kses_post( __('Check the user email before registration done and display the following message if he use a disposable email.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) ),

                'type'      => 'checkbox',
                'default'   => true,
                'params'    => array(
                    'priority'  => 10,
                )

            ),

            array( 'id'     => 'txt_mc4wp_form_message',
                'title'     => esc_html__('Using Disposable Email Address Error Message.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => esc_html__( 'Enter a message to notify your user that he cannot use a disposable email address.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),

                'type'      => 'textarea',
                'default'   => wp_kses_post( __( 'Your email is identified as disposable. Please provide a valid email address so we can stay in touch.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) ),
                'params'    => array(
                    'priority'  => 20,
                )

            ),

        );

    }


    /**
     * The elements array of Gravity Forms
     *
     * @return  array
     *
     * @since   1.0.0
     * @access  public
     */
    public function tab_gravityforms() {

        return array(

            array( 'id'     => 'sw_gravityforms_validate_email',
                'title'     => esc_html__('Validate the user email in the (Gravity Forms) form?', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => wp_kses_post( __('Check the user email before registration done and display the following message if he use a disposable email.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) ),

                'type'      => 'checkbox',
                'default'   => true,
                'params'    => array(
                    'priority'  => 10,
                )

            ),

            array( 'id'     => 'txt_gravityforms_form_message',
                'title'     => esc_html__('Using Disposable Email Address Error Message.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => esc_html__( 'Enter a message to notify your user that he cannot use a disposable email address.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),

                'type'      => 'textarea',
                'default'   => wp_kses_post( __( 'Your email is identified as disposable. Please provide a valid email address so we can stay in touch.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) ),
                'params'    => array(
                    'priority'  => 20,
                )

            ),

        );

    }


    /**
     * The elements array of Ninja Forms
     *
     * @return  array
     *
     * @since   1.0.0
     * @access  public
     */
    public function tab_ninjaforms() {

        return array(

            array( 'id'     => 'sw_ninjaforms_validate_email',
                'title'     => esc_html__('Validate the user email in the (Ninja Forms) form?', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => wp_kses_post( __('Check the user email before registration done and display the following message if he use a disposable email.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) ),

                'type'      => 'checkbox',
                'default'   => true,
                'params'    => array(
                    'priority'  => 10,
                )

            ),

            array( 'id'     => 'txt_ninjaforms_form_message',
                'title'     => esc_html__('Using Disposable Email Address Error Message.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN),
                'description'   => esc_html__( 'Enter a message to notify your user that he cannot use a disposable email address.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),

                'type'      => 'textarea',
                'default'   => wp_kses_post( __( 'Your email is identified as disposable. Please provide a valid email address so we can stay in touch.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) ),
                'params'    => array(
                    'priority'  => 20,
                )

            ),

        );

    }



}