<?php
namespace UltimateEmailValidator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Fire Up the entire plugin after pass all activation tests
 *
 * @version 1.0
 *
 * @author Oxibug
 *
 */
class HelperFactory {

    /**
     * Static instance of the main plugin class
     *
     * @var HelperFactory
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
     * Clone.
     *
     * Disable class cloning and throw an error on object clone.
     *
     * The whole idea of the singleton design pattern is that there is a single
     * object. Therefore, we don't want the object to be cloned.
     *
     * Cloning instances of the class is forbidden.
     *
     * @access public
     * @since 1.0.0
     */
	public function __clone() {

		_doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), '1.0.0' );
	}

	/**
     * Wakeup.
     *
     * Disable unserializing of the class.
     * Unserializing instances of the class is forbidden.
     *
     * @access public
     * @since 1.0.0
     */
	public function __wakeup() {

		_doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), '1.0.0' );
	}


    /**
     * 
     * 
     * @return HelperFactory
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
     * Get Blog ID
     * -------
     * Return
     * -------
     * 0        :   If NOT Multisite
     * -404     :   Error | Multisite - Defined Blog not exist or not numeric
     * #NUMBER  :   Multisite blog ID
     * 
     * @return  \double|integer|null|string
     * 
     * @since   1.0.0
     * @access  public
     */
    public function Get_Defined_BlogID() {
        
        global $wpdb;

        if( ! is_multisite() ) {
            return 0;
        }
        
        /*
         * DO NOT use quotes for this constant which
         * is represents the constant name that must provided by
         * developer in {wp-config.php}
         *
         * */
        $defined_blog = ( defined(ULTIMATE_EMAIL_VALIDATOR_ROOT_BLOG_ID) && ( is_numeric(constant(ULTIMATE_EMAIL_VALIDATOR_ROOT_BLOG_ID)) ) && ( intval(constant(ULTIMATE_EMAIL_VALIDATOR_ROOT_BLOG_ID)) > 0 ) ) ? constant( ULTIMATE_EMAIL_VALIDATOR_ROOT_BLOG_ID ) : -1;

        if( -1 === $defined_blog ) {
            return -404;
        }

        if( FALSE !== ( $obj_blog = get_blog_details( $defined_blog, false ) ) ) {
            return $obj_blog->blog_id;
        }

        return -404;
        
    }


    public function get_relative_classname( $class ) {
        

        $relative_class_name = preg_replace( '/^' . __NAMESPACE__ . '\\\/', '', $class );

        return __NAMESPACE__ . '\\' . $relative_class_name;

    }

    /**
     * Check https scheme in the home URI
     * This function checks the {https} wrd in the uri only,
     * We don't care about the it's expired or not
     * 
     * WordPress function: {is_ssl} doesn�t work behind some load balancers.
     * 
     * @return  boolean
     * 
     * @since   1.0.0
     * @access  public
     */
    public function is_https() {
        
        $check_page = home_url('/');

        return ( 'https' === substr( $check_page, 0, 5 ) );
        
    }


    /**
     * Convert {supported_until} and {today} into RFC3339 datetime format and compare
     * 
     * @param   string  $supported_until        Timespan | MySQL datetime format
     * @param   string  $custom_dt_format       WordPress datetime format
     * @param   int     $support_status_code    Reference variable with (-1, 0, 1) codes
     * -1   : Support is null (Expired)
     * 0    : Date available but has Expired
     * 1    : Supported
     * 
     * @param   string  $readable_date          Reference string with readable date
     * 
     * 
     * @return  string 
     *      valid date: {supported_until} | If Expired: {supported_until} - Expired!
     *      null|empty: Support has Expired
     *      
     * @since   1.0.0
     * @access  public
     * 
     */
    public function readable_support_status( $supported_until, $custom_dt_format = null, &$support_status_code = 0, &$readable_date = null ) {
        
        if( ! $supported_until ) {
            $support_status_code = -1;
            return esc_html__( 'Support has Expired!', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN );
        }

        if( is_null( $custom_dt_format ) ) {
            $custom_dt_format = get_option('date_format');
        }

        $date_to_compare = DateTimeFactory::instance()->convert_timestamp( $supported_until, TRUE );
        
        $readable_date = DateTimeFactory::instance()->convert_timestamp( $supported_until, FALSE, FALSE, FALSE, $custom_dt_format );

        $today = current_time( 'timestamp' );
        
        $today = DateTimeFactory::instance()->convert_timestamp( $today, TRUE );

        if( $date_to_compare > $today ) {
            $support_status_code = 1;
            return $readable_date;
        }

        $support_status_code = 0;
        return sprintf( '%1$s (%2$s)', $readable_date, esc_html__( 'Expired!', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) );

    }


    /**
     *
     * Encode array to base64 format, It'll convert the array into JSON format then decode it using base64encode function
     *
     * @param   array $value
     *
     * @return  string Encoded base64 forrmat
     *
     * @deprecated devcore_base64_encode_array
     * @since   1.0.0
     * @access  public
     */
    public function maybe_base64_encode( $array ) {

        $final_output = '';

        $json_array = @wp_json_encode($array);

        if( !empty( $json_array ) ) {

            $final_output = @base64_encode($json_array);

        }

        return $final_output;

    }


    /**
     * Sanitize input using [sanitize_text_field], Then Decode arrays that had encoded with [self::_base64EncodeArray] function and return the normal array
     *
     * @param   string $base64_format_string Encoded base64 string
     *
     * @return  null|array
     *
     * @deprecated  devcore_base64_decode_string_array
     * @since   1.0.0
     * @access  public
     */
    public function maybe_base64_decode( $base64_format_string = '' ) {

        if( empty($base64_format_string) ) {
            return null;
        }

        $base64_format_string = sanitize_text_field( $base64_format_string );

        $data_decoded = @base64_decode( $base64_format_string );

        if( ! empty( $data_decoded ) ) {

            $data_json_decoded = self::$_instance->parse_json_params( $data_decoded, $JSON_Errors );

            if( FALSE !== $data_json_decoded ) {

                return (array)$data_json_decoded;

            }

        }

        return null;

    }
    

    /**
     * Use PHP explode function but make agressive checks and return
     *
     * @param mixed $delimiter
     * @param mixed $user_entry
     * @param mixed $output_items_types
     *
     * @return \array|null
     *
     * @since   1.0.0
     * @access  public
     */
    public function explode_entry( $delimiter, $user_entry, $output_items_types = 'any' ) {

        if( ( ! $delimiter ) || ( ! $user_entry ) ) {
            return null;
        }

        if( is_array( $exploded = @explode( $delimiter, $user_entry ) ) ) {

            $final = array();

            switch( $output_items_types ) {

                case 'int':
                case 'integer': {

                        foreach( $exploded as $item ) {
                            if( is_numeric( $item ) ) {
                                $final[] = $item;
                            }
                        }

                    } break;


                default: {
                        $final = $exploded;
                    } break;

            }

            return (array)$final;

        }

        return null;

    }



    /**
     * Return the define code to be added in the main project's file
     * Args (NULL) : Return a blueprint of how to define constant code .. 
     * 
     * Example: "if( ! defined( 'CONST_{SLUG}_{ITEM_ID}_ID' ) ) { define( 'CONST_{SLUG}_{ITEM_ID}_ID', {ITEM_ID} ); }"
     * 
     * Args ( array )
     *  - slug
     *  - item_id
     * 
     * Return:
     * 
     * if( ! define( 'CONST_{SLUG}_{ITEM_ID}_ID' ) ) { define( 'CONST_{SLUG}_{ITEM_ID}_ID', {ITEM_ID} ); }
     * 
     * @param   string          $for    Accepts {item_id} {item_ver}
     * @param   array|null      $args   Some options to modify the returned string
     * 
     * @return  boolean
     * 
     * @since   1.0.0
     * @access  public
     * 
     */
    public function define_constant_code( $for = null, $args = null ) {

        $default_args = array(
            'slug'              => '',
            'item_id'           => 0,
            'scrt_ver'          => '',
        );

        if( is_null( $args ) ) {

            switch( $for ) {
                
                case 'item_id': {
                        return "if( ! defined( 'CONST_{SLUG}_{ITEM_ID}_ID' ) ) { define( 'CONST_{SLUG}_{ITEM_ID}_ID', {ITEM_ID} ); }";
                    } break;

                case 'item_ver': {
                        return "if( ! defined( 'CONST_{SLUG}_{ITEM_ID}_VERSION' ) ) { define( 'CONST_{SLUG}_{ITEM_ID}_VERSION', {SENDBACK_VERSION} ); }";
                    } break;

            }

        }
        else {
            
            $args = wp_parse_args( $args, $default_args );

            $slug       = $args['slug'];
            $item_id    = $args['item_id'];
            $scrt_ver   = $args['scrt_ver'];

            switch( $for ) {
                
                case 'item_id': {
                        return "if( ! defined( 'CONST_{$slug}_{$item_id}_ID' ) ) { define( 'CONST_{$slug}_{$item_id}_ID', '$item_id' ); }";
                    } break;

                case 'item_ver': {
                        return "if( ! defined( 'CONST_{$slug}_{$item_id}_VERSION' ) ) { define( 'CONST_{$slug}_{$item_id}_VERSION', '$scrt_ver' ); }";
                    } break;

            }

        }

        return null;

    }
    
    /**
     * Return the defined code example to retrieve the contant value
     * 
     * Args (NULL) : Return a blueprint of defined constant code "( defined( 'CONST_{SLUG}_{ITEM_ID}_ID' ) ) ? CONST_{SLUG}_{ITEM_ID}_ID : null;"
     * 
     * Args ( array )
     *  - slug
     *  - item_id
     *  - append_letter     { ; | , }
     * 
     * Return:
     * 
     * ( defined( 'CONST_{SLUG}_{ITEM_ID}_ID' ) ) ? CONST_{SLUG}_{ITEM_ID}_ID : null {Appended Letter}
     * 
     * @param   string          $for    Accepts {item_id} {item_ver}
     * @param   array|null      $args   Some options to modify the returned string
     * 
     * -- Arguments
     * 1. uppercase
     * 2. allow_dashes
     * 3. allow_underscores
     * 
     * @return  boolean
     * 
     * @since   1.0.0
     * @access  public
     * 
     */
    public function get_defined_constant_code( $for = null, $args = null ) {

        $default_args = array(
            'slug'              => null,
            'item_id'           => null,
            'append_letter'     => ''
        );

        if( is_null( $args ) ) {

            switch( $for ) {
                
                case 'item_id': {
                        return "( defined( 'CONST_{SLUG}_{ITEM_ID}_ID' ) ) ? CONST_{SLUG}_{ITEM_ID}_ID : null;";
                    } break;

                case 'item_ver': {
                        return "( defined( 'CONST_{SLUG}_{ITEM_ID}_VERSION' ) ) ? CONST_{SLUG}_{ITEM_ID}_VERSION : '0.0.0';";
                    } break;

            }

        }
        else {
            
            $args = wp_parse_args( $args, $default_args );

            $slug       = $args['slug'];
            $item_id    = $args['item_id'];
            $append_ltr = $args['append_letter'];

            switch( $for ) {
                
                case 'item_id': {
                        return "( defined( 'CONST_{$slug}_{$item_id}_ID' ) ) ? CONST_{$slug}_{$item_id}_ID : null{$append_ltr}";
                    } break;

                case 'item_ver': {
                        return "( defined( 'CONST_{$slug}_{$item_id}_VERSION' ) ) ? CONST_{$slug}_{$item_id}_VERSION : '0.0.0'{$append_ltr}";
                    } break;

            }

        }


        return null;

    }


    /**
     * Lowercase alphanumeric characters, dashes and underscores are allowed
     * 
     * @param   string          $seller_name
     * @param   array|null      $args
     * -- Arguments
     * 1. uppercase
     * 2. allow_dashes
     * 3. allow_underscores
     * 
     * @return  boolean
     * 
     * @since   1.0.0
     * @access  public
     * 
     */
    public function sanitize_seller_name( $seller_name = null, $args = '' ) {

        $args_default = array(
            'to_upper'          => false,  // sanitize_key always lower
            'allow_dashes'      => true,
            'allow_underscores' => true
        );

        $args = wp_parse_args( $args, $args_default );

        $seller_name = sanitize_key( $seller_name );
        
        if( true === $args['to_upper'] ) {
            $seller_name = strtoupper( $seller_name );            
        }
        
        if( ! $args['allow_dashes'] ) {
            $seller_name = str_replace( '-', '', $seller_name );
        }

        if( ! $args['allow_underscores'] ) {
            $seller_name = str_replace( '_', '', $seller_name );
        }

        return $seller_name;

    }

    /**
     * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
     * Non-scalar values are ignored.
     *
     * This sanitize one dimensional arrays only, It does NOT work work Key=>Value arrays
     * 
     * @param string|array $var Data to sanitize.
     * @return string|array
     */
    public function sanitize_text_field_deep( $var ) {

	    if ( is_array( $var ) ) {
		    return array_map( array( $this, 'sanitize_text_field_deep' ), $var );
	    } else {
		    return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	    }
    }
    
    
    /**
     * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
     * Non-scalar values are ignored.
     *
     * This sanitize one dimensional arrays only, It does NOT work work Key=>Value arrays
     * 
     * @param string|array $var Data to sanitize.
     * @return string|array
     */
    public function get_term_deep( $object_id, $taxonomy ) {

        $output = '';

        $item_cats = wp_get_object_terms( $object_id, $taxonomy, 
            array(
                'orderby' => 'name', 
                'order' => 'ASC', 
                'fields' => 'ids'
            ) );


        foreach( $item_cats as $term_id ) {

            $term = get_term( $term_id, $taxonomy );

            if( $term->parent > 0 ) {
                
                $my_parents = $parent_ids = array();
                $p_id = $term->parent;
                
                /**
                 * 
                 * @var     \WP_Term
                 * */
                $obj_term_parent = null;

                while( $p_id ) {
                    
                    $obj_term_parent = get_term( $p_id, $taxonomy );
                    $my_parents[] = $obj_term_parent;
                    $p_id = $obj_term_parent->parent;

                    if ( in_array( $p_id, $parent_ids ) ) { // Prevent parent loops.
                        break;
                    }
                    $parent_ids[] = $p_id;
                }
                unset( $parent_ids );


                $final_term_ids = array();
                $final_terms = array();

                $num_parents = count( $my_parents );

                while( $obj_term_parent = array_pop( $my_parents ) ) {
                    
                    $final_term_ids[]   = $obj_term_parent->term_id;
                    $final_terms[]      = $obj_term_parent->name;

                    $num_parents--;
                }


                $output[ $object_id ] = array(
                    'term_ids'      => $final_term_ids,
                    'term_name'     => null,
                    'final_name'    => join( '\\', $final_terms )
                );

            }

            $output[ $object_id ] = array(
                'term_ids'      => $term->term_id,
                'term_name'     => $term->name,
                'final_name'    => $term->name
            );
            
        }


        return $output;

    }




    /**
     * Get the last key of an array
     * - We're using it to get the last code in array like {success_operations} in verify and activate, etc... functions
     * -
     * - Steps of PHP functions:
     * 
     * 1. end
     * 2. key
     * 3. reset
     * 
     * @param   array       $input
     * @param   integer     $key_on_failure
     * 
     * @return  boolean|mixed
     * 
     * @since   1.0.0
     * @access  public
     * 
     */
    public function get_last_key( $input = array(), $key_on_failure = 199 ) {

        if ( ! is_array( $input ) || ( count( $input ) == 0 ) ) {
            return $key_on_failure;
        }

        end( $input );
        $key = key( $input );

        reset( $input );

        return $key;

    }

    
    /**
     * Parses the JSON parameters.
     *
     * Avoids parsing the JSON data until we need to access it.
     *
     * Return false on failure and fill the reference param {$errors} with an array of errors
     *
     * @param   string  $body       The JSON string to be decoded into an array
     * @param   array   $errors     A reference variable to store errors array
     *
     * @since 1.0.0 Returns error instance if value cannot be decoded.
     *
     * @return false|array True if the JSON data was passed or no JSON data was provided, WP_Error if invalid JSON was passed.
     */
	public function parse_json_params( $body = '', &$errors = null ) {

        if ( empty( $body ) ) {
			$errors = array(
                400     => esc_html__( 'Empty body to be convert.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
            );

            return false;
		}

		$params = json_decode( $body, true );

		/*
         * Check for a parsing error.
         *
         * Note that due to WP's JSON compatibility functions, json_last_error
         * might not be defined: https://core.trac.wordpress.org/ticket/27799
         */
		if ( null === $params && ( ! function_exists( 'json_last_error' ) || JSON_ERROR_NONE !== json_last_error() ) ) {

			$errors = array(
                400     => esc_html__( 'Invalid JSON body passed.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
			);

			if ( function_exists( 'json_last_error' ) ) {
				$errors[401]    = json_last_error();
				$errors[402]    = json_last_error_msg();
			}

			return false;

		}

        return $params;

	}

    /**
     * Check and return compare operators to use in required fields
     * 
     * @param   string $compare_operator 
     * 
     * @return  string
     * 
     * @since   1.0.0
     * @access  public
     */
    public function check_compare_operator( $compare_operator ) {
        
        if ( ! in_array( $compare_operator, array(
			
            '=', '!=', '>', '>=', '<', '<='

		) ) ) {
			return '=';
		}

        return $compare_operator;

    }


    /**
     * Check and return compare operators to use in required fields
     * 
     * @param   string $compare_operator 
     * 
     * @return  bool
     * 
     * @since 1.0.0
     * @access public
     */
    public function compare_required_value( $user_input, $required_value, $compare_operator ) {
        
        switch( $compare_operator ) {
            
            case '=':
            case '==': {
                    
                    return ( $user_input == $required_value );
                }
            
            case '!=': {
                    
                    return ( $user_input != $required_value );
                }
            
            case '>': {
                    return ( $user_input > $required_value );
                }
            
            case '>=': {
                    return ( $user_input >= $required_value );
                }
            
            case '<': {
                    return ( $user_input < $required_value );
                }     

            case '<=': {
                    return ( $user_input <= $required_value );
                }

        }

        return false;

    }


    /**
     * Helper function: Cast a value to bool
     *
     * @since 2.5.0
     *
     * @static
     *
     * @param mixed $value Value to cast.
     * @return bool
     */
    public function cast_bool( $value ) {

        // @codingStandardsIgnoreStart
        $true  = array(
        '1',
        'true', 'True', 'TRUE',
        'y', 'Y',
        'yes', 'Yes', 'YES',
        'on', 'On', 'ON',
        );
        
        $false = array(
        '0',
        'false', 'False', 'FALSE',
        'n', 'N',
        'no', 'No', 'NO',
        'off', 'Off', 'OFF',
        );
        
        // @codingStandardsIgnoreEnd
        if ( is_bool( $value ) ) {
            return $value;
        } else if ( is_int( $value ) && ( 0 === $value || 1 === $value ) ) {
            return (bool) $value;
        } else if ( ( is_float( $value ) && ! is_nan( $value ) ) && ( (float) 0 === $value || (float) 1 === $value ) ) {
            return (bool) $value;
        } else if ( is_string( $value ) ) {

            $value = trim( $value );
            if ( in_array( $value, $true, true ) ) {
                return true;
            } else if ( in_array( $value, $false, true ) ) {
                return false;
            } else {
                return false;
            }

        }

        return false;

    }
    

    /**
     * Get all rents of certain term uses the implementation of WordPress function: {get_term_parents_list}
     * 
     * Return array of term IDs
     * 
     * @param   int     $term_id 
     * @param   string  $taxonomy 
     * @param   array   $args 
     * 
     * @return  \array|\WP_Error
     * 
     * @since   1.0.0
     * @access  public
     */
    public function get_term_parents( $term_id, $taxonomy, $args = array() ) {

        $list = array();
        $term = get_term( $term_id, $taxonomy );
        
        if ( is_wp_error( $term ) ) {
            return $term;
        }
        
        if ( ! $term ) {
            return $list;
        }
        
        $term_id = $term->term_id;
        
        $defaults = array(
            'format'    => 'name',
            'inclusive' => true,
        );
        
        $args = wp_parse_args( $args, $defaults );
        
        foreach ( array( 'inclusive' ) as $bool ) {
            $args[ $bool ] = wp_validate_boolean( $args[ $bool ] );
        }
        
        $parents = get_ancestors( $term_id, $taxonomy, 'taxonomy' );
        
        if ( $args['inclusive'] ) {
            array_unshift( $parents, $term_id );
        }

        foreach( array_reverse( $parents ) as $p_id ) {

		    $parent = get_term( $p_id, $taxonomy );
		    $name   = ( 'slug' === $args['format'] ) ? $parent->slug : $parent->name;

			$list[] = $name;
		    
	    }

        return $list;

    }


    /**
     * Get all Built-in WordPress Roles
     * 
     * @return  array
     * 
     */
    public function get_wp_roles() {
        
        $wp_roles = wp_roles();

        return $wp_roles->get_names();

    }
    
    /**
     * Get all Built-in WordPress Roles
     * 
     * @return  array
     * 
     */
    public function get_wp_capabilities() {
        
        $wp_roles = wp_roles();
        
        $admin_caps = array();

        if( is_multisite() ) {
            $admin_caps['manage_network_options'] = esc_html__( 'Manage Network Options', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN );
        }

        if( array_key_exists( 'administrator', $wp_roles->role_objects ) ) {
            
            foreach( $wp_roles->role_objects['administrator']->capabilities as $cap_name => $cap_status ) {
                
                $admin_caps[ $cap_name ] = ucwords( str_replace( '_', ' ', $cap_name ) );

            }

        }

        if( count( $admin_caps ) > 0 ) {
            return (array) $admin_caps;
        }

        return null;

    }

    /**
     * Return encrypted email with *
     * Example: Ga********@g****.com
     * 
     * @param   string  $email 
     * @return  \null|string
     * 
     * @since   1.0.0
     * @access  public
     */
    public function hide_email( $email ) {
        
        if( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
            return null;
        }

        list($first, $last) = explode('@', $email);

        /* Hide part before { @ } */
        $first = str_replace( substr($first, 2), str_repeat('*', strlen($first) - 2), $first );
        
        /* Split domain [xyz.com] */
        $last = explode('.', $last);
        /* hide the domain name */
        $last_domain = str_replace(substr($last['0'], 1), str_repeat('*', strlen($last[0]) - 1), $last[0]);
        
        /* return encrypted email */
        return sprintf( '%s@%s.%s', $first, $last_domain, $last[1] );

    }



    /**
     * Return the {CUSTOM_PLUGIN_UPLOADS_DIR} directory in {uploads} dir
     * 
     * @param   bool    $use_trailingslashit    Add {\} at the end of returned string?
     * @param   bool    $base_url               Use the URL {https://...../CUSTOM_PLUGIN_UPLOADS_DIR/} or the base dir path {/var/www/.../CUSTOM_PLUGIN_UPLOADS_DIR/} ? 
     * 
     * 
     * @return  \null|string
     * 
     * @since   1.0.0
     * @access  public
     */
    public function get_filesystem_uploads_dir_root( $use_trailingslashit = false, $base_url = false ) {
        
        $uploads_dir = wp_upload_dir();

        $path = trailingslashit( $uploads_dir['basedir'] );

        if( $base_url ) {
            $path = trailingslashit( $uploads_dir['baseurl'] );
        }


        if( $use_trailingslashit ) {
            return sprintf( '%s%s/', $path, ULTIMATE_EMAIL_VALIDATOR_UPLOADS_DIR_ROOT_DIR );
        }

        return sprintf( '%s%s', $path, ULTIMATE_EMAIL_VALIDATOR_UPLOADS_DIR_ROOT_DIR );

    }
    

    /**
     * Check whether file path extension is exist in the provided string or array of {$extensions}
     * And return an array of file information
     *
     * -- Returned Array contains keys
     *
     *  - dirname
     *  - basename
     *  - extension
     *  - filename
     *
     * @param string $file_path
     *
     * @param string|array $extensions
     *
     * @return array|null
     *
     */
    public function check_file_ext( $file_path = null, $extensions = null ) {

        if( is_null( $file_path ) || is_null( $extensions ) ) {
            return null;
        }

        $path_parts = pathinfo( $file_path );

        if( is_array( $path_parts ) && array_key_exists( 'extension', $path_parts ) ) {

            if( is_string( $extensions ) ) {

                return ( $extensions == $path_parts['extension'] ) ? $path_parts : null;

            }
            elseif( is_array( $extensions ) ) {

                return ( in_array( $path_parts['extension'], $extensions ) ) ? $path_parts : null;

            }

            return null;

        }

        return null;

    }

    
}