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
	<form action="mod_videos_net.php" method="post">
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
				<h1>YTDT Video Network</h1>
				
				<p>This module creates a network of relations between videos, starting from a search or a list of video ids.</p>
				
				<p>It retrieves "related videos" from the <a href="https://developers.google.com/youtube/v3/docs/search/list#relatedToVideoId" target="_blank">search/list#relatedToVideoId</a> API endpoint and creates a graph file in GDF format.</p>
				
				<p>Crawl depth specifies how far from the seeds the script should go. Crawl depth 0 will get only the relations between seeds. Using many seeds and the maximum crawl depth (2) can take a very long time or the script might run out of memory. Start small.</p>
			</td>
		</tr>
		<tr>
			<td colspan="5"><hr /></td>
		</tr>
		<tr>
			<td colspan="5">1) choose a starting point:</td>
		</tr>
		<tr>
			<td><input type="radio" name="mode" value="search" <?php if($_POST["mode"] != "seeds") { echo "checked"; } ?> /></td>
			<td>search query:</td>
			<td><input type="text" name="query" value="<?php if(isset($_POST["query"])) { echo $_POST["query"]; } ?>" /></td>
			<td>(this is passed to the search endpoint)</td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td>Iterations:</td>
			<td><input type="text" name="iterations" max="10" value="<?php echo (isset($_POST["iterations"])) ? $_POST["iterations"]:1; ?>" /></td>
			<td>(max. 10, one iteration gets 50 items)</td>
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
			<td>seeds:</td>
			<td colspan="2">
				<textarea name="seeds"><?php if($_POST["mode"] == "seeds") { echo $_POST["seeds"]; } ?></textarea>
			</td>
			<td>(video ids, comma separated)</td>
		</tr>
		<tr>
			<td colspan="5"><hr /></td>
		</tr>
		<tr>
			<td colspan="2">2) set crawl depth:</td>
			<td><input type="text" name="crawldepth" max="2" value="<?php echo (isset($_POST["crawldepth"])) ? $_POST["crawldepth"]:1; ?>" /></td>
			<td colspan="2">(values are 0, 1 or 2)</td>
		</tr>
		<tr>
			<td colspan="5"><hr /></td>
		</tr>
		<tr>
			<td colspan="5"><input type="submit" /></td>
		</tr>
	</form>
</table>

<p>
<?php

// allow for direct URL parameters and command line for cron
if(isset($argv)) { parse_str(implode('&', array_slice($argv, 1)), $_GET); }
if(isset($_GET["mode"])) { $_POST = $_GET; }

if(isset($_POST["query"]) || isset($_POST["seeds"])) {

	$mode = $_POST["mode"];
	$crawldepth = $_POST["crawldepth"];
	$nodes = array();
	$edges = array();
	
	if($_POST["crawldepth"] > 2 || preg_match("/\D/", $crawldepth)) {
		echo "Wrong crawldepth.";
		exit;
	}

	if($mode == "search") {
		
		if($_POST["query"] == "") {
			echo "Missing query.";
			exit;
		}
		
		if($_POST["iterations"] > 10 || preg_match("/\D/", $_POST["iterations"])) {
			echo "Wrong iteration parameter.";
			exit;
		}
		
		$query = $_POST["query"];
		$iterations = $_POST["iterations"];
		$rankby = $_POST["rankby"];
		
		$ids = getIdsFromSearch($query,$iterations,$rankby);
		
		$no_seeds = count($ids);
		
		//print_r($ids); exit;
		makeNetworkFromIds(0);
		
	} else if($mode == "seeds") {
		
		$seeds = $_POST["seeds"];
		
		$seeds = preg_replace("/\s+/","",$seeds);
		$seeds = trim($seeds);
		
		$ids = explode(",",$seeds);
		
		$no_seeds = count($ids);
		
		//print_r($ids); exit;
		makeNetworkFromIds(0);
		
	} else {
		
		echo "You need to select a mode.";
	}
}


