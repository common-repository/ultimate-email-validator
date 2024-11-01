<?php
namespace UltimateEmailValidator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * An aggressive operations to sanitize user inputs in Admin Panel options
 * and CPT user inputs
 *
 * @author  Oxibug
 * @version 1.0.0
 */
class SanitizationFactory {

    /**
     * Static instance of the main plugin class
     *
     * @var SanitizationFactory
     *
     * @since   1.0.0
     * @access  private
     */
    private static $_instance = null;


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
     * Take an instance
     *
     * @return SanitizationFactory
     */
    public static function instance() {

        if( is_null( self::$_instance ) ) {

            self::$_instance = new self;

        }

        return self::$_instance;

    }

    /**
     * Silent Contructor
     */
    private function __construct() {}




    public function _sanitize_indexed_array( $ele_user_inputs ) {

        if( ( ! is_array( $ele_user_inputs ) ) || ! wp_is_numeric_array( $ele_user_inputs ) || empty( $ele_user_inputs ) ) {
            return array();
        }
        else {

            foreach( $ele_user_inputs as &$value ) {
                $value = sanitize_text_field( $value );
            }

            return wp_unslash( $ele_user_inputs );

        }

    }



    public function _sanitize_key_value_array( $ele_user_inputs ) {

        /*
         * DO NOT check {wp_is_numeric_array} because there's elements return
         * Keys as IDs like multi-checkbox for categories so it always return an empty array
         *
         * */
        if( ( ! is_array( $ele_user_inputs ) ) || empty( $ele_user_inputs ) ) {
            return array();
        }
        else {

            $ele_user_input_sanitized = array();

            foreach( $ele_user_inputs as $key => $value ) {
                $new_key = sanitize_key( $key );
                $ele_user_input_sanitized[ $new_key ] = sanitize_text_field( $value );
            }

            return wp_unslash( $ele_user_input_sanitized );

        }

    }



    /**
     * Check and sanitize repeater elements values
     * This function is a helper for main function {sanitize_user_inputs}
     *
     * @param   array $rep_elements_map
     * @param   array $live_rep_item
     *
     * @return  \array|null
     *
     * @since   1.0.0
     * @access  private
     *
     */
    private function _sanitize_repeater_item( $rep_elements_map, $live_rep_item ) {

        $final = array();

        foreach( $rep_elements_map as $rep_ele ) {

            switch( $rep_ele['type'] ) {

                /* Mini Rep - Does NOT work now - Maybe in the future */
                case 'repeater': {

                        /* In case the repeater is Empty */
                        if( ! isset( $live_rep_item[ $rep_ele['id'] ] ) ) {
                            continue 2;
                        }

                        /* VI: After check {isset} to avoid {Undefined Index} of any empty custom repeaters */
                        $rep_user_inputs = wp_unslash( $live_rep_item[ $rep_ele['id'] ] );


                        /* This works now - The {group} may have repeaters */

                        /*
                         * Temporary Sanitization - Apply {sanitize_text_field} for all repeater elements
                         * Until we make a complicated MAP for repeaters inside groups
                         *
                         * VI NOTE: {$rep_fields} MUST BE reference, Otherwise the $field_value won't modified
                         *
                         * */
                        if( wp_is_numeric_array( $rep_user_inputs ) ) {

                            foreach( $rep_user_inputs as &$rep_fields ) {

                                foreach( $rep_fields as $field_id => &$field_value ) {

                                    $field_value = sanitize_text_field( $field_value );
                                }

                            }

                            $final[ $rep_ele['id'] ] = $rep_user_inputs;

                        }
                        else {

                            $final[ $rep_ele['id'] ] = array();

                        }

                    } break;

                case 'checkbox': {

                        if( ! isset( $live_rep_item[ $rep_ele['id'] ] ) ) {
                            $final[ $rep_ele['id'] ] = 0;
                        }
                        else {
                            $final[ $rep_ele['id'] ] = sanitize_text_field( $live_rep_item[ $rep_ele['id'] ] );
                        }

                    } break;


                case 'multi-select': {

                        /* Output is numeric indexed Array */
                        if( ! isset( $live_rep_item[ $rep_ele['id'] ] ) ) {
                            $final[ $rep_ele['id'] ] = array();
                            continue 2;
                        }

                        $final[ $rep_ele['id'] ] = self::$_instance->_sanitize_indexed_array( $live_rep_item[ $rep_ele['id'] ] );

                    } break;


                case 'range-slider':
                case 'multi-checkbox':
                case 'upload-media': {

                        /* Output is Key => Value Array */
                        if( ! isset( $live_rep_item[ $rep_ele['id'] ] ) ) {
                            $final[ $rep_ele['id'] ] = array();
                            continue 2;
                        }

                        $final[ $rep_ele['id'] ] = self::$_instance->_sanitize_key_value_array( $live_rep_item[ $rep_ele['id'] ] );

                    } break;

                case 'color': {

                        /* Output is Key => Value Array */
                        if( ! isset( $live_rep_item[ $rep_ele['id'] ] ) ) {
                            $final[ $rep_ele['id'] ] = '';
                            continue 2;
                        }

                        $ele_value = $live_rep_item[ $rep_ele['id'] ];

                        if( is_string( $ele_value ) ) {
                            $final[ $rep_ele['id'] ] = sanitize_text_field( $ele_value );
                        }
                        elseif( is_array( $ele_value ) ) {
                            $final[ $rep_ele['id'] ] = self::$_instance->_sanitize_key_value_array( $ele_value );
                        }

                    } break;


                case 'textarea': {
                        $unslashed_text = wp_unslash( $live_rep_item[ $rep_ele['id'] ] );
                        $final[ $rep_ele['id'] ] = wp_kses_post( $unslashed_text );
                    } break;

                default: {

                        $final[ $rep_ele['id'] ] = sanitize_text_field( $live_rep_item[ $rep_ele['id'] ] );

                    } break;

            }

        }


        if( count( $final ) > 0 ) {
            return (array) $final;
        }

        return null;

    }


