<?php

ini_set( 'default_charset', 'UTF-8' );
require_once "config.php";
require_once "common.php";

?>

<!doctype html>

<html lang="en">
<head>
	<meta charset="utf-8">
	
	<title>YouTube Data Tools</title>
	
	<link rel="stylesheet" type="text/css" href="main.css" />
	<link href="https://fonts.googleapis.com/css?family=Droid+Sans|Muli:700" rel="stylesheet">
	
	<?php if(RECAPTCHA) { ?> 
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	<?php } ?>
</head>

<body>

<div id="fullpage">

	<div class="headTab">
		<div class="leftHead" onclick="document.location.href='<?php echo BASEURL; ?>';" style="cursor:pointer;">YouTube Data Tools</div>
		<div class="rightHead">
			<a href="http://thepoliticsofsystems.net">blog</a>
			<a href="http://labs.polsys.net">software</a>
			<a href="http://thepoliticsofsystems.net/papers-and-talks/">research</a>
			<a href="https://www.digitalmethods.net">DMI</a>
			<a href="http://thepoliticsofsystems.net/about/">about</a>
		</div>
	</div>
	
	
	<div class="rowTab">
		<div class="sectionTab">
			<a href="index.php" class="navlink">Home</a>
			<a href="mod_channel_info.php" class="navlink">Channel Info</a>
			<a href="mod_channels_net.php" class="navlink">Channel Network</a>
			<a href="mod_videos_list.php" class="navlink">Video List</a>
			<a href="mod_videos_net.php" class="navlink">Video Network</a>
			<a href="mod_video_info.php" class="navlink">Video Info</a>
			<a href="faq.php" class="navlink">FAQ</a>
		</div>
	</div>
	