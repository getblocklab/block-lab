/**
 * WordPress dependencies
 */
const { BaseControl } = wp.components;

/**
 * Internal dependencies
 */
import { TinyMCE } from '../components';

const BlockLabClassicTextControl = ( props ) => {
	const { field, getValue, instanceId, onChange, rowIndex } = props;
	const editorId = 'number' === typeof rowIndex ? `bl-classic-text-${ field.name }-${ rowIndex }` : `bl-classic-text-${ field.name }`;

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
				editorId={ editorId }
			/>
		</BaseControl>
	);
};

export default BlockLabClassicTextControl;
