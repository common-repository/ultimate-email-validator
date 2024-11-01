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
class Admin_ElementsTemplates {

    /**
     * Silent Constructor  
     * 
     * */
    private function __construct() { }


    /**
     * Checking key and type then returns values 
     * 
     * Types (array, string, int, hex-color)
     * 
     * @param string $key
     * 
     * @param string $type
     * 
     * @param array $arr_keys is an array to check key is exists
     * 
     * @return mixed value or null
     * 
     * @Since 1.0
     * 
     */
    public static function con_check($key, $type = 'string', $arr_keys = array()) {

        $str_output = '';
        
        switch($type){
            case 'array': {

                    $str_output = array();

                    if( ( ! empty( $key ) ) && is_array( $arr_keys ) && array_key_exists( $key, $arr_keys ) ) {
                        $str_output = $arr_keys[ $key ];
                    }

                }
                break;

            case 'string':

                if( is_string($key) && $key && !empty($key) ) {

                    $str_output = $key;

                }
                break;

            case 'int':
                if( is_numeric($key) ) {

                    $str_output = intval($key);

                }
                else {

                    $str_output = 0;

                }
                break;
            
            case 'hex-color':
                if( ! empty($key) ) {

                    preg_match_all('/#?([\da-fA-F]{6})|#([\d|a-f|A-F]){3}/', $key, $matches);

                    if( is_array($matches[0]) && count($matches[0]) > 0 ) {

                        $str_output = $matches[0][0];

                    }

                }
                break;

            default:
                $str_output = '';
                break;
        }


        return $str_output;
    }


    private static function DrawElementTitle( $id = '', $title = '', $params = array(), $after = true ) {
        
        $required = ( array_key_exists('required', $params) && ( true === filter_var( $params['required'], FILTER_VALIDATE_BOOLEAN ) ) ) ? true : false;

        if( ( !empty( $title ) ) || ( array_key_exists('description', $params) && !empty( $params['description'] ) ) ) {
            
            $element_classes = array();
            
            $element_classes[] = 'element-title';
            $element_classes[] = ( ! empty( $params['description'] ) ) ? 'has-description' : ''; ?>

            <div class="<?php echo join( ' ', $element_classes ); ?>">
            
                <?php if( ! empty( $title ) ) { ?>

                    <?php if ( $required ) { ?>

                        <span class="required">*</span>

                    <?php } ?>

                    <label class="label" for="<?php echo sanitize_text_field( $id ); ?>"><?php echo sanitize_text_field( $title ); ?></label>
            
                <?php } ?>

            </div>

    <?php }

    }


    /**
     * 
     * 
     * @since 1.0
     * 
     * @param array $params 
     * 
     */
    private static function DrawElementDescription( $params ) {
        
        if( is_array( $params ) && array_key_exists( 'description', $params ) && ( ! empty( $params['description'] ) ) ) { ?>
            
            <div class="description"><?php echo wp_kses_post( $params['description'] ); ?></div>

        <?php }

    }
    
    
    /**
     * Check value for select element
     * Why?
     * 
     * If empty() applied for {0} will return true SO the {0} option will not selected
     * 
     * @since 1.0
     * 
     * @param array $params 
     * 
     */
    private static function CheckSelectElementValue( $value ) {
        
        if( ( ! empty($value) ) || ( is_numeric( $value ) ) ) {
            
            return $value;

        }

        return null;

    }

    /**
     * Collect element attributes to help add {data-required} and any future need from params array
     * 
     * Useful for elements
     * 
     * Text
     * Textarea
     * 
     * 
     * @param   array       $attributes and Key=>Value pair array
     * @param   array|null  $params 
     * 
     * @return  string
     */
    private static function CollectElementAttributes( $attributes, $params = null ) {
        
        $pre_output = array();

        if( ( is_array( $params ) ) && array_key_exists('required', $params) && ( true === filter_var( $params['required'], FILTER_VALIDATE_BOOLEAN ) ) ) {
            
            $aggressive_required = false;

            $attributes['data-required'] = 'true';

            if( array_key_exists('element_source', $params) ) {

                switch( $params['element_source'] ) {
                    case 'main': {
                        $aggressive_required = true;  
                    } break;

                    case 'repeater': {
                        $aggressive_required = false;
                    } break;
                }

            }
            else {
                $aggressive_required = true;   
            }
            
            if( $aggressive_required ) {
                $attributes['required'] = 'required';
            }
        }

        foreach( $attributes as $attr => $value ) {            
            $pre_output[] = sprintf( '%s=\'%s\'', $attr, $value );
        }

        return join( ' ', $pre_output );

    }


    public static function result( $title = '', $params = array() ) {
        
        $element_classes = array();
        
        $element_classes[] = ( ( array_key_exists( 'css_classes', $params ) ) && ( ! empty( $params['css_classes'] ) ) ) ? sanitize_text_field( $params['css_classes'] ) : '';

        ob_start(); ?>
        
        <div class="srpset-element-wrapper element-result <?php echo join(' ', $element_classes) ?>">

            <?php self::DrawElementTitle( '', $title, $params ); ?>

            <div class="result-screen">
                

            </div>

        </div>

        <?php return ob_get_clean();

    }


    /**
     * Build Label HTML
     * 
     * @since 1.0
     * 
     * @param string $name 
     * 
     * @param string $id 
     * 
     * @param string $db_value 
     * 
     * @param string $default_value 
     * 
     * @param string $title 
     * 
     * @param array $params array (
     * 
     *      -- {style}  => light | default | primary | success | warning | info | danger
     * 
     * )
     * 
     * @return string
     * 
     */
    public static function label( $name = '', $id = '', $db_value = '', $default_value = '', $title = '', $params = array() ) {
        
        $element_classes = array();
        
        $element_classes[] = ( ( array_key_exists( 'style', $params ) ) && ( ! empty( $params['style'] ) ) ) ? sanitize_text_field( $params['style'] ) : 'default';

        ob_start(); ?>
        
        <div class="srpset-element-wrapper element-label <?php echo join(' ', $element_classes) ?>">

            <label name="<?php echo sanitize_text_field( $name ); ?>" id="<?php echo sanitize_text_field( $id ); ?>" class="">
                
                <?php echo wp_kses_post( $title ); ?>

            </label>

        </div>

        <?php return ob_get_clean();

    }


    /**
     * Build Text HTML
     * 
     * @since 1.0
     * 
     * @param string $name 
     * 
     * @param string $id 
     * 
     * @param string $db_value 
     * 
     * @param string $default_value 
     * 
     * @param string $title 
     * 
     * @param array $params 
     * 
     * @return string
     * 
     */
    public static function hidden( $name = '', $id = '', $db_value = '', $default_value = '', $title = '', $params = array() ) {
        
        $element_classes = array();
        
        $final_value = ! empty( $db_value ) ? sanitize_text_field( $db_value ) : ''; 

        $element_attr = array(
            'type'      => 'hidden',
            'class'     => 'user-input',
            'name'      => sanitize_text_field( $name ),
            'id'        => sanitize_text_field( $id ),
            'value'     => sanitize_text_field( $final_value ),
        );
        
        ob_start(); ?>
        
        <div class="srpset-element-wrapper element-hidden hidden <?php echo join(' ', $element_classes) ?>">

            <?php self::DrawElementTitle( $id, $title, $params ); ?>

            <input <?php echo self::CollectElementAttributes( $element_attr, $params ); ?> />

            <?php self::DrawElementDescription( $params ); ?>

        </div>

        <?php return ob_get_clean();

    }


    /**
     * Build Password HTML
     * 
     * @since 1.0
     * 
     * @param string $name 
     * 
     * @param string $id 
     * 
     * @param string $db_value 
     * 
     * @param string $title 
     * 
     * @param array $params 
     * 
     * @return string
     * 
     */
    public static function password( $name = '', $id = '', $db_value = '', $title = '', $params = array() ) {
        
        $element_classes = array();
        
        $final_value = ! empty( $db_value ) ? sanitize_text_field( $db_value ) : ''; 
        
        ob_start(); ?>
        
        <div class="srpset-element-wrapper element-password text <?php echo join(' ', $element_classes) ?>" data-element-type="<?php echo sanitize_text_field( $params['js_element_type'] ); ?>">

            <?php self::DrawElementTitle( $id, $title, $params ); ?>

            <input type="password" class="user-input" name="<?php echo sanitize_text_field( $name ); ?>" id="<?php echo sanitize_text_field( $id ); ?>" value="<?php echo sanitize_text_field( $final_value ); ?>" />

            <?php self::DrawElementDescription( $params ); ?>

        </div>

        <?php return ob_get_clean();

    }


