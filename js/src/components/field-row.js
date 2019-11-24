/**
 * WordPress dependencies
 */
const { Button, TextControl } = wp.components;
const { Component } = wp.element;
const { __ } = wp.i18n;

/**
 * A field row.
 */
export default class FieldRow extends Component {
	/**
	 * Renders the field row.
	 *
	 * @return {Function} The rendered component.
	 */
	render() {
		const { field, uid } = this.props;
		const isFieldDisabled = false;

		return (
			<div className="block-fields-row" data-uid={ uid }>
				<div className="block-fields-row-columns">
					<div className="block-fields-sort">
						<span className="block-fields-sort-handle"></span>
					</div>
					<div className="block-fields-label">
						<Button className="row-title" id={ `block-fields-label_${ uid }` }>
							{ field.label }
						</Button>
						<div className="block-fields-actions">
							<Button className="block-fields-actions-edit">
								{ __( 'Edit', 'block-lab' ) }
							</Button>
							<Button className="block-fields-actions-duplicate">
								{ __( 'Duplicate', 'block-lab' ) }
							</Button>
							<Button className="block-fields-actions-delete">
								{ __( 'Delete', 'block-lab' ) }
							</Button>
						</div>
					</div>
					<div className="block-fields-name" id={ `block-fields-name_${ uid }` }>
						<code id={ `block-fields-name-code_${ uid }` }>{ field.name }</code>
					</div>
					<div className="block-fields-control" id={ `block-fields-control_${ uid }` }>
						{
							/* <?php
							@todo: reimpliment this in JS.
							if ( ! isFieldDisabled && isset( $this->controls[ $field->control ] ) ) :
								echo esc_html( $this->controls[ $field->control ]->label );
							else :
								<span className="dashicons dashicons-warning"></span>
								<span className="pro-required">

										sprintf(
											__( 'This <code>%1$s</code> field requires an active <a href="%2$s">pro license</a>.', 'block-lab' ),
											field.control,
											'https://example.com'
										)

								</span>
							endif;
							*/
						}
					</div>
				</div>
				<div className="block-fields-edit">
					<table className="widefat">
						<tr className="block-fields-edit-label">
							<td className="spacer"></td>
							<th scope="row">
								<label htmlFor={ `block-fields-edit-label-input_${ uid } ` }>
									{ __( 'Field Label', 'block-lab' ) }
								</label>
								<p className="description">
									{ __( 'A label describing your block\'s custom field.', 'block-lab' ) }
								</p>
							</th>
							<td>
								<TextControl
									type="text"
									id={ `block-fields-edit-label-input_${ uid }` }
									className="regular-text"
									value={ field.label }
									data-sync={ `block-fields-label_${ uid }` }
									readOnly={ isFieldDisabled }
								/>
							</td>
						</tr>
						<tr className="block-fields-edit-name">
							<td className="spacer"></td>
							<th scope="row">
								<label id={ `block-fields-edit-name-${ uid }` } htmlFor={ `block-fields-edit-name-input_${ uid }` }>
									{ __( 'Field Name', 'block-lab' ) }
								</label>
								<p className="description">
									{ __( 'Single word, no spaces.', 'block-lab' ) }
								</p>
							</th>
							<td>
								<TextControl
									id={ `block-fields-edit-name-input_${ uid }` }
									className="regular-text"
									value={ field.name }
									data-sync="block-fields-name-code"
									readOnly={ isFieldDisabled }
								/>
							</td>
						</tr>
						<tr className="block-fields-edit-control">
							<td className="spacer"></td>
							<th scope="row">
								<label htmlFor={ `block-fields-edit-control-input_${ uid }` }>
									{ __( 'Field Type', 'block-lab' ) }
								</label>
							</th>
							<td>
								<select
									id={ `block-fields-edit-control-input_${ uid }` }
									data-sync={ `block-fields-control_${ uid }` }
									disabled={ isFieldDisabled }
								>
									{
										/*
										@todo: reimpliment this in JS.
										$controls_for_select = $this->controls;

										// If this field is disabled, it was probably added when there was a valid pro license, so still display it.
										if ( isFieldDisabled && in_array( $field->control, $this->pro_controls, true ) ) {
											$controls_for_select[ $field->control ] = $this->get_control( $field->control );
										}

										// Don't allow nesting repeaters inside repeaters.
										if ( ! empty( $field->settings['parent'] ) ) {
											unset( $controls_for_select['repeater'] );
										}

										foreach ( $controls_for_select as $control_for_select ) :
											?>
											<option
												value="<?php echo esc_attr( $control_for_select->name ); ?>"
												<?php selected( $field->control, $control_for_select->name ); ?>>
												<?php echo esc_html( $control_for_select->label ); ?>
											</option>
										<?php endforeach; ?>
										*/
									}
								</select>
							</td>
						</tr>
						{ /* <?php $this->render_field_settings( $field, $uid ); ?> */ }
						<tr className="block-fields-edit-actions-close">
							<td className="spacer"></td>
							<th scope="row">
							</th>
							<td>
								<Button className="button" title={ __( 'Close Field', 'block-lab' ) } >
									{ __( 'Close Field', 'block-lab' ) }
								</Button>
							</td>
						</tr>
					</table>
				</div>

				{
					/*
					@todo: reimpliment this in JS.
					if ( 'repeater' === field.control ) {
						if ( ! field.settings.sub_fields ) {
							field.settings.sub_fields = [];
						}
						$this->render_fields_sub_rows( $field->settings['sub_fields'], $uid );
					}
					*/
				}
			</div>
		);
	}
}
