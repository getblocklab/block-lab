/**
 * Internal dependencies
 */
import BlockLabTextControl from './text';
import BlockLabTextareaControl from './textarea';
import BlockLabClassicTextControl from './classic-text';
import BlockLabRichTextControl from './rich-text';
import BlockLabURLControl from './url';
import BlockLabEmailControl from './email';
import BlockLabNumberControl from './number';
import BlockLabColorControl from './color';
import BlockLabImageControl from './image';
import BlockLabCheckboxControl from './checkbox';
import BlockLabRadioControl from './radio';
import BlockLabRangeControl from './range';
import BlockLabSelectControl from './select';
import BlockLabMultiselectControl from './multiselect';
import BlockLabPostControl from './post';
import BlockLabRepeaterControl from './repeater';
import BlockLabTaxonomyControl from './taxonomy';
import BlockLabToggleControl from './toggle';
import BlockLabUserControl from './user';

export default {
	text: BlockLabTextControl,
	textarea: BlockLabTextareaControl,
	classic_text: BlockLabClassicTextControl,
	rich_text: BlockLabRichTextControl,
	url: BlockLabURLControl,
	email: BlockLabEmailControl,
	number: BlockLabNumberControl,
	color: BlockLabColorControl,
	image: BlockLabImageControl,
	checkbox: BlockLabCheckboxControl,
	radio: BlockLabRadioControl,
	range: BlockLabRangeControl,
	repeater: BlockLabRepeaterControl,
	select: BlockLabSelectControl,
	multiselect: BlockLabMultiselectControl,
	post: BlockLabPostControl,
	taxonomy: BlockLabTaxonomyControl,
	toggle: BlockLabToggleControl,
	user: BlockLabUserControl,
};
