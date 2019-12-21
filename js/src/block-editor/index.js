/**
 * WordPress dependencies
 */
const { i18n } = wp;

/**
 * Internal dependencies
 */
import registerBlocks from './helpers/registerBlocks';

i18n.setLocaleData( { '': {} }, 'block-lab' );
registerBlocks();
