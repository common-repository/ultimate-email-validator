<?php
namespace UltimateEmailValidator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Filters text content and strips out disallowed HTML.
 *
 * The class functions expects unslashed data.
 *
 * @see wp_kses_post()          for specifically filtering post content and fields.
 * @see wp_allowed_protocols()  for the default allowed protocols in link URLs.
 *
 * @version 1.0
 *
 * @author Oxibug
 *
 */
class KsesFactory {

    /**
     * Static instance of the main plugin class
     *
     * @var KsesFactory
     *
     * @since   1.0.0
     * @access  public
     *
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
     * 
     * 
     * @return KsesFactory
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

    /**
     * Return Most Used Attributes in all SVG Elements
     * - The Main Attributes
     * - The Presentation Attributes
     * 
     * @see     https://developer.mozilla.org/en-US/docs/Web/SVG/Element/svg 
     * 
     * @return  array
     * 
     * @since   1.0.0
     * @access  private
     */
    private function SVG_Elements_MostUsedAttributes() {
        
        return array(
            /* Main Attributes */
            'data-*'        => true,
            'attr-*'        => true,
            'id'            => true,
            'tabindex'      => true,
            'class'         => true,
            
            /* Presentation Attributes */
            'clip-path'             => true,
            'clip-rule'             => true,
            'color'                 => true,
            'color-interpolation'   => true,
            'color-rendering'       => true,
            'cursor'                => true,
            'display'               => true,
            'fill'                  => true,
            'fill-opacity'          => true,
            'fill-rule'             => true,
            'filter'                => true,
            'mask'                  => true,
            'opacity'               => true,
            'pointer-events'        => true,
            'shape-rendering'       => true,
            'stroke'                => true,
            'stroke-dasharray'      => true,
            'stroke-dashoffset'     => true,
            'stroke-linecap'        => true,
            'stroke-linejoin'       => true,
            'stroke-miterlimit'     => true,
            'stroke-opacity'        => true,
            'stroke-width'          => true,
            'transform'             => true,
            'vector-effect'         => true,
            'visibility'            => true,
            
        );

    }

    /**
     * Return all supported SVG Elements
     * 
     * @see     https://developer.mozilla.org/en-US/docs/Web/SVG/Element/svg
     * 
     * @return  array
     * 
     * @since   1.0.0
     * @access  private
     */
    private function SVG_Elements() {
        
        return array(
          
            'a',
            'altglyph',
            'altglyphdef',
            'altglyphitem',
            'animate',
            'animatecolor',
            'animatemotion',
            'animatetransform',

            'circle',
            'clippath',
            'color-profile',
            'cursor',

            'defs',
            'desc',

            'ellipse',

            'feBlend',
            'feColorMatrix',
            'feComponentTransfer',
            'feComposite',
            'feConvolveMatrix',
            'feDiffuseLighting',
            'feDisplacementMap',
            'feDistantLight',
            'feFlood',
            'feFuncA',
            'feFuncB',
            'feFuncG',
            'feFuncR',
            'feGaussianBlur',
            'feImage',
            'feMerge',
            'feMergeNode',
            'feMorphology',
            'feOffset',
            'fePointLight',
            'feSpecularLighting',
            'feSpotLight',
            'feTile',
            'feTurbulence',
            'filter',
            'font',
            'font-face',
            'font-face-format',
            'font-face-name',
            'font-face-src',
            'font-face-uri',
            'foreignObject',

            'g',
            'glyph',
            'glyphref',

            'hkern',

            'image',

            'line',
            'lineargradient',

            'marker',
            'mask',
            'metadata',
            'missing-glyph',
            'mpath',

            'path',
            'pattern',
            'polygon',
            'polyline',

            'radialgradient',
            'rect',

            'script',
            'set',
            'stop',
            'style',
            'svg',
            'switch',
            'symbol',

            'text',
            'textPath',
            'title',
            'tref',
            'tspan',

            'use',

            'view',
            'vkern'
        );

    }

