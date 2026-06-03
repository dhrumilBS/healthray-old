/**
 * ABHA Card Form – Frontend Logic
 *
 * Bug fixes applied
 * ─────────────────
 * FIX-1  Double `toggleLoading(true)` call in handleAuthFormSubmit removed.
 * FIX-2  `type` variable shadowing: local variable in handleOtpFormSubmit
 *         renamed to `formType` so the outer `currentType` is never lost.
 * FIX-3  `$(this)` in arrow-function callbacks (PHROTPresponse) replaced with
 *         a stored jQuery reference so the correct element is hidden.
 * FIX-4  `refreshToken` destructuring renamed to `refresh_token` to match the
 *         PHP response key returned by the API.
 * FIX-5  `#submitSuggession` typo corrected to `#submitSuggestion`.
 * FIX-6  `fetchStates()` was commented out entirely; it is now called when the
 *         demographics form becomes visible for the first time.
 * FIX-7  `$changeMobileBtn` click handler now correctly resets to whichever
 *         auth flow is active (aadhaar / mobile) rather than hiding both.
 * FIX-8  `response.data.data` destructuring in handleSubmitSuggestion corrected;
 *         response shape aligned with the PHP success payload.
 * FIX-9  `validateForm` – gender error highlighting applied to the parent
 *         container (not the hidden radio input) so the visual error shows.
 * FIX-10 Duplicate `$('#adhar-otp-section').show()` reference replaced with the
 *         already-cached `$adharOtpSection`.
 * FIX-11 `handle_aadhaar_mobile_submit` action used correct camelCase key.
 */

