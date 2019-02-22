<?php
/**
 * Block Lab Pro upgrade page.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2018, Block Lab
 * @license   http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

?>
<div class="bl_admin">
	<section class="bl_container">
		<div class="bl_dashboard_welcome bl_tile" style="background-image: url('https://getblocklab.com/wp-content/uploads/2019/02/Block-Lab-Pro-Hero-Background-1.svg');">
			<div class="bl_tile_body">
				<div>
					<span><?php esc_html_e( 'Block Lab Pro', 'block-lab' ); ?></span>
					<h1><?php esc_html_e( 'Early Access Pass', 'block-lab' ); ?></h1>
					<p><?php esc_html_e( 'Lock in a lifetime discount and help kickstart Block Lab Pro.', 'block-lab' ); ?></p>
					<div class="bl_cta_license_form_wrapper">
						<form class="bl_license_key_form" method="post" action="options.php">
							<?php
							register_setting( 'block-lab-license-key', 'block_lab_license_key' );
							settings_fields( 'block-lab-license-key' );
							?>
							<input class="bl_input_text" placeholder="Enter license key" name="block_lab_license_key" type="text" />
							<input class="bl_button" type="submit" value="<?php esc_html_e( 'Activate', 'block-lab' ); ?>" />
						</form>
						<p class="bl_license_key_text">or</p>
						<a target="_blank" class="bl_button bl_button--white bl_button_cta" href="https://getblocklab.com/pro"><?php esc_html_e( 'Check out Early Access', 'block-lab' ); ?></a>
					</div>
				</div>
				<div>
					<img class="bl_early_access_pass" src="https://getblocklab.com/wp-content/uploads/2019/02/block_lab_early_access_pass.png" alt="" />
				</div>
			</div>
		</div>
		<!-- Dashboard Tile -->
		<div class="bl_tile bl_tile_2">
			<div class="bl_tile_header">
				<div class="bl_tile_icon_wrapper" style="background-image: url('https://getblocklab.com/wp-content/uploads/2019/02/block_lab_admin_icon_new_fields.svg');"></div>
			</div>
			<div class="bl_tile_body">
				<h4 class="bl_align_center"><?php esc_html_e( 'Pro Fields', 'block-lab' ); ?></h4>
				<p class="bl_align_center"><?php esc_html_e( 'More fields including repeater, post object, and more to help you build the custom blocks you need for yourself and your clients.', 'block-lab' ); ?></p>
			</div>
		</div>
		<!-- Dashboard Tile -->
		<div class="bl_tile bl_tile_2">
			<div class="bl_tile_header">
				<div class="bl_tile_icon_wrapper" style="background-image: url('https://getblocklab.com/wp-content/uploads/2019/02/block_lab_admin_icon_features.svg');"></div>
			</div>
			<div class="bl_tile_body">
				<h4 class="bl_align_center"><?php esc_html_e( 'Pro Features', 'block-lab' ); ?></h4>
				<p class="bl_align_center"><?php esc_html_e( 'Features including conditional logic, custom validation, and white-labeling, to help you extend Block Lab and leverage the best of Gutenberg.', 'block-lab' ); ?></p>
			</div>
		</div>
		<!-- Dashboard Tile -->
		<div class="bl_tile bl_tile_2">
			<div class="bl_tile_header">
				<div class="bl_tile_icon_wrapper" style="background-image: url('https://getblocklab.com/wp-content/uploads/2019/02/block_lab_admin_icon_support.svg');"></div>
			</div>
			<div class="bl_tile_body">
				<h4 class="bl_align_center"><?php esc_html_e( 'Support & Updates', 'block-lab' ); ?></h4>
				<p class="bl_align_center"><?php esc_html_e( 'Priority support and regular feature and fix updates.', 'block-lab' ); ?></p>
			</div>
		</div>
		<!-- Dashboard Tile -->
		<div class="bl_tile bl_tile_3">
		<div class="bl_tile_body">
				<h4><?php esc_html_e( '★★ Loving Block Lab? ★★', 'block-lab' ); ?></h4>
				<p><?php esc_html_e( 'If Block Lab has helped you build amazing custom blocks for your site, leave us a review on WordPress.org.', 'block-lab' ); ?></p>
				<a class="bl_button" target="_blank" href="https://wordpress.org/plugins/block-lab/#reviews"><?php esc_html_e( '★ Leave Review ★', 'block-lab' ); ?></a>
			</div>
		</div>
		<!-- Dashboard Tile -->
		<div class="bl_tile bl_tile_3">
		<div class="bl_tile_body">
				<h4><?php esc_html_e( 'Get more out of Block Lab', 'block-lab' ); ?></h4>
				<p><?php esc_html_e( 'Subscribe to our newsletter for news, updates, and tutorials on working with Gutenberg.', 'block-lab' ); ?></p>
			</div>
			<div class="bl_tile_footer bl_tile_footer_email">
				<div id="mc_embed_signup">
					<form action="https://getblocklab.us19.list-manage.com/subscribe/post?u=f8e0c6b0ab32fc57ded52ab4a&amp;id=f05b221414" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
						<div id="mc_embed_signup_scroll">
							<div class="mc-field-group">
								<label class="bl_input_label" for="mce-EMAIL">Email Address </label>
								<input class="bl_input" type="email" value="" placeholder="Email Address" name="EMAIL" id="mce-EMAIL" />
							</div>
							<div id="mce-responses" class="clear">
								<div class="response" id="mce-error-response" style="display:none"></div>
								<div class="response" id="mce-success-response" style="display:none"></div>
							</div>
							<div class="clear">
								<input class="bl_button" type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" />
							</div>
						</div>
					</form>
				</div>

				<!--End mc_embed_signup-->
			</div>
		</div>
	</section>
</div>