    /**
     * Return associative array of allowed tags and attributes after merge with WordPress {$allowedposttags} globals
     * -
     * - The new tags added on {$allowedposttags} global variable
     * 
     * - textarea   ( Modify )
     * - img        ( Modify )
     * 
     * - form
     * - fieldset
     * - select
     * - optgroup
     * - option
     * - input
     * - source
     * - picture
     * - iframe
     * 
     * @param   string          $data   The content
     * @param   string|array    $args   A Key => Value arraywith tag and a boolean value to indicate allow it or not
     * 
     * @return  array           Associative array of allowed tags and attributes
     * 
     * @since   1.0.0
     * @access  public
     */
    public function kses_more_allowed_tags( $args = '' ) {
        
        global $allowedposttags;

        $args_default = array(
            'a'             => false,
            'textarea'      => false,
            'img'           => false,

            'form'          => false,
            'fieldset'      => false,

            'select'        => false,
            'optgroup'      => false,
            'option'        => false,

            'input'         => false,
            'source'        => false,

            'picture'       => false,

            'iframe'        => false,

            'svg'           => false,
            'circle'        => false,
            'path'          => false,

        );

        if( empty( $args ) || 
            ( is_string( $args ) && ( 'all' === (string) $args ) ) ) {
            
            $args = array();

            foreach( $args_default as $tag => $tag_status ) {
                $args[ $tag ] = true;
            }

        }
        else {
            $args = wp_parse_args( $args, $args_default );
        }

        /**
         * All SVG elements to chekc and add the default 
         * Presentation Attributes if found in our alloowed tags
         * 
         * @var     array
         * */
        $svg_elements = self::$_instance->SVG_Elements();
        $svg_elements_mostused_attrs = self::$_instance->SVG_Elements_MostUsedAttributes();

        $new_allowed_html_tags = array(

            'a'         => array(
                'href'          => true,
                'download'      => true,
            ),

            'form'      => array(
                'data-*'        => true,
                'attr-*'        => true,
                'novalidate'    => true
            ),

            'fieldset'      => array(
                'data-*'        => true,
                'attr-*'    => true,
                'id'    => true,
                'class' => true,
                'form'  => true,
                'name'  => true,
            ),

            'select'   => array(
                'data-*'    => true,
                'attr-*'    => true,
                'autofocus' => true,
                'class'     => true,
                'id'        => true,
                'disabled'  => true,
                'form'      => true,
                'multiple'  => true,
                'name'      => true,
                'required'  => true,
                'size'      => true,
            ),
            'optgroup' => array(
                'data-*'       => true,
                'attr-*'    => true,
                'disabled' => true,
                'label'    => true,
            ),
            'option'   => array(
                'data-*'    => true,
                'attr-*'    => true,
                'disabled' => true,
                'label'    => true,
                'selected' => true,
                'value'    => true,
            ),
            
            'input'    => array(
                'data-*'       => true,
                'attr-*'       => true,
                'accept'       => true,
                'autocomplete' => true,
                'autofocus'    => true,
                'checked'      => true,
                'class'        => true,
                'disabled'     => true,
                'id'           => true,
                'height'       => true,
                'min'          => true,
                'max'          => true,
                'minlenght'    => true,
                'maxlength'    => true,
                'name'         => true,
                'pattern'      => true,
                'placeholder'  => true,
                'readony'      => true,
                'required'     => true,
                'size'         => true,
                'src'          => true,
                'step'         => true,
                'type'         => true,
                'value'        => true,
                'width'        => true,
            ),
            'textarea' => array(
                'data-*'       => true,
                'placeholder' => true,
                'cols'        => true,
                'rows'        => true,
                'disabled'    => true,
                'name'        => true,
                'id'          => true,
                'readonly'    => true,
                'required'    => true,
                'autofocus'   => true,
                'form'        => true,
                'wrap'        => true,
            ),
            
            'picture'  => true,

            'img' => array(
                'attr-*'   => true,
                'src'      => true,
                'srcset'   => true,
                'sizes'    => true,
                'class'    => true,
                'id'       => true,
                'width'    => true,
                'height'   => true,
                'alt'      => true,
                'longdesc' => true,
                'usemap'   => true,
                'align'    => true,
                'border'   => true,
                'hspace'   => true,
                'vspace'   => true,
            ),

            'source'   => array(
                'data-*' => true,
                'attr-*'        => true,
                'sizes'  => true,
                'src'    => true,
                'srcset' => true,
                'type'   => true,
                'media'  => true,
            ),

            'iframe'    => array(
                'src'             => true,
		        'height'          => true,
		        'width'           => true,
		        'frameborder'     => true,
		        'allowfullscreen' => true,
            ),

            /* Tricky: DO NOT use camelCase: {viewBox} MUST BE {viewbox} */
            'svg'       => array(
                'xmlns'                 => true,
		        'viewbox'               => true,                
                'baseprofile'           => true, /* Deprecated Since v2 */
                'contentscripttype'     => true, /* Deprecated Since v2 */
                'contentstyletype'      => true, /* Deprecated Since v2 */
		        'version'               => true, /* Deprecated Since v2 */
		        'preserveaspectratio'   => true,
		        'height'                => true,
		        'width'                 => true,
		        'x'                     => true,
		        'y'                     => true,
		        'zoomandpan'            => true,
            ),

            'circle'    => array(
                'fill'          => true,
		        'cx'            => true,
		        'cy'            => true,
		        'r'             => true,
            ),
            'path'       => array(
		        'fill'          => true,
		        'd'             => true,
            ),
            'g'       => array(
		        'transform'          => true,
            ),
            'polygon'       => array(
		        'points'          => true,
            ),
        );

        /* 
         * FIX: Modify tags inside {$allowedposttags} 
         * to add new attributes and leave the default ones 
         * */
        foreach( $new_allowed_html_tags as $tag_id => $tag_prop ) {

            if( ! array_key_exists( $tag_id, $allowedposttags ) ) {
                continue;
            }

            $allowedposttags[ $tag_id ] = array_merge( $new_allowed_html_tags[ $tag_id ], $allowedposttags[ $tag_id ] );
        }


        $user_needed_tags = array();

        foreach( $args as $user_tag_key => $tag_status ) {

            if( ! $tag_status || 
                ! array_key_exists( $user_tag_key, $new_allowed_html_tags ) ) {
                continue;
            }

            /* SVG Elements: Check if one of SVG Elements and add the Most Used Attributes in all SVG Elements */
            if( in_array( $user_tag_key, $svg_elements ) ) {
                
                $svg_ele_tags = array_merge( $svg_elements_mostused_attrs, $new_allowed_html_tags[ $user_tag_key ] );

                $user_needed_tags[ $user_tag_key ] = $svg_ele_tags;

            }
            else {
                
                $user_needed_tags[ $user_tag_key ] = $new_allowed_html_tags[ $user_tag_key ];

            }

        }
        
        return array_merge( $allowedposttags, $user_needed_tags );
        
    }


    /**
     * Using {wp_kses} WordPress function to print out allowed tags
     *
     * @see     Widoz Solution: https://gist.github.com/widoz/2b0e7501fb4b86103e3e529339652952
     * 
     * @param   string          $data   The content
     * @param   string|array    $args   A Key => Value array with tag and a boolean value to indicate allow it or not @see {self :: kses_more_allowed_tags}
     * 
     * @return  string          Associative array of allowed tags and attributes
     * 
     * @since   1.0.0
     * @access  public
     */
    public function kses_more( $data = '', $args = 'all' ) {

        $tags_allowed = self::$_instance->kses_more_allowed_tags( $args );
        
        return wp_kses( $data, $tags_allowed );

    }


}