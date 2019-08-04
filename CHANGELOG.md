## Changelog #

### 1.3.5 – 2019-08-05 ###

* New: Block Lab will now enqueue a global stylesheet, so you can keep your common block styles in one place. [Read more](https://github.com/getblocklab/block-lab/pull/371)
* New: Block templates can now be placed inside a sub-folder, for an even cleaner directory structure. [Read more](https://github.com/getblocklab/block-lab/pull/372)
* Fix: Child theme templates are now correctly loaded before their parent templates.

### 1.3.4 - 2019-07-22 ###

* New: Block Lab grew to level 1.3.4. Block Lab learned **Custom Categories**.
* Tweak: **@phpbits** used Pull Request. All right! The **`block_lab_get_block_attributes`** filter was caught!
* Tweak: **Template Loader** used Harden. **Template Loader**'s defense rose!
* Tweak: Booted up a TM! It contained **Unit Tests**!
* Fix: Wild **Missing Filter in Inspector Controls** bug appeared! Go! Bugfix!
* Fix: Enemy **Mixed Up Inspector Controls** fainted! @kienstra gained 0902a06 EXP. Points!

### 1.3.3 - 2019-06-21 ###

* Fix: The previous release broke the `className` field, used for the Additional CSS Class setting. This fixes it.

### 1.3.2 - 2019-06-21 ###

* New: Rich Text Control (for Block Lab Pro users)!
* New: Show Block Category in the list table
* New: We've got a new `block_lab_render_template` hook which fires before rendering a block on the front end. Great for enqueuing JS
* Tweak: Updated logo
* Tweak: Prevent block field slugs from changing when you edit the field title
* Fix: Saving your license key no longer results in an error page
* Fix: License details screen showing the wrong information
* Fix: Remove duplicate IDs on the edit block screen
* Fix: Range sliders can now set a minimum value of zero
* Fix: A console warning about unique props

### 1.3.1 - 2019-05-22 ###

* New: Support for Gutenberg's built-in Additional CSS Class in your block template, by using the field `className`. [Read more](https://github.com/getblocklab/block-lab/wiki/7.-FAQ)
* New: The Textarea field now has an option to automatically add paragraph tags and line-breaks
* Fix: Bug affecting blocks containing Pro fields when there's no active Pro license

### 1.3.0 - 2019-04-30 ###

**Important**: This update includes a backwards compatibility break related to the User field.

[Read more here](https://github.com/getblocklab/block-lab/pull/294#issue-272649668)

* New: A Taxonomy control type, for selecting a Category / Tag / or custom term from a dropdown menu (for Block Lab Pro users)
* Fix: Bug with the Post control when outputting data with block_field()
* Tweak: Update the User control to store data as an object, matching the Post control

### 1.2.3 - 2019-04-23 ###

**Important**: This update includes a backwards compatibility break related to the Image field.
If you are using the `block_value()` function with an image field and externally hosted images, this update may effect you.

[Read more here](https://getblocklab.com/backwards-compatability-break-for-the-image-field/)

* New: A Post control type, for selecting a Post from a dropdown menu (for Block Lab Pro users)
* New: Added the block_lab_controls filter to allow custom controls to be loaded (props @rohan2388)
* New: The Image control now returns the image's Post ID
* Tweak: Travis CI support

### 1.2.2 - 2019-04-05 ###

* New: Block Editor redesign

### 1.2.1 - 2019-03-21 ###

* New: Automatic stylesheet enqueuing. Now you can create custom stylesheets for individual blocks! [Read more here](https://github.com/getblocklab/block-lab/wiki/5.-Styling-Custom-Blocks).
* New: A User control type (for Block Lab Pro users)
* Fix: Various multiselect bug fixes, allowing for empty values in the multiselect control

### 1.2.0 - 2019-02-27 ###

* New: Introducing Block Lab Pro!
* New: A setting for the number of rows to display in a Textarea control
* Fix: Allow negative numbers in Number and Range controls

### 1.1.3 - 2019-01-25 ###

* New: Image field

### 1.1.2 - 2019-01-11 ###

* New: Color field
* Fix: Incorrect output for empty fields

### 1.1.1 - 2018-12-14 ###

* Fix: Undefined index error for multiselect and select fields
* Fix: Correct values now returned for boolean fields like checkbox and toggle
* Fix: Editor preview templates are back! Use the filename `preview-{blog slug}.php`
* Fix: "Field instructions" is now a single line text, and renamed to "Help Text"
* Fix: Slashes being added to field options
* Fix: Allow empty value for select and number fields
* Fix: Allow empty default values

### 1.1.0 - 2018-12-07 ###

* New: Complete revamp of the in-editor preview
* New: Email field
* New: URL field
* New: Number field
* New: `block_config()` and `block_field_config` helper functions, to retrieve your block's configuration
* Fix: filemtime errors
* Fix: HTML tags were being merged together when previewed in the editor
* Fix: Problems with quotes and dashes in a block's title or field parameters
* Fix: `field_value()` sometimes returned the wrong value
* Fix: Incorrect values shown in the editor preview

### 1.0.1 - 2018-11-16 ###

* New: Added "Save Draft" button, so you can save Blocks-in-Progress
* New: Better handling of the auto-slug feature, so you don't accidentally change your block's slug
* New: Better expanding / contracting of the Field settings
* New: Emoji (and special character) support! 😎
* Fix: Resolved Fatal Error that could occur in some environments
* Fix: Remove unused "Description" field
* Fix: Remove duplicate star icon

### 1.0.0 - 2018-11-14 ###

__Rename!__
* Advanced Custom Blocks is now Block Lab

__Added__
* New control types (Radio, Checkbox, Toggle, Select, Range)
* Block icons
* Field location – add your block fields to the inspector
* List table refinements
* Field repeater table improvements

__Fixed__
* All the things. Probably not _all_ the things, but close.

### 0.1.2 - 2018-08-10 ###

__Added__
* New properties `help`, `default` and `required` added to fields.
* Ability to import blocks from a `{theme}/blocks/blocks.json` file. Documentation still to be added.
* Gutenberg controls library updated preparing for `0.0.3`.

__Technical Changes__
* Updated control architecture to improve development and adding adding of additional controls.
* Clean up enqueuing of scripts.

### 0.1 - 2018-08-03 ###
* Initial release.