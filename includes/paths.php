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
class Paths {

    /**
     * Static instance of the main plugin class
     *
     * @var Paths
     *
     * @since 1.0.0
     * @access private
     *
     */
    private static $_instance = null;

    /**
     * Summary of $paths
     *
     * @var array
     */
    private $paths;


    public static function instance() {

        if( is_null( self::$_instance ) ) {

            self::$_instance = new self();

            /* Normlize Paths for Windows */
            $dir = ( function_exists('wp_normalize_path') ) ? wp_normalize_path( dirname( ULTIMATE_EMAIL_VALIDATOR__FILE__ ) ) : dirname( ULTIMATE_EMAIL_VALIDATOR__FILE__ );


            /*
             * Set private variable [ $paths ]
             *
             * */
            self::$_instance->setPaths( Array(

                'APP_ROOT'              => $dir,
                'WP_ROOT'               => preg_replace( '/$\//', '', ABSPATH ),
                'APP_DIR'               => basename( $dir ),
                
                'FS_PREMIUM_DIR'        => $dir . '/fs-premium-features',
                'FS_PREMIUM_DIR_NAME'   => 'fs-premium-features',

                'ADMIN_DIR'             => $dir . '/admin',
                'ADMIN_PANEL_DIR'       => $dir . '/admin-panel',

                'INCLUDES_DIR'          => $dir . '/includes',
                'JOCKER_DIR'            => $dir . '/jocker',

                'DB_DIR'                => $dir . '/db',

                'FRONTEND_DIR'             => $dir . '/frontend',

                'ASSETS_DIR_NAME'       => 'assets',

            ) );


        }

        return self::$_instance;

    }


    private function __construct() {

    }


    /**
     *
     * @since 1.0
     *
     * @param mixed $paths
     *
     */
    protected function setPaths( $paths ) {

        $this->paths = $paths;

    }


    /**
     * Check the file path
     *
     * @since 1.0
     *
     * @param mixed $name
     *
     * @param mixed $file
     *
     * @return mixed
     *
     */
    public function path( $name, $file = '' ) {

        $path = $this->paths[ $name ] . ( ( strlen( $file ) > 0 ) ? ( '/' . preg_replace( '/^\//', '', $file ) ) : '' );

        return apply_filters( ULTIMATE_EMAIL_VALIDATOR_SLUG_MAIN . '/filters/metas_path', $path );

    }


    /**
     * Get absolute URL for any directory
     *
     * We need to convert slashes to UTF-8 - ONLY for assets folder
     *
     * @uses Function: path
     *
     * @since 1.0
     *
     * @param $file
     *
     * @return string
     *
     */
    public function abs_path( $dir_name, $file ) {

        return preg_replace( '/\s/', '%20', plugins_url( $this->path( $dir_name, $file ) ) );

    }


    /**
     * Get absolute URL for assets folder
     *
     * We need to convert slashes to UTF-8 - ONLY for assets folder
     *
     * @since 1.0
     *
     * @param $file
     *
     * @return string
     *
     */
    public function assets_path( $file = '' ) {

        return preg_replace( '/\s/', '%20', plugins_url( $this->path( 'ASSETS_DIR_NAME', $file ), ULTIMATE_EMAIL_VALIDATOR__FILE__ ) );

    }

    /**
     * Get absolute URL for assets folder
     *
     * We need to convert slashes to UTF-8 - ONLY for assets folder
     *
     * @since 1.0
     *
     * @param $file
     *
     * @return string
     *
     */
    public function fs_premium_abs_path( $file = '' ) {

        return preg_replace( '/\s/', '%20', plugins_url( $this->path( 'FS_PREMIUM_DIR_NAME', $file ), ULTIMATE_EMAIL_VALIDATOR__FILE__ ) );

    }

    /**
     *
     *
     * @since 1.0
     *
     * @param string $file
     *
     * @return string
     *
     */
    public function plugin_dir_url( $file ) {

        return plugins_url( $this->path( 'APP_ROOT', $file ), ULTIMATE_EMAIL_VALIDATOR__FILE__ );

    }

}

Paths::instance();