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
class Server_QuickEmailVeification {

    private static $_instance = null;

    /**
     * Summary of $gl_components
     * @var Components
     */
    private $gl_components;



    /**
     * Take a Single Instance
     * 
     * @return Server_QuickEmailVeification
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


    private function _remote_get_params() {

        return array(
            'method'        => 'GET',
	        'timeout'       => 45,
            'redirection'   => 5,
            'httpversion'   => '1.0',
            'blocking'      => true,
            'headers'       => array(
                'Content-Type: application/json'
            ),
            'sslverify' => false,

            'cookies' => array()

        );

    }

    /**
     * 
     * https://api.quickemailverification.com/v1/verify?email=richard@quickemailverification.com&apikey=API_KEY
     * 
     * @param mixed $endpoint 
     * @param mixed $user_value 
     * @return \string|string[]
     */
    private function get_request_url( $email = '', $apikey ) {
        
        $domain = array(

            'ht',
            'tp',
            's:',
            '//',
            'api.quickemailverification',
            '.com'
        );

        $version = 'v1';
        
        $url_endpoint = array_merge( $domain, 
        [
            '/',
            $version,
        ], 
        [
            // VI: No Backslash after
            '/verify'
        ] );
        
        return add_query_arg( [
            
            'email'  => sanitize_email( $email ),
            'apikey'  => sanitize_text_field( $apikey ),

        ], implode( '', $url_endpoint ) );
        
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

        $disable_defender_when_reach_limit = array_key_exists( 'sw_disable_defender_when_reach_limit', $plugin_settings['defender_api_keys'] ) ? 
            HelperFactory::instance()->cast_bool( $plugin_settings['defender_api_keys']['sw_disable_defender_when_reach_limit'] ) :
            false;

        $user_want_send_to_unsafe = array_key_exists( 'sw_quick_email_verification_send_to_unsafe', $plugin_settings['defender_api_keys'] ) ?
            HelperFactory::instance()->cast_bool( $plugin_settings['defender_api_keys']['sw_quick_email_verification_send_to_unsafe'] ) :
            FALSE;


        $request_url = $this->get_request_url( $email, $api_key );

        /**
         * DO NOT use body parameters in {GET} requests
         *
         * //developer.wordpress.org/rest-api/requests/#body-params
         *
         * @var array
         * */
        $params = $this->_remote_get_params();

        /**
         * The server response array
         * 
         * @var array|\WP_Error
         * */
        $response = wp_safe_remote_get( $request_url, $params );

        if( is_wp_error( $response ) ) {
            return new \WP_Error( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_no_response' ), esc_html__( 'Invalid response from the server, Please try again in seconds.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) );
        }

        /**
         * QuickEmailVerification API also returns following HTTP status codes to indicate success or failure of request. The "message" field of API response body will contain more descriptive message about the error.
         * 
         * 200 - Request is completed successfully
         * 400 - Server can not understand the request sent to it. This is kind of response can occur if parameters are passed wrongly.
         * 401 - Server can not verify your authentication to use API. Please check whether API key is proper or not.
         * 
         * -- DO NOT RETURN ERROR -- 402 - You are running out of your credit limit.
         * 
         * 403 - Your account has been disabled.
         * 404 - Requested API can not be found on server.
         * 429 - Too many requests. Rate limit exceeded.
         * 500 - Internal Server Error.
         * 
         * @var int
         * */
        $response_code = wp_remote_retrieve_response_code( $response );

        switch( $response_code ) {
            
            case 400: {
                    return new \WP_Error( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_qev_400' ), esc_html__( '[Quick Email Verification] Server can not understand the request sent to it.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) );
                } break;
            
            case 401: {
                    return new \WP_Error( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_qev_401' ), esc_html__( '[Quick Email Verification] Server can not verify your authentication to use API. Please check whether API key is proper or not.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) );
                } break;
           
            case 403: {
                    if( FALSE === $disable_defender_when_reach_limit ) {
                        return new \WP_Error( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_qev_403' ), esc_html__( '[Quick Email Verification] Your account has been disabled.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) );
                    }

                    // Account Disabled: Email Always Valid
                    return true;

                } break;

            case 404: {
                    return new \WP_Error( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_qev_404' ), esc_html__( '[Quick Email Verification] Requested API can not be found on server.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) );
                } break;

            case 429: {
                    if( FALSE === $disable_defender_when_reach_limit ) {
                        return new \WP_Error( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_qev_429' ), esc_html__( '[Quick Email Verification] Too many requests. Rate limit exceeded.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) );
                    }

                    // Limit Exceded: Email Always Valid
                    return true;
                } break;

            case 500: {
                    return new \WP_Error( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_qev_500' ), esc_html__( '[Quick Email Verification] Internal Server Error.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) );
                } break;
            
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
         *       "result":"invalid",
         *       "reason":"rejected_email",
         *       "disposable":"false",
         *       "accept_all":"false",
         *       "role":"false",
         *       "free":"false",
         *       "email":"richard@quickemailverification.com",
         *       "user":"richard",
         *       "domain":"quickemailverification.com",
         *       "mx_record":"us2.mx1.mailhostbox.com",
         *       "mx_domain":"mailhostbox.com",
         *       "safe_to_send":"false",
         *       "did_you_mean":"",
         *       "success":"true",
         *       "message":null
         *   }
         * 
         * */
        $body_decoded = HelperFactory::instance()->parse_json_params( $body_data, $JSON_Errors );

        if( FALSE === $body_decoded ) {
            return new \WP_Error( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_JSON_convert_body' ), esc_html__( 'Invalid JSON body from the server\'s response, Please try again in seconds.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) );
        }

        if( ! array_key_exists( 'success', $body_decoded ) ) {
            return new \WP_Error( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_invalid_success_in_body_data' ), esc_html__( 'No [success] key found in the server response, Please try again in a minute.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) );
        }

        if( FALSE === HelperFactory::instance()->cast_bool( $body_decoded['success'] ) ) {
            return new \WP_Error( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_invalid_entry' ), esc_html__( 'Invalid API Calling.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) );
        }


        /*
         * At this point this condition SHOULD NOT happened
         * 
         * */
        if( ! array_key_exists( 'disposable', $body_decoded ) ) {
            return new \WP_Error( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_disposable_key_in_body_data' ), esc_html__( 'No [disposable] key was found in the server response, Please try again in a minute.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) );
        }


        $is_disposable      = HelperFactory::instance()->cast_bool( $body_decoded['disposable'] );
        $is_safe_to_send    = HelperFactory::instance()->cast_bool( $body_decoded['safe_to_send'] );
        $is_free_email      = HelperFactory::instance()->cast_bool( $body_decoded['free'] );
        
        /**
         * Role-based Email Addresses - Allowed by UEV
         * 
         * @see http://docs.quickemailverification.com/getting-started/understanding-email-verification-result
         * 
         * @var     bool
         * */
        $is_role_email      = HelperFactory::instance()->cast_bool( $body_decoded['role'] );

        /*
         * Opposite 
         * 
         * If Temporary Email ? False (Not Valid Email) : True (Valid)
         * 
         * */
        if( $is_disposable ) {
            return false;
        }

        if( FALSE === $is_safe_to_send ) {

            // Very BAD to reject Business Emails
            if( $is_role_email ) {
                return true;
            }

            // Reach Here
            return $user_want_send_to_unsafe;

        }

        return true;

    }


}