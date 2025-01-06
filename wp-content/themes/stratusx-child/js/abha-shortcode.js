jQuery(document).ready(($) => {
    function handleActionButtonClick(event) {
        const $button = $(event.target); // Get the clicked button
        $('.link-action-btn').removeClass('active'); // Remove active class from all buttons
        $button.addClass('active'); // Add active class to the clicked button

        const view = $button.data('view'); // Get the view type from the data attribute
        $('#common-section .form-field').removeClass('active').find('input').prop('disabled', true);
        $(`#common-section .${view}-field`).addClass('active').find('input').prop('disabled', false);

        $('#submit-button').text(`Submit ${view.charAt(0).toUpperCase() + view.slice(1)}`);
    }
    $('.link-action-btn').on('click', handleActionButtonClick);

    const $section = $('#common-section');
    const $form = $('#common-form');
    const $otpSection = $('#otp-section');
    const $responseMessage = $('#response-message');
    const $loadingSpinner = $('#loading-spinner');
    const $otpForm = $('#otp-form');
    const $resendButton = $('#resend-otp');
    const $changeMobileBtn = $("#change-mobile");

    let transaction_id = null;
    let resendTimer;

    // Form submission for Aadhaar
    $form.on('submit', async (e) => {
        e.preventDefault();
        $responseMessage.html('');
        toggleLoading(true);

        const aadhaar = $('#aadhaar-input').val().trim();
        if (aadhaar.length !== 12) {
            toggleLoading(false);
            return showError('Enter 12-Digit Aadhaar number.');
        }

        try {
            const response = await $.post(ajax_obj, {
                action: 'adhar_auth_form_submit',
                aadhaar
            });
            console.log(response);

            const APIResponse = response.data.message;
            if (APIResponse.status === 200) {
                transaction_id = APIResponse.data.transaction_id;
                showSuccess(APIResponse.message);
                console.log('response', response);
                console.log('transaction_id', transaction_id);

                $otpSection.show();
                $section.hide();
                startResendTimer();
            } else {
                console.log('APIResponse.status', APIResponse);

                const errorMessage = APIResponse.message || 'Unknown error occurred.';
                showError(errorMessage);
            }
        } catch (err) {
            showError(`Error: ${err.message || 'An unexpected error occurred.'}`);
        } finally {
            toggleLoading(false);
        }
    });

    // Handle OTP submission
    $otpForm.on('submit', async (e) => {
        e.preventDefault();
        toggleLoading(true);

        const otp = $('#otp-input').val().trim();
        const otpMobileno = $('#otp-mobile-input').val().trim();

        if (otp.length !== 6) {
            toggleLoading(false);
            return showError('Invalid OTP.');
        }

        try {
            const response = await $.post(ajax_obj, {
                action: 'verify_otp',
                otp,
                otpMobileno,
                transaction_id
            });
            const APIResponse = response.data.message;
        
            console.log(response);
            
            if (APIResponse.status === 200) {
                showSuccess('OTP verified successfully.');
                $otpSection.hide();
            } else {
                showError(response.data.message || 'Verification failed.');
            }
        } catch (err) {
            showError(`Error: ${err.message || 'An unexpected error occurred.'}`);
        } finally {
            toggleLoading(false);
        }
    });

    // Resend OTP functionality
    $resendButton.on('click', async () => {
        if (!transaction_id) return showError('Cannot resend OTP. Transaction ID is missing.');

        try {
            toggleLoading(true);

            const response = await $.post(ajax_obj, {
                action: 'resend_otp',
                transaction_id
            });

            if (response.success) {
                showSuccess('OTP resent successfully.');
                startResendTimer();
            } else {
                showError(response.data.message || 'Failed to resend OTP.');
            }
        } catch (err) {
            showError(`Error: ${err.message || 'An unexpected error occurred.'}`);
        } finally {
            toggleLoading(false);
        }
    });

    // Handle Change Mobile button click
    $changeMobileBtn.on("click", function () {
        if (!$changeMobileBtn.prop("disabled")) {
            console.log("Changing to the common form section...");
            // Hide OTP section and show common form section
            $otpSection.hide();
            $commonSection.show();
        }
    });

    // Start the resend timer
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

    // Helper functions
    function toggleLoading(show) {
        $loadingSpinner.toggle(show);
    }

    function showError(message) {
        $responseMessage.removeClass('success').addClass('error').text(message).show();
    }

    function showSuccess(message) {
        $responseMessage.removeClass('error').addClass('success').text(message).show();
    }
});