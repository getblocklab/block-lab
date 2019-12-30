/**
 * WordPress dependencies
 */
const { Component } = wp.element;

/**
 * Internal dependencies
 */
import { FieldEdit } from './';

/**
 * A field row.
 */
class Field extends Component {
	/**
	 * Renders the field row.
	 *
	 * @return {Function} The rendered component.
	 */
	render() {
		const { field, uiud } = this.props;

		return (
			<div className="field">
				<div className="field-container">
					<div className="field-icon-container">
						<svg className="field-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
							<path fill="none" d="M0 0h24v24H0V0z" />
							<path d="M21 3H3C2 3 1 4 1 5v14c0 1.1.9 2 2 2h18c1 0 2-1 2-2V5c0-1-1-2-2-2zm0 15.92c-.02.03-.06.06-.08.08H3V5.08L3.08 5h17.83c.03.02.06.06.08.08v13.84zm-10-3.41L8.5 12.5 5 17h14l-4.5-6z" />
						</svg>
						<svg className="field-grab-indicator" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
							<path fill="none" d="M0 0h24v24H0V0z" />
							<path d="M11 18c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2zm-2-8c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0-6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm6 4c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
						</svg>
					</div>
					<span className="field-title">
						{ field.name }
					</span>
					<div className="field-copy-pill">
						<span>{ field.label }</span>
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
							<path fill="none" d="M0 0h24v24H0V0z" />
							<path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm-1 4H8c-1.1 0-1.99.9-1.99 2L6 21c0 1.1.89 2 1.99 2H19c1.1 0 2-.9 2-2V11l-6-6zM8 21V7h6v5h5v9H8z" />
						</svg>
					</div>
				</div>
				<FieldEdit field={ field } uiud={ uiud } />
			</div>
		);
	}
}

export default Field;
