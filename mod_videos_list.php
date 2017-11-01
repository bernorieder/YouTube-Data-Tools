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
	<form action="mod_videos_list.php" method="post">
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
			<td colspan="5"></td>
		</tr>
		<tr>
			<td colspan="5">
				<h1>YTDT Video List</h1>

				<p>This module creates a list of video infos and statistics from one of four sources: the videos uploaded to a specified channel, a playlist, the 
				videos retrieved by a particular search query, or the videos specified by a list of ids.</p>

				<p>The script then creates a tabular file where each row is a video. A number of infos and variables are added for each video.</p>
				
				<p>Check the documentation for the <a href="https://developers.google.com/youtube/v3/docs/videos/list" target="_blank">video/list</a> (used to
				get the info for each video) and the
				<a href="https://developers.google.com/youtube/v3/docs/search/list" target="_blank">search/list</a> (used for the search function) API endpoint for
				additional information.</p>
			</td>
		</tr>
		<tr>
			<td colspan="5"><hr /></td>
		</tr>
		<tr>
			<td><input type="radio" name="mode" value="channel" <?php if($_POST["mode"] != "seeds" && $_POST["mode"] != "search") { echo "checked"; } ?> /></td>
			<td>channel id:</td>
			<td><input type="text" name="channel" value="<?php if(isset($_POST["channel"])) { echo $_POST["channel"]; }; ?>" /></td>
			<td colspan="2">(channel ids can be found in URLs, e.g. https://www.youtube.com/channel/<b>UCiDJtJKMICpb9B1qf7qjEOA</b>)</td>
		</tr>
		<tr>
			<td colspan="5"><hr /></td>
		</tr>
		<tr>
			<td><input type="radio" name="mode" value="playlist" <?php if($_POST["mode"] == "playlist") { echo "checked"; } ?> /></td>
			<td>playlist id:</td>
			<td><input type="text" name="playlist" value="<?php if(isset($_POST["playlist"])) { echo $_POST["playlist"]; }; ?>" /></td>
			<td colspan="2">(playlist ids can be found in URLs, e.g. https://www.youtube.com/playlist?list=<b>PLJtitKU0CAehMmiSI9oCIv3WCJrZqMWZ0</b>)</td>
		</tr>
		
		<tr>
			<td colspan="5"><hr /></td>
		</tr>
		<tr>
			<td valign="top"><input type="radio" name="mode" value="search" <?php if($_POST["mode"] == "search") { echo "checked"; } ?> /></td>
			<td valign="top">search query:</td>
			<td valign="top"><input type="text" name="query" value="<?php if(isset($_POST["query"])) { echo $_POST["query"]; }; ?>" /></td>
			<td colspan="2">(this is passed to the search endpoint)
				<br />optional <a href="http://www.loc.gov/standards/iso639-2/php/code_list.php" target="_blank">ISO 639-1</a> relevance language: <input type="text" name="language" style="width:20px;" value="<?php if(isset($_POST["language"])) { echo $_POST["language"]; }; ?>" />
				<br />optional <a href="https://www.iso.org/obp/ui/#search" target="_blank">ISO 3166-1 alpha-2</a> region code: <input type="text" name="regioncode" style="width:20px;" value="<?php if(isset($_POST["regioncode"])) { echo $_POST["regioncode"]; }; ?>" /> (default = US)
			</td>
		</tr>
		<tr>
			<td></td>
			<td>iterations:</td>
			<td><input type="text" name="iterations" max="10" value="<?php echo (isset($_POST["iterations"])) ? $_POST["iterations"]:1; ?>" /></td>
			<td>(max. 10, one iteration gets 50 items)</td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td>published:</td>
				<td colspan="3">
					<input type="checkbox" name="timeframe" <?php if(isset($_POST["timeframe"])) { echo "checked"; } ?> /> limit search to videos published in a specific timeframe:<br />
				</td>
			</td>
			<td></td>
		</tr>
		
		<tr>
			<td></td>
			<td></td>
				<td colspan="3">
					after: <input type="text" name="date_after" value="<?php echo (isset($_POST["date_after"])) ? $_POST["date_after"]:"1970-01-01T00:00:00Z"; ?>" />&nbsp;&nbsp;&nbsp;
					before: <input type="text" name="date_before" value="<?php echo (isset($_POST["date_before"])) ? $_POST["date_before"]:"1970-01-01T00:00:00Z"; ?>" /> (format: yyyy-mm-ddThh:mm:ssZ)
				</td>
			</td>
			<td></td>
		</tr>
		

		<tr>
			<td></td>
			<td>rank by:</td>
			<td colspan="3">
				<select name="rankby">
					<option value="relevance" <?php if($_POST["rankby"] == "relevance") { echo "selected"; } ?>>relevance - Resources are sorted based on their relevance to the search query</option>
					<option value="date" <?php if($_POST["rankby"] == "date") { echo "selected"; } ?>>date – Resources are sorted in reverse chronological order based on the date they were created</option>
					<option value="rating" <?php if($_POST["rankby"] == "rating") { echo "selected"; } ?>>rating – Resources are sorted from highest to lowest rating</option>
					<option value="title" <?php if($_POST["rankby"] == "title") { echo "selected"; } ?>>title – Resources are sorted alphabetically by title</option>
					<option value="videoCount" <?php if($_POST["rankby"] == "videoCount") { echo "selected"; } ?>>videoCount – Channels are sorted in descending order of their number of uploaded videos</option>
					<option value="viewCount" <?php if($_POST["rankby"] == "viewCount") { echo "selected"; } ?>>viewCount - Resources are sorted from highest to lowest number of views</option>	
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="5"><hr /></td>
		</tr>
		<tr>
			<td><input type="radio" name="mode" value="seeds" <?php if($_POST["mode"] == "seeds") { echo "checked"; } ?> /></td>
			<td>manual selection:</td>
			<td colspan="2">
				<textarea name="seeds"><?php if($_POST["mode"] == "seeds") { echo $_POST["seeds"]; } ?></textarea>
			</td>
			<td>(video ids, comma separated)</td>
		</tr>
		<tr>
			<td colspan="5"><hr /></td>
		</tr>
		<tr>
			<td colspan="5"><input type="submit" /></td>
		</tr>
	</form>
</table>

<?php

$folder = $datafolder;

// allow for direct URL parameters and command line for cron
if(isset($argv)) {
	parse_str(implode('&', array_slice($argv, 1)), $_GET);
	$folder = $cronfolder;
}
if(isset($_GET["mode"])) { $_POST = $_GET; }

//print_r($_POST); exit;

if(isset($_POST["channel"]) || isset($_POST["seeds"]) || isset($_POST["query"])) {

	$mode = $_POST["mode"];

	if($mode == "channel") {
	
		$channel = $_POST["channel"];
		$iterations = $_POST["iterations"];
		
		$ids = getIdsFromChannel($channel);
		
		makeStatsFromIds($ids);
		
	} else if($mode == "playlist") {
	
		$playlist = $_POST["playlist"];
		$iterations = $_POST["iterations"];
		
		$ids = getIdsFromPlaylist($playlist);
		
		makeStatsFromIds($ids);
		
	} else if($mode == "search") {
		
		if($_POST["query"] == "") {
			echo "Missing query.";
			exit;
		}
		
		if($_POST["iterations"] > 50 || preg_match("/\D/", $_POST["iterations"])) {
			echo "Wrong iteration parameter.";
			exit;
		}
		
		$language = $_POST["language"];
		$regioncode = $_POST["regioncode"];
		$query = $_POST["query"];
		$iterations = $_POST["iterations"];
		$date_before = $date_after = false;
		if(isset($_POST["timeframe"])) {
			$date_before = $_POST["date_before"];
			$date_after = $_POST["date_after"];
		}
		$rankby = $_POST["rankby"];
		
		$ids = getIdsFromSearch($query,$iterations,$rankby,$language,$regioncode,$date_before,$date_after);

		makeStatsFromIds($ids);
		
	} else if($mode == "seeds") {
		
		$seeds = $_POST["seeds"];
		
		$seeds = preg_replace("/\s+/","",$seeds);
		$seeds = trim($seeds);
		
		$ids = explode(",",$seeds);
		
		makeStatsFromIds($ids);
		
	} else {
		
		echo "You need to select a mode.";
	}
}


function getIdsFromChannel($channel) {

	global $apikey;

	$restquery = "https://www.googleapis.com/youtube/v3/channels?part=contentDetails&id=".$channel."&key=".$apikey;
	
	$reply = doAPIRequest($restquery);
	
	if(isset($reply->items[0]->contentDetails->relatedPlaylists->uploads)) {
		
		$uplistid = $reply->items[0]->contentDetails->relatedPlaylists->uploads;
		$nextpagetoken = null;
		$ids = array();
		$run = true;
		
		//echo "<br />Retrieving videos."; flush(); ob_flush();
		
		while($run == true) {
		
			$restquery = "https://www.googleapis.com/youtube/v3/playlistItems?part=contentDetails&maxResults=50&playlistId=".$uplistid."&key=".$apikey;
			
			if($nextpagetoken != null) {
				$restquery .= "&pageToken=".$nextpagetoken;
			}
			
			$reply = doAPIRequest($restquery);

			//print_r($reply); //exit;
			
			foreach($reply->items as $item) {
				$ids[] = $item->contentDetails->videoId;
			}
			
			if(isset($reply->nextPageToken)) {
				
				$nextpagetoken = $reply->nextPageToken;
				//return $ids;
					
			} else {
				
				return $ids;
			}
		}
		
	} else {
		
		echo "This is either not a valid channel id or the channel has no uploads playlist.";
	}
}


function getIdsFromPlaylist($uplistid) {

	global $apikey;

	$nextpagetoken = null;
	$ids = array();
	$run = true;
	
	//echo "<br />Retrieving videos."; flush(); ob_flush();
	
	while($run == true) {
	
		$restquery = "https://www.googleapis.com/youtube/v3/playlistItems?part=contentDetails&maxResults=50&playlistId=".$uplistid."&key=".$apikey;
		
		if($nextpagetoken != null) {
			$restquery .= "&pageToken=".$nextpagetoken;
		}
		
		$reply = doAPIRequest($restquery);

		//print_r($reply); exit;
		
		foreach($reply->items as $item) {
			$ids[] = $item->contentDetails->videoId;
		}
		
		if(isset($reply->nextPageToken)) {
			
			$nextpagetoken = $reply->nextPageToken;
			//return $ids;
				
		} else {
			
			return $ids;
		}
	}
}



function getIdsFromSearch($query,$iterations,$rankby,$language,$regioncode,$date_before,$date_after) {

	global $apikey;
	
	$nextpagetoken = null;
	$ids = array();

	for($i = 0; $i < $iterations; $i++) {
		
		$restquery = "https://www.googleapis.com/youtube/v3/search?part=snippet&maxResults=50&q=". urlencode($query)."&type=video&order=".$rankby."&key=".$apikey;
		if($date_before != false) {
			$restquery .= "&publishedAfter=".$date_after."&publishedBefore=".$date_before;
		}
		
		if($language != "") { $restquery .= "&relevanceLanguage=" . $language; }
		if($regioncode != "") { $restquery .= "&regionCode=" . $regioncode; }
		
		//echo $restquery;
		
		if($nextpagetoken != null) {
			$restquery .= "&pageToken=".$nextpagetoken;
		}
		
		$reply = doAPIRequest($restquery);
		$nextpagetoken = $reply->nextPageToken;
		
		//print_r($reply);
		
		foreach($reply->items as $item) {
			$ids[] = $item->id->videoId;
		}
	}
	
	return $ids;
}
	
	
function makeStatsFromIds($ids) {
	
	global $apikey,$mode,$folder;
	
	$vids = array();
	$lookup = array();
	$categoryIds = array();
	
	echo "<br />Getting video details (".count($ids)."): ";
	
	for($i = 0; $i < count($ids); $i++) {
		
		$vid = $ids[$i];
		$lookup[$vid] = $i; 
		
		$restquery = "https://www.googleapis.com/youtube/v3/videos?part=statistics,contentDetails,snippet&id=".$vid."&key=".$apikey;

		$reply = doAPIRequest($restquery);
		
		$vid = $reply->items[0];
		
		//print_r($vid); exit;
		
		// convert YT duraction format to seconds
		preg_match_all('/(\d+)/',$vid->contentDetails->duration,$parts);
		$tmptime = array_reverse($parts[0]);
		$smulti = 1;
		$seconds = 0;
		for($j = 0;  $j < count($tmptime); $j++) {
			$seconds += $tmptime[$j] * $smulti;
			$smulti = $smulti * 60; 
		}
		
		// collect categories
		if(!in_array($vid->snippet->categoryId,$categoryIds)) { $categoryIds[] = $vid->snippet->categoryId; }
		
		
		$row = array();
		$row["channelId"] = $vid->snippet->channelId;
		$row["channelTitle"] = $vid->snippet->channelTitle;
		$row["videoId"] = $vid->id;
		$row["publishedAt"] = $vid->snippet->publishedAt;
		$row["publishedAtSQL"] = date("Y-m-d H:i:s", strtotime($vid->snippet->publishedAt));
		$row["videoTitle"] = preg_replace("/\s+/", " ",$vid->snippet->title);
		$row["videoDescription"] = preg_replace("/\s+/", " ",$vid->snippet->description);
		$row["videoCategoryId"] = $vid->snippet->categoryId;
		$row["videoCategoryLabel"] = "";
		$row["duration"] = $vid->contentDetails->duration;
		$row["durationSec"] = $seconds;
        $row["dimension"] = $vid->contentDetails->dimension;
        $row["definition"] = $vid->contentDetails->definition;
        $row["caption"] = $vid->contentDetails->caption;
        $row["licensedContent"] = $vid->contentDetails->licensedContent;
        $row["viewCount"] = $vid->statistics->viewCount;
        $row["likeCount"] = $vid->statistics->likeCount;
        $row["dislikeCount"] = $vid->statistics->dislikeCount;
        $row["favoriteCount"] = $vid->statistics->favoriteCount;
        $row["commentCount"] = $vid->statistics->commentCount;
		
		$vids[] = $row;
		
		//print_r($row); exit;
		
		echo $i . " "; flush(); ob_flush();
	}
	
	
	// get category labels and assign to videos
	$restquery = "https://www.googleapis.com/youtube/v3/videoCategories?part=snippet&id=".urlencode(implode(",", $categoryIds))."&key=".$apikey;

	$reply = doAPIRequest($restquery);

	$categoryTrans = array();
	foreach($reply->items as $cat) {
		$categoryTrans[$cat->id] = $cat->snippet->title;
	}
	
	for($i = 0; $i < count($vids); $i++) {
		$vids[$i]["videoCategoryLabel"] = $categoryTrans[$vids[$i]["videoCategoryId"]];
	}
	
	
	// create TSV file
	$content_tsv = "position\t".implode("\t", array_keys($vids[0])) . "\n";
	
	for($i = 0; $i < count($vids); $i++) {
		$content_tsv .=  ($i + 1) . "\t". implode("\t",$vids[$i]) . "\n";
	}

	$filename = "videolist_" . $mode . count($vids) . "_" . date("Y_m_d-H_i_s");
	if(isset($_POST["filename"])) { $filename = $_POST["filename"] . "_" . $filename; }

	file_put_contents($folder.$filename.".tab", $content_tsv);
	
	echo '<br /><br />The script has created a file with  '.count($vids).' rows.<br /><br />

	your files:<br />
	<a href="'.$folder.$filename.'.tab" download>'.$filename.'.tab</a><br />';

}

?>

</body>
</html>