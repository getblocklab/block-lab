// @ts-check

/**
 * External dependencies
 */
import * as React from 'react';

/**
 * WordPress dependencies
 */
import { useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { __, _n } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { Step, StepContent, StepIcon } from '../';

/**
 * @typedef {Object} MigrateBlocksProps The component props.
 * @property {Function} goToNext Goes to the next step.
 * @property {boolean} isStepActive Whether this step is active.
 * @property {boolean} isStepComplete Whether this step is complete.
 * @property {number} stepIndex The step index of this step.
 */

/**
 * The step that prompts to back up the site.
 *
 * @param {MigrateBlocksProps} Props The component props.
 * @return {React.ReactElement} The component to prompt to migrate the post content.
 */
const MigrateBlocks = ( { goToNext, isStepActive, isStepComplete, stepIndex } ) => {
	const [ currentBlockMigrationStep, setCurrentBlockMigrationStep ] = useState( 0 );
	const [ isMigrationInProgress, setIsMigrationInProgress ] = useState( false );
	const [ isMigrationError, setIsMigrationError ] = useState( false );
	const [ errorMessages, setErrorMessages ] = useState( [] );

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
	 * @todo Add the 'Install GCB' step.
	 */
	const migrateBlocks = async () => {
		setIsMigrationInProgress( true );
		setErrorMessages( [] );

		const postTypeMigrationResult = await apiFetch( {
			path: '/block-lab/migrate-post-type',
			method: 'POST',
		} );

		// @ts-ignore
		if ( postTypeMigrationResult.success ) {
			setCurrentBlockMigrationStep( 1 );
		} else {
			setErrorMessages( [ __( 'Migrating the post type failed.', 'block-lab' ) ] );
			setIsMigrationError( true );
			setIsMigrationInProgress( false );
			return;
		}

		const contentMigrationResult = await apiFetch( {
			path: '/block-lab/migrate-post-content',
			method: 'POST',
		} );

		// @ts-ignore
		if ( contentMigrationResult.success ) {
			goToNext();
		} else {
			// @ts-ignore
			setErrorMessages( contentMigrationResult.errorMessages );
			setIsMigrationError( true );
			setIsMigrationInProgress( false );
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
				{ !! errorMessages.length && (
					<div className="bl-migration__error">
						<p>{ _n( 'The following error ocurred:', 'The following errors ocurred:', errorMessages.length, 'block-lab' ) }</p>
						{ errorMessages.map( ( message, index ) => <p key={ `bl-error-message-${ index }` }>{ message }</p> ) }
					</div>
				) }
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
						{ isMigrationError ? __( 'Try Again', 'block-lab' ) : __( 'Migrate Now', 'block-lab' ) }
					</button>
				) }
			</StepContent>
		</Step>
	);
};

export default MigrateBlocks;
