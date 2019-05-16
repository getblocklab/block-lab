/* global blockLab */

const { InspectorAdvancedControls } = wp.editor;
const { DotTip } = wp.nux;
const { sprintf, __ } = wp.i18n;

const inspectorControls = ( props, block ) => {
	if ( '-1' === blockLab.authorBlocks.indexOf( block.name ) ) {
		return;
	}

	const tip = sprintf(
		__( 'The Additional CSS Class can be included in your block template with the %1$s field.', 'block-lab' ),
		'<code>className</code>'
	);

	return (
		<InspectorAdvancedControls>
			<DotTip tipId="block-lab/additional-css-class">
				<p className="bl-dot-tip" dangerouslySetInnerHTML={{__html: tip}}></p>
				<p className="bl-dot-tip read-more">
					<a href="https://github.com/getblocklab/block-lab/wiki/7.-FAQ" target="_blank">{__( 'Read more', 'block-lab' )}</a>
				</p>
			</DotTip>
		</InspectorAdvancedControls>
	)
}

export default inspectorControls