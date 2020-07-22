// @ts-check
/* global blockLabMigration */

/**
 * External dependencies
 */
import * as React from 'react';

/**
 * WordPress dependencies
 */
import { speak } from '@wordpress/a11y';
import apiFetch from '@wordpress/api-fetch';
import { Spinner } from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __, _n } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { Step, StepContent, StepFooter, StepIcon } from '../';

/**
 * @typedef {Object} MigrateBlocksProps The component props.
 * @property {Function} goToNext Goes to the next step.
 * @property {boolean} isStepActive Whether this step is active.
 * @property {boolean} isStepComplete Whether this step is complete.
 * @property {number} stepIndex The step index of this step.
 */

/**
 * The step that migrates the blocks.
 *
 * @param {MigrateBlocksProps} Props The component props.
 * @return {React.ReactElement} The component to prompt to migrate the post content.
 */
const MigrateBlocks = ( { isStepActive, isStepComplete, stepIndex } ) => {
	const [ currentBlockMigrationStep, setCurrentBlockMigrationStep ] = useState( 0 );
	const [ isInProgress, setIsInProgress ] = useState( false );
	const [ isError, setIsError ] = useState( false );
	const [ errorMessages, setErrorMessages ] = useState( [] );
	const [ isSuccess, setIsSuccess ] = useState( false );

	const migrationLabels = [
		__( 'Migrating your blocks...', 'block-lab' ),
		__( 'Migrating your post content...', 'block-lab' ),
	];

	/**
	 * Migrates the blocks, going through each migration step.
	 */
	const migrateBlocks = async () => {
		speak( __( 'The migration is now in progress', 'block-lab' ) );
		setIsInProgress( true );
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
			setIsError( true );
			setIsInProgress( false );
			return;
		}

		const contentMigrationResult = await apiFetch( {
			path: '/block-lab/migrate-post-content',
			method: 'POST',
		} );

		// @ts-ignore
		if ( contentMigrationResult.success ) {
			speak( __( 'The migration was successful!', 'block-lab' ) );
			setIsSuccess( true );
		} else {
			// @ts-ignore
			setErrorMessages( contentMigrationResult.errorMessages );
			speak( __( 'The migration failed', 'block-lab' ) );
			setIsError( true );
		}

		setIsInProgress( false );
	};

	return (
		<Step isActive={ isStepActive } isComplete={ isStepComplete }>
			<StepIcon
				index={ stepIndex }
				isComplete={ isStepComplete }
			/>
			<StepContent
				heading={ __( 'Migrate your Blocks', 'block-lab' ) }
				isStepActive={ isStepActive }
			>
				{ ! isSuccess && <p>{ __( "Ok! Everything is ready. Let's do this. While the migration is underway, don't leave this page.", 'block-lab' ) }</p> }
				{ !! errorMessages.length && (
					<div className="bl-migration__error">
						<p>{ _n( 'The following error ocurred:', 'The following errors ocurred:', errorMessages.length, 'block-lab' ) }</p>
						{ errorMessages.map( ( message, index ) => <p key={ `bl-error-message-${ index }` }>{ message }</p> ) }
					</div>
				) }
				{ isInProgress && (
					<>
						<Spinner />
						<p>{ migrationLabels[ currentBlockMigrationStep ] }</p>
					</>
				) }
				{ ! isSuccess && (
					<button
						className="btn"
						onClick={ migrateBlocks }
					>
						{ isError ? __( 'Try Again', 'block-lab' ) : __( 'Migrate Now', 'block-lab' ) }
					</button>
				) }
				{ isSuccess && (
					<>
						<p>
							<span role="img" aria-label={ __( 'party emoji', 'block-lab' ) }>🎉</span>
							&nbsp;
							{ __( 'The migration completed successfully! Time to say goodbye to Block Lab (it’s been fun!) and step into the FUTURE', 'block-lab' ) }
							&nbsp;
							<span className="message-future">{ __( 'FUTURE', 'block-lab' ) }</span>
							&nbsp;
							<sub>{ __( 'FUTURE', 'block-lab' ) }</sub>.
						</p>
						<StepFooter>
							{ /* @ts-ignore */ }
							<a href={ blockLabMigration.gcbUrl } className="btn">
								{ __( 'Go To Genesis Custom Blocks', 'block-lab' ) }
							</a>
						</StepFooter>
					</>
				) }
			</StepContent>
		</Step>
	);
};

export default MigrateBlocks;
