/**
 * WordPress dependencies
 */
import { ToggleControl } from '@wordpress/components';

const BlockLabToggleControl = ( props ) => {
	const { field, onChange, getValue } = props;
	const attr = { ...props.attributes };
	if ( 'undefined' === typeof attr[ field.name ] ) {
		attr[ field.name ] = field.default;
	}

	return (
		<ToggleControl
			label={ field.label }
			help={ field.help }
			checked={ getValue( props ) }
			onChange={ onChange }
		/>
	);
};

export default BlockLabToggleControl;
