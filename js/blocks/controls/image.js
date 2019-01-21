const { BaseControl, TextControl, FormFileUpload, Button, Spinner } = wp.components;
const { MediaUploadCheck, MediaUpload, mediaUpload } = wp.editor;
const { __ } = wp.i18n;

const BlockLabImageControl = ( props, field, block ) => {
	const { setAttributes } = props;
	const attr = { ...props.attributes };
	const isUploading = 'Uploading' === attr[ field.name ].substr( 0, 9 );

	const uploadStart = (filename) => {
		attr[ field.name ] = __( 'Uploading' ) + ' ' + filename;
		setAttributes( attr )
	};

	const uploadComplete = (url) => {
		attr[ field.name ] = url;
		setAttributes( attr )
	};

	const onSelect = (image) => {
		if ( ! image.hasOwnProperty( 'url' ) ) {
			return
		}
		if ( 'blob' === image.url.substr( 0, 4 ) ) {
			// Still uploading…
			return
		}

		uploadComplete( image.url );
	};

	return (
		<BaseControl label={field.label} className="block-lab-media-control" help={field.help}>
			<TextControl
				defaultValue={field.default}
				value={attr[ field.name ]}
				disabled={!!isUploading}
				onClick={(event) => {
					event.target.setSelectionRange(0, event.target.value.length)
				}}
				onChange={image => {
					attr[ field.name ] = image
					setAttributes( attr )
				}}
			/>
			<MediaUploadCheck>
				{isUploading && (
					<Spinner />
				)}
				<FormFileUpload
					isLarge
					disabled={!!isUploading}
					onChange={(event) => {
						let files = event.target.files;
						uploadStart(files[0].name);
						mediaUpload( {
							allowedTypes: [ 'image' ],
							filesList: files,
							onFileChange: ( image ) => {
								onSelect(image[0])
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
						<div className='components-media-library-button'>
							<Button
								isLarge
								disabled={!!isUploading}
								className="editor-media-placeholder__button"
								onClick={ open }
							>
								{ __( 'Media Library' ) }
							</Button>
						</div>
					) }
				/>
			</MediaUploadCheck>
		</BaseControl>
	)
}

export default BlockLabImageControl