    /**
     * Check and sanitize group elements values
     * This function is a helper for main function {sanitize_user_inputs}
     *
     * @param   array $group_elements_map
     * @param   array $live_user_inputs
     *
     * @return  \array|null
     *
     * @since   1.0.0
     * @access  private
     *
     */
    private function _sanitize_group_control( $group_elements_map, $live_user_inputs ) {

        $final = array();

        foreach( $group_elements_map as $group_ele ) {

            switch( $group_ele['type'] ) {

                case 'repeater': {

                        /* In case the repeater is Empty */
                        if( ! isset( $live_user_inputs[ $group_ele['id'] ] ) ) {
                            continue 2;
                        }

                        /* VI: After check {isset} to avoid {Undefined Index} of any empty custom repeaters */
                        $rep_user_inputs = wp_unslash( $live_user_inputs[ $group_ele['id'] ] );


                        /* This works now - The {group} may have repeaters */

                        /*
                         * == Temporary Sanitization ==
                         *
                         * Apply {sanitize_text_field} for all repeater elements
                         * Until we make a complicated MAP for repeaters inside groups
                         *
                         * VI NOTE: {$rep_fields} MUST BE reference, Otherwise the $field_value won't modified
                         *
                         * */
                        if( wp_is_numeric_array( $rep_user_inputs ) ) {

                            foreach( $rep_user_inputs as &$rep_fields ) {

                                foreach( $rep_fields as $field_id => &$field_value ) {

                                    $field_value = sanitize_text_field( $field_value );
                                }

                            }

                            $final[ $group_ele['id'] ] = $rep_user_inputs;

                        }
                        else {

                            $final[ $group_ele['id'] ] = array();

                        }

                    } break;

                case 'checkbox': {

                        if( ! isset( $live_user_inputs[ $group_ele['id'] ] ) ) {
                            $final[ $group_ele['id'] ] = 0;
                        }
                        else {
                            $final[ $group_ele['id'] ] = sanitize_text_field( $live_user_inputs[ $group_ele['id'] ] );
                        }

                    } break;

                case 'multi-select': {

                        /* Output is numeric indexed Array */
                        if( ! isset( $live_user_inputs[ $group_ele['id'] ] ) ) {
                            $final[ $group_ele['id'] ] = array();
                            continue 2;
                        }

                        $final[ $group_ele['id'] ] = self::$_instance->_sanitize_indexed_array( $live_user_inputs[ $group_ele['id'] ] );

                    } break;


                case 'range-slider':
                case 'multi-checkbox':
                case 'upload-media': {

                        /* Output is Key => Value Array -- Big Text or an Array */
                        if( ! isset( $live_user_inputs[ $group_ele['id'] ] ) ) {
                            $final[ $group_ele['id'] ] = array();
                            continue 2;
                        }

                        $final[ $group_ele['id'] ] = self::$_instance->_sanitize_key_value_array( $live_user_inputs[ $group_ele['id'] ] );

                    } break;


                case 'color': {

                        /* Output is Key => Value Array */
                        if( ! isset( $live_user_inputs[ $group_ele['id'] ] ) ) {
                            $final[ $group_ele['id'] ] = '';
                            continue 2;
                        }

                        $ele_value = $live_user_inputs[ $group_ele['id'] ];

                        if( is_string( $ele_value ) ) {
                            $final[ $group_ele['id'] ] = sanitize_text_field( $ele_value );
                        }
                        elseif( is_array( $ele_value ) ) {
                            $final[ $group_ele['id'] ] = self::$_instance->_sanitize_key_value_array( $ele_value );
                        }

                    } break;


                case 'textarea': {
                        $unslashed_text = wp_unslash( $live_user_inputs[ $group_ele['id'] ] );
                        $final[ $group_ele['id'] ] = wp_kses_post( $unslashed_text );
                    } break;

                default: {

                        $final[ $group_ele['id'] ] = sanitize_text_field( $live_user_inputs[ $group_ele['id'] ] );

                    } break;

            }

        }


        if( count( $final ) > 0 ) {
            return (array) $final;
        }

        return null;

    }



