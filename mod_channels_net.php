<?php

require_once "config.php";

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
	<form action="mod_channels_net.php" method="post">
		<tr>
			<td colspan="5">
				<a href="index.php" class="navlink">Home</a>
				<a href="mod_channel_info.php" class="navlink">Channel Info</a>
				<a href="mod_channels_net.php" class="navlink">Channel Network</a>
				<a href="mod_videos_list.php" class="navlink">Video List</a>
				<a href="mod_video_info.php" class="navlink">Video Info</a>
			</td>
		</tr>
		<tr>
			<td colspan="5"></td>
		</tr>
		<tr>
			<td colspan="5">			
				<h1>YTDT Channel Network</h1>

				<p>This module crawls a network of channels connected via the "featured channels" tab from a list of seeds. Seeds can be channels retrieved from a search or via manual input of channel ids.</p>
				
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
			<td><input type="text" name="query" value="" /></td>
			<td>(this is passed to the search endpoint)</td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td>Iterations:</td>
			<td><input type="text" name="iterations" max="20" value="<?php echo (isset($_POST["iterations"])) ? $_POST["iterations"]:1; ?>" /></td>
			<td>(max. 20, one iteration gets 50 items)</td>
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
			<td>(channel ids, comma separated)</td>
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
		
		$restquery = "https://www.googleapis.com/youtube/v3/search?part=snippet&maxResults=50&q=".$query."&type=channel&order=".$rankby."&key=".$apikey;
		
		if($nextpagetoken != null) {
			$restquery .= "&pageToken=".$nextpagetoken;
		}
		
		$reply = json_decode(file_get_contents($restquery));
		$nextpagetoken = $reply->nextPageToken;
		
		foreach($reply->items as $item) {
			$ids[] = $item->id->channelId;
		}
	}
	
	return $ids;
}
	
	
function makeNetworkFromIds($depth) {
	
	global $apikey,$nodes,$edges,$ids,$crawldepth;
	
	echo "<br /><br />getting details for ".count($ids)." channels at depth ".$depth.": ";
	
	$newids = array();
	
	for($i = 0; $i < count($ids); $i++) {
		
		$chid = $ids[$i];
		
		try {
			
			$restquery = "https://www.googleapis.com/youtube/v3/channels?part=brandingSettings,status,id,snippet,contentDetails,contentOwnerDetails,statistics,topicDetails,invideoPromotion&id=".$chid."&key=".$apikey;
			$restquery = "https://www.googleapis.com/youtube/v3/channels?part=brandingSettings,id,snippet,statistics&id=".$chid."&key=".$apikey;
	
			$reply = json_decode(file_get_contents($restquery));
			
		} catch(Exception $e) {
			
			print_r($e);
			$i--;
			continue;
		}

		if(isset($reply->items[0])) {
			
			$nodes[$chid] = $reply->items[0];
			
			if($depth == 0) {
				$nodes[$chid]->isSeed = 1;
				$nodes[$chid]->seedRank = ($i + 1);
			} 
		}
		
		echo $i . " "; flush(); ob_flush();
	}


	//print_r(array_keys($nodes));
	
	foreach($nodes as $nodeid => $nodedata) {

		if(isset($nodedata->brandingSettings->channel->featuredChannelsUrls)) {
				
			foreach($nodedata->brandingSettings->channel->featuredChannelsUrls as $featid) {
								
				if(!isset($nodes[$featid])) {
					
					if(!in_array($featid, $newids)) {
						
						$newids[] = $featid;
						
						if($depth < $crawldepth) {
							$edgeid = $nodeid . "_|_|X|_|_" . $featid;
							$edges[$edgeid] = true;
						}
					}
					
				} else {
	
					$edgeid = $nodeid . "_|_|X|_|_" . $featid;
					$edges[$edgeid] = true;
				}
			}	
		}
	}
	
	
	
	if($depth == $crawldepth) {
		
		renderNetwork();
		
	} else {
		
		$ids = $newids;
		
		$depth++;
		
		makeNetworkFromIds($depth);
	}
}

	

function renderNetwork() {
	
	global $nodes,$edges,$lookup,$no_seeds;
	
	//print_r($nodes); exit;
	
	$nodegdf = "nodedef>name VARCHAR,label VARCHAR,isSeed VARCHAR,seedRank INT,subscriberCount INT,videoCount INT,viewCount INT\n";
	foreach($nodes as $nodeid => $nodedata) {
		$nodegdf .= $nodeid . "," . preg_replace("/,/"," ",$nodedata->snippet->title) . "," . $nodedata->isSeed . "," . $nodedata->seedRank . "," . $nodedata->statistics->subscriberCount . "," . $nodedata->statistics->videoCount . "," . $nodedata->statistics->viewCount . "\n";
	}
	
	$edgegdf = "edgedef>node1 VARCHAR,node2 VARCHAR\n";
	foreach($edges as $edgeid => $edgedata) {
		$tmp = explode("_|_|X|_|_",$edgeid);
		if(isset($nodes[$tmp[0]]) && isset($nodes[$tmp[1]])) {
			$edgegdf .= $tmp[0] . "," . $tmp[1] . "\n";
		}
	}
	
	$gdf = $nodegdf . $edgegdf;
	$filename = "channelnet_seeds" . $no_seeds . "_nodes" . count($nodes) . "_" . date("Y_m_d-H_i_s");

	file_put_contents("./data/".$filename.".gdf", $gdf);
	
	echo '<br /><br />The script has created a net with  '.count($nodes).' channels from '.$no_seeds.' seeds.<br /><br />

	your files:<br />
	<a href="./data/'.$filename.'.gdf">'.$filename.'.gdf</a><br />';

}

?>
</p>

</body>
</html>