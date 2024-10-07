<?php include("html_head.php"); ?>

	<div class="rowTab">
		<div class="sectionTab">
			<h1>Video Network Module</h1> 
		</div>
	</div>

	<div class="rowTab">
		<div class="fullTab">
			<p> This module creates a network of videos, based on the concept of co-commenting. If a user comments on two videos, a link is made between these two videos
				and the more users co-comment, the stronger the link. Up to 1000 top-level comments are taken into account, ranked by relevance.
				Please note that the channel owner is NOT taken into account when establishing connections.
			</p>
			
			<p>The videos in the network are selected either through search or by providing a list of video ids.</p>

			<p>Check the documentation for the <a href="https://developers.google.com/youtube/v3/docs/videos/list" target="_blank">video/list</a> (used to get the info for each video), 
			<a href="https://developers.google.com/youtube/v3/docs/search/list" target="_blank">search/list</a> (used for the search function), and
			<a href="https://developers.google.com/youtube/v3/docs/commentThreads/list" target="_blank">commentThreads/list</a> (to retrieve comments)
			API endpoints for additional information.</p>
		</div>
	</div>

	<div class="rowTab">
		<div class="sectionTab"><h1>Parameters</h1></div>
	</div>
	
	<form action="mod_videos_comments_net.php" method="post">
	
	<div class="rowTab">
		<div class="sectionTab"><h2>Choose a starting point:</h2></div>
	</div>

	<div class="rowTab">
		<div class="oneTab"><input type="radio" name="mode" value="search" <?php if($_POST["mode"] != "seeds") { echo "checked"; } ?> /></div>
		<div class="twoTab">Search query:</div>
		<div class="threeTab">
			<input type="text" name="query" value="<?php if(isset($_POST["query"])) { echo $_POST["query"]; } ?>" />
		</div>
		<div class="fourTab">
			(this is passed to the search endpoint)
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
		<div class="twoTab">Rank by:</div>
		<div class="fourTab">
			<select name="rankby">
					<option value="relevance" <?php if($_POST["rankby"] == "relevance") { echo "selected"; } ?>>relevance - Resources are sorted based on their relevance to the search query</option>
					<option value="date" <?php if($_POST["rankby"] == "date") { echo "selected"; } ?>>date – Resources are sorted in reverse chronological order based on the date they were created</option>
					<option value="rating" <?php if($_POST["rankby"] == "rating") { echo "selected"; } ?>>rating – Resources are sorted from highest to lowest rating</option>
					<option value="title" <?php if($_POST["rankby"] == "title") { echo "selected"; } ?>>title – Resources are sorted alphabetically by title</option>
					<option value="viewCount" <?php if($_POST["rankby"] == "viewCount") { echo "selected"; } ?>>viewCount - Resources are sorted from highest to lowest number of views</option>
			</select>
		</div>
	</div>
	
	<div class="rowTab">
		<div class="oneTab"></div>
		<div class="twoTab">Published:</div>
		<div class="fourTab">
			<input type="checkbox" name="timeframe" <?php if(isset($_POST["timeframe"])) { echo "checked"; } ?> /> limit search to videos published in a specific timeframe (format: yyyy-mm-ddThh:mm:ssZ - timezone: UTC):
			<p>after: <input type="text" name="date_after" value="<?php echo (isset($_POST["date_after"])) ? $_POST["date_after"]:"1970-01-01T00:00:00Z"; ?>" />&nbsp;&nbsp;&nbsp;</p>
			<p>before: <input type="text" name="date_before" value="<?php echo (isset($_POST["date_before"])) ? $_POST["date_before"]:"1970-01-01T00:00:00Z"; ?>" /></p>
		</div>
	</div>
	
	<div class="rowTab">
		<div class="sectionTab"><hr /></div>
	</div>

	<div class="rowTab">
		<div class="oneTab"><input type="radio" name="mode" value="seeds" <?php if($_POST["mode"] == "seeds") { echo "checked"; } ?> /></div>
		<div class="twoTab">Seeds:</div>
		<div class="threeTab">
			<textarea name="seeds"><?php if($_POST["mode"] == "seeds") { echo $_POST["seeds"]; } ?></textarea>
		</div>
		<div class="fourTab">(video ids, comma separated)</div>
	</div>


	<div class="rowTab">
		<div class="sectionTab"><h2>Additional parameters:</h2></div>
	</div>


	<div class="rowTab">
		<div class="oneTab"></div>
		<div class="twoTab">Comments:</div>
		<div class="threeTab"><input type="text" name="maxcomments" max="2" value="<?php echo (isset($_POST["maxcomments"])) ? $_POST["maxcomments"]:100; ?>" /></div>
		<div class="fourTab">(how many top level comments to retrieve for each video, max is 1000)</div>
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

