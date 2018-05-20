<?php

/*
 * Plugin Name: WP Reactions Box
 * Description: Real-time emotion/mood rating plugin which allows visitor to rate individual post though smilie icons.
 * Version: 1.0
 * License:  GNU General Public License 3.0 or newer (GPL) http://www.gnu.org/licenses/gpl.html
 * Author: Deepak Kumar
 */


define('HKP_FEELBOX_PLUGIN_DIR', plugins_url(basename(dirname(__FILE__))));

global $hkp_reactions_db_version;
global $moods;
global $hkp_reactions_options;
global $themes;

$moods = array(
	1 => "bummer",
	2 => "good",
	3 => "sad",
	4 => "lol",
	5 => "scary",
	6 => "shocked",
	7 => "boring",
	8 => "sweet",
	9 => "angry",
	10 => "nerdy",
);

$themes = array(
	1 => 'classic',
	2 => 'emoji',
	3 => 'dark'
);
$hkp_reactions_db_version = "1.0";


require_once('wp-reactions-admin.php');

function hkp_feelbox_install_db_table()
{
	global $wpdb;
	global $hkp_reactions_db_version;
	global $hkp_reactions_options;

	$table_name = $wpdb->prefix . 'hkp_reactions_posts';
	$installed_ver = get_option("hkp_reactions_db_version");

	if ($installed_ver != $hkp_reactions_db_version) {

		$sql = "CREATE TABLE " . $table_name . " (
			`ID` bigint(20) NOT NULL,
			`bummer` bigint(20) DEFAULT '0' ,
			`good` bigint(20) DEFAULT '0' ,
			`sad` bigint(20) DEFAULT '0' ,
			`lol` bigint(20) DEFAULT '0' ,
			`scary` bigint(20) DEFAULT '0' ,
			`shocked` bigint(20) DEFAULT '0' ,
			`boring` bigint(20) DEFAULT '0' ,
			`sweet` bigint(20) DEFAULT '0' ,
			`angry` bigint(20) DEFAULT '0' ,
			`nerdy` bigint(20) DEFAULT '0' ,
			`time` datetime DEFAULT CURRENT_TIMESTAMP ,
			PRIMARY KEY  `ID` (`ID`)
		);";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	add_option("hkp_reactions_db_version", $hkp_reactions_db_version);
}

register_activation_hook(__FILE__, 'hkp_feelbox_install_db_table');
add_action('init', 'hkp_feelbox_init');

function hkp_feelbox_init()
{

	$options = get_option('hkp_reactions_options');
	if (hkp_reactions_box_autoDisplay($options)) {
		add_filter('the_content', 'hkp_reactions_box_to_posts');
	}
}

function hkp_reactions_box_html()
{

	global $wpdb;
	global $post;

	$options = get_option('hkp_reactions_options');

	$wpReactionTitle = hkp_reactions_box_title($options);
	$wpReactionTheme = hkp_reactions_box_theme($options);
	$reactionsMoods = hkp_reactions_box_moods($options);

	$poweredBy = hkp_reactions_box_poweredBy();
	//$randumNum = rand( 0, 1 );
	$randumNum = 0;
	$reactionsObj = hkp_reactions_votes();

	$widgethtml = '<div class="hkp-feelbox-voted" id="hkp-feelbox-widget">';

	$widgethtml .= '<div id="bgLayer"></div>
		<div id="feelbox-header">
			<div id="header-msg"> 
				<a alt="' . $poweredBy[$randumNum]['title'] . '" href="' . $poweredBy[$randumNum]['url'] . '" target="_blank"><span> </span></a> 
			</div>
			<div id="hkp-feelbox-s">
				' . $wpReactionTitle . '
			</div>
			<div style="clear:both"></div>';

	//sparkbar or vote bar
	if (hkp_reactions_box_showVoteBar($options)) {
		$totalVotes = hkp_reactions_totalvotes($reactionsMoods, $reactionsObj);

		$widgethtml .= '<div id="sparkbardiv">';
		foreach ($reactionsMoods as $mood) {

			$width = (isset($reactionsObj->$mood)) ? ($reactionsObj->$mood * 100) / $totalVotes : 0;
			$titleAttr = ucfirst($mood) . ' ' . number_format($width, 0, '.', '') . '%';
			$widgethtml .= '<div title="' . $titleAttr . '" style="width:' . $width . '%" class="spark ' . $mood . '"></div>';
		}
		$widgethtml .= '<div style="clear:both"></div></div>';
	}

	$widgethtml .= '</div>
					<div style="" id="bd">';

	if (hkp_reactions_box_showSocialshare($options)) {
		//Social Share button
		$twitter_user = 'midtbmedia';
		$widgethtml .= '<div id="hkp-feelbox-social">
				<div id="feelbox-msg">Share your vote!</div> 
				<div>
					<a target="_blank" id="hkp-feelbox-twitter-button" class="socialmedia"  href="https://twitter.com/intent/tweet?text=' . htmlspecialchars(urlencode(html_entity_decode(get_the_title(), ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8') . '&url=' . urlencode(esc_url(get_permalink())) . '&via=' . urlencode($twitter_user ? $twitter_user : get_bloginfo('name')) . '"  >share</a>

					<a id="hkp-feelbox-facebook-button" class="socialmedia" href="http://www.facebook.com/sharer.php?u=' . urlencode(esc_url(get_permalink())) . '" onclick="window.open(this.href, \'mywin\',\'left=50,top=50,width=600,height=350,toolbar=0\'); return false;">share</a> 

					<a id="hkp-feelbox-googleplus-button" class="socialmedia" href="http://plus.google.com/share?url=' . esc_url(get_permalink()) . '" onclick="window.open(this.href, \'mywin\',\'left=50,top=50,width=600,height=350,toolbar=0\'); return false;">share</a>
				</div> 
				<div id="hkp-feelbox-s">
					<a id="clr" href="javascript:void(0)">No, thanks.</a>
				</div>
			</div>';
	}

	//Loading
	$widgethtml .= '<div style="display: none;" id="loading"></div>';

	$widgethtml .= '<div id="wp-reactions">';
	foreach ($reactionsMoods as $mood) {

		$moodVotes = (isset($reactionsObj->$mood)) ? $reactionsObj->$mood : 0;
		$widgethtml .= '<div class="wp-emotion-icon"> 
							<div alt="' . $mood . '" data-id="' . $mood . '" class="' . $mood . ' feelbox-emotion-pic ' . $wpReactionTheme . '"></div>
							<div class="feelbox-emotion-counter" id="num-' . $mood . '">' . hkp_reactions_votes_count($moodVotes) . '</div> 
						</div>';
	}

	$widgethtml .= '</div>		
						</div>
					</div>';
	return $widgethtml;
}

