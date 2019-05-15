const { Notice } = wp.components;
const { InspectorAdvancedControls } = wp.editor;
const { select, dispatch } = wp.data;
const { __ } = wp.i18n;

const ADDITIONAL_CSS_NOTICE_ID = 'additional_css_class';

const removeNotice = ( event ) => {
	event.currentTarget.parentNode.remove()
	dispatch( 'block-lab' ).setStatus( ADDITIONAL_CSS_NOTICE_ID, 'dismissed' );
}

const advancedControls = () => {

	const notice = () => {
		let status = select( 'block-lab' ).getNotice( ADDITIONAL_CSS_NOTICE_ID );

		if ( 'dismissed' === status ) {
			return
		}

		return (
			<Notice
				status="info"
				isDismissible={true}
				onRemove={ removeNotice }
				id="bl-inspector-notice"
				className="bl-inspector-notice"
			>
				<p>{__( 'Include the Additional CSS Class in a block template by using this field:', 'block-lab' )}<br /><code>className</code></p>
				<p><a href="https://github.com/getblocklab/block-lab/wiki/7.-FAQ" target="_blank">{__( 'Read more', 'block-lab' )}</a></p>
			</Notice>
		)
	}

	return(
		<InspectorAdvancedControls>
			{notice}
		</InspectorAdvancedControls>
	)
}

export default advancedControls