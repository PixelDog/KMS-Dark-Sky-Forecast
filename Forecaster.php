#!/usr/bin/php
<?php

include (dirname(__FILE__) . '/ForecastReader.php');
include (dirname(__FILE__) . '/ForecastService.php');
include (dirname(__FILE__) . '/ForecastRepository.php');

use KMS\DarkSky\ForecastReader;
use KMS\DarkSky\ForecastService;
use KMS\DarkSky\ForecastRepository;


/**
 * For ForecastService collection of data
 * allow script to run ndefinitely
 * to collect forecasts at intervals
 *
 */
ini_set('max_execution_time', 0);

$params = [];
$required = ['action'];
$argv = $_SERVER['argv'];

// assemble command line params
foreach ($argv as $arg){
	if( strpos($arg, '=') !== false ){
		list($param,$value) = explode('=', $arg);
		$param = trim( $param );
		$params["$param"] = trim( $value );
	}
}


/**
 * Output script usage
 *
 * @param Array $missing missing params
 *
 */
function usage( $missing=null ){
	$usage = "";
	if( $missing ){
		$usage .= "\nYou are missing one or more required parameters: " . implode ( ",", $missing) . "\n\n";
	}
	$usage .= "USAGE:\nForecastreader.php is a command line script for querying and collecting data from the Dark Sky API. Actions/parameters are as follows:\n
ACTIONS:
ForecastReader: Get a forecast from the Dark Sky API by latitude and longitude
ForecastService: Start a collection a forecasts from the Dark Sky API by latitude and longitude and store to the database at [interval]. [interval] default: 240 minutes (4 hours)
findForecastsByLocationIdAfter: Retrieve all forecasts from the database for location_id after epoch\n
PARAMS:
@param (string) action Required, accepts: ForecastReader, ForecastService, findForecastsByLocationIdAfter
@param (float) latitude the latitude of the forecast. Required for actions: ForecastReader, ForecastService
@param (float) longitude the longitude of the forecast. Required for actions: ForecastReader, ForecastService
@param (int) interval the interval of data collection in minutes. Default is 240 minutes (4 hours). Optional for action: ForecastService
@param (int) location_id the location_id in the database for the forecast. Required for action: findForecastsByLocationIdAfter
@param (int) epoch the epoch to query after to find forecasts. Required for action: findForecastsByLocationIdAfter\n
EXAMPLE:\nphp Forecaster.php action=ForecastReader latitude=42.3601 longitude=71.0589\n
";

	return $usage;
}


/**
 * Check for required script params
 *
 * @param Array $required the required params
 * @param Array $params the params passed to the script
 *
 */
function checkRequired( $required, $params ){

	$missing = array_diff( $required, array_keys($params) );

	if( !empty( $missing ) ){
		die ( usage( $missing ) );
	}
}

// check for required params
checkRequired( $required, $params );

// check action and perform
switch ( $params["action"] ) {

	case "ForecastReader":
		$required[] = "latitude";
		$required[] = "longitude";

		// check for required params for this action
		checkRequired( $required, $params );
		$forecast_reader = new ForecastReader();
		$forecast_reader->setLatLong( (float)$params["latitude"], (float)$params["longitude"] );
		$forecast = $forecast_reader->getForecast();

		// for demonstration purposes, render current weather
		echo("\nTimezone: " . $forecast['timezone'] . "\n\n");
		if( isset( $forecast['currently'] )){
			foreach($forecast['currently'] as $key => $value) {
			  echo "$key: $value\n";
			  echo "\n";
			}
		}
		break;

	case "ForecastService":
		$required[] = "latitude";
		$required[] = "longitude";

		// check for required params for this action
		checkRequired( $required, $params );

		$forecast_reader = new ForecastReader();
		$forecast_reader->setLatLong( (float)$params["latitude"], (float)$params["longitude"] );

		// database
		$forecast_repository = new ForecastRepository();

		// service
		$forecast_service = new ForecastService( $forecast_reader, $forecast_repository );

		// check for interval, default is 240 minutes
		if( isset( $params["interval"] ) ){
			$forecast_service->setInterval( (int)$params["interval"] );
		}

		// start collecton of data
		$forecast_service->run();

		break;

	case "findForecastsByLocationIdAfter":
		$required[] = "location_id";
		$required[] = "epoch";

		// check for required params for this action
		checkRequired( $required, $params );

		// database
		$forecast_repository = new ForecastRepository();

		$forecast_repository->findForecastsByLocationIdAfter( (int)$params['location_id'], (int)$params['epoch'] );
		break;

	default:
		echo( $params["action"] . " is not a valid action\n\n");
		die (usage());
		break;

}
