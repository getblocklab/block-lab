import BlockLabTextControl from './text'
import BlockLabTextareaControl from './textarea'
import BlockLabURLControl from './url'
import BlockLabEmailControl from './email'
import BlockLabNumberControl from './number'
import BlockLabCheckboxControl from './checkbox'
import BlockLabRadioControl from './radio'
import BlockLabRangeControl from './range'
import BlockLabSelectControl from './select'
import BlockLabMultiselectControl from './multiselect'
import BlockLabToggleControl from './toggle'
import BlockLabColorPickerControl from './color-picker'

export default {
	'text': BlockLabTextControl,
	'textarea': BlockLabTextareaControl,
	'url': BlockLabURLControl,
	'email': BlockLabEmailControl,
	'number': BlockLabNumberControl,
	'checkbox': BlockLabCheckboxControl,
	'radio': BlockLabRadioControl,
	'range': BlockLabRangeControl,
	'select': BlockLabSelectControl,
	'multiselect': BlockLabMultiselectControl,
	'toggle': BlockLabToggleControl,
	'color-picker': BlockLabColorPickerControl,
}
