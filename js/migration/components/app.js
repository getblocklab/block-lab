/* global blockLabMigration */
// @ts-check

/**
 * WordPress dependencies
 */
import { useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { Intro } from './';
import { ActivateGcb, BackupSite, GetGenesisPro, MigrateBlocks, UpdateHooks } from './steps';
import { FIRST_STEP_NUMBER } from '../constants';

const App = () => {
	const [ currentStepIndex, updateStepIndex ] = useState( FIRST_STEP_NUMBER );

	/**
	 * Sets the step index to the previous step.
	 */
	const goToPrevious = () => {
		updateStepIndex( currentStepIndex - 1 );
	};

	/**
	 * Sets the step index to the next step.
	 */
	const goToNext = () => {
		updateStepIndex( currentStepIndex + 1 );
	};

	const steps = [
		BackupSite,
		UpdateHooks,
		MigrateBlocks,
		ActivateGcb,
	];

	// Conditionally add the step to get Genesis Pro.
	if ( blockLabMigration.isPro ) {
		steps.unshift( GetGenesisPro );
	}

	return (
		<div className="bl-migration__content-wrapper">
			<div className="container bl-migration__content-container">
				<Intro />
				{
					steps.map( ( MigrationStep, index ) => {
						const stepNumber = FIRST_STEP_NUMBER + index;

						return (
							<MigrationStep
								key={ `bl-migration-step-${ stepNumber }` }
								stepIndex={ stepNumber }
								{ ...{ currentStepIndex, goToPrevious, goToNext } }
							/>
						);
					} )
				}
			</div>
		</div>
	);
};

export default App;
