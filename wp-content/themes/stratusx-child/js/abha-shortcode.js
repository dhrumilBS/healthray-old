jQuery(document).ready(($) => {
	const $commonSection = $('#common-section');
	const $adharOtpSection = $('#adhar-otp-section');
	const $mobileOtpSection = $('#mobile-otp-section');
	const $responseMessage = $('#response-message');
	const $loadingSpinner = $('#loading-spinner');
	const $otpForm = $('.otp-form');
	const $resendButton = $('#resend-otp');
	const $changeMobileBtn = $('#change-mobile');
	const $transactionIdField = $('.transaction-id');
	let transactionId = null;
	let ADHARNUMBER = null;
	let resendTimer;
	let loadingInterval;

	// Helper Functions
	function toggleLoading(show, $element = $loadingSpinner) {
		if (show) {
			startLoadingAnimation($element);
		} else {
			stopLoadingAnimation($element);
		}
	}

	function startLoadingAnimation($element) {
		let dots = '';
		stopLoadingAnimation($element); // Clear existing intervals
		loadingInterval = setInterval(() => {
			dots = dots.length < 3 ? dots + '.' : '';
			$element.text(`Loading${dots}`).show();
		}, 500);
	}

	function stopLoadingAnimation($element) {
		clearInterval(loadingInterval);
		$element.text('').hide();
	}

	function showError(message, $element = $responseMessage) {
		$element.text(message).removeClass('success').addClass('error').show();
	}

	function showSuccess(message, $element = $responseMessage) {
		$element.text(message).removeClass('error').addClass('success').show();
	}

	function startResendTimer() {
		let countdown = 30;
		$resendButton.css('opacity', .75);
		$resendButton.prop('disabled', true).text(`Resend OTP in ${countdown}s`);

		resendTimer = setInterval(() => {
			countdown--;
			$resendButton.text(`Resend OTP in ${countdown}s`);

			if (countdown <= 0) {
				clearInterval(resendTimer);
				$resendButton.css('opacity', 1);
				$resendButton.prop('disabled', false).text('Resend OTP');
			}
		}, 1000);
	}

	function handleActionButtonClick(event) {
		const $button = $(event.target);
		$('.link-action-btn').removeClass('active');
		$button.addClass('active');

		const view = $button.data('view');
		$('.auth-form').hide();
		$(`.auth-form[data-type="${view}"]`).show();
	}
	$('.link-action-btn').on('click', handleActionButtonClick);

	// Form Submit Handler
	async function handleAuthFormSubmit(e) {
		e.preventDefault();
		const $form = $(e.target);
		const type = $form.data('type');
		const inputField = $form.find('.auth-input').val().trim();

		$responseMessage.html('');
		toggleLoading(true);

		// Validation 
		// if ((type === 'aadhaar' && !/^\d{12}$/.test(inputField)) ||
		// 	(type === 'mobile' && !/^\d{10}$/.test(inputField))) {
		// 	toggleLoading(false);
		// 	return showError(Please enter a valid ${type === 'aadhaar' ? 'Aadhaar' : 'Mo b ile'} Number.);
		// }

		const payload = type === 'aadhaar' ? { value: inputField } : { number: inputField };
		const actionType = type === 'aadhaar' ? 'aadhaar_auth_form_submit' : 'PHR_mobile_auth_form_submit';

		console.log(actionType, "Payload", payload);

		try {
			const response = await $.post(ajax_obj, {
				action: actionType,
				...payload
			});
			console.log(type, 'Response:', response);
			if (response.success === true) {
				const APIResponse = response.data;
				console.log(APIResponse);
				const { transactionId, message } = APIResponse;
				ADHARNUMBER = inputField;
				$transactionIdField.val(transactionId);
				showSuccess(message);
				type === 'aadhaar' ? $adharOtpSection.show() : $mobileOtpSection.show();
				$commonSection.hide();
				startResendTimer();
			} else {
				const errorMessage = response.data?.message || 'Verification failed.';
				showError(errorMessage);
			}
		} catch (err) {
			console.error('Error:', err);
			showError(`Error: ${err.message}`);
		} finally {
			toggleLoading(false);
		}
	}
	$('.auth-form').on('submit', handleAuthFormSubmit);

	// Change Mobile Button Handler
	$changeMobileBtn.on('click', () => {
		$adharOtpSection.hide();
		$mobileOtpSection.hide();
		$commonSection.show();
	});

	// Verify OTP Form Submit Handler
	async function handleOtpFormSubmit(e) {
		e.preventDefault();
		$responseMessage.html('');
		const $form = $(e.target);
		const type = $form.data('type');
		const otp = $form.find('#otp-input').val().trim();
		const transactionID = $form.find('.transaction-id').val().trim();
		let otpMobileno = '';

		if (type !== 'mobile') {
			otpMobileno = $form.find('#otp-mobile-input').val().trim();
			if (!/^\d{10}$/.test(otpMobileno)) {
				return showError('Invalid mobile number.');
			}
		}

		const payload = type === 'aadhaar' ? { otp: otp, number: otpMobileno, transactionID: transactionID } : { otp: otp, transactionID: transactionID };
		const actionType = type === 'aadhaar' ? 'verify_aadhaar_otp' : 'verify_PHR_otp';


		if (otp.length !== 6) {
			return showError('Invalid OTP.');
		}

		console.log('OTP Payload:', { ...payload });

		try {
			toggleLoading(true);
			const response = await $.post(ajax_obj, {
				action: actionType,
				...payload
			});

			const APIResponse = response.data;

			if (APIResponse && response.success) {
				showSuccess(APIResponse.message);
				$form.closest('.otp-section').hide();

				// ADHAR
				if (APIResponse.image_url) {
					const imageHtml = `
						<div class="image-preview">
							<img src="${APIResponse.image_url}" alt="API Image" />
							<a href="${APIResponse.image_url}" download="image" class="download-btn">Download Image</a>
						</div>`;
					$responseMessage.html(imageHtml);
				}

				// PHR - Mobile
				if (type != 'aadhaar') {
					console.log(type, 'API Response:', APIResponse);
					if (APIResponse.mappedPhrAddress && APIResponse.transactionId) {
						populatePhrDropdown(APIResponse.mappedPhrAddress, APIResponse.transactionId);
					}
					$('.choose-abha-section').show();
					console.log(type, 'OTP Response:', response);

				}


			} else {
				showError(APIResponse.message);
			}
		} catch (err) {
			console.error('OTP Error:', err);
			showError(`Error: ${err.message || 'An unexpected error occurred.'}`);
		} finally {
			toggleLoading(false);
		}
	}

	// Populate PHR Address Dropdown
	function populatePhrDropdown(mappedPhrAddresses, transactionId) {
		const $abhaList = $(".abha-list");
		$abhaList.empty();

		const $dropdown = $("<select>")
			.addClass("phr-dropdown")
			.append('<option value="" disabled selected>Select PHR Address</option>');

		mappedPhrAddresses.forEach((address) => {
			$dropdown.append(`<option value="${address}">${address}</option>`);
		});

		$abhaList.append($dropdown);
		$abhaList.append('<button id="select-phr-btn" class="submit-btn">Continue</button>');
		$("#select-phr-btn").show();

		// Handle PHR selection
		$('#select-phr-btn').off('click').on('click', async function () {
			const selectedAddress = $(".phr-dropdown").val();
			if (!selectedAddress) {
				return showError('Please select a PHR Address.');
			}

			const loginPayload = {
				transaction_id: transactionId,
				phr_address: selectedAddress,
				is_health_number: false,
				already_registered: true,
			};

			console.log('Login Payload:', loginPayload);

			try {
				toggleLoading(true);
				const loginResponse = await $.post(ajax_obj, {
					action: 'login_phr_address',
					...loginPayload,
				});

				if (loginResponse.success) {
					showSuccess(loginResponse.data.message || 'Successfully logged in!');
					// Handle additional actions post-login if needed
				} else {
					showError(loginResponse.data.message || 'Login failed.');
				}
			} catch (err) {
				console.error('PHR Login Error:', err);
				showError(`Error: ${err.message || 'An unexpected error occurred.'}`);
			} finally {
				toggleLoading(false);
			}
		});
	}
	$('.otp-form').on('submit', handleOtpFormSubmit);
	// Resend OTP Handler
	$resendButton.on('click', async () => {
		console.log({ TransactionID: transactionId, value: ADHARNUMBER },);
		if (!transactionId) return showError('Cannot resend OTP. Transaction ID is missing.');

		try {
			toggleLoading(true);

			const response = await $.post(ajax_obj, {
				action: 'aadhaar_auth_form_submit',
				value: ADHARNUMBER
			});

			console.log('Resend OTP Response:', response);
			if (response.success) {
				showSuccess('OTP resent successfully.');
				startResendTimer();
			} else {
				showError(response.data.message || 'Failed to resend OTP.');
			}
		} catch (err) {
			console.error('Resend OTP Error:', err);
			showError(`Error: ${err.message || 'An error occurred.'}`);
		} finally {
			toggleLoading(false);
		}
	});


	$("#create-abha-btn").on("click", function () {
		// Hide the choose ABHA section
		$(".choose-abha-section").hide();

		// Show the create ABHA section
		$(".create-abha-section").show();
	});


	const $form = $("#create-abha-form");
	const $name = $("#name");
	const $day = $("#day");
	const $month = $("#month");
	const $year = $("#year");
	const $gender = $("input[name='gender']");
	const $genderParent = $(".gender-options");
	const $state = $("#state");
	const $city = $("#city");
	const $continueButton = $form.find(".form-submit button");

	function ABHAshowError($element, message) {
		$element.addClass("error");
		const $errorMessage = $("<div>")
			.addClass("error-message")
			.text(message);
		$responseMessage.append($errorMessage);
	}

	function clearError($element) {
		$element.removeClass("error");
	}

	function clearAllErrors() {
		$responseMessage.empty();
		$(".input-field, .gender-options").removeClass("error");
	}

	function validateField($element, isEmpty, isInvalid, emptyErrorMessage, validationErrorMessage) {
		if (isEmpty) {
			// Show "required" error if the field is empty
			ABHAshowError($element, emptyErrorMessage);
			$element.addClass("error");
			return false;
		} else if (isInvalid) {
			// Show validation error only if the field is not empty
			ABHAshowError($element, validationErrorMessage);
			$element.addClass("error");
			return false;
		}
		return true;
	}

	function validateForm() {
		let isValid = true;
		clearAllErrors();

		// Validate Name
		const nameValue = $name.val().trim();
		isValid &= validateField($name, nameValue === "", false, "Name is required.", "");

		// Validate Date of Birth
		const dayValue = $day.val().trim();
		const monthValue = $month.val().trim();
		const yearValue = $year.val().trim();
		const day = parseInt(dayValue, 10);
		const month = parseInt(monthValue, 10);
		const year = parseInt(yearValue, 10);
		const currentYear = new Date().getFullYear();
		const daysInMonth = new Date(year, month, 0).getDate();

		isValid &= validateField(
			$day,
			dayValue === "",
			!isNaN(day) && (day < 1 || day > daysInMonth),
			"Day is required.",
			`Day must be between 1 and ${daysInMonth}.`
		);

		isValid &= validateField(
			$month,
			monthValue === "",
			!isNaN(month) && (month < 1 || month > 12),
			"Month is required.",
			"Month must be between 1 and 12."
		);

		isValid &= validateField(
			$year,
			yearValue === "",
			!isNaN(year) && (year < 1900 || year > currentYear),
			"Year is required.",
			`Year must be between 1900 and ${currentYear}.`
		);

		// Validate Gender
		if (!$gender.is(":checked")) {
			ABHAshowError($gender, "Gender is required.");
			$gender.addClass("error");
			isValid = false;
		}

		// Validate State
		const stateValue = $state.val().trim();
		isValid &= validateField($state, stateValue === "", false, "State is required.", "");

		// Validate City
		const cityValue = $city.val().trim();
		isValid &= validateField($city, cityValue === "", false, "City is required.", "");

		// Enable or disable the "Continue" button based on form validity
		$continueButton.prop("disabled", !isValid);

		return isValid;
	}

	// Add event listeners for validation on input change
	$name.on("input", () => validateForm());
	$day.on("input", () => validateForm());
	$month.on("input", () => validateForm());
	$year.on("input", () => validateForm());
	$gender.on("change", () => validateForm());
	$state.on("input", () => validateForm());
	$city.on("input", () => validateForm());

	// Handle form submission
	$continueButton.on("click", function () {
		if (validateForm()) {
			$form.submit();
		} else {
			$responseMessage.addClass("error-container").show();
		}
	});
});

