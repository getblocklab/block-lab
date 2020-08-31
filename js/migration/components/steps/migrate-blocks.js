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
import { __ } from '@wordpress/i18n';

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
	const [ errorMessage, setErrorMessage ] = useState( '' );
	const [ isSuccess, setIsSuccess ] = useState( false );

	const migrationLabels = [
		__( 'Migrating your blocks…', 'block-lab' ),
		__( 'Migrating your post content…', 'block-lab' ),
	];

	/**
	 * Migrates the custom post type, then chains post content migration to the callback.
	 */
	const migrateCpt = async () => {
		await apiFetch( {
			path: '/block-lab/migrate-post-type',
			method: 'POST',
		} ).then( async () => {
			setCurrentBlockMigrationStep( 1 );
			await migratePostContent();
		} ).catch( ( result ) => {
			if ( result.hasOwnProperty( 'message' ) ) {
				setErrorMessage( result.message );
			}
			speak( __( 'The migration failed in the CPT migration', 'block-lab' ) );
			setIsError( true );
			setIsInProgress( false );
		} );
	};

	/**
	 * Migrates the post content.
	 */
	const migratePostContent = async () => {
		// Used for a 504 Gateway Timeout Error, but could also be for other errors.
		const timeoutErrorCode = 'invalid_json';

		await apiFetch( {
			path: '/block-lab/migrate-post-content',
			method: 'POST',
		} ).then( () => {
			speak( __( 'The migration was successful!', 'block-lab' ) );
			setIsSuccess( true );
		} ).catch( async ( result ) => {
			if ( result.hasOwnProperty( 'code' ) && timeoutErrorCode === result.code ) {
				await migratePostContent();
				return;
			} else if ( result.hasOwnProperty( 'message' ) ) {
				setErrorMessage( result.message );
			}

			speak( __( 'The migration failed in the post content migration', 'block-lab' ) );
			setIsError( true );
		} );
	};

	/**
	 * Handles all of the migration for this step.
	 */
	const migrate = async () => {
		speak( __( 'The migration is now in progress', 'block-lab' ) );
		setErrorMessage( '' );
		setIsInProgress( true );

		// The post content migration is chained to the callback in then().
		await migrateCpt();

		setIsInProgress( false );
	};

	return (
		<Step isActive={ isStepActive } isComplete={ isStepComplete }>
			<StepIcon
				index={ stepIndex }
				isComplete={ isStepComplete }
			/>
			<StepContent
				heading={ __( 'Migrate Your Blocks', 'block-lab' ) }
				isStepActive={ isStepActive }
			>
				{ ! isSuccess && <p>{ __( "Okay! Everything is ready. Let's do this. While the migration is underway, don't leave this page.", 'block-lab' ) }</p> }
				{ !! errorMessage && (
					<div className="bl-migration__error">
						<p>{ __( 'The following error ocurred:', 'block-lab' ) }</p>
						<p>{ errorMessage }</p>
					</div>
				) }
				{ isInProgress && (
					<>
						<Spinner />
						<p>{ migrationLabels[ currentBlockMigrationStep ] }</p>
					</>
				) }
				{ ! isInProgress && ! isSuccess && (
					<button
						className="btn"
						onClick={ migrate }
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