    /**
     * Build Text HTML
     * 
     * @since 1.0
     * 
     * @param string $name 
     * 
     * @param string $id 
     * 
     * @param string $db_value 
     * 
     * @param string $default_value 
     * 
     * @param string $title 
     * 
     * @param array $params 
     * 
     * @return string
     * 
     */
    public static function text( $new_item = true, $name = '', $id = '', $db_value = '', $default_value = '', $title = '', $params = array() ) {
        
        $element_classes = array();
        
        $final_value = '';
        if( $new_item ) {
            $final_value = $default_value;
        }
        else {
            $final_value = ( ! empty( $db_value ) ) ? sanitize_text_field( $db_value ) : ''; 
        }

        $control_source = ( array_key_exists('controls_source', $params) && !empty( $params['controls_source'] ) ) ? $params['controls_source'] : '';

        $placeholder = ( array_key_exists('placeholder', $params) && !empty( $params['placeholder'] ) ) ? $params['placeholder'] : '';

        $locked = ( array_key_exists('locked', $params) && ( TRUE === filter_var( $params['locked'], FILTER_VALIDATE_BOOLEAN ) ) );

        $element_classes[] = ( array_key_exists('js_title', $params) && ( true == $params['js_title'] ) ) ? 'js-title' : '';

        $element_attr = array(
            'type'      => 'text',
            'class'     => 'user-input',
            'name'      => sanitize_text_field( $name ),
            'id'        => sanitize_text_field( $id ),
            'placeholder'  => sanitize_text_field( $placeholder ),
            'value'     => sanitize_text_field( $final_value ),
        );

        if( $locked ) {
            $element_attr['readonly'] = 'readonly';
        }

        ob_start(); ?>
        
        <div class="srpset-element-wrapper element-text text <?php echo join(' ', $element_classes) ?>" 
             data-element-type="<?php echo sanitize_text_field( $params['js_element_type'] ); ?>" data-control-source="<?php echo sanitize_text_field( $control_source ); ?>">

            <?php self::DrawElementTitle( $id, $title, $params ); ?>

            <input <?php echo self::CollectElementAttributes( $element_attr, $params ); ?> />

            <?php self::DrawElementDescription( $params ); ?>

        </div>

        <?php return ob_get_clean();

    }

    /**
     * Build Input Numbers Only HTML
     * 
     * @since 1.0
     * 
     * @param string $name 
     * 
     * @param string $id 
     * 
     * @param string $db_value 
     * 
     * @param string $default_value 
     * 
     * @param string $title 
     * 
     * @param array $params 
     * 
     * @return string
     * 
     */
    public static function number( $new_item = true, $name = '', $id = '', $db_value = '', $default_value = '', $title = '', $params = array() ) {
        
        $element_classes = array();
        
        $final_value = 0;

        if( $new_item ) {
            $final_value = $default_value;
        }
        else {
            $final_value = ( $db_value ) ? $db_value : ''; 
        }

        $placeholder = ( array_key_exists('placeholder', $params) && !empty( $params['placeholder'] ) ) ? $params['placeholder'] : '';

        $locked = ( array_key_exists('locked', $params) && ( TRUE === filter_var( $params['locked'], FILTER_VALIDATE_BOOLEAN ) ) );

        $element_attr = array(
            'type'      => 'number',
            'class'     => 'user-input',
            'name'      => sanitize_text_field( $name ),
            'id'        => sanitize_text_field( $id ),
            'placeholder'  => sanitize_text_field( $placeholder ),
            'value'     => sanitize_text_field( $final_value ),
        );

        if( $locked ) {
            $element_attr['readonly'] = 'readonly';
        }

        ob_start(); ?>
        
        <div class="srpset-element-wrapper element-number number <?php echo join(' ', $element_classes) ?>" data-element-type="<?php echo sanitize_text_field( $params['js_element_type'] ); ?>">

            <?php self::DrawElementTitle( $id, $title, $params ); ?>

            <input <?php echo self::CollectElementAttributes( $element_attr, $params ); ?> />

            <?php self::DrawElementDescription( $params ); ?>

        </div>

        <?php return ob_get_clean();

    }

    /**
     * Build Textarea HTML
     * 
     * @since 1.0
     * 
     * @param string $name 
     * 
     * @param string $id 
     * 
     * @param string $db_value 
     * 
     * @param string $default_value 
     * 
     * @param string $title 
     * 
     * @param array $params 
     * 
     * @return string
     * 
     */
    public static function textarea( $new_item = true, $name = '', $id = '', $db_value = '', $default_value = '', $title = '', $params = array() ) {
        
        $final_value = '';

        if( $new_item ) {
            $final_value = $default_value;
        }
        else {
            $final_value = ( ! empty( $db_value ) ) ? wp_kses_post( $db_value ) : ''; 
        }

        $element_classes = array();
                
        $placeholder = ( array_key_exists('placeholder', $params) && !empty( $params['placeholder'] ) ) ? $params['placeholder'] : '';
        
        $locked = ( array_key_exists('locked', $params) && ( TRUE === filter_var( $params['locked'], FILTER_VALIDATE_BOOLEAN ) ) );

        $element_attr = array(
            'name'      => sanitize_text_field( $name ),
            'id'        => sanitize_text_field( $id ),
            'placeholder'  => sanitize_text_field( $placeholder ),
        );

        if( $locked ) {
            $element_attr['readonly'] = 'readonly';
        }

        ob_start(); ?>


        <div class="srpset-element-wrapper element-textarea text <?php echo join(' ', $element_classes) ?>" data-element-type="<?php echo sanitize_text_field( $params['js_element_type'] ); ?>">

            <?php self::DrawElementTitle( $id, $title, $params ); ?>

            <textarea <?php echo self::CollectElementAttributes( $element_attr, $params ); ?>><?php echo wp_kses_post( $final_value ); ?></textarea>

            <?php self::DrawElementDescription( $params ); ?>

        </div>

        <?php return ob_get_clean();

    }

    /**
     * Draw button field
     * 
     * NOTE: Button element is a special case without name, DB values
     * And [id] field is customizable 
     * 
     * @since 1.0
     * 
     * @param string $id
     * 
     * @param string $title 
     * 
     * @param array $params 
     * 
     * @return string
     * 
     */
    public static function button( $id = '', $title = '', $params = array() ) {
        
        $element_classes = $element_parent_classes = array();
        
        $element_parent_classes[] = 'srpset-element-wrapper';
        $element_parent_classes[] = 'element-button';

        $ajax = false;

        $btn_title = $url = $target = '';
        
        ( array_key_exists( 'title', $params ) && is_string( $params['title'] ) ) && $btn_title = $params['title'];

        ( array_key_exists( 'parent_classes', $params ) && is_string( $params['parent_classes'] ) ) && $element_parent_classes[] = $params['parent_classes'];

        ( array_key_exists( 'classes', $params ) && is_string( $params['classes'] ) ) && $element_classes[] = $params['classes'];

        ( array_key_exists( 'ajax', $params ) && filter_var( $params['ajax'], FILTER_VALIDATE_BOOLEAN ) ) && $ajax = (bool)$params['ajax'] && $element_classes[] = 'button-ajax';

        ( array_key_exists( 'url', $params ) && filter_var( $params['url'], FILTER_VALIDATE_URL ) ) && $url = $params['url'];

        ( array_key_exists( 'target', $params ) && ( !empty( $params['target'] ) ) ) && $target = sanitize_text_field( $params['target'] );

        ob_start(); ?>
        
        <div class="<?php echo join( ' ', $element_parent_classes ); ?>" data-element-type="<?php echo sanitize_text_field( $params['js_element_type'] ); ?>">

            <?php self::DrawElementTitle( $id, $title, $params ); ?>

            <div class="button-inner">

                <?php if( $ajax ) { ?>
                    <div class="button-ajax-loading hidden">
                        <i class="srpset-loading absolute large spin"></i>
                    </div>
                <?php } ?>

                <a id="<?php echo sanitize_text_field( $id ); ?>" href="<?php echo esc_url( $url ); ?>" class="<?php echo join(' ', $element_classes) ?>" target="<?php echo sanitize_text_field( $target ); ?>"><?php echo sanitize_text_field( $btn_title ); ?></a>

            </div>

            <?php self::DrawElementDescription( $params ); ?>

        </div>

        <?php return ob_get_clean();

    }

