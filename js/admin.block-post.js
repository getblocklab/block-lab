/* globals wp, advancedCustomBlocks */
(function( $ ) {

	$(function() {
		blockCategoryInit();

		$( '#acb-add-field' ).on( 'click', function() {
			let template = wp.template( 'field-repeater' ),
				data = { uid: new Date().getTime() },
				field = $( template( data ) );
			$( '.acb-fields-rows' ).append( field );
			field.find( '.acb-fields-actions-edit' ).trigger( 'click' );
		});

		$( '.acb-fields-rows' )
			.on( 'click', '.acb-fields-actions-delete', function() {
				$( this ).closest( '.acb-fields-row' ).remove();
			})
			.on( 'click', '.acb-fields-actions-edit, a.row-title', function() {
				$( this ).closest( '.acb-fields-row' ).toggleClass( 'acb-fields-row-active' );
				$( this ).closest( '.acb-fields-row' ).find( '.acb-fields-edit' ).slideToggle();

				// Fetch field options if field is active and there are no options
				if ( $( this ).closest( '.acb-fields-row' ).hasClass( 'acb-fields-row-active' ) ) {
					if ( 0 === $( this ).closest( '.acb-fields-row' ).find( '.acb-fields-edit-options' ).length ) {
						let fieldRow = $( this ).closest( '.acb-fields-row' ),
							fieldControl = fieldRow.find( '.acb-fields-edit-control select' ).val();
						fetchFieldOptions( fieldRow, fieldControl );
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
				fetchFieldOptions( fieldRow, $( this ).val() );
			})
			.on( 'change keyup', '.acb-fields-edit-label input', function() {
				let slug = slugify( $( this ).val() );
				$( this )
					.closest( '.acb-fields-edit' )
					.find( '.acb-fields-edit-name input' )
					.val( slug )
					.trigger('change');
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
		let categories = wp.blocks.getCategories(),
			category   = $( '#acb-properties-category-saved' );

		for (let i = 0; i < categories.length; i++) {
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

	let fetchFieldOptions = function( fieldRow, fieldControl ) {
		if ( ! advancedCustomBlocks.hasOwnProperty( 'fieldOptionsNonce' ) ) {
			return;
		}

		let loadingRow = '' +
			'<tr class="acb-fields-edit-loading">' +
			'   <td class="spacer"></td>' +
			'   <th></th>' +
			'   <td><span class="loading"></span></td>' +
			'</tr>';

		$( '.acb-fields-edit-options', fieldRow ).remove();
		$( '.acb-fields-edit-control', fieldRow ).after( $( loadingRow ) );

		wp.ajax.send( 'fetch_field_options', {
			success: function( data ) {
				$( '.acb-fields-edit-loading', fieldRow ).remove();

				if ( ! data.hasOwnProperty( 'html' ) ) {
					return;
				}
				let optionsRows = $( data.html );
				$( '.acb-fields-edit-control', fieldRow ).after( optionsRows );
			},
			error: function() {
				$( '.acb-fields-edit-loading', fieldRow ).remove();
			},
			data: {
				control: fieldControl,
				uid:     fieldRow.data( 'uid' ),
				nonce:   advancedCustomBlocks.fieldOptionsNonce
			}
		});
	};

	let slugify = function( text ) {
		return text
			.toLowerCase()
			.replace(/[^\w ]+/g,'')
			.replace(/ +/g,'-');
	};

})(jQuery);