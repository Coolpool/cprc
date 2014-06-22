<?php

/*
 * Includes all the important CPRC files.
 * Include this file in all your scripts
*/

foreach(glob("include/*.php") as $file)
	include_once $file;

?>
