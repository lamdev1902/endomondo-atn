jQuery(function ($) {
	jQuery(document).on('click', 'a.add-exercise-image', function (e) {
		openQuizMediaUploader(e, jQuery(this));
	});

	jQuery(document).on('click', 'span.remove-exercise-img', function (e) {
		var container = $(this)
			.closest('.field')
			.find('.exercise-image-container');
		container.fadeOut();
		container.find('img.exercise-img').attr('src', '');
		container.find('input.exercise-image').val('');
		$(this).closest('.field').find('a.add-exercise-image').text('Add Image');
	});

	$(document).on('change', 'input.video-section', function (e) {
		var container = $(this)
			.closest('.field')
			.find('.exercise-video-container');
		var videoUrl = $(this).val();
		var iframeSrc = '';
		var videoSrc = '';
		var isYouTube = false;
		var isVimeo = false;
		var check = true;

		if (videoUrl) {
			var youtubeMatch = videoUrl.match(
				/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/
			);

			if (youtubeMatch) {
				var videoId = youtubeMatch[1];
				iframeSrc = 'https://www.youtube.com/embed/' + videoId;
				isYouTube = true;
			}

			var vimeoMatchIframe = videoUrl.includes('<iframe') && videoUrl.includes('</iframe>');

			if (isYouTube) {
				var iframe = $(this).closest('.field-item').find('iframe');
				iframe.attr('src', iframeSrc).show();
				$(this).attr('value', iframeSrc);
				$(this).val(iframeSrc);
			} else if (vimeoMatchIframe) {
				var srcMatch = videoUrl.match(/src="([^"]+)"/);
				if (srcMatch) {
					var iframeSrc = srcMatch[1];
					if (iframeSrc) {
						var iframe = $(this).closest('.field-item').find('iframe');
						iframe.attr('src', iframeSrc).show();
						$(this).attr('value', iframeSrc);
						$(this).val(iframeSrc);
					}
				}
			} else if (isValidURL(videoUrl)) {
				var iframe = $(this).closest('.field-item').find('iframe');
				iframe.attr('src', videoUrl).show();
				$(this).attr('value', videoUrl);
				$(this).val(videoUrl);
			} else {
				check = false;
				$(this).attr('value', '');
				$(this).val('');
			}
		} else {
			check = false;
			$(this).attr('value', '');
			$(this).val('');
		}

		check ? container.fadeIn() : container.fadeOut();
	});

	jQuery(document).ready(function ($) {
		$('.muscle-button').on('click', function (e) {
			e.preventDefault();

			validateInput();

			var link = $(this).data('link');
			var formData = $(this)
				.closest('.exercice-form-section')
				.find('form');
			formData = formData.serializeArray();

			var jsonData = {};
			$.each(formData, function (i, field) {
				var parts = field.name.split('[');
				var currentObj = jsonData;

				for (var j = 0; j < parts.length; j++) {
					var key = parts[j].replace(']', '');

					if (j === parts.length - 1) {
						currentObj[key] = field.value || '';
					} else {
						currentObj[key] = currentObj[key] || {};
						currentObj = currentObj[key];
					}
				}
			});

			var editorValue = tinymce.get('description')
				? tinymce.get('description').getContent()
				: $('textarea#description').val();

			if (!jsonData.muscle) {
				jsonData.muscle = {};
			}
			jsonData.muscle.description = editorValue || '';

			jsonData.link = link;
			if ($('.error').length == 0) {
				$('#overlay').fadeIn(300);
				$.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {
						action: 'handle_muscle_data',
						data: jsonData,
						// Add other fields accordingly
					},
					success: function (response) {
						try {
							setTimeout(function () {
								$('#overlay').fadeOut(300);
							}, 500);
							var cleanedResponse = response.replace(/0+$/, '');

							var result = JSON.parse(cleanedResponse);
							if (result.redirect_url) {
								window.location.href = result.redirect_url;
							} else {
								alert('An error occurred.');
							}
						} catch (e) {
							console.error('Parsing error:', e);
							alert('An error occurred.');
						}
					},
					error: function (error) {
						alert('faile');
					},
				});
			}
		});

		$('.equipment-button').on('click', function (e) {
			e.preventDefault();

			validateInput();

			var link = $(this).data('link');
			var formData = $(this)
				.closest('.exercice-form-section')
				.find('form');
			formData = formData.serializeArray();

			var jsonData = {};
			$.each(formData, function (i, field) {
				var parts = field.name.split('[');
				var currentObj = jsonData;

				for (var j = 0; j < parts.length; j++) {
					var key = parts[j].replace(']', '');

					if (j === parts.length - 1) {
						currentObj[key] = field.value || '';
					} else {
						currentObj[key] = currentObj[key] || {};
						currentObj = currentObj[key];
					}
				}
			});

			jsonData.link = link;
			if ($('.error').length == 0) {
				$('#overlay').fadeIn(300);
				$.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {
						action: 'handle_equipment_data',
						data: jsonData,
						// Add other fields accordingly
					},
					success: function (response) {
						try {
							setTimeout(function () {
								$('#overlay').fadeOut(300);
							}, 500);
							var cleanedResponse = response.replace(/0+$/, '');

							var result = JSON.parse(cleanedResponse);
							if (result.redirect_url) {
								window.location.href = result.redirect_url;
							} else {
								alert('An error occurred.');
							}
						} catch (e) {
							console.error('Parsing error:', e);
							alert('An error occurred.');
						}
					},
					error: function (error) {
						alert('faile');
					},
				});
			}
		});

		$('.exercise-button').on('click', function (e) {
			e.preventDefault();

			validateInput();

			var link = $(this).data('link');
			var formData = $(this)
				.closest('.exercice-form-section')
				.find('form');
			formData = formData.serializeArray();

			var jsonData = {};

			$.each(formData, function (i, field) {
				var parts = field.name.split('[');
				var currentObj = jsonData;

				for (var j = 0; j < parts.length; j++) {
					var key = parts[j].replace(']', '');

					if (j === parts.length - 1) {
						if (Array.isArray(currentObj[key])) {
							currentObj[key].push(field.value || '');
						} else if (currentObj[key] !== undefined) {
							currentObj[key] = [
								currentObj[key],
								field.value || '',
							];
						} else {
							currentObj[key] = field.value || '';
						}
					} else {
						currentObj[key] = currentObj[key] || {};
						currentObj = currentObj[key];
					}
				}
			});

			var editorValue = tinymce.get('excDescription')
				? tinymce.get('excDescription').getContent()
				: $('textarea#excDescription').val();

			if (!jsonData.exercise) {
				jsonData.exercise = {};
			}

			jsonData.exercise.description = editorValue || '';
			jsonData.link = link;

			$('.wp-editor-area').each(function () {
				var editorId = $(this).attr('id');
				var editorContent = tinyMCE.get(editorId)
					? tinyMCE.get(editorId).getContent()
					: $(this).val();

				var parts = $(this).attr('name').split('[');
				var currentObj = jsonData;

				for (var j = 0; j < parts.length; j++) {
					var key = parts[j].replace(']', '');

					if (j === parts.length - 1) {
						currentObj[key] = editorContent;
					} else {
						currentObj[key] = currentObj[key] || {};
						currentObj = currentObj[key];
					}
				}
			});

			if ($('.error').length == 0) {
				$('#overlay').fadeIn(300);
				$.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {
						action: 'handle_exercise_data',
						data: jsonData,
					},
					success: function (response) {
						try {
							setTimeout(function () {
								$('#overlay').fadeOut(300);
							}, 500);

							var cleanedResponse = response.replace(/0+$/, '');

							var result = JSON.parse(cleanedResponse);
							if (result.redirect_url) {
								window.location.href = result.redirect_url;
							} else {
								alert('An error occurred.');
							}
						} catch (e) {
							console.error('Parsing error:', e);
							alert('An error occurred.');
						}
					},
					error: function (error) {
						alert('faile');
					},
				});
			}
		});

		$('.plan-button').on('click', function (e) {
			e.preventDefault();

			validateInput();

			var link = $(this).data('link');
			var formData = $(this)
				.closest('.exercice-form-section')
				.find('form');
			formData = formData.serializeArray();

			var jsonData = {};

			$.each(formData, function (i, field) {
				var parts = field.name.split('[');
				var currentObj = jsonData;

				for (var j = 0; j < parts.length; j++) {
					var key = parts[j].replace(']', '');

					if (j === parts.length - 1) {
						if (Array.isArray(currentObj[key])) {
							currentObj[key].push(field.value || '');
						} else if (currentObj[key] !== undefined) {
							currentObj[key] = [
								currentObj[key],
								field.value || '',
							];
						} else {
							currentObj[key] = field.value || '';
						}
					} else {
						currentObj[key] = currentObj[key] || {};
						currentObj = currentObj[key];
					}
				}
			});

			var editorValue = tinymce.get('planDescription')
				? tinymce.get('planDescription').getContent()
				: $('textarea#planDescription').val();

			if (!jsonData.plan) {
				jsonData.plan = {};
			}

			jsonData.plan.description = editorValue || '';
			jsonData.link = link;

			$('.wp-editor-area').each(function () {
				var editorId = $(this).attr('id');
				var editorContent = tinyMCE.get(editorId)
					? tinyMCE.get(editorId).getContent()
					: $(this).val();

				var parts = $(this).attr('name').split('[');
				var currentObj = jsonData;

				for (var j = 0; j < parts.length; j++) {
					var key = parts[j].replace(']', '');

					if (j === parts.length - 1) {
						currentObj[key] = editorContent;
					} else {
						currentObj[key] = currentObj[key] || {};
						currentObj = currentObj[key];
					}
				}
			});

			// Sắp xếp lại các checkbox theo thứ tự người dùng đã chọn cho từng tuần
			if (!jsonData.plan) {
				jsonData.plan = {};
			}
			if (!jsonData.plan.week) {
				jsonData.plan.week = [];
			}

			$.each(checkboxOrder, function (weekIndex, trainingMethods) {
				if (!jsonData.plan.week[weekIndex]) {
					jsonData.plan.week[weekIndex] = {};
				}
				jsonData.plan.week[weekIndex].training = trainingMethods;
			});

			if ($('.error').length == 0) {
				$('#overlay').fadeIn(300);
				$.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {
						action: 'handle_plan_data',
						data: jsonData,
					},
					success: function (response) {
						try {
							setTimeout(function () {
								$('#overlay').fadeOut(300);
							}, 500);

							var cleanedResponse = response.replace(/0+$/, '');

							var result = JSON.parse(cleanedResponse);
							if (result.redirect_url) {
								window.location.href = result.redirect_url;
							} else {
								alert('An error occurred.');
							}
						} catch (e) {
							console.error('Parsing error:', e);
							alert('An error occurred.');
						}
					},
					error: function (error) {
						alert('Failed');
					},
				});
			}
		});

		var checkboxOrder = {};

		$(document).on('change', '.ckkBox.val', function () {
			var weekIndex = $(this).closest('.item').find('input[type="hidden"]').attr('name').match(/plan\[week\]\[(\d+)\]/)[1];
			var value = $(this).val();

			if (!checkboxOrder[weekIndex]) {
				checkboxOrder[weekIndex] = [];
			}

			if (this.checked) {
				if (!checkboxOrder[weekIndex].includes(value)) {
					checkboxOrder[weekIndex].push(value);
				}
			} else {
				checkboxOrder[weekIndex] = checkboxOrder[weekIndex].filter(function (item) {
					return item !== value;
				});
			}
		});

		var isModalPrimary = false;
		var isModalSecondary = false;
		var isModalEquipment = false;

		$(document).on('click', '.add-primary-option', function (e) {
			e.preventDefault();
			var id = $(this).data('id') ? $(this).data('id') : '';
			$('#exercise-primary-modal').modal('open');

			if (isModalPrimary) {
				return;
			}

			isModalPrimary = true;
			$.ajax({
				url: ajaxurl,
				method: 'POST',
				data: {
					action: 'get_muscle_anatomy',
					exercise_id: id,
					type: 'primary',
				},
				success: function (response) {
					if (response) {
						$('.primary-modal').html(response);
						checkRadioInput('primay');
					} else {
						console.error(
							'Không có dữ liệu trả về từ yêu cầu AJAX.'
						);
					}
				},
				error: function (xhr, status, error) {
					console.error('Lỗi khi gửi yêu cầu AJAX: ' + error);
				},
			});
		});

		$(document).on('change', '.search-option', function (e) {
			e.preventDefault();

			var id = $(this).data('id') ? $(this).data('id') : '';

			var type = $(this).data('type') ? $(this).data('type') : '';

			var value = $(this).val();

			var classModel = '.' + type + '-modal';

			var action = type == "equipment" ? "get_equipment_data" : "get_muscle_anatomy";
			$.ajax({
				url: ajaxurl,
				method: 'POST',
				data: {
					action: action,
					exercise_id: id,
					type: type,
					search: value
				},
				success: function (response) {
					if (response) {
						$(classModel).html(response);
					} else {
						console.error(
							'Không có dữ liệu trả về từ yêu cầu AJAX.'
						);
					}
				},
				error: function (xhr, status, error) {
					console.error('Lỗi khi gửi yêu cầu AJAX: ' + error);
				},
			});
		});

		$(document).on('click', '.add-secondary-option', function (e) {
			e.preventDefault();

			var id = $(this).data('id') ? $(this).data('id') : '';
			$('#exercise-secondary-modal').modal('open');

			if (isModalSecondary) {
				return;
			}

			isModalSecondary = true;

			$.ajax({
				url: ajaxurl,
				method: 'POST',
				data: {
					action: 'get_muscle_anatomy',
					exercise_id: id,
					type: 'secondary',
				},
				success: function (response) {
					if (response) {
						$('.secondary-modal').html(response);
						checkRadioInput('secondary');
					} else {
						console.error(
							'Không có dữ liệu trả về từ yêu cầu AJAX.'
						);
					}
				},
				error: function (xhr, status, error) {
					console.error('Lỗi khi gửi yêu cầu AJAX: ' + error);
				},
			});
		});

		var $button = null;
		$(document).on('click', '.section-btn', function (e) {
			e.preventDefault();

			$button = $(this); 

			var id = [];

			var exercise = $(this).data('exercise');
			var duration = $(this).data('duration');
			var reps = $(this).data('reps');
			var note = $(this).data('note');
			var section_id = $(this).data('section-id') ? $(this).data('section-id') : 0;

			let schedule = {};

			if (typeof exercise === 'string') {
				if (exercise.includes(',')) {
					id = exercise.split(',');
				} else {
					id = [exercise];
				}
			} else if (exercise) {
				id = [exercise];
			}

			if (typeof duration === 'string') {
				if (duration.includes(',')) {
					schedule['duration'] = duration.split(',');
				} else {
					schedule['duration'] = [duration];
				}
			} else if (duration !== undefined && duration !== null) {
				schedule['duration'] = [duration];
			} else {
				schedule['duration'] = [];
			}

			if (typeof reps === 'string') {
				if (reps.includes(',')) {
					schedule['reps'] = reps.split(',');
				} else {
					schedule['reps'] = [reps];
				}
			} else if (reps !== undefined && reps !== null) {
				schedule['reps'] = [reps];
			} else {
				schedule['reps'] = [];
			}

			if (typeof note === 'string') {
				if (note.includes(',')) {
					schedule['note'] = note.split(',');
				} else {
					schedule['note'] = [note];
				}
			} else if (note !== undefined && note !== null) {
				schedule['note'] = [note];
			} else {
				schedule['note'] = [];
			}

			var type = 'plan';

			$('#exercise-section-modal').modal('open');

			$('.section-modal').html('');

			$.ajax({
				url: ajaxurl,
				method: 'POST',
				data: {
					action: 'get_section_exercise',
					exercise_id: id,
					section_id: section_id,
					schedule: schedule
				},
				success: function (response) {
					if (response) {
						$('.section-modal').html(response);

						validateInputValue($button);

					} else {
						console.error('Không có dữ liệu trả về từ yêu cầu AJAX.');
					}
				},
				error: function (xhr, status, error) {
					console.error('Lỗi khi gửi yêu cầu AJAX: ' + error);
				},
			});
		});


		function validateInputValue($button) {
			$('.section-modal input[type="checkbox"]').off('change').on('change', function () {
				var value = $(this).val();
				var exercises = $button.attr('data-exercise').split(',');

				if (this.checked) {
					if (!exercises.includes(value)) {
						exercises.push(value);
					}
				} else {
					exercises = exercises.filter(function (e) {
						return e != value;
					});
				}

				exercises = exercises.filter(function (e) {
					return e !== "";
				});

				var updatedExercises = exercises.join(',');
				$button.attr('data-exercise', updatedExercises);
				$button.data('exercise', updatedExercises);

				var $hiddenInput = $button.prev('input[type="hidden"]');
				$hiddenInput.val(updatedExercises);
			});

			$('.section-modal .duration').off('change').on('change', function () {
				var $input = $(this);

				var newDuration = $input.val();

				var exerciseId = $input.data('exercise-id') ? $input.data('exercise-id').toString() : '';

				var $sectionItem = $button.closest('.section-item');

				var exercises = $button.data('exercise').split(',');

				var index = exercises.indexOf(exerciseId);

				if (index !== -1) {
					var $durationInput = $sectionItem.find('input[name$="[duration]"]');
					var durations = $durationInput.val().split(',');

					durations[index] = newDuration;

					$durationInput.val(durations.join(','));

					$button.attr('data-duration', durations.join(','));
					$button.data('duration', durations.join(','));
				}
			});

			$('.section-modal .reps').off('change').on('change', function () {
				var $input = $(this);

				var newDuration = $input.val();

				var exerciseId = $input.data('exercise-id') ? $input.data('exercise-id').toString() : '';

				var $sectionItem = $button.closest('.section-item');

				var exercises = $button.data('exercise').split(',');

				var index = exercises.indexOf(exerciseId);

				if (index !== -1) {
					var $durationInput = $sectionItem.find('input[name$="[reps]"]');

					var durations = $durationInput.val().split(',');

					durations[index] = newDuration;

					$durationInput.val(durations.join(','));

					$button.attr('data-reps', durations.join(','));
					$button.data('reps', durations.join(','));
				}
			});

			$('.section-modal .note').off('change').on('change', function () {
				var $input = $(this);

				var newDuration = $input.val();

				var exerciseId = $input.data('exercise-id') ? $input.data('exercise-id').toString() : '';

				var $sectionItem = $button.closest('.section-item');

				var exercises = $button.data('exercise').split(',');

				var index = exercises.indexOf(exerciseId);

				if (index !== -1) {

					var $durationInput = $sectionItem.find('input[name$="[note]"]');

					var durations = $durationInput.val().split(',');

					durations[index] = newDuration;

					$durationInput.val(durations.join(','));

					$button.attr('data-note', durations.join(','));
					$button.data('note', durations.join(','));
				}
			});
		}



		$('.search-option-section').off('change').on('change', function (e) {
			if ($button.length > 0) {
				e.preventDefault();

				var id = [];

				var exercise = $button.data('exercise');
				var duration = $button.data('duration');
				var reps = $button.data('reps');
				var note = $button.data('note');
				var section_id = $button.data('section-id') ? $button.data('section-id') : 0;

				let schedule = {};

				if (typeof exercise === 'string') {
					if (exercise.includes(',')) {
						id = exercise.split(',');
					} else {
						id = [exercise];
					}
				} else if (exercise) {
					id = [exercise];
				}

				if (typeof duration === 'string') {
					if (duration.includes(',')) {
						schedule['duration'] = duration.split(',');
					} else {
						schedule['duration'] = [duration];
					}
				} else if (duration !== undefined && duration !== null) {
					schedule['duration'] = [duration];
				} else {
					schedule['duration'] = [];
				}

				if (typeof reps === 'string') {
					if (reps.includes(',')) {
						schedule['reps'] = reps.split(',');
					} else {
						schedule['reps'] = [reps];
					}
				} else if (reps !== undefined && reps !== null) {
					schedule['reps'] = [reps];
				} else {
					schedule['reps'] = [];
				}

				if (typeof note === 'string') {
					if (note.includes(',')) {
						schedule['note'] = note.split(',');
					} else {
						schedule['note'] = [note];
					}
				} else if (note !== undefined && note !== null) {
					schedule['note'] = [note];
				} else {
					schedule['note'] = [];
				}



				var searchVal = $(this).val();

				$.ajax({
					url: ajaxurl,
					method: 'POST',
					data: {
						action: 'get_section_exercise',
						exercise_id: id,
						section_id: section_id,
						schedule: schedule,
						search: searchVal
					},
					success: function (responseSearch) {
						if (responseSearch) {
							$('.section-modal').html(responseSearch);
							validateInputValue($button)
						}
					},
					error: function (xhr, status, error) {
						console.error('Lỗi khi gửi yêu cầu AJAX: ' + error);
					},
				})
			}

		})

		$(document).on('click', '.add-equipment-option', function (e) {
			e.preventDefault();

			var id = $(this).data('id') ? $(this).data('id') : '';
			$('#exercise-equipment-modal').modal('open');

			if (isModalEquipment) {
				return;
			}

			isModalEquipment = true;
			$.ajax({
				url: ajaxurl,
				method: 'POST',
				data: {
					action: 'get_equipment_data',
					exercise_id: id,
					type: 'equipment',
				},
				success: function (response) {
					if (response) {
						$('.equipment-modal').html(response);
						checkRadioInput('equipment');
					} else {
						console.error(
							'Không có dữ liệu trả về từ yêu cầu AJAX.'
						);
					}
				},
				error: function (xhr, status, error) {
					console.error('Lỗi khi gửi yêu cầu AJAX: ' + error);
				},
			});
		});

		$('.add-muscle-primary').click(function () {
			var selectedData = checkedInput('primary-modal', 'primary');
			if (
				$('.primary .exercise-option-table tbody tr.single-tr')
					.length > 0
			) {
				$(
					'.primary .exercise-option-table tbody tr.single-tr'
				).remove();
			}

			var exerciseId = $('.add-primary-option').data('id');

			selectedData.forEach(function (data) {
				var newRow = `
                    <tr>
                        <td></td>
                        <td>${data.id}</td>
                        <td>${data.name}</td>
                        <td>
                            <input type="hidden" name="exercise[primary][]" value="${data.id}" id="${data.id}">
                            <a href="javascript:void(0)" class="action-item exercise-muscle-edit">Edit</a>
                            <a href="javascript:void(0)" data-type="primary" data-exercise="${exerciseId}" data-id="${data.id}" class="action-item exercise-muscle-delete">Delete</a>
                        </td>
                    </tr>
                `;
				$('.primary .exercise-option-table tbody').append(newRow);
			});

			$('#exercise-primary-modal').find('.close-modal').click();
		});

		$('.add-muscle-secondary').click(function () {
			var selectedData = checkedInput('secondary-modal', 'secondary');

			if (
				$('.secondary .exercise-option-table tbody tr.single-tr')
					.length > 0
			) {
				$(
					'.secondary .exercise-option-table tbody tr.single-tr'
				).remove();
			}

			var exerciseId = $('.add-secondary-option').data('id');

			selectedData.forEach(function (data) {
				var newRow = `
                    <tr>
                        <td></td>
                        <td>${data.id}</td>
                        <td>${data.name}</td>
                        <td>
                            <input type="hidden" name="exercise[secondary][]" value="${data.id}" id="${data.id}">
                            <a href="javascript:void(0)" class="action-item exercise-muscle-edit">Edit</a>
                            <a href="javascript:void(0)" data-type="secondary" data-exercise="${exerciseId}" data-id="${data.id}" class="action-item exercise-muscle-delete">Delete</a>
                        </td>
                    </tr>
                `;
				$('.secondary .exercise-option-table tbody').append(newRow);
			});

			$('#exercise-secondary-modal').find('.close-modal').click();
		});

		$('.add-equipment').click(function () {
			var selectedData = checkedInput('equipment-modal', 'equipment');

			if (
				$('.equipment .exercise-option-table tbody tr.single-tr')
					.length > 0
			) {
				$(
					'.equipment .exercise-option-table tbody tr.single-tr'
				).remove();
			}

			var exerciseId = $('.add-equipment-option').data('id');
			selectedData.forEach(function (data) {
				var newRow = `
                    <tr>
                        <td></td>
                        <td>${data.id}</td>
                        <td>${data.name}</td>
                        <td>
                            <input type="hidden" name="exercise[equipment][]" value="${data.id}" id="${data.id}">
                            <a href="javascript:void(0)" class="action-item exercise-muscle-edit">Edit</a>
                            <a href="javascript:void(0)" data-type="equipment" data-exercise="${exerciseId}" data-id="${data.id}" class="action-item exercise-muscle-delete">Delete</a>
                        </td>
                    </tr>
                `;
				$('.equipment .exercise-option-table tbody').append(newRow);
			});

			$('#exercise-equipment-modal').find('.close-modal').click();
		});

		$(document).on('click', '.exercise-muscle-delete', function () {
			var id = $(this).data('id');
			var exerciceid = $(this).data('exercise');
			var type = $(this).data('type');
			var table = $(this).closest('.exercise-option-table');
			var tableList = $('.' + type + '-modal' + ' #the-list');
			isChecked = tableList.find(
				'input[type="checkbox"]:checked[value="' + id + '"]'
			);
			if (isChecked.length > 0) {
				isChecked.prop('checked', false);
				isChecked.removeClass('selected');
			}

			$(this).closest('tr').remove();

			var row = table.find('tbody tr');

			if (row.length == 0) {
				var newRow = `
                <tr class="single-tr">
                    <td colspan="7"><a href="#exercise-${type}-modal" data-id="${exerciceid}" class="add-${type}-option insert-btn">Insert ${type} option</a></h3></td>
                </tr>
                `;
				table.find('tbody').append(newRow);
			}
		});

		$('.inline-edit').click(function () {
			var $this = $(this);
			$this.hide();
			$this.siblings('.inline-edit-input').show().focus();
		});

		$('.inline-edit-input').blur(function () {
			var $this = $(this);
			var value = $this.val();
			var id = $this.closest('tr').data('id');
			var column = $this.closest('td').data('column');
			var func = $this.data('ajax');

			$this.hide();
			$this.siblings('.inline-edit').text(value).show();

			$.post(
				ajaxurl,
				{
					action: func,
					id: id,
					column: column,
					value: value,
				},
				function (response) {
                    if (response) {
                        if(response.success) {
                            alert("Update success");
                        }else {
							alert("Update error");
						}
                    }else {
						alert("Update error");
					}
                }
			);
		});

		setCheckboxSelectLabels();

		$(document).on('click', '.toggle-next', function () {
			$(this).closest('.item').find(".checkboxes").slideToggle(400);
		});

		$('.ckkBox').change(function () {
			toggleCheckedAll(this);
			setCheckboxSelectLabels();
		});


		jQuery('#muscle-form')
			.find('input, textarea, select')
			.on('change', function () {
				validateInput();

			});

	});