function hkp_reactions_box_title($options)
{
	if (isset($options['showtitle']) && $options['showtitle'] == 0) {
		$wpReactionTitle = '';
	} else {
		if ($options['title'] && !empty($options['title'])) {
			$wpReactionTitle = $options['title'];
		} else {
			$wpReactionTitle = 'How you feel for this post?';
		}
	}
	return $wpReactionTitle;
}

function hkp_reactions_box_theme($options)
{
	global $themes;
	if (isset($options['theme']) && in_array($options['theme'], $themes)) {
		$theme = $options['theme'];
	} else {
		$theme = $themes[1];
	}
	return $theme;
}

function hkp_reactions_box_poweredBy()
{
	$poweredBy = array(
		0 => array(
			'url' => 'http://www.midtb.org/',
			'title' => 'Powered by midtb.org - News, Entertainment, Technology news'
		),
		1 => array(
			'url' => 'http://www.megashare-viooz.eu/',
			'title' => 'Powered by Megashare-viooz.eu - Movies and Tv shows Database'
		)
	);
	return $poweredBy;
}

function hkp_reactions_box_moods($options)
{
	if (isset($options['moods'])) {
		return $options['moods'];
	} else {
		global $moods;
		return $moods;
	}
}

function hkp_reactions_box_showVoteBar($options)
{
	if (isset($options['votebar']) && $options['votebar'] == 0) {
		return false;
	} else {
		return true;
	}
}

function hkp_reactions_box_autoDisplay($options)
{
	if (isset($options['autoshow']) && $options['autoshow'] == 0) {
		return false;
	} else {
		return true;
	}
}

function hkp_reactions_box_showSocialshare($options)
{
	if (isset($options['socialshare']) && $options['socialshare'] == 0) {
		return false;
	} else {
		return true;
	}
}

function hkp_reactions_votes($postID = null)
{

	global $wpdb;
	if (!$postID) {
		global $post;
		$postID = $post->ID;
	}
	$votes = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}hkp_reactions_posts WHERE ID=" . $postID);
	return $votes;
}

function hkp_reactions_totalvotes($reactionsMoods = null, $reactionsObj = null)
{

	$totalVote = 0;
	if (isset($reactionsMoods) && isset($reactionsObj)) {
		foreach ($reactionsMoods as $mood) {
			$moodVotes = ($reactionsObj->$mood) ? $reactionsObj->$mood : 0;
			$totalVote = $totalVote + $moodVotes;
		}
	}
	return $totalVote;
}

/*
 * Auto add widget after post content. 
 */

function hkp_reactions_box_to_posts($content)
{
	if (is_single()) {
		$content .= hkp_reactions_box_html();
	}
	return $content;
}

/*
 * Function to call widget at in theme location
 */

function hkp_reactions_box()
{
	if (is_single() || is_page() && !(is_home())) {
		echo hkp_reactions_box_html();
	}
}

/*
 * Shortcode for widget call in post, pages
 */
