<?php
namespace KMS\DarkSky;

/**
 * ForecastRepository is a class writing and reading forecast data from the database
 *
 * @package    Weather
 * @category   Database
 * @author     Author <kaozotto@gmail.com>
 * @version    1.0
 */
class ForecastRepository{

    private $pdo, $dbname;

    /**
     * constructor, create an instance of the class
     *
     * @return Object
     * @access public
     */
    public function __construct() {

        require 'db/config.php';

        $this->dbname = $dbname;

        try {
            $this->pdo = new \PDO("mysql:host=$host", $username, $password, $options);
        }
        catch(PDOException $error) {
            echo $error->getMessage() . "\n";
        }
    }

    /**
     * Save information to the database
     *
     * @param float $latitude the location latitude
     * @param float $longitude the location longitude
     * @param array $forecast a collection of weather data
     *
     * @access public
     */
    public function writeToDatabase( $latitude, $longitude, $forecast ){

        // check for existing location
        try {
            $query = $this->pdo->prepare("
                SELECT
                    id,
                    latitude,
                    longitude
                FROM
                    $this->dbname.locations
                WHERE
                    latitude = ? AND longitude = ?
            ");

            $query->execute([ $latitude, $longitude ]);
            $result = $query->fetch(\PDO::FETCH_ASSOC);
        }
        catch(PDOException $error) {
            echo $error->getMessage() . "\n";
        }

        // get id or insert if not exists
        if( !empty($result) ){
            $location_id = $result['id'];
        }
        else{
            try {
                $query = $this->pdo->prepare("
                    INSERT INTO
                        $this->dbname.locations (latitude, longitude, last_forecast_update)
                    VALUES
                        (?, ?, ?)
                ");
                $query->execute( [$latitude, $longitude, $forecast['time']] );
                $location_id = $this->pdo->lastInsertId();
            }
            catch(PDOException $error) {
                echo $error->getMessage() . "\n";
            }
        }

        // save latest forecast
        try {
            $query = $this->pdo->prepare("
                INSERT INTO
                    $this->dbname.forecasts (location_id, time, temperature, precipitation_intensity, precipitation_probability)
                VALUES (?, ?, ?, ?, ?)
            ");
            $query->execute([
                $location_id,
                $forecast['time'],
                $forecast['temperature'],
                $forecast['precipIntensity'],
                $forecast['precipProbability']
            ]);
        }
        catch(PDOException $error) {
            echo $error->getMessage() . "\n";
        }
        $query = null;
    }


    /**
     * Find all forecasts for a locationId after a specific epoch
     *
     * @param int $locationId the location id
     * @param int $epoch the epoch
     *
     * @access public
     */
    public function findForecastsByLocationIdAfter(int $locationId, int $epoch){

        // check for existing location
        try {
            $query = $this->pdo->prepare("
                SELECT
                    f.time,
                    f.temperature,
                    f.precipitation_intensity,
                    f.precipitation_probability,
                    l.latitude,
                    l.longitude,
                    l.id
                FROM
                    $this->dbname.forecasts f
                JOIN
                    $this->dbname.locations l on l.id = f.location_id
                WHERE
                    f.location_id = ? AND f.time > ?
            ");

            $query->execute([ $locationId, $epoch ]);
            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        }
        catch(PDOException $error) {
            echo $error->getMessage() . "\n";
        }

        if( !empty($result) ){
            foreach($result as $row) {
                echo "Location ID: ". $row['id'] . "\n";
                echo "Latitude: ". $row['latitude'] . "\n";
                echo "Longitude: ". $row['longitude'] . "\n";
                echo "Time: ". $row['time'] . "\n";
                echo "Temperature: ". $row['temperature'] . "\n";
                echo "Precipitation Intensity: ". $row['precipitation_intensity'] . "\n";
                echo "Precipitation Probability: ". $row['precipitation_probability'] . "\n";
                echo "\n";
            }
        }
        else{
            echo "No forecasts found for location_id $locationId with eopoch > $epoch\n\n";
        }
    }
}
