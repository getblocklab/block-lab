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
		<div class="bl_dashboard_welcome bl_tile" style="background-image: url('<?php echo esc_attr( block_lab()->get_assets_url( 'images/Block-Lab-Pro-Hero-Background-1.svg' ) ); ?>');">
			<div class="bl_tile_body">
				<div>
					<span><?php esc_html_e( 'Block Lab Pro', 'block-lab' ); ?></span>
					<h1><?php esc_html_e( 'Early Access Pass', 'block-lab' ); ?></h1>
					<p><?php esc_html_e( 'Lock in a lifetime discount and help kickstart Block Lab Pro.', 'block-lab' ); ?></p>
					<a target="_blank" class="bl_button bl_button--white" href="https://getblocklab.com/pro"><?php esc_html_e( 'Check out Early Access', 'block-lab' ); ?></a>
					<input class="bl_input_text" placeholder="License key" type="text" />
					<a class="bl_button bl_button--white-stroke" href=""><?php esc_html_e( 'Enter Your License Key', 'block-lab' ); ?></a>
				</div>
				<div>
					<img class="bl_early_access_pass" src="<?php echo esc_attr( block_lab()->get_assets_url( 'images/block_lab_early_access_pass.png' ) ); ?>" alt="" />
				</div>
			</div>
		</div>
		<!-- Dashboard Tile -->
		<div class="bl_tile bl_tile_2">
			<div class="bl_tile_header">
				<img class="bl_tile_icon" src="<?php echo esc_attr( block_lab()->get_assets_url( 'images/block_lab_admin_icon_new_fields.svg' ) ); ?>" alt="" />
			</div>
			<div class="bl_tile_body">
				<h4 class="bl_align_center"><?php esc_html_e( 'Pro Fields', 'block-lab' ); ?></h4>
				<p class="bl_align_center"><?php esc_html_e( 'More fields including repeater, post object, and more to help you build the custom blocks you need for yourself and your clients.', 'block-lab' ); ?></p>
			</div>
		</div>
		<!-- Dashboard Tile -->
		<div class="bl_tile bl_tile_2">
			<div class="bl_tile_header">
				<img class="bl_tile_icon" src="<?php echo esc_attr( block_lab()->get_assets_url( 'images/block_lab_admin_icon_features.svg' ) ); ?>" alt="" />
			</div>
			<div class="bl_tile_body">
				<h4 class="bl_align_center"><?php esc_html_e( 'Pro Features', 'block-lab' ); ?></h4>
				<p class="bl_align_center"><?php esc_html_e( 'Features including conditional logic, custom validation, and white-labeling, to help you extend Block Lab and leverage the best of Gutenberg.', 'block-lab' ); ?></p>
			</div>
		</div>
		<!-- Dashboard Tile -->
		<div class="bl_tile bl_tile_2">
			<div class="bl_tile_header">
			<img class="bl_tile_icon" src="<?php echo esc_attr( block_lab()->get_assets_url( 'images/block_lab_admin_icon_support.svg' ) ); ?>" alt="" />
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