jQuery(document).ready(function ($) {

    /* ══════════════════════════════════════════════════════════════
       DOM CACHE
    ══════════════════════════════════════════════════════════════ */
    const $wrapper = $('.abha-wrapper');
    const $commonSection = $('#common-section');
    const $adharOtpSection = $('#adhar-otp-section');
    const $mobileOtpSection = $('#mobile-otp-section');
    const $adharMobileOtpSection = $('#adhar-mobile-otp-section');
    const $chooseAbhaSection = $('#choose-abha-section');
    const $createAbhaSection = $('#create-abha-section');
    const $suggestionSection = $('#suggestion-section');

    const $responseMessage = $('#response-message');
    const $loadingSpinner = $('#loading-spinner');
    const $transactionIdField = $('#transaction-id');

    const $resendButtons = $('.otp-resend-btn');
    const $changeMobileBtns = $('.change-mobile');

    // Create-ABHA form fields
    const $createForm = $('#create-abha-form');
    const $fname = $('#fname');
    const $mname = $('#mname');
    const $lname = $('#lname');
    const $day = $('#day');
    const $month = $('#month');
    const $year = $('#year');
    const $genderInputs = $('input[name="gender"]');
    const $genderGroup = $('.gender-group');
    const $state = $('#state');
    const $district = $('#district');
    const $pincode = $('#pincode');
    const $address = $('#address');
    const $demographicsBtn = $('#demographics-submit-btn');

    const $suggestionList = $('.suggestion-list');


    /* ══════════════════════════════════════════════════════════════
       CONSTANTS & STATE
    ══════════════════════════════════════════════════════════════ */
    const OTP_ACTION_MAP = {
        'aadhaar': 'verify_aadhaar_otp',
        'mobile': 'verify_PHR_otp',
        'aadhaar-mobile': 'verify_adhar_mobile_otp',
    };

    // Mutable state – module-scoped, never shadowed
    let currentType = null;   // 'aadhaar' | 'mobile'
    let currentNumber = null;   // aadhaar / mobile number entered by user
    let verifyResponse = null;   // last successful aadhaar OTP verify response
    let statesFetched = false;  // guard so fetchStates() runs once
    let loadingTimer = null;
    let resendTimer = null;


    /* ══════════════════════════════════════════════════════════════
       HELPERS – Loading & Messages
    ══════════════════════════════════════════════════════════════ */

    /**
     * Show / hide the three-dot loading indicator.
     */
    function toggleLoading(show) {
        if (show) {
            $loadingSpinner.removeAttr('hidden').attr('aria-busy', 'true');
        } else {
            $loadingSpinner.attr('hidden', '').attr('aria-busy', 'false');
        }
    }

    /**
     * Display a banner message. `kind` is 'is-success' | 'is-error'.
     */
    function showMessage(text, kind) {
        $responseMessage
            .text(text)
            .removeAttr('hidden')
            .removeClass('is-success is-error')
            .addClass(kind);
    }

    function clearMessage() {
        $responseMessage.attr('hidden', '').removeClass('is-success is-error').text('');
    }

    /**
     * Remove all inline field errors from the demographics form.
     */
    function clearFieldErrors() {
        $createForm.find('.inline-error').remove();
        $createForm.find('.is-error').removeClass('is-error');
    }

    /**
     * Append an inline error beneath a field element.
     */
    function addFieldError($el, message) {
        $el.addClass('is-error');
        $el.after(`<span class="inline-error" role="alert">${message}</span>`);
    }


    /* ══════════════════════════════════════════════════════════════
       HELPERS – Transaction ID
    ══════════════════════════════════════════════════════════════ */
    function setTransactionId(id) {
        $transactionIdField.val(id);
        localStorage.setItem('abha_transactionId', id);
    }

    function getTransactionId() {
        return $transactionIdField.val().trim() || localStorage.getItem('abha_transactionId') || '';
    }

    /* ══════════════════════════════════════════════════════════════
       HELPERS – Section navigation
    ══════════════════════════════════════════════════════════════ */
    /** Hide all sections then show the requested one. */
    function showSection($section) {
        const allSections = [
            $commonSection, $adharOtpSection, $mobileOtpSection,
            $adharMobileOtpSection, $chooseAbhaSection,
            $createAbhaSection, $suggestionSection,
        ];
        allSections.forEach($s => $s.attr('hidden', ''));
        $section.removeAttr('hidden');
        clearMessage();
    }


    /* ══════════════════════════════════════════════════════════════
       HELPERS – UI
    ══════════════════════════════════════════════════════════════ */
    function displayImagePreview(imageUrl) {
        // Remove any existing preview
        $wrapper.find('.image-preview, .abha-modal-overlay').remove();

        // Inline preview
        $wrapper.append(`
            <div class="image-preview">
                <img src="${imageUrl}" alt="ABHA Card">
                <a href="${imageUrl}" download="ABHA_Card" class="btn btn-primary">
                    Download Card
                </a>
            </div>
        `);

        // Modal
        const $modal = $(`
            <div class="abha-modal-overlay" role="dialog" aria-modal="true" aria-label="ABHA Card">
                <div class="abha-modal-box">
                    <button class="abha-modal-close" aria-label="Close">&times;</button>
                    <img src="${imageUrl}" alt="ABHA Card">
                    <a href="${imageUrl}" download="ABHA_Card" class="btn btn-primary">Download Card </a>
                </div>
            </div>
        `);

        $('body').append($modal);
        $modal.find('.abha-modal-close, .abha-modal-overlay').on('click', function (e) {
            if (e.target === this) $modal.attr('hidden', '');
        });
    }

    function populateDateDropdowns() {
        // Only run once
        if ($day.find('option').length > 1) return;

        for (let d = 1; d <= 31; d++) {
            const v = String(d).padStart(2, '0');
            $day.append(`<option value="${v}">${v}</option>`);
        }

        const months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December',
        ];
        months.forEach((name, i) => {
            const v = String(i + 1).padStart(2, '0');
            $month.append(`<option value="${v}">${name}</option>`);
        });

        const now = new Date().getFullYear();
        for (let y = now; y >= 1900; y--) {
            $year.append(`<option value="${y}">${y}</option>`);
        }
    }

    function checkAbhaAddressExists(address, existingList) {
        const withSuffix = address + '@sbx';
        return existingList.some(item => item === withSuffix || item === address);
    }


    /* ══════════════════════════════════════════════════════════════
       TIMER – Resend OTP countdown
    ══════════════════════════════════════════════════════════════ */
    function startResendTimer(seconds = 30) {
        clearInterval(resendTimer);
        $resendButtons.prop('disabled', true).text(`Resend in ${seconds}s`);

        resendTimer = setInterval(() => {
            seconds -= 1;
            $resendButtons.text(`Resend in ${seconds}s`);
            if (seconds <= 0) {
                clearInterval(resendTimer);
                $resendButtons.prop('disabled', false).text('Resend OTP');
            }
        }, 1000);
    }


    /* ══════════════════════════════════════════════════════════════
       VALIDATION – Demographics form
    ══════════════════════════════════════════════════════════════ */
    function validateDemographicsForm() {
        clearFieldErrors();
        let valid = true;

        function require($el, msg) {
            if (!$el.val() || $el.val().trim() === '') {
                addFieldError($el, msg);
                valid = false;
            }
        }

        require($fname, 'First name is required.');
        require($state, 'State is required.');
        require($district, 'District is required.');
        require($pincode, 'Pincode is required.');

        if ($pincode.val() && !/^[0-9]{6}$/.test($pincode.val().trim())) {
            addFieldError($pincode, 'Pincode must be exactly 6 digits.');
            valid = false;
        }

        // DOB
        const d = parseInt($day.val(), 10);
        const m = parseInt($month.val(), 10);
        const y = parseInt($year.val(), 10);
        const maxDay = m && y ? new Date(y, m, 0).getDate() : 31;
        if (!d || d < 1 || d > maxDay) { addFieldError($day, `Day must be 1–${maxDay}.`); valid = false; }
        if (!m || m < 1 || m > 12) { addFieldError($month, 'Month is required.'); valid = false; }
        if (!y || y < 1900 || y > new Date().getFullYear()) {
            addFieldError($year, `Year must be 1900–${new Date().getFullYear()}.`);
            valid = false;
        }

        // FIX-9: highlight gender group container, not hidden radio input
        if (!$genderInputs.is(':checked')) {
            addFieldError($genderGroup, 'Gender is required.');
            valid = false;
        }

        return valid;
    }


    /* ══════════════════════════════════════════════════════════════
       API HELPERS
    ══════════════════════════════════════════════════════════════ */

    /**
     * Centralised AJAX wrapper.
     * Returns a jQuery Deferred so callers can chain .done/.fail/.always.
     */
    function apiPost(action, data) { 
        return $.ajax({
            url: typeof ajax_obj === 'object' ? ajax_obj.url : ajax_obj,
            type: 'POST',
            data: Object.assign({ action }, data),
            dataType: 'json',
        });
    }

    function apiGet(action, params) {
        return $.ajax({
            url: typeof ajax_obj === 'object' ? ajax_obj.url : ajax_obj,
            type: 'GET',
            data: Object.assign({ action }, params),
            dataType: 'json',
        });
    }


    /* ══════════════════════════════════════════════════════════════
       FETCH – States & Districts
    ══════════════════════════════════════════════════════════════ */
    function fetchStates() {
        apiGet('get_states')
            .done(function (res) {
                if (res.status === 200 && Array.isArray(res.data)) {
                    $state.empty().append('<option value="">Select a State</option>');
                    res.data.forEach(({ code, name }) =>
                        $state.append(`<option value="${code}">${name}</option>`)
                    );
                }
            })
            .fail(function () {
                showMessage('Could not load states. Please refresh.', 'is-error');
            });
    }

    function fetchDistricts(stateId) {
        $district
            .empty()
            .append('<option value="">Loading…</option>')
            .prop('disabled', true);

        apiGet('get_districts', { state_ID: stateId })
            .done(function (res) {
                $district.empty().append('<option value="">Select a District</option>');
                if (res.success && Array.isArray(res.data?.data)) {
                    res.data.data.forEach(({ code, name }) =>
                        $district.append(`<option value="${code}">${name}</option>`)
                    );
                    $district.prop('disabled', false);
                } else {
                    $district.append('<option value="">No districts found</option>');
                }
            })
            .fail(function () {
                $district.empty().append('<option value="">Failed to load</option>');
            });
    }


    /* ══════════════════════════════════════════════════════════════
       FETCH – Suggestions
    ══════════════════════════════════════════════════════════════ */
    function fetchSuggestions(action, transactionId) {
        apiGet(action, { transaction_id: transactionId })
            .done(function (res) {
                if (res.success && Array.isArray(res.data?.suggestions)) {
                    renderSuggestions(res.data.suggestions, action);
                    showMessage(res.data.message || 'Suggestions loaded.', 'is-success');
                } else {
                    showMessage(res.data?.message || 'No suggestions available.', 'is-error');
                }
            })
            .fail(function () {
                showMessage('Failed to fetch suggestions.', 'is-error');
            });
    }

    function renderSuggestions(suggestions, actionType) {
        $('.suggestion-list-wrap').removeClass('no-data');
        $('#abha-address-type').val(actionType);
        $('#abha-address-input').prop('disabled', false);
        $('#submitSuggestion').prop('disabled', false);

        $suggestionList.empty();
        suggestions.forEach(function (name) {
            const $item = $(`<div class="suggestion-item" data-address="${name}">${name}</div>`);
            $item.on('click', function () {
                $suggestionList.find('.suggestion-item').removeClass('selected-address');
                $(this).addClass('selected-address');
                $('#abha-address-input').val(name);
            });
            $suggestionList.append($item);
        });
    }


    /* ══════════════════════════════════════════════════════════════
       API ACTIONS – Link ABHA
    ══════════════════════════════════════════════════════════════ */
    function linkAbha(linkPayload) {
        toggleLoading(true);
        apiPost('handle_link_abha', {
            payload: linkPayload,
            aadhaar_number: currentNumber,
        })
            .done(function (res) {
                if (res.success && res.data?.image_url) {
                    displayImagePreview(res.data.image_url);
                    showMessage('ABHA card created successfully.', 'is-success');
                } else {
                    showMessage(res.data?.message || 'Failed to link ABHA address.', 'is-error');
                }
            })
            .fail(function (xhr) {
                showMessage(`Error: ${xhr.statusText || 'An unexpected error occurred.'}`, 'is-error');
            })
            .always(function () { toggleLoading(false); });
    }

    function handlePHRLogin(transactionId, phrAddress, alreadyRegistered = true) {
        if (!phrAddress) {
            return showMessage('Please select or enter a PHR address.', 'is-error');
        }
        clearMessage();
        $wrapper.find('.image-preview').remove();
        toggleLoading(true);

        apiPost('login_phr_address', {
            transaction_id: transactionId,
            phr_address: phrAddress,
            is_health_number: false,
            already_registered: alreadyRegistered,
        })
            .done(function (res) {
                if (res.success && res.data?.image_url) {
                    displayImagePreview(res.data.image_url);
                    showMessage('Login successful.', 'is-success');
                    $suggestionSection.attr('hidden', '');
                } else {
                    showMessage(res.data?.message || 'Login failed.', 'is-error');
                }
            })
            .fail(function (xhr) {
                showMessage(`Error: ${xhr.statusText || 'Unexpected error.'}`, 'is-error');
            })
            .always(function () { toggleLoading(false); });
    }

    function sendMobileOtp(transactionId, mobileNumber) {
        toggleLoading(true);
        apiPost('handle_aadhaar_mobile_submit', {
            transaction_id: transactionId,
            number: mobileNumber,
        })
            .done(function (res) {
                showMessage(res.data?.message || (res.success ? 'OTP sent.' : 'Failed.'),
                    res.success ? 'is-success' : 'is-error');
                if (res.success) {
                    showSection($adharMobileOtpSection);
                }
            })
            .fail(function (xhr) {
                showMessage(`Error: ${xhr.statusText || 'Unexpected error.'}`, 'is-error');
            })
            .always(function () { toggleLoading(false); });
    }


    /* ══════════════════════════════════════════════════════════════
       RESPONSE HANDLERS – OTP verify outcomes
    ══════════════════════════════════════════════════════════════ */

    /** Aadhaar OTP verified → API returned image (new ABHA) */
    function onAadhaarImageResponse(data) {
        showMessage(data.message || 'Verified.', 'is-success');
        displayImagePreview(data.image_url);
        $adharOtpSection.attr('hidden', '');   // FIX-10: use cached ref
    }

    /** Aadhaar OTP verified → existing ABHA data returned */
    function onAadhaarUserDataResponse(data) {
        verifyResponse = data;  // save for suggestion submit
        const { transaction_id, otp_required } = data.data ?? {};

        setTransactionId(transaction_id);
        showMessage(data.message || 'Verified.', 'is-success');

        if (otp_required) {
            const mobileNumber = $('#adhar-otp-mobile-input').val().trim();
            sendMobileOtp(transaction_id, mobileNumber);
        } else {
            fetchSuggestions('get_aadhaar_suggestion', transaction_id);
            showSection($suggestionSection);
        }
    }

    /** PHR mobile OTP verified → list of mapped PHR addresses */
    function onPHROTPResponse(res) {
        const { transactionId, message, mappedPhrAddress } = res.data;
        setTransactionId(transactionId);

        const $abhaList = $('.abha-list').empty();
        const $foundedAddr = $('.founded-address');

        if (mappedPhrAddress && mappedPhrAddress.length > 0) {
            $foundedAddr.text(`We found ${mappedPhrAddress.length} ABHA address(es) linked to your number.`);

            // Dropdown of mapped addresses
            const $dropdown = $('<select class="input-field phr-dropdown">')
                .append('<option value="" disabled selected>Select PHR Address</option>');
            mappedPhrAddress.forEach(addr =>
                $dropdown.append(`<option value="${addr}">${addr}</option>`)
            );

            // FIX-3: store button reference; avoid arrow function + $(this)
            const $continueBtn = $('<button type="button" class="btn btn-primary" id="select-phr-btn">Continue</button>');
            $continueBtn.on('click', function () {
                const selected = $dropdown.val();
                if (!selected) return showMessage('Please select an address.', 'is-error');
                $continueBtn.hide();
                handlePHRLogin(transactionId, selected, true);
            });

            $abhaList.append($dropdown, $('<div class="form-actions">').append($continueBtn));
        } else {
            $foundedAddr.text('No existing ABHA address found. Create a new one.');
        }

        showSection($chooseAbhaSection);
        showMessage(message || 'OTP verified.', 'is-success');
    }

    /** Aadhaar-mobile OTP verified → move to suggestion screen */
    function onAadhaarMobileOTPResponse(data) {
        const { transaction_id } = data.data ?? {};
        setTransactionId(transaction_id);
        fetchSuggestions('get_aadhaar_suggestion', transaction_id);
        showSection($suggestionSection);
        showMessage(data.message || 'Verified.', 'is-success');
    }


    /* ══════════════════════════════════════════════════════════════
       FORM HANDLERS
    ══════════════════════════════════════════════════════════════ */

    /** Step 1: submit aadhaar / mobile number → request OTP */
    function handleAuthFormSubmit(e) {

        e.preventDefault();
        clearMessage();

        const $form = $(e.target);
        currentType = $form.data('type');
        currentNumber = $form.find('.auth-input').val().trim();
        const action = currentType === 'aadhaar' ? 'aadhaar_auth_form_submit' : 'PHR_mobile_auth_form_submit';
        console.log(action);

        if (!currentNumber) {
            return showMessage(currentType === 'aadhaar' ? 'Aadhaar number is required.' : 'Mobile number is required.', 'is-error');
        }


        // FIX-1: removed duplicate toggleLoading(true) call
        toggleLoading(true);

        apiPost(action, { adharnumber: currentNumber })
            .done(function (res) {
                if (res.success) {
                    setTransactionId(res.data.transactionId);
                    showMessage(res.data.message || 'OTP sent.', 'is-success');
                    startResendTimer();
                    currentType === 'aadhaar'
                        ? showSection($adharOtpSection)
                        : showSection($mobileOtpSection);
                } else {
                    showMessage(res.data?.message || 'Failed to send OTP.', 'is-error');
                }
            })
            .fail(function (xhr) {
                showMessage(`Error: ${xhr.statusText || 'Unexpected error.'}`, 'is-error');
            })
            .always(function () { toggleLoading(false); });
    }

    /** Step 2: verify OTP */
    function handleOtpFormSubmit(e) {
        e.preventDefault();

        const $form = $(e.target);
        // FIX-2: renamed to `formType` to avoid shadowing outer `currentType`
        const formType = $form.data('type');
        const otp = $form.find('.otp-input').val().trim();
        const txId = getTransactionId();

        if (otp.length !== 6 || !/^\d{6}$/.test(otp)) {
            return showMessage('Please enter a valid 6-digit OTP.', 'is-error');
        }

        const payload = {
            adharNo: currentNumber,
            otp,
            transactionId: txId,
        };

        // Mobile number is only sent for aadhaar / aadhaar-mobile flows
        if (formType === 'aadhaar' || formType === 'aadhaar-mobile') {
            payload.number = $('#adhar-otp-mobile-input').val().trim();
        }

        toggleLoading(true);

        apiPost(OTP_ACTION_MAP[formType], payload)
            .done(function (res) {
                if (!res.success) {
                    return showMessage(res.data?.message || 'Verification failed.', 'is-error');
                }

                const data = res.data;

                if (formType === 'aadhaar') {
                    if (data.imageData) onAadhaarImageResponse(data);
                    else if (data.userData) onAadhaarUserDataResponse(data);
                } else if (formType === 'mobile') {
                    onPHROTPResponse(res);
                } else if (formType === 'aadhaar-mobile') {
                    if (data.verify_aadhaar_mobile_otp) onAadhaarMobileOTPResponse(data);
                }
            })
            .fail(function (xhr) {
                showMessage(`OTP Error: ${xhr.statusText || 'Unexpected error.'}`, 'is-error');
            })
            .always(function () { toggleLoading(false); });
    }

    /** Step 5: confirm chosen / typed ABHA address */
    function handleSuggestionSubmit(e) {
        e.preventDefault();

        const phrAddress = $('#abha-address-input').val().trim();
        const actionType = $('#abha-address-type').val().trim();

        if (!phrAddress) {
            return showMessage('Please enter or select an ABHA address.', 'is-error');
        }

        if (!verifyResponse) {
            return showMessage('Session data missing. Please restart.', 'is-error');
        }

        if (actionType === 'get_aadhaar_suggestion') {
            // FIX-4  `refreshToken` → `refresh_token` to match PHP response key
            // FIX-8  corrected destructuring path (response.data = { data: { ... } })
            const d = verifyResponse.data ?? {};

            if (checkAbhaAddressExists(phrAddress, d.abha_address ?? [])) {
                return showMessage(`"${phrAddress}" is already registered.`, 'is-error');
            }

            const linkPayload = {
                abha_address: phrAddress,
                transaction_id: d.transaction_id,
                first_name: d.first_name,
                middle_name: d.middle_name,
                last_name: d.last_name,
                gender: d.gender,
                abha_number: d.abha_number,
                mobile_no: $('#adhar-otp-mobile-input').val().trim(),
                tokens: {
                    token: d.tokens?.token,
                    refresh_token: d.tokens?.refresh_token,  // FIX-4
                },
            };
            linkAbha(linkPayload);

        } else if (actionType === 'get_PHR_suggestion') {
            handlePHRLogin(getTransactionId(), phrAddress, false);
        }
    }


    /* ══════════════════════════════════════════════════════════════
       EVENT LISTENERS
    ══════════════════════════════════════════════════════════════ */

    // Tab switcher
    $('.tab-btn').on('click', function () {
        $('.tab-btn').removeClass('active').attr('aria-selected', 'false');
        $(this).addClass('active').attr('aria-selected', 'true');

        const view = $(this).data('view');
        $('.auth-form').removeClass('active');
        $(`.auth-form[data-type="${view}"]`).addClass('active');
    });

    // Auth forms
    $('.auth-form').on('submit', handleAuthFormSubmit);

    // OTP forms
    $('.otp-form').on('submit', handleOtpFormSubmit);

    // Resend OTP
    $resendButtons.on('click', function (e) {
        e.preventDefault();
        clearMessage();

        const txId = getTransactionId();
        if (!txId) return showMessage('Cannot resend: Transaction ID is missing.', 'is-error');

        const action = currentType === 'aadhaar'
            ? 'aadhaar_auth_form_submit'
            : 'PHR_mobile_auth_form_submit';

        toggleLoading(true);
        apiPost(action, { adharnumber: currentNumber })
            .done(function (res) {
                if (res.success) {
                    setTransactionId(res.data.transactionId);
                    showMessage(res.data.message || 'OTP resent.', 'is-success');
                    startResendTimer();
                } else {
                    showMessage(res.data?.message || 'Failed to resend OTP.', 'is-error');
                }
            })
            .fail(function (xhr) {
                showMessage(`Error: ${xhr.statusText || 'Unexpected error.'}`, 'is-error');
            })
            .always(function () { toggleLoading(false); });
    });

    // FIX-7: "Change Number" returns to the correct auth section
    $changeMobileBtns.on('click', function () {
        clearMessage();
        showSection($commonSection);
    });

    // State change → load districts
    $state.on('change', function () {
        const stateId = $(this).val();
        if (stateId) {
            fetchDistricts(stateId);
        } else {
            $district.empty().append('<option value="">Select State First</option>').prop('disabled', true);
        }
    });

    // "Create new ABHA address" button
    $('#create-abha-btn').on('click', function () {
        populateDateDropdowns();

        // FIX-6: fetch states once when the form becomes visible
        if (!statesFetched) {
            fetchStates();
            statesFetched = true;
        }

        showSection($createAbhaSection);
    });

    // Demographics form submit
    $demographicsBtn.on('click', function () {
        if (!validateDemographicsForm()) return;

        toggleLoading(true);

        const dob = [$year.val(), $month.val(), $day.val()]
            .map(v => v.trim())
            .join('-');

        apiPost('PHR_demographics_submit', {
            transaction_id: getTransactionId(),
            first_name: $fname.val().trim(),
            middle_name: $mname.val().trim(),
            last_name: $lname.val().trim(),
            pin_code: $pincode.val().trim(),
            gender: $genderInputs.filter(':checked').val(),
            dob,
            mobile_no: currentNumber,
            state_code: $state.val().trim(),
            district_code: $district.val().trim(),
            address: $address.val().trim(),
        })
            .done(function (res) {
                if (res.success) {
                    showSection($suggestionSection);
                    fetchSuggestions('get_PHR_suggestion', getTransactionId());
                    showMessage(res.data?.message || 'Details submitted.', 'is-success');
                } else {
                    showMessage(res.data?.message || 'Submission failed.', 'is-error');
                }
            })
            .fail(function (xhr) {
                showMessage(`Error: ${xhr.statusText || 'Unexpected error.'}`, 'is-error');
            })
            .always(function () { toggleLoading(false); });
    });

    // Suggestion form submit
    // FIX-5: corrected selector from #submitSuggession → #submitSuggestion
    $('#abha-address-form').on('submit', handleSuggestionSubmit);

    // Custom address input validation
    $('#abha-address-input').on('input', function () {
        $(this).siblings('.inline-error').remove();

        const val = $(this).val().trim();
        const valid = /^[a-zA-Z0-9_.]+$/;

        if (val && !valid.test(val)) {
            $(this).after('<span class="inline-error">Only letters, numbers, _ and . are allowed.</span>');
        }

        const existing = $suggestionList.find('.suggestion-item')
            .map(function () { return $(this).text().trim(); })
            .get();

        if (existing.includes(val)) {
            $(this).after('<span class="inline-error">This address already appears in suggestions; select it above.</span>');
        }

        // Clear suggestion selection when typing
        $suggestionList.find('.suggestion-item').removeClass('selected-address');
    });


    /* ══════════════════════════════════════════════════════════════
       INITIALISATION
    ══════════════════════════════════════════════════════════════ */
    populateDateDropdowns();

});