var weekIndex = 1;

	$('#add-week').click(function () {
		// Start index after the initial row
		var selectOptions = $('select[name="plan[week][0][week_name]"]').html(); // Get options from the first select

		// var item = $('td .item .inner-wrap').html(); // Get options from the first select

		// var updatedItem = item.replace(/\[0\]/g, `[${weekIndex}]`);

		var newRow = `
            <tr class="week-count" data-index="${weekIndex}">
                <td>
                    <select name="plan[week][${weekIndex}][week_name]">
						${selectOptions}
                    </select>
                </td>
                <td><input type="number" data-number="${weekIndex + 1}" value="${weekIndex + 1}" name="plan[week][${weekIndex}][week_number]"></td>
                <td><button type="button" class="remove-week">Remove</button></td>
            </tr>
        `;
		$('#week-table tbody').append(newRow);

		var selectTraining = $('.training').html();

		var section = $('.section').html();

		var updateSection = section.replace(/\[0\]/g, `[${weekIndex}]`);

		var updateTraining = selectTraining.replace('checked', '');

		var round = $('.round').html();

		var updateRound = round.replace(/\[0\]/g, `[${weekIndex}]`);

		var newDay = `
                    <tr data-key="${weekIndex + 1}">
                        <td>
                            <p class="week-number">
                                ${weekIndex + 1}
							</p>
                        </td>
                        <td>
                            <input type="number" disabled="" value="1" name="plan[week][${weekIndex}][days][${weekIndex}][nums_day]">
                        </td>
                        <td>
                            <select name="plan[week][${weekIndex}][days][${weekIndex}][training_method_id]">
									${updateTraining}
							</select>
                        </td>
						<td><div class="section">${updateSection}</td>
						<td><div class="round">${updateRound}</td>
                        <td><button type="button" class="remove-week">Remove</button></td>
                    </tr>
                    <tr data-key="${weekIndex + 1}">
                        <td>
                            <p class="week-number">
                                                            </p>
                        </td>
                        <td>
                            <input type="number" disabled="" value="2" name="plan[week][${weekIndex}][days][${weekIndex}][nums_day]">
                        </td>
                        <td>
                            <select name="plan[week][${weekIndex}][days][${weekIndex}][[training_method_id]">
								${selectTraining}
							</select>
                        </td>
						<td><div class="section">${updateSection}</td>
						<td><div class="round">${updateRound}</td>
                        <td><button type="button" class="remove-day">Remove</button></td>
                    </tr>
                    <tr data-key="${weekIndex + 1}">
                        <td>
                            <p class="week-number"></p>
                        </td>
                        <td>
                            <input type="number" disabled="" value="3" name="plan[week][${weekIndex}][days][${weekIndex}][nums_day]">
                        </td>
                        <td>
                            <select name="plan[week][${weekIndex}][days][${weekIndex}][[training_method_id]">
								${selectTraining}
							</select>
                        </td>
						<td><div class="section">${updateSection}</td>
						<td><div class="round">${updateRound}</td>
                        <td><button type="button" class="remove-day">Remove</button></td>
                    </tr>
                    <tr data-key="${weekIndex + 1}">
                        <td>
                            <p class="week-number"></p>
                        </td>
                        <td>
                            <input type="number" disabled="" value="4" name="plan[week][${weekIndex}][days][${weekIndex}][nums_day]">
                        </td>
                        <td>
                            <select name="plan[week][${weekIndex}][days][${weekIndex}][[training_method_id]">
								${selectTraining}
							</select>
                        </td>
						<td><div class="section">${updateSection}</td>
						<td><div class="round">${updateRound}</td>
                        <td><button type="button" class="remove-day">Remove</button></td>
                    </tr>
                    <tr data-key="${weekIndex + 1}">
                        <td>
                            <p class="week-number"></p>
                        </td>
                        <td>
                            <input type="number" disabled="" value="5" name="plan[week][${weekIndex}][days][${weekIndex}][nums_day]">
                        </td>
                        <td>
                            <select name="plan[week][${weekIndex}][days][${weekIndex}][[training_method_id]">
								${selectTraining}
							</select>
                        </td>
						<td><div class="section">${updateSection}</td>
						<td><div class="round">${updateRound}</td>
                        <td><button type="button" class="remove-day">Remove</button></td>
                    </tr>
                    <tr data-key="${weekIndex + 1}">
                        <td>
                            <p class="week-number"></p>
                        </td>
                        <td>
                            <input type="number" disabled="" value="6" name="plan[week][${weekIndex}][days][${weekIndex}][nums_day]">
                        </td>
                        <td>
                            <select name="plan[week][${weekIndex}][days][${weekIndex}][[training_method_id]">
								${selectTraining}
							</select>
                        </td>
						<td><div class="section">${updateSection}</td>
						<td><div class="round">${updateRound}</td>
                        <td><button type="button" class="remove-day">Remove</button></td>
                    </tr>
		`;

		$('#day-table tbody').append(newDay);
		weekIndex++; // Increment the index for the next added row
	});

	$(document).on('click', '.remove-week', function () {

		$index = $(this).closest('tbody').find('tr').length;

		var $row = $(this).closest('tr');

		var weekNumber = $row.find('input[name^="plan[week]"][name$="[week_number]"]').data('number');

		if ($index > 1) {
			$(this).closest('tr').remove();
			var i = updateIndices($('#week-table tr'), jQuery); // Update indices after removal

			i++;
			weekIndex = i;
			$('#day-table tbody tr').each(function () {
				var key = $(this).attr('data-key');
				if (key == weekNumber) {
					$(this).remove();
				}
			});
			updateWeekNumbers();

		}
	});

	$(document).on('click', '.remove-day', function() {
        var $tr = $(this).closest('tr');

        $tr.find('*').each(function() {
            $.each(this.attributes, function() {
                if (this.name.startsWith('data-')) {
                    $(this.ownerElement).removeAttr(this.name);
                }
            });
        });

        $tr.find('input').each(function(index) {
            if (index !== 0) {
                $(this).val('');
            }
        });

        $tr.find('select').each(function() {
            $(this).prop('selectedIndex', 0);
        });
    });


	function updateWeekNumbers() {
		var i = 1;
		var check = 1;
		$('#day-table tbody tr').each(function (index) {
			index = $(this).data('key');
			if (index != i) {
				if (index != check) {
					i++;
					check = index;
				}
			}

			if ($(this).find('.week-number').text() > 0) {
				$(this).find('.week-number').text(i);
			}

			$(this).attr('data-key', i); // Cập nhật giá trị week-number bắt đầu từ 1
		});
	}

});



