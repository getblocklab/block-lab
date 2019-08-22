/* global blockLab */

/**
 * WordPress dependencies
 */
const { InspectorAdvancedControls } = wp.editor;
const { DotTip } = wp.nux;
const { sprintf, __ } = wp.i18n;

/**
 * Renders the inspector advanced controls.
 *
 * @param {Object} block The block that the controls are for.
 * @return {Function|null} The advanced controls.
 */
const AdvancedControls = ( { block } ) => {
	if ( '-1' === blockLab.authorBlocks.indexOf( block.name ) ) {
		return;
	}

	const tip = sprintf(
		__( 'The Additional CSS Class can be included in your block template with the %1$s field.', 'block-lab' ),
		'<code>className</code>'
	);

	return (
		<InspectorAdvancedControls key={ `inspector-advanced-controls-${ block.name }` }>
			<DotTip tipId="block-lab/additional-css-class">
				<p className="bl-dot-tip" dangerouslySetInnerHTML={ { __html: tip } }></p>
				<p className="bl-dot-tip read-more">
					<a href="https://getblocklab.com/docs/faqs/" target="_blank">{ __( 'Read more', 'block-lab' ) }</a>
				</p>
			</DotTip>
		</InspectorAdvancedControls>
	)
};

export default AdvancedControls;
