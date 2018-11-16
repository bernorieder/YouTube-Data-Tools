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
			YouTube Data Tools (YTDT) is a collection of simple tools for extracting data from the YouTube platform via the <a href="https://developers.google.com/youtube/v3/" target="_blank">YouTube API v3</a> (and some scraping).
			It is not a mashup or fully developed analytics software, but a means for researchers to extract data via YouTube's API to analyze further in other software packages.
		</div>
	</div>
	
	
	<div class="rowTab">
		<div class="sectionTab"><h2>Who develops YTDT?</h2></div>
	</div>
	
	<div class="rowTab">
		<div class="fullTab">
			YTDT is written and maintained by <a href="http://rieder.polsys.net">Bernhard Rieder</a>, Associate Professor in <a href="http://mediastudies.nl" target="_blank">Media Studies</a> at the
			<a href="http://www.uva.nl">University of Amsterdam</a> and researcher at the <a href="https://www.digitalmethods.net" target="_blank">Digital Methods Initiative</a>.
			
			<p>I announce changes or new modules on <a href="https://twitter.com/RiederB/" target="_blank">@RiederB</a>, but I <strong>do not react</strong> to any tool related matters on channels other than <a href="mailto:tools@polsys.net">tools@polsys.net</a>.</p>
		</div>
	</div>
	
	
	<div class="rowTab">
		<div class="sectionTab"><h2>How can I cite YTDT?</h2></div>
	</div>
	
	<div class="rowTab">
		<div class="fullTab">
			There is currently no publication on YTDT. But the different citation standards provide guidelines for how to cite software, e.g. MLA:
			Rieder, Bernhard. YouTube Data Tools. Computer software. Vers. 1.10. N.p., 16 Nov 2018. Web. &lt;https://tools.digitalmethods.net/netvizz/youtube/&gt;.
						
			<p>Alternatively, you can cite this <a href="http://thepoliticsofsystems.net/2015/05/exploring-youtube/">blog post</a>.</p>
			
			<p>If you are interested in the kind of work that can be done with this tool, check out this <a href="http://journals.sagepub.com/doi/full/10.1177/1354856517736982">research paper</a>.</p>
		</div>
	</div>
	
	
	<div class="rowTab">
		<div class="sectionTab"><h2>What kind of files does YTDT generate?</h2></div>
	</div>
	
	<div class="rowTab">
		<div class="fullTab">
			It creates network files in <a href="http://guess.wikispot.org/The_GUESS_.gdf_format" target="_blank">gdf format</a> (a simple text format that specifies a graph) as well as
			statistical files using a <a href="http://en.wikipedia.org/wiki/Tab-separated_values">tab-separated format</a>. You can easily change TSV to CSV by searching and replacing all tabs with commas.
			
			<p>These files can then be analyzed and visualized using graph visualization software such as the powerful and very easy to use <a href="http://gephi.org/" target="_blank">gephi</a>
			platform or statistical tools such as R, Excel, SPSS or the interactive visualization software <a href="http://www.rosuda.org/Mondrian/">Mondrian</a>.</p>
		</div>
	</div>
	
	
	<div class="rowTab">
		<div class="sectionTab"><h2>I don't know how to use YTDT, can you help me?</h2></div>
	</div>
	
	<div class="rowTab">
		<div class="fullTab">
			Since I develop tools in my spare time, this is "as is" software and <strong>no support</strong> is provided. I do not respond to questions on how to use the tool or how
			to solve a particular research problem with it.
			
			<p>There is an <a href="https://www.youtube.com/watch?v=sbErTW2MzCY" target="_blank">introductory video</a> and the interface for
			each data module contains a description of what is does and links to the relevant sections of the API. Most importantly, to make sense of the data, a good
			understanding of YouTube's basic architecture is required. The <a href="https://developers.google.com/youtube/v3/"  target="_blank">
			documentation</a> for YouTube's API has comprehensive descriptions of entities and metrics.</p>
			
			<p>For finding help with technical matters or research design, I recommend looking in your own organization, e.g. your tech support team,
			your thesis supervisor, or one of the departments that are actively engaged in data analysis. There are also numerous commercial data
			analysis services that provide support for their customers.</p>
			
			<p>If your institution is interested in acquiring training or consulting services, please contact <a href="mailto:tools@polsys.net">tools@polsys.net</a>
			with an outline of your requirements to receive a quote.</p>
		</div>
	</div>
	
	
	<div class="rowTab">
		<div class="sectionTab"><h2>What are channel or video ids and how can I find them?</h2></div>
	</div>
	
	<div class="rowTab">
		<div class="fullTab">
			Many of the modules require a video or channel id as input. These can normally be found in the respective YouTube URLs.
			For example, in the URL https://www.youtube.com/watch?v=BNM4kEUEcp8 the strange code after the "=" sign is the video id.
			For further information, there's a lot of great material available on these things through a simple Google query.

		</div>
	</div>


	<div class="rowTab">
		<div class="sectionTab"><h2>The tool does not work (correctly)!</h2></div>
	</div>
	
	<div class="rowTab">
		<div class="fullTab">
			While this is very simple software, this can happen for all kinds of reasons.
	
			<p>High quality bug reports are much appreciated. If you have no experience with reporting bugs effectively, please read
			<a href="http://www.chiark.greenend.org.uk/~sgtatham/bugs.html" target="_blank">this piece</a> at least twice.
			TL;DR: developers need context to debug a tool, when filing a bug report, please add the URL of the call, the browser you are using, a
			screenshot of the interface output, the data files, and a description of what you have been doing and how the problem manifests itself. Without extensive
			information it can be very hard to replicate a problem and subsequently fix it.</p>
			
			<p>Please send bug reports to <a href="mailto:tools@polsys.net">tools@polsys.net</a>. I <strong>do not react</strong> to reports sent through any other channel.</p>
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
			I cannot make any guarantees, but if you send a feature request to <a href="mailto:tools@polsys.net">tools@polsys.net</a>, I will definitely consider it.
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