<?php

if(isset($_GET["mode"])) { $_POST = $_GET; }

include("html_head.php");

?>

	<div class="rowTab">
		<div class="sectionTab">
			<h1>Channel Info Module</h1>
		</div>
	</div>

	<div class="rowTab">
		<div class="fullTab">
			<p>This module retrieves different kinds of information for a channel from the <a href="https://developers.google.com/youtube/v3/docs/channels/list" target="_blank">channels/list</a> API endpoint
			from a specified channel id or channel URL. You can use this module to find channel ids to use in other modules.</p>
			<p>The following resources are requested: brandingSettings, status, id, snippet, contentDetails, statistics, and topicDetails.</p>
			<p>Output is a direct print of the API response.</p>
		</div>
	</div>

	<div class="rowTab">
		<div class="sectionTab"><h1>Parameters</h1></div>
	</div>

	<div class="rowTab">
		<div class="sectionTab"><h2>The channel(s) to investigate:</h2></div>
	</div>

	<form action="mod_channel_info.php" method="post">


	<div class="rowTab">
		<div class="oneTab"></div>
		<div class="twoTab">Channel id or URL:</div>
		<div class="threeTab">
			<input name="hash" value="<?php if(isset($_POST["hash"])) { echo $_POST["hash"]; } ?>" />
		</div>
		<div class="fourTab">(e.g. "https://www.youtube.com/@BernhardRiederAmsterdam/" or "UCtxGqPJPPi8ptAzB029jpYA")</div>
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

<?php

if(isset($_POST["hash"])) {

	echo '<div class="rowTab">
			<div class="sectionTab"><h1>Results</h1></div>
		 </div>
		 <div class="rowTab">';
	 
	if($_POST["hash"] == "") {
		echo "Missing channel id.";
		exit;
	}

	
	if(RECAPTCHA) {
		if($_POST["g-recaptcha-response"] == "") {
			echo "Recaptcha missing.";
			exit;
		}
		testcaptcha($_POST["g-recaptcha-response"]);
	}


	$hash = $_POST["hash"];

	if(preg_match("/http/", $hash)) {
		
		$restquery = "https://www.googleapis.com/youtube/v3/search?part=snippet&q=". urlencode($hash)."&type=channel&fields=items(id(kind,channelId))";

		$reply = doAPIRequest($restquery);

		$hash = $reply->items[0]->id->channelId;
	}
	
	getInfo($hash);
}

function getInfo($hash) {

	$restquery = "https://www.googleapis.com/youtube/v3/channels?part=brandingSettings,status,id,snippet,contentDetails,statistics,topicDetails&id=".$hash;

	$reply = doAPIRequest($restquery);
	
	echo '<table class="resulttable">';
	foreach($reply->items[0] as $key => $var) {
		echo '<tr class="resulttable">';
		echo '<td class="resulttableHi"><b>'.$key.'</b></td>';
		if(gettype($var) != "object" && gettype($var) != "array") { 
			echo '<td class="resulttable">'.$var.'</td>';
		} else {
			
			echo '<td class="resulttable">';
			echo '<table style="display:inline">';
			foreach($var as $key2 => $var2) {
				echo '<tr>';
				echo '<td><b>'.$key2.'</b></td>';
				if(gettype($var2) != "object" && gettype($var2) != "array") {
					echo '<td>'.$var2.'</td>';
				} else {
					
					echo '<td class="resulttable">';
					echo '<table style="display:inline">';
					foreach($var2 as $key3 => $var3) {
						echo '<tr>';
						echo '<td><b>'.$key3.'</b></td>';
						if(gettype($var3) != "object" && gettype($var3) != "array") {
							echo '<td>'.$var3.'</td>';
						} else {
							
							echo '<td class="resulttable">';
							echo '<table style="display:inline">';
							foreach($var3 as $key4 => $var4) {
								echo '<tr>';
								echo '<td><b>'.$key4.'</b></td>';
								if(gettype($var4) != "object" && gettype($var) != "array") {
									echo '<td>'.$var4.'</td>';
								} else {
									echo '<td>'.$var4.'</td>';	
								}
								echo '</tr>';
							}
							echo '</table>';
							echo '</td>';

						}
						echo '</tr>';
					}
					echo '</table>';
					echo '</td>';
					
				}
				echo '</tr>';
			}
			echo '</table>';
			echo '</td>';
			
		}
		echo '</tr>';
	}
	echo '</table></div>';
}

?>

<?php include("html_foot.php"); ?>