<p>
<?php

$folder = $datafolder;

// allow for direct URL parameters and command line for cron
if(isset($argv)) {
	parse_str(implode('&', array_slice($argv, 1)), $_GET);
	$folder = $cronfolder;
}
if(isset($_GET["mode"])) { $_POST = $_GET; }

if(isset($_POST["query"]) || isset($_POST["seeds"])) {

	echo '<div class="rowTab">
			<div class="sectionTab"><h1>Results</h1></div>
		 </div>
		 <div class="rowTab">Processing:';

	
	if(RECAPTCHA) { 
		if($_POST["g-recaptcha-response"] == "") {
			echo "<br /><br />Recaptcha missing.";
			//exit;
		}
		testcaptcha($_POST["g-recaptcha-response"]);
	}

	$mode = $_POST["mode"];
	$maxcomments = $_POST["maxcomments"];
	$nodes = array();
	$edges = array();

	if($_POST["maxcomments"] > 1000 || preg_match("/\D/", $maxcomments)) {
		echo "<br /><br />Wrong number of related videos.";
		exit;
	}

	if($mode == "search") {
		
		if($_POST["query"] == "") {
			echo "<br /><br />Missing query.";
			exit;
		}
		
		if($_POST["iterations"] > 10 || preg_match("/\D/", $_POST["iterations"])) {
			echo "<br /><br />Wrong iteration parameter.";
			exit;
		}
		
		$query = $_POST["query"];
		$language = $_POST["language"];
		$regioncode = $_POST["regioncode"];
		$iterations = $_POST["iterations"];
		$rankby = $_POST["rankby"];
		$date_before = $date_after = false;
		if(isset($_POST["timeframe"])) {
			$date_before = $_POST["date_before"];
			$date_after = $_POST["date_after"];
		}
		
		$ids = getIdsFromSearch($query,$iterations,$rankby,$language,$regioncode,$date_before,$date_after);
		$seeds = $ids;
		
		$no_seeds = count($ids);
		
		//print_r($ids); exit;
		makeNetworkFromIds(0);
		
	} else if($mode == "seeds") {
		
		if($_POST["seeds"] == "") {
			echo "<br /><br />Missing seed ids.";
			exit;
		}
		
		$seeds = $_POST["seeds"];
		
		$seeds = preg_replace("/\s+/","",$seeds);
		$seeds = trim($seeds);
		
		$ids = array_values(array_unique(explode(",",$seeds)));
		$seeds = $ids;
		
		$no_seeds = count($ids);
		
		makeNetworkFromIds();
		
	} else {
		
		echo "<br /><br />You need to select a mode.";
	}
	
	echo '</div>';
}


