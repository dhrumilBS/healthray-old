jQuery(document).ready(($) => {
	const $commonSection = $('#common-section');
	const $otpSection = $('#otp-section');
	const $responseMessage = $('#response-message');
	const $loadingSpinner = $('#loading-spinner');
	const $otpForm = $('#otp-form');
	const $resendButton = $('#resend-otp');
	const $changeMobileBtn = $('#change-mobile');
	const $transactionIdField = $('#transaction-id');
	let transaction_id = null;
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
		$resendButton.prop('disabled', true).text(`Resend OTP in ${countdown}s`);

		resendTimer = setInterval(() => {
			countdown--;
			$resendButton.text(`Resend OTP in ${countdown}s`);

			if (countdown <= 0) {
				clearInterval(resendTimer);
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

		console.log('Payload:', payload);
		try {
			const response = await $.post(ajax_obj, {
				action: actionType,
				...payload
			});

			console.log('Response:', response);
			const APIResponse = response.data;
			if (APIResponse.status === 200) {
				transaction_id = APIResponse.transaction_id;
				$transactionIdField.val(transaction_id);
				showSuccess(APIResponse.message);
				$otpSection.show();
				$form.hide();
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

	// OTP Form Submit Handler
	$otpForm.on('submit', async (e) => {
		e.preventDefault();
		toggleLoading(true);
		$responseMessage.html('');

		const otp = $('#otp-input').val().trim();
		const otpMobileno = $('#otp-mobile-input').val().trim();
		const transactionID = $transactionIdField.val().trim();

		if (otp.length !== 6 || !/^\d{10}$/.test(otpMobileno)) {
			toggleLoading(false);
			return showError('Invalid OTP or mobile number.');
		}

		console.log('OTP Payload:', { otp, otpMobileno, transaction_id: transactionID });
		try {
			const response = await $.post(ajax_obj, {
				action: 'verify_otp',
				otp,
				otpMobileno,
				transaction_id: transactionID
			});

			console.log('OTP Response:', response);
			const APIResponse = response.data;
			if (APIResponse && APIResponse.success) {
				showSuccess(APIResponse.message);
				$otpSection.hide();
			} else {
				showError(APIResponse.message);
			}
		} catch (err) {
			console.error('OTP Error:', err);
			showError(`Error: ${err.message || 'An unexpected error occurred.'}`);
		} finally {
			toggleLoading(false);
		}
	});

	// Resend OTP Handler
	$resendButton.on('click', async () => {
		if (!transaction_id) return showError('Cannot resend OTP. Transaction ID is missing.');

		console.log('Resending OTP for Transaction ID:', transaction_id);
		try {
			toggleLoading(true);

			const response = await $.post(ajax_obj, {
				action: 'resend_otp',
				transaction_id
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
