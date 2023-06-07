<?php

require_once "config.php";
require_once "common.php";

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
			<h1>Channel List Module</h1>
		</div>
	</div>

	<div class="rowTab">
		<div class="fullTab">
		<p>This module creates a list of channel infos and statistics from one of two sources: the 
		channels corresponding to a particular search query or the channels specified by a list of ids.</p>
		
		<p>The script then creates a tabular file where each row is a channel. A number of infos and variables are added for each channel.</p>
		
		<p>Check the documentation for the <a href="https://developers.google.com/youtube/v3/docs/channels/list" target="_blank">channels/list</a> (used to
		get the info for each channel) and the
		<a href="https://developers.google.com/youtube/v3/docs/search/list" target="_blank">search/list</a> (used for the search function) API endpoints for
		additional information.</p>

		</div>
	</div>

	<div class="rowTab">
		<div class="sectionTab"><h1>Parameters</h1></div>
	</div>
	
	<div class="rowTab">
		<div class="sectionTab"><h2>Choose a starting point:</h2></div>
	</div>

	<form action="mod_channels_list.php" method="post">
	
	<div class="rowTab">
		<div class="oneTab"><input type="radio" name="mode" value="search" <?php if($_POST["mode"] != "seeds") { echo "checked"; } ?> /></div>
		<div class="twoTab">Search query:</div>
		<div class="threeTab">
			<input type="text" name="query" value="<?php if(isset($_POST["query"])) { echo $_POST["query"]; }; ?>" />
		</div>
		<div class="fourTab">
			(this is passed to the search endpoint)
			<p>optional <a href="http://www.loc.gov/standards/iso639-2/php/code_list.php" target="_blank">ISO 639-1</a> relevance language: <input type="text" name="language" style="width:20px;" value="<?php if(isset($_POST["language"])) { echo $_POST["language"]; }; ?>" /></p>
			<p>optional <a href="https://www.iso.org/obp/ui/#search" target="_blank">ISO 3166-1 alpha-2</a> region code: <input type="text" name="regioncode" style="width:20px;" value="<?php if(isset($_POST["regioncode"])) { echo $_POST["regioncode"]; }; ?>" /> (default = US)</p>
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
			<input type="checkbox" name="timeframe" <?php if(isset($_POST["timeframe"])) { echo "checked"; } ?> /> limit search to channels created in a specific timeframe (format: yyyy-mm-ddThh:mm:ssZ - timezone: UTC):
			<p>after: <input type="text" name="date_after" value="<?php echo (isset($_POST["date_after"])) ? $_POST["date_after"]:"1970-01-01T00:00:00Z"; ?>" />&nbsp;&nbsp;&nbsp;</p>
			<p>before: <input type="text" name="date_before" value="<?php echo (isset($_POST["date_before"])) ? $_POST["date_before"]:"1970-01-01T00:00:00Z"; ?>" /></p>
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
				<option value="videoCount" <?php if($_POST["rankby"] == "videoCount") { echo "selected"; } ?>>videoCount – Channels are sorted in descending order of their number of uploaded videos</option>
				<option value="viewCount" <?php if($_POST["rankby"] == "viewCount") { echo "selected"; } ?>>viewCount - Resources are sorted from highest to lowest number of views</option>	
			</select>
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
		<div class="fourTab">(channel ids, comma separated)</div>
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
		<div class="sectionTab"><h2>Run:</h2></div>
	</div>
	
	<div class="rowTab">
		<div class="oneTab">
			<div class="g-recaptcha" data-sitekey="6Lf093MUAAAAAIRLVzHqfIq9oZcOnX66Dju7e8sr"></div>
		</div>
	</div>
	
	<div class="rowTab">
		<div class="oneTab"><input type="submit" /></div>
	</div>
	
	</form>
	
<?php

}