function getIdsFromSearch($query,$iterations,$rankby) {

	global $apikey;
	
	$nextpagetoken = null;
	$ids = array();

	for($i = 0; $i < $iterations; $i++) {
		
		$restquery = "https://www.googleapis.com/youtube/v3/search?part=snippet&maxResults=50&q=".urlencode($query)."&type=video&order=".$rankby."&key=".$apikey;
		
		if($nextpagetoken != null) {
			$restquery .= "&pageToken=".$nextpagetoken;
		}
		
		$reply = doAPIRequest($restquery);

		$nextpagetoken = $reply->nextPageToken;
		
		foreach($reply->items as $item) {
			$ids[] = $item->id->videoId;
		}
	}
	
	return $ids;
}
	
	
function makeNetworkFromIds($depth) {
	
	global $apikey,$nodes,$edges,$ids,$crawldepth;
	
	echo "<br /><br />getting details for ".count($ids)." videos at depth ".$depth.": ";
	
	$newids = array();
	$categoryIds = array();
	
	for($i = 0; $i < count($ids); $i++) {
		
		$vid = $ids[$i];
		
		$restquery = "https://www.googleapis.com/youtube/v3/videos?part=statistics,contentDetails,snippet&id=".$vid."&key=".$apikey;
		
		$reply = doAPIRequest($restquery);

		if(isset($reply->items[0])) {
			
			$video = $reply->items[0];
			
			// collect categories
			if(!in_array($video->snippet->categoryId,$categoryIds)) { $categoryIds[] = $video->snippet->categoryId; }
			
			$row = array();
			$row["channelId"] = $video->snippet->channelId;
			$row["channelTitle"] = preg_replace("/\s+/", " ",$video->snippet->channelTitle);
			$row["videoId"] = $video->id;
			$row["publishedAt"] = strtotime($video->snippet->publishedAt);
			$row["videoTitle"] = preg_replace("/\s+/", " ",$video->snippet->title);
			$row["videoDescription"] = preg_replace("/\s+/", " ",$video->snippet->description);
			$row["videoCategoryId"] = $video->snippet->categoryId;
			$row["videoCategoryLabel"] = "";
			$row["duration"] = $video->contentDetails->duration;
	        $row["dimension"] = $video->contentDetails->dimension;
	        $row["definition"] = $video->contentDetails->definition;
	        $row["caption"] = $video->contentDetails->caption;
	        $row["licensedContent"] = $video->contentDetails->licensedContent;
	        $row["viewCount"] = $video->statistics->viewCount;
	        $row["likeCount"] = $video->statistics->likeCount;
	        $row["dislikeCount"] = $video->statistics->dislikeCount;
	        $row["favoriteCount"] = $video->statistics->favoriteCount;
	        $row["commentCount"] = $video->statistics->commentCount;
			
			$nodes[$vid] = $row;
			
			if($depth == 0) {
				$nodes[$vid]["isSeed"] = "yes";
				$nodes[$vid]["seedRank"] = ($i + 1);
			} else {
				$nodes[$vid]["isSeed"] = "no";
				$nodes[$vid]["seedRank"] = "";
			}
		}
		
		echo $i . " "; flush(); ob_flush();
	}


	// get category labels and assign to videos
	$restquery = "https://www.googleapis.com/youtube/v3/videoCategories?part=snippet&id=".urlencode(implode(",", $categoryIds))."&key=".$apikey;

	$reply = doAPIRequest($restquery);

	$categoryTrans = array();
	foreach($reply->items as $cat) {
		$categoryTrans[$cat->id] = $cat->snippet->title;
	}
		
	foreach ($nodes as $key => $node) {
		$nodes[$key]["videoCategoryLabel"] = $categoryTrans[$node["videoCategoryId"]];
	}

		
	echo "<br />getting related videos for ".count($ids)." videos at depth ".$depth.": ";
	
	for($i = 0; $i < count($ids); $i++) {
		
		$vid = $ids[$i];
		
		// get related videos
		$run = true;
		$nextpagetoken = null;
		
		while($run == true) {
	
			$restquery = "https://www.googleapis.com/youtube/v3/search?part=id&maxResults=50&relatedToVideoId=".$vid."&type=video&key=".$apikey;
			
			if($nextpagetoken != null) {
				$restquery .= "&pageToken=".$nextpagetoken;
			}
			
			$reply = doAPIRequest($restquery);
								
			foreach($reply->items as $item) {
				
				$featid = $item->id->videoId;
				
				if(!isset($nodes[$featid])) {
					
					if(!in_array($featid, $newids)) {

						$newids[] = $featid;
					}
					
					if($depth < $crawldepth) {
						$edgeid = $vid . "_|_|X|_|_" . $featid;
						$edges[$edgeid] = true;
					}
					
				} else {
	
					$edgeid = $vid . "_|_|X|_|_" . $featid;
					$edges[$edgeid] = true;
				}
			}
			
			if(isset($reply->nextPageToken) && $reply->nextPageToken != "") {
				
				$nextpagetoken = $reply->nextPageToken;
					
			} else {
				
				$run = false;
			}
		}

		echo $i . " "; flush(); ob_flush();
	}

	
	
	if($depth == $crawldepth) {
		
		//print_r($nodes); exit;
		
		renderNetwork();
		
	} else {
		
		//print_r($newids);
		
		$ids = $newids;
		
		$depth++;
		
		makeNetworkFromIds($depth);
	}
}

	