function updateIndices(tr, $) {
	var i = 1;
	tr.each(function (index) {
		if (index > 0) {
			index -= 1;
			i = index;
		} else {
			i = index;
		};

		$(this).attr('data-index', index);
		$(this).find('select').attr('name', `plan[week][${index}][week_name]`);
		$(this).find('input[name$="[week_number]"]').attr('name', `plan[week][${index}][week_number]`);
		$(this).find('input[name$="[week_number]"]').attr('data-number', index + 1);
		$(this).find('input[name$="[week_number]"]').val(index + 1);
	});

	// Update the global weekIndex variable
	return i;
}


function checkRadioInput(className) {
	var deleteButtons = jQuery(
		'.' + className + ' .exercise-option-table'
	).find('.action-item.exercise-muscle-delete');

	var ids = [];

	deleteButtons.each(function () {
		var id = jQuery(this).data('id');
		ids.push(id);
	});

	var list = jQuery('.' + className + '-modal #the-list tr');
	jQuery('.' + className + '-modal #the-list tr').each(function () {
		var $row = jQuery(this);
		var checkbox = $row.find('input[type="checkbox"]');
		var isChecked = checkbox.is(':checked');

		if (isChecked) {
			var id = $row
				.find('.column-id')
				.contents()
				.filter(function () {
					return this.nodeType === 3;
				})
				.text()
				.trim();

			if (!(jQuery.inArray(parseInt(id), ids) !== -1)) {
				checkbox.prop('checked', false);
				checkbox.removeClass('selected');
			}
		}
	});
}
function checkedSectionInput(className = '', type) {
	var selectedData = [];


	var ids = [];

	jQuery('.' + className + ' #the-list tr').each(function () {
		var $row = jQuery(this);
		var isChecked = $row
			.find('input[type="checkbox"]')
			.is(':checked');

		if (isChecked) {
			var id = $row
				.find('.column-id')
				.contents()
				.filter(function () {
					return this.nodeType === 3;
				})
				.text()
				.trim();

			var name = $row
				.find('.column-name')
				.contents()
				.filter(function () {
					return this.nodeType === 3;
				})
				.text()
				.trim();

			if (!(jQuery.inArray(parseInt(id), ids) !== -1)) {
				selectedData.push({ id: id, name: name });
			}
		}
	});

	return selectedData;
}
function checkedInput(className = '', type) {
	var selectedData = [];

	var deleteButtons = jQuery('.' + type + ' .exercise-option-table').find(
		'.action-item.exercise-muscle-delete'
	);

	var ids = [];

	deleteButtons.each(function () {
		var id = jQuery(this).data('id');
		ids.push(id);
	});

	jQuery('.' + className + ' #the-list tr').each(function () {
		var $row = jQuery(this);
		var isChecked = $row
			.find('input[type="checkbox"]:not(.selected)')
			.is(':checked');

		if (isChecked) {
			var id = $row
				.find('.column-id')
				.contents()
				.filter(function () {
					return this.nodeType === 3;
				})
				.text()
				.trim();

			var name = $row
				.find('.column-name')
				.contents()
				.filter(function () {
					return this.nodeType === 3;
				})
				.text()
				.trim();

			if (!(jQuery.inArray(parseInt(id), ids) !== -1)) {
				selectedData.push({ id: id, name: name });
			}
		}
	});

	return selectedData;
}
function openQuizMediaUploader(e, element) {
	e.preventDefault();
	var aysUploader = wp
		.media({
			title: 'Upload',
			button: {
				text: 'Upload',
			},
			frame: 'post', // <-- this is the important part
			state: 'insert',
			library: {
				type: 'image',
			},
			multiple: false,
		})
		.on('insert', function () {
			var state = aysUploader.state();
			var selection = selection || state.get('selection');
			if (!selection) return;

			var attachment = selection.first();
			var display = state.display(attachment).toJSON();
			attachment = attachment.toJSON();
			// Do something with attachment.id and/or attachment.url here
			var imgurl = attachment.sizes[display.size].url;

			element.text('Edit Image');

			element.parent('.field');
			var container = element
				.closest('.field')
				.find('.exercise-image-container');
			container.fadeIn();

			container.find('img.exercise-img').attr('src', imgurl);
			container.find('input.exercise-image').val(imgurl);
		})
		.open();

	return false;
}

