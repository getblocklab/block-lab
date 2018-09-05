# Getting Started

Block Lab is a WordPress plugin which allows you to add **extra content blocks** to the new WordPress editor. These extra blocks can allow you to build custom structures of content for use on the posts and pages of your websites.

## <a name="custom-blocks"></a>Creating a custom block

Getting started with Block Lab is easy. Look for the **Custom Blocks** menu item in your WordPress admin sidebar, then press Add New.

Start by giving your block a name, in the **Enter block name here** field at the top of the screen.

Next, we need to define some settings for your Block in the **Block Properties** section.

- Choose a Category from the list, or create a new one. This is the heading your block will appear under in the WordPress editor's block selector.
- Write a description for your block.
- Give your block some keywords, used when searching for blocks in the WordPress editor's block selector.

The next section lets you define the options that will be presented to users when adding your new Block to their post. These options are known as **Fields**.

Fields are the points of data that could be different each time you use the block. For example, if you were creating a Button block, you would have fields for the button's text, and link URL.

Add a new field by pressing the Add Field button.

- Give your field a Label, which will be displayed next to the input field when adding the block.
- The field Name should auto-populate, but can be customised. This will be used by the block template.
- Choose the control Type for the field. This is the type of input presented to the user when adding the block.
- Depending on the Type, additional options will be available. Refer to the [Field Types](#field-types) section below for more details.



## <a name="block-templates"></a>Displaying custom blocks in your theme

In order for blocks to properly display, you'll need to create an associated HTML template, using the Block Lab API to include field data. These templates are commonly referred to as **Block Templates**.

### The Basics

The Block Template needs to be stored inside a `blocks` directory in your theme, using the slug of your block in the filename. The slug of your block can be seen in the Slug section of the Block editor. The correct format to use is: `block-{block name}.php`.

For example, if your block's slug is `testimonial`, Block Lab would look for the the Block Template file in your theme: `blocks/block-testimonial.php`. Block Lab first checks if the template exists in the child theme, and if not, in the parent theme.

### Block Previews

Sometimes a block's template markup will be too detailed to be properly previewed in the WordPress editor. In this case, you may create a **Preview Template**. This will be used instead of the Block Template while previewing the block in the WordPress editor. Preview Templates should be saved in your theme, as `block/preview-{block name}.php`.

### Example

A Block Template for a testimonial.

Template: `my-custom-theme/blocks/testimonial.php`

```HTML+PHP
<img src="<?php block_field( 'profile-picture' ); ?>" alt="<?php block_field( 'author-name' ); ?>" />
<h3><?php block_field( 'author-name' ); ?></h3>
<p><?php block_field( 'testimonial' ); ?></p>
```

### Overriding the template path

It is possible to change the template path so that it uses a custom template, outside of the theme or blocks directory.

To use a different template _inside_ your theme, use the `block_lab_override_theme_template( $theme_template )` filter. To use a different template _outside_ your theme (for example, in a plugin), use the `block_lab_template_path( $template_path )` filter.

[Read more about using filters on WordPress.org](https://codex.wordpress.org/Plugin_API).




## <a name="field-types"></a>Field Types

### Text

The Text field creates a simple text input option for the block.

#### Settings

- **Field instructions**: Help text to describe the field.
- **Required**: Whether the field will allow an empty value.
- **Default value**: The default value for this field when adding the block.
- **Placeholder**: The helper text which appears when the input is empty.
- **Character limit**: The maximum number of characters allowed to be entered.

#### Template Usage

The API will return a string.

```HTML+PHP
<h3><?php block_field( 'subtitle' ); ?></h3>
```



### Textarea

The Textarea field creates a multi-line text input option for the block, suitable for paragraphs.

#### Settings

- **Field instructions**: Help text to describe the field.
- **Required**: Whether the field will allow an empty value.
- **Default value**: The default value for this field when adding the block.
- **Placeholder**: The helper text which appears when the input is empty.
- **Character limit**: The maximum number of characters allowed to be entered.

#### Template Usage

The API will return a string.

```HTML+PHP
<div class="notification">
	<h2>Warning!</h2>
	<p><?php block_field( 'notification-message' ); ?></p>
</div>
```



### Select

The Select field creates a list-based menu input option for the block.

#### Settings

- **Field instructions**: Help text to describe the field.
- **Required**: Whether the field will allow an empty value.
- **Choices**: The available items in the list.
    - Enter each choice on a new line.
    - To specify the value and label separately, use this format: `foo : Foo`.
- **Default value**: The default value for this field when adding the block.
- **Allow multiple choices**: Whether multiple items from the list can be checked.

#### Template Usage

If Allow multiple choices is checked, then the API will return an array. Otherwise, the API will return a string.

With multiple values:

```HTML+PHP
<h2>Featuring:</h2>
<ul>
	<?php foreach ( block_value( 'features' ) as $value ) : ?>
	<li><?php echo $value; ?></li>
	<?php endforeach; ?>
</ul>
```

Without multiple values:

```HTML+PHP
<p>Size: <?php block_field( 'tshirt-size' ); ?></p>
```



### Toggle

The Toggle field creates an on / off toggle switch input option for the block.

#### Settings

- **Field instructions**: Help text to describe the field.
- **Default value**: The default value for this field when adding the block.

#### Template Usage

The API will return a boolean.

```HTML+PHP
<?php
$class = 'container';
if ( block_value( 'full-width' ) ) {
	echo $class .= ' full-width';
}
<div class="<?php echo $class; ?>">
</div>
?>
```



### Range

The Range field creates a number slider input option for the block, suitable for integers.

#### Settings

- **Field instructions**: Help text to describe the field.
- **Required**: Whether the field will allow an empty value.
- **Minimum value**: The minimum value that can be selected within the range.
- **Maximum value**: The maximum value that can be selected within the range.
- **Step size**: The smallest change possible while moving the slider.
- **Default value**: The default value for this field when adding the block

#### Template Usage

The API will return an integer.

```HTML+PHP
<div class="columns">
	<?php for ( $i = 1; $i <= block_value( 'number-of-columns' ); $i++ ) : ?>
	<div class="column">
		<p>Column #<?php echo $i; ?></p>
	</div>
	<?php endfor; ?>
</div>
```



### Checkbox

The Checkbox field creates a single checkbox input option for the block.

#### Settings

- **Field instructions**: Help text to describe the field.
- **Default value**: The default value for this field when adding the block.

#### Template Usage

The API will return a boolean.

```HTML+PHP
<?php
if ( block_value( 'show-avatar' ) ) {
	echo get_avatar( $email );
}
?>
```



### Radio

The Radio field creates a multi-choice input option for the block. Multiple items can not be selected.

#### Settings

- **Field instructions**: Help text to describe the field.
- **Required**: Whether the field will allow an empty value.
- **Choices**: The available items in the list.
    - Enter each choice on a new line.
    - To specify the value and label separately, use this format: `foo : Foo`.
- **Default value**: The default value for this field when adding the block.

#### Template Usage

The API will return a string.

```HTML+PHP
<p>Location: <?php block_field( 'location' ); ?></p>
```



## <a name="functions"></a>Helper Functions

These functions are for use in your block templates.

### block_field()

```PHP
block_field( $name, $echo = true );
```

Outputs the value of a specific field.

#### Parameters

- `$name` _(string)_ _(Required)_ The field name
- `$echo` _(bool)_ _(Optional)_ Whether the value should be output or returned.

#### Usage

Output the value of a field as text.

```HTML+PHP
<p><?php block_field( 'testimonial' ); ?></p>
```

Return the value of a field without outputting.

```PHP
$author_name = block_field( 'author', false );
```

Check if the value of the field is set.

```PHP
$url = block_field( 'url', false );

if ( ! empty( $url ) ) {
    echo '<a href="' . $url . '">Click me!</a>';
}
```



### block_value()

```PHP
block_value( $name );
```

Helper function for returning the value of a field without any output. Essentially the same as `block_field( $name, false );`.

#### Parameters

- `$name` _(string)_ _(Required)_ The field name

#### Usage

Return the value of a field.

```PHP
$author_name = block_value( 'author' );
```

