const { BaseControl, Button, DropZone, FormFileUpload, Spinner } = wp.components;
const { withState } = wp.compose;
const { withSelect } = wp.data;
const { mediaUpload, MediaUpload, MediaUploadCheck } = wp.editor;
const { Fragment } = wp.element;
const { __ } = wp.i18n;

const ALLOWED_TYPES = [ 'image' ];
const DEFAULT_IMG_ID = 0;

const BlockLabImageControl = ( props, field, block ) => {
	const ImageControl = withSelect( ( select, ownProps ) => {
		const { attributes } = ownProps;
		const fieldValue = attributes[ field.name ];
		let media, imageAlt,
			imageSrc = '';

		if ( parseInt( fieldValue ) ) {
			media = select( 'core' ).getMedia( fieldValue );
			imageSrc = ( media && media.source_url ) ? media.source_url : '';
		} else if ( 'string' === typeof fieldValue )  {
			// Backwards-compatibility: this used to save the URL as the fieldValue, not the ID as it does now.
			imageSrc = fieldValue;
		}

		// This alt logic is taken from the Gutenberg Image block's edit.js file.
		if ( media && media.alt ) {
			imageAlt = media.alt;
		} else if ( media && media.source_url ) {
			imageAlt = sprintf( __( 'This image has no alt attribute, but its src is %s', 'block-lab' ), media.source_url );
		} else {
			imageAlt = __( 'This image has no alt attribute', 'block-lab' );
		}

		return {
			imageAlt,
			imageSrc,
		 };

	} )( withState( {} )( ownProps => {
		const { imageAlt, imageSrc, isUploading, setAttributes, setState } = ownProps;
		const attr = { ...ownProps.attributes };

		const uploadStart = () => {
			setState( { isUploading: true } )
		};

		const uploadComplete = ( image ) => {
			if ( image.hasOwnProperty( 'id' ) ) {
				attr[ field.name ] = parseInt( image.id );
				setAttributes( attr )
			}
			setState( { isUploading: false } )
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

		const removeImage = () => {
			// The attribute should be an int, so set it to 0 on removing an image.
			attr[ field.name ] = DEFAULT_IMG_ID;
			setAttributes( attr );
		}

		const uploadFiles = ( files ) => {
				mediaUpload( {
				allowedTypes: ALLOWED_TYPES,
				filesList: files,
				onFileChange: ( image ) => {
					onSelect( image[0] )
				}
			} );
		}

		return (
			<BaseControl className="block-lab-media-controls" label={ field.label } help={ field.help }>
				<Fragment>
					{ ! isUploading && imageSrc && (
						<img className="bl-image__img" src={ imageSrc } alt={ imageAlt } />
					) }
					{ ! imageSrc && (
						<div className="bl-image__placeholder">
							<span class="dashicons dashicons-format-image" />
							<MediaUploadCheck>
								<DropZone
									onFilesDrop={ ( files ) => {
										if ( files.length ) {
											uploadStart();
											uploadFiles( files );
										}
									} }
								></DropZone>
							</MediaUploadCheck>
						</div>
					) }
				</Fragment>
				<MediaUploadCheck>
					{ isUploading && (
						<Spinner />
					) }
					<div className="bl-image__buttons">
						<FormFileUpload
							isLarge
							disabled={!!isUploading}
							onChange={(event) => {
								let files = event.target.files;
								uploadStart(files[0].name);
								uploadFiles( files );
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
							allowedTypes={ ALLOWED_TYPES }
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
						{ imageSrc && (
							<Button
								isLarge
								disabled={!!isUploading}
								className="bl-image__remove"
								onClick={removeImage}
							>
								{ __( 'Remove', 'block-lab' ) }
							</Button>
						) }
					</div>
				</MediaUploadCheck>
			</BaseControl>
		);
	} ) );

	return (
		<ImageControl { ...props } />
	)
};

export default BlockLabImageControl;