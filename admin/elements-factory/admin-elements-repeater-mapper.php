<?php
namespace UltimateEmailValidator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Draw HTML elemennts inside repeater control
 * 
 * @author  Oxibug 
 * @version 1.0.0
 */
class Admin_ElementsRepeaterMapper {

    private static $_instance = null;
    
    /**
     * Main repeater parameters
     * 
     * @var     array
     * @since   1.0.0
     * @access  private
     * 
     */
    private $repeater_params;

    protected static $repeaters_count = 0;

    protected static $mini_repeaters_count = 0;
    
    /**
     * Take an instance of class
     * 
     * @return Admin_ElementsRepeaterMapper
     * 
     * @since   1.0.0
     * @access  public
     * @static
     */
    public static function instance() {
        
        if( is_null( self::$_instance ) ) {
            
            self::$_instance = new self;

            self::$_instance->repeater_params = array(
                'buttons'   => array(
                    'show_add'      => false,
                    'show_delete'   => false,
                    'show_collapse' => true,
                    'show_sort'     => false,
                ),
                'text'      => array(
                    'add_new'       => esc_html__( 'Add New Item', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN )
                )
            );

        }

        return self::$_instance;

    }

    /**
     * Get Repeater Controls
     * 
     * @param mixed $repeater_controls 
     * 
     */
    private function __construct( ) {
    }
    
    /**
     * 
     * Create repeater and its controls according to the database saved values,
     * Otherwise (New Page) create the repeater's main container and [Add New Item] button
     * 
     * @since 1.0
     * 
     * @param bool $page_builder (Required) 
     * 
     * @param array $control (Required) 
     * 
     * @param array $get_meta (Required)
     * 
     * @param array $pageBuilderIDsOptions (Required for $page_builder) an array of all important IDs to name repeater controls correctly
     * 
     */
    public function repeater( $control, $saved_repeater = '', $group_id = null ) {
        
        self::$repeaters_count++;

        $builder_has_items = FALSE;
                
        $element_id = $control['id'];

            
        $builder_item_id = sanitize_text_field( $control['id'] );
            
        /**
         * A unique ID for each repeater
         * 
         * VI: All IDs collected and used in JS to avoid
         * move repeaters items from one to another
         * 
         * @var string
         * */
        $repeater_container_id = Admin_Components::instance()->element_id( '', $control['id'], 'container', null );

        $repeater_shortcode_id = sprintf( 'rep_shortcode_%1$s', $element_id );

        
        /* FIX */
        $builder_naked_id = sanitize_text_field( $control['id'] );
        
        /* Added Feature */
        $custom_params = ( array_key_exists( 'params', $control ) ) ? $control['params'] : array();
        $params = wp_parse_args( $custom_params, self::$_instance->repeater_params );

        $i = 0;
        
        $repeater_classes = array();
        $repeater_classes[] = 'srp-repeater-container';
        $repeater_classes[] = 'devcore-repeater-number-' . self::$repeaters_count; ?>


        <div id="<?php echo sanitize_text_field( $repeater_container_id ); ?>"
             class="<?php echo join( ' ', $repeater_classes ); ?>"
             data-element-type="repeater"
             data-repeater-naked-id="<?php echo sanitize_text_field( $builder_naked_id ); ?>"
             data-repeater-group-id="<?php echo ( ! is_null( $group_id ) ) ? sanitize_text_field( $group_id ) : ''; ?>"
             data-repeater-id="<?php echo sanitize_text_field( $builder_naked_id ); ?>"
             data-repeater-count="<?php echo esc_attr( self::$repeaters_count ); ?>">
            
            <div class="parent-rep-item loading-wrp repeater-loading hidden">
                <i class="loading-inner absolute large oxibug-loading-anim-spin"></i>
            </div>

            <div id="<?php echo sanitize_text_field( $repeater_shortcode_id ); ?>" class="parent-rep-item shortcode-popup-container" style="display:none;">
                <div class="shortcode-popup-inner">

                </div>
            </div>

            <div class="parent-rep-item block-header-main">
                <div class="section-left">
                    <h3 class="title"><?php echo sanitize_text_field( $control['title'] ); ?></h3>
                </div>

                <div class="section-right hidden">
                    <div class="controls-container clearfix">
                        
                        <div class="btn-control show-all">
                            <a href="#" class="srp-btn style-2 btn_block_show_all devcore-tooltip-trigger" data-jqueryui-tooltip="true" title="<?php esc_html_e('Show All', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN); ?>"><i class="oxb-trigger-icon"></i></a>
                        </div>

                        <div class="btn-control collapse-all">
                            <a href="#" class="srp-btn style-2 btn_block_collapse_all devcore-tooltip-trigger" data-jqueryui-tooltip="true" title="<?php esc_html_e('Collapse All', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN); ?>"><i class="oxb-trigger-icon"></i></a>
                        </div>

                    </div>
                </div>
            </div>

            <?php if( is_array( $saved_repeater ) && count( $saved_repeater ) > 0 ) {

                // echo print_r($saved_repeater);
                
                $builder_has_items = TRUE;
                
                foreach( $saved_repeater as $cur_block ) {

                    $i++;

                    if( $control['controls'] && array_key_exists('controls', $control) && is_array($control['controls']) ){

                        self::$_instance->get_elements_backend_html( $builder_naked_id, $control['controls'], $cur_block, $i, false, $group_id, $params );

                    }
                    
                } //foreach

            } // is_array


            // New Repeater - OR - Send the counter $i
            $i++;

            if( array_key_exists('controls', $control) && is_array($control['controls']) && ( count($control['controls']) > 0 ) ) {

                echo '<div class="ui-sortable-disabled builder-factory hidden">';
                
                $arr_current_repeater = array(
                    'has_items'         => $builder_has_items ? 'true' : 'false',
                    'builder_container' => sanitize_text_field( $repeater_container_id ),
                    'builder_cur_item'  => esc_attr($i),
                    'controls_count'    => count( $control['controls'] ),
                    'json_controls'     => wp_json_encode( $control['controls'] ),
                    'params'            => wp_json_encode( $params )

                ); ?>

                <input type="hidden" id="<?php echo sanitize_text_field($builder_naked_id) . '_hidden_curitem'; ?>" value="<?php echo esc_attr($i); ?>" />
                <input type="hidden" id="<?php echo sanitize_text_field($builder_naked_id) . '_hidden_cur_repeater_data'; ?>" value="<?php echo HelperFactory::instance()->maybe_base64_encode( $arr_current_repeater ); ?>" />

                <?php echo '</div><!-- builder factory -->';

            } // Check $value['controls'] ?>

            <div class="parent-rep-item repeater-add-item ui-sortable-disabled">
                <div class="btn-control add-item">

                    <a href="#" class="srp-btn style-2 btn_block_add"><i class="oxb-trigger-icon"></i><?php echo esc_html( $params['text']['add_new'] ); ?></a>
                
                </div>
            </div>

        </div><!-- srp-repeater-container END -->

    <?php } // function repeater