function renderNetwork() {
	
	global $nodes,$edges,$lookup,$no_seeds,$mode;
	
	//print_r($nodes); exit;
	
	$nodegdf = "nodedef>name VARCHAR,label VARCHAR,isSeed VARCHAR,seedRank INT,publishedAt INT,channelTitle VARCHAR,channelId VARCHAR,videoCategoryLabel VARCHAR,viewCount INT,likeCount INT,dislikeCount INT,favoriteCount INT,commentCount INT\n";
	foreach($nodes as $nodeid => $nodedata) {
		$nodegdf .= $nodeid . "," . preg_replace("/,|\"|\'/"," ",$nodedata["videoTitle"]) . "," . $nodedata["isSeed"] . "," . $nodedata["seedRank"] . "," . $nodedata["publishedAt"] . "," . preg_replace("/,|\"|\'/"," ",$nodedata["channelTitle"]) . "," . $nodedata["channelId"] . "," . 
					 preg_replace("/,|\"|\'/"," ",$nodedata["videoCategoryLabel"]) . "," .$nodedata["viewCount"] . "," . $nodedata["likeCount"] . "," . $nodedata["dislikeCount"] . "," . $nodedata["favoriteCount"] . "," . $nodedata["commentCount"] . "," . "\n";
	}
	
	$edgegdf = "edgedef>node1 VARCHAR,node2 VARCHAR\n";
	foreach($edges as $edgeid => $edgedata) {
		$tmp = explode("_|_|X|_|_",$edgeid);
		if(isset($nodes[$tmp[0]]) && isset($nodes[$tmp[1]])) {
			$edgegdf .= $tmp[0] . "," . $tmp[1] . "\n";
		}
	}
	
	$gdf = $nodegdf . $edgegdf;
	$filename = "videonet_" . $mode . $no_seeds . "_nodes" . count($nodes) . "_" . date("Y_m_d-H_i_s");
	if(isset($_POST["filename"])) { $filename = $_POST["filename"] . "_" . $filename; }

	file_put_contents("./data/".$filename.".gdf", $gdf);
	
	echo '<br /><br />The script has created a net with  '.count($nodes).' videos from '.$no_seeds.' seeds.<br /><br />

	your files:<br />
	<a href="./data/'.$filename.'.gdf">'.$filename.'.gdf</a><br />';

}

?>
</p>

</body>
</html>
