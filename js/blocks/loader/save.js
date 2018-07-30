const { __ } = wp.i18n;

const saveComponent = (props, block) => {
	return (
		<p>{__( 'Example block content [SAVE]', 'advanced-custom-blocks' )}</p>
	);
}

export default saveComponent