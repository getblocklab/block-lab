const { BaseControl } = wp.components;
const { MediaPlaceholder } = wp.editor;

const BlockLabMediaControl = ( props, field, block ) => {
	const { setAttributes } = props;
	const attr = { ...props.attributes };
	return (
		<BaseControl label={field.label} className="block-lab-media-control" help={field.help}>
			<MediaPlaceholder
				// These labels could be replaced with Control Settings
				labels={{
					title:'',
					instructions:''
				}}
				defaultValue={field.default}
				value={attr[ field.name ]}
				isURLInputVisible={true}
			/>
		</BaseControl>
		// <FormFileUpload
		// 	label={field.label}
		// 	help={field.help}
		// 	defaultValue={field.default}
		// 	value={attr[ field.name ]}
		// 	accept="image/*"
		// 	onChange={mediaControl => {
		// 		console.log(mediaControl)
		// 		attr[ field.name ] = mediaControl
		// 		setAttributes( attr )
		// 	}}
		// >
		// 	Upload
		// </FormFileUpload>
	)
}

export default BlockLabMediaControl