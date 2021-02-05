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
	<form action="mod_videos_list.php" method="post">
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
				<h1>YTDT Comment Ecology</h1>

				<p>blabla description text</p>
			</td>
		</tr>
		<tr>
			<td colspan="5"><hr /></td>
		</tr>
			<tr>
			<td><input type="radio" name="mode" value="search" <?php if($_POST["mode"] == "search") { echo "checked"; } ?> /></td>
			<td>search query:</td>
			<td><input type="text" name="query" value="<?php if(isset($_POST["query"])) { echo $_POST["query"]; }; ?>" /></td>
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
			<td>video list:</td>
			<td colspan="2">
				<textarea name="seeds"><?php if($_POST["mode"] == "seeds") { echo $_POST["seeds"]; } ?></textarea>
			</td>
			<td>(video ids, comma separated)</td>
		</tr>
		<tr>
			<td colspan="5"><hr /></td>
		</tr>
		<tr>
			<td colspan="5"><input type="submit" /></td>
		</tr>
	</form>
</table>

<?php

// allow for direct URL parameters and command line for cron
if(isset($argv)) { parse_str(implode('&', array_slice($argv, 1)), $_GET); }
if(isset($_GET["mode"])) { $_POST = $_GET; }

//print_r($_POST); exit;

if(isset($_POST["channel"]) || isset($_POST["seeds"]) || isset($_POST["query"])) {

	$mode = $_POST["mode"];

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

		makeOverlapFromIds($ids);
		
	} else if($mode == "seeds") {
		
		$seeds = $_POST["seeds"];
		
		$seeds = preg_replace("/\s+/","",$seeds);
		$seeds = trim($seeds);
		
		$ids = explode(",",$seeds);
		
		makeOverlapFromIds($ids);
		
	} else {
		
		echo "You need to select a mode.";
	}
}


function makeOvelapFromIds($ids) {
	
	// grab comments from each vdeo
	
}


?>