    /**
     * Draw Multi Buttons field
     * 
     * NOTE: Multi Buttons element is a special case without name, DB values
     * And [id] field is customizable for each button passed through $params array
     * 
     * @param array $params array of multiple buttons params
     * 
     * @return string
     * 
     */
    public static function multi_buttons( $prepend_id, $params = array() ) {
                
        ob_start(); ?>

        <div class="srpset-element-wrapper element-multi-buttons" data-element-type=""><div class="multi-buttons-inner">

        <?php foreach( $params as $button ) {

            if( !is_array($button) ) {
                continue;
            } 

            /* RESET for each button */
            $element_classes = array();
            $ajax = false;
            $id = $title = $url = $target = '';

            $element_classes[] = 'button-inner';

            ( array_key_exists( 'id', $button ) && is_string( $button['id'] ) ) && $id = $prepend_id . $button['id'];

            ( array_key_exists( 'title', $button ) && is_string( $button['title'] ) ) && $title = $button['title'];

            ( array_key_exists( 'classes', $button ) && is_string( $button['classes'] ) ) && $element_classes[] = $button['classes'];

            ( array_key_exists( 'ajax', $button ) && filter_var( $button['ajax'], FILTER_VALIDATE_BOOLEAN ) ) && $ajax = (bool)$button['ajax'] && $element_classes[] = 'button-ajax';



            ( array_key_exists( 'url', $button ) && filter_var( $button['url'], FILTER_VALIDATE_URL ) ) && $url = esc_url( $button['url'] );

            ( array_key_exists( 'target', $button ) && ( ! $ajax ) && ( !empty( $button['target'] ) ) ) && $target = sanitize_text_field( $button['target'] ); ?>
        
            
            <div class="<?php echo join(' ', $element_classes) ?>" data-button-id="<?php echo sanitize_text_field( $id ); ?>">

                <?php if( $ajax ) { ?>

                    <div class="button-ajax-loading hidden">
                        <i class="srpset-loading absolute large spin"></i>
                    </div>

                <?php } ?>

                <a id="<?php echo sanitize_text_field( $id ); ?>" href="<?php echo esc_url( $url ); ?>" class="button" target="<?php echo sanitize_text_field( $target ); ?>"><?php echo sanitize_text_field( $title ); ?></a>

            </div>

            <?php } ?>
        
        </div></div>

        <?php return ob_get_clean();

    }


    /**
     * Build ColorPicker HTML
     * 
     * @since 1.0
     * 
     * @param boolean $new_item
     * 
     * @param string $con_name_full 
     * 
     * @param string $con_id_full 
     * 
     * @param string|array $db_value 
     * 
     * @param string|array $default_value 
     * 
     * @param string $text_title 
     * 
     * @param array $params 
     * 
     * @return string
     * 
     */
    public static function color( $new_item = true, $con_name_full, $con_id_full, $db_value = '', $default_value = '', $text_title = '', $params = array() ) {

        $default_color_1 = $default_color_2 = $cur_color_1 = $cur_color_2 = '';

        $alpha = $gradient = false;

        if( array_key_exists('alpha', $params) ) {
            
            if( $params['alpha'] === true ) {
                $alpha = true;
            }

            if( $params['gradient'] === true ) {
                $gradient = true;
            }

        }

        // Retrieve Default Values
        if( ! $gradient ) {
            
            if( is_string($default_value) ) {
                $default_color_1 = sanitize_text_field($default_value);
            }
        }
        else {
            
            if( is_array($default_value) && ! empty( $default_value ) ) {
                
                if( isset($default_value['from']) && !empty($default_value['from']) ) {
                    
                    $default_color_1 = sanitize_text_field($default_value['from']);

                }
                if( isset($default_value['to']) && !empty($default_value['to']) ) {
                    
                    $default_color_2 = sanitize_text_field($default_value['to']);

                }
                
            }

        }

        // Retrieve DB Value
        if( $new_item ) {

            $cur_color_1 = $default_color_1;
            $cur_color_2 = $default_color_2;

        }
        else { 

            // Retrieve DB Value
            if( is_string($db_value) ) {

                $cur_color_1 = sanitize_text_field($db_value);

            }
            elseif( is_array($db_value) && ! empty( $db_value ) ) {

                if( isset($db_value['from']) && !empty($db_value['from']) ) {
                    
                    $cur_color_1 = sanitize_text_field($db_value['from']);

                }
                if( isset($db_value['to']) && !empty($db_value['to']) ) {
                    
                    $cur_color_2 = sanitize_text_field($db_value['to']);

                }

            }

        }


        $element_classes = array();
        
        ob_start(); ?>

        <div class="srpset-element-wrapper element-wpcolor color_picker <?php echo join(' ', $element_classes); ?>" data-element-type="<?php echo sanitize_text_field( $params['js_element_type'] ); ?>"
             data-element-id="<?php echo sanitize_text_field($con_id_full); ?>">
        
            <?php self::DrawElementTitle( '', $text_title, $params );

            if( $gradient ) { ?>

                <div class="element-wpcolor-gradient">

                    <div class="inner">
                        <label class="label inline"><?php echo esc_html__('From', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN); ?></label>

                        <div class="srpset-color-picker input-group colorpicker-component">

                            <div class="trigger" data-alpha=<?php echo ( $alpha == true ) ? "true" : "false"; ?> 
                                 data-align=<?php echo ( is_rtl() ) ? "right" : "left"; ?>
                                 data-default-color="<?php echo sanitize_text_field($default_color_1); ?>">

                                <span class="force-table important input-group-addon color-result-container">

                                    <i class="force-tablecell important align-middle color-preview"></i>

                                    <span class="force-tablecell important align-middle select-color"><?php echo esc_html__('Select Color', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN); ?></span>
                                

                                    <input type="text" class="force-tablecell important align-middle form-control color-text" 
                                           name="<?php echo sanitize_text_field($con_name_full); ?>[from]" 
                                           id="<?php echo sanitize_text_field($con_id_full) . '_from'; ?>" 
                                           data-format="alias"
                                           data-alpha=<?php echo ( $alpha == true ) ? "true" : "false"; ?>
                                           data-default-color="<?php echo sanitize_text_field($default_color_1); ?>"
                                           value="<?php echo sanitize_text_field($cur_color_1); ?>" />
                                
                                    <span class="force-tablecell color-actions">

                                        <span class="force-table inner">

                                            <i class="force-tablecell important align-middle srpset-trigger-icon default" data-action="default" data-color="<?php echo sanitize_text_field($default_color_1); ?>"></i>

                                            <i class="force-tablecell important align-middle srpset-trigger-icon clear" data-action="clear"></i>

                                        </span>

                                    </span>

                                </span>

                            </div>

                        </div>
                    </div>

                    <div class="inner">
                        <label class="label inline"><?php echo esc_html__('To', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN); ?></label>

                        <div class="srpset-color-picker input-group colorpicker-component">

                            <div class="trigger" data-alpha=<?php echo ( $alpha == true ) ? "true" : "false"; ?>
                                 data-align=<?php echo ( is_rtl() ) ? "right" : "left"; ?>
                                 data-default-color="<?php echo sanitize_text_field($default_color_2); ?>">

                                <span class="force-table important input-group-addon color-result-container">
                                    
                                    <i class="force-tablecell important align-middle color-preview"></i>

                                    <span class="force-tablecell important align-middle select-color"><?php echo esc_html__('Select Color', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN); ?></span>
                                

                                    <input type="text" class="force-tablecell important align-middle form-control color-text" name="<?php echo sanitize_text_field($con_name_full); ?>[to]" id="<?php echo sanitize_text_field($con_id_full) . '_to'; ?>" 
                                            data-format="alias"
                                            data-alpha=<?php echo ( $alpha == true ) ? "true" : "false"; ?>
                                            data-default-color="<?php echo sanitize_text_field($default_color_2); ?>"
                                            value="<?php echo sanitize_text_field($cur_color_2); ?>" />

                                    <span class="force-tablecell color-actions">

                                        <span class="force-table inner">

                                            <i class="force-tablecell important align-middle srpset-trigger-icon default" data-action="default" data-color="<?php echo sanitize_text_field($default_color_2); ?>"></i>

                                            <i class="force-tablecell important align-middle srpset-trigger-icon clear" data-action="clear"></i>

                                        </span>

                                    </span>

                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            <?php } else { ?>

                <div class="srpset-color-picker input-group colorpicker-component">

                    <div class="trigger" data-alpha=<?php echo ( $alpha == true ) ? "true" : "false"; ?>
                         data-align=<?php echo ( is_rtl() ) ? "right" : "left"; ?> 
                         data-default-color="<?php echo sanitize_text_field($default_color_1); ?>">

                        <span class="force-table important input-group-addon color-result-container">
                            
                            <i class="force-tablecell important align-middle color-preview"></i>
                            
                            <span class="force-tablecell important align-middle select-color"><?php echo esc_html__('Select Color', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN); ?></span>
                        
                            <input type="text" class="force-tablecell important align-middle form-control color-text" name="<?php echo sanitize_text_field($con_name_full); ?>" id="<?php echo sanitize_text_field($con_id_full); ?>"
                                   data-format="alias"
                                   value="<?php echo sanitize_text_field($cur_color_1); ?>" />

                            <span class="force-tablecell color-actions">

                                <span class="force-table inner">

                                    <i class="force-tablecell important align-middle srpset-trigger-icon default" data-action="default" data-color="<?php echo sanitize_text_field($default_color_1); ?>"></i>

                                    <i class="force-tablecell important align-middle srpset-trigger-icon clear" data-action="clear"></i>

                                </span>

                            </span>

                        </span>

                    </div>
                </div>

            <?php }


            self::DrawElementDescription( $params ); ?>

        </div>

        <?php return ob_get_clean();

    }



