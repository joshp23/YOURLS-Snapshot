<?php
/*
Plugin Name: Snapshot: Visual URL Preview
Plugin URI: https://github.com/joshp23/YOURLS-snapshot
Description: Preview plugin with an image Cahche
Version: 1.2.2
Author: Josh Panter <joshu@unfettered.net>
Author URI: https://unfettered.net
*/
// No direct call
if( !defined( 'YOURLS_ABSPATH' ) ) die();
// Add the admin page
yourls_add_action( 'plugins_loaded', 'snapshot_add_page' );
function snapshot_add_page() {
        yourls_register_plugin_page( 'snapshot', 'Snapshot', 'snapshot_do_page' );
}
function snapshot_do_page() {

	// Check if a form was submitted
	snaphsot_form_0();
	snaphsot_form_1();
	snaphsot_form_2();
	
	// Get the options and set defaults if needed
	$opt = snapshot_config();
	
	// Create nonce
	$nonce = yourls_create_nonce( 'snapshot' );
	
	// some values necessary for display
	if ( $opt[7] == 'jpg' ) {
		$is_png = null;
		$is_jpg = 'checked';
	} else {
		$is_png = 'checked';
		$is_jpg = null;
	}
	
	$url_convert = YOURLS_URL_CONVERT;
	if( $url_convert == 62 ) {
		$trigger_scope = "any <strong>non</strong> alphanumeric character";
	} else {
		$trigger_scope = "any upper case letter and non-alphanumeric chacater";
	}
	
	$me 	  = $_SERVER['HTTP_HOST'];
	$me_parts = explode('.', $me);
		$me_0 = $me_parts[0];
		$me_1 = $me_parts[1];
	$myself   = YOURLS_SITE;
	$myKey    = yourls_auth_signature();
	$cronEX   =  rawurlencode('<html><body><pre>0 0 * * 0 wget -O - -q -t 1 "<strong>' . $myself . '</strong>/yourls-api.php" --post-data "signature=?<strong>'. $myKey .'</strong>&action=cflush&age=3&mod=weeks" >/dev/null 2>&1</pre></body></html>');
	$cstat 	  = snapshot_cache_stats();
	
	echo <<<HTML
		<div id="wrap">
			<div id="tabs">
				<div class="wrap_unfloat">
					<ul id="headers" class="toggle_display stat_tab">
						<li class="selected"><a href="#stat_tab_behavior"><h2>Snapshot Config</h2></a></li>
						<li><a href="#stat_tab_screen"><h2>Screen\PhantomJS Settings</h2></a></li>
						<li><a href="#stat_tab_cache"><h2>Cache</h2></a></li>
					</ul>
				</div>

				<div id="stat_tab_behavior" class="tab">

					<form method="post">
			
						<h3>Trigger Character</h3>
						<p>
							<label for="snapshot_char">Trigger character </label> 
							<input type="text" size=3 id="snapshot_char" name="snapshot_char" value="$opt[0]" />
						</p>
						<p>Snapshot will look for a trailing chacter (or character pattern) to trigger the preview page.</p>
						<p><strong>Example:</strong> <code>https://$me/V$opt[0]</code>  will trigger the preview for the short url "V", where <code>https://$me/V</code> is the usual path.</p>
				
						</p>You are using the <strong>base $url_convert</strong> character set to encode your short urls, so you can use $trigger_scope as the trigger character, the default is "<code>~</code>"</p>
						
						<input type="hidden" name="nonce" value="$nonce" />
						<p><input type="submit" value="Submit" /></p>
					</form>
					<hr>
					<h3>Subdomain example</h3>
					
					<p>This explains how to configure an Apache web server to use a subdomain, such as <code>https://preview.$me</code> instead of <code>https://$me/V$opt[0]</code> to display your preview pages.</p>
					
					<h4>Part one: The virtual host</h4>
					
					<p>To use a subdomain for previews, we first need to add it in to the YOURLS virtual host conf file in Apache using the <code>ServerAlias</code> directive. Your conf file should look something like the following; notice that we are making use of the subdomain "<code>preview</code>".</p>
<pre>
&#60;VirtualHost *:80&#62;

	ServerName $me
	<strong>ServerAlias preview.$me</strong>
	
	DocumentRoot /var/www/YOURLS/
	&#60;Directory /var/www/YOURLS/&#62;
		Options -Indexes +FollowSymLinks +MultiViews
		AllowOverride All
		Order allow,deny
		allow from all
	&#60;/Directory&#62;

	# Possible values include: debug, info, notice, warn, error, crit,
	# alert, emerg.
	LogLevel info
	ErrorLog /var/log/apache2/error.log
	CustomLog /var/log/apache2/access.log combined
	
&#60;/VirtualHost&#62;
</pre>
					<p>You might find your Apache default virtual host config file at <code>/etc/apache2/sites-available/000-default.conf</code>.</p>
					<p>Once you have made your adjustments make sure to save the file and restart Apache.</p>
					
					<h4>Part Two: YOURLS .htaccess file</h4>
					
					<p>The following rules need to be added in to the very top of the YOURLS .htaccess file. They make use of both <code>mod_rewrite</code> and <code>mod_proxy</code>, so both of these modules need to be enabled on your server.
<pre>
RewriteEngine On

# SNAPSHOT - PREVIEW
RewriteCond %{HTTP_HOST} ^<strong>preview</strong>\.(<strong>$me_0</strong>\.<strong>$me_1</strong>)$ [NC]
RewriteRule ^/?([a-zA-Z0-9]+)$ https://%1/$1<strong>$opt[0]</strong> [P]
</pre>
				<p>These rules have been generated using this site's current configuration. Any changes to your setup will necessitate an alteration of these rules in your system.</p>
				<p><strong>NOTE:</strong> If you are using SSL on your site, make certain to set <code>SSLProxyEngine on</code> in your virtual host, otherwise these proxies will fail. Else, you will have to adjust the above code accordingly.</p>
				</div>

				<div id="stat_tab_screen" class="tab">
				
					<form method="post">
					
						<h3>PhantomJS binary config</h3>
						
						<div style="padding-left: 10pt;">
						
							<h4>Path:</h4>
							
							<div style="padding-left: 10pt;">
								<p>
									<input type="text" size=20 id="snapshot_phantomjs_path" name="snapshot_phantomjs_path" value="$opt[1]" />
								</p>
								<p><strong>Example:</strong> enter <code>/usr/bin/</code> if you find your binary at <code>/usr/bin/phantomjs</code> <small> This is the correct value if you installed phantomjs via apt in Ubuntu</small></p>
								
							</div>
							
							<h4>Timeout:</h4>
							
							<div style="padding-left: 10pt;">
								<p>
									<input type="text" size=6 id="snapshot_timeout" name="snapshot_timeout" value="$opt[2]" />
								</p>
								<p>Defines the timeout after which any resource requested will stop trying and proceed with other parts of the page.</p>
								<p><strong>Example:</strong> <code>3000</code> will timeout at 3 seconds.</p>
							</div>
						</div>
						<hr>
						<h3>Image Settings</h3>
						 
						<div style="padding-left: 10pt;">
						
							<h4>Viewport Size:</h4>
							
							<div style="padding-left: 10pt;">
								<p>
									<input type="text" size=4 id="snapshot_img_w" name="snapshot_img_w" value="$opt[3]" /> x
									<input type="text" size=4 id="snapshot_img_h" name="snapshot_img_h" value="$opt[4]" />
								</p>
								<p>The dimensions of the virtual browser screen <small>Good to be the same as Clip.</small></p>
							</div>
							
							<h4>Image Clip:</h4>
							
							<div style="padding-left: 10pt;">
								<p>
									<input type="text" size=4 id="snapshot_clip_w" name="snapshot_img_w" value="$opt[5]" /> x
									<input type="text" size=4 id="snapshot_clip_h" name="snapshot_img_h" value="$opt[6]" />
								</p>
								<p>This will clip the output image, resulting in an image file with the above dimensions.</p>
							</div>
							
							<h4>Type:</h4>
							
							<div style="padding-left: 10pt;">
								<p>
				 					<input type="hidden" name="snapshot_img_type" value="jpg">
									<input type="radio" name="snapshot_img_type" value="jpg" $is_jpg > jpg<br>
									<input type="radio" name="snapshot_img_type" value="png" $is_png > png
								</p>
							</div>
							
							<h4>Capture Delay:</h4>
							
							<div style="padding-left: 10pt;">
								<p>
									<input type="text" size=6 id="snapshot_delay" name="snapshot_delay" value="$opt[8]" />
								</p>
								<p>Amount of time to wait after opening a page befor rendering an image, typically to allow scripts to load on the target page.</p>
								<p><strong>Example:</strong> <code>1500</code> will timeout at 1.5 seconds.</p>
							
							</div>
						</div>
						<hr>
						<h3>Cache Settings</h3>
						
						<div style="padding-left: 10pt;">
						
							<h4>Location</h4>
							
							<div style="padding-left: 10pt;">
								<p>
									<input type="text" size=20 id="snapshot_cache_path" name="snapshot_cache_path" value="$opt[9]" /><small> This folder needs to be manually created, and must be writable by your webserver. ie <code>chmod 777</code></small>
								</p>
								<p>The default location is in the YOURLS root, ie <code>/path/to/www/YOURLS/cache/</code></p>
							</div>
							
							<h4>Expiration</h4>
							
							<div style="padding-left: 10pt;">
								<p>
									Replace an image when it is older than 
									<input type="text" size=4 id="snapshot_cache_expire" name="snapshot_cache_expire" value="$opt[10]" />
									<select name="snapshot_cache_expire_mod">
										<option value="$opt[11]" selected >Select One</option>
										<option value="min">Minutes</option>
										<option value="hours">Hours</option>
										<option value="days">Days</option>
										<option value="weeks">Weeks</option>
									</select> <small> Currently $opt[11]</small>.
								</p>
								<p>This can be helpful in case of frequent requests.</p>
								
							</div>
						</div>
						
						<input type="hidden" name="nonce" value="$nonce" />
						<p><input type="submit" value="Submit" /></p>
					</form>
				</div>
				
				<div id="stat_tab_cache" class="tab">
				
					<h3>Status</h3>
						
					<p>Currently the cache consists of $cstat[1] files totaling $cstat[0], with $cstat[3] remaining on your $cstat[2] drive.
					
					<h3>Flush Cache</h3>
					<form method="post">
						<p>Delete any files order than 
						<input type="hidden" name="snapshot_flush_age" value="0" />
						<input type="text" size=2 id="snapshot_flush_age" name="snapshot_flush_age" />
						<select name="snapshot_flush_age_mod">
							<option value="min">Minutes</option>
							<option value="hours">Hours</option>
							<option value="days" selected >Days</option>
							<option value="weeks">Weeks</option>
						</select> <small>Leave empty to flush all</small></p>
						<div class="checkbox">
						  <label>
						    <input name="snapshot_cache_flush_do" type="hidden" value="no" >
						    <input name="snapshot_cache_flush_do" type="checkbox" value="yes" > Really Flush?
						  </label>
						</div>
						<br>
						<input type="hidden" name="nonce" value="$nonce" />
						<p><input type="submit" value="Submit" /></p>
					</form>
					<hr>
					<h3>API</h3>
					
					<p>Snapshot exposes an API call, <code>cflush</code>, that can be used to flush the cache. To use it, send an API request to <code>$myself/yourls-api.php</code> via POST (reccomended) or GET using the following parameter:</p>
					
					<ul>
						<li><code>action=cflush</code></li>
					</ul>
					
					<p>This call will default to your Cache Expiration settings, currently: $opt[10] $opt[11].
					<p>You can override the default with the following parameters:</p>
					<ul>
						<li><code>age=</code>VALUE</li>
						<li><code>mod=</code>VALUE</li>
						<ul>
							<li><code>weeks</code></li>
							<li><code>days</code></li>
							<li><code>hours</code></li>
							<li><code>min</code></li>
						</ul>
					</ul>
					<p><strong>Example</strong>: You could send a GET request like so: <code>$myself/yourls-api.php?signature=$myKey&action=cflush&age=1&mod=weeks</code> in order to flush any images older than 1 week.</p>
					
					<p>Please refer to the <a href="$myself/readme.html#API" target="-blank" >API documentation</a> for more information on YOURLS API requests.</p>
					
					<h3>Auto-Flush</h3>
					
					 <p>This API can be used with a <code>cron</code> job in order to set up an Auto-Flush feature, allowing you to easily keep your cache in check according to your own settings.</p>	
					 <p><strong>Example:</strong> The following <code>cron</code> call, which flushes your cache of files that are more than 3 weeks old, will run every Sunday at midnight:</p>
					 <iframe src="data:text/html;charset=utf-8,$cronEX" width="100%" height="51"/></iframe>

					<p>Look here for more info on <a href="https://help.ubuntu.com/community/CronHowto" target="_blank" >cron</a> and <a href="https://www.gnu.org/software/wget/manual/html_node/HTTP-Options.html" target="_blank">wget</a>.</p>
					<p><strong>NOTE</strong>: The examples on this page have been pre-formatted to work with this site.</p>
				</div>
			</div>
		</div>
HTML;
}
// CSS for YOURLS style preview page and plugin config
yourls_add_action('html_head', 'img_css');
function img_css(){
	$img_css = file_get_contents( dirname( __FILE__ ) . '/assets/preview.css');
	echo '<style>' . $img_css . '</style>';
	echo '<link rel="stylesheet" href="/css/infos.css" type="text/css" media="screen" />';
	echo '<script src="/js/infos.js" type="text/javascript"></script>';
}
// Get options and set defaults
function snapshot_config() {

	// Get values from DB
	$char	 = yourls_get_option( 'snapshot_char' );
	$binPath = yourls_get_option( 'snapshot_phantomjs_path' );
	$timeOut = yourls_get_option( 'snapshot_timeout' );
	$img_w	 = yourls_get_option( 'snapshot_img_w' );
	$img_h	 = yourls_get_option( 'snapshot_img_h' );
	$clip_w	 = yourls_get_option( 'snapshot_clip_w' );
	$clip_h	 = yourls_get_option( 'snapshot_clip_h' );
	$imgType = yourls_get_option( 'snapshot_img_type' );
	$delay 	 = yourls_get_option( 'snapshot_delay' );
	$cache	 = yourls_get_option( 'snapshot_cache_path' );	
	$cacheX	 = yourls_get_option( 'snapshot_cache_expire' );
	$cacheXM = yourls_get_option( 'snapshot_cache_expire_mod' );
	
	// Set defaults if necessary
	if( $char	== null ) $char 	= '~';
	if( $binPath 	== null ) $binPath 	= '/usr/bin/';
	if( $timeOut 	== null ) $timeOut 	= '3000';		// 3 seconds
	if( $img_w 	== null ) $img_w 	= '800';
	if( $img_h 	== null ) $img_h 	= '640';
	if( $clip_w	== null ) $clip_w 	= '800';
	if( $clip_h  	== null ) $clip_h 	= '640';
	if( $imgType 	== null ) $imgType 	= 'jpg';
	if( $delay	== null ) $delay 	= '1500';		// 1.5 seconds
	if( $cache 	== null ) $cache 	= 'cache';
	if( $cacheX	== null ) $cacheX 	= '1';
	if( $cacheXM 	== null ) $cacheXM 	= 'hours';
	
	return array(
	$char,		// opt [0]
	$binPath,	// opt [1]
	$timeOut,	// opt [2]
	$img_w,		// opt [3]
	$img_h,		// opt [4]
	$clip_w,	// opt [5]
	$clip_h,	// opt [6]
	$imgType,	// opt [7]
	$delay,		// opt [8]
	$cache,		// opt [9]
	$cacheX	,	// opt [10]
	$cacheXM	// opt [11]
	);
}
// Check for form 0 - Main
function snaphsot_form_0() {
	if( isset( $_POST['snapshot_char'] ) ) {
		// Check nonce
		yourls_verify_nonce( 'snapshot' );
		yourls_update_option( 'snapshot_char', $_POST['snapshot_char'] );
	}
}
// Check for form 1 - Screen\PhantomJS
function snaphsot_form_1() {
	if( isset( $_POST['snapshot_phantomjs_path'] ) ) {
		// Check nonce
		yourls_verify_nonce( 'snapshot' );
		yourls_update_option( 'snapshot_phantomjs_path', $_POST['snapshot_phantomjs_path'] );
		if(isset($_POST['snapshot_timeout'])) yourls_update_option( 'snapshot_timeout', $_POST['snapshot_timeout'] );
		if(isset($_POST['snapshot_img_w'])) yourls_update_option( 'snapshot_img_w', $_POST['snapshot_img_w'] );
		if(isset($_POST['snapshot_img_h'])) yourls_update_option( 'snapshot_img_h', $_POST['snapshot_img_h'] );
		if(isset($_POST['snapshot_clip_w'])) yourls_update_option( 'snapshot_clip_w', $_POST['snapshot_clip_w'] );
		if(isset($_POST['snapshot_clip_h'])) yourls_update_option( 'snapshot_clip_h', $_POST['snapshot_clip_h'] );
		if(isset($_POST['snapshot_img_type'])) yourls_update_option( 'snapshot_img_type', $_POST['snapshot_img_type'] );
		if(isset($_POST['snapshot_delay'])) yourls_update_option( 'snapshot_delay', $_POST['snapshot_delay'] );
		if(isset($_POST['snapshot_cache_path'])) yourls_update_option( 'snapshot_cache_path', $_POST['snapshot_cache_path'] );
		if(isset($_POST['snapshot_cache_expire'])) yourls_update_option( 'snapshot_cache_expire', $_POST['snapshot_cache_expire'] );
		if(isset($_POST['snapshot_cache_expire_mod'])) yourls_update_option( 'snapshot_cache_expire_mod', $_POST['snapshot_cache_expire_mod'] );
	}
}
// Check for form 2 - Flush
function snaphsot_form_2() {

	// was the flush form submitted?
	if( isset( $_POST['snapshot_cache_flush_do'] ) ) {
		// was the checkbox ticked?
		if( $_POST['snapshot_cache_flush_do'] !== 'no' ) {
			// check the age limit and modifier - these will always be set
			$age = $_POST['snapshot_flush_age'];	 // defaults to 0. ie, anything older than 0 will be deleted
			$mod = $_POST['snapshot_flush_age_mod'];
			snapshot_age_mod($age, $mod);
			
			// Check nonce
			yourls_verify_nonce( 'snapshot' );
			snapshot_cache_flush($age);
		} else {
			echo 'You submitted the Flush Cache without checking "Really Flush"';
		}
	}
}
// Adjust human readable time into seconds
function snapshot_age_mod($age, $mod) {
	switch ($mod) {
		case 'weeks': 
			$age = $age * 7 * 24 * 60 * 60;
			break;
		case 'days':
			$age = $age * 24 * 60 * 60;
			break;
		case 'hours':
			$age = $age * 60 * 60;
			break;
		case 'min':
			$age = $age * 60;
			break;
		default:
			$age = $age;
	}
	return $age;
}
// Handle failed loader request and check for trigger character
yourls_add_action( 'loader_failed', 'snapshot_loader_failed' );
function snapshot_loader_failed( $args ) {

	$opt = snapshot_config();
	
	// Check for a match
	$request = $args[0];
	$pattern = yourls_make_regexp_pattern( yourls_get_shorturl_charset() );
	if( preg_match( "@^([$pattern]+)".$opt[0]."$@", $request, $matches ) ) {
		$keyword = isset( $matches[1] ) ? $matches[1] : '';
		$keyword = yourls_sanitize_keyword( $keyword );
		snapshot_show( $keyword );
		die();
	}
}
// Page router TODO
// Show the template page TODO
// Show the YOURLS style preview page
function snapshot_show( $keyword ) {

	// If the keyword is in the database, then proceed
	if (yourls_keyword_is_taken( $keyword ) == true) {
	
		// set variables for the page draw
		$me 	= $_SERVER['HTTP_HOST'];
		$title 	= yourls_get_keyword_title( $keyword );
		$url   	= yourls_get_keyword_longurl( $keyword );
		$base  	= YOURLS_SITE;
		$l_ico 	= yourls_get_favicon_url( $url );
		$s_ico 	= yourls_get_favicon_url( $me );
		$img 	= snapshot_request($keyword, $url);
		
		// draw the preview page
		require_once( YOURLS_INC.'/functions-html.php' );
		yourls_html_head( 'preview', 'Short URL preview' );
		yourls_html_logo();
		echo <<<HTML
				<h2>Short Link &rArr; Long Link: $title</h2>
				<p><img src="$s_ico" /> <strong><a href="$base/$keyword">$me/$keyword</a> &rArr;</strong> <img src="$l_ico" /> <strong><a href="$base/$keyword">$url</a></strong></p>
				<div id="live_p">
					<img border=1 $img />	
				</div>
				<p>To visit this link, please <strong><a href="$base/$keyword">click here</a></strong>.</p>
				<p>Thank you.</p>
HTML;
		yourls_html_footer();
		
	// If the keyword is not in the database, return to index
	} else {
		yourls_redirect( $base, 302 );
		die();
	}
}
// process image request - check cache
function snapshot_request($keyword, $url) { 

	$opt 	= snapshot_config();
	$age 	= $opt[10];
	$mod 	= $opt[11];
	$target = $opt[9] . '/' . md5($keyword) . '.' . $opt[7];
	
	// calculate/set cachetimes
	if (file_exists($target)) { 
		$filetime	= filemtime($target);
		$cachetime	= time()-$filetime-snapshot_age_mod($age, $mod);
	} else {
		$cachetime	= -1;
	}
	
	// get a new image if there is no fresh cache file
	if (!file_exists($target) || $cachetime>=0) {
		$pic = snapshot_screen($keyword, $url);
	} else {
		$pic = 'src="' . $target . '" width="560"';
	}
	
	// cleanup and return the image value
	clearstatcache();
	return $pic;
}
// Get a new screenshot
function snapshot_screen($keyword, $url) {

	$opt 	 = snapshot_config();
	$keyword = md5($keyword);
	
	require_once( 'screen/autoload.php');

	$screenCapture = new Screen\Capture($url);
	$screenCapture->binPath = ($opt[1]);
	$screenCapture->setOptions([
    'ignore-ssl-errors' => 'yes',
	]);
	$screenCapture->setTimeout($opt[2]);
	$screenCapture->setWidth($opt[3]);
	$screenCapture->setHeight($opt[4]);
	$screenCapture->setClipWidth($opt[5]);
	$screenCapture->setClipHeight($opt[6]);
	$screenCapture->setImageType($opt[7]);
	$screenCapture->setDelay($opt[8]);
	$screenCapture->output->setLocation($opt[9]);
	try {		
		$screenCapture->save($keyword);
		$screenCapture->jobs->clean();
		return 'src="' . $opt[9] . '/' . $keyword . '.' . $opt[7] . '" width="560"';
	} catch (Exception $e) {
		$img = yourls_plugin_url( dirname( __FILE__ ) . '/assets/sorry.png');
		return 'src="' . $img . '" width="420"';
	}
	
}
/*
	CACHE FUNCTIONS
*/
// flush
function snapshot_cache_flush($age) {

	$cache	 = yourls_get_option( 'snapshot_cache_path' );
	if( $cache 	 == null ) $cache 	= 'cache';
	$dir = $_SERVER['DOCUMENT_ROOT'] . '/' . $cache;
	$now = time();
	
	if (file_exists($dir)) {
		echo 'cache is found';
		foreach (new DirectoryIterator($dir) as $fileInfo) {
		    if ($fileInfo->isDot()) {
		    continue;
		    }
		    if ($now - $fileInfo->getCTime() >= $age) {
		        unlink($fileInfo->getRealPath());
		    }
		}
		echo '<font color="green">Cache has been flushed, have a nice day.<font>';
	}
}
// api flush
yourls_add_filter( 'api_action_cflush', 'snapshot_cache_flush_api' );
function snapshot_cache_flush_api() {

	$opt =  snapshot_config();
	// get values from request/set defaults
	$age = ( isset( $_REQUEST['age'] ) ? $_REQUEST['age'] : $opt[10] );
	$mod = ( isset( $_REQUEST['mod'] ) ? $_REQUEST['mod'] : $opt[11] );
	// calculate and destroy
	snapshot_age_mod($age, $mod);
	if( snapshot_cache_flush($age) ) {
		return array(
			'statusCode' => 200,
			'simple'     => "Cache has been flushed",
			'message'    => 'success: flushed',
		);
	} else {
		return array(
			'statusCode' => 500,
			'simple'     => 'Error: could not flush cache, not sure why :-/',
			'message'    => 'error: unknown error',
		);
	}
}
// get cache disk use data
function snapshot_cache_stats() {

	$cache = yourls_get_option( 'snapshot_cache_path' );
	if( $cache == null ) $cache = 'cache';
	$dir   = $_SERVER['DOCUMENT_ROOT'] . '/' . $cache;
	
	$size	= snapshot_cache_size($dir);
	$pop	= snapshot_cache_pop($dir);
	$total	= disk_total_space($dir);
	$total	= snapshot_format_size($total);
	$remain = disk_free_space($dir);
	$remain = snapshot_format_size($remain);
	
	return array(
		$size,		// cstat[0]
		$pop,		// cstat[1]
		$total,		// cstat[2]
		$remain		// cstat[3]
	);
}
// population data
function snapshot_cache_pop($dir) {

	$fi = new FilesystemIterator($dir, FilesystemIterator::SKIP_DOTS);
	return iterator_count($fi);
}
// total size
function snapshot_cache_size($dir) {
    	
    $bytestotal = 0;
    $dir = realpath($dir);
    if( $dir !== false ){
        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)) as $object){
            $bytestotal += $object->getSize();
        }
    }
    $bytestotal = snapshot_format_size($bytestotal);
    return $bytestotal;
}
// format size into human readable number
function snapshot_format_size($bytes){ 
	$kb = 1024;
	$mb = $kb * 1024;
	$gb = $mb * 1024;
	$tb = $gb * 1024;

	if (($bytes >= 0) && ($bytes < $kb)) {
		return $bytes . ' B';

	} elseif (($bytes >= $kb) && ($bytes < $mb)) {
		return ceil($bytes / $kb) . ' KB';

	} elseif (($bytes >= $mb) && ($bytes < $gb)) {
		return ceil($bytes / $mb) . ' MB';

	} elseif (($bytes >= $gb) && ($bytes < $tb)) {
		return ceil($bytes / $gb) . ' GB';

	} elseif ($bytes >= $tb) {
		return ceil($bytes / $tb) . ' TB';
	} else {
		return $bytes . ' B';
	}
}
// Delete cached image on keyword delete
yourls_add_action( 'delete_link', 'delete_snapshot_cache_img' );
function delete_snapshot_cache_img( $args ) {
	
	$opt 	 = snapshot_config();
	$dir 	 = $_SERVER['DOCUMENT_ROOT'] . '/' . $opt[9];
    	$keyword = $args[0];
    	
    	$target  = $dir . '/' . md5($keyword) . '.' . $opt[7];
	if (file_exists($target)) unlink($target);
}