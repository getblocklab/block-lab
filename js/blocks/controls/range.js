const { RangeControl } = wp.components;

const BlockLabRangeControl = ( props, field, block ) => {
	const { setAttributes } = props
	const attr = { ...props.attributes }
	return (
		<RangeControl
			beforeIcon={field.beforeIcon}
			afterIcon={field.afterIcon}
			label={field.label}
			help={field.help}
			value={ ( attr[ field.name ] || '0' == attr[ field.name ] || undefined == attr[ field.name ] ) ? attr[ field.name ] : field.default }
			onChange={rangeControl => {
				attr[ field.name ] = rangeControl
				setAttributes( attr )
			}}
			min={field.min}
			max={field.max}
			step={field.step}
			allowReset={field.allowReset}
		/>
	)
}

export default BlockLabRangeControl