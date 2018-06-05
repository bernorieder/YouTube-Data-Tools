<?php include("html_head.php"); ?>

	<div class="rowTab">
		<div class="sectionTab">
			<h1>Channel Info Module</h1>
		</div>
	</div>

	<div class="rowTab">
		<div class="fullTab">
			<p>This module retrieves different kinds of information for a channel from the <a href="https://developers.google.com/youtube/v3/docs/channels/list" target="_blank">channels/list</a> API endpoint
			from a specified channel id. The following resources are requested: brandingSettings, status, id, snippet, contentDetails, contentOwnerDetails, statistics, topicDetails, invideoPromotion.</p>
			<p>Output is a direct print of the API response.</p>
			<p>You can use comma-separated hashes to retrieve information for more than one channel as a list (tab file).</p>
		</div>
	</div>

	<div class="rowTab">
		<div class="sectionTab"><h1>Parameters</h1></div>
	</div>

	<form action="mod_channel_info.php" method="get">

	<div class="rowTab">
		<div class="leftTab">Channel id:</div>
		<div class="rightTab">
			<input type="text" name="hash" value="<?php if(isset($_GET["hash"])) { echo $_GET["hash"]; } ?>" /> (channel ids can be found in URLs, e.g. https://www.youtube.com/channel/<b>UCtxGqPJPPi8ptAzB029jpYA</b>)
		</div>
	</div>
	
	<div class="rowTab">
		<div class="leftTab"></div>
		<div class="rightTab">
			<input type="submit" />
		</div>
	</div>
	
	</form>

<?php

if(isset($_GET["hash"])) {

	echo '<div class="rowTab">
			<div class="sectionTab"><h1>Results</h1></div>
		 </div>
		 <div class="rowTab">';
	 
	if($_GET["hash"] == "") {
		echo "Missing channel id.";
		exit;
	}

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
	echo '</table></div>';
}

?>

<?php include("html_foot.php"); ?>