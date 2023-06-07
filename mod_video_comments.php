<?php include("html_head.php"); ?>

	<div class="rowTab">
		<div class="sectionTab">
			<h1>Video Info and Comments Module</h1>
		</div>
	</div>

	<div class="rowTab">
		<div class="fullTab">
			<p>This module starts from a video id and retrieves basic info for the video in question and provides a number of analyses of the comment section.
			Comments are retrieved via the <a href="https://developers.google.com/youtube/v3/docs/commentThreads/list" target="_blank">commentThreads/list</a> API endpoint.</p>
				
			<p>The number of comments the script is able to retrieve can vary wildly. In some cases, only a relatively small percentage is made available, while in others well over
			100.000 comments have been successfully retrieved. This seems to be mainly related to the age of the video in question.</p>
			
			<p>The module creates the following outputs:
				<ul> 
					<li>a tabular file containing basic info and statistics about the video;</li>
					<li>a tabular file containing all retrievable comments, both top level and replies;</li>
					<li>a tabular file containing comment authors and their comment count;</li>
					<li>a network file (gdf format) that maps interactions between users in the comment section;</li>
				</ul>
			</p>
			
			<p>The first three elements can be shown directly in the browser by enabling HTML output.</p>
		</div>
	</div>

	<div class="rowTab">
		<div class="sectionTab"><h1>Parameters</h1></div>
	</div>
	
	<form action="mod_video_comments.php" method="get">
		
	<div class="rowTab">
		<div class="sectionTab"><h2>Video selection and comment cutoff:</h2></div>
	</div>

	<div class="rowTab">
		<div class="leftTab">Video id:</div>
		<div class="rightTab">
			<input type="text" name="videohash" value="<?php if(isset($_GET["videohash"])) { echo $_GET["videohash"]; } ?>" /> (video ids can be found in URLs, e.g. <span class="grey">https://www.youtube.com/watch?v=</span><b>aXnaHh40xnM</b>)
		</div>
	</div>

	<div class="rowTab">
		<div class="leftTab">Limit to:</div>
		<div class="rightTab">
			<input type="text" name="toplimit" value="<?php if(isset($_GET["toplimit"])) { echo $_GET["toplimit"]; } ?>" /> top level comments (ranked by relevance, leave empty for no limit)</b>
		</div>
	</div>

	<div class="rowTab">
		<div class="sectionTab"><h2>Output option:</h2></div>
	</div>

	<div class="rowTab">
		<div class="leftTab">HTML output:</div>
		<div class="rightTab">
			<input type="checkbox" name="htmloutput" <?php if($_GET["htmloutput"] == "on") { echo "checked"; } ?> /> (displays HTML result tables in addition to file exports)
		</div>
	</div>

	<div class="rowTab">
		<div class="leftTab">File format:</div>
		<div class="rightTab">
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

// blocked video example: https://www.youtube.com/watch?v=pLN59ZOweUE

$folder = $datafolder;

// allow for direct URL parameters and command line for cron
// e.g. php mod_video_info.php videohash=aXnaHh40xnM or php mod_video_info.php videolist=videolist_xy.tab (file must be in cronfolder)
// don't forget to set $cronfolder in config.php
if(isset($argv)) {
	parse_str(implode('&', array_slice($argv, 1)), $_GET);
	$folder = $cronfolder;
}

$feed = array();
$feed["comments"] = array();

$video = array();


if(isset($_GET["videolist"])) {
	
	$filename = "./".$folder."/".$_GET["videolist"];
		
	$header = NULL;
	$videolist = array();
	if(($handle = fopen($filename, 'r')) !== FALSE) {
		while (($row = fgetcsv($handle,0,"\t",chr(8))) !== FALSE) {
            if(!$header)
				$header = $row;
            else
                $videolist[] = array_combine($header, $row);
        }
        fclose($handle);
    } else {
	    echo "no list file found"; exit;
    }
	
	foreach($videolist as $video) {
		$filename = "videoinfo_".$video["channelId"]."_".$video["videoId"]."_".date("Y_m_d-H_i_s");
		getComments($video["videoId"]);
	}
	
	
} else if(isset($_GET["videohash"])) {

	echo '<div class="rowTab">
			<div class="sectionTab"><h1>Results</h1></div>
		 </div>
		 <div class="rowTab">';
	
	if(RECAPTCHA) {
		if($_GET["g-recaptcha-response"] == "") {
			echo "Recaptcha missing.";
			exit;
		}
		testcaptcha($_GET["g-recaptcha-response"]);
	}
		 
	if($_GET["videohash"] == "") {
		echo "Missing video id.";
		exit;
	}

	if(preg_match("/\D/", $_GET["toplimit"])) {
		echo "<br /><br />Wrong comment limit.";
		exit;
	}

	echo 'Processing:';

	$videohash = $_GET["videohash"];
	$html = $_GET["htmloutput"];
	$commentonly = $_GET["commentonly"];
	$output = $_GET["output"];
	$filename = "videoinfo_".$videohash."_".date("Y_m_d-H_i_s");

	$toplimit = $_GET["toplimit"];
	if($toplimit == "") {
		$toplimit = 0;
	}

	$video = getInfo($videohash);
	$nodecomments = getComments($videohash,$toplimit);
	$commenters = getCommenters($nodecomments);
	makeNetwork($nodecomments);
	
	echo '<br /><br />The following files have been generated:<br />';
	echo '<a href="./data/'.$filename.'_basicinfo.'.$output.'" download>'.$filename.'_basicinfo.'.$output.'</a><br />';
	echo '<a href="./data/'.$filename.'_comments.'.$output.'" download>'.$filename.'_comments.'.$output.'</a><br />';
	echo '<a href="./data/'.$filename.'_authors.'.$output.'" download>'.$filename.'_authors.'.$output.'</a><br />';
	echo '<a href="./data/'.$filename.'_commentnetwork.gdf" download>'.$filename.'_commentnetwork.gdf</a><br />';
	echo '<br />';
	

	if($html == "on") {
	
		echo "<hr /><br />";
	
		// output basic video info table
		echo '<table class="resulttable">';
		foreach($video as $key => $data) {
			echo '<tr class="resulttable">';
			echo '<td class="resulttableHi"><b>'.$key.'</b></td>';
			echo '<td class="resulttable">'.$data.'</td>';
			echo '</tr>';
		}
		echo '</table>';

		echo "<br /><br />";
	
	
		// output author list
		echo '<table class="resulttable">';
		foreach($commenters as $username => $count) {
			echo '<tr class="resulttable">';
			echo '<td class="resulttableHi"><b>'.$username.'</b></td>';
			echo '<td class="resulttable">'.$count.'</td>';
			echo '</tr>';
		}
		echo '</table>';

		echo "<br /><br />";
	
	
		// output full comment table
		echo '<table class="resulttable">';
		echo '<tr class="resulttable">';
		foreach(array_keys($nodecomments[0]) as $key) {
			echo '<td class="resulttableHi"><b>'.$key.'</b></td>';
		}		
		echo '</tr>';
		foreach($nodecomments as $comment) {
			$style = ($comment["isReply"] == 0) ? "resulttable":"resulttableHi";

			echo '<tr class="resulttable">';
			foreach($comment as $element) {
				echo '<td class="'.$style.'">'.$element.'</td>';
			}	
			echo '</tr>';
		}
		echo '</tr>';
		echo '</table>';
		
	}
	
	echo '</div>';
}


function getInfo($videohash) {

	global $html,$filename,$folder,$output;

	// forbidden: fileDetails,processingDetails,suggestions
	$restquery = "https://www.googleapis.com/youtube/v3/videos?part=statistics,contentDetails,snippet,status,topicDetails&id=".$videohash;

	$reply = doAPIRequest($restquery);
	if(count($reply->items) == 0) {
		echo "<br /><br />No results found. You are probably not using a valid video id."; exit;
	}
	$reply = $reply->items[0];
	$video = array();

	$video["id"] = $reply->id;
	
	$video["published"] = date("Y-m-d H:i:s", strtotime($reply->snippet->publishedAt));
	$video["published_unix"] = strtotime($reply->snippet->publishedAt);
	$video["title"] = preg_replace("/\s+/", " ",$reply->snippet->title);
	$video["description"] = preg_replace("/\s+/", " ",$reply->snippet->description);
	$video["channelId"] = $reply->snippet->channelId;
	$video["channelTitle"] = $reply->snippet->channelTitle;
	
	$video["duration"] = $reply->contentDetails->duration;
    $video["dimension"] = $reply->contentDetails->dimension;
    $video["definition"] = $reply->contentDetails->definition;
    $video["caption"] = $reply->contentDetails->caption;
    $video["allowedIn"] = (isset($reply->contentDetails->regionRestriction->allowed)) ? implode(",",$reply->contentDetails->regionRestriction->allowed):"";
    $video["blockedIn"] = (isset($reply->contentDetails->regionRestriction->blocked)) ? implode(",",$reply->contentDetails->regionRestriction->blocked):"";
    
    $video["licensedContent"] = $reply->contentDetails->licensedContent;
    $video["viewCount"] = $reply->statistics->viewCount;
    $video["likeCount"] = $reply->statistics->likeCount;
    $video["dislikeCount"] = $reply->statistics->dislikeCount;
    $video["favoriteCount"] = $reply->statistics->favoriteCount;
    $video["commentCount"] = $reply->statistics->commentCount;
    
    $video["uploadStatus"] = $reply->status->uploadStatus;
    $video["privacyStatus"] = $reply->status->privacyStatus;
    $video["license"] = $reply->status->license;
    $video["embeddable"] = $reply->status->embeddable;
    $video["publicStatsViewable"] = $reply->status->publicStatsViewable;

	
	$fp = fopen("./".$folder.$filename."_basicinfo.".$output, 'w');
	$separator = ($output == "tab") ? "\t":",";

	foreach($video as $key => $data) {
		fputcsv($fp, array($key,$data), $separator);
	}

	fclose($fp);
	
	return $video;
}


function getComments($videohash,$toplimit) {
	
	global $html,$filename,$folder,$output;

	
	// get toplevel comments first

	$nextpagetoken = null;
	$run = true;
	$counter = 0;
	$comments = array();
	
	echo "<br /><br />Getting comments: "; flush(); ob_flush();

	while($run == true) {

		if($toplimit > 0) {
			$restquery = "https://www.googleapis.com/youtube/v3/commentThreads?part=snippet&maxResults=100&order=relevance&videoId=".$videohash;
		} else {
			$restquery = "https://www.googleapis.com/youtube/v3/commentThreads?part=snippet&maxResults=100&videoId=".$videohash;
		}

		if($nextpagetoken != null) {
			$restquery .= "&pageToken=".$nextpagetoken;
		}
		
		$reply = doAPIRequest($restquery);

		foreach($reply->items as $item) {
			if($counter < $toplimit || $toplimit == 0) {
				$comments[] = $item;
			}
			$counter++;
		}

		echo " " . count($comments); flush(); ob_flush();
		
		if(isset($reply->nextPageToken) && $reply->nextPageToken != "" && ($toplimit == 0 || count($comments) < $toplimit)) {
			$nextpagetoken = $reply->nextPageToken;				
		} else {
			$run = false;
		}
	}	
	
	
	// work through top level comments and get replies
	
	$nodecomments = array();
	$counter = 0;
	
	echo "<br /><br/>Digging into thread structure: "; flush(); ob_flush();
	
	foreach($comments as $comment) {
		
		echo " " . $counter; flush(); ob_flush();
		$counter++;
		
		$tmp = array();
		$tmp["id"] = $comment->id;
		$tmp["replyCount"] = $comment->snippet->totalReplyCount;
		$tmp["likeCount"] = $comment->snippet->topLevelComment->snippet->likeCount;
		$tmp["publishedAt"] = date("Y-m-d H:i:s", strtotime($comment->snippet->topLevelComment->snippet->publishedAt));
		$tmp["authorName"] = preg_replace("/\s+/", " ",$comment->snippet->topLevelComment->snippet->authorDisplayName);
		$tmp["text"] = preg_replace("/\s+/", " ",$comment->snippet->topLevelComment->snippet->textDisplay);
		$tmp["authorChannelId"] = $comment->snippet->topLevelComment->snippet->authorChannelId->value;
		$tmp["authorChannelUrl"] = $comment->snippet->topLevelComment->snippet->authorChannelUrl;
		$tmp["isReply"] = 0;
		$tmp["isReplyTo"] = "";
		$tmp["isReplyToName"] = "";
		
		//print_r($tmp);
		
		$nodecomments[] = $tmp;
		
		if($tmp["replyCount"] > 0) {
			
			$replies = array();
			$nextpagetoken = null;
			$run = true;
		
			while($run == true) {
				
				$restquery = "https://www.googleapis.com/youtube/v3/comments?part=snippet&textFormat=plainText&maxResults=100&parentId=".$tmp["id"];
				
				if($nextpagetoken != null) {
					$restquery .= "&pageToken=".$nextpagetoken;
				}
				
				$reply = doAPIRequest($restquery);
			
				foreach($reply->items as $item) {
					$replies[] = $item;
				}
								
				if(isset($reply->nextPageToken) && $reply->nextPageToken != "") {
					$nextpagetoken = $reply->nextPageToken;				
				} else {
					$run = false;
				}
			}
			
			foreach($replies as $reply) {
				
				$tmp2 = array();
				$tmp2["id"] = $reply->id;
				$tmp2["replyCount"] = "";
				$tmp2["likeCount"] = $reply->snippet->likeCount;
				$tmp2["publishedAt"] = date("Y-m-d H:i:s", strtotime($reply->snippet->publishedAt));
				$tmp2["authorName"] = preg_replace("/\s+/", " ",$reply->snippet->authorDisplayName);
				$tmp2["text"] = preg_replace("/\s+/", " ",$reply->snippet->textDisplay);
				$tmp2["authorChannelId"] = $reply->snippet->authorChannelId->value;
				$tmp2["authorChannelUrl"] = $reply->snippet->authorChannelUrl;
				$tmp2["isReply"] = 1;
				$tmp2["isReplyToId"] = $tmp["id"];
				$tmp2["isReplyToName"] = $tmp["authorName"];
				
				$nodecomments[] = $tmp2;	
			}	
		}		
	}
	
	echo '<br /><br/>The script retrieved '.count($nodecomments).' comments from '.count($comments).' top level comments.'; 
	

	$fp = fopen("./".$folder.$filename."_comments.".$output, 'w');
	$separator = ($output == "tab") ? "\t":",";
	$fieldnames = array_keys($nodecomments[0]);
	fputcsv($fp, $fieldnames, $separator);

	foreach($nodecomments as $comment) {
		fputcsv($fp, $comment, $separator);
	}

	fclose($fp);	
	
	return $nodecomments;
}


function getCommenters($nodecomments) {
	
	global $filename,$folder,$output;
	
	$authors = array();
	
	foreach($nodecomments as $comment) {
		if(!isset($authors[$comment["authorName"]])) {
			$authors[$comment["authorName"]] = 0;
		}
		$authors[$comment["authorName"]]++;
	}
	
	arsort($authors);
	
	$fp = fopen("./".$folder.$filename."_authors.".$output, 'w');
	$separator = ($output == "tab") ? "\t":",";
	fputcsv($fp, array("author","count"), $separator);

	foreach($authors as $key => $data) {
		fputcsv($fp, array($key,$data), $separator);
	}

	fclose($fp);	
	
	return $authors;
}


function makeNetwork($nodecomments) {
	
	global $filename,$folder;
	
	$nodes = array();
	$edges = array();
	
	foreach($nodecomments as $nodecomment) {
		
		if(!isset($nodes[$nodecomment["authorName"]])) {
			$nodes[$nodecomment["authorName"]] = 0;
		}
		$nodes[$nodecomment["authorName"]]++;
		
		$tmp = preg_match_all("/oid=\"\d+\">(.*)<\/a>/U",$nodecomment["text"],$out);
		
		if(count($out[1]) > 0) {
			
			foreach($out[1] as $ref) {
				if(!isset($nodes[$ref])) {
					$nodes[$ref] = 0;
				}
				
				$edgeid = $nodecomment["authorName"] . "_|_|X|_|_" . $ref;
				if(!isset($edges[$edgeid])) {
					$edges[$edgeid] = 0;
				}
				$edges[$edgeid]++;
			}
			
		} else if ($nodecomment["isReply"] == 1) {
			
			if(!isset($nodes[$nodecomment["isReplyToName"]])) {
				$nodes[$nodecomment["isReplyToName"]] = 0;
			}
			
			$edgeid = $nodecomment["authorName"] . "_|_|X|_|_" . $nodecomment["isReplyToName"];
			if(!isset($edges[$edgeid])) {
				$edges[$edgeid] = 0;
			}
			$edges[$edgeid]++;
		}		
	}
	
	
	$nodegdf = "nodedef>name VARCHAR,label VARCHAR,commentCount INT\n";
	foreach($nodes as $nodeid => $nodedata) {
		$nodeid = preg_replace("/,/", " ", $nodeid);
		$nodegdf .=sha1($nodeid) . "," . $nodeid  . "," . $nodedata . "\n";
	}
	
	$edgegdf = "edgedef>node1 VARCHAR,node2 VARCHAR,weight INT,directed BOOLEAN\n";
	foreach($edges as $edgeid => $edgedata) {
		$tmp = explode("_|_|X|_|_",$edgeid);
		
		$edgegdf .= preg_replace("/,/", " ", sha1($tmp[0])) . "," . preg_replace("/,/", " ", sha1($tmp[1])) . "," . $edgedata . ",true\n";
	}
	
	$gdf = $nodegdf . $edgegdf;
	
	writefile("./".$folder.$filename."_commentnetwork.gdf",$gdf);
}

?>

</body>
</html>