const { BaseControl, FormFileUpload, Button, Spinner } = wp.components;
const { withSelect } = wp.data;
const { MediaUploadCheck, MediaUpload, mediaUpload } = wp.editor;
const { Fragment } = wp.element;
const { __ } = wp.i18n;

const BlockLabImageControl = ( props, field, block ) => {
	const ImageControl = withSelect( ( select, ownProps ) => {
		const { attributes } = ownProps;
		const media = select( 'core' ).getMedia( attributes[ field.name ] );
		let imageAlt;

		// Taken from the Gutenberg Image block's edit.js file.
		if ( media && media.alt ) {
			imageAlt = media.alt;
		} else if ( media && media.source_url ) {
			imageAlt = sprintf( __( 'This image has no alt attribute, but its src is %s', 'block-lab' ), media.source_url );
		} else {
			imageAlt = __( 'This image has no alt attribute', 'block-lab' );
		}

		return {
			imageSrc: media ? media.source_url : '',
			imageAlt,
		 };

	} )( ( ownProps) => {
		const { imageAlt, imageSrc, setAttributes } = ownProps;
		const attr = { ...ownProps.attributes };
		const isUploading = 'undefined' !== typeof attr[ field.name ] && 'string' === typeof attr[ field.name ] && 'Uploading' === attr[ field.name ].substr( 0, 9 );

		const uploadStart = (filename) => {
			attr[ field.name ] = __( 'Uploading', 'block-lab' ) + ' ' + filename;
			setAttributes( attr )
		};

		const uploadComplete = ( image ) => {
			attr[field.name] = parseInt( image.id );
			setAttributes( attr )
		};

		const onSelect = ( image ) => {
			if ( ! image.hasOwnProperty( 'url' ) || ! image.hasOwnProperty( 'id' ) ) {
				return
			}
			if ( 'blob' === image.url.substr( 0, 4 ) ) {
				// Still uploading…
				return
			}

			uploadComplete( image );
		};

		return (
			<BaseControl className="block-lab-media-controls" label={ field.label } help={ field.help }>
				<Fragment>
					<img class="bl-image__img" src={ imageSrc } alt={ imageAlt } />
				</Fragment>
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
						{ __( 'Upload', 'block-lab' ) }
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
									{ __( 'Media Library', 'block-lab' ) }
								</Button>
							</div>
						) }
					/>
				</MediaUploadCheck>
			</BaseControl>
		);
	} );

	return (
		<ImageControl { ...props } />
	)
};

export default BlockLabImageControl;