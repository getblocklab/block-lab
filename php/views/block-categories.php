<?php
/**
 * Block Lab settings form for the Categories tab.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2018, Block Lab
 * @license   http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

if ( ! empty( $_POST ) ) {
	check_admin_referer( 'block_lab_save_block_categories' );
	if ( isset( $_POST['option'] ) ) {
		$maybe_categories = maybe_unserialize( get_site_option( 'block_lab_custom_categories', array() ) );
		$post_sanitized   = wp_unslash( $_POST );
		foreach ( $post_sanitized['option'] as $slug => $value ) {
			foreach ( $maybe_categories as $index => $data ) {
				if ( $slug === $data['slug'] ) {
					unset( $maybe_categories[ $index ] );
				}
			}
		}
		update_site_option( 'block_lab_custom_categories', $maybe_categories );
	}
	?>
	<div class="notice notice-success">
		<p><strong><?php esc_html_e( 'Settings Saved.', 'block-lab' ); ?></strong></p>
	</div>
	<?php
}
?>
<form method="post" action="">
	<?php
	wp_nonce_field( 'block_lab_save_block_categories' );
	?>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">
				<label><?php esc_html_e( 'Custom Block Categories', 'block-lab' ); ?></label>
			</th>
			<td>
				<?php
				$maybe_categories = get_site_option( 'block_lab_custom_categories', array() );
				if ( false === $maybe_categories || empty( $maybe_categories ) ) {
					esc_html_e( 'There are no categories to display.', 'block-lab' );
				} else {
					$maybe_categories = maybe_unserialize( $maybe_categories );
					foreach ( $maybe_categories as $category ) {
						?>
						<div>
							<h3><?php echo esc_html( $category['category'] ); ?></h3>
							<label>
								<input
									type="checkbox"
									name="option[<?php echo esc_attr( $category['slug'] ); ?>]" value="on"
								/>
								<?php esc_html_e( 'Remove', 'block-lab' ); ?> <?php echo esc_html( $category['category'] ); ?>
							</label>
						</div>
						<?php
					}
				}
				?>
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>
</form>
