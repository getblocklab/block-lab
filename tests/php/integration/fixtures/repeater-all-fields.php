<?php
/**
 * A mock template for a block, testing a repeater with all field types as sub_fields.
 *
 * @package Block_Lab
 */

// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaping could interfere with testing block_sub_value().

$repeater_name     = 'repeater';
$non_object_fields = [
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
];

?>
<div class="<?php block_field( 'className' ); ?>">
<?php
if ( block_rows( $repeater_name ) ) :
	$row_number = 0;
	printf( 'block_row_count() returns %d', block_row_count( $repeater_name ) );
	while ( block_rows( $repeater_name ) ) :
		block_row( $repeater_name );
			printf(
				'In row %d, the result of block_row_index() is %d',
				$row_number,
				block_row_index()
			);

		foreach ( $non_object_fields as $field ) :
			?>
			<p class="<?php block_field( 'className' ); ?>">
				<?php
				printf(
					'In row %d, here is the result of block_sub_field() for %s: ',
					$row_number,
					$field
				);
				block_sub_field( $field );
				?>
			</p>

			<p>
				<?php
				printf(
					'And in row %d, here is the result of calling block_sub_value() for %s: %s',
					$row_number,
					$field,
					block_sub_value( $field )
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
				'In row %d, here is the result of block_sub_field() for %s: ',
				$row_number,
				$name
			);
			block_sub_field( $name );

			$block_sub_value = block_sub_value( $name );
			foreach ( $value as $block_value_property ) :
				printf(
					'In row %d, here is the result of passing %s to block_sub_value() with the property %s: %s',
					$row_number,
					$name,
					$block_value_property,
					$block_sub_value->$block_value_property
				);
			endforeach;
		endforeach;

		$row_number++;
	endwhile;
	reset_block_rows( $repeater_name );
endif;
