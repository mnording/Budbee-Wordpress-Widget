<?php
/**
 *
 * Main File of plugin
 *
 * @category Plugins
 * @package  BudbeeWooWidget
 * @author   Name <email@email.com>
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link     http://url.com
 */

/**
 * Undocumented class
 */
class BudbeeWidgetSettings {
	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'budbee_widget_plugin_create_menu' ) );
	}
	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function budbee_widget_plugin_create_menu() {
		add_menu_page( 'Budbee Widget Settings', 'Budbee Widget', 'administrator', 'budbee-widget-settings', array( $this, 'budbee_widget_plugin_settings_page' ), plugins_url( '/images/icon.png', __FILE__ ) );
		add_action( 'admin_init', array( $this, 'register_budbee_widget_plugin_settings' ) );
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function register_budbee_widget_plugin_settings() {
		register_setting( 'budbee-widget-plugin-settings-group', 'budbee_widget_api_key' );
		register_setting( 'budbee-widget-plugin-settings-group', 'budbee_widget_api_secret' );
		register_setting( 'budbee-widget-plugin-settings-group', 'budbee_widget_placement_hook' );
	}
	/**
	 * Undocumented function
	 *
	 * @return String The Api Key from Budbee
	 */
	public function get_api_key() {
		return get_option( 'budbee_widget_api_key' );
	}
	/**
	 * Undocumented function
	 *
	 * @return String The Api Secret from Budbee
	 */
	public function get_api_secret() {
		return get_option( 'budbee_widget_api_secret' );
	}
	/**
	 * Undocumented function
	 *
	 * @return String The name of the hook where the widget should be added
	 */
	public function get_placement_hook() {
		return get_option( 'budbee_widget_placement_hook' );
	}
	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function budbee_widget_plugin_settings_page() {
		?>
<div class="wrap">
<h1>Budbee Widget</h1>

<form method="post" action="options.php">
		<?php settings_fields( 'budbee-widget-plugin-settings-group' ); ?>
		<?php do_settings_sections( 'budbee-widget-plugin-settings-group' ); ?>
	<table class="form-table">
		<tr valign="top">
		<th scope="row">Api Key</th>
		<td><input type="text" name="budbee_widget_api_key" value="<?php echo esc_attr( get_option( 'budbee_widget_api_key' ) ); ?>" /></td>
		</tr>
		<tr valign="top">
		<th scope="row">Api Secret</th>
		<td><input type="text" name="budbee_widget_api_secret" value="<?php echo esc_attr( get_option( 'budbee_widget_api_secret' ) ); ?>" /></td>
		</tr>
		<tr valign="top">
		<th scope="row">Hook to display widget at</th>
		<td><input type="text" name="budbee_widget_placement_hook" value="<?php echo esc_attr( get_option( 'budbee_widget_placement_hook' ) ); ?>" /></td>
		</tr>
	</table>
		<?php submit_button(); ?>
</form>
</div>
	<?php }
}
?>
