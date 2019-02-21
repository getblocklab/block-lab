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
                    <span>Block Lab Pro</span>
                    <h1>Early Access Pass</h1>
                    <p>Lock in a lifetime discount and help kickstart Block Lab Pro.</p>
                    <a target="_blank" class="bl_button bl_button--white" href="https://getblocklab.com/pro">Check out Early Access</a>
                    <!-- <a class="bl_button bl_button--white-stroke" href="">Enter Your License Key</a> -->
                    <input class="bl_input_text" placeholder="License key" type="text">
                </div>
                <div>
                    <img class="bl_early_access_pass" src="<?php echo esc_attr( block_lab()->get_assets_url( 'images/block_lab_early_access_pass.svg' ) ); ?>" alt="">
                </div>
            </div>
        </div>
        <!-- Dashboard Tile -->
        <div class="bl_tile bl_tile_2">
            <div class="bl_tile_header">
                <img class="bl_tile_icon" src="<?php echo esc_attr( block_lab()->get_assets_url( 'images/block_lab_wpadmin_icon_newfields.svg' ) ); ?>" alt="">
            </div>
            <div class="bl_tile_body"> 
                <h4 class="bl_align_center">New Fields</h4>
                <p class="bl_align_center">More fields including repeater, post object, and more to help you build the custom blocks you need for yourself and your clients.</p>
            </div>
        </div>
        <!-- Dashboard Tile -->
        <div class="bl_tile bl_tile_2">
            <div class="bl_tile_header">
                <img class="bl_tile_icon" src="<?php echo esc_attr( block_lab()->get_assets_url( 'images/block_lab_wpadmin_icon_newfields.svg' ) ); ?>" alt="">
            </div>
            <div class="bl_tile_body">
                <h4 class="bl_align_center">New Features</h4>
                <p class="bl_align_center">Features including conditional logic, custom validation, and white-labeling, to help you extend Block Lab and leverage the best of Gutenberg.</p>
            </div>
        </div>
        <!-- Dashboard Tile -->
        <div class="bl_tile bl_tile_2">
            <div class="bl_tile_header">
                <img class="bl_tile_icon" src="<?php echo esc_attr( block_lab()->get_assets_url( 'images/block_lab_wpadmin_icon_newfields.svg' ) ); ?>" alt="">
            </div>
            <div class="bl_tile_body">
                <h4 class="bl_align_center">Support & Updates</h4>
                <p class="bl_align_center">Priority support and regular feature and fix updates.</p>
            </div>
        </div>
        <!-- Dashboard Tile -->
        <div class="bl_tile bl_tile_3">
        <div class="bl_tile_body">
                <h4>★★ Loving Block Lab? ★★</h4>
                <p>If Block Lab has helped you make the most of Gutenberg and custom blocks for your site, leave us a review on WordPress.org.</p>
            </div>
            <div class="bl_tile_footer">
                <a class="bl_button" target="_blank" href="https://wordpress.org/plugins/block-lab/#reviews">★ Leave Review</a>
            </div>
        </div>
        <!-- Dashboard Tile -->
        <div class="bl_tile bl_tile_3">
        <div class="bl_tile_body">
                <h4>Get more out of Block Lab</h4>
                <p>Subscribe to our newsletter for news, updates, and tutorials on working with Gutenberg.</p>
            </div>
            <div class="bl_tile_footer">
                <!-- <a class="bl_button" target="_blank" href="http://eepurl.com/dO6l8n">Subscribe</a> -->
                <!-- Begin Mailchimp Signup Form -->
                <link href="//cdn-images.mailchimp.com/embedcode/classic-10_7.css" rel="stylesheet" type="text/css">
                <style type="text/css">
                    #mc_embed_signup{background:#fff; clear:left; font:14px Helvetica,Arial,sans-serif; }
                    /* Add your own Mailchimp form style overrides in your site stylesheet or in this style block.
                    We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
                </style>
                <div id="mc_embed_signup">
                    <form action="https://getblocklab.us19.list-manage.com/subscribe/post?u=f8e0c6b0ab32fc57ded52ab4a&amp;id=f05b221414" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
                        <div id="mc_embed_signup_scroll">
                        
                    <div class="mc-field-group">
                        <label class="bl_input_label" for="mce-EMAIL">Email Address </label>
                        <input class="bl_input" type="email" value="" placeholder="Email Address" name="EMAIL" class="required email" id="mce-EMAIL">
                    </div>
                        <div id="mce-responses" class="clear">
                            <div class="response" id="mce-error-response" style="display:none"></div>
                            <div class="response" id="mce-success-response" style="display:none"></div>
                        </div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                        <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_f8e0c6b0ab32fc57ded52ab4a_f05b221414" tabindex="-1" value=""></div>
                        <div class="clear"><input class="bl_button" type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
                        </div>
                    </form>
                </div>

                <!--End mc_embed_signup-->
            </div>
        </div>
        <div class="bl_section_heading">
            <h2>Block Lab Pro Features</h2>
        </div>
    </section>
</div>