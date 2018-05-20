<?php

class Hkp_ReactionsBox_Options {

	public $options;

	public function __construct() {

		$this->options = get_option( 'hkp_reactions_options' );
		$this->hkp_reactions_box_register_settings();
	}

	static public function hkp_reactions_box_add_menu_admin() {
		add_options_page( 'Settings: WP Reactions Box', 'WP Reactions Box', 'administrator', 'wpreactionsbox', array( 'Hkp_ReactionsBox_Options', 'hkp_reactions_box_menu_options_admin' ) );
		add_dashboard_page( ' WP Reactions Box Dashboard', 'WP Reactions Box', 'administrator', 'wpreactionsbox', array( 'Hkp_ReactionsBox_Options', 'hkp_reactions_box_dashboard' ) );
	}

	static public function hkp_reactions_box_menu_options_admin() {
		$optionsValues = get_option( 'hkp_reactions_options' );
		?>
		<div class="wrap">
			<div class="setting-box" style="float: left; max-width: 700px;">
				<h2 class="nav-tab-wrapper">WP Reactions Box
					<a class="nav-tab" href="<?php echo admin_url( 'index.php?page=wpreactionsbox' ); ?>">
						<?php _e( "Stats" ); ?>
					</a> 
					<a class="nav-tab nav-tab-active" rel="settings" href="#">
						<?php _e( "Settings" ); ?>
					</a> 					
				</h2>
				<div class="container">
					<div class="content" id="settings">						
						<form method="post" action="options.php">
							<?php
							settings_fields( 'hkp_reactions_options' ); //option_group
							do_settings_sections( __FILE__ );
							?>
							<p class="submit">
								<input type="submit" name="submit" class="button-primary" value="Save Changes" />
							</p>
						</form>
					</div>
				</div>
			</div>	
		</div>
		<div class="powered_by">
			<h2>Powered by</h2>
			<a target="_blank" href="http://www.midtb.org/"><img src="<?php echo plugins_url( 'images/midtb-bnr.jpg', __FILE__ ); ?>" /></a>
			<br /><br />
			<a target="_blank" href="http://www.xtcz.net/"><img src="<?php echo plugins_url( 'images/xtcz-bnr.jpg', __FILE__ ); ?>" /></a>
		</div>
		<?php
	}

	public function hkp_reactions_box_register_settings() {

		//option_group, option_name
		register_setting( 'hkp_reactions_options', 'hkp_reactions_options' );

		//id, title of the section,	callback, page?
		add_settings_section( 'hkp_reactions_main_settings', 'Settings', array( $this, 'hkp_reactions_box_main_settings_cb' ), __FILE__ );

		//id, title, callback, page
		add_settings_field( 'hkp_reactions_title', 'Title', array( $this, 'hkp_reactions_title_field' ), __FILE__, 'hkp_reactions_main_settings' );
		add_settings_field( 'hkp_reactions_showtitle', 'Show Title', array( $this, 'hkp_reactions_showtitle_field' ), __FILE__, 'hkp_reactions_main_settings' );
		add_settings_field( 'hkp_reactions_theme', 'Icons style', array( $this, 'hkp_reactions_theme_field' ), __FILE__, 'hkp_reactions_main_settings' );
		add_settings_field( 'hkp_reactions_votebar', 'Graphical bar', array( $this, 'hkp_reactions_votebar_field' ), __FILE__, 'hkp_reactions_main_settings' );
		add_settings_field( 'hkp_reactions_autoshow', 'Automatic display', array( $this, 'hkp_reactions_autoshow_field' ), __FILE__, 'hkp_reactions_main_settings' );
		add_settings_field( 'hkp_reactions_socialshare', 'Social media sharing', array( $this, 'hkp_reactions_socialshare_field' ), __FILE__, 'hkp_reactions_main_settings' );
		add_settings_field( 'hkp_reactions_moods', 'Show Selected Reactions/Moods', array( $this, 'hkp_reactions_moods_field' ), __FILE__, 'hkp_reactions_main_settings' );
		add_settings_field( 'hkp_reactions_onloadajax', 'Enable if used caching', array( $this, 'hkp_reactions_onloadajax_field' ), __FILE__, 'hkp_reactions_main_settings' );
	}

