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
		<a href="faq.php" class="navlink">FAQ</a>
	</td>
</tr>
<tr>
	<td colspan="2"></td>
</tr>
<tr>
	<td colspan="2">
		<h1>Frequently Asked Questions</h1>
		
		<h2>What is this?</h2>
		
		<p>YouTube Data Tools (YTDT) is a collection of simple tools for extracting data from the YouTube platform via the <a href="https://developers.google.com/youtube/v3/" target="_blank">YouTube API v3</a>.
		It is not a mashup or fully developed analytics software, but a means for researchers to extract data via YouTube's API to analyze further in other software packages.</p>
		
		
		<h2>Who develops YTDT?</h2>
		
		<p>YTDT is written and maintained by <a href="http://rieder.polsys.net">Bernhard Rieder</a>, Associate Professor in <a href="http://mediastudies.nl" target="_blank">Media Studies</a> at the
		<a href="http://www.uva.nl">University of Amsterdam</a> and researcher at the <a href="https://www.digitalmethods.net" target="_blank">Digital Methods Initiative</a>.</p>
		
		<p>I announce changes or new modules on <a href="https://twitter.com/RiederB/" target="_blank">@RiederB</a>, but I do not react to any tool related matters on channels other than <a href="mailto:tools@polsys.net">tools@polsys.net</a>.</p>
		
		
		<h2>What kind of files does YTDT generate?</h2>
		
		<p>It creates network files in <a href="http://guess.wikispot.org/The_GUESS_.gdf_format" target="_blank">gdf format</a> (a simple text format that specifies a graph) as well as
		statistical files using a <a href="http://en.wikipedia.org/wiki/Tab-separated_values">tab-separated format</a>. You can easily change TSV to CSV by searching and replacing all tabs with commas.</p>
		
		<p>These files can then be analyzed and visualized using graph visualization software such as the powerful and very easy to use <a href="http://gephi.org/" target="_blank">gephi</a>
		platform or statistical tools such as R, Excel, SPSS or the interactive visualization software <a href="http://www.rosuda.org/Mondrian/">Mondrian</a>.</p>
		
		
		<h2>I don't know how to use YTDT, can you help me?</h2>
		
		<p>Unfortunately, I do not have the spare time to provide any assistance for this app and can therefore not respond to inquiries concerning how to use the app or how
		to solve a particular research problem with it.</p>
		
		<p>I will make an introductory video as soon as I have te time, however. In the meantime, the interface for
		each data module contains a description of what is does and links to the relevant sections of the API. Most importantly, to make sense of the data, a good
		understanding of YouTube's basic architecture is required. The <a href="https://developers.google.com/youtube/v3/"  target="_blank">
		documentation</a> for YouTube's API has comprehensive descriptions of entities and metrics.</p>
		
		<p>Almost all of the modules require a video or channel id as input. These can normally be found in the respective YouTube URLs. For example, in the URL https://www.youtube.com/watch?v=BNM4kEUEcp8 the strange code after the "=" sign is the video id.
		For further information, there's a lot of great information available on these things through a simple Google query.</p>
		
		<p>If you would like to learn more about this kind of research, you may want to consider joining the Digital Methods Initiative's
		<a href="https://wiki.digitalmethods.net/Dmi/DmiSummerSchool" target="_blank">summer</a> or
		<a href="https://wiki.digitalmethods.net/Dmi/WinterSchool" target="_blank">winter</a> school, or even enrol in our M.A. program in
		<a href="http://studiegids.uva.nl/xmlpages/page/2014-2015-en/search-programme/programme/741" target="_blank">New Media and Digital Culture</a> or our two-year
		<a href="hhttp://studiegids.uva.nl/xmlpages/page/2014-2015-en/search-programme/programme/554" target="_blank">research MA</a>.
		In these programs, we combine training in analytical techniques with critical conceptual interrogation about new media.</p>
		
		
		<h2>The tool does not work (correctly)!</h2>

		<p>While this is very simple software, this can happen for various reasons.</p>

		<p>High quality bug reports are much appreciated. If you have no experience with reporting bugs effectively, please read <a href="http://www.chiark.greenend.org.uk/~sgtatham/bugs.html" target="_blank">this piece</a> at least twice.
		TL;DR: developers need context to debug a tool, when filing a bug report, please add the URL of the call, the browser you are using, a screenshot of the interface output,
		the data files, and a description of what you have been doing and how the problem manifests itself.</p>
		
		<p>Please send bug reports to <a href="mailto:tools@polsys.net">tools@polsys.net</a>. I do not react to reports sent through any other channel.</p>
		
		
		<h2>Can you add feature X to YTDT?</h2>
		
		<p>I cannot make any guarantees, but if you send a feature request to <a href="mailto:tools@polsys.net">tools@polsys.net</a>, I will definitely consider it.</p>
		
		
		<h2>Can I have the source code?</h2>
		
		<p>Yes, you can. The full source code is available on <a href="https://github.com/bernorieder/YouTube-Data-Tools" target="_blank">github</a>.</p>
	</td>
</tr>
</table>

</body>
</html>