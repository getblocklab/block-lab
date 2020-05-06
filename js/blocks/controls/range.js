/**
 * WordPress dependencies
 */
import { RangeControl } from '@wordpress/components';

const BlockLabRangeControl = ( props ) => {
	const { field, getValue, onChange } = props;
	const value = getValue( props );

	return (
		<RangeControl
			label={ field.label }
			help={ field.help }
			value={ 'undefined' !== typeof value ? value : field.default }
			onChange={ onChange }
			min={ field.min }
			max={ field.max }
			step={ field.step }
		/>
	);
};

export default BlockLabRangeControl;
