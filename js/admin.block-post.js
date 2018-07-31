(function($){

	$(function() {
		blockCategoryInit();

		$( '#acb-add-field' ).on( 'click', function() {
			let template = wp.template( 'field-repeater' ),
				data = { uid: new Date().getTime() },
				field = $( template( data ) );
			$( '.acb-fields-rows' ).append( field );
			field.find( '.acb-fields-options-edit' ).trigger( 'click' );
		});

		$( '#acb-properties-category' ).on( 'change', function() {
			if ( '__custom' === $( this ).val() ) {
				$( '#acb-properties-category-custom' ).show();
			} else {
				$( '#acb-properties-category-custom' ).hide();
			}
		});

		$( '.acb-fields-rows' )
			.on( 'click', '.acb-fields-options-delete', function() {
				$( this ).closest( '.acb-fields-row' ).remove();
			})
			.on( 'click', '.acb-fields-options-edit, a.row-title', function() {
				$( this ).closest( '.acb-fields-row' ).toggleClass( 'acb-fields-row-active' );
				$( this ).closest( '.acb-fields-row' ).find( '.acb-fields-edit' ).slideToggle();
			})
			.on( 'click', '.acb-fields-edit-actions-close', function() {
				$( this ).closest( '.acb-fields-row' ).removeClass( 'acb-fields-row-active' );
				$( this ).closest( '.acb-fields-edit' ).slideUp();
			})
			.on( 'change keyup', '.acb-fields-edit input, .acb-fields-edit select', function() {
				let sync = $( this ).data( 'sync' );
				$( '#' + sync ).text( $( this ).val() );
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
		let categories = wp.blocks.getCategories();
		for (let i = 0; i < categories.length; i++) {
			$( '<option/>', {
				value: categories[i].slug,
				text: categories[i].title,
			} ).appendTo( '#acb-properties-category' );
		}
		$( '#acb-properties-category option[value="__custom"]' ).remove().appendTo( '#acb-properties-category' );
		$( '#acb-properties-category' ).prop( 'selectedIndex', 0 );
	}

})(jQuery);