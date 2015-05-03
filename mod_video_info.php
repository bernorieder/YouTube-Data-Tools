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

				<p>some explanation</p>
			</td>
		</tr>
		<tr>
			<td colspan="3"><hr /></td>
		</tr>
		<tr>
			<td>video id:</td>
			<td><input type="text" name="videohash" value="" /></td>
			<td>(video ids can be found in URLs, e.g. https://www.youtube.com/watch?v=dQw4w9WgXcQ)</td>
		</tr>
		<tr>
			<td colspan="3"><input type="submit" /></td>
		</tr>
	</form>
</table>

<?php

$feed = array();
$feed["comments"] = array();

$video = array();

if(isset($_GET["videohash"])) {

	$videohash = $_GET["videohash"];

	getInfo();
	getReplies();
	getChunk(1);	// though shallst start counting at 1 in the Googleverse
}

function getInfo() {

	global $video,$videohash;

	$restquery = 'https://gdata.youtube.com/feeds/api/videos/'.$videohash.'?v=2&alt=json';
	//https://www.googleapis.com/youtube/v3/videos?id=bzsRsugCXII&key=AIzaSyDpPvVCPAUw53kwG1H45Dmqk3m_zOALYNQ
	//https://www.googleapis.com/youtube/v3/videos?part=statistics,player&id=bzsRsugCXII&key=AIzaSyDpPvVCPAUw53kwG1H45Dmqk3m_zOALYNQ
	//https://developers.google.com/youtube/articles/changes_to_comments

	$reply = doAPIRequest($restquery);

	//print_r($reply); exit;

	$video["published"] = date("Y-m-d H:i:s", strtotime($reply->entry->published->{'$t'}));
	$video["title"] = $reply->entry->title->{'$t'};
	$video["uploader"] = $reply->entry->author[0]->name->{'$t'};
	$video["duration"] = $reply->entry->{'media$group'}->{'media$content'}[0]->duration;
	$video["user_type"] = $reply->entry->{'media$group'}->{'media$credit'}[0]->{'yt$type'};

	$video["favoriteCount"] = $reply->entry->{'yt$statistics'}->favoriteCount;
	$video["viewCount"] = $reply->entry->{'yt$statistics'}->viewCount;

	$video["numDislikes"] = $reply->entry->{'yt$rating'}->numDislikes;
	$video["numLikes"] = $reply->entry->{'yt$rating'}->numLikes;
	if($video["numDislikes"] == 0 && $video["numLikes"] == 0) {
		$video["likebalance"] = 0;
	} else if ($video["numDislikes"] == 0) {
		$video["likebalance"] = $video["numLikes"];
	} else if ($video["numLikes"] == 0) {
		$video["likebalance"] = -$video["numDislikes"];
	} else {
		$video["likebalance"] = $video["numLikes"] / $video["numDislikes"];
	}

	$video["keywords"] = $reply->entry->{'media$group'}->{'media$keywords'}->{'$t'};

	$perms = $reply->entry->{'yt$accessControl'};
	$video["permissions"] = array();

	print_r($video["perms"]);

	foreach($perms as $perm) {
		$video["permissions"][$perm->action] = $perm->permission;
	}


	// published data, tags, title, author, accesscontrol, favorite count, view count, likes/dislikes
	// "yt$type": "partner"???
}

function getChunk($currentPos) {

	global $feed,$videohash;

	$stop = false;

	if($currentPos + 50 >= 1000) { // replace with 1000!!!!

		$noresults = 1000 - $currentPos;		// here also
		$stop = true;

	} else {

		$noresults = 50;
	}


	$restquery = 'http://gdata.youtube.com/feeds/api/videos/'.$videohash.'/comments?v=2'.
			 	 '&max-results='.$noresults.
			 	 '&start-index='.$currentPos.
			 	 '&alt=json';

	$reply = doAPIRequest($restquery);

	//print_r($reply);

	$feed["totalcomments"] = $reply->feed->{'openSearch$totalResults'}->{'$t'};

	if(!isset($reply->feed->entry)) {
		$stop = true;
	} else {
		$feed["comments"] = array_merge($feed["comments"], $reply->feed->entry);
	}

	if($stop == false) {
		getChunk($currentPos + 50);
	} else {
		processComments();
	}
}

function getReplies() {

	global $feed,$videohash;

	$restquery = 'http://gdata.youtube.com/feeds/api/videos/'.$videohash.'/responses?v=2'.
			 	 '&alt=json';

	$reply = doAPIRequest($restquery);

	$feed["totalvideoreplies"] = $reply->feed->{'openSearch$totalResults'}->{'$t'};

	//print_r($reply);
}

function processComments() {

	global $feed,$video,$stopwords,$punctuation;

	//file_put_contents("data.json", json_encode($feed));
	//$feed = array();
	//$json = json_decode(file_get_contents("data.json"));
	//$feed["comments"] = $json->comments;

	//print_r($feed);

	$spamcounter = 0;
	$updatecounter = 0;
	$userlist = array();
	$mentionlist = array();
	$wordlist = array();
	$hidate = 0;
	$lodate = 1000000000000000;

	$feed["nicecomments"] = array();

	for($i = 0; $i < count($feed["comments"]); $i++) {

		$nice = array();
		$nice["pubdate"] = date("Y-m-d H:i:s", strtotime($feed["comments"][$i]->published->{'$t'}));
		$nice["author"] = $feed["comments"][$i]->author[0]->name->{'$t'};
		//$nice["title"] = $feed["comments"][$i]->title->{'$t'};
		$nice["content"] = $feed["comments"][$i]->content->{'$t'};

		$feed["nicecomments"][] = $nice;

		// check for Spam
		if(isset($feed["comments"][$i]->{'yt$spam'})) {
			$spamcounter++;
		}

		// collect authornames
		$username = $feed["comments"][$i]->author[0]->name->{'$t'};
		if(!isset($userlist[$username])) {
			$userlist[$username] = 1;
		} else {
			$userlist[$username]++;
		}

		// collect mentions
		$content = $feed["comments"][$i]->content->{'$t'};
		preg_match_all("/\@(.+?)\s/",$content,$find);

		if(count($find[1]) > 0) {
			for($j = 0; $j < count($find[1]); $j++) {
				if(!isset($mentionlist[$find[1][$j]])) {
					$mentionlist[$find[1][$j]] = 1;
				} else {
					$mentionlist[$find[1][$j]]++;
				}
			}
		}
		//print_r($find);

		// collect updates published
		if($feed["comments"][$i]->published->{'$t'} != $feed["comments"][$i]->updated->{'$t'}) {
			$updatecounter++;
		}

		// collect words
		$content = preg_replace("/[".implode("|",$punctuation)."]/"," ", strtolower($content));
		$content = preg_replace("/\s+/"," ", $content);
		$tmpwords = explode(" ",$content);
		foreach ($tmpwords as $word) {
			if(strlen($word) > 2 && !in_array($word, $stopwords)) {
				if(!isset($wordlist[$word])) {
					$wordlist[$word] = 1;
				} else {
					$wordlist[$word]++;
				}
			}
		}
		//print_r($tmpwords);

		// dates
		$date = strtotime($feed["comments"][$i]->published->{'$t'});
		//echo $date;
		if($date < $lodate) { $lodate = $date; }
		if($date > $hidate) { $hidate = $date; }
	}

	arsort($userlist);
	arsort($mentionlist);
	arsort($wordlist);

	$timedist = $hidate - $lodate;
	$timedist_full = $hidate - strtotime($video["published"]);
	$timedist = $timedist / (60 * 60 * 24);
	$timedist_full = $timedist_full / (60 * 60 * 24);;

	$feed["commentsretrieved"] = count($feed["comments"]);
	$feed["spamcounter"] = $spamcounter;
	$feed["updatecounter"] = $updatecounter;
	$feed["firstcommentretrieved"] = date("Y-m-d H:i:s", $lodate);
	$feed["lastcommentretrieved"] = date("Y-m-d H:i:s", $hidate);
	$feed["timedist_retrieved_days"] = $timedist;
	$feed["comments_per_day_retrieved"] = $feed["commentsretrieved"] / $timedist;
	$feed["timedist_full_days"] = $timedist_full;
	$feed["comments_per_day_full"] = $feed["totalcomments"] / $timedist_full;
	$feed["uploadercomments"] = (isset($userlist[$video["uploader"]])) ? $userlist[$video["uploader"]] : 0;
	$feed["uploadermentions"] = (isset($mentionlist[$video["uploader"]])) ? $mentionlist[$video["uploader"]] : 0;
	$feed["userlist"] = $userlist;
	$feed["mentionlist"] = $mentionlist;
	$feed["wordlist"] = $wordlist;

	$feed["comments"] = array();

	$csv = "Hash,published,title,uploader,user_type,duration,viewCount,favoriteCount,numLikes,numDislikes,likebalance,keywords,permissions_comment,permissions_videoRespond,";
	$csv .= "permissions_embed,totalVideoReplies,totalcomments,spamcounter,comments_per_day_full,uploadercomments,uploadermentions\n";
	$csv .= $_GET["videohash"] .",";
	$csv .= $video["published"] .",";
	$csv .= $video["title"] .",";
	$csv .= $video["uploader"] .",";
	$csv .= $video["user_type"] .",";
	$csv .= $video["duration"] .",";
	$csv .= $video["viewCount"] .",";
	$csv .= $video["favoriteCount"] .",";
	$csv .= $video["numLikes"] .",";
	$csv .= $video["numDislikes"] .",";
	$csv .= $video["likebalance"] .",";
	$csv .= '"'.$video["keywords"] .'",';
	$csv .= $video["permissions"]["comment"] .",";
	$csv .= $video["permissions"]["videoRespond"] .",";
	$csv .= $video["permissions"]["embed"] .",";
	$csv .= $feed["totalvideoreplies"] .",";
	$csv .= $feed["totalcomments"] .",";
	$csv .= $feed["spamcounter"] .",";
	$csv .= $feed["comments_per_day_full"] .",";
	$csv .= $feed["uploadercomments"] .",";
	$csv .= $feed["uploadermentions"] .",";

	$filename = $_GET["videohash"].".csv";

	file_put_contents($filename, $csv);

	echo '<p><a href="'.$filename.'">'.$filename.'</a>';

	echo '<pre>';
	print_r($video);
	print_r($feed);
	echo '</pre>';
}

?>

</body>
</html>