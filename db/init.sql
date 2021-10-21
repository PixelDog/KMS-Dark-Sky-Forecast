CREATE DATABASE kms_darksky_db;

use kms_darksky_db;

CREATE TABLE locations (
	id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
	latitude DECIMAL(10,6) NOT NULL,
	longitude DECIMAL(10,6) NOT NULL,
	last_forecast_update INT(11) UNSIGNED NOT NULL
) engine=MyISAM;

CREATE TABLE forecasts (
	id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
	location_id INT(11) UNSIGNED NOT NULL, 
	time INT(11) UNSIGNED NOT NULL,
	temperature DECIMAL(5,2) NOT NULL,
	precipitation_intensity DECIMAL(10,6) UNSIGNED NOT NULL,
	precipitation_probability DECIMAL(10,6) NOT NULL
) engine=MyISAM;
