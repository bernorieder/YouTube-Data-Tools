<?php include("html_head.php"); ?>

	<div class="rowTab">
		<div class="sectionTab">
			<h1>Frequently Asked Questions</h1>
		</div>
	</div>

	<div class="rowTab">
		<div class="sectionTab"><h2>What is this?</h2></div>
	</div>
	
	<div class="rowTab">
		<div class="fullTab">
			YouTube Data Tools (YTDT) is a collection of simple modules for extracting data from the YouTube platform via the <a href="https://developers.google.com/youtube/v3/" target="_blank">YouTube API v3</a>.
			It is not a mashup or fully developed analytics software, but a means for researchers to collect data in standard file formats to analyze further in
			other software packages.
		</div>
	</div>
	
	
	<div class="rowTab">
		<div class="sectionTab"><h2>Who develops YTDT?</h2></div>
	</div>
	
	<div class="rowTab">
		<div class="fullTab">
			YTDT is written and maintained by <a href="http://rieder.polsys.net">Bernhard Rieder</a>, Associate Professor in <a href="http://mediastudies.nl" target="_blank">Media Studies</a> at the
			<a href="http://www.uva.nl">University of Amsterdam</a> and researcher with the <a href="https://www.digitalmethods.net" target="_blank">Digital Methods Initiative</a>.
			
			<p>Development and maintainance of this tool are financed by the Dutch <a href="https://pdi-ssh.nl/" target="_blank">Platform Digitale Infrastructuur 
			Social Science and Humanities</a> as part of the <a href="https://cat4smr.humanities.uva.nl/" target="_blank">CAT4SMR project</a>.</p>

			<p>Changes or new modules are announced on <a href="https://twitter.com/RiederB/" target="_blank">@RiederB</a> and <a href="https://twitter.com/cat4smr" target="_blank">@cat4smr</a>, but
			for questions and support please refer to the help section below.</p>
		</div>
	</div>
	
	
	<div class="rowTab">
		<div class="sectionTab"><h2>How can I cite YTDT?</h2></div>
	</div>
	
	<div class="rowTab">
		<div class="fullTab">
			There is currently no publication on YTDT. But the different citation standards provide guidelines for how to cite software, e.g. APA:
			Rieder, Bernhard (2015). YouTube Data Tools (Version 1.22) [Software]. Available from https://tools.digitalmethods.net/netvizz/youtube/.
						
			<p>Alternatively, you can cite this <a href="http://thepoliticsofsystems.net/2015/05/exploring-youtube/">blog post</a>.</p>
			
			<p>If you are interested in the kind of work that can be done with this tool, check out this <a href="http://journals.sagepub.com/doi/full/10.1177/1354856517736982">research paper</a>.</p>
		</div>
	</div>
	
	
	<div class="rowTab">
		<div class="sectionTab"><h2>What kind of files does YTDT generate?</h2></div>
	</div>
	
	<div class="rowTab">
		<div class="fullTab">
			It creates network files in <a href="https://gephi.org/users/supported-graph-formats/gdf-format/" target="_blank">gdf format</a> (a simple text format that specifies a graph) as well as
			statistical files using a <a href="http://en.wikipedia.org/wiki/Tab-separated_values">tab-separated format</a>. You can easily change TSV to CSV by searching and replacing all tabs with commas.
			
			<p>These files can then be analyzed and visualized using graph visualization software such as the powerful and very easy to use <a href="http://gephi.org/" target="_blank">gephi</a>
			platform or statistical tools such as R, Excel, SPSS, or others.</p>
		</div>
	</div>
	
	
	<div class="rowTab">
		<div class="sectionTab"><h2>I don't know how to use YTDT, can you help me?</h2></div>
	</div>
	
	<div class="rowTab">
		<div class="fullTab">
			There is an <a href="https://www.youtube.com/watch?v=sbErTW2MzCY" target="_blank">introductory video</a> and the interface for
			each data module contains a description of what is does and links to the relevant sections of the API. Most importantly, to make sense of the data, a good
			understanding of YouTube's basic architecture is required. The <a href="https://developers.google.com/youtube/v3/"  target="_blank">
			documentation</a> for YouTube's API has comprehensive descriptions of entities and metrics.
			
			<p>We provide user support through a <a href="https://www.reddit.com/r/CAT4SMR/" target="_blank">subreddit</a> and a
			<a href="https://www.facebook.com/groups/678943026381479" target="_blank">Facebook Group</a>.</p>
		</div>
	</div>
	
	
	<div class="rowTab">
		<div class="sectionTab"><h2>What are channel or video ids and how can I find them?</h2></div>
	</div>
	
	<div class="rowTab">
		<div class="fullTab">
			Many of the modules require a video or channel id as input. These can normally be found in the respective YouTube URLs.
			
			<p>For example, in the URL <span class="grey">https://www.youtube.com/watch?v=</span><b>BNM4kEUEcp8</b> the strange code after the "=" sign is the video id.</p>
			
			<p>Channel ids can be found either directly in the channel URL (e.g. <span class="grey">https://www.youtube.com/channel/</span><b>UCtxGqPJPPi8ptAzB029jpYA</b>)
			or by pasting the full channel URL <a href="https://commentpicker.com/youtube-channel-id.php" target="_blank">here</a>.</p>
		</div>
	</div>


	<div class="rowTab">
		<div class="sectionTab"><h2>The tool does not work (correctly)!</h2></div>
	</div>
	
	<div class="rowTab">
		<div class="fullTab">
			While this is very simple software, this can happen for all kinds of reasons. Most problems are due to limitations or bugs in YouTube's Web-API and
			cannot be solved easily on our side, though. Sometimes the tool will fail because users have been using it too heavily.
	
			<p>High quality bug reports are much appreciated. If you have no experience with reporting bugs effectively, please read
			<a href="http://www.chiark.greenend.org.uk/~sgtatham/bugs.html" target="_blank">this piece</a>.
			TL;DR: developers need context to debug a tool, when filing a bug report, please add the URL of the call, the browser you are using, a
			screenshot of the interface output, the data files, and a description of what you have been doing and how the problem manifests itself. Without extensive
			information it can be very hard to replicate a problem and subsequently fix it.</p>
			
			<p>Please submit bug reports via our <a href="https://www.reddit.com/r/CAT4SMR/" target="_blank">subreddit</a>,
			<a href="https://www.facebook.com/groups/678943026381479" target="_blank">Facebook Group</a>, or (ideally) <a href="https://github.com/bernorieder/YouTube-Data-Tools/issues" target="_blank">github</a>.
			Please do not use Twitter - we need more information than 280 characters can provide.
			</p>
		</div>
	</div>
		
		
	<div class="rowTab">
		<div class="sectionTab"><h2>I want to make crawls with higher crawl depth!</h2></div>
	</div>
	
	<div class="rowTab">
		<div class="fullTab">
			Since the public version of the script runs on a server that does a bunch of different things, this is not possible due to resource
			constraints. But you can always get the source code (see below) and remove the line of code that checks for crawl depth. You may still run out of RAM,
			but networks with > 100K nodes should be easily doable with 4 GB.
		</div>
	</div>
	
	
	<div class="rowTab">
		<div class="sectionTab"><h2>Can you add feature X to YTDT?</h2></div>
	</div>
	
	<div class="rowTab">
		<div class="fullTab">
			We cannot make any guarantees, but if you post a feature request in our <a href="https://www.reddit.com/r/CAT4SMR/" target="_blank">subreddit</a> or
			<a href="https://www.facebook.com/groups/678943026381479" target="_blank">Facebook Group</a>, we will definitely consider it.
		</div>
	</div>
	
	
	<div class="rowTab">
		<div class="sectionTab"><h2>Where is the source code?</h2></div>
	</div>
	
	<div class="rowTab">
		<div class="fullTab">
			The full source code is available on <a href="https://github.com/bernorieder/YouTube-Data-Tools" target="_blank">github</a>. You'll also find installation instructions there.
		</div>
	</div>		
			
<?php include("html_foot.php"); ?>