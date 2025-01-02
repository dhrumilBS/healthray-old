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


document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('#common-form');
    const otpSection = document.querySelector('#otp-section');
    const responseMessage = document.querySelector('#response-message');
    const loadingSpinner = document.querySelector('#loading-spinner');
    const buttons = document.querySelectorAll('.link-action-btn');
    const resendButton = document.querySelector('#resend-otp');
    const otpForm = document.querySelector('#otp-form');

    let transactionId = null;
    let resendTimer;

    // Toggle between Aadhaar and Mobile fields
    buttons.forEach(button => button.addEventListener('click', () => {
        buttons.forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');

        document.querySelectorAll('.form-field').forEach(field => {
            const isActive = field.classList.contains(`${button.dataset.view}-field`);
            field.classList.toggle('active', isActive);
            field.querySelector('input').disabled = !isActive;
        });

        document.querySelector('#submit-button').textContent = button.dataset.view === 'aadhaar' ? 'Submit Aadhaar' : 'Submit Mobile';
    }));

    // Handle form submission via AJAX
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        showLoading(true);

        const activeInput = document.querySelector('.form-field.active input');
        const inputValue = activeInput.value.trim().replace(/\s+/g, '');
        const isAadhaar = activeInput.id === 'aadhaar-input';

        if (!inputValue || (isAadhaar && inputValue.length !== 12)) {
            return showError('Please enter a valid input.');
        }

        try {
            const formData = new FormData();
            formData.append('action', 'adhar_auth_form_submit');
            formData.append(isAadhaar ? 'aadhaar' : 'mobile', inputValue);

            const response = await fetch(ajax_obj, {
                method: 'POST',
                body: formData,
            });

            const data = await response.json();


            console.log(data);
            


            if (data.success) {
                responseMessage.textContent = data.data.message;
                otpSection.style.display = 'block';
                startResendTimer();
            } else {
                showError(data.data.message);
            }
        } catch (err) {
            showError(`Error: ${err.message}`);
        }
    });

    // Helper functions
    function showError(message) {
        responseMessage.textContent = message;
        showLoading(false);
    }

    function showLoading(show) {
        loadingSpinner.style.display = show ? 'block' : 'none';
    }

    function startResendTimer() {
        let countdown = 60;
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
});
