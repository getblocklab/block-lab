<?php
/**
 * Template for testing-example block to test the output of the Text control.
 *
 * @package Block_Lab
 */

$field_name = 'testing-text';
?>
<p>
	<?php echo esc_html( sprintf( 'Here is the result of calling block_value with the field name: %s', block_value( $field_name ) ) ); ?>
	<?php echo 'Here is the result of calling block_field with the field name: '; ?><?php block_field( $field_name ); ?>
</p>
