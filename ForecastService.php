<?php
namespace KMS\DarkSky;


/**
 * ForecastService is a class for fetching data from the DarkSky API at intervals and storing in the database
 *
 * @package    Weather
 * @category   Database
 * @author     Author <kaozotto@gmail.com>
 * @version    1.0
 */
class ForecastService{

    private $forecast_reader, $forecast_repository;
    public $interval;

    /**
     * constructor, create an instance of the class
     *
     * @param Object $forecast_reader the ForecastReader dependency
     * @param Object $forecast_repository the ForcaseRepository dependency
     *
     * @return Object
     * @access public
     */
    public function __construct( ForecastReader $forecast_reader, ForecastRepository $forecast_repository ) {

        $this->forecast_reader = $forecast_reader;
        $this->forecast_repository = $forecast_repository;

        // set default interval for collection of data
        $this->interval = 240; // 4 hours ( 240 minutes / 60 )
    }


    /**
     * Sets the time interval for the collection of data
     *
     * @param int $minutes the collection interval in minutes
     *
     * @access public
     */
    public function setInterval( int $minutes ){
        $this->interval = $minutes;
    }

    /**
     * Starts the collection of data at intervals
     *
     * PRODUCTION TODO: in a real scenario, this should be
     * replaced by a cron job that uses ForecastService
     * to collect data
     *
     * @access public
     */
    public function run(){
        while(true){

            $forecast = $this->forecast_reader->getForecast();

            $this->forecast_repository->writeToDatabase(
                $this->forecast_reader->longitude,
                $this->forecast_reader->latitude,
                $forecast['currently']
            );

            echo "Next query in $this->interval minute(s)\n\n";
            sleep( $this->interval * 60 );
        }
    }
}
