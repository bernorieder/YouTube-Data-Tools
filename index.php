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
		<a href="mod_video_info.php" class="navlink">Video Info</a>
	</td>
</tr>
<tr>
	<td colspan="2"></td>
</tr>
<tr>
	<td colspan="2">
		<h1>YouTube Data Tools</h1>
		<p>This is a collection of simple tools for extracting data from the YouTube platform via the YouTube API v3.</p>
		<p>YTDT is written and maintained by <a href="http://rieder.polsys.net">Bernhard Rieder</a>, Associate Professor in Media Studies at the
		<a href="http://www.uva.nl">University of Amsterdam</a> and researcher at the <a href="https://www.digitalmethods.net" target="_blank">Digital Methods Initiative</a>.</p>
		<p>Source 
	</td>
</tr>
<tr>
	<td colspan="2"><hr /></td>
</tr>
<tr>
	<td><h2>Channel Info</h2></td>
	<td>
		<p>This module retrieves a maximum of information for a channel from a specified channel id.</p>
		<p><a href="mod_channel_info.php">launch</a></p>
	</td>
</tr>
<tr>
	<td colspan="2"><hr /></td>
</tr>
<tr>
	<td><h2>Channel Network</h2></td>
	<td>
		<p>Maps a network of channels via the "featured channels" feature. Start from a search or a list of channel ids.</p>
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
	<td><h2>Video Info and Comments</h2></td>
	<td>
		<p>Retrieves statistics and comments from a video via a video id.</p>
		<p><a href="mod_video_comments.php">launch</a></p>
	</td>
</tr>
</table>

</body>
</html>