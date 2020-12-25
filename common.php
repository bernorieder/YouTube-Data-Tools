<?php

date_default_timezone_set('UTC');

// punctuation and stopword collections
$punctuation = array("\s","\.",",","!","\?",":",";","\/","\\","#","@","&","\^","\$","\|","`","~","=","\+","\*","\"","'","\(","\)","\]","\[","\{","\}","<",">","�");
$stopwords = array("a", "about", "above", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", "along", "already", "also","although","always","am","among", "amongst", "amoungst", "amount",  "an", "and", "another", "any","anyhow","anyone","anything","anyway", "anywhere", "are", "around", "as",  "at", "back","be","became", "because","become","becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom","but", "by", "call", "can", "cannot", "cant", "co", "con", "could", "couldnt", "cry", "de", "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight", "either", "eleven","else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred", "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own","part", "per", "perhaps", "please", "put", "rather", "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "very", "via", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves", "the");
	
$callcount = 0;

// some common functions
function doAPIRequest($url) {
	
	global $callcount, $apikey;

	$url .= "&key=" . $apikey;

	$callcount++;
	
	$run = true;
	$errorcount = 0;
	
	while($run == true) {
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$reply = curl_exec($ch);
		$info = curl_getinfo($ch);
				
		if($reply != false) {
			$run = false;
			$reply = json_decode($reply);
			if(isset($reply->error)) {
				if($reply->error->errors[0]->reason == "backendError") {
					echo("YouTube's API reported 'backendError'. The tool will wait and try again.");
					sleep(10);
					continue;
				} elseif($reply->error->errors[0]->reason == "notFound") {
					echo("YouTube's API reported 'notFound'. The tool will skip this item.");
					sleep(1);
					return $reply;
				} elseif($reply->error->errors[0]->reason != "subscriptionForbidden") {
					echo("The request failed. YouTube's API gave the following error: " . $reply->error->errors[0]->reason);
					exit;
				} else {
					return $reply;
				}
			} else {
				return $reply;
			}
		} else {
			$errorcount++;
			if($errorcount > 10) {
				echo("Too many connection errors.");
				exit;
			}
			sleep(1);
		}
	}
}


function testcaptcha($response) {
	
	global $secret;
	
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS,"secret=".$secret."&response=".$response);
	
	$reply = json_decode(curl_exec($ch));
	
	curl_close($ch);
	
	if($reply->success != 1) {
		echo "Recaptcha failed.";
		exit;
	} 
}

function writefile($filename,$content) {
	
	global $callcount;
	
	file_put_contents($filename,$content);
	
	$message = date("Y-m-d H:i:s") . "  " . $callcount . " " . get_client_ip_server() .  " " . $filename . "\n";
	
	file_put_contents("writefile.log", $message, FILE_APPEND);
}

function get_client_ip_server() {

    $ipaddress = '';

    if ($_SERVER['HTTP_CLIENT_IP'])
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if($_SERVER['HTTP_X_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if($_SERVER['HTTP_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if($_SERVER['HTTP_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if($_SERVER['REMOTE_ADDR'])
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
 
    return $ipaddress;
}
	
?>