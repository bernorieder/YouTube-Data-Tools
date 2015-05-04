<!doctype html>

<html lang="en">
<head>
	<meta charset="utf-8">
	
	<title>YouTube Data Tools</title>
	
	<link rel="stylesheet" type="text/css" href="main.css" />
</head>

<body>
	
<table>
<tr>
	<td colspan="2">
		<a href="index.php" class="navlink">Home</a>
		<a href="mod_channel_info.php" class="navlink">Channel Info</a>
		<a href="mod_channels_net.php" class="navlink">Channel Network</a>
		<a href="mod_videos_list.php" class="navlink">Video List</a>
		<a href="mod_videos_net.php" class="navlink">Video Network</a>
		<a href="mod_video_info.php" class="navlink">Video Info</a>
	</td>
</tr>
<tr>
	<td colspan="2"></td>
</tr>
<tr>
	<td colspan="2">
		<h1>YouTube Data Tools</h1>
		
		<p>This is a collection of simple tools for extracting data from the YouTube platform via the <a href="https://developers.google.com/youtube/v3/" target="_blank">YouTube API v3.</a></p>
		
		<p>This is "as is" software, no support is provided. Source code is available on <a href="https://github.com/bernorieder/YouTube-Data-Tools" target="_blank">github</a>.
		High quality bug reports are appreciated. If you have no experience with reporting bugs effectively, please read <a href="http://www.chiark.greenend.org.uk/~sgtatham/bugs.html" target="_blank">this piece</a> at least twice.
		Send bug reports to <a href="mailto:tools@polsys.net">tools@polsys.net</a> only.<p>
		
		<p>YTDT is written and maintained by <a href="http://rieder.polsys.net">Bernhard Rieder</a>, Associate Professor in <a href="http://mediastudies.nl" target="_blank">Media Studies</a> at the
		<a href="http://www.uva.nl">University of Amsterdam</a> and researcher at the <a href="https://www.digitalmethods.net" target="_blank">Digital Methods Initiative</a>.</p>
	</td>
</tr>
<tr>
	<td colspan="2"><hr /></td>
</tr>
<tr>
	<td><h2>Channel Info</h2></td>
	<td>
		<p>This module retrieves different kinds of information for a channel from a specified channel id.</p>
		<p><a href="mod_channel_info.php">launch</a></p>
	</td>
</tr>
<tr>
	<td colspan="2"><hr /></td>
</tr>
<tr>
	<td><h2>Channel Network</h2></td>
	<td>
		<p>This module crawls a network of channels connected via the "featured channels" tab from a list of seeds. Seeds can be channels retrieved from a search or via manual input of channel ids.</p>
		<p><a href="mod_channels_net.php">launch</a></p>
	</td>
</tr>
<tr>
	<td colspan="2"><hr /></td>
</tr>
<tr>
	<td><h2>Video List</h2></td>
	<td>
		<p>This module creates a list of video infos and statistics from one of four sources: the videos uploaded to a specified channel,
		a playlist, the videos retrieved by a particular search query, or the videos specified by a list of ids.</p>
		<p><a href="mod_videos_list.php">launch</a></p>
	</td>
</tr>
<tr>
	<td colspan="2"><hr /></td>
</tr>
<tr>
	<td><h2>Video Network</h2></td>
	<td>
		<p>This module creates a network of relations between videos via YouTube's "related videos" feature, starting from a search or a list of video ids.</p>
		<p><a href="mod_videos_net.php">launch</a></p>
	</td>
</tr>
<tr>
	<td colspan="2"><hr /></td>
</tr>
<tr>
	<td><h2>Video Info and Comments</h2></td>
	<td>
		<p>This module starts from a video id and retrieves basic info for the video in question and provides a number of analyses of the comment section.</p>
		<p><a href="mod_video_comments.php">launch</a></p>
	</td>
</tr>
</table>

</body>
</html>