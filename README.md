KMS Dark Sky Forecast is a command line utility written in PHP to experiment
with the Dark Sky Weather API. You can get forecasts by latitude and longitude,
and even store them in a database. Dark Sky has joined Apple, but this forecaster
will be good through the end of 2022.

-------------------------------------------------------------------
REQUIREMENTS
-------------------------------------------------------------------
PHP ^7.0.31, MySQL ^5.7.23


-------------------------------------------------------------------
SETUP
-------------------------------------------------------------------
1) Set up database configuration. (optional. Needed for ForeCastService only
to store forecasts in a database. You can still use the action ForecastReader
to print a forecast to the terminal).

Edit:
db/config.php

Change the following vars to appropriate values for your database configuration for local host:
$host       = "localhost"; // local host
$username   = "root"; // database username for local host
$password   = "root99"; // user password
$dbname     = "kms_darksky_db"; // database name

NOTE: You do not need to change $dbname, the install script will create the database for you.
If you choose to change the name of the database, please also change it on line 3 of:
db/init.sql

2) Install the database and tables.
Run:
php db/install.php


-------------------------------------------------------------------
TESTING / USAGE: Forecaster.php command line script
-------------------------------------------------------------------

To print out usage from your terminal, run:
php Forecaster.php

USAGE:
Forecastreader.php is a command line script for querying and getting
forecasts by lat/long from the Dark Sky API.

Actions/parameters are as follows:

ACTIONS:
ForecastReader:
Get a forecast from the Dark Sky API by latitude and longitude
@param float latitude
@param float longitude

ForecastService:
Start a collection of forecasts from the Dark Sky API by latitude and longitude and store to the
database at [interval]. [interval] default: 240 minutes (4 hours)
@param float latitude
@param float longitude
@param int interval

findForecastsByLocationIdAfter:
Retrieve all forecasts from the database for location_id after epoch
@param int location_id
@param int epoch

PARAMS:
@param (string) action Required, accepts: ForecastReader, ForecastService, findForecastsByLocationIdAfter
@param (float) latitude the latitude of the forecast. Required for actions: ForecastReader, ForecastService
@param (float) longitude the longitude of the forecast. Required for actions: ForecastReader, ForecastService
@param (int) interval the interval of data collection in minutes. Default is 240 minutes (4 hours). Optional for action: ForecastService
@param (int) location_id the location_id in the database for the forecast. Required for action: findForecastsByLocationIdAfter
@param (int) epoch the epoch to query after to find forecasts. Required for action: findForecastsByLocationIdAfter


EXAMPLES:

// action ForecastReader
// get a forecast for latitude=42.3601 longitude=71.0589
php Forecaster.php action=ForecastReader latitude 42.3601 and longitude 71.0589

Example Output:
Timezone: Asia/Almaty
time: 1634840802
summary: Snow
icon: snow
precipIntensity: 0.0309
precipProbability: 0.5
precipType: snow
precipAccumulation: 0.383
temperature: 26.23
apparentTemperature: 20.81
dewPoint: 26.23
humidity: 1
pressure: 1022.7
windSpeed: 4.51
windGust: 7.95
windBearing: 266
cloudCover: 0.99
uvIndex: 0
visibility: 1.026
ozone: 309.5


// action ForecastService
// start a collection of forecasts for latitude 32.22 and longitude=60.05 at an interval of 5 minutes
php Forecaster.php action=ForecastService latitude=32.22 longitude=60.05 interval=5

// action findForecastsByLocationIdAfter
// get all forecasts for location_id 4 with epoch greater than 1537329750
php Forecaster.php action=findForecastsByLocationIdAfter location_id=4 epoch=1537329750
