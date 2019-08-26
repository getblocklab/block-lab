<?php
/**
 * A mock template for a block, testing all fields except the repeater.
 *
 * @package Block_Lab
 */

// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaping could interfere with testing block_value().

$string_fields = array(
	'text',
	'textarea',
	'url',
	'email',
	'number',
	'color',
	'image',
	'select',
	'toggle',
	'range',
	'checkbox',
	'radio',
	'rich-text',
);

foreach ( $string_fields as $field ) :
	?>
	<p class="<?php block_field( 'className' ); ?>">
		<?php
		/* translators: %s is the field name */
		printf(
			'Here is the result of block_field() for %s: ',
			$field
		);
		block_field( $field );
		?>
	</p>

	<p>
		<?php
		/* translators: %s is the field name, %s is the result of calling block_value() */
		printf(
			'And here is the result of calling block_value() for %s: %s',
			$field,
			block_value( $field )
		);
		?>
	</p>
	<?php
endforeach;

$non_string_fields = array(
	'post'     => array( 'ID', 'post_name' ),
	'taxonomy' => array( 'term_id', 'name' ),
	'user'     => array( 'ID', 'first_name' ),
);

foreach ( $non_string_fields as $name => $value ) :
	/* translators: %s is the field name */
	printf(
		'Here is the result of block_field() for %s: ',
		$name
	);
	block_field( $name );

	$block_value = block_value( $name );
	foreach ( $value as $block_value_property ) :
		printf(
			'Here is the result of passing %s to block_value() with the property %s: %s',
			$name,
			$block_value_property,
			$block_value->$block_value_property
		);
	endforeach;
endforeach;