    /**
     * 
     * Get repeater's HTML controls, Use in both Client-Side and Server-Side
     * 
     * @since 1.0
     * 
     * @param string $repeater_id (Required) Repeater Unique ID
     * 
     * @param array $repeater_controls (Required) The array of repeater's sensitive data [has_items, builder_container, builder_cur_item, controls_count, json_controls, json_groups]
     * 
     * @param array $cur_block (Optional) Use it ONLY in old posts already have data saved in database
     * 
     * @param integer $current_item_number (Required) The position the repeater's item will be created
     * 
     * @param boolean $new_item (Required) Check whether this item is new by user or already saved in DB
     * 
     * @param array $params_user (Optional)
     * 
     */
    public function get_elements_backend_html( $repeater_id, $repeater_controls, $cur_block = array(), $current_item_number, $new_item = true, $group_id = null, $params_user = array() ) {
        
        $block_classes = array();

        $collapse_tooltip_text = ( $new_item ) ? esc_html__( 'Collapse', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ) : esc_html__( 'Show', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN );
        
        $params = wp_parse_args( $params_user, self::$_instance->repeater_params );

        if( is_array($repeater_controls) && (count($repeater_controls) > 0) ) {
            
            $block_classes[] = 'parent-rep-item';
            $block_classes[] = 'option-item';
            $block_classes[] = 'builder-item';
            $block_classes[] = 'repeater-item-' . sanitize_text_field( $current_item_number );
            $block_classes[] = 'clearfix'; ?>

            <div class="<?php echo join( ' ', $block_classes ); ?>" data-curitem="<?php echo sanitize_text_field( $current_item_number ); ?>">
                        
                <div class="d-flex align-items-center justify-content-between block-header parent-rep-header repeater-block-title-header handle-sorting">
                    
                    <div class="section-left mr-auto">
                        <div class="number-badge"><?php echo sanitize_text_field( $current_item_number ); ?></div>
                        <h4 class="user-js-title"></h4>
                    </div>

                    <div class="section-right">
                        <div class="d-flex controls-container clearfix">

                            <?php if( $params['buttons']['show_add'] ) { ?>
                                <div class="flex-fill btn-control add-item"><a href="#" class="srp-btn style-2 btn_block_add devcore-tooltip-trigger" data-jqueryui-tooltip="true" title="<?php esc_html_e( 'Add New Item', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ); ?>"><i class="devcore-header-icon oxb-trigger-icon"></i></a></div>
                            <?php } if( $params['buttons']['show_delete'] ) { ?>
                                <div class="flex-fill btn-control remove-item"><a href="#" class="srp-btn style-2 btn_block_remove devcore-tooltip-trigger" data-jqueryui-tooltip="true" title="<?php esc_html_e( 'Delete', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ); ?>"><i class="devcore-header-icon oxb-trigger-icon"></i></a></div>
                            <?php } if( $params['buttons']['show_collapse'] ) { ?>
                                <div class="flex-fill btn-control collapse-item"><a href="#" class="srp-btn style-2 btn_block_collapse devcore-tooltip-trigger" data-jqueryui-tooltip="true" title="<?php echo sanitize_text_field( $collapse_tooltip_text ); ?>"><i class="devcore-header-icon oxb-trigger-icon"></i></a></div>
                            <?php } if( $params['buttons']['show_sort'] ) { ?>
                                <div class="flex-fill btn-control move-item"><span class="srp-btn style-2 btn_block_move"><i class="devcore-header-icon oxb-trigger-icon"></i></span></div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="spacer"></div>

                <div class="parent-rep-inside srp-inside-block clearfix <?php echo ( $new_item ) ? '' : 'collapsed animated fadeIn'; ?>" data-collapsed="<?php echo ( $new_item ) ? 'false' : 'true'; ?>">
                            
                    <div class="controller-items row clearfix">
    
                        <?php foreach ( $repeater_controls as $cur_control ) {

                                self::$_instance->get_elements_backend_html_helper( $cur_control, $repeater_id, $cur_block, $current_item_number, $new_item, $group_id );
                                  
                        } ?>
                
                    </div><!-- controller-items -->
                        
            </div><!-- srp-inside-block -->
            
        </div>

    <?php }

    }


    /**
     * 
     * 
     * @param mixed $cur_control 
     * @param mixed $repeater_id 
     * @param mixed $cur_repeater 
     * @param mixed $current_item_number 
     * @param mixed $new_item 
     * @param mixed $group_id 
     */
    protected function get_elements_backend_html_helper( $cur_control, $repeater_id, $cur_repeater = array(), $current_item_number, $new_item = true, $group_id = null ) {
        
        $repeater_naked_id = $repeater_id;

        // Controller Array Variables
        $bu_con_name = $bu_con_id = $bu_con_css_class = $bu_con_text_title = $bu_con_default_value = $bu_con_db_value = '';
        
        $bu_con_slider_id = '';
        $bu_con_slider_min_value = 0;
        $bu_con_slider_max_value = 100;
        $bu_con_slider_step_value = 1; 
        
        $js_has_switchery = false;


        /* Reset Params Array */
        $params_array = $element_classes =  array();

        if( $cur_control && is_array($cur_control) && ( count($cur_control) > 0 ) ) {
            
            /* 
             * == Fix ==
             * 
             * NON tab pages 
             * 
             * @see {page-map.php} - To avoid using left pane in some pages just make the section's key a number
             * 
             * */
            if( is_null( $group_id ) || empty( $group_id ) || is_numeric( $group_id ) ) {
                $group_id = null;
            }
            
            if( array_key_exists('id', $cur_control) && 
                array_key_exists('type', $cur_control) && 
                !empty( $cur_control['id'] ) && 
                !empty( $cur_control['type'] ) ) {
                
                
                $cur_item_id = sanitize_text_field( $cur_control['id'] );

                $bu_con_name = Admin_Components::instance()->element_name( $repeater_naked_id, $current_item_number, $cur_item_id, $group_id );
                $repeater_id = Admin_Components::instance()->element_id( '', $repeater_naked_id, '', $group_id );
            
                $bu_con_id = $repeater_id . '_' . $current_item_number . '_' . $cur_item_id . '_id';

                $bu_con_css_class = Admin_ElementsTemplates::con_check( 'css_class', 'array', $cur_control );

                $bu_con_text_title = Admin_ElementsTemplates::con_check( 'title', 'array', $cur_control );
                                
                $bu_con_default_value = Admin_ElementsTemplates::con_check( 'default', 'array', $cur_control );


                /* For Already Saved Repeaters */
                $bu_con_db_value = '';

                if( is_array( $cur_repeater ) && ( count( $cur_repeater ) > 0 ) ){
                    
                    $cur_item_db_value = Admin_ElementsTemplates::con_check( 'id', 'array', $cur_control );
                    
                    if( $cur_item_db_value && !empty($cur_item_db_value) ) {

                        $bu_con_db_value = isset($cur_repeater[$cur_item_db_value]) ? $cur_repeater[$cur_item_db_value] : '';
                        
                    }
                }

                if( array_key_exists('params', $cur_control) && is_array($cur_control['params']) && ( count($cur_control['params']) > 0 ) ) {

                    $params_array = $cur_control['params'];

                }

                /* 
                 * Fix:
                 * To remove features like [Required] attribute 
                 * from elements belongs to repeater 
                 * */
                $params_array['element_source'] = 'repeater';

                /* After Initializing $params_array */
                $params_array['description'] = Admin_ElementsTemplates::con_check( 'description', 'array', $cur_control );

                
                $params_array['js_element_type'] = array_key_exists( 'js_element_type', $cur_control ) ? $cur_control['js_element_type'] : '';


                $params_array['is_new_item'] = $new_item;
                

                switch( $cur_control['type'] ) {
                                        
                    case 'text': {
                        
                            echo Admin_ElementsTemplates::text( $new_item, $bu_con_name, $bu_con_id, $bu_con_db_value, $bu_con_default_value, $bu_con_text_title, $params_array);

                        } break;

                    case 'textarea': {

                        echo Admin_ElementsTemplates::textarea( $new_item, $bu_con_name, $bu_con_id, $bu_con_db_value, $bu_con_default_value, $bu_con_text_title, $params_array );

                    } break;

                case 'select': {

                        if( array_key_exists('options', $cur_control) && is_array($cur_control['options']) && count($cur_control['options']) > 0 ) {

                            echo Admin_ElementsTemplates::select($new_item, $bu_con_name, $bu_con_id, $cur_control['options'], $bu_con_db_value, $bu_con_default_value, $bu_con_text_title, $params_array);

                        } else {
                            
                            echo wp_kses_post( sprintf( __( '<div class="note warning">There\'s no array provided to display in select box, control [ <b>%s</b> ]</div>', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), $cur_control['id'] ) );

                        }

                    } break;

                case 'multi-select': {

                        if( array_key_exists('options', $cur_control) && is_array($cur_control['options']) && count($cur_control['options']) > 0 ) {

                            echo Admin_ElementsTemplates::multi_select($new_item, $bu_con_name, $bu_con_id, $cur_control['options'], $bu_con_db_value, $bu_con_default_value, $bu_con_text_title, $params_array);

                        } else {
                            
                            echo wp_kses_post( sprintf( __( '<div class="note warning">There\'s no array provided to display in select box, control [ <b>%s</b> ]</div>', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), $cur_control['id'] ) );

                        }

                    } break;

                case 'checkbox': {
                        
                        echo Admin_ElementsTemplates::checkbox( $new_item, $bu_con_name, $bu_con_id, $bu_con_db_value, $bu_con_default_value, $bu_con_text_title, $params_array);

                    } break;

                case 'multi-checkbox': {

                        if( array_key_exists('options', $cur_control) && is_array($cur_control['options']) && count($cur_control['options']) > 0 ) {

                            echo Admin_ElementsTemplates::multi_checkbox($new_item, $bu_con_name, $bu_con_id, $cur_control['options'], $bu_con_db_value, $bu_con_default_value, $bu_con_text_title, $params_array);

                        } else {
                            
                            echo wp_kses_post( sprintf( __( '<div class="note warning">There\'s no array provided to display in select box, control [ <b>%s</b> ]</div>', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), $cur_control['id'] ) );

                        }

                    } break;
                                    
                case 'radio': {
                        
                        if( array_key_exists('options', $cur_control) && is_array($cur_control['options']) && count($cur_control['options']) > 0 ) {

                            echo Admin_ElementsTemplates::radio($new_item, $bu_con_name, $bu_con_id, $cur_control['options'], $bu_con_db_value, $bu_con_default_value, $bu_con_text_title, $params_array);

                        } else {
                            
                            echo wp_kses_post( sprintf( __( '<div class="note warning">There\'s no array provided to display in select box, control [ <b>%s</b> ]</div>', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ), $cur_control['id'] ) );

                        }

                    } break;


                } //switch

            } // Check name and type

        }

    }

}