    /**
     * Build Checkbox HTML
     * 
     * @since 1.0
     * 
     * @param boolean $new_item
     * 
     * @param string $con_name_full 
     * 
     * @param string $con_id_full 
     * 
     * @param string|array $db_value 
     * 
     * @param string|array $default_value 
     * 
     * @param string $text_title 
     * 
     * @param array $params 
     * 
     * @return string
     * 
     */
    public static function checkbox( $new_item = true, $con_name_full, $con_id_full, $db_value = '', $default_value = false, $text_title = '', $params = array() ) {
        
        $value_final = '';
        $db_value_final = false;

        if( $new_item ) {
            if( !empty($default_value) && ($default_value == true) ){
                $value_final = 'checked="checked"';
            }
        }
        else{
            
            $db_value_final = filter_var( $db_value, FILTER_VALIDATE_BOOLEAN );

            if( $db_value_final ) {

                $value_final = 'checked="checked"';

            }
        }
        
        $element_classes = array();

        ob_start(); ?>

        <div class="srpset-element-wrapper element-checkbox <?php echo join( ' ', $element_classes ); ?>" data-element-type="<?php echo sanitize_text_field( $params['js_element_type'] ); ?>">
                        
            <div class="srpset-checkbox-container">
                <input type="checkbox" class="srpset-checkbox" name="<?php echo sanitize_text_field($con_name_full); ?>" id="<?php echo sanitize_text_field($con_id_full); ?>"
                    data-currentval="<?php echo sanitize_text_field($db_value_final); ?>" <?php if( !empty($value_final) ) { echo sanitize_text_field( $value_final ); } ?> />
            
                <?php self::DrawElementTitle( $con_id_full, $text_title, $params ); ?>

            </div>

            <?php self::DrawElementDescription( $params ); ?>

       </div>

        <?php return ob_get_clean();

    }


    /**
     * Build Multi-Checkbox HTML
     * 
     * @since 1.0
     * 
     * @param boolean $new_item
     * 
     * @param string $con_name_full 
     * 
     * @param string $con_id_full 
     * 
     * @param array $arr_options
     * 
     * @param string|array $db_value 
     * 
     * @param string|array $default_value 
     * 
     * @param string $text_title 
     * 
     * @param array $params 
     * 
     * @return string
     * 
     */
    public static function multi_checkbox($new_item = true, $con_name_full, $con_id_full, $arr_options = array(), $db_value = '', $default_value = false, $text_title = '', $params = array() ) {
        
        $element_classes = array();
        
        $element_classes[] = ( is_array($params) && array_key_exists('style', $params) && in_array( $params['style'], array('inline', 'block') ) ) ? sanitize_text_field( $params['style'] ) : 'block';
        
        ob_start(); ?>


        <div class="srpset-element-wrapper element-multi-checkbox <?php echo join(' ', $element_classes); ?>" data-element-type="<?php echo sanitize_text_field( $params['js_element_type'] ); ?>">
                      
            <?php self::DrawElementTitle( '', $text_title, $params, false ); ?>
              
            <div class="srpset-checkbox-container">
                
                <?php if( is_array($arr_options) && (count($arr_options) > 0) ) {
                          
                    foreach($arr_options as $key => $option) {
                        
                        $check_status = false;
                        
                        // Convert spaces to dashes if exists to avoid bugs
                        $sanitized_key = sanitize_key( $key ); ?>
                                                
                        <?php if( $new_item && ( is_array($default_value) && (count($default_value) > 0) ) ) {

                                  if( is_string($default_value) ) {
                                      $default_value = (array)$default_value;
                                  }

                                  if ( in_array($sanitized_key, $default_value) ) {
                                      $check_status = true;
                                  }

                              }
                              elseif( is_array($db_value) && (count($db_value) > 0) ) {

                                  if( array_key_exists( $sanitized_key, $db_value ) ) {

                                      $check_status = filter_var( $db_value[ $sanitized_key ], FILTER_VALIDATE_BOOLEAN );

                                  }
                                      

                              } ?>
                        
                        <div class="checkbox-inner">
                            <input type="checkbox" class="srpset-checkbox" name="<?php echo sanitize_text_field($con_name_full) . '[' . $sanitized_key . ']'; ?>" id="<?php echo sanitize_text_field($con_id_full) . '_' . $sanitized_key; ?>"
                               <?php if( $check_status ) { echo esc_html('checked="checked"'); } ?> />
                                    
                            <label class="label" for="<?php echo sanitize_text_field($con_id_full) . '_' . $sanitized_key; ?>"><?php echo sanitize_text_field($option); ?></label>
                        </div>

                    <?php }

                } ?>

            </div>

            <?php self::DrawElementDescription( $params ); ?>

       </div>

        <?php return ob_get_clean();

    }


    /**
     * Build Select HTML
     * 
     * @since 1.0
     * 
     * @param boolean $new_item
     * 
     * @param string $con_name_full 
     * 
     * @param string $con_id_full 
     * 
     * @param array $arr_options
     * 
     * @param string|array $db_value 
     * 
     * @param string|array $default_value 
     * 
     * @param string $text_title 
     * 
     * @param array $params 
     * 
     * @return string
     * 
     */
    public static function select( $new_item = true, $con_name_full, $con_id_full, $arr_options, $db_value = '', $default_value='', $text_title = '', $params = array() ){
        
        $element_classes = array();

        $placeholder = ( array_key_exists('placeholder', $params) && !empty( $params['placeholder'] ) ) ? $params['placeholder'] : '';
        
        $locked = ( array_key_exists('locked', $params) && ( TRUE == filter_var( $params['locked'], FILTER_VALIDATE_BOOLEAN ) ) ) ? 'disabled=disabled' : '';
        
        $use_modern_select = ( array_key_exists('modern_select', $params) && ( TRUE == filter_var( $params['modern_select'], FILTER_VALIDATE_BOOLEAN ) ) ) ? true : false;

        $cls = array(
            'oxibug-ddl',
            'ddl-single'
        );

        $cls[] = ( $use_modern_select ) ? 'style-modern' : 'style-basic';

        ob_start(); ?>

        <div class="srpset-element-wrapper element-select select_option <?php echo join(' ', $element_classes); ?>" data-element-type="<?php echo sanitize_text_field( $params['js_element_type'] ); ?>">
            
            <?php self::DrawElementTitle( '', $text_title, $params ); ?>
        
            <select name="<?php echo sanitize_text_field( $con_name_full ); ?>" id="<?php echo sanitize_text_field( $con_id_full ); ?>" 
                    class="<?php echo join( ' ', $cls ); ?>" 
                    data-placeholder="<?php echo sanitize_text_field( $placeholder ); ?>" <?php echo esc_html( $locked ); ?>>

                <?php if( is_array( $arr_options ) && ( count($arr_options) > 0 ) ) {
                          
                    foreach( $arr_options as $key => $option ) { ?>
                        
                        <option value="<?php echo sanitize_text_field($key); ?>" <?php if( $new_item && ( ! is_null( self::CheckSelectElementValue( $default_value ) ) ) ) {

                            echo selected( $default_value, sanitize_text_field( $key ) );

                        } elseif( ! is_null( self::CheckSelectElementValue( $db_value ) ) ) {

                            echo selected( $db_value, sanitize_text_field( $key ) );
                            
                        } ?>><?php echo sanitize_text_field($option); ?></option>

                    <?php }

                } ?>

            </select>

            <?php self::DrawElementDescription( $params ); ?>
            
        </div>

        <?php return ob_get_clean();

    }


