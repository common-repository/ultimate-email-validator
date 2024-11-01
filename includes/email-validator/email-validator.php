<?php
namespace UltimateEmailValidator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Trigger the Email validation tests
 *
 * @version 1.0
 *
 * @author Oxibug
 *
 */
class EmailValidator {

    /**
     * Static instance of the main plugin class
     *
     * @var EmailValidator
     *
     * @since   1.0.0
     * @access  public
     *
     */
    private static $_instance = null;


    /**
     * Globals Variables Object
     *
     * @var Components
     *
     */
    public $loc_globals;
    

    
    /**
     * Take an instance
     * 
     * @return EmailValidator
     */
    public static function instance() {

        if( is_null( self::$_instance ) ) {

            self::$_instance = new self;
            
            self::$_instance->loc_globals = Components::instance();

        }

        return self::$_instance;

    }


    private function __construct() {}

    
    /**
     * Check is Email disposable through all supported APIs
     * 
     * Step 01:     Check Email is Greater than 6 letters and it has {@} in the not first position
     * Step 02:     [PRO]   Check the email domain in the Custom Repeater from Plugin Settings
     * Step 03:     [FREE]  Connect to APIs 
     * 
     * VI Note: This function MUST be called after Super_Jocker class has instantiated to
     * be able to use the saved or default plugin settings options
     * 
     * VI Note: DO NOT ever use {is_email} function because in some forms it used inside actions
     * Which generates Allocate Memory error so use the main implementation of the function
     * 
     * @param   string  $email
     * 
     * @return  \boolean|\WP_Error
     * 
     * @since   1.0.0
     * @access  public
     */
    public function is_valid_email( $email = '' ) {
                
        /* Test for the minimum length the email can be */
        if ( strlen( $email ) < 6 ) {
            return false;
        }

        /* Test for an @ character after the first position */
        if ( strpos( $email, '@', 1 ) === false ) {
            return false;
        }

        /* 
         * Split out the local and domain parts 
         * 
         * DO NOT add the rest of the function because we JUST need
         * any string in domain part
         * 
         * */
        list( $local, $domain ) = explode( '@', $email, 2 );

        /**
         * Use the Custom APIs?
         * @param bool
         * */
        $use_api = false;
        
        /**
         * Here We're very sure that the plugins settings has data and the Defender is Enabled
         * 
         * @see Jocker_SuperJocker
         * 
         * @param array
         * */
        $plugin_settings = Jocker_SuperJocker::instance()->plugin_settings;

             
        /**
         * Before start validation process
         * 
         * @param   string          $email
         * @param   array|null      $plugin_settings The saved or default settings
         * 
         * @since   1.0.0
         * */
        do_action( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/actions/is_valid_email', $email, $plugin_settings );


        if( ( ! array_key_exists( 'block_domains', $plugin_settings ) ) || 
            ( ! array_key_exists( 'rep_email_domains', $plugin_settings['block_domains'] ) ) || 
            ( ! is_array( $plugin_settings['block_domains']['rep_email_domains'] ) ) || 
            ( empty( $plugin_settings['block_domains']['rep_email_domains'] ) ) ) {
            
            $use_api = true;

        }

        /*
         * Custom Repeaters Found
         * 
         * */
        if( ! $use_api ) {
            $block_domains = $plugin_settings['block_domains']['rep_email_domains'];
            
            $is_disposable_domain = false;
            
            foreach( $block_domains as $prov ) {
                if( strtolower( $domain ) !== strtolower( $prov['txt_domain'] ) ) {
                    continue;
                }

                $is_disposable_domain = true;
                break;
            }

            if( $is_disposable_domain ) {
                return false;
            }

        }





        /**
         * If we reach here so 
         * 
         * 1. No Custom Providers Found
         * 2. Custom Providers don't have the email domain in the black list
         * 
         * Use the APIs anyway
         * */
        if( ! array_key_exists( 'defender_api_keys', $plugin_settings ) ) {
            
            return new \WP_Error( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '_apikey_is_missing' ), esc_html__( 'API Keys Tab is missing, Please Contact the Administrator with this message.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) );

        }

        $validate_by_vendor = array_key_exists( 'ddl_validate_by_vendor', $plugin_settings['defender_api_keys'] ) ? 
            $plugin_settings['defender_api_keys']['ddl_validate_by_vendor'] :
            'all';


        $disable_defender_when_reach_limit = array_key_exists( 'sw_disable_defender_when_reach_limit', $plugin_settings['defender_api_keys'] ) ? 
            HelperFactory::instance()->cast_bool( $plugin_settings['defender_api_keys']['sw_disable_defender_when_reach_limit'] ) :
            false;

        
        /**
         * VI: Unique Keys with names and Text fields in Plugin settings
         * 
         * @var array
         * */
        $available_vendors = [
            'block_temporary_email'  => [
                'name'      => __( 'Block Temporary Email', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
                'field_id'  => 'txt_block_temp_email_api_key',
            ],
            'quick_email_verification'  => [
                'name'      => __( 'Quick Email Verification', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
                'field_id'  => 'txt_quick_email_verification_api_key',
            ],
        ];

        
        /**
         * Have you found API Key?
         * 
         * @var     bool
         * */
        $api_found  = false;
        
        /**
         * - WP_Error   > Limit | Other Issue
         * - False      > Disposable Email
         * - True       > Valid Email
         * 
         * @var     \WP_Error|Boolean
         * */
        $is_valid   = true;
        

        foreach( $available_vendors as $vendor_id => $details ) {
        
            $field_apikey = $details['field_id'];
     
            if( ( 'all' !== (string) $validate_by_vendor ) && ( (string) $validate_by_vendor !== (string) $vendor_id ) ) {
                continue;
            }

            if( ! array_key_exists( $field_apikey, $plugin_settings['defender_api_keys'] ) ||
                empty( $plugin_settings['defender_api_keys'][ $field_apikey ] ) ) {
                
                continue;

            }

            $api_found = true;

            switch( $vendor_id ) {
                
                case 'block_temporary_email': {
                    $is_valid = Server_BlockTemporaryEmail::instance()->is_valid_email( $plugin_settings, $plugin_settings['defender_api_keys'][ $field_apikey ], $email );
                    } break;

                case 'quick_email_verification': {
                    $is_valid = Server_QuickEmailVeification::instance()->is_valid_email( $plugin_settings, $plugin_settings['defender_api_keys'][ $field_apikey ], $email );
                    } break;

            }


            // Disposable from the first API, Do NOT Continue
            if( FALSE === $is_valid ) {
                return false;
            }

        }

        // Case 01: APIs Are Missing - Always Valid
        if( ! $api_found ) {
            return true;
        }

        // Case 02: Email is Valid
        if( TRUE === $is_valid ) {
            return $is_valid;
        }

        /* Case 03: WP_Error */
        if( $disable_defender_when_reach_limit ) {
            
            // Always valid
            return true;
        }

        // WP Error
        return $is_valid;

    }

}