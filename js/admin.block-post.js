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
			category   = $( '#acb-properties-category-custom' ),
			custom     = $( '#acb-properties-category option[value="__custom"]' );

		for (let i = 0; i < categories.length; i++) {
			if ( 'reusable' === categories[i].slug ) {
				continue;
			}
			$( '<option/>', {
				value: categories[i].slug,
				text: categories[i].title,
			} ).appendTo( '#acb-properties-category' );
		}
		custom.remove().appendTo( '#acb-properties-category' );

		if ( category.val() !== '' ) {
			let option = $( '#acb-properties-category option[value="' + category.val() + '"]' );
			if ( option.length > 0 ) {
				$( '#acb-properties-category' ).prop( 'selectedIndex', option.index() );
				category.hide();
				category.val('');
			} else {
				$( '#acb-properties-category' ).prop( 'selectedIndex', custom.index() );
				category.show();
			}
		} else {
			$( '#acb-properties-category' ).prop( 'selectedIndex', 0 );
			category.hide();
		}
	};

	let slugify = function( text ) {
		return text
			.toLowerCase()
			.replace(/[^\w ]+/g,'')
			.replace(/ +/g,'-');
	};

})(jQuery);