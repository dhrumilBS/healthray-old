<div class="section-wrapper">
    <div class="content-sec">
        <h2>Create Ayushman Bharat Health Account ABHA (Health ID) Card</h2>
    </div>

    <div class="form-container">
        <div id="common-section">
            <div class="link-action">
                <button class="link-action-btn active" data-view="aadhaar">Aadhaar Number</button>
                <button class="link-action-btn" data-view="mobile">Mobile Number</button>
            </div>

            <!-- auth-form - aadhaar -->
            <form class="auth-form  active" data-type="aadhaar">
                <?php wp_nonce_field('ajax_nonce_action', '_wpnonce'); ?>
                <input type="hidden" name="action" value="aadhaar_auth_form_submit">
                <div class="form-field">
                    <input type="text" class="input-field auth-input" id="aadhaar-input" pattern="[0-9]{12}" placeholder="Enter Aadhaar Number" required>
                </div>
                <div class="checkbox-input">
                    <input type="checkbox" id="Aadhaar-permissions" required>
                    <label for="Aadhaar-permissions">I agree to the necessary permissions for Healthray to set up my ABHA Health Locker. <a href="#">Learn More</a></label>
                </div>
                <div class="form-submit">
                    <button type="submit" class="submit-btn">Submit Aadhaar</button>
                </div>
            </form>

            <!-- auth-form - mobile -->
            <form class="auth-form  " data-type="mobile">
                <?php wp_nonce_field('ajax_nonce_action', '_wpnonce'); ?>
                <input type="hidden" name="action" value="mobile_auth_form_submit">
                <div class="form-field">
                    <input type="text" class="input-field auth-input" id="mobile-input" pattern="[0-9]{10}" placeholder="Enter Mobile Number" required>
                </div>
                <div class="checkbox-input">
                    <input type="checkbox" id="mobile-permissions" required>
                    <label for="mobile-permissions">I agree to the necessary permissions for Healthray to set up my ABHA Health Locker. <a href="#">Learn More</a></label>
                </div>
                <div class="form-submit">
                    <button type="submit" class="submit-btn">Submit Mobile</button>
                </div>
            </form>
        </div>

        <!-- adhar-otp -->
        <div id="adhar-otp-section" class="otp-section">
            <form id="adhar-otp-form" class="otp-form" data-type="aadhaar">
                <input type="hidden" name="action" value="verify_otp">
                <input type="hidden" name="transaction_id" class="transaction-id">
                <div class="form-field d-flex">
                    <input type="text" id="otp-input" class="input-field auth-input" placeholder="Enter OTP" maxlength="6" required>
                    <div class="form-submit resend-btn">
                        <a id="resend-otp" class="submit-link" href="javascript:void(0)" disabled><span> Resend OTP</span></a>
                        <a id="change-mobile" class="submit-link" href="javascript:void(0)">Change Mobile</a>
                    </div>
                </div>

                <div class="second-mobile-number">
                    <div class="form-field">
                        <input type="tel" id="otp-mobile-input" name="otp-mobile" class="input-field auth-input" pattern="[0-9]{10}" placeholder="Enter your mobile number" required>
                    </div>
                    <div class="d- info-circle">
                        <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" viewBox="0 0 512 512">
                            <path fill="currentColor" d="M256 40c118.621 0 216 96.075 216 216 0 119.291-96.61 216-216 216-119.244 0-216-96.562-216-216 0-119.203 96.602-216 216-216m0-32C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm-36 344h12V232h-12c-6.627 0-12-5.373-12-12v-8c0-6.627 5.373-12 12-12h48c6.627 0 12 5.373 12 12v140h12c6.627 0 12 5.373 12 12v8c0 6.627-5.373 12-12 12h-72c-6.627 0-12-5.373-12-12v-8c0-6.627 5.373-12 12-12zm36-240c-17.673 0-32 14.327-32 32s14.327 32 32 32 32-14.327 32-32-14.327-32-32-32z"></path>
                        </svg>
                        <div>This mobile number will be used for all the communications</div>
                    </div>
                </div>

                <div class="form-submit">
                    <button type="submit" class="submit-btn">Verify OTP</button>
                </div>
            </form>
        </div>

        <!-- mobile-otp -->
        <div id="mobile-otp-section" class="otp-section">
            <form id="mobile-otp-form" class="otp-form" data-type="mobile">
                <input type="hidden" name="action" value="verify_otp">
                <input type="hidden" name="transaction_id" class="transaction-id">
                <div class="form-field d-flex">
                    <input type="text" id="otp-input" class="input-field" placeholder="Enter OTP" maxlength="6" required>
                    <div class="form-submit resend-btn">
                        <a id="resend-otp" class="submit-link" href="javascript:void(0)" disabled><span> Resend OTP</span></a>
                        <a id="change-mobile" class="submit-link" href="javascript:void(0)">Change Mobile</a>
                    </div>
                </div>
                <div class="form-submit">
                    <button type="submit" class="submit-btn">Verify OTP</button>
                </div>
            </form>
        </div>

        <!-- Select ABHA to continue -->
        <div class="choose-abha-section">
            <div class="text-center header">
                <h3 class="title">Select ABHA to continue</h3>
                <p>We have found ABHA 1 address linked with your mobile number.</p>
            </div>
            <div class="abha-phr-address">
                <div class="abha-list">
                    <!-- <div class="abha-item">
                        <div class="abha-icon">
                            <svg viewBox="0 0 50 39" xmlns="http://www.w3.org/2000/svg" height="38" width="48">
                                <rect x="1" y="6" width="48" height="32" rx="3" fill="#fff" stroke="#152ce1" stroke-width="2"></rect>
                                <path stroke="#152ce1" stroke-width="2" stroke-linecap="round" d="M8 20h10M8 25h6M8 30h8"></path>
                                <path d="M34.5 19.5h-3v3h3v-3zm-3-1.5h3c.813 0 1.5.688 1.5 1.5v3a1.5 1.5 0 01-1.5 1.5h-3a1.48 1.48 0 01-1.5-1.5v-3a1.5 1.5 0 011.5-1.5zm3 9.5h-3v3h3v-3zm-3-1.5h3c.813 0 1.5.688 1.5 1.5v3a1.5 1.5 0 01-1.5 1.5h-3a1.48 1.48 0 01-1.5-1.5v-3a1.5 1.5 0 011.5-1.5zm8-6.5v3h3v-3h-3zm-1.5 0a1.5 1.5 0 011.5-1.5h3c.813 0 1.5.688 1.5 1.5v3a1.5 1.5 0 01-1.5 1.5h-3a1.48 1.48 0 01-1.5-1.5v-3zm-5.75 1.25c0-.25.219-.5.5-.5h.5c.25 0 .5.25.5.5v.5c0 .281-.25.5-.5.5h-.5a.494.494 0 01-.5-.5v-.5zm.5 7.5h.5c.25 0 .5.25.5.5v.5c0 .281-.25.5-.5.5h-.5a.494.494 0 01-.5-.5v-.5c0-.25.219-.5.5-.5zm7.5-7.5c0-.25.219-.5.5-.5h.5c.25 0 .5.25.5.5v.5c0 .281-.25.5-.5.5h-.5a.494.494 0 01-.5-.5v-.5zM38 26.5c0-.25.219-.5.5-.5h2c.25 0 .5.25.5.5 0 .281.219.5.5.5h1c.25 0 .5-.219.5-.5 0-.25.219-.5.5-.5.25 0 .5.25.5.5v3c0 .281-.25.5-.5.5h-2a.494.494 0 01-.5-.5c0-.25-.25-.5-.5-.5-.281 0-.5.25-.5.5v2c0 .281-.25.5-.5.5h-1a.494.494 0 01-.5-.5v-5zm3.5 4.5c.25 0 .5.25.5.5 0 .281-.25.5-.5.5a.494.494 0 01-.5-.5c0-.25.219-.5.5-.5zm2 0c.25 0 .5.25.5.5 0 .281-.25.5-.5.5a.494.494 0 01-.5-.5c0-.25.219-.5.5-.5zM1 9a3 3 0 013-3h42a3 3 0 013 3v5H1V9z" fill="#152ce1"></path>
                                <rect x="23" width="4" height="8" rx="1" fill="#5669ec"></rect>
                            </svg>
                        </div>
                        <div class="abha-details">
                            <strong title="mangukiyadhrumil@abdm">mangukiyadh...mil@abdm</strong>
                            <div>Mangukiya Dhrumil Alkeshbhai</div>
                            <div>26yrs ● Male</div>
                        </div>
                    </div> -->
                </div>
                <div class="form-submit">
                    <button id="select-phr-btn" class="submit-btn" style="display: none;">Continue</button>
                </div>
            </div>
            <div class="separator">
                <span>or</span>
            </div>
            <div class="create-abha text-center">
                <button class="create-abha-btn" id="create-abha-btn">
                    <div class="d-flex">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                            <path d="M18.5 12c0 .563-.469 1.031-1 1.031H13v4.5c0 .532-.469.969-1 .969a.98.98 0 01-1-.969v-4.5H6.5c-.563 0-1-.469-1-1.031a.98.98 0 011-.969H11v-4.5c0-.562.438-1.031 1-1.031.531 0 1 .469 1 1.031v4.5h4.5c.531-.031 1 .438 1 .969z" fill="currentColor"></path>
                        </svg>
                        <p>Create new ABHA address</p>
                    </div>
                </button>
            </div>

        </div>

        <!-- CREATE NEW ABHA ADDRESS -->
        <div class="create-abha-section">
            <form id="create-abha-form" class="create-abha-form" data-type="aadhaar">
                <div class="text-center">
                    <h3>Enter ABHA Information</h3>
                </div>
                <div class="form-content">
                    <div class="form-field">
                        <input type="text" id="name" placeholder="Name" class="input-field">
                    </div>
                    <div class="form-field">
                        <p>Date of Birth<span class="required">*</span></p>
                        <div class="form-group date-of-birth">
                            <div>
                                <input type="number" min="1" max="31" id="day" placeholder="DD" class="input-field" value="1">
                            </div>
                            <div>
                                <input type="number" min="1" max="12" id="month" placeholder="MM" class="input-field" value="1">
                            </div>
                            <div>
                                <input type="number" min="1900" id="year" placeholder="YYYY" class="input-field" value="1990">
                            </div>
                        </div>
                    </div>
                    <div class="form-field">
                        <p>Gender<span class="required">*</span></p>
                        <div class="form-group radio-input gender-options">
                            <div class="">
                                <input type="radio" name="gender" id="male" value="male" class="input-field">
                                <label for="male"> Male </label>
                            </div>
                            <div class="">
                                <input type="radio" name="gender" id="female" value="female" class="input-field">
                                <label for="female">Female </label>
                            </div>
                            <div class="">
                                <input type="radio" name="gender" id="other" value="other" class="input-field">
                                <label for="other">Other </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-field">
                        <div class="form-group state-city">
                            <div>
                                <label for="state">State<span class="required">*</span></label>
                                <input type="text" id="state" placeholder="State" class="input-field">
                            </div>
                            <div>
                                <label for="city">City<span class="required">*</span></label>
                                <input type="text" id="city" placeholder="city" class="input-field">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-submit">
                    <button type="submit" class="submit-btn">Verify OTP</button>
                </div>
            </form>
        </div>
    </div>

    <div id="response-message">

    </div>
    <div id="loading-spinner" style="display: none;">Loading...</div>
</div>