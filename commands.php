<?php
/**
 * JavaScript / PHP Web Terminal
 * Adam Shannon
 * 2010-04-19
 */

// Todo
// - Create a function to parse flags and return the value.
//   You give it a a flag and the list of arguments and it
//   returns the value of the flag.
//   e.g. parse_flag('-f', array('-f Y:m:d', '-z American/Chichago'));
//   @return: Y:m:d

// Grab the whole command
$string = $_GET['string'];
$values = explode(' ', $string, 2);
	$values[0] = strtolower($values[0]);
	
	// Change the variable to be easy to use.
	$flags = $values[1];

/**
 * @name: parse_flag(String flag, Array flags)
 * This function will return the value of the given flag.
 *
 * @parm: String flag
 * @parm: Array flags
 * @return: String
 */
function parse_flag($flag, $flags) {
	
	// Build the regex to parse from.
	$flag_split_any = '/(\s+)?-([a-z0-9]+)\s/';
	
	if (is_array($flag)) {
		$flag_split = '/(\s+)?(';
		
		foreach ($flag as $f) {
				$flag_split .= $f . '|';
		}
		
		$flag_split = substr($flag_split, 0, strlen($flag_split) - 1) . ')\s/';
	} else {
		
		$flag_split = '/(\s+)?' . $flag . '\s/';
		
	}
	
	// Break apart and find the flag.
	$f = preg_split($flag_split, $flags);
		
		// Now break the string off at the next sign of a parameter start.
		$return = preg_split($flag_split_any, $f[1]);
		
		// print_r($return);
		
	return trim($return[0]);
	
}

	// Before we perform a server request check to see if we're 
	// parseing a flag for the client
	if (array_key_exists('parseFlag', $_GET)) {
		
		echo parse_flag(array($_GET['flag0'], $_GET['flag1']), $_GET['flags']);
		exit();
	}
	
	// Perform a server side request
	switch ($values[0]) {
		
		// A basic echo statement
		case 'echo':
		
			// Look for the help flag.
			if (preg_match('/(\s+)?--?help(\s+)?/', $flags) == 1) {
			
				// Show the help dialog and the supported carriers.
				echo '> Echo text back to the screen..' . "\n";
				echo '> The "-text" isn\'t required.' . "\n";
				echo '> Usage: echo -text' . "\n\n";
				exit();
				
			}
			
			echo '> ' . stripslashes($values[1]);
		
		break;
		
		
		// Show the UNIX Timestamp (UTC)
		case 'time':
		
			// Parse the timezone (-z)
			// $timezone = parse_flag('-z', $flags);
			
				// if (!isset($timezone)) {
					// $timezone = 'America/Chicago';
				// }
				
				// date_default_timezone_set($timezone);
				
			// Look for the help command
			if (preg_match('/(\s+)?--?help(\s+)?/', $flags) == 1) {
				
				echo '> Show the unix timestamp. ' . "\n";
				echo '> For timezones look here: http://php.net/manual/en/timezones.php' . "\n";
				echo '> Usage: time [-timezone]' . "\n";
				exit();
				
			}
			
			echo '> ' . @time() . ' (GMT)';
			
		break;
		
		
		// Show the date, with optional formats and timezones.
		case 'date':
		
			// Look for the help flag.
			if (preg_match('/(\s+)?--?help(\s+)?/', $flags) == 1) {
			
				// Show the help dialog and the supported carriers.
				echo '> Show the date with php\'s built in function.' . "\n";
				echo '> For date formats look here: http://php.net/manual/en/function.date.php' . "\n";
				echo '> For timezones look here: http://php.net/manual/en/timezones.php' . "\n";
				echo '> Usage: date [-timezone] [-format]' . "\n\n";
				exit();
				
			}
			
			// Parse the timezone (-z)
			$timezone = parse_flag(array('-z', '-timezone'), $flags);
				
				if (empty($timezone)) {
					$timezone = 'American/Chicago';
				}
				
				date_default_timezone_set($timezone);
			
			// Parse the format (-f)
			$format = parse_flag(array('-f', '-format'), $flags);
			
				if (empty($format)) {
					$format = 'Y-m-d';
				}
			
			echo '> ' . @date($format);
			
		break;
		
		// Parse a given url with php's build in function.
		case 'parseurl':
		
			// Look for the help flag.
			if (preg_match('/(\s+)?--?help(\s+)?/', $flags) == 1) {
			
				// Show the help dialog and the supported carriers.
				echo '> Parse a URI with PHP\'s built in function' . "\n";
				echo '> Usage: parseurl -url' . "\n\n";
				exit();
				
			}
			
			$url = parse_flag('-url', $flags);
			
				// If the user doesn't give a flag then just assume they only sent the url.
				if (empty($url)) {
					$url = $flags;
				}
			
			print_r(parse_url($url));
			
		break;
		
		// Get the whois information for a domain.
		case 'whois':
		
			// Look for the help command
			if (preg_match('/(\s+)?--?help(\s+)?/', $flags) == 1) {
				
				echo '> Get the WHOIS information for a given ur(i/l)' . "\n";
				echo '> Usage: whois -url' . "\n";
				
			} else {
				
				// Run the whois methods
				require_once('third_party/whois.class.php');
				
				/**
				 * @name: getwhois(String domain, String tld)
				 * Find the whois information for {@code: domain} with {@code: tld}.
				 *
				 * @parm: $doamin
				 * @parm: $tld
				 * @return: String
				 */
				function getwhois($domain, $tld) {
				
					$whois = new Whois();
					
					if( !$whois->ValidDomain($domain.'.'.$tld) ){
						echo 'Sorry, the domain is not valid or not supported.';
					}
					
					if( $whois->Lookup($domain.'.'.$tld) ) {
						return $whois->GetData(1);
					} else {
						echo 'Sorry, an error occurred.';
					}
				}
				
				// Parse the url and information.
				$domain = parse_flag('-url', $flags);
					
					if (empty($domain)) {
						$domain = $flags;
					}
					
					// Remove any http(s?)://
					$domain = str_replace(array('http://', 'https://'), '', $domain);
				
				$dot = strpos($domain, '.');
				$sld = substr($domain, 0, $dot);
				$tld = substr($domain, $dot+1);
				$whois = getwhois($sld, $tld);
				
				echo '> ' . $whois;
			
			}
			
		break;
		
		// Send and email.
		case 'mail':
			
			// Look for the help flag.
			if (preg_match('/(\s+)?--?help(\s+)?/', $flags) == 1) {
			
				// Show the help dialog and the supported carriers.
				echo '> Send an email' . "\n";
				echo '> Usage: mail -to -from -subject [-message] [-reply] [-cc] [-bcc]' . "\n\n";
				exit();
				
			} else {
			
				// First, parse the required flags
				$to = parse_flag(array('-t', '-to'), $flags);
				$from = parse_flag(array('-f', '-from'), $flags);
				$subject = parse_flag(array('-s', '-subject'), $flags);
				$message = parse_flag(array('-m', '-message'), $flags);
				
					// Check the required flags to go on.
					// Messages arn't required because of the useful "eom" subject-syntax.
					if (empty($to) || empty($from) || empty($subject)) {
						
						echo 'Error :: Required flags not filled in.';
						exit();
					}
					
					// Because validating email's with a regex is almost impossible, the 
					// fact that email's can be fake, and that this is a local script,
					// I'm only going to check for a basic pattern.
					$email_regex = '([a-z+\.0-9]{1,}+)@([a-z0-9\.]{1,}+)\.[a-z]{2,}';
					if (@preg_match($email_regex, $to) == 1) {
						echo '> Your TO email address doesn\'t seem to conform to the regex...';
					}
					
					if (@preg_match($email_regex, $from) == 1) {
						echo '> Your FROM email address doesn\'t seem to conform to the regex...';
					}
					
					$from = 'From: ' . $from . "\r\n";
				
				// Now grab the optional flags.
				$reply = parse_flag(array('-r', '-reply'), $flags);
				
					if (!empty($reply)) {
						$reply = 'Reply-To: ' . $reply . "\r\n";
					}
				
				$cc = parse_flag('-cc', $flags);
					
					if (!empty($cc)) {
						$cc = 'Cc: ' . $cc . "\r\n";
					}
				
				$bcc = parse_flag('-bcc', $flags);
				
					if (!empty($bcc)) {
						$bcc = 'Bcc: ' . $bcc . "\r\n";
					}
				
				// Format some of the data
				$message = wordwrap($message, 70);
				
				$mail = mail($to, $subject, $message, $from . $reply . $cc . $bcc);
				
					if ($mail == true) {
						echo '> Mail sent.';
					} else {
						echo '> An error in sending the mail has occoured.';
					}
			}
			
		break;
		
		// Get the resource from a given uri.
		case 'get':
		
			// Look for the help flag.
			if (preg_match('/(\s+)?--?help(\s+)?/', $flags) == 1) {
			
				// Show the help dialog and the supported carriers.
				echo '> Load the reference from an external resource (URI).' . "\n";
				echo '> Note, typing "-ur(i/l)" isn\'t needed.' . "\n";
				echo '> Usage: get -url [-timeout] [-[http]version] [-port] [-referer] [-useragent] [-user -pass]' . "\n\n";
				exit();
				
			}
			
			$url_bad = parse_flag(array('-uri', '-url'), $flags);
			
				if (empty($url)) {
					$url_bad = $flags;
				}
				
			// If a timeout flag exists respect it.
			$timeout = parse_flag(array('-t', '-timeout'), $flags);
				
				if (empty($timeout)) {
					$timeout = 20;
				}
				
			// Break apart the url at the first space.
			$url_fixed = explode(' ', $url_bad);
			$url = $url_fixed[0];
				
			// Setup the curl execution
			$ch = curl_init();
			
				// Check for an HTTP version flag
				$http_version = parse_flag(array('-httpversion', '-version'), $flags);
				
					if (!empty($http_version)) {
						curl_setopt($ch, CURLOPT_HTTP_VERSION, $http_version);
					}
					
				// Look to connect on a different port
				$port = parse_flag('-port', $flags);
				
					if (!empty($port)) {
						curl_setopt($ch, CURLOPT_PORT, $port);
					}
					
				// Manually set the referer
				$referer = parse_flag(array('-r', '-referer'), $flags);
				
					if (!empty($referer)) {
						curl_setopt($ch, CURLOPT_REFERER, $referer);
					}
					
				// Set a different useragent
				$user_agent = parse_flag(array('-ua', '-useragent'), $flagS);
				
					if (!empty($user_agent)) {
						curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
					}
					
				// Check for an HTTP auth sequence
				$user = parse_flag(array('-u', '-user'), $flags);
				$pass = parse_flag(array('-p', '-pass'), $flags);
				
					if (!empty($user) && !empty($pass)) {
						curl_setopt($ch, CURLOPT_USERPWD, $user . ':' . $pass);
					}
				
			
			curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_TIMEOUT, 2 * $timeout);
			curl_setopt($ch, CURLOPT_ENCODING, '');
			curl_setopt($ch, CURLOPT_REFERER, 'http:\/\/www.google.com\/');
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERAGENT, 'JavaScript / PHP Terminal');
				
			// Look for the headers flag.
			if (preg_match('/(\s+)?(-h|-headers)(\s+)?/', $flags) == 1) {
				
				// Show only the headers.
				curl_setopt($ch, CURLOPT_HEADER, true);
				curl_setopt($ch, CURLOPT_NOBODY, true);
				
				// Get the response
				$response = curl_exec($ch);
				
				echo "\nHeaders:\n" . $response;
				
			} else {
				
				// Get the response back so we can parse it.
				$response = curl_exec($ch);
				
					// Remove unneeded tags (and their content)
					// $response = strip_tags($response, '<a><article><aside><div><p><span>');
					
					// This is a useful way to strip tags.
					// {@Credit: http://davebrooks.wordpress.com/2009/04/22/php-preg_replace-some-useful-regular-expressions/}
					$response = preg_replace (
						array(
						// Remove invisible content
						'@<head[^>]*?>.*?</head>@siu',
						'@<style[^>]*?>.*?</style>@siu',
						'@<script[^>]*?.*?</script>@siu',
						'@<object[^>]*?.*?</object>@siu',
						'@<embed[^>]*?.*?</embed>@siu',
						'@<applet[^>]*?.*?</applet>@siu',
						'@<noframes[^>]*?.*?</noframes>@siu',
						'@<noscript[^>]*?.*?</noscript>@siu',
						'@<noembed[^>]*?.*?</noembed>@siu',
						// Add line breaks before & after blocks
						'@<((br)|(hr))@iu',
						'@</?((address)|(blockquote)|(center)|(del))@iu',
						'@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
						'@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
						'@</?((table)|(th)|(td)|(caption))@iu',
						'@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
						'@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
						'@</?((frameset)|(frame)|(iframe))@iu',),
						array(
						' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
						"\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
						"\n\$0", "\n\$0",)
					,$response);
					
					// {@End Credit}
					
					// Replace some tags with extra line feeds
					$response = preg_replace('/<h[1-6]{1}>/', "{LINEFEED}", $response);
					$response = preg_replace('/<br(\s\/)?>/', "{LINEFEED}", $response);
						
					// Remove all remaining tags and comments and return.
					$response = strip_tags($response);
					
					// Remove extra whitespace.
					$response = trim(preg_replace('/(\n|\r|\t){2,}/', "{LINEFEED}{LINEFEED}", $response));
					$response = preg_replace('/\{LINEFEED\}/', "\n", $response);
					
				echo wordwrap($response, 75);
				
			}
			
		break;
		
		// Generate random numbers
		case 'random':
		
			// Look for the help flag.
			if (preg_match('/(\s+)?--?help(\s+)?/', $flags) == 1) {
			
				// Show the help dialog and the supported carriers.
				echo '> Generate a random number' . "\n";
				echo '> Usage: random [-min] [-max] [-add] [-multiply] [-count]' . "\n\n";
				exit();
				
			}
			
			// Check for a number to add to the response(s).
			$add = parse_flag(array('-a', '-add'), $flags);
			
				if (empty($add)) {
					$add = 0;
				}
				
			// Look for a number to multiply by.
			$multiply = parse_flag(array('-m', '-multiply'), $flags);
			
				if (empty($multiply)) {
					$multiply = 1;
				}
				
			// Now look for how many numbers to generate
			$count = parse_flag(array('-c', '-n', '-count'), $flags);
			
				if (empty($count)) {
					$count = 1;
				}
				
			// Look for min and max values
			$min = parse_flag(array('-l', '-min'), $flags);
			$max = parse_flag(array('-h', '-max'), $flags);
				
				if (empty($min)) {
					$min = 0;
				}
				
				if (empty($max)) {
					$max = 100000;
				}
				
			// Generate the numbers
			for ($n = 0; $n < $count; $n++) {
				echo '> ' . ((mt_rand($min, $max) * $multiply) + $add);
				
					if ($count > 1) { echo "\n"; }
			}
			
		break;
		
		// Generate a random string (used for passwords)
		case 'pass':
		
			// Look for the help flag.
			if (preg_match('/(\s+)?--?help(\s+)?/', $flags) == 1) {
			
				// Show the help dialog and the supported carriers.
				echo '> This command will generate a pseudorandom password.' . "\n";
				echo '> Usage: pass [-alpha] [-numeric] [-symbols] [-length] [-count] ' . "\n\n";
				exit();
				
			}
			
			// Establish the character strings.
			$alpha = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$numeric = '0123456789';
			$symbols = '`~!@#$%^&*()_+-=[]{};:,.?';
			$pool = null;
			$all = true;
			
			// Add to the pool
			if (preg_match('/(\s+)?-(a|alpha)(\s+)?/', $flags) == 1) {
				$pool .= $alpha;
				$all = false;
			}
			
			if (preg_match('/(\s+)?-(n|numeric)(\s+)?/', $flags) == 1) {
				$pool .= $numeric;
				$all = false;
			}
			
			if (preg_match('/(\s+)?-(s|symbol|symbols)(\s+)?/', $flags) == 1) {
				$pool .= $symbols;
				$all = false;
			}
			
			if ($all == true) {
				$pool = $alpha . $numeric . $symbols;
			}

			// Find out how long the password should be.
			$length = parse_flag(array('-l', '-length'), $flags);
			
				if (empty($length)) {
					$length = 20;
				}
				
			// Find out how many passwords to make
			$count = parse_flag(array('-c', '-count'), $flags);
			
				if (empty($count)) {
					$count = 1;
				}
			
			// Now generate the random passwords
			$pool_len = strlen($pool) - 1;
			$pass = null;
		
			for ($n = 0; $n < $count; $n++) {
				for ($i = 0; $i < $length; $i++) {
					$pass .= substr($pool, mt_rand(0, $pool_len), 1);
				}
				
				echo '> ' . addslashes($pass) . "\n";
				$pass = null;
			}
			
		break;
		
		// Send an SMS message
		case 'sms':
			
			/**
			 * http://en.wikipedia.org/wiki/List_of_carriers_providing_SMS_transit
			 */
			$carriers = array(
				'att' => '@txt.att.net',
				'att-mms' => '@mms.att.net',
				'boost' => '@myboostmobile.com',
				'cingular' => '@cingular.com',
				'nextel' => '@messaging.nextel.com',
				'nextel-mx' => '@messaging.nextel.com.mx',
				'qwest' => '@qwestmp.com',
				'sprint' => '@messaging.sprintpcs.com',
				'sprint-mms' => '@pm.sprint.com',
				'uscellular' => '@email.uscc.net',
				'uscellular-mms' => '@mms.uscc.net',
				'verizon' => '@vtext.com',
				'verizon-mms' => '@vzwpix.com',
			);
			
			// Look for the help flag.
			if (preg_match('/(\s+)?--?help(\s+)?/', $flags) == 1) {
			
				// Show the help dialog and the supported carriers.
				echo '> This command will send an email message to a carrier' . "\n";
				echo '> which will deliver an SMS to the phone.' . "\n";
				echo '> Usage: sms -to -carrer -from -message' . "\n\n";
				echo '> Supported Carriers:' . "\n";
				print_r($carriers);
				exit();
				
			}
		
			// Get the required flags
			$to = parse_flag(array('-t', '-to'), $flags);
			$from = parse_flag(array('-f', '-from'), $flags);
			$message = parse_flag(array('-m', '-msg', '-message'), $flags);
			$carrier = parse_flag(array('-c', '-carrier', '-company'), $flags);
			
				// Verify that the carrier is in the list
				if (array_key_exists($carrier, $carriers) == false) {
					echo 'Carrier isn\'t supported.';
					exit();
				}
				
			// Otherwise, send the email
			$mail = mail($to . $carriers[$carrier], '', $message, 'From: ' . $from);
			
				if ($mail == true) {
					echo '> Message Sent';
				} else {
					echo '> Message Failed to Send.';
				}
			
		break;
		
		// Show the md5 for a string
		// TODO: allow a (-f[ile]) flag which will get an external file and send the md5.
		case 'md5':
			
			// Get the string to hash
			$string = parse_flag(array('-s', '-string'), $flags);
			
				if (empty($string)) {
					$string = $flags;
				}
				
				echo '> ' . md5($string);
			
		break;
		
		// Show the sha1 for a string
		// TODO: allow a (-f[ile]) flag which will get an external file and send the sha1.
		case 'sha1':
			
			// Get the string
			$string = parse_flag(array('-s', '-string'), $flags);
			
				if (empty($string)) {
					$string = $flags;
				}
				
				echo '> ' . sha1($string);
			
		break;
		
		// Show an error by default.
		default:
			
			echo 'Error';
			
		break;
		
	}