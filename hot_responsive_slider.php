<?php
/**
 * Plugin Name: Hot Responsive Slider
 * Plugin URI: http://hot-themes.com/wordpress/plugins/responsive-slider
 * Description: Hot Responsive Slider allows you to create responsive, touch-friendly image sliders and insert them easily into your posts with shortcodes.
 * Version:1.0
 * Author: HotThemes
 * Author URI: http://hot-themes.com
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( !function_exists('add_action') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

// Pre-2.6 compatibility
if ( !defined('WP_CONTENT_URL') )
	define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if ( !defined('WP_CONTENT_DIR') )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( !defined( 'WP_PLUGIN_DIR' ) )
	define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

new RESPONSIVE_SLIDER_plugin();

class RESPONSIVE_SLIDER_plugin {

	var $OPTIONS = array();
	var $plugin_path = '';

	function RESPONSIVE_SLIDER_plugin() {
    	//delete_option('HOT_RESPONSIVE_SLIDER_PLUGIN'); // - Uncoment to reset options once
		
		$this->plugin_path = plugins_url('/', __FILE__);
        $this->OPTIONS = get_option('HOT_RESPONSIVE_SLIDER_PLUGIN');
		$this->RESPONSIVE_SLIDER_textdomain();
			
			
		if (empty($this->OPTIONS)){
		///DEFAULTS/////////////////////////////////////
			
			$this->OPTIONS['mode'] = 'horizontal';
			$this->OPTIONS['speed'] = 500;
			$this->OPTIONS['slideMargin'] = 0;
			$this->OPTIONS['randomStart'] = false;
			$this->OPTIONS['infiniteLoop'] = true;
			$this->OPTIONS['hideControlOnEnd'] = true;
			$this->OPTIONS['easing'] = 'linear';
			$this->OPTIONS['pager'] = true;
			$this->OPTIONS['pagerType'] = 'full';
			$this->OPTIONS['controls'] = true;
			$this->OPTIONS['auto'] = true;
			$this->OPTIONS['autoControls'] = false;
			$this->OPTIONS['autoControlsCombine'] = true;
			$this->OPTIONS['pause'] = 4000;
			$this->OPTIONS['autoDirection'] = 'next';
			$this->OPTIONS['autoHover'] = true;
			$this->OPTIONS['slideWidth'] = 0;
			
		    update_option('HOT_RESPONSIVE_SLIDER_PLUGIN', (array)$this->OPTIONS);
		////////////////////////////////////////////////
		}		
					
		add_action('admin_menu',array( $this, 'RESPONSIVE_SLIDER_plugin_menu'));
		
		add_filter( 'the_content', array( &$this, 'RESPONSIVE_SLIDER_Render' ));
		add_filter( 'the_excerpt', array( &$this, 'RESPONSIVE_SLIDER_Render' ));
		add_filter( 'widget_text', array( &$this, 'RESPONSIVE_SLIDER_Render' ));
		
		add_action('wp_enqueue_scripts', array( $this, 'RESPONSIVE_SLIDER_styles_and_scripts'));
		add_action('wp_head',array( $this, 'RESPONSIVE_SLIDER_inline_styles_and_scripts'),15);
	    add_action('admin_init', array( $this,'admin_utils'));
	}

	function admin_utils() {
			//
	}

	function RESPONSIVE_SLIDER_styles_and_scripts() {
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery.bxslider', plugins_url('/js/jquery.bxslider.js', __FILE__),array('jquery','jquery'),'4.1.2');

		wp_enqueue_style( 'hot-responsive-slider-style', plugins_url('/css/hot_responsive_slider.css', __FILE__));
	}

	function RESPONSIVE_SLIDER_inline_styles_and_scripts() {

		$mode 					= $this->OPTIONS['mode'];
		$speed 					= $this->OPTIONS['speed'];
		$slideMargin 			= $this->OPTIONS['slideMargin'];
		$randomStart 			= $this->OPTIONS['randomStart'];
		$randomStartValue		= "false";
		if($randomStart) { $randomStartValue = "true"; }else{ $randomStartValue = "false"; }
		$infiniteLoop 			= $this->OPTIONS['infiniteLoop'];
		$infiniteLoopValue		= "true";
		if($infiniteLoop) { $infiniteLoopValue = "true"; }else{ $infiniteLoopValue = "false"; }
		$hideControlOnEnd 		= $this->OPTIONS['hideControlOnEnd'];
		$hideControlOnEndValue	= "true";
		if($hideControlOnEnd) { $hideControlOnEndValue = "true"; }else{ $hideControlOnEndValue = "false"; }
		$easing 				= $this->OPTIONS['easing'];
		$pager 					= $this->OPTIONS['pager'];
		$pagerValue				= "true";
		if($pager) { $pagerValue = "true"; }else{ $pagerValue = "false"; }
		$pagerType 				= $this->OPTIONS['pagerType'];
		$controls 				= $this->OPTIONS['controls'];
		$controlsValue			= "true";
		if($controls) { $controlsValue = "true"; }else{ $controlsValue = "false"; }
		$auto 					= $this->OPTIONS['auto'];
		$autoValue				= "true";
		if($auto) { $autoValue = "true"; }else{ $autoValue = "false"; }
		$autoControls 			= $this->OPTIONS['autoControls'];
		$autoControlsValue		= "false";
		if($autoControls) { $autoControlsValue = "true"; }else{ $autoControlsValue = "false"; }
		$autoControlsCombine 	= $this->OPTIONS['autoControlsCombine'];
		$autoControlsCombineValue		= "true";
		if($autoControlsCombine) { $autoControlsCombineValue = "true"; }else{ $autoControlsCombineValue = "false"; }
		$pause 					= $this->OPTIONS['pause'];
		$autoDirection 			= $this->OPTIONS['autoDirection'];
		$autoHover 				= $this->OPTIONS['autoHover'];
		$autoHoverValue			= "true";
		if($autoHover) { $autoHoverValue = "true"; }else{ $autoHoverValue = "false"; }
		$slideWidth 			= $this->OPTIONS['slideWidth'];

		echo "
		<script>
			jQuery(document).ready(function(){
				jQuery('.hot_responsive_slider').bxSlider({
					mode: '".$mode."',
					speed: ".$speed.",
					slideMargin: ".$slideMargin.",
					randomStart: ".$randomStartValue.",
					infiniteLoop: ".$infiniteLoopValue.",
					hideControlOnEnd: ".$hideControlOnEndValue.",
					easing: '".$easing."',
					pager: ".$pagerValue.",
					pagerType: '".$pagerType."',
					controls: ".$controlsValue.",
					auto: ".$autoValue.",
					autoControls: ".$autoControlsValue.",
					autoControlsCombine: ".$autoControlsCombineValue.",
					pause: ".$pause.",
					autoDirection: '".$autoDirection."',
					autoHover: ".$autoHoverValue.",
					slideWidth: ".$slideWidth."	
				});
			});
		</script>";
	}

	function RESPONSIVE_SLIDER_textdomain() {
	    load_plugin_textdomain('hot-RESPONSIVE_SLIDER-plugin', false, dirname(plugin_basename(__FILE__) ) . '/languages');
	}

	function copter_remove_crappy_markup($string) {
	    $patterns = array(
	        '#^\s*</p>#',
	        '#<p>\s*$#'
	    );

	    return preg_replace($patterns, '', $string);
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////

	function RESPONSIVE_SLIDER_Render($srchtml) {
		if ( !preg_match("#{slider}(.*?){/slider}#s",$srchtml) ) {
			return $srchtml;
		}
	   
		if (preg_match_all("#{slider}(.*?){/slider}#s", $srchtml, $matches, PREG_PATTERN_ORDER) > 0) {
			$RESPONSIVE_SLIDERcount = -1;
			foreach ($matches[0] as $match) {
				$RESPONSIVE_SLIDERcount++;
				
				$hotresponsiveslider_input = preg_replace("/{.+?}/", "", $match);
				$hotresponsiveslider_params = explode(",", $hotresponsiveslider_input);
				
				$keywords = explode(" ", $hotresponsiveslider_params[0]);
				$keywords_number = count($keywords);
				$keywords_number_1 = $keywords_number - 1;

				/*

				The parameters entered in the shortcode should be separated by colon.
				They can be called from the array:

				$hotresponsiveslider_params[0] --> the first parameter
				$hotresponsiveslider_params[1] --> the second parameter
				$hotresponsiveslider_params[2] --> the third parameter, and so on
				...

				*/

				$directory = ABSPATH.$hotresponsiveslider_params[0];
				$scanned_directory = array_diff(scandir($directory), array('..', '.'));
				sort($scanned_directory);
				$how_many_images = count($scanned_directory);

				$html = '<!-- Hot Responsive Slider starts here -->';
				$html.= '<ul class="hot_responsive_slider">';

				foreach($scanned_directory as $index=>$scanned_file) {
					$html.= '<li><img src="'.get_option('siteurl').'/'.$hotresponsiveslider_params[0].'/'.$scanned_file.'" alt="slide '.$index.'" /></li>';
				}

				$html.= '</ul>';
				$html.= '<!-- Hot Responsive Slider ends here -->';
				
				$srchtml = preg_replace( "#{slider}".$hotresponsiveslider_input."{/slider}#s", $html, $srchtml );

			}
	   }
	   return $srchtml;
	}

	function RESPONSIVE_SLIDER_plugin_menu() {
		add_options_page('Hot Responsive Slider', 'Hot Responsive Slider', 'manage_options', 'hot-RESPONSIVE_SLIDER-settings', array( $this,'RESPONSIVE_SLIDER_RenderSettings'));
	}

	function RESPONSIVE_SLIDER_RenderSettings() {
		if(isset($_POST['action'])) {
			if($_POST['action'] === "save") {
				foreach($this->OPTIONS as $Name => $option) {
					if(isset($_POST[$Name])) {
						if($_POST[$Name] == "on" || $_POST[$Name] == "Yes") {
							$this->OPTIONS[$Name] = true;
						}else{
							$this->OPTIONS[$Name] = $_POST[$Name];
						}
					}else{
						$this->OPTIONS[$Name] = false;
					}
				}
				update_option('HOT_RESPONSIVE_SLIDER_PLUGIN', (array)$this->OPTIONS);
			}
		} ?>
		<div class='wrap'>
			<form method="post">
				<h2><?php echo __('Hot Responsive Slider Options','hot-RESPONSIVE_SLIDER-plugin'); ?></h2>
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="mode"><?php echo __('Mode','hot-RESPONSIVE_SLIDER-plugin'); ?></label>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><span><?php echo __('Mode','hot-RESPONSIVE_SLIDER-plugin'); ?></span></legend>
								<label>
									<input type="radio" name="mode" value="horizontal" <?php if($this->OPTIONS['mode']=="horizontal") { echo 'checked="checked"'; } ?>> <?php echo __('Horizontal','hot-RESPONSIVE_SLIDER-plugin'); ?>
								</label>
								<br>
								<label>
									<input type="radio" name="mode" value="vertical" <?php if($this->OPTIONS['mode']=="vertical") { echo 'checked="checked"'; } ?>> <?php echo __('Vertical','hot-RESPONSIVE_SLIDER-plugin'); ?>
								</label>
								<br>
								<label>
									<input type="radio" name="mode" value="fade" <?php if($this->OPTIONS['mode']=="fade") { echo 'checked="checked"'; } ?>> <?php echo __('Fade','hot-RESPONSIVE_SLIDER-plugin'); ?>
								</label>
								<p class="description">Type of transition between slides.</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th>
							<label for="speed"> <?php echo  __('Animation Speed','hot-RESPONSIVE_SLIDER-plugin'); ?></label>
						</th>
						<td>
							<input name="speed" type="number" step="10" min="10" id="speed" value="<?php echo $this->OPTIONS['speed'];?>" class="small-text"> milliseconds
						</td>
					</tr>
					<tr>
						<th>
							<label for="slideMargin"> <?php echo  __('Slide Margin','hot-RESPONSIVE_SLIDER-plugin'); ?></label>
						</th>
						<td>
							<input name="slideMargin" type="number" step="1" min="0" id="slideMargin" value="<?php echo $this->OPTIONS['slideMargin'];?>" class="small-text"> pixels
						</td>
					</tr>
					<tr>
						<th>
							<label for="randomStart"> <?php echo __('Random Start','hot-RESPONSIVE_SLIDER-plugin'); ?> </label>
						</th>
						<td>
							<input type="checkbox" name="randomStart" id="randomStart" <?php echo (((boolean)$this->OPTIONS['randomStart'] === true)?  "checked='checked'": ""); ?> />
							<p class="description">Start slider on a random slide.</p>
						</td>
					</tr>	
					<tr>
						<th>
							<label for="infiniteLoop"> <?php echo __('Infinite Loop','hot-RESPONSIVE_SLIDER-plugin'); ?> </label>
						</th>
						<td>
							<input type="checkbox" name="infiniteLoop" id="infiniteLoop" <?php echo (((boolean)$this->OPTIONS['infiniteLoop'] === true)? "checked='checked'": ""); ?> />
							<p class="description">If enabled, clicking "Next" while on the last slide will transition to the first slide and vice-versa.</p>
						</td>
					</tr>
					<tr>
						<th>
							<label for="hideControlOnEnd"> <?php echo __('Hide Control On End','hot-RESPONSIVE_SLIDER-plugin'); ?> </label>
						</th>
						<td>
							<input type="checkbox" name="hideControlOnEnd" id="hideControlOnEnd" <?php echo (((boolean)$this->OPTIONS['hideControlOnEnd'] === true)? "checked='checked'": ""); ?> />
							<p class="description">If enabled and Infinite Loop disabled, "Next" control will be hidden on last slide and vice-versa.</p>
						</td>
					</tr>
					<tr>
						<th>
							<label for="easing"> <?php echo __('Easing Type','hot-RESPONSIVE_SLIDER-plugin'); ?> </label>
						</th>
						<td>
							<select name="easing" id="easing">
								<option <?php if($this->OPTIONS['easing']=="linear") { ?> selected="selected"<?php } ?> value="linear">Linear</option>
								<option <?php if($this->OPTIONS['easing']=="ease") { ?> selected="selected"<?php } ?> value="ease">Ease</option>
								<option <?php if($this->OPTIONS['easing']=="ease-in") { ?> selected="selected"<?php } ?> value="ease-in">Ease In</option>
								<option <?php if($this->OPTIONS['easing']=="ease-out") { ?> selected="selected"<?php } ?> value="ease-out">Ease Out</option>
								<option <?php if($this->OPTIONS['easing']=="ease-in-out") { ?> selected="selected"<?php } ?> value="ease-in-out">Ease In Out</option>
							</select>
						</td>
					</tr>
					<tr>
						<th>
							<label for="pager"> <?php echo __('Pager','hot-RESPONSIVE_SLIDER-plugin'); ?> </label>
						</th>
						<td>
							<input type="checkbox" name="pager" id="pager" <?php echo (((boolean)$this->OPTIONS['pager'] === true)? "checked='checked'": ""); ?> />
							<p class="description">If enabled, a pager will be added.</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="pagerType"><?php echo __('Pager Type','hot-RESPONSIVE_SLIDER-plugin'); ?></label>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><span><?php echo __('Pager Type','hot-RESPONSIVE_SLIDER-plugin'); ?></span></legend>
								<label>
									<input type="radio" name="pagerType" value="full" <?php if($this->OPTIONS['pagerType']=="full") { echo 'checked="checked"'; } ?>> <?php echo __('Full','hot-RESPONSIVE_SLIDER-plugin'); ?>
								</label>
								<br>
								<label>
									<input type="radio" name="pagerType" value="short" <?php if($this->OPTIONS['pagerType']=="short") { echo 'checked="checked"'; } ?>> <?php echo __('Short','hot-RESPONSIVE_SLIDER-plugin'); ?>
								</label>
								<p class="description">If "full", a pager link will be generated for each slide. If "short", a x / y pager will be used (ex. 1 / 5).</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th>
							<label for="controls"> <?php echo __('Controls','hot-RESPONSIVE_SLIDER-plugin'); ?> </label>
						</th>
						<td>
							<input type="checkbox" name="controls" id="controls" <?php echo (((boolean)$this->OPTIONS['controls'] === true)? "checked='checked'": ""); ?> />
							<p class="description">If enabled, "Next" / "Prev" controls will be added.</p>
						</td>
					</tr>
					<tr>
						<th>
							<label for="auto"> <?php echo __('Auto Rotation','hot-RESPONSIVE_SLIDER-plugin'); ?> </label>
						</th>
						<td>
							<input type="checkbox" name="auto" id="auto" <?php echo (((boolean)$this->OPTIONS['auto'] === true)? "checked='checked'": ""); ?> />
							<p class="description">Slides will automatically rotate.</p>
						</td>
					</tr>
					<tr>
						<th>
							<label for="autoControls"> <?php echo __('Start/Stop Controls','hot-RESPONSIVE_SLIDER-plugin'); ?> </label>
						</th>
						<td>
							<input type="checkbox" name="autoControls" id="autoControls" <?php echo (((boolean)$this->OPTIONS['autoControls'] === true)? "checked='checked'": ""); ?> />
							<p class="description">If enabled, "Start" / "Stop" controls will be added.</p>
						</td>
					</tr>
					<tr>
						<th>
							<label for="autoControlsCombine"> <?php echo __('Combine Auto Controls','hot-RESPONSIVE_SLIDER-plugin'); ?> </label>
						</th>
						<td>
							<input type="checkbox" name="autoControlsCombine" id="autoControlsCombine" <?php echo (((boolean)$this->OPTIONS['autoControlsCombine'] === true)? "checked='checked'": ""); ?> />
							<p class="description">When slideshow is playing only "Stop" control is displayed and vice-versa.</p>
						</td>
					</tr>
					<tr>
						<th>
							<label for="pause"> <?php echo  __('Pause Between Slides','hot-RESPONSIVE_SLIDER-plugin'); ?></label>
						</th>
						<td>
							<input name="pause" type="number" step="100" min="100" id="pause" value="<?php echo $this->OPTIONS['pause'];?>" class="small-text"> milliseconds
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="autoDirection"><?php echo __('Auto Direction','hot-RESPONSIVE_SLIDER-plugin'); ?></label>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><span><?php echo __('Auto Direction','hot-RESPONSIVE_SLIDER-plugin'); ?></span></legend>
								<label>
									<input type="radio" name="autoDirection" value="next" <?php if($this->OPTIONS['autoDirection']=="next") { echo 'checked="checked"'; } ?>> <?php echo __('Next','hot-RESPONSIVE_SLIDER-plugin'); ?>
								</label>
								<br>
								<label>
									<input type="radio" name="autoDirection" value="prev" <?php if($this->OPTIONS['autoDirection']=="prev") { echo 'checked="checked"'; } ?>> <?php echo __('Previous','hot-RESPONSIVE_SLIDER-plugin'); ?>
								</label>
							</fieldset>
							<p class="description">The direction of auto slide rotation.</p>
						</td>
					</tr>
					<tr>
						<th>
							<label for="autoHover"> <?php echo __('Stop Rotation On Mouse Over','hot-RESPONSIVE_SLIDER-plugin'); ?> </label>
						</th>
						<td>
							<input type="checkbox" name="autoHover" id="autoHover" <?php echo (((boolean)$this->OPTIONS['autoHover'] === true)? "checked='checked'": ""); ?> />
							<p class="description">Auto show will pause when mouse hovers over slider.</p>
						</td>
					</tr>
					<tr>
						<th>
							<label for="slideWidth"> <?php echo  __('Slider Width','hot-RESPONSIVE_SLIDER-plugin'); ?></label>
						</th>
						<td>
							<input name="slideWidth" type="number" step="1" min="0" id="slideWidth" value="<?php echo $this->OPTIONS['slideWidth'];?>" class="small-text"> pixels
							<p class="description">The width of each slide. This setting is required for all horizontal carousels!</p>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input name="save" type="submit" value="<?php echo  __('Save Changes','hot-RESPONSIVE_SLIDER-plugin'); ?>" class="button button-primary" />
					<input type="hidden" name="action" value="save" />
				</p>
			</form>
	    	<br/>
			<div>	
				<?php echo __('<h2>Instructions </h2><p>The plugin settings can be accessed from Settings &gt; Hot Responsive Slider. Each parameter is explained in this page.</p><p>To insert slider into your post or page, use this shortcode: <code>{slider}path/to/images{/slider}</code>. The path should be entered relative to your WordPress site, in example: <code>{slider}wp-content/uploads/2015/01{/slider}</code>.</p><p>Images that you uploaded through WordPress Media Library are located in <code>wp-content/uploads</code> folder of your site. There you can see many subfolders, usually per year and month when images are uploaded. So, to include images from January 2015, the folder is <code>wp-content/uploads/2015/01</code>.</p><p>You can also upload images using FTP into any folder of your site. In this case, the uploaded image will not be included in Media Library.</p><p>It is recommended to include images with identical resolution (width and height) into the slider instance. Slider can show the images with different resolutions though.</p>','hot-RESPONSIVE_SLIDER-plugin'); ?>
			</div>	
	   </div>
	<?php	
	}

} //class

?>