    /**
     * Build Multi-Select HTML
     * 
     * @since 1.0
     * 
     * @param boolean $new_item
     * 
     * @param string $con_name_full 
     * 
     * @param string $con_id_full 
     * 
     * @param array $arr_options
     * 
     * @param string|array $db_value 
     * 
     * @param string|array $default_value 
     * 
     * @param string $text_title 
     * 
     * @param array $params 
     * 
     * @return string
     * 
     */
    public static function multi_select($new_item = true, $con_name_full, $con_id_full, $arr_options, $db_value = '', $default_value=array(), $text_title = '', $params = array() ) {
        
        $element_classes = array();
        
        $placeholder = ( array_key_exists('placeholder', $params) && !empty( $params['placeholder'] ) ) ? $params['placeholder'] : '';

        $close_on_select = ( array_key_exists('close_on_select', $params) && is_bool( $params['close_on_select'] ) ) ? (bool)$params['close_on_select'] : true;

        $use_modern_select = ( array_key_exists('modern_select', $params) && ( TRUE == filter_var( $params['modern_select'], FILTER_VALIDATE_BOOLEAN ) ) ) ? true : false;

        $cls = array(
            'oxibug-ddl',
            'ddl-multiple'
        );

        $cls[] = ( $use_modern_select ) ? 'style-modern' : 'style-basic';


        if( is_string($default_value) ) {
            $default_value = (array)$default_value;
        }

        ob_start(); ?>

        <div class="srpset-element-wrapper element-multi-select select_option <?php echo join( ' ', $element_classes ); ?>" data-element-type="<?php echo sanitize_text_field( $params['js_element_type'] ); ?>">

            <?php self::DrawElementTitle( '', $text_title, $params ); ?>
        
            <select name="<?php echo sanitize_text_field($con_name_full); ?>[]" id="<?php echo sanitize_text_field($con_id_full); ?>" class="<?php echo join( ' ', $cls ); ?>" multiple="multiple" 
                    data-placeholder="<?php echo sanitize_text_field( $placeholder ); ?>" data-close-on-select=<?php echo ( $close_on_select ) ? 'true' : 'false'; ?>>
                
                <?php if( is_array($arr_options) && (count($arr_options) > 0) ) {
                          
                    foreach($arr_options as $key => $option) {
                        
                        // Convert spaces to dashes if exists to avoid bugs
                        $key = sanitize_title($key); ?>
                        
                        <option value="<?php echo sanitize_text_field($key); ?>" <?php if( $new_item && ( is_array($default_value) && (count($default_value) > 0) ) ) {
                                                                                 
                            if ( in_array($key, $default_value) ) {
                                echo esc_html(' selected="selected"');
                            }

                        } elseif( is_array($db_value) && (count($db_value) > 0) ) {

                            if( in_array($key, $db_value) ) {
                                echo esc_html(' selected="selected"');
                            }

                        } ?>><?php echo sanitize_text_field($option); ?></option>

                    <?php }
                } ?>

            </select>

            <?php self::DrawElementDescription( $params ); ?>
            
        </div>

        <?php return ob_get_clean();

    }

    /**
     * Build Multi-Select HTML
     * 
     * @since 1.0
     * 
     * @param boolean $new_item
     * 
     * @param string $con_name_full 
     * 
     * @param string $con_id_full
     * 
     * @param string|array $db_value 
     * 
     * @param int $default_value 
     * 
     * @param string $text_title 
     * 
     * @param array $params 
     * 
     * @return string
     * 
     */
    public static function spinner( $new_item = true, $con_name_full, $con_id_full, $db_value = '', $default_value = 0, $text_title = '', $params = array() ) {

        $min_value = 0;
        $max_value = 100;
        $step_value = 1;

        /**
         * 'n' for decimal numbers or 'C' for currency values
         * 
         * To Do: Globalize JS can be included in the future to add culture
         * 
         * */
        $number_format = 'n'; 

        $available_number_formats = array( 'n', 'C' );

        $cur_value = 0;

        if( array_key_exists('min_value', $params) ){
            $min_value = intval($params['min_value']);
        }

        if( array_key_exists('max_value', $params) ){
            $max_value = intval($params['max_value']);
        }

        if( array_key_exists('step_value', $params) ){
            $step_value = intval($params['step_value']);
        }
        
        $number_format = ( array_key_exists('format', $params) && in_array( $params['format'], $available_number_formats ) ) ? sanitize_text_field( $params['format'] ) : 'n';


        if( $new_item && !empty($default_value) ) {

            $def_val = self::con_check($default_value, 'int');
            
            if( is_numeric($def_val) && $def_val >= $min_value && $def_val <= $max_value ){
                $cur_value = intval($def_val);
            }
        }
        elseif( !empty($db_value) ) {

            $db_value = self::con_check($db_value, 'int');

            if( is_numeric($db_value) && $db_value >= $min_value && $db_value <= $max_value ){
                $cur_value = intval($db_value);
            }
        }
        else{
            $cur_value = intval($min_value);
        }
        
        $element_classes = array();


        ob_start(); ?>


        <div class="srpset-element-wrapper element-spinner controller <?php echo join( ' ', $element_classes ); ?>" data-element-type="<?php echo sanitize_text_field( $params['js_element_type'] ); ?>"
             data-min-value="<?php echo intval($min_value); ?>" data-max-value="<?php echo intval($max_value); ?>" data-step-value="<?php echo intval($step_value); ?>"
             data-number-format="<?php echo sanitize_text_field( $number_format ); ?>"
             data-new-item="<?php echo ($new_item) ? 'true' : 'false'; ?>" data-default-value="<?php echo sanitize_text_field( $cur_value ); ?>">

            <?php self::DrawElementTitle( '', $text_title, $params ); ?>

            <div class="spinner-inner">
                
                <input id="<?php echo sanitize_text_field( $con_id_full ); ?>" type="text" class="srpset-spinner" value="<?php echo sanitize_text_field( $cur_value ); ?>" name="<?php echo sanitize_text_field( $con_name_full ); ?>" />
            
            </div>
        
            <?php self::DrawElementDescription( $params ); ?>

        </div>

        <?php return ob_get_clean();

    }


