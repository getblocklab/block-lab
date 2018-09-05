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
		blockCategoryInit();
		blockIconInit();
		blockFieldInit();

		$( '#title' ).on( 'change keyup', function() {
			let slug = slugify( $( this ).val() );
			$( '#block-lab-properties-slug' ).val( slug );
		});

		$( '#block-lab-add-field' ).on( 'click', function() {
			let template = wp.template( 'field-repeater' ),
				data     = { uid: new Date().getTime() },
				field    = $( template( data ) );
			$( '.block-lab-fields-rows' ).append( field );
			$( '.block-lab-no-fields' ).hide();
			field.find( '.block-lab-fields-actions-edit' ).trigger( 'click' );
		});

		$( '#block_lab_block_properties .block-lab-properties-icons span' ).on( 'click', function() {
			$( '#block_lab_block_properties .block-lab-properties-icons span.selected' ).removeClass( 'selected' );
			$( this ).addClass( 'selected' );
			$( '#block-lab-properties-icon' ).val( $( this ).data( 'value' ) );
		});

		$( '.block-lab-fields-rows' )
			.on( 'click', '.block-lab-fields-actions-delete', function() {
				$( this ).closest( '.block-lab-fields-row' ).remove();
				if ( 0 === $( '.block-lab-fields-rows' ).children( '.block-lab-fields-row' ).length ) {
					$( '.block-lab-no-fields' ).show();
				}
			})
			.on( 'click', '.block-lab-fields-actions-edit, a.row-title', function() {
				$( this ).closest( '.block-lab-fields-row' ).toggleClass( 'block-lab-fields-row-active' );
				$( this ).closest( '.block-lab-fields-row' ).find( '.block-lab-fields-edit' ).slideToggle();

				// Fetch field settings if field is active and there are no settings.
				if ( $( this ).closest( '.block-lab-fields-row' ).hasClass( 'block-lab-fields-row-active' ) ) {
					let fieldRow = $( this ).closest( '.block-lab-fields-row' );
					if ( 0 === fieldRow.find( '.block-lab-fields-edit-settings' ).length ) {
						let fieldControl = fieldRow.find( '.block-lab-fields-edit-control select' ).val();
						fetchFieldSettings( fieldRow, fieldControl );
					}
				}
			})
			.on( 'click', '.block-lab-fields-edit-actions-close a.button', function() {
				$( this ).closest( '.block-lab-fields-row' ).removeClass( 'block-lab-fields-row-active' );
				$( this ).closest( '.block-lab-fields-edit' ).slideUp();
			})
			.on( 'change keyup', '.block-lab-fields-edit input, .block-lab-fields-edit select', function() {
				let sync = $( this ).data( 'sync' );
				$( '#' + sync ).text( $( this ).val() );
			})
			.on( 'change', '.block-lab-fields-edit-control select', function() {
				let fieldRow = $( this ).closest( '.block-lab-fields-row' );
				fetchFieldSettings( fieldRow, $( this ).val() );
			})
			.on( 'change keyup', '.block-lab-fields-edit-label input', function() {
				let slug = slugify( $( this ).val() );
				$( this )
					.closest( '.block-lab-fields-edit' )
					.find( '.block-lab-fields-edit-name input' )
					.val( slug )
					.trigger( 'change' );
			})
			.sortable({
				axis: 'y',
				cursor: 'grabbing',
				handle: '.block-lab-fields-sort-handle',
				containment: 'parent',
				tolerance: 'pointer'
			});
	});

	let blockCategoryInit = function() {
		let categories       = wp.blocks.getCategories(),
			categoriesLength = categories.length,
			category         = $( '#block-lab-properties-category-saved' );

		for (let i = 0; i < categoriesLength; i++) {
			if ( 'reusable' === categories[i].slug ) {
				continue;
			}
			$( '<option/>', {
				value: categories[i].slug,
				text: categories[i].title,
			} ).appendTo( '#block-lab-properties-category' );
		}

		if ( category.val() !== '' ) {
			let option = $( '#block-lab-properties-category option[value="' + category.val() + '"]' );
			if ( option.length > 0 ) {
				$( '#block-lab-properties-category' ).prop( 'selectedIndex', option.index() );
			}
		}
	};

	let blockIconInit = function() {
		let iconsContainer = $( '.block-lab-properties-icons' ),
			selectedIcon   = $( '.selected', iconsContainer );
		if ( 0 !== iconsContainer.length && 0 !== selectedIcon.length ) {
			iconsContainer.scrollTop( selectedIcon.position().top );
		}
	}

	let blockFieldInit = function() {
		if ( 0 === $( '.block-lab-fields-rows' ).children( '.block-lab-fields-row' ).length ) {
			$( '.block-lab-no-fields' ).show();
		}
	};

	let fetchFieldSettings = function( fieldRow, fieldControl ) {
		if ( ! blockLab.hasOwnProperty( 'fieldSettingsNonce' ) ) {
			return;
		}

		let loadingRow = '' +
			'<tr class="block-lab-fields-edit-loading">' +
			'   <td class="spacer"></td>' +
			'   <th></th>' +
			'   <td><span class="loading"></span></td>' +
			'</tr>';

		$( '.block-lab-fields-edit-settings', fieldRow ).remove();
		$( '.block-lab-fields-edit-control', fieldRow ).after( $( loadingRow ) );

		wp.ajax.send( 'fetch_field_settings', {
			success: function( data ) {
				$( '.block-lab-fields-edit-loading', fieldRow ).remove();

				if ( ! data.hasOwnProperty( 'html' ) ) {
					return;
				}
				let settingsRows = $( data.html );
				$( '.block-lab-fields-edit-control', fieldRow ).after( settingsRows );
			},
			error: function() {
				$( '.block-lab-fields-edit-loading', fieldRow ).remove();
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
