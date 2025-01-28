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
		console.log("1. Inside handleOtpFormSubmit for:", type);

		// Add number for aadhaar type
		if (type === 'aadhaar') {
			mobileNumber = $form.find('#adhar-otp-mobile-input').val().trim();
			payload.number = mobileNumber;
			console.log("2.", mobileNumber);
		}

		// Validate OTP and number
		if (otp.length !== 6 || (type !== 'mobile' && type !== 'aadhaar' && !/^\d{10}$/.test(payload.number))) {
			return showMessage('Invalid input.', 'error');
		}

		console.log('3. Submitting OTP Payload:', payload);

		try {
			toggleLoading(true);
			const response = await $.post(ajax_obj, { action, ...payload });
			// const response = {
			// 	"success": true,
			// 	"userData": true,
			// 	"data": {
			// 		"message": "This account already exist",
			// 		"data": {
			// 			"is_new": false,
			// 			"transaction_id": "8c83ad60-a802-405f-84de-6ce28d076e0e",
			// 			"otp_required": false,
			// 			"abha_number": "91562356614684",
			// 			"first_name": "Mangukiya",
			// 			"last_name": "Alkeshbhai",
			// 			"middle_name": "Dhrumil",
			// 			"dob": "30-06-1999",
			// 			"gender": "M",
			// 			"address": "33, Sonal Park Society, Ambatalawadi, Katargam, Surat City, Surat City, Surat, Gujarat",
			// 			"email": null,
			// 			"state": "GUJARAT",
			// 			"district": "SURAT",
			// 			"abha_address": [
			// 				"91562356614684@sbx"
			// 			],
			// 			"tokens": {
			// 				"token": "eyJhbGciOiJSUzUxMiJ9.eyJpc0t5Y1ZlcmlmaWVkIjp0cnVlLCJzdWIiOiI5MS01NjIzLTU2NjEtNDY4NCIsImNsaWVudElkIjoiYWJoYS1wcm9maWxlLWFwcC1hcGkiLCJzeXN0ZW0iOiJBQkhBLU4iLCJhY2NvdW50VHlwZSI6InN0YW5kYXJkIiwibW9iaWxlIjoiOTcyNTYxOTYzNCIsImFiaGFOdW1iZXIiOiI5MS01NjIzLTU2NjEtNDY4NCIsInByZWZlcnJlZEFiaGFBZGRyZXNzIjoiOTE1NjIzNTY2MTQ2ODRAc2J4IiwidHlwIjoiVHJhbnNhY3Rpb24iLCJleHAiOjE3MzgwNDE5NjIsImlhdCI6MTczODA0MDE2MiwidHhuSWQiOiI4YzgzYWQ2MC1hODAyLTQwNWYtODRkZS02Y2UyOGQwNzZlMGUifQ.a8CFetp_2r80bValtV7101MHtJhYCCokS1xBQCBt2Cb63xeR0sUe0p0tOK4Ro2o1kjH2egLd_QoZYJW0wmbDNl01nDIhjwq0ATxVYEX0EB_eKz0_gMf5WlvM4k5ajWi1xwlhlg__wN6Op2SUpRE5MSNFmQBLY9DBlwr-h-RtmNkMyD3curTWxI0VQnNuQUQYXAvORPPAGqhlk6kJzCJUAE39S4TH-36ehVfeMGllqcJdk0sF0XuitGRdVBf_OHKPcXYJZ9og8i928kUOhOYMroIlsDgHZWCnwKDjdztzX6DH9C6_3m2bie9eQeXqosjqKyytlgCp-WvyU3negtSlLY65BvTHvjyJBvgEbzB0tMxhmt09F1_x3g4T0k8BmWGNjug24DWI_PbJNkVnyjVN3btvLBwrnzJMG43E5K9kwhnS1T8_u20hsa2mr0B9Sv-ELy4QZPeJRCQ3TfYDuejCPqub7uOQZNndrVSrPFLnwJPg9mxT1-oH42n4jugq9hFtW0XIefTk3j5AiZ0tX3kHITdM3gTQyNm59VEdhZuhheAeuWb_pg1j8osNhW1oYED-Jqxido_ZyVFiA13TZIygl4xN5IF26vW7HWqauoGLki0T-92Drp7f68T_pD1O3Jem1M_V1YbwnITe6srM0MoR77V8WPCgSIxJoOtwBSTKYok",
			// 				"expiresIn": 1800,
			// 				"refreshToken": "eyJhbGciOiJSUzUxMiJ9.eyJzdWIiOiI5MS01NjIzLTU2NjEtNDY4NCIsImNsaWVudElkIjoiYWJoYS1wcm9maWxlLWFwcC1hcGkiLCJzeXN0ZW0iOiJBQkhBLU4iLCJ0eXAiOiJSZWZyZXNoIiwiZXhwIjoxNzM5MzM2MTYyLCJpYXQiOjE3MzgwNDAxNjJ9.iGCLHNj7J4Db2KqNTJonLqcEeYpxBQJNA1-5H5BLSHXSzcIDqVjqJDOTVxwSICMmgvvtAkL8HQYSn4z6QNgbP2H_yttkAHxLnGRaYv42lp3liVcLJZLLzrFnkppA2Ywr-CqkXZEp0qg9ftB9h29XwaJ7eLzM71yB6ArCsVQwrxslcyUGwZrAJdLx5bwU2JEutGXMg0xoV3rotOOJiolI9VfAi16cY79qxVhH7oie0bpGVE7IFPs0NepfI619Mdu1wziRlkLNgVK-7K_hqZ2c65KwZV5M6nRzeA6OJlc3GCie0f8soP2nZF3kBssMZizoXe_cTy9B8JfbQzH2Ge_UhL4EpTrxWRpQQ_BG5xCtA8KMXVZh4QusEic7xQALt71h1Xlv0vLlYwQt7cwiQ6MkyyiEhME6sqC8xIVhQmjgTrjZYeL5m3UT3k04ohIwSc9b7JJQyoOZZHeY3E0hFQkk3WQGEtsfGs0F1AkE7nUZr5VhBnzNOkeAruOyeqPOgpnOHRs1sogf-EUGuPLzIim7SxdPfjemE303_BsWFuS83UqygdrdgD1PqUEId6ZgogLQTb18jwZfYqajfwSlkNQsACt-cSISrcwV6exskmJe_d-yOYvKWwD9LhhPBpuMuVLniVyMZjJq7hi29MVlTvRtrXTNBOZsA_xDjPyzBgtbD4U",
			// 				"refreshExpiresIn": 1296000
			// 			}
			// 		}
			// 	}
			// }
			console.log('4. Verify OTP Response:', response);
			const { message, data: { transaction_id, otp_required, first_name, last_name, middle_name, abha_address, abha_number, tokens: { token, refreshToken }, gender } } = response.data;

			if (response.success && response.userData) {
				if (otp_required) {
					console.log("OTP is required. Handle OTP process.");
					showMessage(message, 'success');
				} else if (otp_required === false) {
					const payload = {
						is_new: 1,
						abha_address: abha_address[0],
						transaction_id,
						user_details: {
							first_name,
							middle_name,
							last_name,
							gender,
							abha_health_number: abha_number,
							mobile_no: mobileNumber,
							tokens: { token, refreshToken }
						}
					};
					console.log("(0) - payload link", payload);

					await processUserData(payload, data);
				}
				if (response.data.image_url) {
					displayImagePreview(image_url);
					showMessage(message, 'success');
				}
				localStorage.setItem('transactionId', transaction_id);
			} else {
				console.log("data", response.data);
				console.log("message", message);
				showMessage(message, 'error');
			}
		} catch (err) {
			console.error('OTP Error:', err);
			showMessage(`E r ror: ${err.message || 'An unexpected error occurred.'}`, 'error');
		} finally {
			toggleLoading(false);
		}
	}
	$('.otp-form').on('submit', handleOtpFormSubmit);

	async function processUserData(payload) {
		try {
			toggleLoading(true);
			const action = "hadle_link_abha";
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

		// if (validateForm()) {
		// 	toggleLoading(true);
		// 	const payload = {
		// 		transaction_id: $transactionIdField.val().trim(),
		// 		first_name: $fname.val().trim(),
		// 		middle_name: $mname.val().trim(),
		// 		last_name: $lname.val().trim(),
		// 		pin_code: $pincode.val().trim(),
		// 		gender: $gender.filter(":checked").val().trim(),
		// 		dob: `${$year.val().trim()}-${$month.val().trim()}-${$day.val().trim()}`,
		// 		mobile_no: mobileInputField,
		// 		state_code: $state.val().trim(),
		// 		district_code: $district.val().trim(),
		// 		address: $address.val().trim()
		// 	};

		// 	console.log('Submitting demographics payload:', payload);

		// 	try {
		// 		const response = await $.post(ajax_obj, { action: "PHR_demographics_submit", ...payload });
		// 		console.log('Demographics submission response:', response);
		// 		if (response.success) {
		// 			$('.create-abha-section').hide();
		// 			var action = "get_PHR_suggestion";
		// 			fetchSuggestion(action, $transactionIdField.val().trim());
		// 			$suggestionSection.show();
		// 			showMessage(response.data?.message || "Demographics submitted successfully.", 'success');
		// 		} else {
		// 			showMessage(response.data?.data || "Failed to submit demographics.", 'error');
		// 		}
		// 	} catch (err) {
		// 		console.error("Error:", err);
		// 		showMessage(`Error: ${err.message}`, 'error');
		// 	} finally {
		// 		toggleLoading(false);
		// 	}
		// }
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

	// Fetch Suggestion
	async function fetchSuggestion(action, transaction_id) {
		try {
			const response = await $.get(ajax_obj, { action: action, transaction_id });

			console.log('Fetch suggestion Response:', response);
			if (response.success) {
				const suggestions = response.data.suggestions.suggestion;
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

	$('#getaadharmobilesuggestion').on('click', () => {
		console.log("dsfs");
		var action = "get_aadhaar_suggestion";
		fetchSuggestion(action, $transactionIdField.val().trim());
	});
	$('#getsuggestion').on('click', () => {
		var action = "get_PHR_suggestion";
		fetchSuggestion(action, $transactionIdField.val().trim());
	});

	$('#submitSuggession').on('click', async function () {
		const $transactionId = $transactionIdField.val().trim();
		console.log('Submitting suggestion for Transaction ID:', $transactionId);
		const $phrAddress = $('.suggestion-list').find('.selected-address').data('address');
		if (!$phrAddress) {
			return showMessage('Please select a PHR Address before submitting.', 'error');
		}
		await handlePHRLogin($transactionId, $phrAddress, false);
	});
});