    /**
     * Check all user inputs values and sanitize it
     *
     * @param   array $user_inputs
     * @param   array $elements_map
     *
     * @return  \array|null
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public function sanitize_user_inputs( $user_inputs, $elements_map, $grouped_with_tabs = TRUE ) {

        $final = array();

        if( $grouped_with_tabs ) {
            
            /* 
             * FIX tabs that has checkboxes only
             * 
             * VI: The tab {import_export} MUST be unset before save
             * 
             * */
            foreach( $elements_map as $tab_key => $tab_elements ) {

                if( ! array_key_exists( $tab_key, $user_inputs ) ) {
                    $user_inputs[ $tab_key ] = array();
                    continue;
                }

            }

            /* Start Operations */
            foreach( $elements_map as $tab_key => $tab_elements ) {

                if( ! array_key_exists( $tab_key, $user_inputs ) ) {
                    continue;
                }

                foreach( $tab_elements as $ele ) {

                    switch( $ele['type'] ) {

                        case 'repeater': {

                                /* In case the repeater is Empty */
                                if( ! isset( $user_inputs[ $tab_key ][ $ele['id'] ] ) ) {
                                    continue 2;
                                }

                                $rep_elements_map = $ele['elements'];

                                /* The repeater SHOULD NOT contains single checkbox */
                                $rep_live_items = $user_inputs[ $tab_key ][ $ele['id'] ];

                                /* Repeater is Special Case - Because each item has one or more element */
                                foreach( $rep_live_items as $rep_item ) {

                                    if( ! is_null( $rep_item_sanitized = self::$_instance->_sanitize_repeater_item( $rep_elements_map, $rep_item ) ) ) {

                                        $final[ $tab_key ][ $ele['id'] ][] = $rep_item_sanitized;

                                    }

                                }

                            } break;


                        case 'gorup': {

                                /* In case the repeater is Empty */
                                if( ! isset( $user_inputs[ $tab_key ][ $ele['id'] ] ) ) {
                                    continue 2;
                                }

                                $group_elements_map = $ele['elements'];

                                /* The repeater SHOULD NOT contains single checkbox */
                                $gorup_live_items = $user_inputs[ $tab_key ][ $ele['id'] ];

                                if( ! is_null( $rep_item_sanitized = self::$_instance->_sanitize_group_control( $group_elements_map, $gorup_live_items ) ) ) {

                                    $final[ $tab_key ][ $ele['id'] ] = $rep_item_sanitized;

                                }

                            } break;


                        case 'checkbox': {

                                if( ! isset( $user_inputs[ $tab_key ][ $ele['id'] ] ) ) {
                                    $final[ $tab_key ][ $ele['id'] ] = 0;
                                }
                                else {
                                    $final[ $tab_key ][ $ele['id'] ] = sanitize_text_field( $user_inputs[ $tab_key ][ $ele['id'] ] );
                                }

                            } break;


                        case 'multi-select': {

                                /* Output is numeric indexed Array -- Big Text or an Array */
                                if( ! isset( $user_inputs[ $tab_key ][ $ele['id'] ] ) ) {
                                    $final[ $tab_key ][ $ele['id'] ] = array();
                                    continue 2;
                                }

                                $final[ $tab_key ][ $ele['id'] ] = self::$_instance->_sanitize_indexed_array( $user_inputs[ $tab_key ][ $ele['id'] ] );

                            } break;


                        case 'range-slider':
                        case 'multi-checkbox':
                        case 'upload-media': {

                                /* Output is Key => Value Array -- Big Text or an Array */
                                if( ! isset( $user_inputs[ $tab_key ][ $ele['id'] ] ) ) {
                                    $final[ $tab_key ][ $ele['id'] ] = array();
                                    continue 2;
                                }

                                $final[ $tab_key ][ $ele['id'] ] = self::$_instance->_sanitize_key_value_array( $user_inputs[ $tab_key ][ $ele['id'] ] );

                            } break;


                        case 'color': {

                            /* Output is Key => Value Array */
                            if( ! isset( $user_inputs[ $tab_key ][ $ele['id'] ] ) ) {
                                $final[ $tab_key ][ $ele['id'] ] = '';
                                continue 2;
                            }

                            $ele_value = $user_inputs[ $tab_key ][ $ele['id'] ];

                            if( is_string( $ele_value ) ) {
                                $final[ $tab_key ][ $ele['id'] ] = sanitize_text_field( $ele_value );
                            }
                            elseif( is_array( $ele_value ) ) {
                                $final[ $tab_key ][ $ele['id'] ] = self::$_instance->_sanitize_key_value_array( $ele_value );
                            }

                        } break;


                        case 'textarea': {

                                $unslashed_text = wp_unslash( $user_inputs[ $tab_key ][ $ele['id'] ] );

                                $final[ $tab_key ][ $ele['id'] ] = wp_kses_post( $unslashed_text );

                            } break;


                        default: {

                                $final[ $tab_key ][ $ele['id'] ] = sanitize_text_field( $user_inputs[ $tab_key ][ $ele['id'] ] );

                            } break;

                    }



                }

            }

        }
        else {

            /*
             * The user inputs without tabs
             * Means single fields like CPT
             *
             * */
            foreach( $elements_map as $ele ) {

                switch( $ele['type'] ) {

                    case 'repeater': {

                            /* In case the repeater is Empty */
                            if( ! isset( $user_inputs[ $ele['id'] ] ) ) {
                                continue 2;
                            }

                            $rep_elements_map = $ele['elements'];

                            /* The repeater SHOULD NOT contains single checkbox */
                            $rep_live_items = $user_inputs[ $ele['id'] ];

                            /* Repeater is Special Case - Because each item has one or more element */
                            foreach( $rep_live_items as $rep_item ) {

                                if( ! is_null( $rep_item_sanitized = self::$_instance->_sanitize_repeater_item( $rep_elements_map, $rep_item ) ) ) {

                                    $final[ $ele['id'] ][] = $rep_item_sanitized;

                                }

                            }

                        } break;


                    case 'group': {

                            /* In case the repeater is Empty */
                            if( ! isset( $user_inputs[ $ele['id'] ] ) ) {
                                continue 2;
                            }

                            $group_elements_map = $ele['elements'];

                            /* The repeater SHOULD NOT contains single checkbox */
                            $group_live_items = $user_inputs[ $ele['id'] ];


                            if( ! is_null( $group_sanitized_control = self::$_instance->_sanitize_group_control( $group_elements_map, $group_live_items ) ) ) {

                                $final[ $ele['id'] ] = $group_sanitized_control;

                            }

                        } break;

                    case 'checkbox': {

                            if( ! isset( $user_inputs[ $ele['id'] ] ) ) {
                                $final[ $ele['id'] ] = 0;
                            }
                            else {
                                $final[ $ele['id'] ] = $user_inputs[ $ele['id'] ];
                            }

                        } break;

                    case 'multi-select': {

                            /* Output is numeric indexed Array -- Big Text or an Array */
                            if( ! isset( $user_inputs[ $ele['id'] ] ) ) {
                                $final[ $ele['id'] ] = array();
                                continue 2;
                            }

                            $final[ $ele['id'] ] = self::$_instance->_sanitize_indexed_array( $user_inputs[ $ele['id'] ] );

                        } break;


                    case 'range-slider':
                    case 'multi-checkbox':
                    case 'upload-media': {

                            /* Output is Key => Value Array -- Big Text or an Array */
                            if( ! isset( $user_inputs[ $ele['id'] ] ) ) {
                                $final[ $ele['id'] ] = array();
                                continue 2;
                            }

                            $final[ $ele['id'] ] = self::$_instance->_sanitize_key_value_array( $user_inputs[ $ele['id'] ] );

                        } break;


                    case 'color': {

                            /* Output is Key => Value Array */
                            if( ! isset( $user_inputs[ $ele['id'] ] ) ) {
                                $final[ $ele['id'] ] = '';
                                continue 2;
                            }

                            $ele_value = $user_inputs[ $ele['id'] ];

                            if( is_string( $ele_value ) ) {
                                $final[ $ele['id'] ] = sanitize_text_field( $ele_value );
                            }
                            elseif( is_array( $ele_value ) ) {
                                $final[ $ele['id'] ] = self::$_instance->_sanitize_key_value_array( $ele_value );
                            }

                        } break;


                    case 'textarea': {
                            $unslashed_text = wp_unslash( $user_inputs[ $ele['id'] ] );
                            $final[ $ele['id'] ] = wp_kses_post( $unslashed_text );
                        } break;

                    default: {

                            /* elements [select] generate false isset when empty */
                            $final[ $ele['id'] ] = ( ! isset( $user_inputs[ $ele['id'] ] ) ) ? '' : sanitize_text_field( $user_inputs[ $ele['id'] ] );

                        } break;

                }

            }

        }


        if( count( $final ) > 0 ) {
            return (array) $final;
        }

        return null;

    }


}