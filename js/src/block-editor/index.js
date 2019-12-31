/* global blockLab, blockLabBlocks */

/**
 * WordPress dependencies
 */
const { i18n } = wp;

/**
 * Internal dependencies
 */
import { registerBlocks } from './helpers';
import { Edit } from './components';

i18n.setLocaleData( { '': {} }, 'block-lab' );
registerBlocks( blockLab, blockLabBlocks, Edit );
