import BlockLabTextControl from './text'
import BlockLabTextareaControl from './textarea'
import BlockLabURLControl from './url'
import BlockLabEmailControl from './email'
import BlockLabNumberControl from './number'
import BlockLabColorControl from './color'
import BlockLabMediaControl from './media'
import BlockLabCheckboxControl from './checkbox'
import BlockLabRadioControl from './radio'
import BlockLabRangeControl from './range'
import BlockLabSelectControl from './select'
import BlockLabMultiselectControl from './multiselect'
import BlockLabToggleControl from './toggle'

export default {
	text: BlockLabTextControl,
	textarea: BlockLabTextareaControl,
	url: BlockLabURLControl,
	email: BlockLabEmailControl,
	number: BlockLabNumberControl,
	color: BlockLabColorControl,
	media: BlockLabMediaControl,
	checkbox: BlockLabCheckboxControl,
	radio: BlockLabRadioControl,
	range: BlockLabRangeControl,
	select: BlockLabSelectControl,
	multiselect: BlockLabMultiselectControl,
	toggle: BlockLabToggleControl,
}