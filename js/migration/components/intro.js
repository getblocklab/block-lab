// @ts-check
/* global blockLabMigration */

/**
 * External dependencies
 */
import * as React from 'react';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * The introduction to the migration.
 *
 * @return {React.ReactElement} The introduction to the migration.
 */
const Intro = () => {
	// @ts-ignore
	const editorGifUrl = blockLabMigration.editorGifUrl;
	const developerNoticeUrl = 'https://getblocklab.com/migrating-to-genesis-custom-blocks/';

	return (
		<>
			<div>
				<h1>{ __( 'Migrating to Genesis Custom Blocks', 'block-lab' ) }</h1>
				<p>{ __( 'We’ve been busy! Since the Block Lab team joined WP Engine in 2020, the new plugin (Genesis Custom Blocks) has got a whole lot better. Plenty of things under the hood, but the biggest change is the new Block Builder interface.', 'block-lab' ) }</p>
				<p>{ __( 'Check it out below!', 'block-lab' ) }</p>
				<img
					width={ 800 }
					height={ 534 }
					src={ editorGifUrl }
					alt={ __( 'New editor screencast', 'block-lab' ) }
				/>
				<p>{ __( 'Migrating from Block Lab to Genesis Custom Blocks is super easy, seamless, and (for 95% of users) completely automated.', 'block-lab' ) }</p>
				<p>{ __( 'To continue receiving the best of what our team is building, we encourage you to migrate over.', 'block-lab' ) }</p>
				<div className="dev-notice">
					<svg fill="currentColor" viewBox="0 0 20 20">
						<path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clipRule="evenodd"></path>
					</svg>
					<span>{ __( 'Need to let the developer for this site know about this? Send them this link.', 'block-lab' ) }</span>
					<a href={ developerNoticeUrl } target="_blank" rel="noopener noreferrer" className="btn">
						<span>{ __( 'Developer Notice', 'block-lab' ) }</span>
						<svg fill="currentColor" viewBox="0 0 20 20">
							<path d="M11 3a1 1 0 100 2h3.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z"></path>
							<path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z"></path>
						</svg>
					</a>
				</div>
			</div>
			<h2>{ __( "Let's Migrate", 'block-lab' ) }</h2>
		</>
	);
};

export default Intro;
