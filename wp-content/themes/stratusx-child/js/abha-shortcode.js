jQuery(document).ready(($) => {
	const $commonSection = $('#common-section');
	const $adharOtpSection = $('#adhar-otp-section');
	const $mobileOtpSection = $('#mobile-otp-section');
	const $responseMessage = $('#response-message');
	const $loadingSpinner = $('#loading-spinner');
	const $otpForm = $('.otp-form');
	const $resendButton = $('.otp-resend-btn');
	const $changeMobileBtn = $('.change-mobile');
	const $transactionIdField = $('#transaction-id');
	const $state = $("#state");
	const $district = $("#district");

	let type = null;
	let ADHARNUMBER = null;
	let loadingInterval, resendTimer;

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
		stopLoadingAnimation($element);
		loadingInterval = setInterval(() => {
			dots = dots.length < 3 ? dots + '.' : '';
			$element.text(`Loading${dots}`).show();
		}, 500);
	}

	function stopLoadingAnimation($element) {
		clearInterval(loadingInterval);
		$element.text('').hide();
	}

	function showMessage(message, type) {
		$responseMessage.text(message).removeClass('success error').addClass(type).show();
	}

	function startResendTimer() {
		let countdown = 3;
		$resendButton.css('opacity', 0.75).prop('disabled', true).text(`Resend OTP in ${countdown}s`);
		resendTimer = setInterval(() => {
			$resendButton.text(`Resend OTP in ${--countdown}s`);
			if (countdown <= 0) {
				clearInterval(resendTimer);
				$resendButton.css('opacity', 1).prop('disabled', false).text('Resend OTP');
			}
		}, 1000);
	}

	function displayImagePreview(imageUrl) {
		$responseMessage.html(`
			<div class="image-preview">
				<img src="${imageUrl}" alt="API Image" />
				<a href="${imageUrl}" download="image" class="download-btn">Download Image</a>
			</div>
		`);
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
		type = $form.data('type');
		const inputField = $form.find('.auth-input').val().trim();

		$responseMessage.html('');
		toggleLoading(true);

		// Construct Payload and Determine ActionType
		const payload = type === 'aadhaar' ? { value: inputField } : { number: inputField };
		const actionType = type === 'aadhaar' ? 'aadhaar_auth_form_submit' : 'PHR_mobile_auth_form_submit';

		console.log('Submitting payload:', payload);
		console.log('Action type:', actionType);

		try {
			const response = await $.post(ajax_obj, { action: actionType, ...payload });
			console.log('Response received:', response);
			if (response.success) {
				const { transactionId, message } = response.data;
				ADHARNUMBER = inputField;
				$transactionIdField.val(transactionId);
				showMessage(message, 'success');
				type === 'aadhaar' ? $adharOtpSection.show() : $mobileOtpSection.show();
				$commonSection.hide();
				startResendTimer();
			} else {
				showMessage(response.data?.message || 'Verification failed.', 'error');
			}
		} catch (err) {
			console.error('Error:', err);
			showMessage(`Error: ${err.message}`, 'error');
		} finally {
			toggleLoading(false);
		}
	}
	$('.auth-form').on('submit', handleAuthFormSubmit);

	// Change Mobile Button Handler
	$changeMobileBtn.on('click', () => {
		$responseMessage.html('');
		$adharOtpSection.hide();
		$mobileOtpSection.hide();
		$commonSection.show();
	});

	// Function to handle successful OTP verification
	async function handlePostOtpSuccess(APIResponse, $form) {
		console.log('Post OTP API Response:', APIResponse);

		// Show image preview if available
		if (APIResponse.image_url) {
			displayImagePreview(APIResponse.image_url);
		} else if (APIResponse.data.otp_required) {
			const transactionId = $transactionIdField.val().trim();
			const mobileNumber = $form.find('#adhar-otp-mobile-input').val().trim();
			const payload = { transaction_id: transactionId, number: mobileNumber };

			console.log("Sending mobile submission payload:", payload);

			try {
				toggleLoading(true);
				const response = await $.post(ajax_obj, { action: 'handle_aadhaar_mobile_submit', ...payload });
				const APIResponse = response.data;

				console.log(APIResponse);
				if (response.success) {
					showMessage(APIResponse.message, 'success');
				} else {
					showMessage(APIResponse.message, 'error');
				}


			}
			catch (err) {
				console.error('Mobile submission Error:', err);
				showMessage(`Error: ${err.message || 'An unexpected error occurred.'}`, 'error');
			}
			finally { toggleLoading(false); }


			$('#adhar-mobile-otp-section').show();
		} else {
			// Handle the dropdown population if addresses are available
			handleAddressPopulation(APIResponse);

			// If no additional addresses are available, show an error message
			showMessage(APIResponse.message || 'Something went wrong.', 'error');
		}
	}

	// Make a mobile submission if required
	async function handleMobileSubmit(e) {
		
		$form = $(e.target);
		console.log($form);

		const transactionId = $transactionIdField.val().trim();
		const mobileNumber = $form.find('#adhar-otp-mobile-input').val().trim();
		const otp = $form.find('#adhar-mobile-otp-input').val().trim();
		const payload = { transaction_id: transactionId, otp: otp };

		console.log("Verify adhar-mobile payload:", payload);

		try {
			toggleLoading(true);
			const response = await $.post(ajax_obj, { action: 'verify_adhar_mobile_otp', ...payload });
			const APIResponse = response.data;

			if (response.success) {
				showMessage(APIResponse.message, 'success');
			} else {
				showMessage(APIResponse.message, 'error');
			}
		} catch (err) {
			console.error('Mobile submission Error:', err);
			showMessage(`Error: ${err.message || 'An unexpected error occurred.'}`, 'error');
		} finally {
			toggleLoading(false);
		}
	}

	$('#adhar-mobile-otp-form').on('submit', handleMobileSubmit);

	// Verify OTP form submit handler
	async function handleOtpFormSubmit(e) {
		// e.preventDefault();

		const $form = $(e.target);
		console.log($form);
		
		const otp = $form.find('.otp-input').val().trim();
		const transactionID = $transactionIdField.val().trim();
		const type = $form.data('type');
		const action = type === 'aadhaar' ? 'verify_aadhaar_otp' : 'verify_PHR_otp';

		const payload = (type === 'aadhaar')
			? { otp, number: $form.find('#adhar-otp-mobile-input').val().trim(), transactionID }
			: { otp, transactionID };

		// Validate OTP and number
		if (otp.length !== 6 || (type !== 'mobile' && !/^\d{10}$/.test(payload.number))) {
			return showMessage('Invalid input.', 'error');
		}

		console.log('Submitting OTP Payload:', payload);

		try {
			toggleLoading(true);
			const response = await $.post(ajax_obj, { action, ...payload });
			console.log('Verify OTP Response:', response);

			if (response.success) {
				showMessage(response.data.message, 'success');
				await handlePostOtpSuccess(response.data, $form);
			} else {
				showMessage(response.data.message, 'error');
			}
		} catch (err) {
			console.error('OTP Error:', err);
			showMessage(`Error: ${err.message || 'An unexpected error occurred.'}`, 'error');
		} finally {
			toggleLoading(false);
		}
	}

	// Additional function to handle address population in the dropdown
	function handleAddressPopulation(APIResponse) {
		if (APIResponse.mappedPhrAddress || APIResponse.data.abha_address) {
			const addressToUse = APIResponse.mappedPhrAddress ?? APIResponse.data.abha_address;
			const transactionIdToUse = APIResponse.transactionId ?? APIResponse.transaction_id;

			// Populate the PHR dropdown
			populatePhrDropdown(addressToUse, transactionIdToUse);

			// Show the section that allows users to choose an address
			$('.choose-abha-section').show();
		}
	}

	// Attach the submit handler to the OTP form
	$otpForm.on('submit', handleOtpFormSubmit);


	// Populate PHR Address Dropdown
	function populatePhrDropdown(mappedPhrAddresses, transactionId) {
		const $abhaList = $(".abha-list").empty();
		const $foundedAddress = $(".founded-address");
		const $foundedAddresstext = `We have found ABHA address linked with your mobile number.`;

		const $dropdown = $("<select class='input-field phr-dropdown'>").append('<option value="" disabled selected>Select PHR Address</option>');
		mappedPhrAddresses.forEach(address => $dropdown.append(`<option value="${address}">${address}</option>`));

		$foundedAddress.text($foundedAddresstext);
		$abhaList.append($dropdown, '<div class="form-submit"><button id="select-phr-btn" class="submit-btn">Continue</button></div>');
		$("#select-phr-btn").show().off('click').on('click', () => {
			const selectedAddress = $dropdown.val();
			console.log('Selected PHR Address:', selectedAddress);
			handlePHRLogin(transactionId, selectedAddress, true);
		});
	}

	// Resend OTP Handler
	$resendButton.on('click', async (e) => {
		e.preventDefault();
		const actionType = type === 'aadhaar' ? 'aadhaar_auth_form_submit' : 'PHR_mobile_auth_form_submit';
		const transactionId = $transactionIdField.val().trim();
		if (!transactionId) return showMessage('Cannot resend OTP. Transaction ID is missing.', 'error');

		try {
			toggleLoading(true);
			const response = await $.post(ajax_obj, { action: actionType, value: ADHARNUMBER });
			console.log('Resend OTP Response:', response);
			if (response.success) {
				showMessage('OTP resent successfully.', 'success');
				startResendTimer();
			} else {
				showMessage(response.data.message || 'Failed to resend OTP.', 'error');
			}
		} catch (err) {
			console.error('Resend OTP Error:', err);
			showMessage(`Error: ${err.message || 'An error occurred.'}`, 'error');
		} finally {
			toggleLoading(false);
		}
	});

	// Fetch States
	async function fetchStates() {
		try {
			const response = await $.get(ajax_obj, { action: "get_states" });
			console.log('States Fetch Response:', response);
			if (response.success) {
				let states = response.data.data;
				$state.empty().append('<option value="">Select a State</option>');
				states.forEach(({ code, name }) => $state.append(`<option value="${code}">${name}</option>`));
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
			const response = await $.get(ajax_obj, { action: "get_districts", state_ID: stateID });
			console.log('Districts Fetch Response:', response);
			$district.empty().append('<option value="">Select a District</option>').prop("disabled", true);
			if (response.success) {
				const districts = response.data.data;
				districts.forEach(({ code, name }) => $district.append(`<option value="${code}">${name}</option>`));
				$district.prop("disabled", false);
			} else {
				console.error(`Error fetching districts: ${response.data.message}`);
			}
		} catch (error) {
			console.error("Unexpected error while fetching districts:", error);
		}
	}

	// Update Districts on State Change
	$state.on("change", function () {
		const stateID = $(this).val();
		if (stateID) {
			fetchDistricts(stateID);
		} else {
			$district.empty().append('<option value="">Select a State First</option>').prop("disabled", true);
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
	const $pincode = $("#pincode");
	const $address = $("#address");
	const $continueButton = $form.find(".form-submit button");
	const $suggestionSection = $('.suggestion-section');
	const $suggestionList = $('.suggestion-list');

	// Toggle ABHA Sections
	$("#create-abha-btn").on("click", function () {
		console.log(ADHARNUMBER);
		$responseMessage.html('');
		$(".choose-abha-section").hide();
		$('.create-abha-section').show();
	});

	// Clear all error messages
	function clearAllErrors() {
		$responseMessage.text('');
		$form.find(".error-message").remove();
		$form.find(".error").removeClass("error");
	}

	// Validate individual fields
	function validateField($element, isValid, errorMessage) {
		if (!isValid) {
			$element.addClass('error');
			$responseMessage.append(`<p><em>${errorMessage}</em></p>`).removeClass('success').addClass('error').show();
			return false;
		}
		return true;
	}

	// Validate whole form
	function validateForm() {
		clearAllErrors();
		let isValid = true;

		// Collect fields
		const fields = [
			[$fname, $fname.val().trim() !== "", "First name is required."],
			[$mname, $mname.val().trim() !== "", "Middle name is required."],
			[$lname, $lname.val().trim() !== "", "Last name is required."],
			[$day, parseInt($day.val(), 10) >= 1 && parseInt($day.val(), 10) <= new Date(parseInt($year.val(), 10), parseInt($month.val(), 10), 0).getDate(), `Day must be between 1 and ${new Date(parseInt($year.val(), 10), parseInt($month.val(), 10), 0).getDate()}.`],
			[$month, parseInt($month.val(), 10) >= 1 && parseInt($month.val(), 10) <= 12, "Month must be between 1 and 12."],
			[$year, parseInt($year.val(), 10) >= 1900 && parseInt($year.val(), 10) <= new Date().getFullYear(), `Year must be between 1900 and ${new Date().getFullYear()}.`],
			[$gender, $gender.is(":checked"), "Gender is required."],
			[$state, $state.val().trim() !== "", "State is required."],
			[$district, $district.val().trim() !== "", "District is required."],
			[$pincode, $pincode.val().trim() !== "", "Pincode is required."]
		];

		// Validate all fields
		fields.forEach(([element, condition, message]) => {
			isValid &= validateField(element, condition, message);
		});

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

		// Populate months
		const monthNames = [
			"January", "February", "March", "April", "May", "June",
			"July", "August", "September", "October", "November", "December"
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
				address: $address.val().trim()
			};

			console.log('Submitting demographics payload:', payload);

			try {
				const response = await $.post(ajax_obj, { action: "PHR_demographics_submit", ...payload });
				console.log('Demographics submission response:', response);
				if (response.success) {
					$('.create-abha-section').hide();
					fetchSuggestion($transactionIdField.val().trim());
					$suggestionSection.show();
					showMessage(response.data?.message || "Demographics submitted successfully.", 'success');
				} else {
					showMessage(response.data?.data || "Failed to submit demographics.", 'error');
				}
			} catch (err) {
				console.error("Error:", err);
				showMessage(`Error: ${err.message}`, 'error');
			} finally {
				toggleLoading(false);
			}
		}
	});

	//  Login PHR address
	async function handlePHRLogin(transactionId, phrAddress, already_registered = true) {
		if (!phrAddress) {
			return showMessage('PHR Address is required.', 'error');
		}

		const loginPayload = {
			transaction_id: transactionId,
			phr_address: phrAddress,
			is_health_number: false,
			already_registered,
		};
		console.log('PHR Login Payload:', loginPayload);

		try {
			toggleLoading(true);
			const loginResponse = await $.post(ajax_obj, { action: 'login_phr_address', ...loginPayload });
			console.log('PHR Login Response:', loginResponse);
			if (loginResponse.success) {
				const imageUrl = loginResponse.data?.image_url;
				if (imageUrl)
					displayImagePreview(imageUrl);
				else
					showMessage('Login successful, but no image was returned.', 'success');

				$suggestionSection.hide();
			} else {
				showMessage(loginResponse.data?.message || 'Login failed.', 'error');
			}
		} catch (err) {
			console.error('PHR Login Error:', err);
			showMessage(`Error: ${err.message || 'An unexpected error occurred.'}`, 'error');
		} finally {
			toggleLoading(false);
		}
	}

	// Fetch Suggestion
	async function fetchSuggestion(transaction_id) {
		try {
			const response = await $.get(ajax_obj, { action: "get_PHR_suggestion", transaction_id });

			console.log('Fetch Suggestion Response:', response);
			if (response.success) {
				const suggestions = response.data.suggestions;
				$suggestionList.empty();
				suggestions.forEach(name => {
					const $suggestionItem = $(`
                        <div class="suggestion-item" data-address=${name}>
                            ${name}
                        </div>`);
					$suggestionList.append($suggestionItem);
					$suggestionItem.on('click', function () {
						$('.suggestion-item').removeClass('selected-address');
						$(this).addClass('selected-address');
					});
				});
			} else {
				console.log(`Error fetching suggestions: ${response.data.message}`);
			}
		} catch (error) {
			console.log("Unexpected error while fetching suggestions:", error);
		}
	}

	$('#getSuggestion').on('click', () => {
		fetchSuggestion($transactionIdField.val().trim());
	});

	$('#submitSuggestion').on('click', async function () {
		const $transactionId = $transactionIdField.val().trim();
		console.log('Submitting suggestion for Transaction ID:', $transactionId);
		const $phrAddress = $('.suggestion-list').find('.selected-address').data('address');
		if (!$phrAddress) {
			return showMessage('Please select a PHR Address before submitting.', 'error');
		}
		await handlePHRLogin($transactionId, $phrAddress, false);
	});
});