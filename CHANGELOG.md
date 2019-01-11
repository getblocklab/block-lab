## Changelog #
 
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
* Ability to import blocks from a `{theme}/blocks/blocks.json` file.
  Documentation still to be added.
* Gutenberg controls library updated preparing for `0.0.3`.

__Technical Changes__ 
* Updated control architecture to improve development 
  and adding adding of additional controls. 
* Clean up enqueuing of scripts.
 
### 0.1 - 2018-08-03 ###
* Initial release.