<?php

// setup: rename this file to config.php, get an API key from https://code.google.com/apis/console and paste it below.

// base URL
$baseurl = 'https://tools.digitalmethods.net/netvizz/youtube/';

// Google API key
$apikey = "your_api_key";

// Google Repatcha
define('RECAPTCHA', false);
$secret = "your_recaptcha_secret";

// php runtime variables
ini_set("default_charset", "UTF-8");
ini_set("memory_limit", "4000M");
ini_set("max_execution_time", 3600*5);

// folders
$datafolder = "./data/";			// where you put the datafiles for analysis
$cronfolder = "./crondata/";		// output folder for cron automation

?>