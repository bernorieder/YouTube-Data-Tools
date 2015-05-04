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
	<form action="mod_video_info.php" method="get">
		<tr>
			<td colspan="3">
				<a href="index.php" class="navlink">Home</a>
				<a href="mod_channel_info.php" class="navlink">Channel Info</a>
				<a href="mod_channels_net.php" class="navlink">Channel Network</a>
				<a href="mod_videos_list.php" class="navlink">Video List</a>
				<a href="mod_video_info.php" class="navlink">Video Info</a>
			</td>
		</tr>
		<tr>
			<td colspan="3"></td>
		</tr>
		<tr>
			<td colspan="3">			
				<h1>YTDT Video Info and Comments</h1>

				<p>This module starts from a video id and retrieves basic info for the video in question and provides a number of analyses of the comment section.</p>
				
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
			</td>
		</tr>
		<tr>
			<td colspan="3"><hr /></td>
		</tr>
		<tr>
			<td>video id:</td>
			<td><input type="text" name="videohash" value="<?php if(isset($_GET["videohash"])) { echo $_GET["videohash"]; } ?>" /></td>
			<td>(video ids can be found in URLs, e.g. https://www.youtube.com/watch?v=aXnaHh40xnM)</td>
		</tr>
		<tr>
			<td>HTML output:</td>
			<td><input type="checkbox" name="htmloutput" <?php if($_GET["htmloutput"] == "on") { echo "checked"; } ?> /></td>
			<td>(adds HTML result tables in addition to the file exports)</td>
		</tr>
		<tr>
			<td colspan="3"><hr /></td>
		</tr>
		<tr>
			<td colspan="3"><input type="submit" /></td>
		</tr>
	</form>
</table>

<?php

// blocked video example: https://www.youtube.com/watch?v=pLN59ZOweUE

$feed = array();
$feed["comments"] = array();

$video = array();

if(isset($_GET["videohash"])) {

	$videohash = $_GET["videohash"];
	$html = $_GET["htmloutput"];
	$filename = "videoinfo_".$videohash."_".date("Y_m_d-H_i_s");

	$video = getInfo($videohash);
	$nodecomments = getComments($videohash);
	$commenters = getCommenters($nodecomments);
	makeNetwork($nodecomments);
	
	echo '<br /><br />The following files have been generated:<br />';
	echo '<a href="./data/'.$filename.'_basicinfo.tab">'.$filename.'_basicinfo.tab</a><br />';
	echo '<a href="./data/'.$filename.'_comments.tab">'.$filename.'_comments.tab</a><br />';
	echo '<a href="./data/'.$filename.'_authors.tab">'.$filename.'_authors.tab</a><br />';
	echo '<a href="./data/'.$filename.'_commentnetwork.gdf">'.$filename.'_commentnetwork.gdf</a><br />';
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
}


function getInfo($videohash) {

	global $apikey,$html,$filename;

	// forbidden: fileDetails,processingDetails,suggestions
	$restquery = "https://www.googleapis.com/youtube/v3/videos?part=statistics,contentDetails,snippet,status,topicDetails&id=".$videohash."&key=".$apikey;

	$reply = doAPIRequest($restquery);
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
	
	
	$content = "";
	foreach($video as $key => $data) {
		$content .= $key."\t".$data."\n";
	}
	file_put_contents("./data/".$filename."_basicinfo.tab",$content);
	
	return $video;
}


function getComments($videohash) {
	
	global $apikey,$html,$filename;

	
	// get toplevel comments first

	$nextpagetoken = null;
	$run = true;
	$comments = array();
	
	echo "<br />getting comments: "; flush(); ob_flush();

	while($run == true) {
		
		$restquery = "https://www.googleapis.com/youtube/v3/commentThreads?part=snippet&maxResults=100&videoId=".$videohash."&key=".$apikey;
		
		if($nextpagetoken != null) {
			$restquery .= "&pageToken=".$nextpagetoken;
		}
		
		$reply = doAPIRequest($restquery);
		
		foreach($reply->items as $item) {
			$comments[] = $item;
		}
		
		echo " " . count($comments); flush(); ob_flush();
		
		if(isset($reply->nextPageToken) && $reply->nextPageToken != "") {
			$nextpagetoken = $reply->nextPageToken;				
		} else {
			$run = false;
		}
	}	
	
	
	// work through top level comments and get replies
	
	$nodecomments = array();
	$counter = 0;
	
	echo "<br /><br/>digging into thread structure: "; flush(); ob_flush();
	
	foreach($comments as $comment) {
		
		echo " " . $counter;
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
				
				$restquery = "https://www.googleapis.com/youtube/v3/comments?part=snippet&maxResults=100&parentId=".$tmp["id"]."&key=".$apikey;
				
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
	
	
	$content = implode("\t",array_keys($nodecomments[0])) . "\n";
	foreach($nodecomments as $comment) {
		$content .= implode("\t",$comment) . "\n";
	}
	file_put_contents("./data/".$filename."_comments.tab",$content);
	
	
	return $nodecomments;
}


function getCommenters($nodecomments) {
	
	global $filename;
	
	$authors = array();
	
	foreach($nodecomments as $comment) {
		if(!isset($authors[$comment["authorName"]])) {
			$authors[$comment["authorName"]] = 0;
		}
		$authors[$comment["authorName"]]++;
	}
	
	arsort($authors);
	
	$content = "";
	foreach($authors as $key => $data) {
		$content .= $key."\t".$data."\n";
	}
	file_put_contents("./data/".$filename."_authors.tab",$content);
	
	return $authors;
}


function makeNetwork($nodecomments) {
	
	global $filename;
	
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
		$nodegdf .= $nodeid . "," . $nodeid  . "," . $nodedata . "\n";
	}
	
	$edgegdf = "edgedef>node1 VARCHAR,node2 VARCHAR,weight INT\n";
	foreach($edges as $edgeid => $edgedata) {
		$tmp = explode("_|_|X|_|_",$edgeid);
		
		$edgegdf .= $tmp[0] . "," . $tmp[1] . "," . $edgedata . "\n";
	}
	
	$gdf = $nodegdf . $edgegdf;
	
	file_put_contents("./data/".$filename."_commentnetwork.gdf",$gdf);
}

?>

</body>
</html>