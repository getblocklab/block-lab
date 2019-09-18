/**
 * WordPress dependencies
 */
const { BaseControl } = wp.components;
const { RichText, RichTextToolbarButton } = wp.blockEditor;
const { Fragment } = wp.element;
const { __ } = wp.i18n;
const {
	__unstableChangeListType: changeListType,
	__unstableIsListRootSelected: isListRootSelected,
	__unstableIsActiveListType: isActiveListType,
	applyFormat,
	registerFormatType,
	removeFormat,
} = wp.richText;

const listControlName = 'block-lab/rich-text-list-controls';
const tagName = 'ul';

/**
 * Gets the styling for a given alignment.
 *
 * @param {string} alignment The alignment, like 'left' or 'right'.
 * @return {string} The alignment style, like text-align: left;.
 */
const getListStyle = ( alignment ) => {
	return `text-align: ${ alignment }`;
};

/**
 * The list buttons to set the <ul> and <li> format.
 *
 * @param {Object} props The component properties.
 * @param {Object} props.value The value.
 * @param {Function} props.onChange The handler for changes of the toolbar.
 */
const ListToolbar = ( { value, onChange } ) => {
	const setAttributes = () => {};

	return (
		<Fragment>
			<RichTextToolbarButton
				icon="editor-ul"
				title={ __( 'Convert to unordered list', 'block-lab' ) }
				isActive={ !! value && !! value.name && isActiveListType( value, 'ul', tagName ) }
				onClick={ () => {
					onChange( changeListType( value, { type: 'ul' } ) );

					if ( isListRootSelected( value ) ) {
						setAttributes( { ordered: false } );
					}
				} }
			/>
			<RichTextToolbarButton
				icon="editor-ol"
				title={ __( 'Convert to ordered list', 'block-lab' ) }
				isActive={ !! value && !! value.name && isActiveListType( value, 'ol', tagName ) }
				onClick={ () => {
					onChange( changeListType( value, { type: 'ol' } ) );

					if ( isListRootSelected( value ) ) {
						setAttributes( { ordered: true } );
					}
				} }
			/>
		</Fragment>
	);
};

registerFormatType(
	listControlName,
	{
		title: __( 'List Type Controls', 'block-lab' ),
		tagName,
		className: 'bl-aligned',
		attributes: {
			align: 'style',
		},
		edit: ( { onChange, value } ) => {
			return (
				<ListToolbar
					value={ value }
					onChange={ ( newListType ) => {
						if ( newListType ) {
							onChange(
								applyFormat(
									value,
									{
										type: listControlName,
										attributes: {
											align: getListStyle( newListType ),
										},
									},
								)
							);
						} else {
							onChange(
								removeFormat(
									value,
									{
										type: listControlName,
										attributes: {
											align: getListStyle( value ),
										},
									},
								)
							);
						}
					} }
				/>
			);
		},
	}
);

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
