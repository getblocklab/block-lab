// @ts-check

/**
 * External dependencies
 */
import * as React from 'react';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import { Step, StepContent, StepIcon } from '../';

/**
 * @typedef {Object} MigrateBlocksProps The component props.
 * @property {number} currentStepIndex The current step in the migration process.
 * @property {number} stepIndex The step index of this step.
 * @property {Function} goToNext Goes to the next step.
 */

/**
 * The step that prompts to back up the site.
 *
 * @param {MigrateBlocksProps} Props The component props.
 * @return {React.ReactElement} The component to prompt to migrate the post content.
 */
const MigrateBlocks = ( { currentStepIndex, stepIndex, goToNext } ) => {
	const isStepActive = currentStepIndex === stepIndex;
	const isStepComplete = currentStepIndex > stepIndex;
	const [ currentBlockMigrationStep, setCurrentBlockMigrationStep ] = useState( 0 );
	const [ isMigrationInProgress, setIsMigrationInProgress ] = useState( false );

	const migrationLabels = [
		__( 'Migrating your blocks...', 'block-lab' ),
		__( 'Migrating your post content...', 'block-lab' ),
		__( 'Installing Genesis Custom Blocks...', 'block-lab' ),
	];
	const numberOfBlockMigrationSteps = migrationLabels.length;
	const progressStep = 1 / numberOfBlockMigrationSteps;
	const progressRatio = progressStep + ( currentBlockMigrationStep / numberOfBlockMigrationSteps );

	/**
	 * Migrates the blocks, going through each migration step.
	 *
	 * @todo Refactor this, and handle the 'Intall GCB' step.
	 */
	const migrateBlocks = async () => {
		setIsMigrationInProgress( true );
		const postTypeMigrationResult = await apiFetch( {
			path: '/block-lab/migrate-post-type',
			method: 'POST',
		} );

		// @ts-ignore
		if ( postTypeMigrationResult.success ) {
			setCurrentBlockMigrationStep( 1 );
		} else {
			return;
		}

		const contentMigrationResult = await apiFetch( {
			path: '/block-lab/migrate-post-content',
			method: 'POST',
		} );

		// @ts-ignore
		if ( contentMigrationResult.success ) {
			goToNext();
		}
	};

	return (
		<Step isActive={ isStepActive } isComplete={ isStepComplete }>
			<StepIcon
				index={ stepIndex }
				isComplete={ isStepComplete }
			/>
			<StepContent>
				<h3>{ __( 'Migrate your Blocks', 'block-lab' ) }</h3>
				<p>{ __( "Ok! Everything is ready. Let's do this. While the migration is underway, don't leave this page.", 'block-lab' ) }</p>
				{ isMigrationInProgress ? (
					<div>
						<progress
							value={ progressRatio * 100 }
							max="100"
						/>
						<label>{ migrationLabels[ currentBlockMigrationStep ] }</label>
					</div>
				) : (
					<button
						className="btn"
						onClick={ migrateBlocks }
					>
						{ __( 'Migrate Now', 'block-lab' ) }
					</button>
				) }
			</StepContent>
		</Step>
	);
};

export default MigrateBlocks;
