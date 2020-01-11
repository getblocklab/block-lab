/**
 * WordPress dependencies
 */
import { BaseControl } from '@wordpress/components';
import { RichText } from '@wordpress/block-editor';

const BlockLabRichTextControl = ( props ) => {
	const { field, getValue, instanceId, onChange } = props;

	return (
		<BaseControl
			label={ field.label }
			id={ `bl-rich-text-${ instanceId }` }
			className="block-lab-rich-text-control"
			help={ field.help }
		>
			{
			/*
			* @todo: Resolve known issue with toolbar not disappearing on blur
			* @see: https://github.com/WordPress/gutenberg/issues/7463
			*/
			}
			<RichText
				key={ `block-lab-${ field.name }` }
				placeholder={ field.placeholder || '' }
				keepPlaceholderOnFocus={ true }
				defaultValue={ field.default }
				value={ getValue( props ) }
				className="input-control"
				multiline={ true }
				inlineToolbar={ true }
				onChange={ onChange }
			/>
		</BaseControl>
	);
};

export default BlockLabRichTextControl;