    /**
     * Build Slider HTML
     * 
     * @since 1.0
     * 
     * @param boolean $new_item
     * 
     * @param string $con_name_full 
     * 
     * @param string $con_id_full
     * 
     * @param string|array $db_value 
     * 
     * @param int $default_value 
     * 
     * @param string $text_title 
     * 
     * @param array $params 
     * 
     * @return string
     * 
     */
    public static function slider( $new_item = true, $con_name_full, $con_id_full, $db_value = '', $default_value = 0, $text_title = '', $params = array() ) {

        $id_slider = 'sld_' . $con_id_full . '_value';

        $min_value = 0;
        $max_value = 100;
        $step_value = 1;

        $cur_value = 0;

        if( array_key_exists('min_value', $params) ){
            $min_value = intval($params['min_value']);
        }

        if( array_key_exists('max_value', $params) ){
            $max_value = intval($params['max_value']);
        }

        if( array_key_exists('step_value', $params) ){
            $step_value = intval($params['step_value']);
        }


        if( $new_item && !empty($default_value) ) {

            $def_val = self::con_check($default_value, 'int');
            
            if( is_numeric($def_val) && $def_val >= $min_value && $def_val <= $max_value ){
                $cur_value = intval($def_val);
            }
        }
        elseif( !empty($db_value) ) {

            $db_value = self::con_check($db_value, 'int');

            if( is_numeric($db_value) && $db_value >= $min_value && $db_value <= $max_value ){
                $cur_value = intval($db_value);
            }
        }
        else{
            $cur_value = intval($min_value);
        }
        
        $element_classes = array();
        
        ob_start(); ?>

        <div class="srpset-element-wrapper element-slider controller slider_container <?php echo join( ' ', $element_classes ); ?>" data-element-type="<?php echo sanitize_text_field( $params['js_element_type'] ); ?>"
             data-slider-id="<?php echo sanitize_text_field($id_slider); ?>" data-slidervalueinput-id="<?php echo sanitize_text_field($con_id_full); ?>" data-min-value="<?php echo intval($min_value); ?>" 
             data-max-value="<?php echo intval($max_value); ?>" data-step-value="<?php echo intval($step_value); ?>"
             data-new-item="<?php echo ($new_item) ? 'true' : 'false'; ?>" data-modified="false"
             data-default-value="<?php echo intval($cur_value); ?>">

            <?php self::DrawElementTitle( '', $text_title, $params ); ?>

            <div class="slider-inner">
                <div id="<?php echo sanitize_text_field( $id_slider ); ?>" class="input_slider"></div>

                <input id="<?php echo sanitize_text_field( $con_id_full ); ?>" type="text" class="input_value" readonly="readonly" value="<?php echo sanitize_text_field( $cur_value ); ?>" name="<?php echo sanitize_text_field( $con_name_full ); ?>" />
            </div>


            <?php self::DrawElementDescription( $params ); ?>

        </div>

        <?php return ob_get_clean();

    }

    /**
     * Build Range Slider HTML
     * 
     * @since 1.0
     * 
     * @param boolean $new_item
     * 
     * @param string $con_name_full 
     * 
     * @param string $con_id_full
     * 
     * @param string|array $db_value 
     * 
     * @param string $default_value Two integer values separated by comma
     * 
     * @param string $text_title 
     * 
     * @param array $params 
     * 
     * @return string
     * 
     */
    public static function range_slider( $new_item = true, $con_name_full, $con_id_full, $db_value = '', $default_value = '0,0', $text_title = '', $params = array() ) {

        $id_slider = 'sld_' . $con_id_full . '_value';

        $min_value = 0;
        $max_value = 100;
        $step_value = 1;
        
        $unit = '';

        $unit_positions_array = array('before', 'after');
        $unit_position = 'before';

        $cur_value_min = $cur_value_max = 0;

        $temp_value = '';

        $final_user_value = '';

        if( array_key_exists('min_value', $params) ) {
            $min_value = intval($params['min_value']);
        }

        if( array_key_exists('max_value', $params) ) {
            $max_value = intval($params['max_value']);
        }

        if( array_key_exists('step_value', $params) ) {
            $step_value = intval($params['step_value']);
        }

        if( array_key_exists('unit', $params) ) {
            $unit = sanitize_text_field($params['unit']);
        }

        if( array_key_exists('unitposition', $params) ) {
            $unit_position = ( in_array($params['unitposition'], $unit_positions_array) ) ? sanitize_text_field($params['unitposition']) : 'before';
        }


        if( $new_item && !empty($default_value) ) {

            $temp_value = sanitize_text_field($default_value);
            
            // User Default Input Example [ 150, 250 ]
            $user_value = @explode(',', $temp_value);
            
            if( is_array($user_value) && (count($user_value) > 1) && (count($user_value) < 3) ){
                $cur_value_min = ($user_value[0] >= $min_value) ? intval($user_value[0]) : $min_value;
                $cur_value_max = ($user_value[1] <= $max_value) ? intval($user_value[1]) : $max_value;
            }
        }
        elseif( is_array($db_value) && ( count($db_value) > 0 ) ) {

            $unit_position = ( array_key_exists('unitposition', $db_value) && in_array($db_value['unitposition'], $unit_positions_array) ) ? $db_value['unitposition'] : 'before';
            $unit = ( array_key_exists('unit', $db_value) && !empty($db_value['unit']) ) ? $db_value['unit'] : '';

            $cur_value_min = ( array_key_exists('min', $db_value) && ($db_value['min'] >= $min_value) ) ? intval($db_value['min']) : intval($min_value);

            $cur_value_max = ( array_key_exists('max', $db_value) && ($db_value['max'] <= $max_value) ) ? intval($db_value['max']) : intval($max_value);
            
        }
        else{
            $cur_value_min = intval($min_value);
            $cur_value_max = intval($max_value);
        }

        
        if( $unit_position == 'before' ) {
            $final_user_value = $unit . $cur_value_min . ' ' . $unit . $cur_value_max;
        }
        else {
            $final_user_value = $cur_value_min . $unit . ' ' . $cur_value_max . $unit;
        }
        

        $element_classes = array();

        ob_start(); ?>


        <div class="srpset-element-wrapper element-range-slider controller slider_container <?php echo join(' ', $element_classes); ?>" data-element-type="<?php echo sanitize_text_field( $params['js_element_type'] ); ?>"
             data-new-item="<?php echo ($new_item) ? 'true' : 'false'; ?>" data-slider-id="<?php echo sanitize_text_field($id_slider); ?>" data-slidervalueinput-id="<?php echo sanitize_text_field($con_id_full); ?>" 
             data-min-value="<?php echo intval($min_value); ?>" data-max-value="<?php echo intval($max_value); ?>" data-step-value="<?php echo intval($step_value); ?>" 
             data-default-value-min="<?php echo sanitize_text_field($cur_value_min); ?>" data-default-value-max="<?php echo sanitize_text_field($cur_value_max); ?>"
             data-current-value-min="<?php echo sanitize_text_field($cur_value_min); ?>" data-current-value-max="<?php echo sanitize_text_field($cur_value_max); ?>" 
             data-unit="<?php echo sanitize_text_field($unit); ?>" data-unit-position="<?php echo sanitize_text_field($unit_position); ?>"
             data-modified="false">

            <div class="element-inner">
                
                <?php self::DrawElementTitle( '', $text_title, $params ); ?>
            
                <input type="text" class="input_value_user" readonly="readonly" value="<?php echo sanitize_text_field( $final_user_value ); ?>" />
            </div>


            <div id="<?php echo sanitize_text_field($id_slider); ?>" class="input_slider"></div>
            
            
            <input id="<?php echo sanitize_text_field($con_id_full); ?>_unitposition" type="hidden" value="<?php echo sanitize_text_field($unit_position); ?>" name="<?php echo sanitize_text_field($con_name_full); ?>[unitposition]" />

            <input id="<?php echo sanitize_text_field($con_id_full); ?>_unit" type="hidden" value="<?php echo sanitize_text_field($unit); ?>" name="<?php echo sanitize_text_field($con_name_full); ?>[unit]" />

            <input id="<?php echo sanitize_text_field($con_id_full); ?>_min" type="hidden" value="<?php echo sanitize_text_field($cur_value_min); ?>" name="<?php echo sanitize_text_field($con_name_full); ?>[min]" />

            <input id="<?php echo sanitize_text_field($con_id_full); ?>_max" type="hidden" value="<?php echo sanitize_text_field($cur_value_max); ?>" name="<?php echo sanitize_text_field($con_name_full); ?>[max]" />


            <?php self::DrawElementDescription( $params ); ?>

        </div>


        <?php return ob_get_clean();

    }

