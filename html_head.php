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
	
	<?php 

		$fn = basename($_SERVER['REQUEST_URI']);

	?>
	
	<div class="rowTab">
		<div class="sectionTab">
			<a href="index.php" class="navlink" <?php if($fn == "index.php" || $fn == "") { echo('style="background-color:#0B3C5D"'); } ?>>Home</a>
			<a href="mod_channel_info.php" class="navlink" <?php if($fn == "mod_channel_info.php") { echo('style="background-color:#0B3C5D"'); } ?>>Channel Info</a>
			<a href="mod_channels_list.php" class="navlink" <?php if($fn == "mod_channels_list.php") { echo('style="background-color:#0B3C5D"'); } ?>>Channel List</a>
			<a href="mod_channels_net.php" class="navlink" <?php if($fn == "mod_channels_net.php") { echo('style="background-color:#0B3C5D"'); } ?>>Channel Network</a>
			<a href="mod_videos_list.php" class="navlink" <?php if($fn == "mod_videos_list.php") { echo('style="background-color:#0B3C5D"'); } ?>>Video List</a>
			<!-- <a href="mod_videos_net.php" class="navlink" <?php if($fn == "mod_videos_net.php") { echo('style="background-color:#0B3C5D"'); } ?>>Video Network</a> -->
			<a href="mod_videos_comments_net.php" class="navlink" <?php if($fn == "mod_videos_comments_net.php") { echo('style="background-color:#0B3C5D"'); } ?>>Video Co-commenting Network</a>
			<a href="mod_video_comments.php" class="navlink" <?php if($fn == "mod_video_comments.php") { echo('style="background-color:#0B3C5D"'); } ?>>Video Comments</a>
			<a href="faq.php" class="navlink" <?php if($fn == "faq.php") { echo('style="background-color:#0B3C5D"'); } ?>>FAQ</a>
		</div>
	</div>