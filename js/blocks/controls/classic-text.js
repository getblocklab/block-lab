/**
 * WordPress dependencies
 */
const { BaseControl } = wp.components;

/**
 * Internal dependencies
 */
import { TinyMCE } from '../components';

const BlockLabClassicTextControl = ( props ) => {
	const { field, getValue, instanceId, onChange } = props;

	return (
		<BaseControl
			label={ field.label }
			id={ `bl-classic-text-${ instanceId }` }
			className="block-lab-classic-text-control"
			help={ field.help }
		>
			<TinyMCE
				content={ getValue( props ) }
				onChange={ onChange }
				editorId={ `classic-text-${ field.name }` }
			/>
		</BaseControl>
	);
};

export default BlockLabClassicTextControl;
