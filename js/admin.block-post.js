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

		$( '#block_fields' ).on( 'click', '#block-add-sub-field', function() {
			let template = wp.template( 'field-repeater' ),
				data     = { uid: new Date().getTime() },
				field    = $( template( data ) ),
				row      = $( this ).closest( '.block-fields-row' );

			// Prevents adding a repeater, in a repeater, in a repeater…
			field.find( '.block-fields-edit-control option[value="repeater"]' ).remove();

			// Add parent UID as a hidden input
			let parentInput = $( '<input>' ).attr({
				'type': 'hidden',
				'name': 'block-fields-parent[' + data.uid + ']',
				'value': row.find( 'input[name^="block-fields-name"]' ).val()
			});
			field.append( parentInput );

			$( '.block-fields-sub-rows', row ).append( field );
			$( '.repeater-no-fields', row ).hide();
			$( '.repeater-has-fields', row ).show();
			field.find( '.block-fields-actions-edit' ).trigger( 'click' );
			field.find( '.block-fields-edit-label input' ).select();
		});

		$( '#block_properties .block-properties-icon-select span' ).on( 'click', function() {
			let svg = $( 'svg', this ).clone();
			$( '#block_properties .block-properties-icon-select span.selected' ).removeClass( 'selected' );
			$( this ).addClass( 'selected' );
			$( '#block-properties-icon' ).val( $( this ).data( 'value' ) );
			$( '#block-properties-icon-current' ).html( svg );
		});

		$( '#block_template .template-location a.filename' ).on( 'click', function( event ) {
			event.preventDefault();

			let copy  = $( '#block_template .template-location .click-to-copy' ),
				input = $( 'input', copy ),
				width = $( this ).width() + input.outerWidth( false ) - input.width();

			copy.show();
			input.outerWidth( width ).focus().select();

			let copied = document.execCommand('copy');

			if ( copied ) {
				copy.attr( 'data-tooltip', blockLab.copySuccessMessage );
			} else {
				copy.attr( 'data-tooltip', blockLab.copyFailMessage );
			}

			$( this ).hide();
		});

		$( '#block_template .template-location .click-to-copy input' ).on( 'blur', function() {
			$( '#block_template .template-location a.filename' ).show();
			$( this ).parent().hide();
		});

		$( '.block-fields-rows' )
			.on( 'click', '.block-fields-actions-delete', function() {
				let subRows = $( this ).closest( '.block-fields-sub-rows' );
				$( this ).closest( '.block-fields-row' ).remove();
				if ( 0 === $( '.block-fields-rows' ).children( '.block-fields-row' ).length ) {
					$( '.block-no-fields' ).show();
				}
				if ( 0 !== subRows.length && 0 === $( '.block-fields-row', subRows ).length ) {
					$( '.repeater-no-fields' ).show();
					$( '.repeater-has-fields' ).hide();
				}
			})
			.on( 'click', '.block-fields-actions-edit, a.row-title', function() {
				let currentRow = $( this ).closest( '.block-fields-row' );

				// If we're expanding this row, first collapse all other rows and scroll this row into view.
				if ( ! currentRow.hasClass( 'block-fields-row-active' ) ) {
					let editRow   = $( '.block-fields-rows .block-fields-edit' );

					scrollRowIntoView( currentRow );
					editRow.slideUp();

					$( '.block-fields-rows .block-fields-row-active' ).removeClass( 'block-fields-row-active' );
				}

				currentRow.toggleClass( 'block-fields-row-active' );
				currentRow.find( '.block-fields-edit' ).first().slideToggle();

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
				let fieldRow = $( this ).closest( '.block-fields-row' );
				fieldRow.removeClass( 'block-fields-row-active' );
				$( '.block-fields-edit', fieldRow ).slideUp();
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

				if ( 'repeater' === $( this ).val() ) {
					let subRows = wp.template( 'sub-field-rows' );
					fieldRow.append( subRows );
					blockFieldSubRowsInit( $( '.block-fields-sub-rows', fieldRow ) );
				} else {
					$( '.block-fields-sub-rows,.block-fields-sub-rows-actions', fieldRow ).remove();
				}
			})
			.on( 'change keyup', '.block-fields-edit-label input', function() {
				let slug = $( this )
					.closest( '.block-fields-edit' )
					.find( '.block-fields-edit-name input' );

				if ( 'false' !== slug.data( 'autoslug' ) ) {
					slug
						.val( slugify( $( this ).val() ) )
						.trigger( 'change' );
				}
			})
			.on( 'blur', '.block-fields-edit-label input', function() {
				$( this )
					.closest( '.block-fields-edit' )
					.find( '.block-fields-edit-name input' )
					.data( 'autoslug', 'false' );
			})
			.on( 'mouseenter', '.block-fields-row div:not(.block-fields-edit,.block-fields-sub-rows,.block-fields-sub-rows-actions)', function() {
				$( this ).parent().addClass('hover');
			})
			.on( 'mouseleave', '.block-fields-row div', function() {
				$( this ).parent().removeClass('hover');
			})
			.sortable({
				axis: 'y',
				cursor: 'grabbing',
				handle: '> .block-fields-row-columns .block-fields-sort-handle',
				containment: 'parent',
				tolerance: 'pointer',
			});
	});

	let blockTitleInit = function() {
		let title = $( '#title' ),
		    slug  = $( '#block-properties-slug' );

		// If this is a new block, then enable auto-generated slugs.
		if( '' === title.val() && '' === slug.val() ) {

			// If auto-generated slugs are enabled, set the slug based on the title.
			title.on( 'change keyup', function() {
				if ( 'false' !== slug.data( 'autoslug' ) ) {
					slug.val( slugify( title.val() ) );
				}
			});

			// Turn auto-generated slugs off once a title has been set.
			title.on( 'blur', function() {
				if ( '' !== title.val() ) {
					slug.data( 'autoslug', 'false' );
				}
			});
		}
	};

	let blockIconInit = function() {
		let iconsContainer = $( '.block-properties-icon-select' ),
			selectedIcon   = $( '.selected', iconsContainer );
		if ( 0 !== iconsContainer.length && 0 !== selectedIcon.length ) {
			iconsContainer.scrollTop( selectedIcon.position().top );
		}
	};

	let blockFieldInit = function() {
		if ( 0 === $( '.block-fields-rows' ).children( '.block-fields-row' ).length ) {
			$( '.block-no-fields' ).show();
		}
		$( '.block-fields-edit-name input' ).data( 'autoslug', 'false' );
		$( '.block-fields-sub-rows' ).each( function() {
			blockFieldSubRowsInit( $( this ) );
		});
	};

	let blockFieldSubRowsInit = function( subRows ) {
		subRows.sortable({
			axis: 'y',
			cursor: 'grabbing',
			handle: '> .block-fields-row-columns .block-fields-sort-handle',
			containment: 'parent',
			tolerance: 'pointer',
		});
	}

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
				$( '.block-fields-edit-control', fieldRow ).after( settingsRows );
				scrollRowIntoView( fieldRow );
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

	let scrollRowIntoView = function( row ) {
		let scrollTop = 0;

		$( '.block-fields-rows .block-fields-row' ).each( function() {
			// Add the height of all previous rows to the target scrollTop position.
			if ( $( this ).is( row ) ) {
				return false;
			}

			let height = $( this ).children().first().outerHeight();
			scrollTop += height;
		});

		$( 'body' ).animate({
			scrollTop: scrollTop
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
