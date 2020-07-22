/* global blockLabMigration */
// @ts-check

/**
 * External dependencies
 */
import * as React from 'react';

/**
 * WordPress dependencies
 */
import { useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { Intro } from './';
import { BackUpSite, GetGenesisPro, InstallActivateGcb, MigrateBlocks, UpdateHooks } from './steps';
import { FIRST_STEP_NUMBER } from '../constants';

/**
 * The migration admin page.
 *
 * @return {React.ReactElement} The component for the admin page.
 */
const App = () => {
	const [ currentStepIndex, setStepIndex ] = useState( FIRST_STEP_NUMBER );

	/**
	 * Sets the step index to the previous step.
	 */
	const goToPrevious = () => {
		setStepIndex( currentStepIndex - 1 );
	};

	/**
	 * Sets the step index to the next step.
	 */
	const goToNext = () => {
		setStepIndex( currentStepIndex + 1 );
	};

	const steps = [
		BackUpSite,
		UpdateHooks,
		InstallActivateGcb,
		MigrateBlocks,
	];

	// Conditionally add the step to get Genesis Pro.
	// @ts-ignore
	if ( blockLabMigration.isPro ) {
		steps.unshift( GetGenesisPro );
	}

	return (
		<div className="bl-migration__content-wrapper">
			<div className="container bl-migration__content-container">
				<Intro />
				{
					steps.map( ( MigrationStep, index ) => {
						const stepIndex = FIRST_STEP_NUMBER + index;
						const isStepActive = currentStepIndex === stepIndex;
						const isStepComplete = currentStepIndex > stepIndex;

						return (
							<MigrationStep
								key={ `bl-migration-step-${ stepIndex }` }
								{ ...{ currentStepIndex, goToNext, goToPrevious, isStepActive, isStepComplete, stepIndex } }
							/>
						);
					} )
				}
			</div>
		</div>
	);
};

export default App;
