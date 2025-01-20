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
		console.log(" $element", $element);
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
			console.log("Mobile Noe.",ADHARNUMBER);	
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
						$responseMessage.html('');
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
		const $foundedAddress = $(".founded-address");
		const $foundedAddresstext = `We have found ABHA ${$foundedAddress} address linked with your mobile number.`;

		const $dropdown = $("<select class='input-field'>")
			.addClass("phr-dropdown")
			.append('<option value="" disabled selected>Select PHR Address</option>');

		mappedPhrAddresses.forEach((address) => {
			$dropdown.append(`<option value="${address}">${address}</option>`);
		});

		$foundedAddress.text($foundedAddresstext);
		$abhaList.append($dropdown);
		$abhaList.append('<div class="form-submit"> <button id="select-phr-btn" class="submit-btn" style="display: none;">Continue</button> </div>');
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

				if (loginResponse.image_url) {
					const imageHtml = `
						<div class="image-preview">
							<img src="${APIResponse.image_url}" alt="API Image" />
							<a href="${APIResponse.image_url}" download="image" class="download-btn">Download Image</a>
						</div>`;
					$responseMessage.html(imageHtml);
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



	const $form = $("#create-abha-form");
	const $fname = $("#fname");
	const $mname = $("#mname");
	const $lname = $("#lname");
	const $day = $("#day");
	const $month = $("#month");
	const $year = $("#year");
	const $gender = $("input[name='gender']");
	const $state = $("#state");
	const $district = $("#district");
	const $pincode = $("#pincode");
	const $address = $("#address");
	const $continueButton = $form.find(".form-submit button");
	// Toggle ABHA Sections
	$("#create-abha-btn").on("click", function () {
		console.log(ADHARNUMBER);
		$(".choose-abha-section").hide();
		$('.create-abha-section').show();
	});

	function clearAllErrors() {
		$responseMessage.text('');
		$form.find(".error-message").remove();
		$form.find(".error").removeClass("error");
	}

	// Fetch States
	async function fetchStates() {
		try {
			const response = await $.get(ajax_obj, { action: "get_states" });

			if (response.success) {
				const states = response.data.data;
				$state.empty().append('<option value="">Select a State</option>');

				states.forEach(({ code, name }) => {
					$state.append(`<option value="${code}">${name}</option>`);
				});
			} else {
				console.log(`Error fetching states: ${response.data.message}`);
			}
		} catch (error) {
			console.log("Unexpected error while fetching states:", error);
		}
	}

	// Fetch Districts

	async function fetchDistricts(stateID) {
		try {
			const response = await $.get(ajax_obj, {
				action: "get_districts",
				state_ID: stateID,
			});

			if (response.success) {
				const districts = response.data.data;
				$district.empty().append('<option value="">Select a District</option>');

				districts.forEach(({ code, name }) => {
					$district.append(`<option value="${code}">${name}</option>`);
				});

				$district.prop("disabled", false);
			} else {
				console.error(`Error fetching districts: ${response.data.message}`);
				$district.empty().append('<option value="">No Districts Found</option>').prop("disabled", true);
			}
		} catch (error) {
			console.error("Unexpected error while fetching districts:", error);
		}
	}

	// Update Districts on State Change
	$state.on("change", function () {
		const stateID = $(this).val();
		$district.empty().append('<option value="">Loading...</option>').prop("disabled", true);

		if (stateID) {
			fetchDistricts(stateID);
		} else {
			$district.empty().append('<option value="">Select a State First</option>').prop("disabled", true);
		}
	});

	// Form Validation
	function validateField($element, isValid, errorMessage) {

		if (!isValid) {
			$element.addClass('error');
			$responseMessage.append(`<p><em>${errorMessage}</em></p>`).removeClass('success').addClass('error').show();
			return false;
		}
		return true;
	}

	function validateForm() {
		clearAllErrors();
		let isValid = true;

		isValid &= validateField($fname, $fname.val().trim() !== "", "First name is required.");
		isValid &= validateField($mname, $mname.val().trim() !== "", "Middle name is required.");
		isValid &= validateField($lname, $lname.val().trim() !== "", "Last name is required.");

		const day = parseInt($day.val(), 10);
		const month = parseInt($month.val(), 10);
		const year = parseInt($year.val(), 10);
		const currentYear = new Date().getFullYear();
		const daysInMonth = new Date(year, month, 0).getDate();

		isValid &= validateField($day, day >= 1 && day <= daysInMonth, `Day must be between 1 and ${daysInMonth}.`);
		isValid &= validateField($month, month >= 1 && month <= 12, "Month must be between 1 and 12.");
		isValid &= validateField($year, year >= 1900 && year <= currentYear, `Year must be between 1900 and ${currentYear}.`);

		isValid &= validateField($gender, $gender.is(":checked"), "Gender is required.");
		isValid &= validateField($state, $state.val().trim() !== "", "State is required.");
		isValid &= validateField($district, $district.val().trim() !== "", "District is required.");
		isValid &= validateField($pincode, $pincode.val().trim() !== "", "Pincode is required.");

		return isValid;
	}

	// Populate dropdowns for day, month, and year
	function populateDateDropdowns() {
		const $dayDropdown = $("#day");
		const $monthDropdown = $("#month");
		const $yearDropdown = $("#year");

		// Populate days
		for (let i = 1; i <= 31; i++) {
			const dayValue = i.toString().padStart(2, '0');
			$dayDropdown.append(`<option value="${dayValue}">${dayValue}</option>`);
		}

		// Populate months with names
		const monthNames = [
			"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
		];
		monthNames.forEach((month, index) => {
			const monthValue = (index + 1).toString().padStart(2, '0');
			$monthDropdown.append(`<option value="${monthValue}">${month}</option>`);
		});

		// Populate years
		const currentYear = new Date().getFullYear();
		for (let i = currentYear; i >= 1900; i--) {
			$yearDropdown.append(`<option value="${i}">${i}</option>`);
		}
	}

	// Initialize
	fetchStates();
	populateDateDropdowns();

	// Form Submission
	$continueButton.on("click", async function (e) {	
		e.preventDefault();
		if (validateForm()) {
			toggleLoading(true);
			const payload = {
				transaction_id: $transactionIdField.val().trim(),
				first_name: $fname.val().trim(),
				middle_name: $mname.val().trim(),
				last_name: $lname.val().trim(),
				pin_code: $pincode.val().trim(),
				gender: $gender.filter(":checked").val().trim(),
				dob: `${$year.val().trim()}-${$month.val().trim()}-${$day.val().trim()}`,
				mobile_no: ADHARNUMBER,
				state_code: $state.val().trim(),
				district_code: $district.val().trim(),
				address: $address.val().trim(),
			};
			console.log(payload);

			try {
				const response = await $.post(ajax_obj, {
					action: "PHR_demographics_submit",
					...payload
				});

				if (response.success) {
					const message = response.data?.message || "Demographics submitted successfully.";
					showSuccess(message);
				} else {
					const errorMessage = response.data?.data || "Failed to submit demographics.";
					showError(errorMessage);
				}
			} catch (err) {
				console.error("Error:", err);
				showError(`Error: ${err.message}`);
			} finally {
				toggleLoading(false);
			}
		} else {
			console.log("Error Find");
		}
	});


});