<?php

require_once "config.php";

?>

<!doctype html>

<html lang="en">
<head>
	<meta charset="utf-8">
	
	<title>YouTube Tool Collection</title>
	
	<link rel="stylesheet" type="text/css" href="main.css" />
</head>

<body>

<table>
	<form action="mod_channel_info.php" method="get">
		<tr>
			<td colspan="3">
				<a href="index.php" class="navlink">Home</a>
				<a href="mod_channel_info.php" class="navlink">Channel Info</a>
				<a href="mod_channels_net.php" class="navlink">Channel Network</a>
				<a href="mod_videos_list.php" class="navlink">Video List</a>
				<a href="mod_video_info.php" class="navlink">Video Info</a>
			</td>
		</tr>
		<tr>
			<td colspan="3"></td>
		</tr>
		<tr>
			<td colspan="3">			
				<h1>YTDT Channel Info</h1>

				<p>This module retrieves a maximum of information for a channel from the <a href="https://developers.google.com/youtube/v3/docs/channels/list" target="_blank">channels/list</a> API endpoint
				from a specified channel id. The following resources are requested: brandingSettings, status, id, snippet, contentDetails, contentOwnerDetails, statistics, topicDetails, invideoPromotion.</p>
				<p>Output is a direct print of the API response.</p>
			</td>
		</tr>
		<tr>
			<td colspan="3"><hr /></td>
		</tr>
		<tr>
			<td>channel id:</td>
			<td><input type="text" name="hash" value="UCiDJtJKMICpb9B1qf7qjEOA" /></td>
			<td>(channel ids can be found in URLs, e.g. https://www.youtube.com/channel/UCiDJtJKMICpb9B1qf7qjEOA)</td>
		</tr>
		<tr>
			<td colspan="3"><input type="submit" /></td>
		</tr>
	</form>
</table>


<?php

if(isset($_GET["hash"])) {

	$hash = $_GET["hash"];
	
	getInfo();
}

function getInfo() {

	global $hash,$apikey;

	$restquery = "https://www.googleapis.com/youtube/v3/channels?part=brandingSettings,status,id,snippet,contentDetails,contentOwnerDetails,statistics,topicDetails,invideoPromotion&id=".$hash."&key=".$apikey;
	
	// example
	//$hash = "LLP2X3cVGXP0r56gOGhiRDlA";
	//$restquery = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=".$hash."&maxResults=50&key=".$apikey;

	
	$reply = json_decode(file_get_contents($restquery));

	echo '<pre>';
	print_r($reply->items[0]);
	echo '</pre>';
}

?>

</body>
</html>