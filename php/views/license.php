<?php
/**
 * Block Lab settings form for the License tab.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
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
				<label><?php esc_html_e( 'License', 'block-lab' ); ?></label>
			</th>
			<td>
				<?php
				if ( block_lab()->is_pro() ) {
					$license = block_lab()->admin->license->get_license();

					$limit = __( 'unlimited', 'block-lab' );
					if ( isset( $license['license_limit'] ) && intval( $license['license_limit'] ) > 0 ) {
						$limit = $license['license_limit'];
					}

					$count = '0';
					if ( isset( $license['site_count'] ) ) {
						$count = $license['site_count'];
					}

					$expiry = gmdate( get_option( 'date_format' ) );
					if ( isset( $license['expires'] ) ) {
						$expiry = gmdate( get_option( 'date_format' ), strtotime( $license['expires'] ) );
					}

					echo wp_kses_post(
						sprintf(
							'<p>%1$s %2$s</p>',
							sprintf(
								// translators: A number, wrapped in <strong> tags.
								__( 'Your license includes %1$s site installs.', 'block-lab' ),
								'<strong>' . $limit . '</strong>'
							),
							sprintf(
								// translators: A number, wrapped in <strong> tags.
								__( '%1$s of them are in use.', 'block-lab' ),
								'<strong>' . $count . '</strong>'
							)
						)
					);

					echo wp_kses_post(
						sprintf(
							'<p>%1$s %2$s</p>',
							sprintf(
								// translators: A date.
								__( 'Your license expires on %1$s.', 'block-lab' ),
								'<strong>' . $expiry . '</strong>'
							),
							sprintf(
								// translators: An opening and closing anchor tag.
								__( '%1$sManage Licenses%2$s', 'block-lab' ),
								'<a href="https://getblocklab.com/checkout/purchase-history/" target="_blank">',
								'</a>'
							)
						)
					);
				} else {
					echo wp_kses_post(
						sprintf(
							'<p>%1$s</p>',
							__( 'No license was found for this installation.', 'block-lab' )
						)
					);
				}
				?>
			</td>
		</tr>
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