    /**
     * Build Radio Images HTML
     * 
     * @since 1.0
     * 
     * @param boolean $new_item
     * 
     * @param string $con_name_full 
     * 
     * @param string $con_id_full 
     * 
     * @param array $arr_con_images
     * 
     * @param string|array $db_value 
     * 
     * @param string|array $default_value 
     * 
     * @param string $text_title 
     * 
     * @param array $params 
     * 
     * @return string
     * 
     */
    public static function radio_images( $new_item = true, $con_name_full, $con_id_full, $arr_con_images = array(), $db_value = '', $default_value='', $text_title = '', $params = array() ) {
        
        $element_classes = array();

        ob_start(); ?>


        <div class="srpset-element-wrapper element-optionimages optionimages-container <?php echo join( ' ', $element_classes ) ?>" data-element-type="<?php echo sanitize_text_field( $params['js_element_type'] ); ?>">

            <?php $arr_option_image_values = $arr_con_images;

            if( is_array( $arr_option_image_values ) ) { ?>

                <?php self::DrawElementTitle( '', $text_title, $params, false ); ?>

                <ul id="<?php echo sanitize_text_field( $con_id_full ) . '_list'; ?>" class="images-list clearfix">
                
                    <?php foreach ( $arr_option_image_values as $option => $image ) {
                    
                    // Convert spaces to dashes if exists to avoid bugs
                    $option = sanitize_title( $option ); ?>
                
                    <li>
                        <input id="<?php echo sanitize_text_field($con_id_full) . '_' . sanitize_text_field($option); ?>" class="input_option_image" name="<?php echo sanitize_text_field($con_name_full); ?>" type="radio" 
                            value="<?php echo sanitize_text_field($option); ?>"
                           
                        <?php if( $new_item && is_string( $default_value ) && ( ! empty( $default_value ) ) ) {

                            checked($default_value, $option, true); 
                        }
                                 
                        elseif( is_string( $db_value ) && ( ! empty( $db_value ) ) ) { 

                            $db_value_final = sanitize_text_field($db_value);

                            checked( $db_value_final, $option, true );

                        }  ?> />

                        <a class="checkbox-select" href="#"><img src="<?php echo esc_url( $image ); ?>" /></a>
                    </li>
                
                    <?php } // foreach ?>

                </ul>
            
            <?php } // if array ?>


            <?php self::DrawElementDescription( $params ); ?>

        </div>

        
        <?php return ob_get_clean();

    }


    /**
     * Build Radio Buttons HTML
     * 
     * @since 1.0
     * 
     * @param boolean $new_item
     * 
     * @param string $con_name_full 
     * 
     * @param string $con_id_full 
     * 
     * @param array $arr_options
     * 
     * @param string $db_value 
     * 
     * @param string $default_value 
     * 
     * @param string $text_title 
     * 
     * @param array $params 
     * 
     * @return string
     * 
     */
    public static function radio( $new_item = true, $con_name_full, $con_id_full, $arr_options = array(), $db_value = '', $default_value='', $text_title = '', $params = array() ) {
        
        $style_final = ( is_array($params) && array_key_exists('style', $params) && in_array( $params['style'], array('inline', 'block') ) ) ? sanitize_text_field( $params['style'] ) : 'block';
        
        $element_classes = array();

        ob_start(); ?>

        <div class="srpset-element-wrapper element-radio radio-container <?php echo join( ' ', $element_classes ); ?>" data-element-type="<?php echo sanitize_text_field( $params['js_element_type'] ); ?>">

            <?php if( is_array( $arr_options ) && count( $arr_options ) > 0 ) { ?>

                <?php self::DrawElementTitle( '', $text_title, $params, false ); ?>

                <ul id="<?php echo sanitize_text_field($con_id_full) . '_list'; ?>" class="radio-list <?php echo sanitize_text_field($style_final); ?> clearfix">
                    <?php foreach ($arr_options as $opt_key => $opt_val) {
                    
                    // Convert spaces to dashes if exists to avoid bugs
                    $opt_key = sanitize_title($opt_key); ?>

                    <li>
                        <input id="<?php echo sanitize_text_field($con_id_full) . '_' . sanitize_title($opt_key); ?>" class="input_option_normal" name="<?php echo sanitize_text_field($con_name_full); ?>" type="radio" 
                               value="<?php echo sanitize_text_field($opt_key); ?>"
                           
                               <?php if( $new_item && is_string( $default_value ) && ( ! empty( $default_value ) ) ) {
                                     
                                    checked( $default_value, $opt_key, true);

                                }
                                elseif( is_string( $db_value ) && ( ! empty( $db_value ) ) ) { 
                                  
                                    $db_value_final = sanitize_text_field( $db_value );

                                    checked( $db_value_final, $opt_key, true); 
                                     
                                } ?> />

                        <label for="<?php echo sanitize_text_field($con_id_full) . '_' . sanitize_text_field($opt_key); ?>" class=""><?php echo sanitize_text_field($opt_val); ?></label>
                    </li>
                    <?php } // foreach ?>
                </ul>

            <?php } // if array ?>


            <?php self::DrawElementDescription( $params ); ?>

        </div>


        <?php return ob_get_clean();

    }


    /**
     * Build Upload Media Element HTML
     * 
     * @since 1.0
     * 
     * @param boolean $new_item
     * 
     * @param string $con_name_full 
     * 
     * @param string $con_id_full 
     * 
     * @param string $db_value 
     * 
     * @param string $default_value 
     * 
     * @param string $text_title 
     * 
     * @param array $params 
     * 
     * @return string
     * 
     */
    public static function upload_media( $new_item = true, $con_name_full, $con_id_full, $db_value = '', $default_value='', $text_title = '', $params = array() ) {
        
        $media_url = '';

        $type = 'image'; 
        $subtype = '';
        
        $element_classes = $remove_button_classes = $preview_container_classes = array();


        if( $new_item && is_array($default_value) && (count($default_value) > 0) ) {
            
            if( isset($default_value['url']) && !empty($default_value['url']) ) {
                $media_url = $default_value['url'];
            }

            if( isset($default_value['type']) && !empty($default_value['type']) ) {
                $type = $default_value['type'];
            }

            if( isset($default_value['subtype']) && !empty($default_value['subtype']) ) {
                $subtype = $default_value['subtype'];
            }

        }
        elseif( is_array($db_value) && ( count($db_value) > 0 ) ) {

            if( isset($db_value['url']) && !empty($db_value['url']) ) {
                $media_url = $db_value['url'];
            }

            if( isset($db_value['type']) && !empty($db_value['type']) ) {
                $type = $db_value['type'];
            }

            if( isset($db_value['subtype']) && !empty($db_value['subtype']) ) {
                $subtype = $db_value['subtype'];
            }

        }
        
        
        $remove_button_classes[] = 'input-button extra-button warning has-icon btn_media_remove';
        $remove_button_classes[] = empty( $media_url ) ? 'hidden' : '';

        $enable_preview = ( array_key_exists('enable_preview', $params) && is_bool( $params['enable_preview'] ) ) ? (bool)$params['enable_preview'] : true;
        
        $preview_container_classes[] = 'srp_media_preview';
        $preview_container_classes[] = ( $enable_preview && ( ! empty( $media_url ) ) ) ? 'has-data' : '';

        $supported_types = ( array_key_exists('media_types', $params) && !empty( $params['media_types'] ) ) ? $params['media_types'] : '';

        ob_start();
        
        if( function_exists('wp_enqueue_media') ) { ?>

            <div class="srpset-element-wrapper element-upload-media controller upload_media <?php echo join( ' ', $element_classes ); ?>" data-element-type="<?php echo sanitize_text_field( $params['js_element_type'] ); ?>"
                 data-enable-preview=<?php echo ( $enable_preview ) ? 'true' : 'false'; ?> 
                 data-media-types="<?php echo esc_html( $supported_types ); ?>">

            <?php self::DrawElementTitle( $con_id_full, $text_title, $params ); ?>

            <div id="<?php echo sanitize_text_field($con_id_full); ?>_container" class="element-inner srpset-upload-media-container">

                <div class="srpset-input-group">
                    
                    <input type="text" class="input-text input_media" readonly="readonly" name="<?php echo sanitize_text_field($con_name_full); ?>[url]" id="<?php echo sanitize_text_field($con_id_full); ?>_url" value="<?php echo esc_url($media_url); ?>" />
                    <input type="hidden" class="input_type" name="<?php echo sanitize_text_field($con_name_full); ?>[type]" id="<?php echo sanitize_text_field($con_id_full); ?>_type" value="<?php echo sanitize_text_field($type); ?>" />
                    <input type="hidden" class="input_subtype" name="<?php echo sanitize_text_field($con_name_full); ?>[subtype]" id="<?php echo sanitize_text_field($con_id_full); ?>_subtype" value="<?php echo sanitize_text_field($subtype); ?>" />
                
                    <span class="input-button has-icon btn_media_upload" data-jqueryui-tooltip="true" title="<?php esc_html_e( 'Upload Media', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ); ?>">
                        <i class="srpset-trigger-icon"></i>
                    </span>
                    
                    <span class="<?php echo join( ' ', $remove_button_classes ); ?>" data-jqueryui-tooltip="true" title="<?php esc_html_e( 'Remove', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ); ?>">
                        <i class="srpset-trigger-icon"></i>
                    </span>

                </div>

                <div class="<?php echo join( ' ', $preview_container_classes ); ?>">

                    <?php if( $enable_preview && ( ! empty( $media_url ) ) ) {
                              
                        switch( $type ) {
                                  
                            case 'image': ?>
                            
                                <img id="image_<?php echo sanitize_text_field($con_id_full); ?>" src="<?php echo esc_url($media_url); ?>" class="" alt="" />

                            <?php break;
                                  
                            case 'video': {
                                    
                                if( ( ! empty( $subtype ) ) && ( in_array( $subtype, Admin_ElementsFactory::$supported_html5_video ) ) ) { ?>
              
                                    <video width="400" height="300" controls><source src="<?php echo esc_url( $media_url ); ?>" type="<?php echo Admin_ElementsFactory::ReturnMIME_Type( $subtype ); ?>" /></video>

                            <?php }
                                    
                            } break;

                            case 'audio': {
                                
                                if( ( ! empty( $subtype ) ) && ( in_array( $subtype, Admin_ElementsFactory::$supported_html5_audio ) ) ) { ?>
                        
                                    <audio controls><source src="<?php echo esc_url( $media_url ); ?>" type="<?php echo Admin_ElementsFactory::ReturnMIME_Type( $subtype ); ?>" /></audio>            

                            <?php }

                            } break;


                            case 'application':
                            case 'text': {
                                
                                if( ( ! empty( $subtype ) ) ) { ?>

                                    <span class="type-application subtype-<?php echo esc_attr( self::element_upload_media_GetMediaTypeEquivalentCssClass($subtype) ); ?>"><i class="srpset-trigger-icon"></i></span>

                            <?php }

                            } break;


                            default:
                                break;

                        }
                              
                    } ?>

                </div>
                
                <?php self::DrawElementDescription( $params ); ?>

            </div>

        </div>

        <?php } else { ?>

            <div class="warning"><?php echo esc_html__( 'Please update your WordPress copy to the latest version.', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ); ?></div>

        <?php }


        return ob_get_clean();

    }


