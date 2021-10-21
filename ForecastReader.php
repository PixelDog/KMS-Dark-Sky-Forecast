<?php
namespace KMS\DarkSky;

include (dirname(__FILE__) . '/ErrorLogger.php');

/**
 * ForecastReader is a class for getting weather forecasts from the DarkSky API
 *
 * @package    Weather
 * @category   Forecasts
 * @author     Author <kaozotto@gmail.com>
 * @version    1.0
 */
class ForecastReader{

    use ErrorLogger;

    public $latitude, $longitude;

    /**
     * Setter for $lattitude and $longitude
     *
     * @param float $lattitude the latitude of the desired forecast
     * @param float $longitude the longitude of the desired forecast
     *
     * @access public
     */
    public function setLatLong( float $latitude, float $longitude ){

        // Check for valid lat and long
        if( $latitude > 90 || $latitude < -90 ){
            $this->error( "Latitude must be between rand of 90 and -90" );
        }

        if( $longitude > 180 || $longitude < -180 ){
            $this->error( "Longitude must be between rand of 180 and -180" );
        }

        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }


    /**
     * Retrieve the forecast from the DarkSky API
     *
     * @return array
     * @access public
     */
    public function getForecast(){

        $api_url = "https://api.darksky.net/forecast/d7e55ad7c5ffa237d5c2aeb3adcef34b/$this->latitude,$this->longitude";

        echo "Retrieving forecast for latitude: $this->latitude longitude: $this->longitude\n\n";

        // load file and decode
        $json = file_get_contents( $api_url );
        $decoded = json_decode( $json, TRUE );

        // check for valid json data
        if ( $decoded === null && json_last_error() !== JSON_ERROR_NONE ) {
            error( "Error in json file: " . json_last_error() );
        }

        // display
        array_walk_recursive($decoded, function($key, $value){
          if(!empty($item)){
            echo "$key: $item\n";
          }
        });

		// return our collection
        return $decoded;
    }
}
