<?php

namespace KMS\DarkSky;

trait ErrorLogger {

	/**
	 * Handle displaying of errors
	 *
	 * @param string $error the error message to display
	 *
	 */
	public function error( $error ){
	    die ( "Have mercy: $error\n" );
	}

}
