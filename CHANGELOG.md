## Changelog #

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