	public function hkp_reactions_box_main_settings_cb() {
		//optional
	}

	/*
	 * Inputs
	 */

	public function hkp_reactions_title_field() {
		$title = (isset( $this->options['title'] )) ? $this->options['title'] : 'How you feel for this post?';
		echo "<input id='hkp_reactions_options-title' name='hkp_reactions_options[title]' type='text' class='regular-text' value='{$title}' />";
		echo '<p id="tagline-description" class="description"> The title before WP Reactions Box. </p>';
	}

	public function hkp_reactions_showtitle_field() {

		if ( isset( $this->options['showtitle'] ) && $this->options['showtitle'] == 0 ) {
			$enableHide = 'checked';
			$enableShow = '';
		} else {
			$enableHide = '';
			$enableShow = 'checked';
		}

		echo "<label style='margin-right: 15px;'><input id='hkp_reactions_options-showtitle-show' name='hkp_reactions_options[showtitle]' {$enableShow} type='radio' value='1' /> Show </label>";
		echo "<label style='margin-right: 15px;'><input id='hkp_reactions_options-showtitle-hide' name='hkp_reactions_options[showtitle]' {$enableHide} type='radio'  value='0' /> Hide </label>";
	}

	public function hkp_reactions_theme_field() {

		$classic = ($this->options['theme'] == 'classic') ? 'checked' : '';
		$emoji = ($this->options['theme'] == 'emoji') ? 'checked' : '';
		$korosensei = ($this->options['theme'] == 'dark') ? 'checked' : '';
		if ( $classic == '' && $emoji == '' && $korosensei == '' ) {
			$classic = 'checked';
		}
		echo "<label style='margin-right: 15px;'><input id='hkp_reactions_options-theme-classic' name='hkp_reactions_options[theme]' {$classic} type='radio' value='classic' /> Classic </label>";
		echo "<label style='margin-right: 15px;'><input id='hkp_reactions_options-theme-emoji' name='hkp_reactions_options[theme]' {$emoji} type='radio'  value='emoji' /> Glossy Emoji</label>";
		echo "<label style='margin-right: 15px;'><input id='hkp_reactions_options-theme-korosensei' name='hkp_reactions_options[theme]' {$korosensei} type='radio'  value='dark' /> Dark Icons</label>";
	}

	public function hkp_reactions_votebar_field() {

		if ( isset( $this->options['votebar'] ) && $this->options['votebar'] == 0 ) {
			$enableHide = 'checked';
			$enableShow = '';
		} else {
			$enableHide = '';
			$enableShow = 'checked';
		}

		echo "<label style='margin-right: 15px;'><input id='hkp_reactions_options-votebar-show' name='hkp_reactions_options[votebar]' {$enableShow} type='radio' value='1' /> Show </label>";
		echo "<label style='margin-right: 15px;'><input id='hkp_reactions_options-votebar-hide' name='hkp_reactions_options[votebar]' {$enableHide} type='radio'  value='0' /> Hide </label>";
		echo '<p id="tagline-description" class="description"> Show the graphical bar above all moods. </p>';
	}

	public function hkp_reactions_autoshow_field() {

		if ( isset( $this->options['autoshow'] ) && $this->options['autoshow'] == 0 ) {
			$enableHide = 'checked';
			$enableShow = '';
		} else {
			$enableHide = '';
			$enableShow = 'checked';
		}

		echo "<label style='margin-right: 15px;'><input id='hkp_reactions_options-autoshow-show' name='hkp_reactions_options[autoshow]' {$enableShow} type='radio' value='1' /> Show </label>";
		echo "<label style='margin-right: 15px;'><input id='hkp_reactions_options-autoshow-hide' name='hkp_reactions_options[autoshow]' {$enableHide} type='radio'  value='0' /> Hide </label>";
		echo '<p id="tagline-description" class="description">Automatically display the FeelBox at the end of each blog post.</p>';
		echo '<p id="tagline-description" class="description">You can also use the <code>hkp_reactions_box()</code> PHP function in your templates or use the <code>[hkp_reactions_box]</code> shortcode to show the FeelBox where you want. </a>';
	}

