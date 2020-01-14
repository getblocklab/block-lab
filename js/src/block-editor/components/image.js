/**
 * WordPress dependencies
 */
import { BaseControl, Button, Placeholder, DropZone, FormFileUpload, Spinner } from '@wordpress/components';
import { withState } from '@wordpress/compose';
import { withSelect } from '@wordpress/data';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { mediaUpload } from '@wordpress/editor';
import { __, sprintf } from '@wordpress/i18n';

const ALLOWED_TYPES = [ 'image' ];
const DEFAULT_IMG_ID = 0;

const Image = withSelect( ( select, ownProps ) => {
	const { getValue } = ownProps;
	const fieldValue = getValue( ownProps );
	let media, imageAlt,
		imageSrc = '';

	if ( parseInt( fieldValue ) ) {
		media = select( 'core' ).getMedia( fieldValue );
		imageSrc = ( media && media.source_url ) ? media.source_url : '';
	} else if ( 'string' === typeof fieldValue ) {
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
} )( withState()( ( ownProps ) => {
	const { field, getValue, imageAlt, imageSrc, instanceId, isUploading, onChange, setState } = ownProps;
	const uploadStart = () => {
		setState( { isUploading: true } );
	};

	const uploadComplete = ( image ) => {
		if ( image.hasOwnProperty( 'id' ) ) {
			onChange( parseInt( image.id ) );
		}
		setState( { isUploading: false } );
	};

	const onSelect = ( image ) => {
		if ( ! image.hasOwnProperty( 'url' ) || ! image.hasOwnProperty( 'id' ) ) {
			return;
		}
		if ( 'blob' === image.url.substr( 0, 4 ) ) {
			// Still uploading…
			return;
		}

		uploadComplete( image );
	};

	const removeImage = () => {
		// The attribute should be an int, so set it to 0 on removing an image.
		onChange( DEFAULT_IMG_ID );
	};

	const uploadFiles = ( files ) => {
		mediaUpload( {
			allowedTypes: ALLOWED_TYPES,
			filesList: files,
			onFileChange: ( image ) => {
				onSelect( image[ 0 ] );
			},
		} );
	};

	return (
		<BaseControl className="block-lab-media-controls" label={ field.label } id={ `bl-image-${ instanceId }` }>
			{ !! field.help && <p className="components-base-control__help">{ field.help }</p> }
			{ ! isUploading && imageSrc && (
				<img className="bl-image__img" src={ imageSrc } alt={ imageAlt } />
			) }
			{ ! imageSrc && (
				<Placeholder className="bl-image__placeholder" icon="format-image" label={ __( 'Image', 'block-lab' ) } instructions={ __( 'Drag an image, upload a new one or select a file from your library.', 'block-lab' ) }>
					<MediaUploadCheck>
						<DropZone
							onFilesDrop={ ( files ) => {
								if ( files.length ) {
									uploadStart();
									uploadFiles( files );
								}
							} }
						></DropZone>
						{ isUploading && (
							<Spinner />
						) }
						{ ! isUploading && (
							<>
								<FormFileUpload
									isLarge
									disabled={ !! isUploading }
									onChange={ ( event ) => {
										const files = event.target.files;
										uploadStart( files[ 0 ].name );
										uploadFiles( files );
									} }
									accept="image/*"
									multiple={ false }
								>
									{ __( 'Upload', 'block-lab' ) }
								</FormFileUpload>
								<MediaUpload
									gallery={ false }
									multiple={ false }
									onSelect={ onSelect }
									allowedTypes={ ALLOWED_TYPES }
									value={ getValue( ownProps ) }
									render={ ( { open } ) => (
										<div className="components-media-library-button">
											<Button
												isLarge
												disabled={ !! isUploading }
												className="editor-media-placeholder__button"
												onClick={ open }
											>
												{ __( 'Media Library', 'block-lab' ) }
											</Button>
										</div>
									) }
								/>
							</>
						) }
					</MediaUploadCheck>
				</Placeholder>
			) }
			{ imageSrc && (
				<Button
					isLarge
					disabled={ !! isUploading }
					className="bl-image__remove"
					onClick={ removeImage }
				>
					{ __( 'Remove', 'block-lab' ) }
				</Button>
			) }
		</BaseControl>
	);
} ) );

export default Image;
