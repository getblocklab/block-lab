<?php
/**
 * A mock template for a block, testing all fields except the repeater.
 *
 * @package Block_Lab
 */

// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaping could interfere with testing block_value().

$non_object_fields = [
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
	'text',
	'rich-text',
	'classic-text',
];

foreach ( $non_object_fields as $field ) :
	?>
	<p class="<?php block_field( 'className' ); ?>">
		<?php
		printf(
			'Here is the result of block_field() for %s: ',
			$field
		);
		block_field( $field );
		?>
	</p>

	<p>
		<?php
		printf(
			'Here is the result of calling block_value() for %s: %s',
			$field,
			block_value( $field )
		);
		?>
	</p>
	<?php
endforeach;

$non_string_fields = [
	'post'     => [ 'ID', 'post_name' ],
	'taxonomy' => [ 'term_id', 'name' ],
	'user'     => [ 'ID', 'first_name' ],
];

foreach ( $non_string_fields as $name => $value ) :
	printf(
		'Here is the result of block_field() for %s: ',
		$name
	);
	block_field( $name );

	$block_value = block_value( $name );
	foreach ( $value as $block_value_property ) :
		if ( isset( $block_value->$block_value_property ) ) :
			printf(
				'Here is the result of passing %s to block_value() with the property %s: %s',
				$name,
				$block_value_property,
				$block_value->$block_value_property
			);
		endif;
	endforeach;
endforeach;

echo 'Here is the result of block_field() for multiselect: ';
block_field( 'multiselect' );