	public function hkp_reactions_socialshare_field() {

		if ( isset( $this->options['socialshare'] ) && $this->options['socialshare'] == 0 ) {
			$enableHide = 'checked';
			$enableShow = '';
		} else {
			$enableHide = '';
			$enableShow = 'checked';
		}

		echo "<label style='margin-right: 15px;'><input id='hkp_reactions_options-socialshare-show' name='hkp_reactions_options[socialshare]' {$enableShow} type='radio' value='1' /> Show </label>";
		echo "<label style='margin-right: 15px;'><input id='hkp_reactions_options-socialshare-hide' name='hkp_reactions_options[socialshare]' {$enableHide} type='radio'  value='0' /> Hide </label>";
		echo '<p id="tagline-description" class="description">Allow people to share on Twitter, Facebook and Google+ after vote.</p>';
	}

	public function hkp_reactions_moods_field() {
		global $moods;
		foreach ( $moods as $mood ) {
			if ( isset( $this->options['moods'] ) ) {
				$enable = (in_array( $mood, $this->options['moods'] )) ? 'checked' : '';
			} else {
				$enable = 'checked';
			}

			echo "<label><input id='hkp_reactions_options-moods-" . $mood . "' name='hkp_reactions_options[moods][]' {$enable} type='checkbox' value='" . $mood . "' /> " . ucfirst( $mood ) . "</label> <br />";
		}
		echo '<p id="tagline-description" class="description">Only selected reactions/moods will be displayed on frontend</p>';
	}

	public function hkp_reactions_onloadajax_field () {
		if (  $this->options['onloadajax'] == 1 ) {
			$enableHide = '';
			$enableShow = 'checked';
		} else {
			$enableHide = 'checked';
			$enableShow = '';
		}

		echo "<label style='margin-right: 15px;'><input id='hkp_reactions_options-onloadajax-enable' name='hkp_reactions_options[onloadajax]' {$enableShow} type='radio' value='1' /> Enable </label>";
		echo "<label style='margin-right: 15px;'><input id='hkp_reactions_options-onloadajax-disable' name='hkp_reactions_options[onloadajax]' {$enableHide} type='radio'  value='0' /> Disable </label>";
		echo '<p id="tagline-description" class="description">Enable if you have used caching for your theme else leave it disabled.</p>';
	}

