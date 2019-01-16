const { BaseControl, TextControl, FormFileUpload, Button } = wp.components;
const { MediaUploadCheck, MediaUpload, mediaUpload } = wp.editor;
const { __ } = wp.i18n;

const BlockLabVideoControl = ( props, field, block ) => {
	const { setAttributes } = props;
	const attr = { ...props.attributes };

	const onSelect = (media) => {
		if ( ! media.hasOwnProperty( 'url' ) ) {
			return
		}
		if ( 'blob' === media.url.substr( 0, 4 ) ) {
			// Still uploading…
			return
		}
		attr[ field.name ] = media.url
		setAttributes( attr )
	};

	return (
		<BaseControl label={field.label} className="block-lab-media-control" help={field.help}>
			<TextControl
				defaultValue={field.default}
				value={attr[ field.name ]}
				onClick={(event) => {
					event.target.setSelectionRange(0, event.target.value.length)
				}}
				onChange={colorControl => {
					attr[ field.name ] = colorControl
					setAttributes( attr )
				}}
			/>
			<MediaUploadCheck>
				<FormFileUpload
					isLarge
					className=''
					onChange={(event) => {
						let files = event.target.files;
						mediaUpload( {
							allowedTypes: [ 'video' ],
							filesList: files,
							onFileChange: ( media ) => onSelect( media[0] ),
						} );
					}}
					accept='video/mp4,video/x-m4v,video/*'
					multiple={ false }
				>
					{ __( 'Upload' ) }
				</FormFileUpload>
				<MediaUpload
					gallery={ false }
					multiple={ false }
					onSelect={ onSelect }
					allowedTypes={ [ 'video' ] }
					value={ attr[ field.name ] }
					render={ ( { open } ) => (
						<Button
							isLarge
							className="editor-media-placeholder__button"
							onClick={ open }
						>
							{ __( 'Media Library' ) }
						</Button>
					) }
				/>
			</MediaUploadCheck>
		</BaseControl>
	)
}

export default BlockLabVideoControl