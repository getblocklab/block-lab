// @ts-check

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
	const developerNoticeUrl = 'https://getblocklab.com/migrating-to-genesis-custom-blocks/';
	const announcementUrl = 'https://getblocklab.com/the-block-lab-team-are-joining-wp-engine/';

	return (
		<>
			<div>
				<h1>{ __( 'Migrating to Genesis Custom Blocks', 'block-lab' ) }</h1>
				<p>{ __( 'In April, the Block Lab team joined the Genesis team at WP Engine. With our full-time focus, we’re very excited about the future of custom block tooling in WordPress. You can read more about that moment in this', 'block-lab' ) } <a target="_blank" rel="noopener noreferrer" className="text-purple-600 underline hover:text-purple-700" href={ announcementUrl }>{ __( 'announcement post', 'block-lab' ) }.</a></p>
				<p>
					{ __( 'As part of this move, we have been working on a new plugin that is based on what we developed at Block Lab.', 'block-lab' ) }
					&nbsp;
					{ __( 'Genesis Custom Blocks is now the home of all our custom block efforts and what we have planned is very very cool!', 'block-lab' ) }
					&nbsp;
					{ __( 'Version 1.0 of this plugin is now released and has full feature parity with Block Lab.', 'block-lab' ) }
				</p>
				<p>{ __( 'To continue receiving the best of what our team is building, we encourage you to migrate over. Our migration tool makes this nice and easy, and for the majority of use cases, completely automated.', 'block-lab' ) }</p>
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