	static public function hkp_reactions_box_dashboard() {
		global $wpdb;
		global $moods;

		if ( !current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		?>
		<style>
			.selected { font-weight:bold; color:#000; text-decoration: none; }
			.totaltable,.mood_table { display:none; }

		</style>
		<script type="text/javascript">
		    jQuery(document).ready(function () {
		        jQuery("#tabs a").click(function () {
		            var activeTab = jQuery(this).attr("rel");
		            jQuery(this).addClass("button-primary").removeClass("button-secondary").siblings().addClass("button-secondary").removeClass("button-primary");
		            jQuery(".mood_table:visible").fadeOut("fast", function () {
		                jQuery("#mood_" + activeTab).slideDown("fast");
		            });
		            return false;
		        });


		    });
		</script>
		<?php
		Hkp_ReactionsBox_Options::wp_reactions_printmoodtables();
	}

	static public function wp_reactions_printmoodtables() {
		global $wpdb;
		$options = get_option( 'hkp_reactions_options' );
		$reactionsMoods = hkp_reactions_box_moods( $options );
		?>
		<div class="wrap">
			<h2 class="nav-tab-wrapper">WP Reactions Box
				<a class="nav-tab nav-tab-active" href="<?php echo admin_url( 'index.php?page=wpreactionsbox' ); ?>">
					<?php _e( "Stats" ); ?>
				</a> 
				<a class="nav-tab" rel="settings" href="<?php echo admin_url( 'options-general.php?page=wpreactionsbox' ); ?>">
					<?php _e( "Settings" ); ?>
				</a> 								
			</h2>
			<h3>
				<?php _e( "Most voted by mood" ); ?>
			</h3>
			<p>
				<?php _e( "To get an accurate read of the posts people care about, we're only counting posts that have more than one vote." ); ?>
			</p>
			<div id="tabs">
				<?php
				$i = 0;
				foreach ( $reactionsMoods as $moods ) {
					?>
					<a href="#" class="<?php echo ($i == 0) ? 'button-primary' : 'button-secondary'; ?> "  rel="<?php echo $moods; ?>"><?php _e( "Most" ); ?> <?php echo ucfirst( $moods ); ?></a>
					<?php
					$i++;
				}
				?>
			</div>
			<?php
			$i = 0;
			foreach ( $reactionsMoods as $moods ) {
				$objs = $wpdb->get_results( hkp_reactions_moods_sql( $moods, 10 ) );
				?>
				<div class="mood_table" style="<?php echo ($i == 0) ? 'display:block' : ''; ?>" id="mood_<?php echo $moods ?>">
					<h4><?php
						_e( "Most" );
						echo " " . ucfirst( $moods );
						?>  </h4>
					<table class="widefat">
						<thead>
							<tr>
								<th> <?php echo _e( "Post" ); ?> </th>

							</tr>
						</thead>
						<?php
						if ( sizeof( $objs ) > 0 ) {
							foreach ( $objs as $obj ) {
								?>
								<tr>
									<td>
										<strong><a href="<?php echo get_permalink( $obj->ID ); ?>"><?php echo get_the_title( $obj->ID ) ?></a></strong>
										<div class="row-moods">
											<?php
											$numItems = count( $reactionsMoods );
											$i = 0;
											foreach ( $reactionsMoods as $moods ) {
												?>
												<span> 
													<?php echo ucfirst( $moods ) . ": " . $obj->$moods; ?> 
													<?php echo ( ++$i != $numItems) ? '|' : ''; ?>
												</span>
												<?php
											}
											?>
										</div>
									</td>

								</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td> <?php _e( "No one has voted for this. Not yet." ); ?></td>
							</tr>
						<?php } ?>													
					</table>
				</div>
				<?php
				$i++;
			}
			?>
			<h3> <?php _e( "Recent Votes" ); ?> </h3>
			<?php $objs = $wpdb->get_results( hkp_reactions_recentlyVoted_sql( 30 ) ); ?>
			<div class="RecentVotes">					
					<table class="widefat">
						<thead>
							<tr>
								<th> <?php echo _e( "Post" ); ?> </th>

							</tr>
						</thead>
						<?php
						if ( sizeof( $objs ) > 0 ) {
							foreach ( $objs as $obj ) {
								?>
								<tr>
									<td>
										<strong><a href="<?php echo get_permalink( $obj->ID ); ?>"><?php echo get_the_title( $obj->ID ) ?></a></strong>
										<div class="row-moods">
											<?php
											$numItems = count( $reactionsMoods );
											$i = 0;
											foreach ( $reactionsMoods as $moods ) {
												?>
												<span> 
													<?php echo ucfirst( $moods ) . ": " . $obj->$moods; ?> 
													<?php echo ( ++$i != $numItems) ? '|' : ''; ?>
												</span>
												<?php
											}
											?>
										</div>
									</td>

								</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td> <?php _e( "No one has voted for this. Not yet." ); ?></td>
							</tr>
						<?php } ?>													
					</table>
				</div>
			
		</div>
		<?php
	}

}

add_action( 'admin_menu', function () {
	Hkp_ReactionsBox_Options::hkp_reactions_box_add_menu_admin();
} );

add_action( 'admin_init', function () {
	new Hkp_ReactionsBox_Options();
} );

function hkp_reactions_moods_sql( $moods, $limit ) {
	global $wpdb;
	return "SELECT * FROM {$wpdb->prefix}hkp_reactions_posts WHERE {$moods}>0 ORDER BY {$moods} DESC LIMIT " . $limit;
}

function hkp_reactions_recentlyVoted_sql( $limit ) {
	global $wpdb;
	return "SELECT * FROM {$wpdb->prefix}hkp_reactions_posts ORDER BY time DESC LIMIT " . $limit;
}

