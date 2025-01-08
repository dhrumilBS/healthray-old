jQuery(document).ready(($) => {
	const $commonSection = $('#common-section');
	const $adharOtpSection = $('#adhar-otp-section');
	const $mobileOtpSection = $('#mobile-otp-section');
	const $responseMessage = $('#response-message');
	const $loadingSpinner = $('#loading-spinner');
	const $otpForm = $('.otp-form');
	const $resendButton = $('#resend-otp');
	const $changeMobileBtn = $('#change-mobile');
	const $transactionIdField = $('#transaction-id');
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



	// Action Button Click Handler
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

		if ((type === 'aadhaar' && !/^\d{12}$/.test(inputField)) ||
			(type === 'mobile' && !/^\d{10}$/.test(inputField))) {
			toggleLoading(false);
			return showError(`Please enter a valid ${type === 'aadhaar' ? 'Aadhaar' : 'Mobile'} Number.`);
		}

		const payload = type === 'aadhaar' ? { value: inputField } : { number: inputField, is_health_number: false };
		const actionType = type === 'aadhaar' ? 'aadhaar_auth_form_submit' : 'mobile_auth_form_submit';
		console.log(actionType, "Payload", payload);

		try {

			const response = await $.post(ajax_obj, {
				action: actionType,
				...payload
			});

			ADHARNUMBER = inputField;
			console.log("ADHARNUMBER", ADHARNUMBER);


			console.log(type, ' Response:', response);

			const APIResponse = response.data;
			if (response.success === true) {
				transactionId = response.data.transactionId;
				$transactionIdField.val(transactionId);

				showSuccess(APIResponse.message);

				type === 'aadhaar' ? $adharOtpSection.show() : $mobileOtpSection.show();

				$commonSection.hide();
				startResendTimer();
			} else {
				showError(APIResponse.message);
			}
		} catch (err) {
			console.error('Error:', err);
			showError(`Error: ${err.message}`);
		} finally {
			toggleLoading(false);
		}
	}
	$('.auth-form').on('submit', handleAuthFormSubmit);
	$changeMobileBtn.on('click', async (e) => {
		$otpSection.hide();
		$commonSection.show();
	})


	// OTP Form Submit Handler
	// $otpForm.on('submit', async (e) => {

	// 	e.preventDefault();
	// 	toggleLoading(true);
	// 	$responseMessage.html('');

	// 	const otp = $('#otp-input').val().trim();
	// 	const otpMobileno = $('#otp-mobile-input').val().trim();
	// 	const transactionID = $('#transaction-id').val().trim();

	// 	if (otp.length !== 6 || !/^\d{10}$/.test(otpMobileno)) {
	// 		toggleLoading(false);
	// 		return showError('Invalid OTP or mobile number.');
	// 	}

	// 	console.log('OTP Payload:', {
	// 		action: 'verify_otp',
	// 		"otp": otp,
	// 		"number": otpMobileno,
	// 		"transaction_id": transactionID
	// 	});
	// 	try {
	// 		const response = await $.post(ajax_obj, {
	// 			action: 'verify_otp',
	// 			"otp": otp,
	// 			"number": otpMobileno,
	// 			"transaction_id": transactionID
	// 		});

	// 		console.log('OTP Response:', response);

	// 		const APIResponse = response.data;
	// 		if (APIResponse && APIResponse.success) {
	// 			showSuccess(APIResponse.message);
	// 			$otpSection.hide();
	// 		} else {
	// 			showError(APIResponse.message);
	// 		}
	// 	} catch (err) {
	// 		console.error('OTP Error:', err);
	// 		showError(`Error: ${err.message || 'An unexpected error occurred.'}`);
	// 	} finally {
	// 		toggleLoading(false);
	// 	}
	// });

	// OTP Form Submit Handler
	async function handleOtpFormSubmit(e, formId) {
		e.preventDefault();
		const $form = $(`#${formId}`);
		const otp = $form.find('#otp-input').val().trim();
		const transactionID = $form.find('#transaction-id').val().trim();
		let otpMobileno = '';

		if (formId === 'adhar-otp-form') {
			// Aadhaar OTP Form: No mobile number required
			otpMobileno = $form.find('#otp-mobile-input').val().trim();
			if (!/^\d{10}$/.test(otpMobileno)) {
				toggleLoading(false);
				return showError('Invalid mobile number.');
			}
		}

		if (otp.length !== 6) {
			toggleLoading(false);
			return showError('Invalid OTP.');
		}

		console.log('OTP Payload:', {
			action: 'verify_otp',
			otp,
			number: otpMobileno,
			transaction_id: transactionID,
		});

		try {
			toggleLoading(true);
			const response = await $.post(ajax_obj, {
				action: 'verify_otp',
				otp,
				number: otpMobileno,
				transaction_id: transactionID,
			});

			console.log('OTP Response:', response);
			const APIResponse = response.data;

			if (APIResponse && APIResponse.success) {
				showSuccess(APIResponse.message);
				$form.closest('.otp-section').hide();
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

	// Attach Event Listeners
	$('#adhar-otp-form').on('submit', (e) => handleOtpFormSubmit(e, 'adhar-otp-form'));
	$('#mobile-otp-form').on('submit', (e) => handleOtpFormSubmit(e, 'mobile-otp-form'));

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
});

