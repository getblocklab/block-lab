// @ts-check
/* global blockLabMigration */

/**
 * External dependencies
 */
import * as React from 'react';

/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';
import { Spinner } from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { ButtonNext, Step, StepContent, StepFooter, StepIcon } from '../';

/**
 * @typedef {Object} GetGenesisProProps The component props.
 * @property {React.EventHandler<React.MouseEvent<HTMLButtonElement, MouseEvent>>} goToNext Goes to the next step.
 * @property {boolean} isStepActive Whether this step is active.
 * @property {boolean} isStepComplete Whether this step is complete.
 * @property {number} stepIndex The step index of this step.
 */

/**
 * The step to get Genesis Pro.
 *
 * @param {GetGenesisProProps} Props The component props.
 * @return {React.ReactElement} The component to get Genesis Pro.
 */
const GetGenesisPro = ( { goToNext, isStepActive, isStepComplete, stepIndex } ) => {
	// @ts-ignore
	const genesisProKey = blockLabMigration.genesisProKey;
	const [ isSubmittingKey, setIsSubmittingKey ] = useState( false );
	const [ keySubmittedSuccessfully, setKeySubmittedSuccessfully ] = useState( false );
	const [ subscriptionKey, updateSubscriptionKey ] = useState( !! genesisProKey ? genesisProKey : '' );
	const [ submissionMessage, setSubmissionMessage ] = useState( '' );

	const urlMigrateWithoutGenPro = 'https://getblocklab.com/migrating-to-genesis-custom-blocks/';
	const urlOptInGenesisPro = 'https://forms.gle/26u7NDRUp2A9i2aF8';
	const shouldAllowNextStep = !! genesisProKey || keySubmittedSuccessfully;

	/**
	 * The handler for changing the subscription key.
	 *
	 * @param {React.ChangeEvent<HTMLInputElement>} event The change event.
	 */
	const onChangeSubscriptionKey = ( event ) => {
		updateSubscriptionKey( event.target.value );
	};

	/**
	 * Submits the subscription key to the endpoint.
	 */
	const submitSubscriptionKey = async () => {
		setIsSubmittingKey( true );

		await apiFetch( {
			path: '/block-lab/update-subscription-key',
			method: 'POST',
			data: { subscriptionKey },
		} ).then( () => {
			setSubmissionMessage( __( 'Thanks! Your key is valid, and has been saved.', 'block-lab' ) );
			setKeySubmittedSuccessfully( true );
		} ).catch( ( error ) => {
			const errorMessage = error.message ? error.message : __( 'There was an error validating the key.', 'block-lab' );
			setSubmissionMessage( errorMessage );
			setKeySubmittedSuccessfully( false );
			updateSubscriptionKey( '' );
		} );

		setIsSubmittingKey( false );
	};

	return (
		<Step isActive={ isStepActive } isComplete={ isStepComplete }>
			<StepIcon
				index={ stepIndex }
				isComplete={ isStepComplete }
			/>
			<StepContent
				heading={ __( 'Get Genesis Pro', 'block-lab' ) }
				isStepActive={ isStepActive }
			>
				<p></p>
				<div className="pro-box">
					<h3>{ __( 'Migrating from Block Lab Pro', 'block-lab' ) }</h3>
					<p>{ __( "It looks like you’re a Block Lab Pro customer! Thank you so much for your support. We wouldn't be here without you! Rest assured, your Block Lab Pro license will continue to receive security updates and support for the duration of its term.", 'block-lab' ) }*</p>
					<div className="pro-box-tiles">
						<div className="pro-box-tile">
							<div className="pro-box-tile__icon">
								<svg
									width="100%"
									height="100%"
									viewBox="0 0 91 75"
									version="1.1"
									xmlns="http://www.w3.org/2000/svg"
									xmlnsXlink="http://www.w3.org/1999/xlink"
									xmlSpace="preserve"
									style={ {
										fillRule: 'evenodd',
										clipRule: 'evenodd',
										strokeLinejoin: 'round',
										strokeMiterlimit: 2,
									} }
								>
									<g id="bl_genesis_icon">
										<path d="M43.31,39.843c0.288,0.81 0.687,1.495 1.196,2.053c0.508,0.558 1.111,0.984 1.809,1.276c0.698,0.293 1.46,0.439 2.288,0.439c0.631,0 1.189,-0.055 1.675,-0.163c0.487,-0.108 0.945,-0.252 1.377,-0.432l0,-2.984l-1.944,0c-0.288,0 -0.513,-0.076 -0.675,-0.229c-0.162,-0.153 -0.243,-0.346 -0.243,-0.581l0,-2.512l6.994,0l0,8.306c-0.504,0.369 -1.028,0.686 -1.572,0.95c-0.545,0.267 -1.126,0.485 -1.742,0.656c-0.617,0.171 -1.275,0.297 -1.972,0.379c-0.698,0.081 -1.447,0.12 -2.249,0.12c-1.44,0 -2.772,-0.253 -3.997,-0.762c-1.224,-0.509 -2.284,-1.211 -3.179,-2.107c-0.896,-0.895 -1.599,-1.958 -2.107,-3.186c-0.509,-1.229 -0.763,-2.564 -0.763,-4.005c0,-1.466 0.243,-2.816 0.729,-4.045c0.486,-1.228 1.182,-2.288 2.087,-3.179c0.904,-0.892 1.998,-1.585 3.281,-2.079c1.283,-0.496 2.716,-0.744 4.3,-0.744c0.82,0 1.589,0.069 2.31,0.203c0.72,0.135 1.384,0.32 1.992,0.554c0.608,0.234 1.163,0.513 1.668,0.837c0.503,0.324 0.953,0.675 1.35,1.052l-1.324,2.013c-0.126,0.189 -0.276,0.338 -0.452,0.446c-0.175,0.108 -0.367,0.162 -0.574,0.162c-0.27,0 -0.549,-0.09 -0.837,-0.27c-0.36,-0.216 -0.7,-0.403 -1.019,-0.561c-0.32,-0.158 -0.647,-0.285 -0.98,-0.384c-0.333,-0.099 -0.684,-0.171 -1.053,-0.216c-0.369,-0.046 -0.783,-0.068 -1.243,-0.068c-0.855,0 -1.625,0.151 -2.308,0.453c-0.685,0.3 -1.267,0.727 -1.749,1.275c-0.483,0.55 -0.853,1.209 -1.115,1.978c-0.261,0.77 -0.391,1.628 -0.391,2.573c0,1.045 0.144,1.972 0.432,2.782Zm42.005,0.944c-0.658,1.855 -1.461,3.536 -2.437,4.951c0.979,-3.651 1.411,-7.51 1.208,-11.481c-1.438,-19.116 -17.393,-34.183 -36.878,-34.183c-12.247,0 -23.093,5.958 -29.826,15.127c-0.386,0.552 -0.769,1.11 -1.129,1.69c-2.23,3.589 -3.761,7.411 -4.65,11.311c-0.128,-3.334 0.369,-6.946 1.446,-10.724c-3.39,0.467 -6.246,1.301 -8.413,2.492c0.001,0 0.002,0.001 0.003,0.001c-1.918,1.056 -3.3,2.391 -4.03,4.003c-0.002,0.005 -0.005,0.01 -0.008,0.015c-0.103,0.231 -0.194,0.468 -0.271,0.71c-0.092,0.292 -0.163,0.59 -0.214,0.89c-0.015,0.089 -0.017,0.182 -0.029,0.272c-0.027,0.214 -0.055,0.427 -0.063,0.646c-0.002,0.102 0.007,0.208 0.008,0.311c0.001,0.21 0.001,0.419 0.021,0.632c0.01,0.104 0.032,0.209 0.045,0.313c0.029,0.217 0.057,0.434 0.103,0.655c0.02,0.099 0.053,0.201 0.077,0.301c0.056,0.228 0.113,0.457 0.187,0.688c0.03,0.093 0.069,0.188 0.102,0.281c0.085,0.242 0.173,0.485 0.277,0.73c0.036,0.084 0.08,0.17 0.118,0.254c0.117,0.258 0.237,0.515 0.375,0.775c0.039,0.074 0.084,0.15 0.125,0.224c0.149,0.273 0.306,0.547 0.478,0.821c0.04,0.064 0.084,0.128 0.124,0.191c0.186,0.289 0.379,0.578 0.588,0.87c0.038,0.052 0.079,0.105 0.117,0.157c0.223,0.305 0.455,0.609 0.702,0.914c0.034,0.042 0.069,0.082 0.103,0.124c0.26,0.318 0.532,0.637 0.82,0.957c0.027,0.03 0.056,0.061 0.083,0.091c0.301,0.332 0.613,0.664 0.941,0.996c0.02,0.021 0.04,0.041 0.06,0.06c0.339,0.344 0.692,0.687 1.061,1.031c0.012,0.011 0.025,0.023 0.037,0.034c0.378,0.352 0.77,0.704 1.178,1.057c0.004,0.005 0.01,0.008 0.016,0.014c0.414,0.358 0.843,0.715 1.286,1.073c0.001,0.001 0.001,0.002 0.002,0.003c0.447,0.359 0.909,0.717 1.384,1.076c7.15,5.394 17.488,10.59 29.489,14.386c2.195,0.695 4.369,1.315 6.515,1.87c-13.418,-1.826 -24.644,-4.124 -34.98,-10.802c4.203,15.814 18.606,27.468 35.742,27.468c12.033,0 22.719,-5.747 29.475,-14.642c6.999,-0.911 11.792,-3.35 13.03,-7.263c1.059,-3.348 -0.634,-7.309 -4.398,-11.37Z"></path>
									</g>
								</svg>
							</div>
							<h4>{ __( '12 months free', 'block-lab' ) }</h4>
							<p>{ __( 'As part of the migration to Genesis Custom Blocks, we’d like to set you up with a free year of Genesis Pro. This new Genesis subscription will give you access to all the features you’ve loved in Block Lab Pro.', 'block-lab' ) }</p>
						</div>
						<div className="pro-box-tile">
							<div className="pro-box-tile__icon">
								<svg
									width="100%"
									height="100%"
									viewBox="0 0 101 50"
									version="1.1"
									xmlns="http://www.w3.org/2000/svg"
									xmlnsXlink="http://www.w3.org/1999/xlink"
									xmlSpace="preserve"
									style={ {
										fillRule: 'evenodd',
										clipRule: 'evenodd',
										strokeLinejoin: 'round',
										strokeMiterlimit: 2,
									} }
								>
									<g id="bl_infinity_icon">
										<path d="M50.017,16.489l8.579,-8.58c9.47,-9.47 24.848,-9.47 34.318,0c9.47,9.47 9.47,24.848 0,34.318c-9.47,9.47 -24.848,9.47 -34.318,0l-8.579,-8.58l-8.58,8.58c-9.47,9.47 -24.847,9.47 -34.318,0c-9.47,-9.47 -9.47,-24.848 0,-34.318c9.471,-9.47 24.848,-9.47 34.318,0l8.58,8.58Zm-17.159,0l8.579,8.579l-8.579,8.579c-4.735,4.736 -12.424,4.736 -17.159,0c-4.735,-4.735 -4.735,-12.423 0,-17.158c4.735,-4.736 12.424,-4.736 17.159,0Zm34.318,17.158l-8.58,-8.579l8.58,-8.579c4.735,-4.736 12.423,-4.736 17.158,0c4.736,4.735 4.736,12.423 0,17.158c-4.735,4.736 -12.423,4.736 -17.158,0Z"></path>
									</g>
								</svg>
							</div>
							<h4>{ __( 'Unlimited Sites', 'block-lab' ) }</h4>
							<p>{ __( 'All Genesis Pro subscriptions are valid on an unlimited number of installs, and come with additional access to the Genesis Framework, Genesis Themes, and Genesis Page Builder.', 'block-lab' ) }</p>
						</div>
						<div className="pro-box-tile">
							<div className="pro-box-tile__icon">
								<svg
									width="100%"
									height="100%"
									viewBox="0 0 101 52"
									version="1.1"
									xmlns="http://www.w3.org/2000/svg"
									xmlnsXlink="http://www.w3.org/1999/xlink"
									xmlSpace="preserve"
									style={ {
										fillRule: 'evenodd',
										clipRule: 'evenodd',
										strokeLinejoin: 'round',
										strokeMiterlimit: 2,
									} }
								>
									<g id="bl_key_icon">
										<path d="M51.602,31.449c-2.477,11.61 -12.8,20.327 -25.143,20.327c-14.188,0 -25.708,-11.519 -25.708,-25.708c0,-14.189 11.52,-25.708 25.708,-25.708c11.678,0 21.547,7.802 24.675,18.474l49.617,0l0,12.615l-8.995,0l0,15.615l-12.616,0l0,-15.615l-27.538,0Zm-25.143,-15.898c5.805,0 10.517,4.713 10.517,10.517c0,5.804 -4.712,10.517 -10.517,10.517c-5.804,0 -10.517,-4.713 -10.517,-10.517c0,-5.804 4.713,-10.517 10.517,-10.517Z"></path>
									</g>
								</svg>
							</div>
							<h4>{ __( 'New Subscription Key', 'block-lab' ) }</h4>
							<p>{ __( 'To migrate and maintain your Block Lab Pro feature set, you will need a Genesis Pro subscription key. Step number 1 below will walk you through setting up your account.', 'block-lab' ) }</p>
						</div>
					</div>
					<p>{ __( '* Block Lab Pro licenses will not be renewing and Pro updates / support will end when your current license expires.', 'block-lab' ) }</p>
				</div>
				<p>{ __( "Since you're a Block Lab Pro customer, we've already emailed you regarding setting up a WP Engine account with a free Pro subscription.", 'block-lab' ) }</p>
				<p>{ __( 'To migrate and maintain your Block Lab Pro feature set with Genesis Custom Blocks, you will need your Genesis Pro subscription key.', 'block-lab' ) }</p>
				<ul>
					<li>{ __( 'Already have got it? Enter the subscription key below to continue migrating.', 'block-lab' ) }</li>
					<li>{ __( 'Don’t have one yet? Please opt-in using the link below.', 'block-lab' ) }</li>
				</ul>
				{ ! keySubmittedSuccessfully && (
					<>
						<div className="get-genesis-pro">
							<a
								href={ urlOptInGenesisPro }
								className="btn"
								target="_blank"
								rel="noopener noreferrer"
							>
								{ __( 'Opt-in for Genesis Pro', 'block-lab' ) }
							</a>
							<span>{ __( '(This may take up to 3 working days)', 'block-lab' ) }</span>
						</div>
						<p>{ __( 'then', 'block-lab' ) }</p>
						<div className="genesis-pro-form">
							<input
								type="text"
								placeholder={ __( 'Paste your Genesis Pro subscription key', 'block-lab' ) }
								value={ subscriptionKey }
								onChange={ onChangeSubscriptionKey }
							/>
							<button
								onClick={ submitSubscriptionKey }
								disabled={ isSubmittingKey }
							>
								{ __( 'Save', 'block-lab' ) }
							</button>
							{ isSubmittingKey && <Spinner /> }
						</div>
					</>
				) }
				<p className="pro-submission-message">{ submissionMessage }</p>
				{ ! keySubmittedSuccessfully && (
					<p className="help-text">
						{ __( 'Want to migrate but not set up Genesis Pro just now?', 'block-lab' ) }
						&nbsp;
						<a
							href={ urlMigrateWithoutGenPro }
							target="_blank"
							rel="noopener noreferrer"
							aria-label={ __( 'More information about migrating but not setting up Genesis Pro', 'genesis-custom-blocks' ) }
						>
							{ __( 'Read here for what that means.', 'block-lab' ) }
						</a>
					</p>
				) }
				<StepFooter>
					<ButtonNext
						checkboxLabel={ shouldAllowNextStep ? null : __( 'Migrate without Genesis Pro.', 'block-lab' ) }
						onClick={ goToNext }
						stepIndex={ stepIndex }
					/>
				</StepFooter>
			</StepContent>
		</Step>
	);
};

export default GetGenesisPro;