function setCheckboxSelectLabels(elem) {
	var wrappers = jQuery('.wrapper');
	jQuery.each(wrappers, function (key, wrapper) {
		var checkboxes = jQuery(wrapper).find('.ckkBox');
		var label = jQuery(wrapper).find('.checkboxes').attr('id');

		var prevText = '';
		jQuery.each(checkboxes, function (i, checkbox) {
			var button = jQuery(wrapper).find('button');
			var val = jQuery(checkbox).val();
			var inputHidden = jQuery(wrapper).find('.val-' + val);
			if (jQuery(checkbox).prop('checked') == true) {
				var text = jQuery(checkbox).next().html();
				var btnText = prevText + text;
				var numberOfChecked = jQuery(wrapper).find(
					'input.val:checkbox:checked'
				).length;
				if (numberOfChecked >= 6) {
					btnText = 'All ' + label + ' Type selected';
				}
				jQuery(button).text(btnText);
				prevText = btnText + ', ';

				inputHidden.val(val);
			} else {
				inputHidden.val('');
			}
		});
	});
}

function toggleCheckedAll(checkbox) {
	var apply = jQuery(checkbox)
		.closest('.wrapper')
		.find('.apply-selection');
	apply.fadeIn('slow');

	var val = jQuery(checkbox).closest('.checkboxes').find('.val');
	var all = jQuery(checkbox).closest('.checkboxes').find('.all');
	var ckkBox = jQuery(checkbox).closest('.checkboxes').find('.ckkBox');

	if (!jQuery(ckkBox).is(':checked')) {
		jQuery(all).prop('checked', true);
		return;
	}

	if (jQuery(checkbox).hasClass('all')) {
		jQuery(val).prop('checked', false);
	} else {
		jQuery(all).prop('checked', false);
	}
}

