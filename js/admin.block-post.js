/**
 * Used for editing Blocks.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2018, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 *
 * Globals wp, blockLab
 */

(function( $ ) {

	$(function() {
		blockTitleInit();
		blockCategoryInit();
		blockIconInit();
		blockFieldInit();

		$( '#block-add-field' ).on( 'click', function() {
			let template = wp.template( 'field-repeater' ),
				data     = { uid: new Date().getTime() },
				field    = $( template( data ) );
			$( '.block-fields-rows' ).append( field );
			$( '.block-no-fields' ).hide();
			field.find( '.block-fields-actions-edit' ).trigger( 'click' );
			field.find( '.block-fields-edit-label input' ).select();
		});

		$( '#block_properties .block-properties-icons span' ).on( 'click', function() {
			$( '#block_properties .block-properties-icons span.selected' ).removeClass( 'selected' );
			$( this ).addClass( 'selected' );
			$( '#block-properties-icon' ).val( $( this ).data( 'value' ) );
		});

		$( '.block-fields-rows' )
			.on( 'click', '.block-fields-actions-delete', function() {
				$( this ).closest( '.block-fields-row' ).remove();
				if ( 0 === $( '.block-fields-rows' ).children( '.block-fields-row' ).length ) {
					$( '.block-no-fields' ).show();
				}
			})
			.on( 'click', '.block-fields-actions-edit, a.row-title', function() {
				let currentRow = $( this ).closest( '.block-fields-row' );

				// If we're expanding this row, first collapse all other rows.
				if ( ! currentRow.hasClass( 'block-fields-row-active' ) ) {
					$( '.block-fields-rows .block-fields-edit' ).slideUp();
					$( '.block-fields-rows .block-fields-row-active' ).removeClass( 'block-fields-row-active' );
				}

				currentRow.toggleClass( 'block-fields-row-active' );
				currentRow.find( '.block-fields-edit' ).slideToggle();

				// Fetch field settings if field is active and there are no settings.
				if ( $( this ).closest( '.block-fields-row' ).hasClass( 'block-fields-row-active' ) ) {
					let fieldRow = $( this ).closest( '.block-fields-row' );
					if ( 0 === fieldRow.find( '.block-fields-edit-settings' ).length ) {
						let fieldControl = fieldRow.find( '.block-fields-edit-control select' ).val();
						fetchFieldSettings( fieldRow, fieldControl );
					}
				}
			})
			.on( 'click', '.block-fields-edit-actions-close a.button', function() {
				$( this ).closest( '.block-fields-row' ).removeClass( 'block-fields-row-active' );
				$( this ).closest( '.block-fields-edit' ).slideUp();
			})
			.on( 'change keyup', '.block-fields-edit input', function() {
				let sync = $( this ).data( 'sync' );
				$( '#' + sync ).text( $( this ).val() );
			})
			.on( 'change keyup', '.block-fields-edit select', function() {
				let sync   = $( this ).data( 'sync' );
				let option = $( 'option:selected', $( this ) ).text();
				$( '#' + sync ).text( option );
			})
			.on( 'change', '.block-fields-edit-control select', function() {
				let fieldRow = $( this ).closest( '.block-fields-row' );
				fetchFieldSettings( fieldRow, $( this ).val() );
			})
			.on( 'change keyup', '.block-fields-edit-label input', function() {
				let slug = slugify( $( this ).val() );
				$( this )
					.closest( '.block-fields-edit' )
					.find( '.block-fields-edit-name input' )
					.val( slug )
					.trigger( 'change' );
			})
			.sortable({
				axis: 'y',
				cursor: 'grabbing',
				handle: '.block-fields-sort-handle',
				containment: 'parent',
				tolerance: 'pointer'
			});
	});

	let blockTitleInit = function() {
		let title = $( '#title' ),
		    slug  = $( '#block-properties-slug' );

		// If this is a new block, then enable auto-generated slugs.
		if( '' === title.val() && '' === slug.val() ) {
			let autoSlug = true;

			// If auto-generated slugs are enabled, set the slug based on the title.
			title.on( 'change keyup', function() {
				if ( autoSlug ) {
					slug.val( slugify( title.val() ) );
				}
			});

			// Turn auto-generated slugs off once a title has been set.
			title.on( 'blur', function() {
				if ( '' !== title.val() ) {
					autoSlug = false;
				}
			});
		}
	};

	let blockCategoryInit = function() {
		let categories       = wp.blocks.getCategories(),
			categoriesLength = categories.length,
			category         = $( '#block-properties-category-saved' );

		for (let i = 0; i < categoriesLength; i++) {
			if ( 'reusable' === categories[i].slug ) {
				continue;
			}
			$( '<option/>', {
				value: categories[i].slug,
				text: categories[i].title,
			} ).appendTo( '#block-properties-category' );
		}

		if ( category.val() !== '' ) {
			let option = $( '#block-properties-category option[value="' + category.val() + '"]' );
			if ( option.length > 0 ) {
				$( '#block-properties-category' ).prop( 'selectedIndex', option.index() );
			}
		}
	};

	let blockIconInit = function() {
		let iconsContainer = $( '.block-properties-icons' ),
			selectedIcon   = $( '.selected', iconsContainer );
		if ( 0 !== iconsContainer.length && 0 !== selectedIcon.length ) {
			iconsContainer.scrollTop( selectedIcon.position().top );
		}
	};

	let blockFieldInit = function() {
		if ( 0 === $( '.block-fields-rows' ).children( '.block-fields-row' ).length ) {
			$( '.block-no-fields' ).show();
		}
	};

	let fetchFieldSettings = function( fieldRow, fieldControl ) {
		if ( ! blockLab.hasOwnProperty( 'fieldSettingsNonce' ) ) {
			return;
		}

		let loadingRow = '' +
			'<tr class="block-fields-edit-loading">' +
			'   <td class="spacer"></td>' +
			'   <th></th>' +
			'   <td><span class="loading"></span></td>' +
			'</tr>';

		$( '.block-fields-edit-settings', fieldRow ).remove();
		$( '.block-fields-edit-control', fieldRow ).after( $( loadingRow ) );

		wp.ajax.send( 'fetch_field_settings', {
			success: function( data ) {
				$( '.block-fields-edit-loading', fieldRow ).remove();

				if ( ! data.hasOwnProperty( 'html' ) ) {
					return;
				}
				let settingsRows = $( data.html );
				$( '.block-fields-edit-location', fieldRow ).after( settingsRows );
			},
			error: function() {
				$( '.block-fields-edit-loading', fieldRow ).remove();
			},
			data: {
				control: fieldControl,
				uid:     fieldRow.data( 'uid' ),
				nonce:   blockLab.fieldSettingsNonce
			}
		});
	};

	let slugify = function( text ) {
		return text
			.toLowerCase()
			.replace( /[^\w ]+/g,'' )
			.replace( / +/g,'-' )
			.replace( /_+/g,'-' );
	};

})( jQuery );
