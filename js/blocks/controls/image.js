const { BaseControl, TextControl, FormFileUpload, Button, Spinner } = wp.components;
const { MediaUploadCheck, MediaUpload, mediaUpload } = wp.editor;
const { __ } = wp.i18n;

const BlockLabImageControl = ( props, field, block ) => {
	const { setAttributes } = props;
	const attr = { isUploading: false, ...props.attributes };

	const uploadStart = () => {
		attr.isUploading = true
		setAttributes( attr )
	};

	const uploadComplete = () => {
		attr.isUploading = false
		setAttributes( attr )
	};

	const onSelect = (media) => {
		if ( ! media.hasOwnProperty( 'url' ) ) {
			return
		}
		if ( 'blob' === media.url.substr( 0, 4 ) ) {
			// Still uploading…
			return
		}

		uploadComplete();

		attr[ field.name ] = media.url
		setAttributes( attr )
	};

	return (
		<BaseControl label={field.label} className="block-lab-media-control" help={field.help}>
			<TextControl
				defaultValue={field.default}
				value={attr[ field.name ]}
				disabled={!!attr.isUploading}
				onClick={(event) => {
					event.target.setSelectionRange(0, event.target.value.length)
				}}
				onChange={image => {
					attr[ field.name ] = image
					setAttributes( attr )
				}}
			/>
			<MediaUploadCheck>
				{attr.isUploading && (
					<Spinner />
				)}
				<FormFileUpload
					isLarge
					disabled={!!attr.isUploading}
					onChange={(event) => {
						let files = event.target.files;
						uploadStart();
						mediaUpload( {
							allowedTypes: [ 'image' ],
							filesList: files,
							onFileChange: ( media ) => {
								onSelect(media[0])
							}
						} );
					}}
					accept='image/*'
					multiple={ false }
				>
					{ __( 'Upload' ) }
				</FormFileUpload>
				<MediaUpload
					gallery={ false }
					multiple={ false }
					onSelect={ onSelect }
					allowedTypes={ [ 'image' ] }
					value={ attr[ field.name ] }
					render={ ( { open } ) => (
						<Button
							isLarge
							disabled={!!attr.isUploading}
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

export default BlockLabImageControl