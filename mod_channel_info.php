<?php

require_once "config.php";
require_once "common.php";

?>

<!doctype html>

<html lang="en">
<head>
	<meta charset="utf-8">
	
	<title>YouTube Data Tools</title>
	
	<link rel="stylesheet" type="text/css" href="main.css" />
</head>

<body>

<table>
	<form action="mod_channel_info.php" method="get">
		<tr>
			<td colspan="5">
				<a href="index.php" class="navlink">Home</a>
				<a href="mod_channel_info.php" class="navlink">Channel Info</a>
				<a href="mod_channels_net.php" class="navlink">Channel Network</a>
				<a href="mod_videos_list.php" class="navlink">Video List</a>
				<a href="mod_videos_net.php" class="navlink">Video Network</a>
				<a href="mod_video_info.php" class="navlink">Video Info</a>
				<a href="faq.php" class="navlink">FAQ</a>
			</td>
		</tr>
		<tr>
			<td colspan="3"></td>
		</tr>
		<tr>
			<td colspan="3">			
				<h1>YTDT Channel Info</h1>

				<p>This module retrieves different kinds of information for a channel from the <a href="https://developers.google.com/youtube/v3/docs/channels/list" target="_blank">channels/list</a> API endpoint
				from a specified channel id. The following resources are requested: brandingSettings, status, id, snippet, contentDetails, contentOwnerDetails, statistics, topicDetails, invideoPromotion.</p>
				<p>Output is a direct print of the API response.</p>
				<p>You can use comma-separated hashes to retrieve information for more than one channel as a list (tab file).</p>
			</td>
		</tr>
		<tr>
			<td colspan="3"><hr /></td>
		</tr>
		<tr>
			<td>channel id:</td>
			<td><input type="text" name="hash" value="<?php if(isset($_GET["hash"])) { echo $_GET["hash"]; } ?>" /></td>
			<td>(channel ids can be found in URLs, e.g. https://www.youtube.com/channel/<b>UCtxGqPJPPi8ptAzB029jpYA</b>)</td>
		</tr>
		<tr>
			<td colspan="3"><hr /></td>
		</tr>
		<tr>
			<td colspan="3"><input type="submit" /></td>
		</tr>
	</form>
</table>


<?php

if(isset($_GET["hash"])) {

	$hash = $_GET["hash"];
	
	if(preg_match("/,/", $hash)) {
		getInfos($hash);
	} else {
		getInfo($hash);
	}
}


function getInfos($hash) {
	
	global $apikey,$datafolder;
	
	$hashes = explode(",",$hash);
	
	$channels = array();
	
	foreach($hashes as $hash) {
		
		
		
		$restquery = "https://www.googleapis.com/youtube/v3/channels?part=id,snippet,statistics&id=".$hash."&key=".$apikey;
		
		$reply = doAPIRequest($restquery);
		
		//print_r($reply);
		
		$channel = array();
		$channel["id"] = $reply->items[0]->id;
		$channel["title"] = $reply->items[0]->snippet->title;
		$channel["description"] = $reply->items[0]->snippet->description;
		$channel["publishedAt"] = $reply->items[0]->snippet->publishedAt;
		$channel["defaultLanguage"] = $reply->items[0]->snippet->defaultLanguage;
		$channel["country"] = $reply->items[0]->snippet->country;
		$channel["viewCount"] = $reply->items[0]->statistics->viewCount;
		$channel["subscriberCount"] = $reply->items[0]->statistics->subscriberCount;
		$channel["videoCount"] = $reply->items[0]->statistics->videoCount;
		
		$channels[] = $channel;
		
	}
	
	$filename = "channellist_channels" . count($hashes) . "_" . date("Y_m_d-H_i_s") . ".tab";
	$fp = fopen($datafolder.$filename, 'w');
	
	
	fputcsv($fp,array_keys($channel),"\t");
	
	foreach($channels as $channel) {
		fputcsv($fp,$channel,"\t");
	}
	
	
	
	echo '<br /><br />The script has retrieved information for '.count($hashes).' channels.<br /><br />

	your file:<br />
	<a href="'.$datafolder.$filename.'" download>'.$filename."</a><br />";

}


function getInfo($hash) {

	global $apikey;

	$restquery = "https://www.googleapis.com/youtube/v3/channels?part=brandingSettings,status,id,snippet,contentDetails,contentOwnerDetails,statistics,topicDetails,invideoPromotion&id=".$hash."&key=".$apikey;
	
	// example
	//$hash = "LLP2X3cVGXP0r56gOGhiRDlA";
	//$restquery = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=".$hash."&maxResults=50&key=".$apikey;

	
	$reply = doAPIRequest($restquery);

	/*
	echo '<pre>';
	print_r($reply->items[0]);
	echo '</pre>';
	*/
	
	echo "<hr /><br />";

	echo '<table class="resulttable">';
	foreach($reply->items[0] as $key => $var) {
		echo '<tr class="resulttable">';
		echo '<td class="resulttableHi"><b>'.$key.'</b></td>';
		if(gettype($var) != "object" && gettype($var2) != "array") { 
			echo '<td class="resulttable">'.$var.'</td>';
		} else {
			
			echo '<td class="resulttable">';
			echo '<table style="display:inline">';
			foreach($var as $key2 => $var2) {
				echo '<tr>';
				echo '<td><b>'.$key2.'</b></td>';
				if(gettype($var2) != "object" && gettype($var2) != "array") {
					echo '<td>'.$var2.'</td>';
				} else {
					
					echo '<td class="resulttable">';
					echo '<table style="display:inline">';
					foreach($var2 as $key3 => $var3) {
						echo '<tr>';
						echo '<td><b>'.$key3.'</b></td>';
						if(gettype($var3) != "object" && gettype($var3) != "array") {
							echo '<td>'.$var3.'</td>';
						} else {
							
							echo '<td class="resulttable">';
							echo '<table style="display:inline">';
							foreach($var3 as $key4 => $var4) {
								echo '<tr>';
								echo '<td><b>'.$key4.'</b></td>';
								if(gettype($var4) != "object" && gettype($var) != "array") {
									echo '<td>'.$var4.'</td>';
								} else {
									echo '<td>'.$var4.'</td>';	
								}
								echo '</tr>';
							}
							echo '</table>';
							echo '</td>';

						}
						echo '</tr>';
					}
					echo '</table>';
					echo '</td>';
					
				}
				echo '</tr>';
			}
			echo '</table>';
			echo '</td>';
			
		}
		echo '</tr>';
	}
	echo '</table>';
}

?>

</body>
</html>