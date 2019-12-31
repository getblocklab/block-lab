/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Button, TextControl } = wp.components;
const { Component } = wp.element;

/**
 * Internal dependencies
 */
import { isNewFieldName, saveFieldValue } from '../helpers';

/**
 * A field's editing section.
 */
class FieldEdit extends Component {
	/**
	 * Constructs the class.
	 *
	 * @param {*} args The arguments.
	 */
	constructor( ...args ) {
		super( ...args );
		const { field } = this.props;
		this.state = { wasLabelEdited: ! isNewFieldName( field.name ) };
	}

	/**
	 * Whether or not the field label should be 'slugified' and set as the name.
	 */
	doSlugify() {
		const { field } = this.props;
		return ! this.state.wasLabelEdited || ! field.name || isNewFieldName( field.name );
	}

	/**
	 * Renders the field's editing section.
	 *
	 * @return {Function} The rendered component.
	 */
	render() {
		const { field, onClose, uiud } = this.props;
		const isFieldDisabled = false;

		return (
			<div className="block-fields-edit">
				<table className="widefat">
					<tr className="block-fields-edit-label">
						<td className="spacer"></td>
						<th scope="row">
							<label htmlFor={ `block-fields-edit-label-input_${ uiud } ` }>
								{ __( 'Field Label', 'block-lab' ) }
							</label>
							<p className="description">
								{ __( 'A label describing your block\'s custom field.', 'block-lab' ) }
							</p>
						</th>
						<td>
							<TextControl
								type="text"
								id={ `block-fields-edit-label-input_${ uiud }` }
								className="regular-text"
								value={ field.label }
								onChange={ ( newValue ) => {
									saveFieldValue( field.name, 'label', newValue, this.doSlugify() );
								} }
								onBlur={ () => {
									this.setState( { wasLabelEdited: true } );
								} }
								data-sync={ `block-fields-label_${ uiud }` }
								readOnly={ isFieldDisabled }
							/>
						</td>
					</tr>
					<tr className="block-fields-edit-name">
						<td className="spacer"></td>
						<th scope="row">
							<label id={ `block-fields-edit-name-${ uiud }` } htmlFor={ `block-fields-edit-name-input_${ uiud }` }>
								{ __( 'Field Name', 'block-lab' ) }
							</label>
							<p className="description">
								{ __( 'Single word, no spaces.', 'block-lab' ) }
							</p>
						</th>
						<td>
							<TextControl
								id={ `block-fields-edit-name-input_${ uiud }` }
								className="regular-text"
								value={ field.name }
								onChange={ ( newValue ) => {
									saveFieldValue( field.name, 'name', newValue );
								} }
								data-sync="block-fields-name-code"
								readOnly={ isFieldDisabled }
							/>
						</td>
					</tr>
					<tr className="block-fields-edit-control">
						<td className="spacer"></td>
						<th scope="row">
							<label htmlFor={ `block-fields-edit-control-input_${ uiud }` }>
								{ __( 'Field Type', 'block-lab' ) }
							</label>
						</th>
						<td>
							<select
								id={ `block-fields-edit-control-input_${ uiud }` }
								data-sync={ `block-fields-control_${ uiud }` }
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
							<Button
								className="button"
								title={ __( 'Close Field', 'block-lab' ) }
								onClick={ onClose }
							>
								{ __( 'Close Field', 'block-lab' ) }
							</Button>
						</td>
					</tr>
				</table>
			</div>
		);
	}
}

export default FieldEdit;
