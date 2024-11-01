<?php
namespace UltimateEmailValidator;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * The main factory for admin page
 *
 * @author  Oxibug
 * @version 1.0.0
 */
class AdminPanel_BackendFactory {

    /**
     * Keep an instance of class
     *
     * @since 1.0
     *
     * @var AdminPanel_BackendFactory
     *
     */
    private static $_instance = null;


    /**
     * All Admin Pages globals
     * 
     * @var Admin_Components
     * 
     */
    private $ap_globals;
    
    
    /**
     * All globals
     * 
     * @var Components
     * 
     */
    private $loc_globals;

    /**
     * Add allowed HTML to use inside section title in case a user need an icon
     *
     * @since 1.0
     *
     * @var array
     *
     */
    private $tab_title_allowed_html = array(

        'i'     => array(
            'class' => array(),
            'style' => array(),
            'aria-hidden'   => array(),
        ),
        'span'  => array(
            'class' => array(),
            'style' => array(),
            'aria-hidden'   => array(),
        ),
        'img'  => array(
            'class' => array(),
            'src' => array(),
            'alt' => array(),
            'aria-hidden'   => array(),
        ),

    );


    /**
     * Instantiate in AdminPanel_Manager
     *
     * And after hooking admin menu through [ admin_menu ] and [ network_admin_menu ] actions
     *
     * NOTE: is_admin() and capabilities test applied in AdminPanel_Manager class
     *
     * @since 1.0
     *
     * @param string $page (REQUIRED)
     *
     * @return AdminPanel_BackendFactory
     *
     */
    public static function instance( $page ) {

        if( is_null( self::$_instance ) ) {

            self::$_instance = new self;

            self::$_instance->loc_globals = Components::instance();

            self::$_instance->ap_globals = Admin_Components::instance();

            self::$_instance->core_actions( $page );

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
     *
     * @since 1.0
     *
     */
    private function core_actions( $page ) {

        /**
         * Extensions
         * 
         * @param   string  $page   The page id from $_GET array- You MUST filter admin pages ids
         * to be able to use this action use filter {SLUG/filters/admin/pages/ids} to add your pages
         * 
         * */
        do_action( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN. '/actions/admin/before/plugin_panels', $page );


        if( $page === self::$_instance->ap_globals->apid_settings ) {
            add_action( 'admin_init', array( &$this, 'initialize_AdminPageSettings' ) );
        }
        
        /**
         * Extensions
         * 
         * @param   string  $page   The page id from $_GET array- You MUST filter admin pages ids
         * to be able to use this action use filter {SLUG/filters/admin/pages/ids} to add your pages
         * 
         * */
        do_action( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN. '/actions/admin/after/plugin_panels', $page );

    }


    /**
     * Never define functions inside callbacks.
     * these functions could be run multiple times; this would result in a fatal error.
     *
     * custom option and settings
     */
    public function initialize_AdminPageSettings() {

        /*
         * Add filter [ PLUGIN_MAIN_SLUG/filters/admin/page/settings/map ]
         *
         * */
        AdminPanel_PageMap::instance();


        /*
         * register a new section in the "srpset-options" page
         *
         * Use [ &$this ] in add_settings_section
         */
        add_settings_section(
            self::$_instance->ap_globals->apog_settings,

            esc_html__( 'Administrator Panel', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),

            array( &$this, 'pageSettings_header_backend_html' ),

            self::$_instance->ap_globals->apid_settings
        );


        // register a new setting for "srpset-options" page
        register_setting( self::$_instance->ap_globals->apog_settings, ULTIMATE_EMAIL_VALIDATOR_ADMIN_PAGE_CON_PREFIX );


        /*
         * register a new field in the "_section_general" section, inside the "srpset-options" page
         *
         * Use [ &$this ]
         *
         * */
        add_settings_field(

            ULTIMATE_EMAIL_VALIDATOR_ADMIN_PAGE_CON_PREFIX, // as of WP 4.6 this value is used only internally

            // use $args' label_for to populate the id inside the callback
            esc_html__( 'The Page Admin', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ),

            array( &$this, 'pageSettings_draw_backend_html' ),

            self::$_instance->ap_globals->apid_settings,

            self::$_instance->ap_globals->apog_settings,

            array(

                'page'          => self::$_instance->ap_globals->apid_settings,
                'con_prefix'    => ULTIMATE_EMAIL_VALIDATOR_ADMIN_PAGE_CON_PREFIX,
                'page_map'      => apply_filters( ( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/filters/admin/page/settings/map' ), array() )

            )

        );

    }


    /**
     * Section callbacks can accept an $args parameter, which is an array.
     * $args have the following keys defined: title, id, callback.
     * the values are defined at the add_settings_section() function.
     *
     * @since 1.0
     *
     *
     * */
    public function pageSettings_header_backend_html( $args ) { ?>

        <p id="<?php echo esc_attr( $args['id'] ); ?>" class="hidden"><?php esc_html_e( 'You can change your page as you wish!', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ); ?></p>
    
    <?php }

    
    /**
     * Get all available sections which only have controls
     *
     * @since 1.0
     *
     * @param array $array Represents the entire page array passed throug [ add_settings_field ] function
     *
     * @return array
     *
     */
    private function _GetAvailableSectionsAndTitles( $array ) {

        $output = array();

        if( ( is_array( $array['sections'] ) ) && ( count( $array['sections'] ) > 0 ) ) {

            foreach ( $array['sections']  as $key => $value) {

                if( ( is_array( $value ) ) && array_key_exists( 'title', $value )
                    && ( array_key_exists( 'controls', $value ) ) && ( is_array( $value['controls'] ) ) && ( count( $value['controls'] ) > 0 ) ) {

                    /**
                     * Constraints
                     *
                     * */
                    switch ( $key ) {

                        case 'pagebuilder':
                        case 'pagemeta':
                        case 'review':
                        case 'fonts_manager': {

                                if( self::$_instance->loc_globals->is_plugin_adjustable ) {

                                    $output[ $key ] = $value['title'];

                                }

                            } break;


                        default: {

                                $output[ $key ] = $value['title'];

                            } break;

                    }

                }

            }

        }

        return $output;

    }


    private function _check_element_valid( $element ) {
        if( ! $element ) {
            return false;
        }

        if( ( ! array_key_exists('id', $element) ) || 
            ( ! array_key_exists( 'type', $element ) ) || 
            ( ! ( $element['id'] ) ) ||
            ( ! ( $element['type'] ) ) ) {
            
            return false;

        }

        return true;
    }


    private function get_elements_map( $tabs_map ) {
        
        $final_elements = array();

        if( ! array_key_exists('sections', $tabs_map) ) {
            return null;
        }

        foreach( $tabs_map['sections'] as $tab_key => $tab_elements ) {

            if( ! array_key_exists( 'controls', $tab_elements ) || ! is_array( $tab_elements['controls'] ) ) {
                continue;
            }

            foreach( $tab_elements['controls'] as $element ) {

                if( ! self::$_instance->_check_element_valid( $element ) ) {
                    continue;
                }

                switch( $element['type'] ) {

                    case 'label': {
                        /* We need elements we could save in DB */
                        continue 2;
                    }

                    default: {

                            $final_elements[ $tab_key ][] = array(
                                'type'          => $element['type'],
                                'id'            => $element['id']
                            );
                        } break;

                    case 'repeater': {
                            
                            if( array_key_exists( 'controls', $element ) && 
                                is_array( $element['controls'] ) ) {
                                
                                $elements_in_rep = array();

                                foreach( $element['controls'] as $rep_element ) {

                                    if( ! self::$_instance->_check_element_valid( $rep_element ) ) {
                                        continue;
                                    }

                                    $elements_in_rep[] = array(
                                        'type'          => $rep_element['type'],
                                        'id'            => $rep_element['id']
                                    );
                                                                        
                                } /* Repeater Elements */

                                $final_elements[ $tab_key ][] = array(
                                    'type'          => $element['type'],
                                    'id'            => $element['id'],
                                    'elements'      => $elements_in_rep
                                );

                            } /* Check Repeater */

                        } break;

                }
                
            } /* Main Elements */

        }

        if( count( $final_elements ) > 0 ) {
            return (array)$final_elements;
        }

        return null;

    }

    /**
     * Start drawing HTML tabs and controls
     *
     * @since 1.0
     *
     * @param array $args
     *
     */
    public function pageSettings_draw_backend_html( $args ) {

        $new_items = true;

        if( ( ! is_array( $args['page_map'] ) ) || empty( $args['page_map'] ) ) {
            return;
        }

        if( ! isset( $args['con_prefix'] ) ) {
            return;
        }

        /**
         * Prepare defaults and unset [license] and [import_export] fields
         *
         * */
        $defaults = Admin_ElementsFactory::_Collect_Defaults( $args['page_map'] );

        if( array_key_exists( 'import_export', $defaults ) ) {
            unset( $defaults['import_export'] );
        }

        $elements_map = Admin_Components::instance()->get_elements_map( $args['page_map'], TRUE );


        /**
         * Store all available tabs that already have controls
         *
         * */
        $tabs = $this->_GetAvailableSectionsAndTitles( $args['page_map'] ) ;

        $tab_active = ( count( $tabs ) > 0 ) ? key( $tabs ) : ''; // Get first key otherwise Get cookie

        /**
         * The keywords that user cannot use in groups array
         *
         * */
        $child_groups_builtin = array( 'default_group' );

        
        /**
         * Stored DB value
         * @var array|null
         * */
        $db_values = AdminPanel_DBFactory::instance()->get_settings_by_page('plugin_settings');


        /**
         * The final export text in Base64Format
         * @var
         * */
        $db_export_text = '';
        
        /**
         * Check whether the elements has values in DB or it's a new form
         * @var
         * */
        $new_items = true;

        if( is_array( $db_values ) && ( count( $db_values ) > 0 ) ) {
                
            $new_items = false;

            /* In case we can unset any key from DB value before convert into Export text */
            $db_export_text = HelperFactory::instance()->maybe_base64_encode( $db_values );

        }


        Admin_Components::instance()->draw_required_hidden_fields( array(
            
            /* The page ID - Exactly like used in {admin_menu} function while creating it */
            'page'          => self::$_instance->ap_globals->apid_settings,    
            /* panel | cpt */
            'page_type'     => 'panel',
            /* Array */
            'map'           => $elements_map,
            /* Array */
            'defaults'      => $defaults,

        ) );
        

        $header_title = $header_description = $header_image = '';

        $form_header_class = array(
            'srpset-form-header'
        );
        
        if( array_key_exists( 'settings', $args['page_map'] ) && ( is_array( $args['page_map']['settings'] ) )
            && array_key_exists( 'header', $args['page_map']['settings'] ) && ( is_array( $args['page_map']['settings']['header'] ) ) ) {
            
            
            $header_array = $args['page_map']['settings']['header'];

            $banner_type = 'text';
            if( array_key_exists( 'type', $header_array ) ) {
                $banner_type = $header_array['type'];
            }

            switch( $banner_type ) {
                
                case 'text' : {

                    if( array_key_exists( 'title', $header_array ) ) {
                        $header_title = sanitize_text_field( $header_array['title'] );
                        $form_header_class[] = 'has-title';
                    }

                    if( array_key_exists( 'description', $header_array ) ) {
                        $header_description = esc_html( $header_array['description'] );
                        $form_header_class[] = 'has-description';
                    }

                } break;

                case 'image' : {

                    if( array_key_exists( 'image', $header_array ) ) {
                        $header_image = esc_html( $header_array['image'] );
                        $form_header_class[] = 'has-image';
                    }

                } break;

                case 'svg' : {

                    if( array_key_exists( 'image', $header_array ) ) {
                        $header_image = esc_html( $header_array['image'] );
                        $form_header_class[] = 'has-image';
                        $form_header_class[] = 'svg';
                    }

                } break;

            }
            
            
            if( ( ! empty( $header_title ) ) || ( ! empty( $header_description ) ) || ( ! empty( $header_image ) ) ) { ?>
            
            <div class="<?php echo sanitize_text_field( join( ' ', $form_header_class ) ); ?>">

                <div class="header-inner">

                    <?php if( !empty( $header_image ) ) { ?>

                        <div class="image-wrp">
                            <img src="<?php echo esc_url( $header_image ); ?>" alt="<?php echo sanitize_text_field( $header_title ); ?>" />
                        </div>

                    <?php } elseif ( ( ! empty( $header_title ) ) || ( ! empty( $header_description ) ) ) { ?>

                        <h1 class="header-title"><?php echo sanitize_text_field( $header_title ); ?></h1>

                        <h4 class="header-description"><?php echo esc_html( $header_description ); ?></h4>

                    <?php } ?>

                    <div class="donation-wrp">
                        <a href="https://ko-fi.com/hadyshaltout" target="_blank" class="button button-purple">
                            <span class="fa fa-heart"></span>
                            <span class="btn-content"><?php esc_html_e( 'Buy me a Coffee', ULTIMATE_EMAIL_VALIDATOR_TEXTDOMAIN ); ?></span>
                        </a>
                    </div>

                </div>

            </div>
            
        <?php } } ?>
        

        <div class="srpset-form-inner style-tabs vertical clearfix" data-active-tab="<?php echo sanitize_text_field( $tab_active ); ?>">

            <div class="d-flex tabs-wrapper clearfix">

                <div class="parent-tabs-header clearfix">

                    <ul class="tabs-header-list clearfix">

                        <?php foreach ( $tabs as $tab_key => $tab_title ) {

                            /* Leave inside loop */
                            $css_tab_trigger = array(
                                'd-flex',
                                'align-items-center',
                                'tab-trigger',
                                'tab-header-item'
                            );

                            if( (string) $tab_key === (string) $tab_active ) {
                                $css_tab_trigger[] = 'active';
                            } ?>
        	        
                            <li class="<?php echo sanitize_text_field( join( ' ', $css_tab_trigger ) ); ?>" 
                                data-tab="<?php echo sanitize_text_field( $tab_key ); ?>">
                                <?php echo KsesFactory::instance()->kses_more( $tab_title, array(
                                      'svg'         => true,
                                      'g'           => true,
                                      'circle'      => true,
                                      'path'        => true,
                                      'polygon'     => true,
                                ) ); ?>
                            </li>
                        
                        <?php } ?>
        
                    </ul>

                </div>

            
                <div class="parent-tabs-body clearfix">
        
        
                    <?php $tab_db_value = $tab_body_classes = array();
                    
                    foreach( (array)$tabs as $tab_key => $tab_settings ) {
            
                        $tab_body_classes[] = 'body-inner';
                        $tab_body_classes[] = $tab_key;
                        ( $tab_key == $tab_active ) && $tab_body_classes[] = 'active';

                        $tab_db_value = ( is_array( $db_values ) && ( count( $db_values ) > 0 ) && ( array_key_exists( $tab_key, $db_values ) ) ) ? $db_values[ $tab_key ] : array();
                        

                        /**
                         * 
                         * DO NOT WORRY
                         * 
                         * */
                        $tabs_controls = $args['page_map']['sections'][ $tab_key ]['controls'];
            

                        switch( $tab_key ) {
                          
                            case 'import_export': {
                                
                                /*
                                 * Bevause of we won't ever save export text in DB
                                 * So We should fill [$tab_db_value] with an appropriate fields
                                 * 
                                 */
                                $tab_db_value = array();
                                $tab_db_value['text_export'] = $db_export_text;
                                                                
                            } break;

                        } ?>

                        <div class="<?php echo join( ' ', $tab_body_classes ); ?>" data-tab="<?php echo sanitize_text_field( $tab_key ); ?>">

                        
                        <?php 
                        /*
                         * Child Groups
                         * 
                         * */
                        if( array_key_exists( 'groups', $args['page_map']['sections'][ $tab_key ] )
                            && is_array( $args['page_map']['sections'][ $tab_key ]['groups'] )
                            && ( count( $args['page_map']['sections'][ $tab_key ]['groups'] ) > 0 ) ) {
                            
                            $child_groups_available = $args['page_map']['sections'][ $tab_key ]['groups'];

                            /**
                             * The first group of the user list
                             * 
                             * NOTE: DO NOT use (key) function because of builtin value [ default_group ]
                             * 
                             * */
                            $group_active = '';

                            // Set $group_active
                            if( array_key_exists( 'default_group', $child_groups_available ) && ( ! empty( $child_groups_available['default_group'] ) ) ) {
                                
                                $group_active = $child_groups_available['default_group'];

                            }
                            else {

                                // Get the first key
                                foreach( (array)$child_groups_available as $group_key => $group_title ) {

                                    if( ! in_array( $group_key, $child_groups_builtin ) ) {
                                        
                                        $group_active = $group_key;

                                        break;
                                        
                                    }

                                }

                            }


                            $tabs_controls_sorted = Admin_ElementsFactory::_sortControlsByPriority( $tabs_controls ); ?>


                            <div class="child-groups-wrapper clearfix" data-active-group="<?php echo sanitize_text_field( $group_active ); ?>">

                                <div class="groups-header clearfix">

                                    <ul class="group-header-list">

                                        <?php foreach( (array)$child_groups_available as $group_key => $group_title ) {
                                        
                                            if( in_array( $group_key, $child_groups_builtin ) ) {
                                                continue;
                                            } ?>

                                            <li class="group-trigger group-header-item <?php echo ( $group_key == $group_active ) ? 'active' : ''; ?>" data-group="<?php echo sanitize_text_field( $group_key ); ?>">
                                                <?php echo sanitize_text_field( $group_title ); ?>
                                            </li>
                                
                                        <?php } ?>

                                    </ul>

                                </div>

                                <div class="groups-body-container clearfix">

                                    <?php foreach( (array)$child_groups_available as $group_key => $group_title ) {
                                    
                                        if( in_array( $group_key, $child_groups_builtin ) ) {
                                            continue;
                                        } ?>


                                        <div class="group-body clearfix <?php echo ( $group_key == $group_active ) ? 'active' : ''; ?>" data-group="<?php echo sanitize_text_field( $group_key ); ?>">
                                
                                            <?php 
                                            /**
                                             * The current group controls
                                             * 
                                             * */
                                            $current_group_controls = Admin_ElementsFactory::_collectControlsByGroupKey( $tabs_controls_sorted, $group_key );


                                            if( is_array( $current_group_controls ) && ( count( $current_group_controls ) > 0 ) ) {

                                                Admin_ElementsFactory::_Draw_Elements( $new_items, $tab_key, $tab_db_value, $current_group_controls );
                                                
                                            } ?>

                                        </div>


                                    <?php } ?>

                                </div>

                            </div>


                        <?php }

                        else {
                            
                            Admin_ElementsFactory::_Draw_Elements( $new_items, $tab_key, $tab_db_value, $tabs_controls );
                            
                        }
                        
                        unset( $tab_body_classes );
                        $tab_body_classes = array(); ?>

                        </div>

                    <?php } ?>


                </div><!-- tabs-body -->

            </div><!-- tabs-wrapper -->

        </div><!-- srpset-form-inner -->
        
    <?php }
   

}