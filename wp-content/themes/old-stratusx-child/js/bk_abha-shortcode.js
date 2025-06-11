// document.addEventListener('DOMContentLoaded', () => {
//     const form = document.querySelector('#common-form');
//     const otpSection = document.querySelector('#otp-section');
//     const responseMessage = document.querySelector('#response-message');
//     const loadingSpinner = document.querySelector('#loading-spinner');
//     const buttons = document.querySelectorAll('.link-action-btn');
//     const resendButton = document.querySelector('#resend-otp');
//     const otpForm = document.querySelector('#otp-form');

//     let transactionId = null;
//     let resendTimer;

//     // Toggle between Aadhaar and Mobile fields
//     buttons.forEach(button => button.addEventListener('click', () => {
//         buttons.forEach(btn => btn.classList.remove('active'));
//         button.classList.add('active');

//         document.querySelectorAll('.form-field').forEach(field => {
//             const isActive = field.classList.contains(`${button.dataset.view}-field`);
//             field.classList.toggle('active', isActive);
//             field.querySelector('input').disabled = !isActive;
//         });

//         document.querySelector('#submit-button').textContent = button.dataset.view === 'aadhaar' ? 'Submit Aadhaar' : 'Submit Mobile';
//     }));

//     // Handle form submission
//     form.addEventListener('submit', async (e) => {
//         e.preventDefault();
//         showLoading(true);

//         const activeInput = document.querySelector('.form-field.active input');
//         const inputValue = activeInput.value.trim().replace(/\s+/g, '');
//         const isAadhaar = activeInput.id === 'aadhaar-input';

//         if (!inputValue || (isAadhaar && inputValue.length !== 12)) {
//             return showError('Please enter a valid input.');
//         }

//         try {
//             const endpoint = isAadhaar
//                 ? 'https://node-stage.healthray.com/api/v2/abha/m1-external/aadhaar/generate_otp'
//                 : 'https://node-stage.healthray.com/api/v2/abha/m1-external/mobile/generate_otp';

//             const response = await fetch(endpoint, {
//                 method: 'POST',
//                 headers: { 'Content-Type': 'application/json' },
//                 body: JSON.stringify({ [isAadhaar ? 'aadhaar' : 'mobile']: inputValue })
//             });

//             const data = await response.json();
//             handleResponse(response, data, () => {
//                 transactionId = data.data.transactionId;
//                 responseMessage.textContent = data.message;

//                 if (data.status == 200) {
//                     otpSection.style.display = 'block';
//                     startResendTimer();
//                 }
//             });
//         } catch (err) {
//             showError(`Error: ${err.message}`);
//         }
//     });

//     // Handle OTP submission
//     otpForm.addEventListener('submit', async (e) => {
//         e.preventDefault();
//         showLoading(true);

//         const otp = document.querySelector('#otp-input').value.trim();

//         if (!otp || !transactionId) return showError('Invalid OTP or transaction ID.');

//         try {
//             const response = await fetch('https://node-stage.healthray.com/api/v2/abha/m1-external/aadhaar/verify_otp', {
//                 method: 'POST',
//                 headers: { 'Content-Type': 'application/json' },
//                 body: JSON.stringify({ otp, transaction_id: transactionId })
//             });

//             const data = await response.json();
//             handleResponse(response, data, () => {
//                 responseMessage.textContent = 'OTP Verified Successfully.';
//                 otpSection.style.display = 'none';
//             });
//         } catch (err) {
//             showError(`Error: ${err.message}`);
//         }
//     });

//     // Resend OTP
//     resendButton.addEventListener('click', async () => {
//         if (!transactionId) return showError('Cannot resend OTP. Transaction ID is missing.');

//         try {
//             showLoading(true);
//             const response = await fetch('https://node-stage.healthray.com/api/v2/abha/m1-external/aadhaar/generate_otp', {
//                 method: 'POST',
//                 headers: { 'Content-Type': 'application/json' },
//                 body: JSON.stringify({ transaction_id: transactionId })
//             });

//             const data = await response.json();
//             handleResponse(response, data, () => {
//                 responseMessage.textContent = 'OTP Resent Successfully.';
//                 startResendTimer();
//             });
//         } catch (err) {
//             showError(`Error: ${err.message}`);
//         }
//     });