function getIdsFromSearch($query,$iterations,$rankby,$language,$regioncode,$date_before,$date_after) {
	
	$nextpagetoken = null;
	$ids = array();

	for($i = 0; $i < $iterations; $i++) {
		
		$restquery = "https://www.googleapis.com/youtube/v3/search?part=snippet&maxResults=50&q=".urlencode($query)."&type=video&order=".$rankby;
		if($date_before != false) {
			$restquery .= "&publishedAfter=".$date_after."&publishedBefore=".$date_before;
		}
		
		if($language != "") { $restquery .= "&relevanceLanguage=" . $language; }
		if($regioncode != "") { $restquery .= "&regionCode=" . $regioncode; }
		
		if($nextpagetoken != null) {
			$restquery .= "&pageToken=".$nextpagetoken;
		}
		
		$reply = doAPIRequest($restquery);

		$nextpagetoken = $reply->nextPageToken;
		
		foreach($reply->items as $item) {
			$ids[] = $item->id->videoId;
		}
	}
	
	return array_values(array_unique($ids));
}
	
	
function makeNetworkFromIds() {
	
	global $nodes,$edges,$ids,$maxcomments;
	
	echo "<br /><br />getting comments for ".count($ids)." videos: ";
	
	$categoryIds = array();

	// iterate over video ids to get comment authors and video infos
	for($i = 0; $i < count($ids); $i++) {
		
		$videohash = $ids[$i];

		// get video info
		$restquery = "https://www.googleapis.com/youtube/v3/videos?part=statistics,contentDetails,snippet&id=".$videohash;
		
		$reply = doAPIRequest($restquery);
		//print_r($reply);

		if(isset($reply->items[0])) {

			$video = $reply->items[0];

			// collect categories
			if(!in_array($video->snippet->categoryId,$categoryIds)) {
				$categoryIds[] = $video->snippet->categoryId;
			}

			$row = array();
			$row["channelId"] = $video->snippet->channelId;
			$row["channelTitle"] = preg_replace("/\s+/", " ",$video->snippet->channelTitle);
			$row["videoId"] = $video->id;
			$row["publishedAtUnix"] = strtotime($video->snippet->publishedAt);
			$row["publishedAtSQL"] = date("Y-m-d H:m:s", $row["publishedAtUnix"]);
			$row["videoTitle"] = preg_replace("/\s+/", " ",$video->snippet->title);
			$row["videoDescription"] = preg_replace("/\s+/", " ",$video->snippet->description);
			$row["videoCategoryId"] = $video->snippet->categoryId;
			$row["videoCategoryLabel"] = "";
			$row["duration"] = $video->contentDetails->duration;
			$row["dimension"] = $video->contentDetails->dimension;
			$row["definition"] = $video->contentDetails->definition;
			$row["caption"] = $video->contentDetails->caption;
			$row["defaultLanguage"] = $video->snippet->defaultLanguage;
			$row["defaultAudioLanguage"] = $video->snippet->defaultAudioLanguage;
			$row["licensedContent"] = $video->contentDetails->licensedContent;
			$row["viewCount"] = $video->statistics->viewCount;
			$row["likeCount"] = (isset($video->statistics->likeCount)) ? $video->statistics->likeCount:"";
			$row["commentCount"] = (isset($video->statistics->commentCount)) ? $video->statistics->commentCount:"";
		}

		// get comments
		//$restquery = "https://www.googleapis.com/youtube/v3/commentThreads?part=snippet&maxResults=100&order=relevance&videoId=".$videohash;
	
		//$reply = doAPIRequest($restquery);
		//print_r($reply);

		$nextpagetoken = null;
		$run = true;
		$counter = 0;
		$comments = array();
	
		while($run == true) {

			$restquery = "https://www.googleapis.com/youtube/v3/commentThreads?part=snippet&maxResults=100&order=relevance&videoId=".$videohash;
	
			if($nextpagetoken != null) {
				$restquery .= "&pageToken=".$nextpagetoken;
			}
			
			$reply = doAPIRequest($restquery);


			$row["commentsDisabled"] = 0; 
			if(isset($reply->error)) {
				if($reply->error->errors[0]->reason == "commentsDisabled") {
					$row["commentsDisabled"] = 1;
				}
			}
	
			foreach($reply->items as $item) {
				if($counter < $maxcomments) {
					$comments[] = $item;
				}
				$counter++;
			}
			
			if(isset($reply->nextPageToken) && $reply->nextPageToken != "" && count($comments) < $maxcomments) {
				$nextpagetoken = $reply->nextPageToken;				
			} else {
				$run = false;
			}
		}	

		$authorids = array();
		if(isset($reply->items[0])) {

			foreach ($comments as $comment) {
				$authorid = $comment->snippet->topLevelComment->snippet->authorChannelId->value;
				//$authorid = $comment->snippet->topLevelComment->snippet->authorDisplayName;

				if($authorid != $row["channelId"]) {
					if(!isset($authorids[$authorid])) {
						$authorids[$authorid] = 0;
					}
					$authorids[$authorid]++;
				}
			}
		}

		$row["authorids"] = $authorids;
		$nodes[] = $row;

		echo $i . " "; flush(); ob_flush();
	}


	// get category labels and assign to videos
	$restquery = "https://www.googleapis.com/youtube/v3/videoCategories?part=snippet&id=".urlencode(implode(",", $categoryIds));

	$reply = doAPIRequest($restquery);

	$categoryTrans = array();
	foreach($reply->items as $cat) {
		$categoryTrans[$cat->id] = $cat->snippet->title;
	}
		
	foreach ($nodes as $key => $node) {
		$nodes[$key]["videoCategoryLabel"] = $categoryTrans[$node["videoCategoryId"]];
	}
	

	// determine network connections
	for($i = 0; $i < count($nodes)-1; $i++) {
		
		for($j = $i+1; $j < count($nodes); $j++) {
		
			$intersect = array_intersect(array_keys($nodes[$i]["authorids"]), array_keys($nodes[$j]["authorids"]));

			//print_r($intersect);

			if(count($intersect) > 0) {
				$edgeid = $nodes[$i]["videoId"] . "_|_|X|_|_" . $nodes[$j]["videoId"];
				$edges[$edgeid] = count($intersect);
			}			
		}
	}

	renderNetwork();
}

	

