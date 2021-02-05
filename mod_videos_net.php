<?php include("html_head.php"); ?>

	<div class="rowTab">
		<div class="sectionTab">
			<h1>Video Network Module</h1>
		</div>
	</div>

	<div class="rowTab">
		<div class="fullTab">
			<p>This module creates a network of relations between videos, starting from a search or a list of video ids. It will also generate a network of channels based on the same relations. (If a video from one channel points to the video of another channel, an edge is created and the more often that happens, the more weight the connection gets.)</p>
			
			<p>It retrieves "related videos" from the <a href="https://developers.google.com/youtube/v3/docs/search/list#relatedToVideoId" target="_blank">search/list#relatedToVideoId</a> API endpoint and creates a graph file in GDF format.</p>
			
			<p>Crawl depth specifies how far from the seeds the script should go. Crawl depth 0 will get only the relations between seeds. Using many seeds and the maximum crawl depth (2) can take a very long time or the script will very probably run out of memory. Start small.</p>
		</div>
	</div>

	<div class="rowTab">
		<div class="sectionTab"><h1>Parameters</h1></div>
	</div>
	
	<form action="mod_videos_net.php" method="post">
	
	<div class="rowTab">
		<div class="sectionTab"><h2>1) choose a starting point:</h2></div>
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
		<div class="sectionTab"><h2>2) set additional parameters:</h2></div>
	</div>

	<div class="rowTab">
		<div class="oneTab"></div>
		<div class="twoTab">Crawl depth:</div>
		<div class="threeTab"><input type="text" name="crawldepth" max="2" value="<?php echo (isset($_POST["crawldepth"])) ? $_POST["crawldepth"]:1; ?>" /></div>
		<div class="fourTab">(values are 0, 1 or 2)</div>
	</div>
	
	<div class="g-recaptcha" data-sitekey="6Lf093MUAAAAAIRLVzHqfIq9oZcOnX66Dju7e8sr"></div>
	
	<div class="rowTab">
		<div class="oneTab"></div>
		<div class="fourTab"><input type="submit" /></div>
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
			exit;
		}
		testcaptcha($_POST["g-recaptcha-response"]);
	}

	$mode = $_POST["mode"];
	$crawldepth = $_POST["crawldepth"];
	$nodes = array();
	$edges = array();
	
	if($_POST["crawldepth"] > 2 || preg_match("/\D/", $crawldepth)) {
		echo "<br /><br />Wrong crawldepth.";
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
		
		$ids = explode(",",$seeds);
		
		$no_seeds = count($ids);
		
		//print_r($ids); exit;
		makeNetworkFromIds(0);
		
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
	
	return $ids;
}
	
	
function makeNetworkFromIds($depth) {
	
	global $nodes,$edges,$ids,$crawldepth;
	
	echo "<br /><br />getting details for ".count($ids)." videos at depth ".$depth.": ";
	
	$newids = array();
	$categoryIds = array();
	
	for($i = 0; $i < count($ids); $i++) {
		
		$vid = $ids[$i];
		
		$restquery = "https://www.googleapis.com/youtube/v3/videos?part=statistics,contentDetails,snippet&id=".$vid;
		
		$reply = doAPIRequest($restquery);

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
			$row["dislikeLikeRatio"] = (isset($video->statistics->likeCount) && isset($video->statistics->dislikeCount) && $video->statistics->likeCount > 0) ? $video->statistics->dislikeCount / $video->statistics->likeCount:"";
			$row["likeCount"] = (isset($video->statistics->likeCount)) ? $video->statistics->likeCount:"";
			$row["dislikeCount"] = (isset($video->statistics->dislikeCount)) ? $video->statistics->dislikeCount:"";
			$row["favoriteCount"] = $video->statistics->favoriteCount;
			$row["commentCount"] = (isset($video->statistics->commentCount)) ? $video->statistics->commentCount:"";

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
	$restquery = "https://www.googleapis.com/youtube/v3/videoCategories?part=snippet&id=".urlencode(implode(",", $categoryIds));

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
		
		$restquery = "https://www.googleapis.com/youtube/v3/search?part=id&maxResults=50&relatedToVideoId=".$vid."&type=video";

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

		/*
		while($run == true) {
	
			$restquery = "https://www.googleapis.com/youtube/v3/search?part=id&maxResults=50&relatedToVideoId=".$vid."&type=video";
			
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
		*/
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
	
	global $nodes,$edges,$lookup,$no_seeds,$mode,$folder;
	
	// to generate channel network
	$ch_nodes = array();
	$ch_edges = array();
	
	
	// generate related video network and extract data for related channel network
	$nodegdf = "nodedef>name VARCHAR,label VARCHAR,isSeed VARCHAR,seedRank INT,publishedAt INT,channelTitle VARCHAR,channelId VARCHAR,videoCategoryLabel VARCHAR,viewCount INT,likeCount INT,dislikeCount INT,dislikeLikeRatio FLOAT,favoriteCount INT,commentCount INT\n";
	foreach($nodes as $nodeid => $nodedata) {

		$nodegdf .= $nodeid . "," . preg_replace("/,|\"|\'/"," ",$nodedata["videoTitle"]) . "," . $nodedata["isSeed"] . "," . $nodedata["seedRank"] . "," . $nodedata["publishedAt"] . "," . preg_replace("/,|\"|\'/"," ",$nodedata["channelTitle"]) . "," . $nodedata["channelId"] . "," . preg_replace("/,|\"|\'/"," ",$nodedata["videoCategoryLabel"]) . "," .$nodedata["viewCount"] . "," . $nodedata["likeCount"] . "," . $nodedata["dislikeCount"] . "," . $nodedata["dislikeLikeRatio"] . "," . $nodedata["favoriteCount"] . "," . $nodedata["commentCount"] . "," . "\n";

		if(!isset($ch_nodes[$nodedata["channelId"]])) {
			$tmpnode = array();
			$tmpnode["channelId"] = $nodedata["channelId"];
			$tmpnode["channelTitle"] = $nodedata["channelTitle"];
			$ch_nodes[$nodedata["channelId"]] = $tmpnode;
		}
	}
	
	
	$edgegdf = "edgedef>node1 VARCHAR,node2 VARCHAR,directed BOOLEAN\n";
	foreach($edges as $edgeid => $edgedata) {

		$tmp = explode("_|_|X|_|_",$edgeid);

		if(isset($nodes[$tmp[0]]) && isset($nodes[$tmp[1]])) {
			
			$edgegdf .= $tmp[0] . "," . $tmp[1] . ",true\n";
			
			$tmpedge = $nodes[$tmp[0]]["channelId"] . "_|_|X|_|_" . $nodes[$tmp[1]]["channelId"];
			if(!isset($ch_edges[$tmpedge])) {
				$ch_edges[$tmpedge] = 0;
			}
			$ch_edges[$tmpedge]++;
		}
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
			
			$ch_edgegdf .= $tmp[0] . "," . $tmp[1] . "," . $edgedata . ",true\n";
		
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

	your files:<br />
	<a href="'.$folder.$filename.'.gdf" download>'.$filename.'.gdf</a> (the related video network)<br />
	<a href="'.$folder.$ch_filename.'.gdf" download>'.$ch_filename.'.gdf</a> (the extracted channel network)<br />';
}

?>
</p>

</body>
</html>
