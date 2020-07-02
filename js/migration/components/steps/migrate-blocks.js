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

/**
 * Internal dependencies
 */
import { Step, StepContent, StepIcon } from '../';

/**
 * @typedef {Object} MigrateBlocksProps The component props.
 * @property {number} currentStepIndex The current step in the migration process.
 * @property {number} stepIndex The step index of this step.
 * @property {React.MouseEventHandler} goToNext Goes to the next step.
 * @property {React.MouseEventHandler} goToPrevious Goes to the next step.
 */

/**
 * The step that prompts to back up the site.
 *
 * @param {MigrateBlocksProps} Props The component props.
 * @return {React.ReactElement} The component to prompt to back up the site.
 */
const MigrateBlocks = ( { currentStepIndex, stepIndex } ) => {
	const isStepActive = currentStepIndex === stepIndex;
	const isStepComplete = currentStepIndex > stepIndex;
	const [ currentBlockMigrationStep ] = useState( 0 );
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
	 * @todo Add the API calls and advance through the 3 migration steps.
	 */
	const migrateBlocks = () => {
		setIsMigrationInProgress( true );
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