add_shortcode('hkp_reactions_box', 'hkp_reactions_box');

function hkp_print_feelbox_shortcode()
{
	if (is_single() || is_page() && !(is_home())) {
		return feelbox_get_widget_html();
	}
}

function hkp_feelbox_register_styles()
{
	wp_enqueue_style('hkp_feelbox_style', plugins_url('css/style.css', __FILE__));
	wp_enqueue_script('hkp_feelbox_ajax_script', plugins_url('js/ajax.js', __FILE__), array('jquery'));


	global $post;

	$options = get_option('hkp_reactions_options');
	$nonce = wp_create_nonce('hkp-wp-feelbox');

	$votebar = (hkp_reactions_box_showVoteBar($options)) ? 1 : 0;
	$socialShare = (hkp_reactions_box_showSocialshare($options)) ? 1 : 0;

	$enableAjaxPopulate = ($options['onloadajax'] == 1) ? 1 : 0;

	if (is_single() || is_page() && !(is_home())) {
		wp_localize_script('hkp_feelbox_ajax_script', 'hkpFeelboxAjax', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'action' => 'wp_feelbox_cast_vote',
			'token' => $nonce,
			'id' => $post->ID,
			'voteBar' => $votebar,
			'social' => $socialShare,
			'onloadAjax' => $enableAjaxPopulate,
			'onloadaction' => 'hkp_reactions_populate',
		));
	}
}

add_action('wp_enqueue_scripts', 'hkp_feelbox_register_styles');
add_action('wp_ajax_wp_feelbox_cast_vote', 'hkp_feelbox_cast_vote');
add_action('wp_ajax_nopriv_wp_feelbox_cast_vote', 'hkp_feelbox_cast_vote');

function hkp_feelbox_cast_vote()
{
	$nonce = $_POST['token'];
	// is this a valid request?
	if (!wp_verify_nonce($nonce, 'hkp-wp-feelbox')) {
		die("Oops!");
	}

	$postid = $_POST['postID'];
	$reaction = $_POST['reaction'];

	$response = hkp_feelbox_save_results($reaction, $postid);

	// response output
	header("Content-Type: application/json");
	echo $response;

	exit;
}

function hkp_feelbox_save_results($vote, $postid)
{

	global $wpdb;
	global $feelbox_wp_options;
	global $moods;
	$wpdb->show_errors();

	if ($vote && in_array($vote, $moods)) {

		$table_name = $wpdb->prefix . 'hkp_reactions_posts';

		$voteFields = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM " . $table_name . " WHERE ID=%s",
				$postid
			)
		);

		$options = get_option('hkp_reactions_options');

		$votecount = (isset($voteFields->$vote)) ? $voteFields->$vote : '';
		$reactionsMoods = hkp_reactions_box_moods($options);

		$totalCount = hkp_reactions_totalvotes($reactionsMoods, $voteFields);

		//$recordexists = (sizeof( $votecount ) > 0) ? true : false;
		if ($voteFields) {

			$row = $wpdb->update(
				$table_name,
				array('ID' => $postid, $vote => $votecount + 1, 'time' => current_time('mysql')),
				array('ID' => $postid)
			);
		} else {
			$row = $wpdb->insert(
				$table_name,
				array('ID' => $postid, $vote => 1, 'time' => current_time('mysql'))
			);
		}

		// generate the response
		$response = json_encode(array('success' => true, 'vote' => $vote, 'formatedVote' => hkp_reactions_votes_count($votecount + 1), 'voteCount' => $votecount + 1, 'totalvotes' => $totalCount + 1));
		return $response;
	} else {
		$response = json_encode(array('success' => false));
		return $response;
	}
}


/*
 * Ajax to populat all moods/recations if theme caching enabled
 */
add_action('wp_ajax_hkp_reactions_populate', 'hkp_reactions_ajax_populate');
add_action('wp_ajax_nopriv_hkp_reactions_populate', 'hkp_reactions_ajax_populate');
function hkp_reactions_ajax_populate()
{
	global $wpdb;

	$postID = $_POST['postID'];
	$options = get_option('hkp_reactions_options');
	$reactionsMoods = hkp_reactions_box_moods($options);
	$reactionsObj = hkp_reactions_votes($postID);

	$response = [];
	$totalVote = 0;
	foreach ($reactionsMoods as $mood) {
		$moodVotes = ($reactionsObj->$mood) ? $reactionsObj->$mood : 0;
		$response['reactions'][$mood] = $moodVotes;

		$totalVote = $totalVote + $moodVotes;
	}
	$response['totalVotes'] = $totalVote;
	$response = json_encode($response);
	header("Content-Type: application/json");
	echo $response;

	exit;
}

function hkp_reactions_votes_count($votes)
{

	if ($votes > 999) {
		$vote = $votes / 1000;
		return floor($vote) . "k+";
	} else {
		return $votes;
	}

}