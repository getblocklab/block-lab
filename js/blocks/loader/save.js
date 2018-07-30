const { __ } = wp.i18n;

const saveComponent = props => {
	return (
		<p>{__( 'Example block content [SAVE]', 'advanced-custom-blocks' )}</p>
	);
}

export default saveComponent