<?php

require_once "config.php";
require_once "common.php";

//ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');
//error_reporting(E_ALL);

$folder = DATAFOLDER;

// allow for direct URL parameters
if(isset($_GET["mode"])) { $_POST = $_GET; }

// command line interface
// e.g. php mod_videos_list.php rankby=relevance mode=search iterations=6 query=yourquery filename=yourfilename
// don't forget to set CRONFOLDER in config.php
if(isset($argv)) {
	parse_str(implode('&', array_slice($argv, 1)), $_POST);
	$folder = CRONFOLDER;
	define('WEBMODE', false);
} else {

	define('WEBMODE', true);

	include("html_head.php");

?>

	<div class="rowTab">
		<div class="sectionTab">
			<h1>Video List Module</h1>
		</div>
	</div>

	<div class="rowTab">
		<div class="fullTab">
		<p>This module creates a list of video infos and statistics from one of four sources: the videos uploaded to a specified channel, a playlist, the 
		videos retrieved by a particular search query, or the videos specified by a list of ids.</p>
		
		<p>The script then creates a tabular file where each row is a video. A number of infos and variables are added for each video.</p>
		
		<p>Check the documentation for the <a href="https://developers.google.com/youtube/v3/docs/videos/list" target="_blank">video/list</a> (used to
		get the info for each video) and the
		<a href="https://developers.google.com/youtube/v3/docs/search/list" target="_blank">search/list</a> (used for the search function) API endpoint for
		additional information.</p>

		</div>
	</div>

	<div class="rowTab">
		<div class="sectionTab"><h1>Parameters</h1></div>
	</div>
	
	<div class="rowTab">
		<div class="sectionTab"><h2>Choose a way of making a list:</h2></div>
	</div>
	
	<form action="mod_videos_list.php" method="post">
	
	<div class="rowTab">
		<div class="oneTab"><input type="radio" name="mode" value="channel" <?php if($_POST["mode"] != "seeds" && $_POST["mode"] != "search") { echo "checked"; } ?> /></div>
		<div class="twoTab">Channel id:</div>
		<div class="threeTab">
			<input type="text" name="channel" value="<?php if(isset($_POST["channel"])) { echo $_POST["channel"]; }; ?>" />
		</div>
		<div class="fourTab">(channel ids can be found in URLs, e.g. <span class="grey">https://www.youtube.com/channel/</span><b>UCtxGqPJPPi8ptAzB029jpYA</b>)</div>
	</div>
	
	<div class="rowTab">
		<div class="sectionTab"><hr /></div>
	</div>
	
	
	<div class="rowTab">
		<div class="oneTab"><input type="radio" name="mode" value="playlist" <?php if($_POST["mode"] == "playlist") { echo "checked"; } ?> /></div>
		<div class="twoTab">Playlist id:</div>
		<div class="threeTab">
			<input type="text" name="playlist" value="<?php if(isset($_POST["playlist"])) { echo $_POST["playlist"]; }; ?>" />
		</div>
		<div class="fourTab">(playlist ids can be found in URLs, e.g. <span class="grey">https://www.youtube.com/playlist?list=</span><b>PLJtitKU0CAehMmiSI9oCIv3WCJrZqMWZ0</b>)</div>
	</div>
	
	<div class="rowTab">
		<div class="sectionTab"><hr /></div>
	</div>
	
	
	<div class="rowTab">
		<div class="oneTab"><input type="radio" name="mode" value="search" <?php if($_POST["mode"] == "search") { echo "checked"; } ?> /></div>
		<div class="twoTab">Search query:</div>
		<div class="threeTab">
			<input type="text" name="query" value="<?php if(isset($_POST["query"])) { echo $_POST["query"]; }; ?>" />
		</div>
		<div class="fourTab">
			(this is passed to the search endpoint, check the "q" parameter <a href="https://developers.google.com/youtube/v3/docs/search/list" target="_blank">here</a> for how to use boolean operators)
			<p>optional <a href="http://www.loc.gov/standards/iso639-2/php/code_list.php" target="_blank">ISO 639-1</a> relevance language: <input type="text" name="language" style="width:20px;" value="<?php if(isset($_POST["language"])) { echo $_POST["language"]; }; ?>" /></p>
			<p>optional <a href="https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2" target="_blank">ISO 3166-1 alpha-2</a> region code: <input type="text" name="regioncode" style="width:20px;" value="<?php if(isset($_POST["regioncode"])) { echo $_POST["regioncode"]; }; ?>" /> (default = US)</p>
		</div>
	</div>
	
	<div class="rowTab">
		<div class="oneTab"></div>
		<div class="twoTab">Iterations:</div>
		<div class="threeTab">
			<input type="text" name="iterations" max="10" value="<?php echo (isset($_POST["iterations"])) ? $_POST["iterations"]:1; ?>" />
		</div>
		<div class="fourTab">(max. 10, one iteration gets 50 items)</div>
	</div>
	
	<div class="rowTab">
		<div class="oneTab"></div>
		<div class="twoTab">Published:</div>
		<div class="fourTab">
			<input type="checkbox" name="timeframe" <?php if(isset($_POST["timeframe"])) { echo "checked"; } ?> /> limit search to videos published in a specific timeframe (format: yyyy-mm-ddThh:mm:ssZ - timezone: UTC):
			<p>after: <input type="text" name="date_after" value="<?php echo (isset($_POST["date_after"])) ? $_POST["date_after"]:"1970-01-01T00:00:00Z"; ?>" />&nbsp;&nbsp;&nbsp;</p>
			<p>before: <input type="text" name="date_before" value="<?php echo (isset($_POST["date_before"])) ? $_POST["date_before"]:"1970-01-01T00:00:00Z"; ?>" /></p>
			<input type="checkbox" name="daymode" <?php if(isset($_POST["daymode"])) { echo "checked"; } ?> /> make a search for each day of the timeframe (can yield many more videos, use wisely)
		</div>
	</div>
	
	<div class="rowTab">
		<div class="oneTab"></div>
		<div class="twoTab">Rank by:</div>
		<div class="fourTab">
			<select name="rankby">
				<option value="relevance" <?php if($_POST["rankby"] == "relevance") { echo "selected"; } ?>>relevance - Resources are sorted based on their relevance to the search query</option>
				<option value="date" <?php if($_POST["rankby"] == "date") { echo "selected"; } ?>>date – Resources are sorted in reverse chronological order based on the date they were created</option>
				<option value="rating" <?php if($_POST["rankby"] == "rating") { echo "selected"; } ?>>rating – Resources are sorted from highest to lowest rating</option>
				<option value="title" <?php if($_POST["rankby"] == "title") { echo "selected"; } ?>>title – Resources are sorted alphabetically by title</option>
				<!--<option value="videoCount" <?php if($_POST["rankby"] == "videoCount") { echo "selected"; } ?>>videoCount – Channels are sorted in descending order of their number of uploaded videos</option>-->
				<option value="viewCount" <?php if($_POST["rankby"] == "viewCount") { echo "selected"; } ?>>viewCount - Resources are sorted from highest to lowest number of views</option>	
			</select>
		</div>
	</div>

	<div class="rowTab">
		<div class="oneTab"></div>
		<div class="twoTab">Location:</div>
		<div class="fourTab">
			<input type="checkbox" name="location" <?php if(isset($_POST["location"])) { echo "checked"; } ?> /> search for videos that specify location in their metadata:
			<p>point: <input type="text" name="location_point" value="<?php echo (isset($_POST["location_point"])) ? $_POST["location_point"]:""; ?>" /> (latitude/longitude coordinates, e.g. 37.42307,-122.08427)</p>
			<p>radius: <input type="text" name="location_radius" value="<?php echo (isset($_POST["location_radius"])) ? $_POST["location_radius"]:""; ?>" /> (radius in m, km, ft, or mi, e.g. 10km)</p>
		</div>
	</div>
	

	<div class="rowTab">
		<div class="sectionTab"><hr /></div>
	</div>
	
	
	<div class="rowTab">
		<div class="oneTab"><input type="radio" name="mode" value="seeds" <?php if($_POST["mode"] == "seeds") { echo "checked"; } ?> /></div>
		<div class="twoTab">Manual selection:</div>
		<div class="threeTab">
			<textarea name="seeds"><?php if($_POST["mode"] == "seeds") { echo $_POST["seeds"]; } ?></textarea>
		</div>
		<div class="fourTab">(video ids, comma separated)</div>
	</div>

	<div class="rowTab">
		<div class="sectionTab"><h2>Output options:</h2></div>
	</div>

	<div class="rowTab">
		<div class="oneTab"></div>
		<div class="twoTab">File format:</div>
		<div class="fourTab">
			csv <input type="radio" name="output" value="csv" checked /> / 
			tab <input type="radio" name="output" value="tab" />
		</div>
	</div>

	<div class="rowTab">
		<div class="oneTab"></div>
		<div class="twoTab">Co-tag network:</div>
		<div class="fourTab"><input type="checkbox" name="cotag" <?php if(isset($_POST["cotag"])) { echo "checked"; } ?> /> generate a co-tag network (can run out of memory if used with very long video lists)</div>
	</div>
	
	<div class="rowTab">
		<div class="sectionTab"><h2>Run:</h2></div>
	</div>

	<div class="rowTab">
		<div class="oneTab">
			<div class="g-recaptcha" data-sitekey="<?php echo $sitekey; ?>"></div>
		</div>
	</div>
	
	<div class="rowTab">
		<div class="oneTab"><input type="submit" /></div>
	</div>
	
	</form>
	
<?php

}

if(isset($_POST["channel"]) || isset($_POST["seeds"]) || isset($_POST["query"])) {

	outweb('<div class="rowTab">
			<div class="sectionTab"><h1>Results</h1></div>
		 </div>
		 <div class="rowTab">');
	out('Processing:');

	if(RECAPTCHA && WEBMODE) {
		if($_POST["g-recaptcha-response"] == "") {
			echo "<br /><br />Recaptcha missing.";
			exit;
		}
		testcaptcha($_POST["g-recaptcha-response"]);
	}

	$mode = $_POST["mode"];
	$output = $_POST["output"];
	$cotag = isset($_POST["cotag"]);

	if($mode == "channel") {

		if($_POST["channel"] == "") {
			out("<br /><br />Missing channel id.");
			exit;
		}
	
		$channel = $_POST["channel"];
		
		if(preg_match("/,/",$channel)) {
			$channels = preg_split("/,/",$channel);
			
			out("<br /><br />Getting videos from several channels: ");
			
			$ids = array();
			$count = 0;
			foreach($channels as $channel) {
				$tmpsids = getIdsFromChannel(trim($channel));
				
				out($count . " ");
				
				$count++;
				
				$ids = array_merge($ids,$tmpsids);
			} 
			
			$ids = array_unique($ids);
			
		} else {
		
			$ids = getIdsFromChannel($channel);
		}
		
		makeStatsFromIds($ids);
		
	} else if($mode == "playlist") {
	
		if($_POST["playlist"] == "") {
			out("<br /><br />Missing playlist id.");
			exit;
		}
	
		$playlist = $_POST["playlist"];
		
		$ids = getIdsFromPlaylist($playlist);
		
		makeStatsFromIds($ids);
		
	} else if($mode == "search") {
		
		if($_POST["query"] == "" && !isset($_POST["location"])) {
			out("<br /><br />Missing query.");
			exit;
		}
		
		if($_POST["iterations"] > 10 || preg_match("/\D/", $_POST["iterations"])) {
			out("<br /><br />Wrong iteration parameter.");
			exit;
		}
		
		$language = $_POST["language"];
		$regioncode = $_POST["regioncode"];
		$query = $_POST["query"];
		$iterations = $_POST["iterations"];
		$daymode = isset($_POST["daymode"]);
		$date_before = $date_after = false;
		if(isset($_POST["timeframe"])) {
			$date_before = $_POST["date_before"];
			$date_after = $_POST["date_after"];
		}
		$rankby = $_POST["rankby"];
		$locationmode = isset($_POST["location"]);
		if($locationmode == true) {
			if($_POST["location_point"] != "" && $_POST["location_radius"] != "") {
				$location_point = $_POST["location_point"];
				$location_radius = $_POST["location_radius"];
			} else {
				out("missing location parameters");
			}
		} else {
			$location_point = $location_radius = "";
		}
		
		$ids = getIdsFromSearch($query,$iterations,$rankby,$language,$regioncode,$daymode,$date_before,$date_after,$locationmode,$location_point,$location_radius);

		makeStatsFromIds($ids);
		
	} else if($mode == "seeds") {
		
		if($_POST["seeds"] == "") {
			out("<br /><br />Missing seed ids.");
			exit;
		}
		
		$seeds = $_POST["seeds"];
		
		$seeds = preg_replace("/\s+/","",$seeds);
		$seeds = trim($seeds);
		
		$ids = explode(",",$seeds);
		
		$ids = array_values(array_unique($ids));
		
		makeStatsFromIds($ids);
		
	} else {
		
		out("<br /><br />You need to select a mode.");
	}
	
	outweb('</div>');
}


function getIdsFromChannel($channel) {

	$restquery = "https://www.googleapis.com/youtube/v3/channels?part=contentDetails&id=".$channel;
	
	$reply = doAPIRequest($restquery);
	
	if(isset($reply->items[0]->contentDetails->relatedPlaylists->uploads)) {
		
		$uplistid = $reply->items[0]->contentDetails->relatedPlaylists->uploads;
		$nextpagetoken = null;
		$ids = array();
		$run = true;
		
		while($run == true) {
		
			$restquery = "https://www.googleapis.com/youtube/v3/playlistItems?part=contentDetails&maxResults=50&playlistId=".$uplistid;
			
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
		
		out("This is either not a valid channel id or the channel has no uploads playlist.");
	}
}


function getIdsFromPlaylist($uplistid) {

	$nextpagetoken = null;
	$ids = array();
	$run = true;
	
	while($run == true) {
	
		$restquery = "https://www.googleapis.com/youtube/v3/playlistItems?part=contentDetails&maxResults=50&playlistId=".$uplistid;
		
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


function getIdsFromSearch($query,$iterations,$rankby,$language,$regioncode,$daymode,$date_before,$date_after,$locationmode,$location_point,$location_radius) {
	
	$nextpagetoken = null;
	$datespans = array();
	$ids = array();
	
	if($daymode) {
		
		$before = strtotime($date_before);
		$after = strtotime($date_after);
		
		while($after < $before) {
			
			$datespans[] = array("after" => date("Y-m-d\TH:i:s",$after) . "Z","before" => date("Y-m-d\TH:i:s",$after + 86400) . "Z");
			
			$after = $after + 86400;
		}
	} else {
		
		$datespans[] = array("after" => $date_after,"before" => $date_before);
	}

	//print_r($datespans);
	
	out("<br /><br />Executing searches (".count($datespans)."): ");
	$counter = 0;

	foreach($datespans as $datespan) {

		out($counter . " ");
		$counter++;

		$nextpagetoken = null;
	
		for($i = 0; $i < $iterations; $i++) {
			
			$restquery = "https://www.googleapis.com/youtube/v3/search?part=snippet&maxResults=50&q=". urlencode($query)."&type=video&order=".$rankby;
			if($date_before != false) {
				$restquery .= "&publishedAfter=".$datespan["after"]."&publishedBefore=".$datespan["before"];
			}
			
			if($language != "") { $restquery .= "&relevanceLanguage=" . $language; }
			if($regioncode != "") { $restquery .= "&regionCode=" . $regioncode; }
			if($locationmode) { $restquery .= "&type=video&location=" . $location_point . "&locationRadius=" . $location_radius; }
			
			if($nextpagetoken != null) {
				$restquery .= "&pageToken=".$nextpagetoken;
			}

			//echo $restquery;
			
			$reply = doAPIRequest($restquery);

			//print_r($reply);
			
			foreach($reply->items as $item) {
				$ids[] = $item->id->videoId;
			}

			if(isset($reply->nextPageToken)) {
				$nextpagetoken = $reply->nextPageToken;
			} else {
				break;
			}
		}
	}
	
	return array_values(array_unique($ids));
}
	
	
function makeStatsFromIds($ids) {
	
	global $mode,$folder,$output,$cotag;	
	
	$vids = array();
	$lookup = array(); 
	$categoryIds = array();	

	out("<br /><br />Getting video details (".count($ids)."): ");

	for($i = 0; $i < count($ids); $i++) {
	
		$vid = $ids[$i];
		$lookup[$vid] = $i;
		
		$restquery = "https://www.googleapis.com/youtube/v3/videos?part=statistics,contentDetails,snippet,recordingDetails,topicDetails&id=".$vid;

		//print($restquery);

		$reply = doAPIRequest($restquery);
		//print_r($reply);
		
		$vid = $reply->items[0];
		
		//print_r($vid); exit;		
		$seconds = 0;
		preg_match_all('/(\d+)M/',$vid->contentDetails->duration,$parts);
		$seconds += $parts[1][0] * 60;
		preg_match_all('/(\d+)S/',$vid->contentDetails->duration,$parts);
		$seconds += $parts[1][0];
		
		// collect categories
		if(!in_array($vid->snippet->categoryId,$categoryIds)) { $categoryIds[] = $vid->snippet->categoryId; }
		
		$row = array();
		$row["channelId"] = $vid->snippet->channelId;
		$row["channelTitle"] = $vid->snippet->channelTitle;
		$row["videoId"] = $ids[$i];
		$row["publishedAt"] = $vid->snippet->publishedAt;
		$row["publishedAtSQL"] = ($vid->snippet->publishedAt == "") ? "":date("Y-m-d H:i:s", strtotime($vid->snippet->publishedAt));
		$row["videoTitle"] = preg_replace("/\s+/", " ",$vid->snippet->title);
		$row["videoDescription"] = preg_replace("/\s+/", " ",$vid->snippet->description);
		$row["tags"] = (isset($vid->snippet->tags)) ? implode(",",$vid->snippet->tags):"";
		$row["videoCategoryId"] = $vid->snippet->categoryId;
		$row["videoCategoryLabel"] = "";
		$row["topicCategories"] = (isset($vid->topicDetails->topicCategories)) ? implode(",",$vid->topicDetails->topicCategories):"";
		$row["topicCategories"] = preg_replace("/https\:\/\/en\.wikipedia\.org\/wiki\//i","",$row["topicCategories"]);
		$row["duration"] = $vid->contentDetails->duration;
		$row["durationSec"] = $seconds;
        $row["dimension"] = $vid->contentDetails->dimension;
        $row["definition"] = $vid->contentDetails->definition;
        $row["caption"] = $vid->contentDetails->caption;
		$row["defaultLanguage"] = $vid->snippet->defaultLanguage;
		$row["defaultLAudioLanguage"] = $vid->snippet->defaultAudioLanguage;
        $row["thumbnail_maxres"] = $vid->snippet->thumbnails->maxres->url;
        $row["licensedContent"] = $vid->contentDetails->licensedContent;
		$row["locationDescription"] = $vid->recordingDetails->locationDescription;
		$row["latitude"] = $vid->recordingDetails->location->latitude;
		$row["longitude"] = $vid->recordingDetails->location->longitude;
        $row["viewCount"] = $vid->statistics->viewCount;
        $row["likeCount"] = $vid->statistics->likeCount;
        $row["dislikeCount"] = $vid->statistics->dislikeCount;
        $row["favoriteCount"] = $vid->statistics->favoriteCount;
        $row["commentCount"] = $vid->statistics->commentCount;
		
		$vids[] = $row;
		//print_r($row); exit;
		
		out($i . " ");
	}
	
	
	// get category labels and assign to videos
	$restquery = "https://www.googleapis.com/youtube/v3/videoCategories?part=snippet&id=".urlencode(implode(",", $categoryIds));

	$reply = doAPIRequest($restquery);

	$categoryTrans = array();
	foreach($reply->items as $cat) {
		$categoryTrans[$cat->id] = $cat->snippet->title;
	}
	
	for($i = 0; $i < count($vids); $i++) {
		$vids[$i]["videoCategoryLabel"] = $categoryTrans[$vids[$i]["videoCategoryId"]];
	}


	// co-tag network
	if($cotag) {
		$tagnodes = array();
		$tagedges = array();
		foreach($vids as $vid) {
			$tags = explode(",",strtolower(trim($vid["tags"])));
			$tags = array_filter($tags);

			for($i = 0; $i < count($tags); $i++) {
				if(!isset($tagnodes[$tags[$i]])) {
					$tagnodes[$tags[$i]] = 0;
				}
				$tagnodes[$tags[$i]]++;
			}

			for($i = 0; $i < count($tags); $i++) {

				for($j = $i+1; $j < count($tags); $j++) {

					$tmpedge = array($tags[$i],$tags[$j]);
					sort($tmpedge);

					$edgeid = $tmpedge[0] . "_|_|X|_|_" . $tmpedge[1];
					if(!isset($tagedges[$edgeid])) {
						$tagedges[$edgeid] = 0;
					}
					$tagedges[$edgeid]++;
				}
			}
		}

		$gdf = "nodedef>name VARCHAR,label VARCHAR,count INT\n";
		foreach($tagnodes as $nodeid => $nodedata) {

			$gdf .= $nodeid . "," . $nodeid . "," . $nodedata . "\n";

		}
		
		$gdf .= "edgedef>node1 VARCHAR,node2 VARCHAR,weight INT,directed BOOLEAN\n";
		foreach($tagedges as $edgeid => $edgedata) {
			$tmpedge = explode("_|_|X|_|_",$edgeid);
			$gdf .= $tmpedge[0] . "," . $tmpedge[1] . "," . $edgedata . ",false\n";
		}

		$filenamegdf = "videolist_tagnet_" . $mode . count($vids) . "_" . date("Y_m_d-H_i_s") . ".gdf";
		writefile($folder.$filenamegdf, $gdf);
	}

	

	
	// generate and write video list file
	$filename = "videolist_" . $mode . count($vids) . "_" . date("Y_m_d-H_i_s") . "." . $output;
	if(isset($_POST["filename"])) { $filename = $_POST["filename"] . "_" . $filename; }

	$fp = fopen($folder.$filename, 'w');
	$fieldnames = array_keys($vids[0]);
	array_unshift($fieldnames,'position');
	$separator = ($output == "tab") ? "\t":",";

	fputcsv($fp, $fieldnames,$separator);

	for($i = 0; $i < count($vids); $i++) {
		array_unshift($vids[$i], $i + 1);
		fputcsv($fp, $vids[$i],$separator);
	}

	fclose($fp);
	
	out("<br /><br />The script has created a file with " . count($vids) . " rows.<br /><br />");

	outweb('your files:<br />');
	if($cotag) {
		outweb('<a href="'.$folder.$filenamegdf.'" download>' . $filenamegdf . '</a><br />');
	}

	outweb('<a href="'.$folder.$filename.'" download>' . $filename . '</a>
	</body>
	</html>');

}

?>