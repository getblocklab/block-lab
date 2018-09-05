/**
 * Used for editing Blocks.
 *
 * @package   Advanced_Custom_Blocks
 * @copyright Copyright(c) 2018, Advanced Custom Blocks
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 *
 * Globals wp, advancedCustomBlocks
 */

(function( $ ) {

	$(function() {
		blockCategoryInit();
		blockIconInit();
		blockFieldInit();

		$( '#title' ).on( 'change keyup', function() {
			let slug = slugify( $( this ).val() );
			$( '#acb-properties-slug' ).val( slug );
		});

		$( '#acb-add-field' ).on( 'click', function() {
			let template = wp.template( 'field-repeater' ),
				data     = { uid: new Date().getTime() },
				field    = $( template( data ) );
			$( '.acb-fields-rows' ).append( field );
			$( '.acb-no-fields' ).hide();
			field.find( '.acb-fields-actions-edit' ).trigger( 'click' );
		});

		$( '#acb_block_properties .acb-properties-icons span' ).on( 'click', function() {
			$( '#acb_block_properties .acb-properties-icons span.selected' ).removeClass( 'selected' );
			$( this ).addClass( 'selected' );
			$( '#acb-properties-icon' ).val( $( this ).data( 'value' ) );
		});

		$( '.acb-fields-rows' )
			.on( 'click', '.acb-fields-actions-delete', function() {
				$( this ).closest( '.acb-fields-row' ).remove();
				if ( 0 === $( '.acb-fields-rows' ).children( '.acb-fields-row' ).length ) {
					$( '.acb-no-fields' ).show();
				}
			})
			.on( 'click', '.acb-fields-actions-edit, a.row-title', function() {
				$( this ).closest( '.acb-fields-row' ).toggleClass( 'acb-fields-row-active' );
				$( this ).closest( '.acb-fields-row' ).find( '.acb-fields-edit' ).slideToggle();

				// Fetch field settings if field is active and there are no settings.
				if ( $( this ).closest( '.acb-fields-row' ).hasClass( 'acb-fields-row-active' ) ) {
					if ( 0 === $( this ).closest( '.acb-fields-row' ).find( '.acb-fields-edit-settings' ).length ) {
						let fieldRow     = $( this ).closest( '.acb-fields-row' ),
							fieldControl = fieldRow.find( '.acb-fields-edit-control select' ).val();
						fetchFieldSettings( fieldRow, fieldControl );
					}
				}
			})
			.on( 'click', '.acb-fields-edit-actions-close a.button', function() {
				$( this ).closest( '.acb-fields-row' ).removeClass( 'acb-fields-row-active' );
				$( this ).closest( '.acb-fields-edit' ).slideUp();
			})
			.on( 'change keyup', '.acb-fields-edit input, .acb-fields-edit select', function() {
				let sync = $( this ).data( 'sync' );
				$( '#' + sync ).text( $( this ).val() );
			})
			.on( 'change', '.acb-fields-edit-control select', function() {
				let fieldRow = $( this ).closest( '.acb-fields-row' );
				fetchFieldSettings( fieldRow, $( this ).val() );
			})
			.on( 'change keyup', '.acb-fields-edit-label input', function() {
				let slug = slugify( $( this ).val() );
				$( this )
					.closest( '.acb-fields-edit' )
					.find( '.acb-fields-edit-name input' )
					.val( slug )
					.trigger( 'change' );
			})
			.sortable({
				axis: 'y',
				cursor: 'grabbing',
				handle: '.acb-fields-sort-handle',
				containment: 'parent',
				tolerance: 'pointer'
			});
	});

	let blockCategoryInit = function() {
		let categories       = wp.blocks.getCategories(),
			categoriesLength = categories.length,
			category         = $( '#acb-properties-category-saved' );

		for (let i = 0; i < categoriesLength; i++) {
			if ( 'reusable' === categories[i].slug ) {
				continue;
			}
			$( '<option/>', {
				value: categories[i].slug,
				text: categories[i].title,
			} ).appendTo( '#acb-properties-category' );
		}

		if ( category.val() !== '' ) {
			let option = $( '#acb-properties-category option[value="' + category.val() + '"]' );
			if ( option.length > 0 ) {
				$( '#acb-properties-category' ).prop( 'selectedIndex', option.index() );
			}
		}
	};

	let blockIconInit = function() {
		let iconsContainer = $( '.acb-properties-icons' ),
			selectedIcon   = $( '.selected', iconsContainer );
		if ( 0 !== iconsContainer.length && 0 !== selectedIcon.length ) {
			iconsContainer.scrollTop( selectedIcon.position().top );
		}
	}

	let blockFieldInit = function() {
		if ( 0 === $( '.acb-fields-rows' ).children( '.acb-fields-row' ).length ) {
			$( '.acb-no-fields' ).show();
		}
	};

	let fetchFieldSettings = function( fieldRow, fieldControl ) {
		if ( ! advancedCustomBlocks.hasOwnProperty( 'fieldSettingsNonce' ) ) {
			return;
		}

		let loadingRow = '' +
			'<tr class="acb-fields-edit-loading">' +
			'   <td class="spacer"></td>' +
			'   <th></th>' +
			'   <td><span class="loading"></span></td>' +
			'</tr>';

		$( '.acb-fields-edit-settings', fieldRow ).remove();
		$( '.acb-fields-edit-control', fieldRow ).after( $( loadingRow ) );

		wp.ajax.send( 'fetch_field_settings', {
			success: function( data ) {
				$( '.acb-fields-edit-loading', fieldRow ).remove();

				if ( ! data.hasOwnProperty( 'html' ) ) {
					return;
				}
				let settingsRows = $( data.html );
				$( '.acb-fields-edit-control', fieldRow ).after( settingsRows );
			},
			error: function() {
				$( '.acb-fields-edit-loading', fieldRow ).remove();
			},
			data: {
				control: fieldControl,
				uid:     fieldRow.data( 'uid' ),
				nonce:   advancedCustomBlocks.fieldSettingsNonce
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