function renderNetwork() {
	
	global $nodes,$edges,$seeds,$no_seeds,$mode,$folder;

	// to generate channel network
	$ch_nodes = array();
	$ch_edges = array();
	$ch_link = array();

	
	// generate related video network and extract data for related channel network
	$nodegdf = "nodedef>name VARCHAR,label VARCHAR,seedRank INT,publishedAtUnix INT,publishedAtSQL VARCHAR,channelTitle VARCHAR,channelId VARCHAR,videoCategoryLabel VARCHAR,defaultLanguage VARCHAR,defaultAudioLanguage VARCHAR,viewCount INT,likeCount INT,commentCount INT,commentsDisabled VARCHAR\n";
	foreach($nodes as  $nodedata) {

		//print_r($nodedata);

		$nodegdf .= $nodedata["videoId"] . "," . preg_replace("/,|\"|\'/"," ",$nodedata["videoTitle"]) . "," . $nodedata["seedRank"] . "," . $nodedata["publishedAtUnix"] . "," . $nodedata["publishedAtSQL"] . "," .  preg_replace("/,|\"|\'/"," ",$nodedata["channelTitle"]) . "," . $nodedata["channelId"] . "," . preg_replace("/,|\"|\'/"," ",$nodedata["videoCategoryLabel"]) . "," .$nodedata["defaultLanguage"]. "," .$nodedata["defaultAudioLanguage"]. "," .$nodedata["viewCount"] . "," . $nodedata["likeCount"] . "," . $nodedata["commentCount"] . "," . $nodedata["commentsDisabled"] . "\n";

		$ch_link[$nodedata["videoId"]] = $nodedata["channelId"];
		if(!isset($ch_nodes[$nodedata["channelId"]])) {
			$tmpnode = array();
			$tmpnode["channelId"] = $nodedata["channelId"];
			$tmpnode["channelTitle"] = $nodedata["channelTitle"];
			$ch_nodes[$nodedata["channelId"]] = $tmpnode;
		}
	}
	
	
	$edgegdf = "edgedef>node1 VARCHAR,node2 VARCHAR,weight INT,directed BOOLEAN\n";
	foreach($edges as $edgeid => $edgedata) {

		$tmp = explode("_|_|X|_|_",$edgeid);
			
		$edgegdf .= $tmp[0] . "," . $tmp[1] . "," . $edgedata .  ",false\n";
		
		$tmpedge = $ch_link[$tmp[0]] . "_|_|X|_|_" . $ch_link[$tmp[1]];
		if(!isset($ch_edges[$tmpedge])) {
			$ch_edges[$tmpedge] = 0;
		}
		$ch_edges[$tmpedge]++;
	}
	
	// generate related channel network
	$ch_nodegdf = "nodedef>name VARCHAR,label VARCHAR\n";
	foreach($ch_nodes as $nodeid => $nodedata) {

		$ch_nodegdf .= $nodeid . "," . preg_replace("/,|\"|\'/"," ",$nodedata["channelTitle"]) . "\n";
	}
	
	$ch_edgegdf = "edgedef>node1 VARCHAR,node2 VARCHAR,weight INT,directed BOOLEAN\n";
	foreach($ch_edges as $edgeid => $edgedata) {

		$tmp = explode("_|_|X|_|_",$edgeid);

		if(isset($ch_nodes[$tmp[0]]) && isset($ch_nodes[$tmp[1]])) {
			
			$ch_edgegdf .= $tmp[0] . "," . $tmp[1] . "," . $edgedata . ",false\n";
		
		}
	}

	$gdf = $nodegdf . $edgegdf;
	$ch_gdf = $ch_nodegdf . $ch_edgegdf;
	
	
	$filename = "videonet_" . $mode . $no_seeds . "_nodes" . count($nodes) . "_" . date("Y_m_d-H_i_s");
	$ch_filename = "videonet_channels_" . $mode . $no_seeds . "_nodes" . count($nodes) . "_" . date("Y_m_d-H_i_s");
	if(isset($_POST["filename"])) {
		$filename = $_POST["filename"] . "_" . $filename;
		$ch_filename = $_POST["filename"] . "_" . $ch_filename;
	}

	writefile($folder.$filename.".gdf", $gdf);
	writefile($folder.$ch_filename.".gdf", $ch_gdf);
	
	echo '<br /><br />The script has created a net with  '.count($nodes).' videos from '.$no_seeds.' seeds.<br /><br />

	Your files:<br />
	<a href="'.$folder.$filename.'.gdf" download>'.$filename.'.gdf</a> (the related video network)<br />
	<a href="'.$folder.$ch_filename.'.gdf" download>'.$ch_filename.'.gdf</a> (the extracted channel network)<br />';
}

?>
</p>

</body>
</html>