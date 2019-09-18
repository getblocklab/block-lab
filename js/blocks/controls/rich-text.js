/**
 * WordPress dependencies
 */
const { BaseControl } = wp.components;

/**
 * Internal dependencies
 */
import { TinyMCE } from '../components';

const BlockLabRichTextControl = ( props ) => {
	const { field, getValue, instanceId, onChange } = props;

	return (
		<BaseControl
			label={ field.label }
			id={ `bl-rich-text-${ instanceId }` }
			className="block-lab-rich-text-control"
			help={ field.help }
		>
			<TinyMCE
				content={ getValue( props ) }
				onChange={ onChange }
				clientId={ `rich-text-${ field.name }` }
			/>
		</BaseControl>
	);
};

export default BlockLabRichTextControl;
