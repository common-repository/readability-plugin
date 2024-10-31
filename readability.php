<?php
/*
Plugin Name: Readability
Plugin URI: http://www.oneindonesia.com
Description: With Readability Wordpress plugin, it will turn your Wordpress website / blog into a comfortable reading view right in your web browser for your users. Too busy to read by right then and there? Readability makes it simple to let your users save  favorite articles for reading later.
Version: 1.0
Author: Daya S.
Author URI: http://oneindonesia.com
License:  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

define("readability","1.0",false);

function readability_url( $path = '' ) {
	global $wp_version;
	if ( version_compare( $wp_version, '2.8', '<' ) ) { 
		$folder = dirname( plugin_basename( __FILE__ ) );
		if ( '.' != $folder )
			$path = path_join( ltrim( $folder, '/' ), $path );

		return plugins_url( $path );
	}
	return plugins_url( $path, __FILE__ );
}

function activate_readability() {
	global $readability_options;
	$readability_options = array('position_button'=>'after',
							   'style'=>'horizontal', 
							   'own_css'=>'float: right;');
	add_option('readability_options',$readability_options);
}	

global $readability_options;	

$readability_options = get_option('readability_options');		
	  
register_activation_hook( __FILE__, 'activate_readability' );

function add_readability_automatic($content){ 
	global $readability_options, $post;
 
	$own_css = $readability_options['own_css'];
	$credit_to_Author = $readability_options['credit_to_Author'];
	
	$htmlCode .= "<div style=\"$own_css\">";
	$htmlCode .= "<div id='rdbWrapper'></div><script type='text/javascript'>
(function() {
    var s     = document.getElementsByTagName('script')[0],
        rdb   = document.createElement('script');
    rdb.type  = 'text/javascript';
    rdb.async = true;
    rdb.src   = document.location.protocol + '//www.readability.com/embed.js';
    s.parentNode.insertBefore(rdb, s);
})();
</script>\n";
	
	$htmlCode .="</div>";
 
	$readability = $htmlCode;
	if($readability_options['position_button'] == 'before' ){
		$content = $readability . $content;
	}
	else if($readability_options['position_button'] == 'after' ){
		$content = $content . $readability;
	} else  if($readability_options['position_button'] == 'before_and_after' ){
		$content = $readability. $content. $readability;
	}
	return $content;
}

if ($readability_options['position_button'] != 'manual'){
	add_filter('the_content','add_readability_automatic'); 
}

function add_readability(){
	global $readability_options, $post;
	$own_css = $readability_options['own_css'];
	$credit_to_Author = $readability_options['credit_to_Author'];

	$htmlCode .= "<div style=\"$own_css\">";
	$htmlCode .= "<div id='rdbWrapper'></div><script type='text/javascript'>
(function() {
    var s     = document.getElementsByTagName('script')[0],
        rdb   = document.createElement('script');
    rdb.type  = 'text/javascript';
    rdb.async = true;
    rdb.src   = document.location.protocol + '//www.readability.com/embed.js';
    s.parentNode.insertBefore(rdb, s);
})();
</script>\n";
	
	$htmlCode .="</div>";
			
	$readability = $htmlCode;

	echo $readability;
}

// function for adding settings page to wp-admin
function readability_settings() {
	add_options_page('Readability', 'Readability', 9, basename(__FILE__), 'readability_options_form');
}

function readability_options_form(){ 
	global $readability_options;
?>

<div class="wrap">

<form method="post" action="options.php">

<?php settings_fields('readability_options_group'); ?>

<h2>Readability Setting</h2> 

<table class="form-table" style="clear:none;width:70%;">
<tr valign="top">
<th scope="row">Location of Readability Buttons:</th>
<td><select name="readability_options[position_button]" id="position_button" >
<option value="before" <?php if ($readability_options['position_button'] == "before"){ echo "selected";}?> >Before Content</option>
<option value="after" <?php if ($readability_options['position_button'] == "after"){ echo "selected";}?> >After Content</option>
<option value="before_and_after" <?php if ($readability_options['position_button'] == "before_and_after"){ echo "selected";}?> >Before and After</option>
<option value="manual" <?php if ($readability_options['position_button'] == "manual"){ echo "selected";}?> >Manual Insertion</option>
</select><br/>
<b>Note:</b> &nbsp;You can also use this tag <code>add_readability();</code> for manually insert button to any of your post item.
</td>
</tr>

<tr valign="top">
<th scope="row">Custom CSS for &lt;div&gt; (i.e. float: right;):</th>
<td><input id="own_css" name="readability_options[own_css]" value="<?php echo $readability_options['own_css']; ?>"></td>
</td>
</tr>

</table>

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Update Setting') ?>" />
</p>

</form>

</div>
<?php }

// Hook for adding admin menus
if ( is_admin() ){ // admin actions
  add_action('admin_menu', 'readability_settings');
  add_action( 'admin_init', 'register_readability_settings' ); 
} 
function register_readability_settings() { // whitelist options
  register_setting( 'readability_options_group', 'readability_options' );
}

?>