//     // Helper functions
//     function showError(message) {
//         responseMessage.textContent = message;
//         showLoading(false);
//     }

//     function showLoading(show) {
//         loadingSpinner.style.display = show ? 'block' : 'none';
//     }

//     function handleResponse(response, data, onSuccess) {
//         showLoading(false);
//         if (response.ok) {
//             onSuccess();
//         } else {
//             showError(`Error: ${data.message || 'Something went wrong.'}`);
//         }
//     }

//     function startResendTimer() {
//         let countdown = 60;
//         resendButton.disabled = true;
//         resendTimer = setInterval(() => {
//             resendButton.textContent = `Resend OTP in ${countdown}s`;
//             countdown--;

//             if (countdown < 0) {
//                 clearInterval(resendTimer);
//                 resendButton.textContent = 'Resend OTP';
//                 resendButton.disabled = false;
//             }
//         }, 1000);
//     }
// });


jQuery(document).ready(($) => {
    $('.link-action-btn').on('click', handleActionButtonClick);

    const $form = $('#common-form');
    const $otpSection = $('#otp-section');
    const $responseMessage = $('#response-message');
    const $loadingSpinner = $('#loading-spinner');
    const $otpForm = $('#otp-form');
    const $resendButton = $('#resend-otp');

    let transactionId = null;

    $form.on('submit', async (e) => {
        e.preventDefault();
        $responseMessage.html('');
        toggleLoading(true);
    
        const aadhaar = $('#aadhaar-input').val().trim();
        if (aadhaar.length !== 12) {
            toggleLoading(false);
            return showError('Invalid Aadhaar number.');
        }
    
        try {
            const response = await $.post(ajax_obj, {
                action: 'adhar_auth_form_submit',
                aadhaar
            });
            
            const APIResponse = response.data.message;
            console.log(APIResponse);
            if (APIResponse.status == 200) {
                const transactionId = APIResponse.data.transactionId;
                showSuccess(APIResponse.message);
                $otpSection.show();
                $form.hide();
                startResendTimer();

            } else {                
                const errorMessage = response.data && APIResponse.message ? APIResponse.message : 'Unknown error occurred.';
                showError(errorMessage);
            }
            // if (response.success) {
            //     const transactionId = response.data.transactionId;
            //     showSuccess('OTP sent. Please verify.');
            //     console.log("response.success");
            //     console.log(response);
            //     $otpSection.show();
            // } else {
            //     console.log("response");
            //     console.log(response.data.message.data.message);
            //     const errorMessage = response.data && response.data.message ? response.data.message : 'Unknown error occurred.';
            //     showError(errorMessage);
            // }
        } catch (err) {
            console.error("err");
            console.error(err);
            showError(`Error: ${err.message || 'An unexpected error occurred.'}`);
        } finally {
            toggleLoading(false);
        }
    });
    

    // Handle OTP form submission
    $otpForm.on('submit', async (e) => {
        e.preventDefault();
        toggleLoading(true);
        const otp = $('#otp-input').val().trim();
        if (otp.length !== 6) return showError('Invalid OTP.');
        
        try {
            const response = await $.post(ajax_obj, {
                action: 'verify_otp',
                otp,
                transactionId
            });

            if (response.success) {
                showSuccess('OTP verified successfully.');
            } else {
                showError("OTP",response.data.message);
            }
        } catch (err) {
            showError(`Error: ${err}`);
        } finally {
            toggleLoading(false);
        }
    });

    $resendButton.on('click', async (e) => {
        e.preventDefault();
        if (!transactionId) return showError('Cannot resend OTP. Transaction ID is missing.');

        try {
            showLoading(true);
            const response = await fetch('https://node-stage.healthray.com/api/v2/abha/m1-external/aadhaar/generate_otp', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ transaction_id: transactionId })
            });

            const data = await response.json();
            handleResponse(response, data, () => {
                responseMessage.textContent = 'OTP Resent Successfully.';
                startResendTimer();
            });
        } catch (err) {
            showError(`Error: ${err.message}`);
        }
    });

    // Toggle loading spinner
    function toggleLoading(show) {
        $loadingSpinner.toggle(show);
    }

    // Display error message
    function showError(message) {
        $responseMessage.addClass('error')
        $responseMessage.text(message)
    }
    
    // Display success message
    function showSuccess(message) {
        $responseMessage.addClass('success')
        $responseMessage.text(message)
    }

    function startResendTimer() {
        let countdown = 30;
        resendButton.disabled = true;
        resendTimer = setInterval(() => {
            resendButton.textContent = `Resend OTP in ${countdown}s`;
            countdown--;

            if (countdown < 0) {
                clearInterval(resendTimer);
                resendButton.textContent = 'Resend OTP';
                resendButton.disabled = false;
            }
        }, 1000);
    }

    function handleActionButtonClick(event) {
        const $button = $(event.target); // Get the clicked button
        $('.link-action-btn').removeClass('active'); // Remove active class from all buttons
        $button.addClass('active'); // Add active class to the clicked button
    
        const view = $button.data('view'); // Get the view type from the data attribute
        $('.form-field')
            .removeClass('active') // Deactivate all form fields
            .find('input')
            .prop('disabled', true); // Disable all inputs
    
        $(`.${view}-field`)
            .addClass('active') // Activate the specific form field
            .find('input')
            .prop('disabled', false); // Enable inputs for the active form
    
        $('#submit-button').text(`Submit ${view.charAt(0).toUpperCase() + view.slice(1)}`); // Update button text
    }
});



