var el = wp.element.createElement,
	registerBlockType = wp.blocks.registerBlockType,
	blockStyle = { backgroundColor: '#900', color: '#fff', padding: '20px' };

registerBlockType('gutenberg-exercise-block/exercise-list', {
	title: 'Exercise List',

	icon: 'universal-access-alt',

	category: 'layout',

	edit: function () {
		var options = exerciseNames.map(function (name, index) {
			return wp.element.createElement(
				'option',
				{ key: index, value: name },
				name
			);
		});
	
		return wp.element.createElement(
			'div',
			null,
			wp.element.createElement(
				'p',
				null,
				'Select Exercise:'
			),
			wp.element.createElement(
				'select',
				{
					onChange: function (event) {
						// Handle the change event here
					}
				},
				options
			)
		);
	},

	save: function () {
		// No dynamic content to save
		return null;
	},
});