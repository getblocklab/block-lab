/**
 * WordPress dependencies
 */
import { RangeControl } from '@wordpress/components';

const BlockLabRangeControl = ( props ) => {
	const { field, getValue, onChange } = props;
	const value = getValue( props );

	return (
		<RangeControl
			beforeIcon={ field.beforeIcon }
			afterIcon={ field.afterIcon }
			label={ field.label }
			help={ field.help }
			value={ ( value || undefined === value ) ? value : field.default }
			onChange={ onChange }
			min={ field.min }
			max={ field.max }
			step={ field.step }
			allowReset={ field.allowReset }
		/>
	);
};

export default BlockLabRangeControl;
