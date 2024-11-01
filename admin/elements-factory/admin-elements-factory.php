<?php
namespace UltimateEmailValidator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * A factory of HTML elements used in Plugin's settings page
 *
 * @author  Oxibug 
 * @version 1.0.0
 */
class Admin_ElementsFactory {

    /**
     * 
     * Represents the equivalent elements names we use in javascript code [$.srpset.element.XXXXX]
     * .
     * Through Filter: [ PLUGIN_MAIN_SLUG/filters/admin/pages/js/element_types ]
     * 
     * @var array
     * 
     */
    public static $js_element_names = array();


    // HTML5
    public static $supported_html5_video = array();
    public static $supported_html5_audio = array();


    // Background Control Parts
    public static $background_repeat = array();
    public static $background_size = array();
    public static $background_attachment = array();
    public static $background_position = array();


    // Social Networks
    public static $social_networks_names = array();

    /**
     * 
     * An object of Admin_ElementsFactory to call non static functions
     * 
     * @var Admin_ElementsFactory
     * 
     */
    private static $_instance = null;


    private static $exclude_controls = array (
        'result', 'button', 'multi-buttons', 'label'
    );


    /**
     * Take an instace of class and initialize the core arrays
     * We need to check before create any control
     * 
     * @return  Admin_ElementsFactory
     * 
     * @since   1.0.0
     * @access  public
     * @static
     */
    public static function instance() {
        
        if( is_null( self::$_instance ) ) {
            
            self::$_instance = new self;
            

            /*
             * Call Initial Functions
             * 
             * */
            self::$js_element_names = self::$_instance->_js_setElementTypes();
            

            // HTML5 Supported Video and Audio Types
            self::$supported_html5_audio = self::$_instance->ReturnHTML5_SupportedAudioTypes();
            self::$supported_html5_video = self::$_instance->ReturnHTML5_SupportedVideoTypes();


            // Background Control Parts
            self::$background_repeat = self::$_instance->backgroundRepeat();
            self::$background_attachment = self::$_instance->backgroundAttachment();
            self::$background_size = self::$_instance->backgroundSize();
            self::$background_position = self::$_instance->backgroundPosition();

            // Social Networks - Names
            self::$social_networks_names = self::$_instance->ReturnSocialNetworks_Names();

        }

        return self::$_instance;

    }


    /** 
     * Silent Constructor 
     * 
     * */
    private function __construct() { }


    /**
     * 
     * Return an array with all element types we use in main javascript file
     * 
     * And pass it through [ PLUGIN_MAIN_SLUG/js/element_types ]
     * 
     * NON Translatable array
     * 
     * @since 1.0
     * 
     * @return array
     * 
     */
    private function _js_setElementTypes() {
        
        return apply_filters( (ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/filters/admin/pages/js/element_types'), array(
            
            // Elements
            'label'             => 'label',
            'password'          => 'password',
            'text'              => 'text',
            'textarea'          => 'textarea',
            'spinner'           => 'spinner',
            'slider'            => 'slider',
            'range-slider'      => 'range_slider',
            'color'             => 'color',
            'icon'              => 'icon',
            'select'            => 'select',
            'multi-select'      => 'multi_select',
            'date-picker'       => 'date_picker',
            'checkbox'          => 'checkbox',
            'switch'            => 'switch',
            'multi-checkbox'    => 'multi_checkbox',
            'radio'             => 'radio',
            'radio-images'      => 'radio_images',
            'wp-taxonomy'       => 'wp_taxonomy',
            'wp-multi-taxonomy' => 'wp_multi_taxonomy',
            'wp-sidebars'       => 'wp_sidebars',
            'wp-users'          => 'wp_users',
            'oembed'            => 'oEmbed',
            
            'upload-media'      => 'upload_media',
            'upload-gallery'    => 'upload_gallery',
            'background'        => 'background',

            'margin'            => 'margin',
            'padding'           => 'padding',
            'border'            => 'border',
            'border-radius'     => 'border_radius',

            // Review
            'review'            => 'review',

            // Repeaters
            'repeater'          => 'repeater',
            'mini-repeater'     => 'mini_repeater',

        ) );

    }
    