if(isset($_POST["query"]) || isset($_POST["seeds"])) {

	outweb('<div class="rowTab">
			<div class="sectionTab"><h1>Results</h1></div>
		 </div>
		 <div class="rowTab">');
	out('Processing:');

	$topic_ids = file_get_contents("topic_ids.json");  
	$topic_ids = json_decode($topic_ids, true);

	if(RECAPTCHA && WEBMODE) {
		if($_POST["g-recaptcha-response"] == "") {
			echo "<br /><br />Recaptcha missing.";
			exit;
		}
		testcaptcha($_POST["g-recaptcha-response"]);
	}

	$mode = $_POST["mode"];
	$output = $_POST["output"];

	if($mode == "search") {

			
		if($_POST["query"] == "") {
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
		$date_before = $date_after = false;
		if(isset($_POST["timeframe"])) {
			$date_before = $_POST["date_before"];
			$date_after = $_POST["date_after"];
		}
		$rankby = $_POST["rankby"];

		
		$ids = getIdsFromSearch($query,$iterations,$rankby,$language,$regioncode,$date_before,$date_after);

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
		
		$ids = array_unique($ids);
		
		makeStatsFromIds($ids);

	} else {
		
		out("<br /><br />You need to select a mode.");
	}
	
	outweb('</div>');
} 




function getIdsFromSearch($query,$iterations,$rankby,$language,$regioncode,$date_before,$date_after) {
	
	$nextpagetoken = null;
	$datespans = array();
	$ids = array();
	
	$datespan = array("after" => $date_after,"before" => $date_before);
	
	out("<br /><br />Executing search iterations (" . $iterations . "): ");

	$nextpagetoken = null;

	for($i = 0; $i < $iterations; $i++) {
		
		out($i . " ");

		$restquery = "https://www.googleapis.com/youtube/v3/search?part=id&maxResults=50&q=". urlencode($query)."&type=channel&order=".$rankby;
		if($date_before != false) {
			$restquery .= "&publishedAfter=".$datespan["after"]."&publishedBefore=".$datespan["before"];
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
			$ids[] = $item->id->channelId;
		}
	}

	return array_values(array_unique($ids));
}
	
	
function makeStatsFromIds($ids) {
	
	global $mode,$folder,$output,$topic_ids;
	
	$vids = array();
	$lookup = array();
	$categoryIds = array();
	
	out("<br /><br />Getting channel details (".count($ids)."): ");
	
	for($i = 0; $i < count($ids); $i++) {
		
		out($i . " ");

		$restquery = "https://www.googleapis.com/youtube/v3/channels?part=id,snippet,topicDetails,statistics,brandingSettings&id=".$ids[$i];
		
		$reply = doAPIRequest($restquery);
		
		//print_r($reply);
		
		$channel = array();
		$channel["id"] = $reply->items[0]->id;
		$channel["title"] = preg_replace("/\s+/", " ",$reply->items[0]->snippet->title);
		$channel["description"] = preg_replace("/\s+/", " ",$reply->items[0]->snippet->description);
		$channel["publishedAt"] = $reply->items[0]->snippet->publishedAt;
		$channel["defaultLanguage"] = $reply->items[0]->snippet->defaultLanguage;
		$channel["country"] = $reply->items[0]->snippet->country;
		$channel["viewCount"] = $reply->items[0]->statistics->viewCount;
		$channel["subscriberCount"] = $reply->items[0]->statistics->subscriberCount;
		$channel["videoCount"] = $reply->items[0]->statistics->videoCount;
		$channel["thumbnail"] = $reply->items[0]->snippet->thumbnails->high->url;
		
		$keywords = $reply->items[0]->brandingSettings->channel->keywords;


		preg_match_all("/\".+?\"/", $keywords, $matches);

		$keywords = preg_replace("/\".+?\"/","",$keywords);
		$keywords = preg_replace("/\s+/"," ",trim($keywords));
		$keywords = ($keywords != "") ? explode(" ",trim($keywords)):array();
		//print_r($keywords);
		$keywords = array_merge($keywords,$matches[0]);
		
		$keywords = implode("|",$keywords);
		$keywords = preg_replace("/\"/","",$keywords);

		//print_r($keywords);
		
		$channel["keywords"] = $keywords;
		
		$topics = $reply->items[0]->topicDetails->topicIds;
		$tmptopics = array();
		$channel["topicDetails"] = "";
		foreach($topics as $topic) {
			array_push($tmptopics,$topic_ids[$topic]);
		}
		
		$channel["topicDetails"] = implode("|",$tmptopics);
		
		$channels[] = $channel;
	}
	
	//print_r($channels);

	$filename = "channelsearch_channels" . count($channels) . "_" . date("Y_m_d-H_i_s") . "." . $output;
	if(isset($_POST["filename"])) { $filename = $_POST["filename"] . "_" . $filename; }

	$fp = fopen($folder.$filename, 'w');
	$fieldnames = array_keys($channels[0]);
	array_unshift($fieldnames,'position');
	$separator = ($output == "tab") ? "\t":",";

	fputcsv($fp, $fieldnames,$separator);

	for($i = 0; $i < count($channels); $i++) {
		array_unshift($channels[$i], $i + 1);
		fputcsv($fp, $channels[$i],$separator);
	}

	fclose($fp);


	out("<br /><br />The script has created a file with " . count($channels) . " rows.<br /><br />");

	outweb('your files:<br />
	<a href="'.$folder.$filename.'" download>' . $filename . '</a>
	</body>
	</html>');
}

?>