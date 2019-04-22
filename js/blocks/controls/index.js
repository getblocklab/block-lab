import BlockLabTextControl from './text'
import BlockLabTextareaControl from './textarea'
import BlockLabURLControl from './url'
import BlockLabEmailControl from './email'
import BlockLabNumberControl from './number'
import BlockLabColorControl from './color'
import BlockLabImageControl from './image'
import BlockLabCheckboxControl from './checkbox'
import BlockLabRadioControl from './radio'
import BlockLabRangeControl from './range'
import BlockLabSelectControl from './select'
import BlockLabMultiselectControl from './multiselect'
import BlockLabPostControl from './post'
import BlockLabToggleControl from './toggle'
import BlockLabUserControl from './user'

export default {
	text: BlockLabTextControl,
	textarea: BlockLabTextareaControl,
	url: BlockLabURLControl,
	email: BlockLabEmailControl,
	number: BlockLabNumberControl,
	color: BlockLabColorControl,
	image: BlockLabImageControl,
	checkbox: BlockLabCheckboxControl,
	radio: BlockLabRadioControl,
	range: BlockLabRangeControl,
	select: BlockLabSelectControl,
	multiselect: BlockLabMultiselectControl,
	post: BlockLabPostControl,
	toggle: BlockLabToggleControl,
	user: BlockLabUserControl,
}