    /**
     * Get the equivalent CSS class name for the meida subtype to determine which icon
     * 
     * Will appear rather than the media
     * 
     * @since 1.0
     * 
     * @param string $subtype 
     * 
     * @return string
     * 
     */
    protected static function element_upload_media_GetMediaTypeEquivalentCssClass( $subtype ) {
        
        $output = '';

        switch ($subtype) {

            case 'tar':
            case 'zip': 
            case 'gz':
            case 'gzip':
            case 'rar':                
            case '7z': {
                $output = 'compressed';
            } break;

            case 'pdf': {
                $output = 'pdf';
            } break;

            case 'psd':
            case 'xcf': {
                $output = 'octet';                
            } break;

            case 'wri':
            case 'doc':
            case 'docx':
            case 'docm':
            case 'dotx':
            case 'dotm': {
                $output = 'ms-word';
            } break;

            case 'xla':
            case 'xls':
            case 'xlt':
            case 'xlw':
            case 'xlsx':
            case 'xlsm':
            case 'xlsb':
            case 'xltm':
            case 'xlam': {
                $output = 'ms-excel';
            } break;

            case 'pot':
            case 'pps':
            case 'ppt':
            case 'pptm':
            case 'ppsm':
            case 'ppsx':
            case 'potm':
            case 'ppam':
            case 'sldx':
            case 'sldm': {
                $output = 'ms-powerpoint';
            } break;

        	default:
                $output = 'file';
            break;

        }
        
        return $output;

    }


    /**
     * Build Upload Gallery Element HTML
     * 
     * @since 1.0
     * 
     * @param boolean $new_item
     * 
     * @param string $con_name_full 
     * 
     * @param string $con_id_full 
     * 
     * @param string $db_value 
     * 
     * @param string $default_value 
     * 
     * @param string $text_title 
     * 
     * @param array $params 
     * 
     * @return string
     * 
     */
    public static function upload_gallery($new_item = true, $con_name_full, $con_id_full, $db_value = '', $default_value='', $text_title = '', $params = array() ) {
        
        $value_final = '';
        
        if( $new_item && !empty($default_value) ) {
            
            $value_final = $default_value;

        } elseif( !empty($db_value) ) {

            $value_final = ( is_string( $db_value ) && ( ! empty( $db_value ) ) ) ? $db_value : '';
            
        }
        

        $element_classes = $clear_gallery_classes = $preview_container_classes = array();
        
        $clear_gallery_classes[] = 'input-button extra-button warning has-icon btn_media_clear';
        $clear_gallery_classes[] = ( empty( $value_final ) ) ? 'hidden' : '';


        $preview_container_classes[] = 'srp_media_preview';
        $preview_container_classes[] = ( !empty( $value_final ) && has_shortcode( $value_final, 'gallery' ) ) ? 'has-data' : '';

        ob_start();

        if (function_exists('wp_enqueue_media')) { ?>

            <div class="srpset-element-wrapper element-upload-gallery controller upload_gallery <?php echo join(' ', $element_classes); ?>" data-element-type="<?php echo sanitize_text_field( $params['js_element_type'] ); ?>">

                <?php self::DrawElementTitle( $con_id_full, $text_title, $params, false ); ?>

                <div id="<?php echo sanitize_text_field($con_id_full); ?>_gallery_container" class="element-inner srpset-upload-media-container gallery-sortable">
                
                    <div class="srpset-input-group">

                        <input type="text" class="input-text input_media" readonly="readonly" name="<?php echo sanitize_text_field($con_name_full); ?>" id="<?php echo sanitize_text_field($con_id_full); ?>" value="<?php echo esc_html( $value_final ); ?>" />

                        <span class="input-button has-icon btn_media_upload" data-jqueryui-tooltip="true" title="<?php esc_html_e( 'Add/Edit Gallery', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ); ?>">
                            <i class="srpset-trigger-icon"></i>
                        </span>

                        <span class="<?php echo join( ' ', $clear_gallery_classes ); ?>" data-jqueryui-tooltip="true" title="<?php esc_html_e( 'Clear', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ); ?>">
                            <i class="srpset-trigger-icon"></i>
                        </span>
                    
                    </div>

                    <div class="<?php echo join( ' ', $preview_container_classes ); ?>">

                        <?php $gal_att_ids = $arr_gal_shortcode = array();
                
                            $gal_shortcode = $value_final;
                
                            if( ! empty( $gal_shortcode ) && has_shortcode( $gal_shortcode, 'gallery' ) ) {

                                // parse shortcode to get 'ids' param
                                $pattern = get_shortcode_regex();
                                preg_match('/' . $pattern . '/s', $gal_shortcode, $match);
                                $arr_gal_shortcode = @shortcode_parse_atts($match[3]);

                            }
                
                            if ( is_array( $arr_gal_shortcode ) && ( count($arr_gal_shortcode) > 0 ) && isset( $arr_gal_shortcode['ids'] ) ) {

                                $gal_att_ids = @explode(',',  $arr_gal_shortcode['ids']);

                            }
                
                            if( is_array( $gal_att_ids ) && ( count($gal_att_ids) > 0 ) ) {

                                $img_attributes = $img_src = $img_title = '';

                                $sizes = array();

                                foreach ( $gal_att_ids as $att_id ) {

                                    $img_attributes = wp_get_attachment_image_src( $att_id );
                                
                                    if( $img_attributes ) {

                                        $img_src = $img_attributes[0];

                                        if ( is_ssl() ) {

                                            $img_src = str_replace('http://', 'https://', $img_src);

                                        }

                                    }
                                
                                    echo '<span data-id="' . sanitize_text_field( $att_id ) . '" title="' . sanitize_text_field( $img_title ) . '"><img src="' . esc_url( $img_src ) . '" width="' . esc_attr( $img_attributes[1] ) . '" height="' . esc_attr( $img_attributes[2] ) . '" alt="" /><i class="srpset-trigger-icon remove"></i></span>';


                                    unset( $sizes );
                                    $sizes = array();

                                }

                            } ?>

                    </div>

                    <?php self::DrawElementDescription( $params ); ?>

                </div>
                
            </div>


        <?php } else { ?>
            
            <div class="srp-warning"><?php echo esc_html__( 'Please update your WordPress copy to the latest version!', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ); ?></div>

        <?php }
        

        return ob_get_clean();

    }

}