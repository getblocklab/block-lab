const { data, apiFetch } = wp;
const { registerStore } = data;

const DEFAULT_STATE = {
	notices: {},
};

const actions = {
	setStatus( notice, status ) {
		return {
			type: 'SET_STATUS',
			notice,
			status
		};
	},
	fetchFromAPI( notice ) {
		return {
			type: 'FETCH_FROM_API',
			notice,
		};
	},
};

registerStore( 'block-lab', {
	reducer( state = DEFAULT_STATE, action ) {
		switch ( action.type ) {
			case 'SET_STATUS':
				apiFetch({
					path: '/block-lab/v1/notices/' + action.notice,
					method: 'PUT',
					data: { 'status': action.status }
				});
				return {
					...state,
					notices: {
						...state.notices,
						[ action.notice ]: action.status,
					},
				};
		}

		return state;
	},

	actions,

	selectors: {
		getNotice( state, notice ) {
			const { notices } = state;
			return notices[ notice ];
		},
	},

	controls: {
		FETCH_FROM_API( action ) {
			return apiFetch( { path: '/block-lab/v1/notices/' + action.notice } );
		},
	},

	resolvers: {
		* getNotice( notice ) {
			const status = yield actions.fetchFromAPI( notice );
			return actions.setStatus( notice, status );
		},
	},
} );