// document.addEventListener('DOMContentLoaded', () => {
//     const form = document.querySelector('#common-form');
//     const otpSection = document.querySelector('#otp-section');
//     const responseMessage = document.querySelector('#response-message');
//     const loadingSpinner = document.querySelector('#loading-spinner');
//     const buttons = document.querySelectorAll('.link-action-btn');
//     const resendButton = document.querySelector('#resend-otp');
//     const otpForm = document.querySelector('#otp-form');

//     let transactionId = null;
//     let resendTimer;

//     form.addEventListener('submit', async (e) => {
//         showLoading(true);
//         e.preventDefault();

//         const aadhaar = document.querySelector('#aadhaar-input').value.trim();
//         if (aadhaar.length !== 12) return showError('Invalid Aadhaar number.');

//         try {
//             const response = await fetch(ajax_obj, {
//                 method: 'POST',
//                 body: new URLSearchParams({ action: 'adhar_auth_form_submit', aadhaar }),
//             });
            
//             const data = await response.json();
//             console.log(data);
            
//             if (data.success) {
//                 transactionId = data.data.transactionId;
//                 showSuccess('OTP sent. Please verify.');
//                 otpSection.style.display = 'block';
//             } else {
//                 console.log('headers',data.data[0].headers)
//                 console.log('body', data.data[0].body.transactionId)
//                 showError(data.data.data);
//             }
//         } catch (err) {
//             console.log(err);            
//             showError(`Error: ${err}`);
//         } finally {
//             showLoading(false);
//         }
//     });

//     otpForm.addEventListener('submit', async (e) => {
//         e.preventDefault();
//         showLoading(true);

//         const otp = document.querySelector('#otp-input').value.trim();
//         if (otp.length !== 6) return showError('Invalid OTP.');


//         try {

//             const response = await fetch(ajax_obj, {
//                 method: 'POST',
//                 body: new URLSearchParams({ action: 'verify_otp', otp, transactionId }),
//             });
//             const data = await response.json();
//             console.log(data);

//             if (data.success) {
//                 showSuccess('OTP verified successfully.');
//             } else {
//                 showError(data.data.message);
//             }
//         } catch (err) {
//             showError(`Error: ${err.message}`);
//         } finally {
//             showLoading(false);
//         }
//     });

//     function showLoading(show) {
//         loadingSpinner.style.display = show ? 'block' : 'none';
//     }

//     function showError(message) {
//         responseMessage.textContent = message;
//         responseMessage.style.color = 'red';
//     }

//     function showSuccess(message) {
//         responseMessage.textContent = message;
//         responseMessage.style.color = 'green';
//     }
// });
