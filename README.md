# Block Lab #

Contributors: lukecarbis, ryankienstra, Stino11, rheinardkorf
Tags: gutenberg, blocks, block editor, fields, template
Requires at least: 5.0
Tested up to: 5.5
Requires PHP: 5.6
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl

The easy way to build custom blocks for Gutenberg.

## Description ##

### IMPORTANT! ###
The Block Lab team has moved its custom block efforts over to [Genesis Custom Blocks](https://wordpress.org/plugins/genesis-custom-blocks/). To take advantage of all the great things about Block Lab as well as gain access to all new features as they are released, we recommend that you install Genesis Custom Blocks.

If you’re an existing Block Lab user and would like to learn more about what this means for you, including how to easily and automatically migrate to the new plugin, you can find more details [here](https://getblocklab.com/welcome-to-genesis-custom-blocks).

----

Gutenberg, the new WordPress editor, opens up a whole new world for the way we build pages, posts, and websites with WordPress. Block Lab makes it easy to harness Gutenberg and build custom blocks the way you want them to be built. Whether you want to implement a custom design, deliver unique functionality, or even remove your dependence on other plugins, Block Lab equips you with the tools you need to hit “Publish” sooner.

Notice: If you haven't used Block Lab yet, please install [Genesis Custom Blocks](https://wordpress.org/plugins/genesis-custom-blocks) instead. If you have used it, Block Lab now has a migration UI that will ensure your blocks and content work the same in [Genesis Custom Blocks](https://wordpress.org/plugins/genesis-custom-blocks), and you'll have the same features.

Block Lab will still be maintained in the mid-term, but won't get new features.

## Features ##

### A Familiar Experience ###
Work within the WordPress admin with an interface you already know.

### Block Fields ###
Add from a growing list of available fields to your custom blocks.

### Simple Templating ###
Let the plugin do the heavy lifting so you can use familiar WordPress development practices to build block templates.

### Developer Friendly Functions ###
Simple to use functions, ready to render and work with the values stored through your custom block fields.

## Links ##
* [WordPress.org](https://wordpress.org/plugins/block-lab)
* [Github](https://github.com/getblocklab/block-lab)
* [Documentation](https://getblocklab.com/docs)
* [Support](https://wordpress.org/support/plugin/block-lab)

## Installation ##
### From Within WordPress ###
* Visit Plugins > Add New
* Search for "Block Lab"
* Install the Block Lab plugin
* Activate Block Lab from your Plugins page.

### Manually ###
* Clone Block Lab into a working directory with `https://github.com/getblocklab/block-lab.git`
* `cd` into the `block-lab` directory, and run `npm install && composer install`
* Next, build the scripts and styles with `npm build`
* Move the `block-lab` folder to your `/wp-content/plugins/` directory
* Activate the Block Lab plugin through the Plugins menu in WordPress

## Frequently Asked Questions ###
**Q: Do I need to write code to use this plugin?**
A: Although the plugin handles the majority of the work in building a custom block, you will need to build HTML templates to display the content of the block. You can learn how in the developer documentation.

**Q: I have an idea for the plugin**
A: This plugin is open source and can only be better through community contribution. The GitHub repo is [here](https://github.com/getblocklab/block-lab).

**Q: Where can I find documentation for this plugin?**
A: [Here](https://getblocklab.com/docs/)

## Contributing ##

See [Contributing to Genesis Custom Blocks](https://github.com/studiopress/genesis-custom-blocks/blob/develop/CONTRIBUTING.md).
