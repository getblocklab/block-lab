<?php
/**
 * Block Lab settings form for the License tab.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2018, Block Lab
 * @license   http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

?>
<form method="post" action="options.php">
	<?php
	settings_fields( 'block-lab-license-key' );
	do_settings_sections( 'block-lab-license-key' );
	?>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">
				<label for="block_lab_license_key"><?php esc_html_e( 'License key', 'block-lab' ); ?></label>
			</th>
			<td>
				<input type="password" name="block_lab_license_key" id="block_lab_license_key" class="regular-text" value="<?php echo esc_attr( get_option( 'block_lab_license_key' ) ); ?>" />
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>
</form>
