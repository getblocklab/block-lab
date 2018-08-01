import inspectorControls from './inspector'
import { getControl } from "./controls";
import { simplifiedFields } from "./fields";
import icons from '../icons'

const { __ } = wp.i18n;

const formControls = ( props, block ) => {

	const fields = simplifiedFields( block.fields ).map( field => {

		// If its not meant for the inspector then continue (return null).
		// if ( !field.location ) {
		if ( ! field.location || ! field.location.includes('editor') ) {
			return null
		}

		return (
			<div>
				{getControl( props, field )}
			</div>
		)
	} )

	return (
		<div>
			{fields}
		</div>
	)
}


const editComponent = (props, block) => {
	const { className, isSelected } = props;

	return [
		inspectorControls(props, block),
		(
			<div className={className}>
				<h3>{ icons.logo } { block.title }</h3>
				{isSelected ? (
					<div>
					{formControls(props, block)}
					</div>
				) : null}
			</div>
		),
	]
}

export default editComponent