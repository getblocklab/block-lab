/**
 * WordPress dependencies
 */
import { useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { Intro } from './';
import { BackupSite, UpdateHooks } from './steps';

const App = () => {
	const initialStepNumber = 1;
	const [ currentStepIndex, updateStepIndex ] = useState( initialStepNumber );

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
	];

	return (
		<div className="bl-migration__content-wrapper">
			<div className="container bl-migration__content-container">
				<Intro />
				{
					steps.map( ( MigrationStep, index ) => {
						const stepNumber = initialStepNumber + index;
						return <MigrationStep key={ stepNumber } stepIndex={ stepNumber } { ...{ currentStepIndex, goToPrevious, goToNext } } />;
					} )
				}
			</div>
		</div>
	);
};

export default App;