    /**
     * 
     * Return an array of Background Repeat values through filter ['PLUGIN_MAIN_SLUG/background/repeat']
     * 
     * @since 1.0
     * 
     * @return array
     * 
     */
    private function backgroundRepeat() {
        
        return apply_filters( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/filters/admin/pages/elements/background/repeat' ), array(

            'repeat' => esc_html__( 'Repeat All (Default)', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),

            'repeat-x' => esc_html__( 'Repeat Horizontal', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
            'repeat-y' => esc_html__( 'Repeat Vertical', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
            'no-repeat' => esc_html__( 'No Repeat', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
            'initial' => esc_html__( 'Initial', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
            'inherit' => esc_html__( 'Inherit', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
            
        ) );

    }


    /**
     * 
     * Return an array of Background Size values through filter ['PLUGIN_MAIN_SLUG/background/size']
     * 
     * @since 1.0
     * 
     * @return array
     * 
     */
    private function backgroundSize() {
        
        return apply_filters( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/filters/admin/pages/elements/background/size' ), array(
      
            'auto' => esc_html__( 'Auto (Default)', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),

            'cover' => esc_html__( 'Cover', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
            'contain' => esc_html__( 'Contain', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
            'initial' => esc_html__( 'Initial', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
            'inherit' => esc_html__( 'Inherit', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),

        ) );

    }


    /**
     * 
     * Return an array of Background Attachment values through filter ['PLUGIN_MAIN_SLUG/background/attachment']
     * 
     * @since 1.0
     * 
     * @return array
     * 
     */
    private function backgroundAttachment() {
        
        return apply_filters( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/filters/admin/pages/elements/background/attachment' ), array(
      
            'scroll' => esc_html__( 'Scroll (Default)', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),

            'fixed' => esc_html__( 'Fixed', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
            'local' => esc_html__( 'Local', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
            'initial' => esc_html__( 'Initial', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
            'inherit' => esc_html__( 'Inherit', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),

        ) );

    }


    /**
     * 
     * Return an array of Background Position values through filter ['PLUGIN_MAIN_SLUG/background/position']
     * 
     * **
     * ATTENTION: DO NOT add new keys for this array because we use a fixed array values in main javascript file
     * **
     * 
     * @since 1.0
     * 
     * @return array
     * 
     */
    private function backgroundPosition() {
        
        return apply_filters( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/filters/admin/pages/elements/background/position' ), array(

            'left-top' => esc_html__( 'Left Top (Default)', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
            'left-center' => esc_html__( 'Left Center', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
            'left-bottom' => esc_html__( 'Left Bottom', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),

            'right-top' => esc_html__( 'Right Top', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
            'right-center' => esc_html__( 'Right Center', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
            'right-bottom' => esc_html__( 'Right Bottom', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),

            'center-top' => esc_html__( 'Center Top', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
            'center-center' => esc_html__( 'Center Center', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),
            'center-bottom' => esc_html__( 'Center Bottom', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),

        ) );

    }

    /**
     * 
     * Return the supported HTML5 Video Types through filter ['PLUGIN_MAIN_SLUG/html5/supported/video']
     * 
     * @since 1.0
     * 
     * @return array
     * 
     */
    private function ReturnHTML5_SupportedVideoTypes() {

        return apply_filters( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/filters/admin/pages/helper/html5/supported/video'), array( 
            
            'mp4', 
            'm4v', 
            'ogv', 
            'webm' 
            
        ) );

    }


    /**
     * 
     * Return the supported HTML5 Audio Types through filter ['PLUGIN_MAIN_SLUG/html5/supported/audio']
     * 
     * @since 1.0
     * 
     * @return array
     * 
     */
    private function ReturnHTML5_SupportedAudioTypes() {

        return apply_filters( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/filters/admin/pages/helper/html5/supported/audio'), array( 
            
            'mp3', 'm4a', 'm4b', 'wav', 'ogg', 'oga', 'mpeg'
            
        ) );

    }

    /**
     * 
     * Return Social Networks names through filter ['PLUGIN_MAIN_SLUG/social_networks/names']
     * 
     * You can add new names through that filter in [ setup.php ]
     * 
     * @since 1.0
     * 
     * @return array
     * 
     */
    private function ReturnSocialNetworks_Names() {

        return apply_filters( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/filters/admin/pages/helper/social_networks/names'), array(

            'facebook' => 'Facebook',
            'twitter' => 'Twitter',
            'instagram' => 'Instagram',
            'pinterest' => 'Pinterest',
            'bloglovin' => 'Bloglovin',
            'email' => 'E-Mail',
            'googleplus' => 'Google Plus',
            'stumbleupon' => 'Stumbleupon',
            'youtube' => 'Youtube',
            'linkedin' => 'LinkedIn',
            'dropbox' => 'Dropbox',
            'rss' => 'RSS',
            'skype' => 'Skype',
            'wordpress' => 'WordPress',
            'dribbble' => 'Dribbble',
            'vimeo' => 'Vimeo',
            'vk' => 'VK',
            'fivehundredpx' => '500px',
            'github' => 'Github',
            'soundcloud' => 'SoundCloud',
            'myspace' => 'Myspace',
            'flattr' => 'Flattr',
            'google' => 'Google',
            'blogger' => 'Blogger',
            'reddit' => 'Reddit',
            'tumblr' => 'Tumblr',
            'evernote' => 'Evernote',
            'lastfm' => 'LastFM',
            'digg' => 'Digg',
            'flickr' => 'Flickr',
            'delicious' => 'Delicious',
            'amazon' => 'Amazon',
            'android' => 'Android',
            'angellist' => 'Angellist',
            'forrst' => 'Forrst',
            'pinboard' => 'Pinboard',
            'viadeo' => 'Viadeo',
            'yahoo' => 'Yahoo',
            'yelp' => 'Yelp',
            'drupal' => 'Drupal',
            'ebay' => 'Ebay',

        ) );

    }



    /**
     * 
     * Return Control Type to use in main javascript scripts
     * 
     * And trigger the main functions if the element is visible
     * 
     * Example: convert [oembed] to [oEmbed] to trigger javascript function [$.srpset.element.oEmbed.init] 
     * every time the element appear in the viewport
     * 
     * @since 1.0
     * 
     * @param $el_type string
     * 
     * @return string
     * 
     */
    public static function return_control_type_for_js( $el_type ) {
        
        $output = 'custom_element';
        
        if( array_key_exists( $el_type, self::$js_element_names ) ) {
            
            $output = self::$js_element_names[ $el_type ];

        }

        return sanitize_text_field( $output );

    }


    /**
     * 
     * Return the MIME full string by provided type
     * 
     * @since 1.0
     * 
     * @param string $type 
     * 
     */
    public static function ReturnMIME_Type( $type ) {

        $output = '';

        switch( $type ) { 
            
            /*
             * VIDEO
             * */
            case 'asf':
            case 'asx': { 
                    $output = 'video/x-ms-asf'; 
                } break;

            case 'wmv': $output = 'video/x-ms-wmv'; break;

            case 'wmx': $output = 'video/x-ms-wmx'; break;

            case 'wm': $output = 'video/x-ms-wm'; break;

            case 'avi': $output = 'video/avi'; break;

            case 'divx': $output = 'video/divx'; break;

            case 'flv': $output = 'video/x-flv'; break;

            case 'ogv': $output = 'video/ogg'; break;

            case 'webm': $output = 'video/webm'; break;

            case 'mkv': $output = 'video/x-matroska'; break;

            case 'mov': 
            case 'qt': {
                    $output = 'video/quicktime';
                } break;

            case 'mpeg': 
            case 'mpg':
            case 'mpe': {
                    $output = 'video/mpeg';
                } break;

            case 'mp4': 
            case 'm4v': {
                    $output = 'video/mp4';
                } break;

            case '3gp': 
            case '3gpp': {
                    $output = 'video/3gpp';
                } break;

            case '3g2': 
            case '3gp2': {
                    $output = 'video/3gpp2';
                } break;


            /*
             * AUDIO
             * 
             * */
            case 'wav': $output = 'audio/wav'; break;

            case 'wma': $output = 'audio/x-ms-wma'; break;
            
            case 'wax': $output = 'audio/x-ms-wax'; break;
            
            case 'mka': $output = 'audio/x-matroska'; break;

            case 'mp3': 
            case 'm4a':
            case 'm4b': {
                    $output = 'audio/mpeg';
                } break;

            case 'ra': 
            case 'ram': {
                    $output = 'audio/x-realaudio';
                } break;

            case 'ogg': 
            case 'oga': {
                    $output = 'audio/ogg';
                } break;

            case 'mid': 
            case 'midi': {
                    $output = 'audio/midi';
                } break;


            default:
                $output = null;

        }


        if( empty( $output ) ) {
            return null;
        }

        return $output;

    }


    /**
     * 
     * Sort controls array by Priority and ID ASC
     * 
     * @since 1.0
     * 
     * @access for local functions only
     * 
     * @param array $controls The user defined controls array .. Must contains [id], [params] and [group] and [priority] keys inside [params] array 
     * 
     * @return array
     * 
     */
    public static function _sortControlsByPriority( $controls = array() ) {
        
        $unsortable_ids = $unsortable_priority = $sortable_ids = $sortable_priority = array();

        if( is_array( $controls ) && ( count($controls) > 0 ) ) {

            foreach( $controls as $control ) { 
                
                if( is_array( $control ) 
                    && ( count($control) > 0 ) 
                    && array_key_exists( 'id', $control ) 
                    && !empty( $control['id'] ) ) {


                    if( array_key_exists( 'params', $control ) && is_array( $control['params'] ) &&
                        array_key_exists( 'group', $control['params'] ) && !empty( $control['params']['group'] ) &&
                        array_key_exists( 'priority', $control['params'] ) && !empty( $control['params']['priority'] ) &&
                        is_numeric( $control['params']['priority'] ) && ( intval( $control['params']['priority'] ) > 0 ) ) {

                        $sortable_ids[] = sanitize_text_field( $control['id'] );

                        $sortable_priority[] = intval( $control['params']['priority'] );
                        
                    }
                    else {
                        
                        /*
                         * JUST To Fix ERROR: [ array_multisort(): Array sizes are inconsistent ]
                         * 
                         * ONLY controls have [ group ] and [ priority ] Will appear 
                         * 
                         * */
                        $unsortable_ids[] = sanitize_text_field( $control['id'] );

                        $unsortable_priority[] = 10000000;

                    }


                }
                
            }


            $sortable_ids = array_merge( $sortable_ids, $unsortable_ids );

            $sortable_priority = array_merge( $sortable_priority, $unsortable_priority );


            if( is_array( $sortable_priority ) && ( count($sortable_priority) > 0 ) && is_array( $sortable_ids ) && ( count($sortable_ids) > 0 ) ) {
                
                /*
                 * [ @ ] to Avoid Errors if control don't have [group] and [priority] inside a [params] array
                 * 
                 * */
                @array_multisort( $sortable_priority, SORT_ASC, $sortable_ids, SORT_ASC, $controls );

            }

        }
        

        return $controls;
    }


    /**
     * 
     * 
     * @since 1.0
     * 
     * @param array $sorted_array 
     * 
     * @param string $group_key 
     * 
     * @return array
     * 
     */
    public static function _collectControlsByGroupKey( $sorted_array, $group_key ) {
        
        $group_controls = array();

        foreach ( (array)$sorted_array as $control ) {
        	
            if( ( array_key_exists( 'params', $control ) )
                && is_array( $control['params'] ) 
                && ( array_key_exists( 'group', $control['params'] ) ) 
                && ( ! empty( $control['params']['group'] ) )
                && ( $control['params']['group'] == $group_key ) ) {
                
                $group_controls[] = $control;

            }

        }
        
        return $group_controls;

    }


    /**
     * Draw elements by type for Custom Post Types
     * 
     * Custom Post Types using Custom Tables to save its data so we need
     * to serialize and do deserialize before retrieving some types like those
     * return array [multi-select | multi-checkbox | etc], While the other {draw_elements}
     * used to save data using {update_option} and {get_option} WordPress functions and saved
     * in a single fields as well so we don't need to serialize and deserialize manually because
     * it's already done by WordPress functions
     * 
     * Elements need to serialize:
     * 
     * group
     * multi-select
     * multi-checkbox
     * color {gradient}
     * background
     * oembed
     * 
     * @since 1.0
     * 
     * 
     * @param bool $new_item
     * @param string $tab The current tab key
     * @param array $db_values The current tab DB values
     * @param array $elements An array of elements that cooked in the [ sections ] folder
     * 
     * @return void
     * 
     */
    public static function Draw_CPT_Elements( $new_item = true, $tab = '', $db_values = array(), $elements = array(), $use_array_prefix = TRUE ) {
        
        if( ( ! is_array( $elements ) ) || ( is_array( $elements ) && ( count( $elements ) == 0 ) ) ) {
            return;
        }

        /*
         * Let's Start
         * 
         * */
        foreach ((array)$elements as $control) {
        	
            if( ( ! array_key_exists( 'type', $control ) ) || ( ! array_key_exists( 'id', $control ) ) ) {
                continue;
            }
            
            $db_element_value = null;

            if( array_key_exists( 'db_col', $control ) && ( ! empty( $control['db_col'] ) ) ) {
                $db_element_value = ( is_array( $db_values ) && array_key_exists( $control['db_col'], $db_values ) ) ? maybe_unserialize( $db_values[ $control['db_col'] ] ) : null;
            }
            else {
                $db_element_value = ( is_array( $db_values ) && array_key_exists( $control['id'], $db_values ) ) ? maybe_unserialize( $db_values[ $control['id'] ] ) : null;
            }

            if( 'group' == $control['type'] ) {
             
                if( ! array_key_exists( 'controls', $control ) ) {
                    continue;
                }

                self::_draw_group_elements( $new_item, $control, $db_element_value, $use_array_prefix );

            }
            else {
                self::_draw_elements_helper( $control, $new_item, $tab, $db_element_value, $use_array_prefix );
            }
            
        }

    }

    

    private static function _draw_group_elements( $is_new = false, $group_control = array(), $db_value = null, $use_array_prefix = TRUE ) {
        
        if( array_key_exists( 'controls', $group_control ) && ( count( $group_control['controls'] ) > 0 ) ) { ?>

            <div class="group-element-wrapper" data-group="<?php echo sanitize_text_field( $group_control['id'] ); ?>">

                <?php if( $group_control['title'] ) { ?>
                <div class="group-header">
                    <div class="header-inner"><?php echo sanitize_text_field( $group_control['title'] ); ?></div>
                </div>
                <?php } ?>

                <div class="group-elements-inner">

                    <?php foreach ( $group_control['controls'] as $group_element ) {
                    
                        if( ( ! array_key_exists( 'type', $group_element ) ) || ( ! array_key_exists( 'id', $group_element ) ) ) {
                            continue;
                        }

                        $db_group_element_value = ( is_array( $db_value ) && array_key_exists( $group_element['id'], $db_value ) ) ? $db_value[ $group_element['id'] ] : null;

                        self::_draw_elements_helper( $group_element, $is_new, $group_control['id'], $db_group_element_value, $use_array_prefix );

                    } ?>
                        
                </div>
                                            
            </div>

        <?php }

    }

    /**
     * Draw elements by type - Only to use with elements 
     * written by WordPress functions {update_option} and retrieved by {get_option}
     * because we do not use {unserialize} here
     * 
     * @since 1.0
     * 
     * @param bool $new_item
     * @param string $tab The current tab key
     * @param array $db_values The current tab DB values
     * @param array $elements An array of elements that cooked in the [ sections ] folder
     * 
     * @return void
     * 
     */
    public static function _Draw_Elements( $new_item = true, $tab = '', $db_values = array(), $elements = array(), $use_array_prefix = TRUE ) {
        
        if( ( ! is_array( $elements ) ) || ( is_array( $elements ) && ( count( $elements ) == 0 ) ) ) {            
            return;
        }
        
        foreach ((array)$elements as $control) {
        	
            if( ( ! array_key_exists( 'type', $control ) ) || ( ! array_key_exists( 'id', $control ) ) ) {
                continue;
            }
        
            $db_element_value = ( is_array( $db_values ) && array_key_exists( $control['id'], $db_values ) ) ? $db_values[ $control['id'] ] : null;
            
            self::_draw_elements_helper( $control, $new_item, $tab, $db_element_value, $use_array_prefix );
            
        }

    }


    
    private static function _draw_elements_helper( $control, $new_item = true, $tab = '', $db_value = null, $use_array_prefix = TRUE ) {

        $name = $id = $default_value = $title = '';
                
        $options = $params = array();

        $cpt_cap = Admin_Components::instance()->apcap_cpt;

        /*
         * DO NOT EVER modify {element_name} or {element_id}
         * Unless search about all places use it, It's VERY IMPORTANT
         * In multiple places
         * 
         * VI NOTE: {WP_FileSystem} classes we use element names generated by the same function {element_id},
         * 
         * */
        if( $use_array_prefix ) {
            /* Use array like slug[name] */
            $name = Admin_Components::instance()->element_name( $control['id'], -1, null, $tab );
        } else {
            /* DO NTO Use array like slug[name] but like id {slug_name} */
            $name = Admin_Components::instance()->element_id( '', $control['id'], '', $tab );
        }

        $id = Admin_Components::instance()->element_id( '', $control['id'], '', $tab );

        /* Special Case - Button */
        $prepend_id = Admin_Components::instance()->element_id( '', $tab, '_', null );
                        
        // echo print_r( $final_value );

        // Might be an array
        $default_value = ( array_key_exists( 'default', $control ) ) ? $control['default'] : '';
            

        $title = ( array_key_exists( 'title', $control ) && ( ! empty( $control['title'] ) ) ) ? ( $control['title'] ) : '';
            
        /* Options Array */
        $options = ( array_key_exists( 'options', $control ) && ( ! empty( $control['options'] ) ) ) ? (array)$control['options'] : array();

        /* Params Array */
        $params = ( array_key_exists( 'params', $control ) && ( is_array( $control['params'] ) ) ) ? (array)$control['params'] : array();

        $params['js_element_type'] = '';


        /*
            * Do NOT Sanitize description here
            * 
            * The sanitization will be done before draw the element to fix html elements inside the description
            * 
            * */
        $params['description'] = ( array_key_exists( 'description', $control ) && ( ! empty( $control['description'] ) ) ) ? $control['description'] : '';
            

        switch ( $control['type'] ) {
            	
            case 'repeater': {
                    
                Admin_ElementsRepeaterMapper::instance()->repeater( $control, $db_value, $tab );

            } break;

            case 'result': {
                        
                    echo Admin_ElementsTemplates::result( $title, $params );

                } break;

            // Special Case Button
            case 'button': {
                        
                    echo Admin_ElementsTemplates::button( $id, $title, $params );

                } break;

            case 'multi-buttons': {
                        
                    /* Fix Multi-buttons */
                    $params['buttons'] = ( array_key_exists( 'buttons', $control ) && ( is_array( $control['buttons'] ) ) ) ? $control['buttons'] : array();

                    echo Admin_ElementsTemplates::multi_buttons( $prepend_id, $params['buttons'] );

                } break;

            case 'label': {
                        
                    echo Admin_ElementsTemplates::label( $name, $id, $db_value, $default_value, $title, $params );

                } break;

            case 'hidden': {
                        
                    echo Admin_ElementsTemplates::hidden( $name, $id, $db_value, $default_value, $title, $params );

                } break;

            case 'password': {
                        
                    echo Admin_ElementsTemplates::password( $name, $id, $db_value, $title, $params );

                } break;

            case 'text': {
                        
                    echo Admin_ElementsTemplates::text( $new_item, $name, $id, $db_value, $default_value, $title, $params );

                } break;

            case 'number': {
                        
                    echo Admin_ElementsTemplates::number( $new_item, $name, $id, $db_value, $default_value, $title, $params );

                } break;

            case 'textarea': {
                        
                    echo Admin_ElementsTemplates::textarea( $new_item, $name, $id, $db_value, $default_value, $title, $params );

                } break;

            case 'color': {
                        
                    echo Admin_ElementsTemplates::color( $new_item, $name, $id, $db_value, $default_value, $title, $params );

                } break;

            case 'radio': {
                        
                    echo Admin_ElementsTemplates::radio( $new_item, $name, $id, $options, $db_value, $default_value, $title, $params );

                } break;

            case 'radio-images': {
                        
                    echo Admin_ElementsTemplates::radio_images( $new_item, $name, $id, $options, $db_value, $default_value, $title, $params );

                } break;

            case 'checkbox': {
                        
                    echo Admin_ElementsTemplates::checkbox( $new_item, $name, $id, $db_value, $default_value, $title, $params );

                } break;


            case 'multi-checkbox': {
                        
                    echo Admin_ElementsTemplates::multi_checkbox( $new_item, $name, $id, $options, $db_value, $default_value, $title, $params );

                } break;

            case 'select': {
                
                    echo Admin_ElementsTemplates::select( $new_item, $name, $id, $options, $db_value, $default_value, $title, $params );

                } break;

            case 'multi-select': {
                        
                    echo Admin_ElementsTemplates::multi_select( $new_item, $name, $id, $options, $db_value, $default_value, $title, $params );

                } break;

            case 'spinner': {
                        
                    $default_params = array(
                          
                        'min_value' => 0,
                        'max_value' => 100,
                        'step_value' => 1,

                        'format' => 'n',        // 'n' for decimal numbers or 'C' for currency values available

                    );

                    $params = wp_parse_args( $params, $default_params );

                    echo Admin_ElementsTemplates::spinner( $new_item, $name, $id, $db_value, $default_value, $title, $params );

                } break;


            case 'slider': {
                        
                    $default_params = array(
                          
                        'min_value' => 0,
                        'max_value' => 100,
                        'step_value' => 1

                    );

                    $params = wp_parse_args( $params, $default_params );

                    echo Admin_ElementsTemplates::slider( $new_item, $name, $id, $db_value, $default_value, $title, $params );

                } break;


            case 'range-slider': {
                        
                    $default_params = array(
                          
                        'min_value' => -10,
                        'max_value' => 10,
                        'step_value' => 1,
                        'unit' => '',
                        'unitposition' => 'before',

                    );

                    $params = wp_parse_args( $params, $default_params );

                    echo Admin_ElementsTemplates::range_slider( $new_item, $name, $id, $db_value, $default_value, $title, $params );

                } break;


            case 'upload-media': {
                        
                    echo Admin_ElementsTemplates::upload_media( $new_item, $name, $id, $db_value, $default_value, $title, $params );

                } break;

            case 'upload-gallery': {
                        
                    echo Admin_ElementsTemplates::upload_gallery( $new_item, $name, $id, $db_value, $default_value, $title, $params );

                } break;

        }

    }


    /**
     * Collect all default values for controls map
     * 
     * @since 1.0
     * 
     * @param array $map 
     * 
     * @return null|array
     * 
     */
    public static function _Collect_Defaults( $map = array() ) {
        
        if( ! array_key_exists( 'sections', $map ) ) {
            return null;
        }


        $default_value = '';
        
        $params = $output = array();
        
        foreach( $map['sections'] as $section => $section_content ) {
            
            if( array_key_exists( 'controls', $section_content ) && is_array( $section_content['controls'] ) && ( count( $section_content['controls'] ) > 0 ) ) {
                
                foreach ( (array)$section_content['controls'] as $control) {
                    
                    if( ( ! array_key_exists( 'type', $control ) ) || ( in_array( $control['type'], self::$exclude_controls ) ) ) {
                        continue;
                    }
        
                    $params = array_key_exists( 'params', $control ) ? $control['params'] : array();

                    // Might be an array
                    $default_value = ( array_key_exists( 'default', $control ) ) ? $control['default'] : '';
                    
                    switch ( $control['type'] ) {
                        
                        case 'repeater': {
                            $default_value = array();
                        } break;

                        case 'text':
                        case 'radio':
                        case 'radio-images':
                        case 'select': {
                                $default_value = ( array_key_exists( 'default', $control ) && is_string( $control['default'] ) && ( ! empty( $control['default'] ) ) ) ? sanitize_text_field( $control['default'] ) : '';
                            } break;

                        case 'textarea': {
                                $default_value = ( array_key_exists( 'default', $control ) && is_string( $control['default'] ) && ( ! empty( $control['default'] ) ) ) ? wp_kses_post( $control['default'] ) : '';
                            } break;
                        
                        case 'color': {
                                $gradient = ( array_key_exists( 'gradient', $params ) && ( filter_var( $params['gradient'], FILTER_VALIDATE_BOOLEAN ) ) ) ? true : false;
                                
                                if( $gradient ) {
                                    
                                    if( is_array( $control['default'] ) && array_key_exists( 'from', $control['default'] ) && array_key_exists( 'to', $control['default'] ) ) {
                                        $default_value = $control['default'];
                                    }
                                    else {
                                        $default_value = array( 
                                            'from' => '', 
                                            'to' => '' 
                                        );
                                    }
                                }

                            } break;
                        
                        case 'checkbox': {
                                $default_value = ( array_key_exists( 'default', $control ) && ( filter_var( $control['default'], FILTER_VALIDATE_BOOLEAN ) ) ) ? 'on' : 0;
                            } break;


                        case 'multi-checkbox': {
                            $default_value = array();
                            $temp_default_value = ( is_array( $control['default'] ) && ( count( $control['default'] ) > 0 ) ) ? (array)$control['default'] : array();
                            foreach( $temp_default_value as $key ) {
                                $default_value[ $key ] = 'on';
                            }
                        } break;

                        case 'multi-select': {
                                $default_value = ( is_array( $control['default'] ) && ( count( $control['default'] ) > 0 ) ) ? (array)$control['default'] : array();
                            } break;
                        
                        case 'spinner':
                        case 'slider': {
                                
                                $default_params = array(
                                      
                                    'min_value' => 0,
                                    'max_value' => 100,
                                    'step_value' => 1,

                                    'format' => 'n',        // 'n' for decimal numbers or 'C' for currency values available

                                );

                                $params = wp_parse_args( $params, $default_params );

                                $default_value = ( array_key_exists( 'default', $control ) && ( is_numeric( $control['default'] ) ) && ( intval($control['default']) >= $params['min_value'] ) && ( intval($control['default']) <= $params['max_value'] ) ) ? intval( $control['default'] ) : intval( $params['min_value'] );

                            } break;
                        

                        case 'range-slider': {
                                
                                $default_params = array(
                                      
                                    'min_value' => -10,
                                    'max_value' => 10,
                                    'step_value' => 1,
                                    'unit' => '',
                                    'unitposition' => 'before',

                                );

                                $params = wp_parse_args( $params, $default_params );
                                
                                $final_min_val = $final_max_val = 0;

                                $temp_default_value = ( array_key_exists( 'default', $control ) ) ? $control['default'] : '';

                                // User Default Input Example [ 150, 250 ]
                                $user_value = @explode(',', $temp_default_value);
                                
                                if( is_array($user_value) && (count($user_value) > 1) && (count($user_value) < 3) ) {

                                    $final_min_val = ( $user_value[0] >= $params['min_value'] ) ? intval($user_value[0]) : $params['min_value'];
                                    $final_max_val = ( $user_value[1] <= $params['max_value'] ) ? intval($user_value[1]) : $params['max_value'];
                                    
                                }

                                $default_value = array();
                                $default_value['unitposition'] = $params['unitposition'];
                                $default_value['unit'] = $params['unit'];
                                $default_value['min'] = $final_min_val;
                                $default_value['max'] = $final_max_val;

                            } break;


                        case 'upload-media': {
                                $default_value = ( array_key_exists( 'default', $control ) && is_string( $control['default'] ) && ( ! empty( $control['default'] ) ) ) ? esc_url( $control['default'] ) : '';
                            } break;

                        case 'upload-gallery': {
                                $default_value = ( array_key_exists( 'default', $control ) && is_string( $control['default'] ) && ( ! empty( $control['default'] ) ) && has_shortcode( $control['default'], 'gallery' ) ) ? sanitize_text_field( $control['default'] ) : '';
                            } break;
                        
                    }


                    $output[ $section ][ $control['id'] ] = $default_value;
                    
                }

            }

        } // foreach- sections
     
        return $output;

    }

}