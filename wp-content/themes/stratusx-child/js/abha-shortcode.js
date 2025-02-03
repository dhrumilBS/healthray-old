jQuery(document).ready(($) => {
	const $commonSection = $('#common-section');
	const $adharOtpSection = $('#adhar-otp-section');
	const $mobileOtpSection = $('#mobile-otp-section');
	const $responseMessage = $('#response-message');
	const $loadingSpinner = $('#loading-spinner');
	const $resendButton = $('.otp-resend-btn');
	const $changeMobileBtn = $('.change-mobile');
	const $transactionIdField = $('#transaction-id');
	const $state = $("#state");
	const $district = $("#district");
	const typeToAction = {
		aadhaar: 'verify_aadhaar_otp',
		mobile: 'verify_PHR_otp',
		'aadhaar-mobile': 'verify_adhar_mobile_otp'
	};

	let type = null;
	let ADHARNUMBER = null;
	let mobileNumber = null;
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
		}, 250);
	}

	function stopLoadingAnimation($element) {
		clearInterval(loadingInterval);
		$element.text('').hide();
	}

	function showMessage(message, type) {
		$responseMessage.text(message).removeClass('success error').addClass(type).show();
	}

	function startResendTimer() {
		let countdown = 30;
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
		const payload = { value: inputField };
		const action = type === 'aadhaar' ? 'aadhaar_auth_form_submit' : 'PHR_mobile_auth_form_submit';
		console.log(action, "Field: ", inputField);
		console.log('Submitting payload:', payload);

		try {
			const response = await $.post(ajax_obj, { action, ...payload });
			if (response.success) {
				console.log("response", response);
				console.log("response.data", response.data);
				const { transactionId, message } = response.data;
				localStorage.setItem('transactionId', transactionId);
				$transactionIdField.val(transactionId);
				type === 'aadhaar' ? ADHARNUMBER = inputField : mobileNumber = inputField;
				showMessage(message, 'success');
				type === 'aadhaar' ? $adharOtpSection.show() : $mobileOtpSection.show();
				$commonSection.hide();
				startResendTimer();
			} else {
				console.log(response);
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

	// Verify OTP form submit handler
	async function handleOtpFormSubmit(e) {
		e.preventDefault();
		const $form = $(e.target);
		const otp = $form.find('.otp-input').val().trim();
		const transactionId = localStorage.getItem('transactionId');
		const type = $form.data('type');
		const action = typeToAction[type];
		// Build payload based on type
		const payload = { otp, transactionId, action };
		// Add number for aadhaar type
		if (type === 'aadhaar' || type === 'aadhaar-mobile') {
			payload.number = $('#adhar-otp-mobile-input').val().trim();
		}
		// Validate OTP and number
		if (otp.length !== 6 || (type !== 'mobile' && type !== 'aadhaar' && type !== 'aadhaar-mobile' && !/^\d{10}$/.test(payload.number))) {
			return showMessage('Invalid input.', 'error');
		}
		try {
			console.log('2. Submitting OTP Payload:', payload);
			toggleLoading(true);
			const response = await $.post(ajax_obj, { action, ...payload });
			console.log('4. Verify OTP Response:', response);

			if (response.success && response.data.userData) {
				const { message, data: { transaction_id, otp_required, first_name, last_name, middle_name, abha_address, abha_number, tokens: { token, refreshToken }, gender } } = response.data;
				localStorage.setItem('transactionId', transaction_id);

				if (otp_required) {
					const number = $('#adhar-otp-mobile-input').val().trim();
					const payload = { transaction_id, number }
					console.log(payload);
					await aadhaar_mobile_submit(payload, $form);
					return showMessage(message, 'success');
				} else if (otp_required === false) {
					fetchSuggestion("get_aadhaar_suggestion", transaction_id);
					$suggestionSection.show();
					// await processUserData(payload, response.data);
					return showMessage("message", 'success');
				}

				console.log(response.data);

				if (response.data.imageData) {
					(response.data.image_url);
					showMessage(message, 'success');
				}
			}

			if (response.data.verify_aadhaar_mobile_otp) {
				localStorage.setItem('transactionId', response.data.transactionId);
				showMessage(response.data.message, 'success');
				fetchSuggestion("get_aadhaar_suggestion", response.data.transactionId);
				$suggestionSection.show();
			}
		} catch (err) {
			console.error(`'OTP Error:', ${err}`);
			showMessage(`E r ror: ${err.message || 'An unexpected error occurred.'}`, 'error');
		} finally {
			toggleLoading(false);
		}
	}
	$('.otp-form').on('submit', handleOtpFormSubmit);

	async function aadhaar_mobile_submit(payload, $form) {
		try {
			toggleLoading(true);
			const response = await $.post(ajax_obj, { action: 'handle_aadhaar_mobile_submit', ...payload });
			const APIResponse = response.data;
			console.log(APIResponse);
			if (response.success) {
				$form.hide();
				showMessage(APIResponse.message, 'success');
				$('#adhar-mobile-otp-section').show();
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
	async function processUserData(payload) {
		try {
			toggleLoading(true);
			const action = "handle_link_abha";
			const response = await $.post(ajax_obj, { action, payload });
			if (response.data.image_url) {
				displayImagePreview(image_url);
				showMessage(message, 'success');
			}
		}
		catch (err) {
			console.error('User Data Error:', err);
			showMessage(`E r ror: ${err.message || 'An unexpected error occurred.'}`, 'error');

		}
		finally { toggleLoading(false); }
	}

	// Fetch Suggestion
	async function fetchSuggestion(action, transaction_id) {
		console.log('fetchSuggestion');
		try {
			const response = await $.get(ajax_obj, { action: action, transaction_id });

			console.log('Fetch suggestion Response:', response);
			if (response.success) {
				$('.suggestion-list-wrap').removeClass('no-data');
				$('#abha-address-input').attr('disabled', false);
				console.log("response.data", response.data);

				const suggestions = response.data.suggestions;
				$suggestionList.empty();
				suggestions.forEach(name => {
					const $suggestionItem = $(`<div class="suggestion-item" data-address=${name}> ${name} </div>`);
					$suggestionList.append($suggestionItem);
					$suggestionItem.on('click', function () {
						$('.suggestion-item').removeClass('selected-address');
						$('#abha-address-input').val(`${name}`);
						$(this).addClass('selected-address');
					});
				});
				showMessage(`${response.data.message}`, 'success');
			} else {
				console.log(`Error fetching suggestions: ${response.data.message}`);
				showMessage(`Error fetching suggestions: ${response.data.message}`, 'error');
			}
		} catch (error) {
			console.log(`Unexpected error while fetching suggestions: ${error}`);
			showMessage(`Unexpected error while fetching suggestions: ${error}`, 'error');
		}
	}
	$('#abha-address-input').on('input', function () {
		$('.suggestion-item').removeClass('selected-address');
		const inputValue = $(this).val().trim();
		const validRegex = /^[a-zA-Z0-9_.]+$/; // Allowed characters
		const existingValues = $('.suggestion-item').map(function () {
			return $(this).text().trim();
		}).get(); // Get all suggestion values
		$('.error-message').remove(); // Remove previous errors
		$('.suggestion-item').removeClass('selected-address');
		// Check if the input contains only valid characters
		if (inputValue && !validRegex.test(inputValue)) {
			$(this).after('<div class="error-message" style="color: red;">Invalid input! Only alphabets, numbers, _, and . are allowed.</div>');
		}

		// Check for duplicates in the suggestion list
		if (existingValues.includes(inputValue)) {
			$(this).after('<div class="error-message" style="color: red;">Duplicate entry! This address already exists.</div>');
		}
	});

	// Change Mobile Button Handler
	$changeMobileBtn.on('click', () => {
		$responseMessage.html('');
		$adharOtpSection.hide();
		$mobileOtpSection.hide();
		$commonSection.show();
	});

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
		console.log("mobileInputField: ", ADHARNUMBER);
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
		console.log("mobileInputField: ", ADHARNUMBER);

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
				mobile_no: mobileInputField,
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
					var action = "get_PHR_suggestion";
					fetchSuggestion(action, $transactionIdField.val().trim());
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
			if (loginResponse.success) {
				const imageUrl = loginResponse.data?.image_url;
				if (imageUrl)
					displayImagePreview(imageUrl);
				else
					showMessage('Login successful, but no image was returned.', 'success');

				$suggestionSection.hide();
			} else {
				console.log(loginResponse);

				showMessage(loginResponse.data?.message || 'Login failed.', 'error');
			}
		} catch (err) {
			console.error('PHR Login Error:', err);
			showMessage(`Error: ${err.message || 'An unexpected error occurred.'}`, 'error');
		} finally {
			toggleLoading(false);
		}
	}

	$('#getaadharmobilesuggestion').on('click', () => {
		console.log(localStorage.getItem('transactionId'));
		var action = "get_aadhaar_suggestion";
		fetchSuggestion(action, localStorage.getItem('transactionId'));
	});
	$('#getsuggestion').on('click', () => {
		var action = "get_PHR_suggestion";
		fetchSuggestion(action, localStorage.getItem('transactionId'));
	});

	$('#submitSuggession').on('click', async function () {
		const $transactionId = localStorage.getItem('transactionId');
		console.log('Submitting suggestion for Transaction ID:', $transactionId);
		const $phrAddress = $('.suggestion-list').find('.selected-address').data('address');
		if (!$phrAddress) {
			return showMessage('Please select a PHR Address before submitting.', 'error');
		}
		await handlePHRLogin($transactionId, $phrAddress, false);
	});
});