function validateInput() {

	jQuery('#muscle-form')
		.find('input, textarea, select')
		.each(function () {
			var $input = jQuery(this);
			var value = $input.val().trim();
			var type = $input.attr('type');

			if (jQuery(this).closest('.field').find('.field-label').hasClass('attention')) {
				if ($input.val()) {
					$input.closest('.field-item').removeClass('error');
					$input.closest('.field-item').find('.error-message').remove();
				}
				if ($input.is(':visible')) {
					if (
						$input.is('select') ||
						$input.is('textarea') ||
						type === 'text' ||
						type === 'radio' ||
						type === 'hidden'
					) {
						if (value === '') {
							isValid = false;
							$input
								.closest('.field-item')
								.addClass('error')
								.append(
									'<span class="error-message">This field is required</span>'
								);
						}
					}
				}
			}
		});

	if (jQuery('.error-message').length > 0) {
		jQuery('html, body').animate(
			{
				scrollTop: jQuery('.error').first().offset().top,
			},
			500
		);
	}
}

function isValidURL(string) {
	var urlPattern = new RegExp('^(https?:\\/\\/)?' + // protocol
		'((([a-zA-Z0-9\\$\\_\\+\\!\\*\\,\\;\\=\\:.-]+)\\.[a-zA-Z]{2,})|' + // domain name and extension
		'((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ipv4
		'(\\:\\d+)?(\\/[-a-zA-Z0-9@:%_\\+.~#?&//=]*)?$', 'i'); // port and path
	return !!urlPattern.test(string);
}
