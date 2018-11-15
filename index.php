<?php include("html_head.php"); ?>

	<div class="rowTab">
		<div class="fullTab">
			<p>This is a collection of simple tools for extracting data from the YouTube platform via the <a href="https://developers.google.com/youtube/v3/" target="_blank">YouTube API v3</a>.</p>
			
			<p>For some context and a small introduction, please check out this <a href="http://thepoliticsofsystems.net/2015/05/exploring-youtube/">blog post</a>.
			
			<p>Each of the modules has a basic description of how it works, there is a <a href="faq.php">FAQ</a> section with additional information, and an <a href="https://www.youtube.com/watch?v=sbErTW2MzCY" target="_blank">introductory video</a>.</p>
		</div>
	</div>
	
	<div class="rowTab">
		<div class="sectionTab">
			<h1>Modules</h1>
		</div>
	</div>

	<div class="rowTab">
		<div class="sectionTab"><h2>Channel Info</h2></div>
	</div>

	<div class="rowTab">
		<div class="fullTab">		
			This module retrieves different kinds of information for a channel from a specified channel id.
			<p><a href="mod_channel_info.php">launch</a></p>
		</div>
	</div>
	
	
	<div class="rowTab">
		<div class="sectionTab"><h2>Channel Network</h2></div>
	</div>
	
	<div class="rowTab">
		<div class="fullTab">
			This module crawls a network of channels connected via the "featured channels" (and via subscriptions) tab from a list of seeds. Seeds can be channels retrieved from a search or via manual input of channel ids.
			<p><a href="mod_channels_net.php">launch</a></p>
		</div>
	</div>
	
	
	<div class="rowTab">
		<div class="sectionTab"><h2>Video List</h2></div>
	</div>
	
	<div class="rowTab">
		<div class="fullTab">
			This module creates a list of video infos and statistics from one of four sources: the videos uploaded to a specified channel, a playlist, the videos retrieved by a particular search query, or the videos specified by a list of ids.
			<p><a href="mod_videos_list.php">launch</a></p>
		</div>
	</div>
	
	
	<div class="rowTab">
		<div class="sectionTab"><h2>Video Network</h2></div>
	</div>
	
	<div class="rowTab">
		<div class="fullTab">
			This module creates a network of relations between videos via YouTube's "related videos" feature, starting from a search or a list of video ids.
			<p><a href="mod_videos_net.php">launch</a></p>
		</div>
	</div>
	
	
	<div class="rowTab">
		<div class="sectionTab"><h2>Video Info and Comments</h2></div>
	</div>
	
	<div class="rowTab">
		<div class="fullTab">
			This module starts from a video id and retrieves basic info for the video in question and provides a number of analyses of the comment section.
			<p><a href="mod_video_info.php">launch</a></p>
		</div>
	</div>
	
	
	<div class="rowTab">
		<div class="sectionTab">
			<h1>Version History</h1>
		</div>
	</div>
	
	<div class="rowTab">
		<div class="fullTab">
			<ul>
			<li>v1.10 - 15/11/2018 - Added a "related channel" module</li>
			<li>v1.09 - 10/05/2018 - Added a "per day" mode to the video list module</li>
			<li>v1.08 - 06/12/2017 - Channel info module can now make queries for several channels at a time</li>
			<li>v1.07 - 19/10/2017 - Added region code parameter to video list</li>
			<li>v1.06 - 12/05/2017 - Added country and "channel age" to channel network module</li>
			<li>v1.05 - 01/02/2017 - Added date limited search to video list module</li>
			<li>v1.04 - 01/02/2015 - Added like/dislike ratio to video network module output</li>
			<li>v1.03 - 18/10/2015 - Small bugfix</li>
			<li>v1.02 - 05/06/2015 - Added subscriptions to channel network</li>
			<li>v1.01 - 26/05/2015 - Bug fix for video network, video category added to several modules</li>
			<li>v1.0 - 04/05/2015 - Initial Resease</li>
			</ul>
		</div>
	</div>

	
<?php include("html_foot.php"); ?>