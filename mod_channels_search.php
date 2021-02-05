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
			<h1>Channel Search Module</h1>
		</div>
	</div>

	<div class="rowTab">
		<div class="fullTab">
		<p>This module queries the <a href="https://developers.google.com/youtube/v3/docs/search/list" target="_blank">search/list</a> API endpoint
		for channels that match a search query and creates a tabular file where each row is a channel.</p>
		</div>
	</div>

	<div class="rowTab">
		<div class="sectionTab"><h1>Parameters</h1></div>
	</div>
	
	<form action="mod_channels_search.php" method="post">
	
	<div class="rowTab">
		<div class="oneTab"></div>
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
	
	<div class="g-recaptcha" data-sitekey="6Lf093MUAAAAAIRLVzHqfIq9oZcOnX66Dju7e8sr"></div>
	
	<div class="rowTab">
		<div class="oneTab"></div>
		<div class="fourTab">
			<input type="submit" />
		</div>
	</div>
	
	</form>
	
<?php

}

if(isset($_POST["query"])) {

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

	return $ids;
}
	
	
function makeStatsFromIds($ids) {
	
	global $mode,$folder;
	
	$vids = array();
	$lookup = array();
	$categoryIds = array();
	
	out("<br /><br />Getting channel details (".count($ids)."): ");
	
	for($i = 0; $i < count($ids); $i++) {
		
		out($i . " ");

		$restquery = "https://www.googleapis.com/youtube/v3/channels?part=id,snippet,statistics&id=".$ids[$i];
		
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
		
		$channels[] = $channel;
	}
	
	
	// create TSV file
	$content_tsv = "position\t".implode("\t", array_keys($channels[0])) . "\n";
	
	for($i = 0; $i < count($channels); $i++) {
		$content_tsv .=  ($i + 1) . "\t". implode("\t",$channels[$i]) . "\n";
	}

	$filename = "channelsearch_channels" . count($channels) . "_" . date("Y_m_d-H_i_s");
	if(isset($_POST["filename"])) { $filename = $_POST["filename"] . "_" . $filename; }

	writefile($folder.$filename.".tab", $content_tsv);
	
	out("<br /><br />The script has created a file with " . count($channels) . " rows.<br /><br />");

	outweb('your files:<br />
	<a href="'.$folder.$filename.'.tab" download>' . $filename . '.tab</a>
	</body>
	</html>');
}

?>