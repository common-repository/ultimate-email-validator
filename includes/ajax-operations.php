<?php
namespace UltimateEmailValidator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * All operations could perform on any AJAX variables
 *
 * @version 1.0
 * @author  Oxibug
 *
 */
class AJAX_Operations {

    /**
     * An instance of the class
     *
     * @since 1.0
     *
     * @var AJAX_Operations
     *
     */
    private static $_instance = null;

    /**
     * Instantiate in WordPress action [ init ] in [ engine.php ]
     *
     * NOTE: is_admin() applied in Engine class
     *
     * @since 1.0
     *
     * @return AJAX_Operations
     *
     */
    public static function instance() {

        if( is_null( self::$_instance ) ) {

            self::$_instance = new self;

            self::$_instance->core_actions();

        }

        return self::$_instance;

    }


    /**
     *
     *
     * @since 1.0
     *
     */
    private function core_actions() {

    }


    /**
     * Using {wp_send_json} to send errors array in JSON format
     *
     * @uses    wp_send_json        Using WordPress function to send back the respond as JSON format and die
     * 
     * @param   bool $success       The status success or not
     * @param   array $messages     Messages Codes => Description key value pairs array
     *
     * @return  void
     */
    public function die_messages( $success = false, $messages = array() ) {

        $erros_codes = array(

            'success'   => $success,
            'codes'     => $messages

        );

        wp_send_json( $erros_codes );

    }
    
    
    /**
     * Echo <divs> with error codes instead of using JSON 
     * to send errors for AJAX actions do not use JSON dataType
     *
     * @param   bool    $success       The status success or not
     * @param   array   $messages     Messages Codes => Description key value pairs array
     *
     * @return  void
     */
    public function echo_die_messages( $success = false, $messages = array(), $args = array() ) {
        
        $args_default = array(
            'box_title'     => null,
            'show_code'     => true
        );

        $args = wp_parse_args( $args, $args_default );

        $cls_issues = array(
            'issues-response'
        );
        
        $cls_issues[] = $success ? 'success' : 'fail'; ?>

        <div class="<?php echo join( ' ', $cls_issues ); ?>">

            <?php if( ( ! is_null( $args['box_title'] ) ) && ( ! empty( $args['box_title'] ) ) ) { ?>
                <div class="title-box"><?php echo esc_html( $args['box_title'] ); ?></div>
            <?php }

            foreach( $messages as $code => $description ) { ?>
            
                <div class="msg-box d-flex">
                    
                    <?php if( $args['show_code'] ) { ?>
                        <div class="part code"><?php echo ( is_numeric( $code ) ) ? esc_attr( $code ) : esc_html( $code ); ?></div>
                    <?php } ?>

                    <div class="part desc"><?php echo wp_kses_post( $description ); ?></div>
                </div>

            <?php } ?>

        </div>

        <?php wp_die();

    }


    /**
     * Using {wp_send_json} to send errors array in JSON format
     *
     * @uses    die_messages        Using WordPress function to send back the respond as JSON format and die
     * @uses    echo_die_messages   Using local function to echo {issues-response}
     *
     * @param   bool    $check_admin_page   Check if in admin pages
     * @param   bool    $return_json        True: Return result as JSON, Otherwise Echo divs in function {SELF :: echo_die_messages}
     * 
     * @return  void
     */
    public function test_ajax_request( $check_admin_page = false, $return_json = false ) {

        if( $check_admin_page && ! is_admin() ) {

            if( $return_json ) {                
                self::$_instance->die_messages( false, array(
                    900     => esc_html__( 'AJAX: Invalid Request! This operation must applied through admin pages only.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN )
                ) );
            }
            else {
                self::$_instance->echo_die_messages( false, array(
                    900     => esc_html__( 'AJAX: Invalid Request! This operation must applied through admin pages only.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN )
                ) );
            }
        }

        if( ! check_ajax_referer( ULTIMATE_EMAIL_VALIDATOR_AJAX_NONCE_ACTION, ( ULTIMATE_EMAIL_VALIDATOR_SLUG_AJAX_VARS . '_security' ), false) ) {

            if( $return_json ) {                
                self::$_instance->die_messages( false, array(
                    900     => esc_html__( 'AJAX: Invalid Request! Please reload the page an try again', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN )
                ) );
            }
            else {
                self::$_instance->echo_die_messages( false, array(
                    900     => esc_html__( 'AJAX: Invalid Request! Please reload the page an try again', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN )
                ) );
            }

        }

    }
    

    /**
     * 1. Apply ULTIMATE_EMAIL_VALIDATOR_SLUG_AJAX_VARS constant before the variable name
     * Ex: ULTIMATE_EMAIL_VALIDATOR_SLUG_AJAX_VARS . '_' . variable
     * 
     * ---------------------------
     * 
     * 2. Check existance of $_POST key in $_POST array 
     * then apply {wp_unslash} on it
     * 
     * VI NOTE: DO NOT apply any sanitization here because of some JSON
     * values returned from JS
     * 
     * --
     * For Types:
     * string:  Just Return it
     * bool:    Apply filter {FILTER_VALIDATE_BOOLEAN} 
     * int:     Apply filter {FILTER_VALIDATE_INT} 
     * 
     * @param   string    $var_name
     * @param   string    $type               string | ( int - integer ) | ( bool - boolean )
     * @param   mixed     $default            The default value to return if any error happened
     * @param   bool      $trim               Trim white spaces from value?
     * @param   string    $sanitize_callback  The sanitization function to apply on the value result
     * 
     * @return  mixed
     * 
     * @since   1.0.0
     * @access  public
     * 
     */
    public function secure_post_var( $var_name = '', $type = 'string', $default = '', $trim = TRUE, $sanitize_callback = 'sanitize_text_field' ) {
        
        $var_name = ULTIMATE_EMAIL_VALIDATOR_SLUG_AJAX_VARS . '_' . $var_name;

        if( ! isset( $_POST[ $var_name ] ) ) {
            return $default;
        }

        if( empty( $_POST[ $var_name ] ) ) {
            return $default;
        }

        $value = wp_unslash( $_POST[ $var_name ] );

        if( $trim ) {
            $value = trim( $value );
        }

        if( ! is_null( $sanitize_callback ) && ! empty( $sanitize_callback ) && is_callable( $sanitize_callback ) ) {
            $value = call_user_func( $sanitize_callback, $value );
        }

        switch ($type) {

            case 'string': {
                return $value;
            }

            case 'bool':
            case 'boolean': {
                return filter_var( $value, FILTER_VALIDATE_BOOLEAN ) ? : $default;
            }

            case 'int':
            case 'integer': {
                return filter_var( $value, FILTER_VALIDATE_INT ) ? : $default;
            }

        	default:
                return $value;
        }

    }

}