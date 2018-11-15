<?php include("html_head.php"); ?>

	<div class="rowTab">
		<div class="sectionTab">
			<h1>Channel Network Module</h1>

		</div>
	</div>

	<div class="rowTab">
		<div class="fullTab">
			<p>This module crawls a network of channels connected via the "related channels" panel, starting from a list of seeds. These data are not retrieved via the API, but scraped from YT's web interface. To reduce queries, this module caches channel data for three days.
				
			<p>Crawl depth specifies how far from the seeds the script should go. Crawl depth 0 will get only the relations between seeds. Using many seeds and the maximum crawl depth (3) can take a very long time or the script might run out of memory or get blocked. Start small.</p>
				
			<p>NB: since graph analysis software can have difficulties with very large numbers, channels' viewcount is given in 100s.</p>
		</div>
	</div>

	<div class="rowTab">
		<div class="sectionTab"><h1>Parameters</h1></div>
	</div>
	
	<form action="mod_channels_related.php" method="post">
	
	<div class="rowTab">
		<div class="sectionTab"><h2>1) choose a starting point:</h2></div>
	</div>

	<div class="rowTab">
		<div class="oneTab"></div>
		<div class="twoTab">Seeds:</div>
		<div class="threeTab">
			<textarea name="seeds"><?php echo $_POST["seeds"]; ?></textarea>
		</div>
		<div class="fourTab">(channel ids, comma separated)</div>
	</div>

	<div class="rowTab">
		<div class="sectionTab"><h2>2) set additional parameters:</h2></div>
	</div>
	
	<div class="rowTab">
		<div class="oneTab"></div>
		<div class="twoTab">Crawl depth:</div>
		<div class="threeTab"><input type="text" name="crawldepth" max="3" value="<?php echo (isset($_POST["crawldepth"])) ? $_POST["crawldepth"]:1; ?>" /></div>
		<div class="fourTab">(values are 0, 1, 2 or 3)</div>
	</div>
	
	<div class="rowTab">
		<div class="oneTab"></div>
		<div class="fourTab"><input type="submit" /></div>
	</div>
	
	</form>

<?php

if(isset($_POST["seeds"])) {

	$crawldepth = $_POST["crawldepth"];
	
	$nodes = array();
	$edges = array();

	echo '<div class="rowTab">
			<div class="sectionTab"><h1>Result</h1></div>
		 </div>
		 <div class="rowTab">Processing:';
	
	if($_POST["crawldepth"] > 4 || preg_match("/\D/", $_POST["crawldepth"])) {
		echo "<br /><br />Wrong crawldepth.";
		exit;
	}
			
	$seeds = $_POST["seeds"];
	
	$seeds = preg_replace("/\s+/","",$seeds);
	$seeds = trim($seeds);
	
	$ids = explode(",",$seeds);
	
	$no_seeds = count($ids);
	
	//print_r($ids); exit;
	makeNetworkFromIds(0);
		
	echo '</div>';
}
	
	
function makeNetworkFromIds($depth) {
	
	global $apikey,$nodes,$edges,$ids,$crawldepth;
	
	echo "<br /><br />getting details for ".count($ids)." channels at depth ".$depth.": ";
	
	$newids = array();
	
	for($i = 0; $i < count($ids); $i++) {
		
		$chid = $ids[$i];
		
		$jsonfn = "./cache/channelinfo_" . $chid . ".json";
		
		if (file_exists($jsonfn) && $delta < (60 * 60 * 24 * 3)) {
		
			$nodes[$chid] = json_decode(file_get_contents($jsonfn));
		
		} else {
			
			$restquery = "https://www.googleapis.com/youtube/v3/channels?part=id,snippet,statistics&id=".$chid."&key=".$apikey;
		
			$reply = doAPIRequest($restquery);
	
			if(isset($reply->items[0])) {
				
				$nodes[$chid] = $reply->items[0];
				
				if($depth == 0) {
					$nodes[$chid]->isSeed = "yes";
					$nodes[$chid]->seedRank = ($i + 1);
				} else {
					$nodes[$chid]->isSeed = "no";
					$nodes[$chid]->seedRank = "";
				}
				
				$html = file_get_contents("https://www.youtube.com/channel/" . $chid);
			
				preg_match_all('/branded-page-related-channels-item.+data-external-id=\"(.*)\">/', $html, $matches, PREG_OFFSET_CAPTURE);	
			
				$nodes[$chid]->matches = $matches[1];
				
				file_put_contents($jsonfn, json_encode($nodes[$chid]));
			}
		}
				
		echo $i . " "; flush(); ob_flush();
	}


	foreach($nodes as $nodeid => $nodedata) {

		//print_r($nodedata->matches);
		//exit;

		if(count($nodedata->matches) > 0) {
				
			foreach($nodedata->matches as $match) {
				
				$featid = $match[0];
								
				if(!isset($nodes[$featid])) {
					
					if(!in_array($featid, $newids)) {
						
						$newids[] = $featid;
					}
					
					if($depth < $crawldepth) {
						$edgeid = $nodeid . "_|_|X|_|_" . $featid;
						$edges[$edgeid] = true;
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
	
	global $nodes,$edges,$lookup,$no_seeds,$mode;

	
	$nodegdf = "nodedef>name VARCHAR,label VARCHAR,isSeed VARCHAR,seedRank INT,subscriberCount INT,videoCount INT,viewCount(100s) INT,country VARCHAR,publishedAt VARCHAR,daysactive INT\n";
	foreach($nodes as $nodeid => $nodedata) {
		
		$nodedata->statistics->viewCount = round($nodedata->statistics->viewCount / 100);
		$nodedata->snippet->country = (isset($nodedata->snippet->country)) ? $nodedata->snippet->country:"not set";
		$daysactive = round((time() - strtotime($nodedata->snippet->publishedAt)) / (60 * 60 * 24));
		
		$nodegdf .= $nodeid . "," . preg_replace("/,|\"|\'/"," ",$nodedata->snippet->title) . "," . $nodedata->isSeed . "," . $nodedata->seedRank . "," . $nodedata->statistics->subscriberCount . "," . $nodedata->statistics->videoCount . "," . $nodedata->statistics->viewCount . "," . $nodedata->snippet->country . "," . $nodedata->snippet->publishedAt . "," . $daysactive .  "\n";
	}
	
	$edgegdf = "edgedef>node1 VARCHAR,node2 VARCHAR,directed BOOLEAN\n";
	foreach($edges as $edgeid => $edgedata) {
		$tmp = explode("_|_|X|_|_",$edgeid);
		if(isset($nodes[$tmp[0]]) && isset($nodes[$tmp[1]])) {
			$edgegdf .= $tmp[0] . "," . $tmp[1] . ",true\n";
		}
	}
	
	$gdf = $nodegdf . $edgegdf;
	$filename = "channelnet_" . $mode . $no_seeds . "_nodes" . count($nodes) . "_" . date("Y_m_d-H_i_s");

	file_put_contents("./data/".$filename.".gdf", $gdf);
	
	echo '<br /><br />The script has created a net with  '.count($nodes).' channels from '.$no_seeds.' seeds.<br /><br />

	your files:<br />
	<a href="./data/'.$filename.'.gdf" download>'.$filename.'.gdf</a><br />';

}

?>

</body>
</html>