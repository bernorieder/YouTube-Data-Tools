<?php include("html_head.php"); ?>

	<div class="rowTab">
		<div class="fullTab"> 
			<p>This is a collection of simple tools for extracting data from the YouTube platform via the <a href="https://developers.google.com/youtube/v3/" target="_blank">YouTube API v3</a>.</p>
			
			<p>For some context and a small introduction, please check out this <a href="http://thepoliticsofsystems.net/2015/05/exploring-youtube/">blog post</a>.
			
			<p>Each of the modules has a basic description of how it works, there is a <a href="faq.php">FAQ</a> section with additional information, an <a href="https://www.youtube.com/watch?v=TmF4mWZYnbk" target="_blank">introductory video</a>,
				and the excellent worksheet <a href="https://docs.google.com/document/d/1LJ8365kiUrqk6y3WH8pksmUt77mX65nojuijt1Okwyc/edit">Working with YouTube Data Tools</a> created by Daniel Jurg.</p>

			<p>To learn more about how we handle data, please read our <a href="privacy.php">privacy policy</a>.</p>
			
			<p>If you use this tool in a scientific publication, please cite it, e.g. in APA style: Rieder, Bernhard (2015). YouTube Data Tools (Version 1.42) [Software]. Available from https://ytdt.digitalmethods.net.</p>
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
			This module retrieves different kinds of information for a channel from a specified channel id or channel URL. You can use this module to find channel ids to use in other modules.
			<p><a href="mod_channel_info.php">launch</a></p>
		</div>
	</div>
	

	<div class="rowTab">
		<div class="sectionTab"><h2>Channel List</h2></div>
	</div>

	<div class="rowTab">
		<div class="fullTab">		
			This module provides infos and statistics on channels taken from a searches or a list of channel ids.
			<p><a href="mod_channels_list.php">launch</a></p>
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
	
	<!--
	<div class="rowTab">
		<div class="sectionTab"><h2>Related Channel Network</h2></div>
	</div>
	
	<div class="rowTab">
		<div class="fullTab">
			This module starts from a list of seeds and crawls networks of channels connected via the "related channels" panel which YouTube generates algorithmically.
			<p><a href="mod_channels_net.php">launch</a></p>
		</div>
	</div>
	-->
	
	<div class="rowTab">
		<div class="sectionTab"><h2>Video List</h2></div>
	</div>
	
	<div class="rowTab">
		<div class="fullTab">
			This module creates a list of video infos and statistics from one of four sources: the videos uploaded to a specified channel, a playlist, the videos retrieved by a particular search query, or the videos specified by a list of ids.
			<p><a href="mod_videos_list.php">launch</a></p>
		</div>
	</div>
	
	<!--
	<div class="rowTab">
		<div class="sectionTab"><h2>Video Network</h2></div>
	</div>
	
	<div class="rowTab">
		<div class="fullTab">
			This module creates a network of relations between videos via YouTube's "related videos" feature, starting from a search or a list of video ids.
			<p><a href="mod_videos_net.php">launch</a></p>
		</div>
	</div>
	-->

	<div class="rowTab">
		<div class="sectionTab"><h2>Video Co-commenting Network</h2></div>
	</div>
	
	<div class="rowTab">
		<div class="fullTab">
			This module creates a network of videos, based on the concept of co-commenting. If a user comments on two videos, a link is made between these two videos and the more users co-comment, the stronger the link.
			<p><a href="mod_videos_comments_net.php">launch</a></p>
		</div>
	</div>
	
	
	<div class="rowTab">
		<div class="sectionTab"><h2>Video Comments</h2></div>
	</div>
	
	<div class="rowTab">
		<div class="fullTab">
			This module starts from a video id and retrieves basic info for the video in question and provides a number of analyses of the comment section.
			<p><a href="mod_video_comments.php">launch</a></p>
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
			<li>v1.42 - 03/11/2023 - Added pseudonymization option to video comments module</li>
			<li>v1.41 - 13/10/2023 - Added location search and location data to video list module</li>
			<li>v1.40 - 29/09/2023 - Video Network module removed, Video Co-commenting Network module added</li>
			<li>v1.31 - 25/02/2023 - Channel info module now works with channel URLs</li>
			<li>v1.30 - 13/01/2023 - Many bug fixes, renamed modules, added topic categories and keywords to channel list module, added co-tag network to video list module</li>
			<li>v1.24 - 01/12/2022 - Fixed channel network by moving to new API endpoint</li>
			<li>v1.23 - 22/05/2022 - Added output format selector</li>
			<li>v1.22 - 25/02/2021 - Added a means to limit to n top comments in video info module</li>
			<li>v1.21 - 11/01/2021 - Added video tags to video list module</li>
			<li>v1.20 - 31/12/2020 - Added channel search module, housekeeping</li>
			<li>v1.12 - 20/09/2019 - Added a channel network output to the video network module</li>
			<li>v1.11 - 02/07/2019 - Removed "related channel" module after YT removed the feature from the interface; added thumbnail url to video list module</li>
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
			<li>v1.00 - 04/05/2015 - Initial Release</li>
			</ul>
		</div>
	</div>

<?php include("html_foot.php"); ?>