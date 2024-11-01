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
class DateTimeFactory {

    /**
     * Static instance of the main plugin class
     *
     * @var DateTimeFactory
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
     * RFC3339 DateTime Global Format
     * @see https://www.w3.org/TR/NOTE-datetime
     * 
     */
    const RFC3339 = 'Y-m-d\TH:i:s\Z';


    /**
     * 
     * 
     * @return DateTimeFactory
     * 
     */
    public static function instance() {

        if( is_null( self::$_instance ) ) {

            self::$_instance = new self;

        }

        return self::$_instance;

    }


    private function __construct() {}
    

    /**
     * Return Local WordPress DateTime Format.
     *
     * @return string The WordPress date and time format
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public function local_datetime_format( $separator = null ) {

        if( is_string( $separator ) && !empty( $separator ) ) {
            return sprintf( '%1$s%2$s%3$s', get_option('date_format'), $separator, get_option('time_format') );
        }


        return get_option('date_format') . get_option('time_format');
    }


    /**
     * Return Local WordPress Timezone.
     *
     * @return  string PHP timezone string for the site
     *
     * @since   1.0.0
     * @access  public
     *
     */
    public function local_timezone() {

        // If site timezone string exists, return it.
        $timezone = get_option( 'timezone_string' );
        if ( $timezone ) {
            return $timezone;
        }

        // Get UTC offset, if it isn't set then return UTC.
        $utc_offset = intval( get_option( 'gmt_offset', 0 ) );
        if ( 0 === $utc_offset ) {
            return 'UTC';
        }

        // Adjust UTC offset from hours to seconds.
        $utc_offset *= 3600;

        // Attempt to guess the timezone string from the UTC offset.
        $timezone = timezone_name_from_abbr( '', $utc_offset );
        if ( $timezone ) {
            return $timezone;
        }

        // Last try, guess timezone string manually.
        foreach ( timezone_abbreviations_list() as $abbr ) {
            foreach ( $abbr as $city ) {
                if ( (bool) date( 'I' ) === (bool) $city['dst'] && $city['timezone_id'] && intval( $city['offset'] ) === $utc_offset ) {
                    return $city['timezone_id'];
                }
            }
        }

        // Fallback to UTC.
        return 'UTC';

    }


    /**
	 * Convert a UNIX timestamp or MySQL datetime into a PHP DateTime class object
     * 
     * @uses    local_timezone
     * @uses    local_datetime_format
     * 
	 * @param   int|string  $timestamp          UNIX timestamp or MySQL datetime
	 * @param   bool        $convert_to_utc 
	 * @param   bool        $convert_to_gmt     Use GMT timezone.
	 * @param   string      $custom_dt_format   Provide a custom date-time format instead of the default
     * 
     * @return  \DateTime   datetime in WordPress local format
     * 
     * @since   1.0.0
     * @access  public
     *
	 */
	public function convert_timestamp_to_datetime( $timestamp, $convert_to_utc = false, $convert_to_gmt = false, $custom_dt_format = null ) {

        $wp_locformat = ( is_null( $custom_dt_format ) ) ? self::$_instance->local_datetime_format( ' ' ) : $custom_dt_format;

		if ( $convert_to_gmt ) {
			if ( is_numeric( $timestamp ) ) {
                /* Date Time format - Ex: {Y-m-d H:i:s} */
				$timestamp = date( $wp_locformat, $timestamp );
			}

			$timestamp = get_gmt_from_date( $timestamp );
		}

		if ( $convert_to_utc ) {
			$timezone = new \DateTimeZone( self::$_instance->local_timezone() );
		} else {
			$timezone = new \DateTimeZone( 'UTC' );
		}

		try {

			if ( is_numeric( $timestamp ) ) {
				$date = new \DateTime( "@{$timestamp}" );
			} else {
				$date = new \DateTime( $timestamp, $timezone );
			}

			// convert to UTC by adjusting the time based on the offset of the site's timezone
			if ( $convert_to_utc ) {
				$date->modify( -1 * $date->getOffset() . ' seconds' );
			}

		} catch ( \Exception $e ) {
			$date = new \DateTime( '@0' );
		}
        
        return $date;

	}


    /**
	 * Format a UNIX timestamp or MySQL datetime into 
     * - RFC3339 or 
     * - Readable WordPress local format datetime 
     * --
     * Note: To return RFC3339 datetime format you MUST pass {$RFC3339} as true
     * Note: Use {$RFC3339} to compare two dates
	 * See Comply with RFC3339 format https://validator.w3.org/feed/docs/error/InvalidRFC3339Date.html
     * 
	 * @param   int|string  $timestamp          UNIX timestamp or MySQL datetime
     * @param   bool        $RFC3339            Comply with RFC3339 format?
     * TRUE     : Return {Y-m-d\TH:i:s\Z}
     * FALSE    : Return local WordPress readable format separated by a space {Date Time}
	 * @param   bool        $convert_to_utc 
	 * @param   bool        $convert_to_gmt     Use GMT timezone.
	 * @param   string      $custom_dt_format   Provide a custom date-time format instead of the default
     * 
     * @return  string      datetime in WordPress local format
     * 
     * @since   1.0.0
     * @access  public
     *
	 */
	public function convert_timestamp( $timestamp, $RFC3339 = false, $convert_to_utc = false, $convert_to_gmt = false, $custom_dt_format = null ) {

        $wp_locformat = ( is_null( $custom_dt_format ) ) ? self::$_instance->local_datetime_format( ' ' ) : $custom_dt_format;

        $date = self::$_instance->convert_timestamp_to_datetime( $timestamp, $convert_to_utc, $convert_to_gmt );
        
        if( $RFC3339 ) {
            /* 
             * Modify to RFC3339 Format Ex: {Y-m-d\TH:i:s\Z} 
             * NOT Sure About: self::$_instance->local_datetime_format( '\T' ) . '\Z';
             * 
             * @see PHP Constant - \DateTime::RFC3339
             * 
             * */
            return $date->format( self::RFC3339 );
        }

        /* Date Time format  - Ex: {Y-m-d H:i:s} */
        return $date->format( $wp_locformat );

	}

    /**
     * Check whether the date is in RFC3339 format
     * 
     * @param   string  $date 
     * @return  boolean
     * 
     * @since   1.0.0
     * @access  public
     */
    public function is_valid_RFC3339( $date ) {
        
        if( ( ! $date ) || ( FALSE === \DateTime::createFromFormat( self::RFC3339, $date ) ) ) {
            return false;
        }

        return true;
    }
}