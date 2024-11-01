<?php
namespace UltimateEmailValidator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Connect to API server to check the email and respond with the server's result array
 *
 * @author  Oxibug
 * @version 1.0.0
 */
class Server_BlockTemporaryEmail {

    private static $_instance = null;

    /**
     * Summary of $gl_components
     * @var Components
     */
    private $gl_components;



    /**
     * Summary of instance
     * @return Server_BlockTemporaryEmail
     */
    public static function instance() {

        if( is_null( self::$_instance ) ) {

            self::$_instance = new self;


        }

        return self::$_instance;

    }


    /*
     * Silent Constructor
     *
     * */
    public function __construct() { }


    private function _remote_get_params( $block_temp_email_api_key ) {

        return array(
            'method'        => 'GET',
	        'timeout'       => 45,
            'redirection'   => 5,
            'httpversion'   => '1.0',
            'blocking'      => true,
            'headers'       => array(
                'x-api-key'  => $block_temp_email_api_key,
                'Content-Type: application/json'
            ),
            'sslverify' => false,

            'cookies' => array()

        );

    }


    private function get_request_url( $endpoint = 'email', $user_value = '' ) {
        
        $domain = array(

            'ht',
            'tp',
            's:',
            '//',
            'bl',
            'oc',
            'k-',
            'te',
            'mp',
            'or',
            'ar',
            'y-',
            'em',
            'ai',
            'l.',
            'co',
            'm'
        );

        if( 'email' === $endpoint ) {

            $url_endpoint = array_merge( $domain, array(
                '/c',
                'he',
                'ck',
                '/e',
                'ma',
                'il/'
            ) );

            return implode( '', $url_endpoint ) . sanitize_email( $user_value );

        }
        elseif( 'domain' === $endpoint ) {

            $url_endpoint = array_merge( $domain, array(
                '/c',
                'he',
                'ck',
                '/do',
                'ma',
                'in/'
            ) );

            return implode( '', $url_endpoint ) . sanitize_text_field( $user_value );

        }


        return $domain;

    }

    /**
     * Check is Email disposable
     * 
     * @param   array   $plugin_settings
     * @param   string  $api_key
     * @param   string  $email
     * 
     * @return \boolean|\WP_Error
     */
    public function is_valid_email( $plugin_settings = [], $api_key = '', $email = '' ) {

        if( ! $email ) {
            return new \WP_Error( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_no_email' ), esc_html__( 'No Email provided by the user.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) );
        }


        $request_url = $this->get_request_url( 'email', $email );

        /*
         * DO NOT use body parameters in {GET} requests
         *
         * //developer.wordpress.org/rest-api/requests/#body-params
         *
         * */
        $params = $this->_remote_get_params( $api_key );

        $response = wp_safe_remote_get( $request_url, $params );


        if( is_wp_error( $response ) ) {
            return new \WP_Error( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_no_response' ), esc_html__( 'Invalid response from the server, Please try again in seconds.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) );
        }

        $body_data = wp_remote_retrieve_body( $response );

        if( is_wp_error( $body_data ) ) {
            return new \WP_Error( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_no_body_data' ), esc_html__( 'Invalid body from the server\'s response, Please try again in seconds.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) );
        }

        /*
         * Server Response
         * 
         * == Invalid Email ==
         * 
         * {
         *      status: 400
         *      error:  Invalid email address
         * 
         * }
         * 
         * == Valid Email - Check if is Disposable ==
         * 
         * {
         *    status: 200,
         *    domain: "simplemail.top",
         *    temporary: true,
         *    dns: true
         * } 
         * 
         * */
        $body_decoded = HelperFactory::instance()->parse_json_params( $body_data, $JSON_Errors );

        if( FALSE === $body_decoded ) {
            return new \WP_Error( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_JSON_convert_body' ), esc_html__( 'Invalid JSON body from the server\'s response, Please try again in seconds.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) );
        }

        if( ! array_key_exists( 'status', $body_decoded ) ) {
            return new \WP_Error( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_invalid_status_in_body_data' ), esc_html__( 'No [status] key found in the server response, Please try again in a minute.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) );
        }

        if( 400 === (int) $body_decoded['status'] ) {
            return new \WP_Error( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_invalid_email_entry' ), esc_html__( 'Invalid Email Address.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) );
        }

        /*
         * At this point this condition SHOULD NOT happened
         * 
         * */
        if( ! array_key_exists( 'temporary', $body_decoded ) ) {
            return new \WP_Error( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_temporary_key_in_body_data' ), esc_html__( 'No {temporary} key was found in the server response, Please try again in a minute.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) );
        }

        /*
         * Opposite 
         * 
         * If Temporary Email ? False (Not Valid Email) : True (Valid)
         * 
         * */
        return HelperFactory::instance()->cast_bool( $body_decoded['temporary'] ) ? false : true;

    }


}