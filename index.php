<?php
/*
Plugin Name: InQ.Social for WordPress
Plugin URI:  
Description: InQ.Social is a consumer research and online marketing platform that collects and turns customer data into actionable insights. We offer a set of Interactive Widgets to help you to establish a dialog with your users and a bunch of services to process this data and make marketing a piece of cake.
Version:     1.0
Author:      InQ.Social
Author URI:  https://inq.social
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

//avoid remote access
defined('ABSPATH') or die('');

//settings initialization
function inqsocial_options() {	
	add_menu_page('InQ.Social', 'InQ.Social', 'administrator', 'inqsocial_settings', 'inqsocial_settings_page', plugins_url('/icon.png', __FILE__));
}

add_action('admin_menu', 'inqsocial_options');

//settings appearance
function inqsocial_settings_page(){
	
	?><div class="wrap">		
		<form method="post" enctype="multipart/form-data" action="options.php">		
			<?php 			
			settings_fields('inqsocial_options');			
			do_settings_sections('inqsocial_settings');			
			?>			
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />			
			</p>			
		</form>		
	</div>	
	<?php	
}

//register options in database
function set_inqsocial_options() {
	
	//create section for plugin settings
	
	register_setting('inqsocial_options', 'inqsocial_options', 'validate_inqsocial_settings');
	
	add_settings_section('inqsocial_section', 'InQ.Social plugin settings', '', 'inqsocial_settings');
	
	//Enable inqsocial plugin
	$field_params = array(
		'type'      => 'checkbox',
		'id'        => 'inqsocial_enable',
		'desc'      => 'check to enable InQ.Social plugin'
	);
	add_settings_field('inqsocial_enable_field', 'Enable InQ.Social:', 'display_inqsocial_settings', 'inqsocial_settings', 'inqsocial_section', $field_params);
	
	//Enable InQ.Social plugin
	$field_params = array(
		'type'      => 'checkbox',
		'id'        => 'inqsocial_allow_pass_id',
		'desc'      => 'check to allow to pass user id (assigned in WordPress)'
	);
	add_settings_field('inqsocial_allow_pass_id_field', 'Allow to pass User Id:', 'display_inqsocial_settings', 'inqsocial_settings', 'inqsocial_section', $field_params);
	
	//Enable InQ.Social plugin
	$field_params = array(
		'type'      => 'checkbox',
		'id'        => 'inqsocial_allow_pass_email',
		'desc'      => 'check to allow to pass user E-Mail (assigned in WordPress)'
	);
	add_settings_field('inqsocial_allow_pass_email_field', 'Allow to pass E-Mail Address:', 'display_inqsocial_settings', 'inqsocial_settings', 'inqsocial_section', $field_params);
	
	//InQ.Social plugin state
	
	$field_params = array(
		'type'      => 'text',
		'id'        => 'inqsocial_website_id',
		'desc'      => 'InQ.Social Website Id (required)',
		'label_for' => 'inqsocial_plugin_state'
	);
	
	add_settings_field('inqsocial_website_id_field', 'InQ.Social Website Id:', 'display_inqsocial_settings', 'inqsocial_settings', 'inqsocial_section', $field_params);	
}

add_action('admin_init', 'set_inqsocial_options');

function validate_inqsocial_settings($input) {
	
	foreach ($input as $k => $v) {
		
		$valid_input[$k] = trim($v);
		
		if($k === 'inqsocial_website_id'){
			
			$source_value = substr($valid_input[$k], 0, 36);
			
			$valid_value = preg_replace('~[^a-z0-9\-]+~', '', $source_value);
			
			if(strlen($valid_value) === 36){				
				$valid_input[$k] = $valid_value;				
			} else {				
				$valid_input[$k] = '';				
			}			
		}		
	}
	
	return $valid_input;
}



function display_inqsocial_settings($args) {
	
	extract($args);
	
	$option_name = 'inqsocial_options';
	
	$o = get_option($option_name);
	
	switch ($type) {
		
		case 'text': 
		
			$o[$id] = esc_attr( stripslashes($o[$id]) );			
			echo "<input class='regular-text' type='text' id='$id' name='" . $option_name . "[$id]' value='$o[$id]' />";  			
			echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : ""; 
			
			break;
			
		case 'checkbox':
		
			$checked = ($o[$id] == 'on') ? " checked='checked'" :  '';			
			echo "<label><input type='checkbox' id='$id' name='" . $option_name . "[$id]' $checked />"; 			
			echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";			
			echo "</label>";
			
			break;			
	}	
}

function get_inqsocial_options(){	
	return get_option('inqsocial_options');	
}

//SET default value for the plugin settings
function set_inqsocial_default_options(){
	
	$default_option = get_option('inqsocial_options');
	
	$default_option['inqsocial_enable'] = '';	
	$default_option['inqsocial_allow_pass_id'] = '';	
	$default_option['inqsocial_allow_pass_email'] = '';	
	$default_option['inqsocial_website_id'] = '';
	
	update_option('inqsocial_options', $default_option);	
}

function remove_inqsocial_options(){	
	delete_option('inqsocial_options');	
}

register_deactivation_hook(__FILE__, 'remove_inqsocial_options');

function include_inqsocial_script(){
		
	$options = get_option('inqsocial_options');
	
	if($options['inqsocial_enable']){
		
		$website_id = $options['inqsocial_website_id'];
		
		if($website_id !== ''){
			
			$id_part = '';			
			$email_part = '';
			
			if(is_user_logged_in()){
				
				$current_user = wp_get_current_user();
				
				if($options['inqsocial_allow_pass_id']){				
					$id_part = 'id: "' . $current_user->ID . '",';				
				}
			
				if($options['inqsocial_allow_pass_email']){				
					$email_part = 'email: "' . $current_user->user_email . '",';				
				}				
			}
			
			echo '<script type="text/javascript">			
				var inq_customer = 
				{
					' . $id_part . '
					' . $email_part . '				
					name: null
					};		
				</script>
				<script src="https://widget.inq.social/website/inq_' . $website_id . '.js" type="text/javascript"></script>';		
		}		
	}	
}

add_action('wp_footer', 'include_inqsocial_script');

function inqsocial_settings_script(){
	
	wp_enqueue_script('jquery');	
	wp_enqueue_script("functionality", plugins_url('/settings.js', __FILE__));
	
}

add_action('admin_head', 'inqsocial_settings_script');
?>