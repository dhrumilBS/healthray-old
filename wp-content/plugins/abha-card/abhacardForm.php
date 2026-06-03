<?php if (!defined('ABSPATH'))
    exit; ?>

<div class="abha-wrapper">

    <!-- ══════════════════════════════════════════
         HEADER
    ══════════════════════════════════════════ -->
    <div class="abha-header">
        <h2>Create Ayushman Bharat Health Account (ABHA) Card</h2>
    </div>

    <div class="abha-body">

        <!-- ══════════════════════════════════════════
             STEP 1 · Auth method selector + input
        ══════════════════════════════════════════ -->
        <div id="common-section" class="abha-section">

            <div class="tab-switcher" role="tablist">
                <button class="tab-btn active" role="tab" aria-selected="true" aria-controls="form-aadhaar"
                    data-view="aadhaar">
                    Aadhaar Number
                </button>
                <button class="tab-btn" role="tab" aria-selected="false" aria-controls="form-mobile" data-view="mobile">
                    Mobile Number
                </button>
            </div>

            <!-- Aadhaar form -->
            <form id="form-aadhaar" class="auth-form active" data-type="aadhaar" role="tabpanel"
                aria-labelledby="tab-aadhaar" novalidate>
                <div class="form-field">
                    <label for="aadhaar-input" class="field-label">Aadhaar Number</label>
                    <input type="text" id="aadhaar-input" name="adharnumber" class="input-field auth-input"
                        pattern="[0-9]{12}" maxlength="12" placeholder="Enter 12-digit Aadhaar Number"
                        inputmode="numeric" autocomplete="off" required>
                </div>
                <div class="form-field checkbox-field">
                    <input type="checkbox" id="aadhaar-consent" required>
                    <label for="aadhaar-consent">
                        I agree to the necessary permissions for Healthray to set up my ABHA Health Locker.
                    </label>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Get OTP</button>
                </div>
            </form>

            <!-- Mobile form -->
            <form id="form-mobile" class="auth-form" data-type="mobile" role="tabpanel" aria-labelledby="tab-mobile"
                novalidate>
                <div class="form-field">
                    <label for="mobile-input" class="field-label">Mobile Number</label>
                    <input type="tel" id="mobile-input" name="adharnumber" class="input-field auth-input"
                        pattern="[0-9]{10}" maxlength="10" placeholder="Enter 10-digit Mobile Number"
                        inputmode="numeric" autocomplete="tel" required>
                </div>
                <div class="form-field checkbox-field">
                    <input type="checkbox" id="mobile-consent" required>
                    <label for="mobile-consent">
                        I agree to the necessary permissions for Healthray to set up my ABHA Health Locker.
                    </label>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Get OTP</button>
                </div>
            </form>

            <!-- Shared hidden transaction ID store -->
            <input type="hidden" id="transaction-id">
        </div><!-- /#common-section -->


        <!-- ══════════════════════════════════════════
             STEP 2a · Aadhaar OTP
        ══════════════════════════════════════════ -->
        <div id="adhar-otp-section" class="abha-section otp-section" hidden>
            <form id="adhar-otp-form" class="otp-form" data-type="aadhaar" novalidate>

                <div class="form-field otp-row">
                    <label for="adhar-otp-input" class="field-label">Enter OTP</label>
                    <div class="otp-input-group">
                        <input type="text" id="adhar-otp-input" class="input-field otp-input" placeholder="6-digit OTP"
                            maxlength="6" inputmode="numeric" autocomplete="one-time-code" required>
                        <div class="otp-actions">
                            <button type="button" class="btn-link otp-resend-btn" disabled>
                                Resend OTP
                            </button>
                            <button type="button" class="btn-link change-mobile">
                                Change Number
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-field mobile-field" id="second-mobile-wrap">
                    <label for="adhar-otp-mobile-input" class="field-label">Mobile Number</label>
                    <input type="tel" id="adhar-otp-mobile-input" class="input-field" pattern="[0-9]{10}" maxlength="10"
                        placeholder="Enter your mobile number" inputmode="numeric" required>
                    <p class="field-hint">
                        <svg class="icon-info" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                            aria-hidden="true">
                            <path fill="currentColor"
                                d="M256 40c118.621 0 216 96.075 216 216 0 119.291-96.61 216-216 216-119.244 0-216-96.562-216-216 0-119.203 96.602-216 216-216m0-32C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm-36 344h12V232h-12c-6.627 0-12-5.373-12-12v-8c0-6.627 5.373-12 12-12h48c6.627 0 12 5.373 12 12v140h12c6.627 0 12 5.373 12 12v8c0-6.627-5.373 12-12 12h-72c-6.627 0-12-5.373-12-12v-8c0-6.627 5.373-12 12-12zm36-240c-17.673 0-32 14.327-32 32s14.327 32 32 32 32-14.327 32-32-14.327-32-32-32z" />
                        </svg>
                        This number will be used for all future communications.
                    </p>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Verify OTP</button>
                </div>
            </form>
        </div><!-- /#adhar-otp-section -->


        <!-- ══════════════════════════════════════════
             STEP 2b · Mobile (PHR) OTP
        ══════════════════════════════════════════ -->
        <div id="mobile-otp-section" class="abha-section otp-section" hidden>
            <form id="mobile-otp-form" class="otp-form" data-type="mobile" novalidate>

                <div class="form-field otp-row">
                    <label for="mobile-otp-input" class="field-label">Enter OTP</label>
                    <div class="otp-input-group">
                        <input type="text" id="mobile-otp-input" class="input-field otp-input" placeholder="6-digit OTP"
                            maxlength="6" inputmode="numeric" autocomplete="one-time-code" required>
                        <div class="otp-actions">
                            <button type="button" class="btn-link otp-resend-btn" disabled>
                                Resend OTP
                            </button>
                            <button type="button" class="btn-link change-mobile">
                                Change Number
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Verify OTP</button>
                </div>
            </form>
        </div><!-- /#mobile-otp-section -->


        <!-- ══════════════════════════════════════════
             STEP 2c · Aadhaar-alternate-mobile OTP
        ══════════════════════════════════════════ -->
        <div id="adhar-mobile-otp-section" class="abha-section otp-section" hidden>
            <form id="adhar-mobile-otp-form" class="otp-form" data-type="aadhaar-mobile" novalidate>

                <div class="form-field otp-row">
                    <label for="adhar-mobile-otp-input" class="field-label">Enter OTP</label>
                    <div class="otp-input-group">
                        <input type="text" id="adhar-mobile-otp-input" class="input-field otp-input"
                            placeholder="6-digit OTP" maxlength="6" inputmode="numeric" autocomplete="one-time-code"
                            required>
                        <div class="otp-actions">
                            <button type="button" class="btn-link otp-resend-btn" disabled>
                                Resend OTP
                            </button>
                            <button type="button" class="btn-link change-mobile">
                                Change Number
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Verify OTP</button>
                </div>
            </form>
        </div><!-- /#adhar-mobile-otp-section -->


        <!-- ══════════════════════════════════════════
             STEP 3 · Choose existing ABHA address
        ══════════════════════════════════════════ -->
        <div id="choose-abha-section" class="abha-section choose-abha-section" hidden>
            <div class="section-header">
                <h3>Select ABHA Address to Continue</h3>
                <p class="founded-address">We found ABHA addresses linked to your account.</p>
            </div>

            <div class="abha-list form-field"></div>

            <div class="separator"><span>or</span></div>

            <div class="text-center">
                <button type="button" class="btn btn-outline" id="create-abha-btn">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                        aria-hidden="true">
                        <path
                            d="M18.5 12c0 .563-.469 1.031-1 1.031H13v4.5c0 .532-.469.969-1 .969a.98.98 0 01-1-.969v-4.5H6.5c-.563 0-1-.469-1-1.031a.98.98 0 011-.969H11v-4.5c0-.562.438-1.031 1-1.031.531 0 1 .469 1 1.031v4.5h4.5c.531-.031 1 .438 1 .969z"
                            fill="currentColor" />
                    </svg>
                    Create New ABHA Address
                </button>
            </div>
        </div><!-- /#choose-abha-section -->


        <!-- ══════════════════════════════════════════
             STEP 4 · PHR demographic details form
        ══════════════════════════════════════════ -->
        <div id="create-abha-section" class="abha-section create-abha-section" hidden>
            <form id="create-abha-form" novalidate>
                <div class="section-header text-center">
                    <h3>Enter ABHA Information</h3>
                </div>

                <!-- Full name -->
                <div class="form-field">
                    <label class="field-label">Full Name</label>
                    <div class="form-group name-group">
                        <input type="text" id="fname" name="first_name" class="input-field" placeholder="First Name"
                            required>
                        <input type="text" id="mname" name="middle_name" class="input-field" placeholder="Middle Name">
                        <input type="text" id="lname" name="last_name" class="input-field" placeholder="Last Name">
                    </div>
                </div>

                <!-- Date of birth -->
                <div class="form-field">
                    <label class="field-label">Date of Birth <span class="required" aria-hidden="true">*</span></label>
                    <div class="form-group dob-group">
                        <select id="day" name="dob_day" class="input-field" required>
                            <option value="">Day</option>
                        </select>
                        <select id="month" name="dob_month" class="input-field" required>
                            <option value="">Month</option>
                        </select>
                        <select id="year" name="dob_year" class="input-field" required>
                            <option value="">Year</option>
                        </select>
                    </div>
                </div>

                <!-- Gender -->
                <div class="form-field">
                    <label class="field-label">Gender <span class="required" aria-hidden="true">*</span></label>
                    <div class="form-group gender-group" role="group" aria-label="Gender">
                        <label class="gender-option">
                            <input type="radio" name="gender" value="male">
                            <span>Male</span>
                        </label>
                        <label class="gender-option">
                            <input type="radio" name="gender" value="female">
                            <span>Female</span>
                        </label>
                        <label class="gender-option">
                            <input type="radio" name="gender" value="other">
                            <span>Other</span>
                        </label>
                    </div>
                </div>

                <!-- State & District -->
                <div class="form-field">
                    <div class="form-group state-district-group">
                        <div class="field-wrap">
                            <label for="state" class="field-label">
                                State <span class="required" aria-hidden="true">*</span>
                            </label>
                            <select id="state" name="state_code" class="input-field" required>
                                <option value="">Select a State</option>
                            </select>
                        </div>
                        <div class="field-wrap">
                            <label for="district" class="field-label">
                                District <span class="required" aria-hidden="true">*</span>
                            </label>
                            <select id="district" name="district_code" class="input-field" disabled required>
                                <option value="">Select State First</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Pincode -->
                <div class="form-field">
                    <label for="pincode" class="field-label">
                        Pincode <span class="required" aria-hidden="true">*</span>
                    </label>
                    <!-- FIX: was pattern="{0-9}[6]" (invalid) → [0-9]{6} -->
                    <input type="text" id="pincode" name="pin_code" class="input-field" placeholder="6-digit Pin Code"
                        pattern="[0-9]{6}" maxlength="6" inputmode="numeric" required>
                </div>

                <!-- Address -->
                <div class="form-field">
                    <!-- FIX: placeholder was "Pin code" → correct label/placeholder -->
                    <label for="address" class="field-label">
                        Address <span class="required" aria-hidden="true">*</span>
                    </label>
                    <textarea id="address" name="address" class="input-field" rows="3"
                        placeholder="Enter your full address" required></textarea>
                </div>

                <div class="form-actions">
                    <!-- FIX: type="button" prevents accidental double-submit via form submit event -->
                    <button type="button" id="demographics-submit-btn" class="btn btn-primary">
                        Continue
                    </button>
                </div>
            </form>
        </div><!-- /#create-abha-section -->


        <!-- ══════════════════════════════════════════
             STEP 5 · Suggested ABHA addresses
        ══════════════════════════════════════════ -->
        <div id="suggestion-section" class="abha-section suggestion-section" hidden>
            <div class="section-header text-center">
                <h3>Choose Your ABHA Address</h3>
                <p>Select a suggestion or create a custom address below.</p>
            </div>

            <div class="suggestion-list-wrap no-data">
                <div class="suggestion-list" aria-live="polite">No suggestions yet.</div>

                <div class="separator"><span>Or create a custom address</span></div>

                <form id="abha-address-form" novalidate>
                    <div class="form-field">
                        <!-- FIX: removed `disabled` from hidden input (was preventing JS .val() reads in some browsers) -->
                        <input type="hidden" id="abha-address-type" name="abha-address-type">
                        <input type="text" id="abha-address-input" name="abha-address" class="input-field"
                            placeholder="Enter custom ABHA Address" disabled autocomplete="off">
                        <p class="field-hint">
                            Allowed characters: letters (a–z, A–Z), numbers (0–9), underscore (_), dot (.)
                        </p>
                    </div>
                    <!-- FIX: id corrected to match JS selector (#submitSuggestion, not #submitSuggession) -->
                    <div class="form-actions">
                        <button type="submit" id="submitSuggestion" class="btn btn-primary" disabled>
                            Confirm Address
                        </button>
                    </div>
                </form>
            </div>
        </div><!-- /#suggestion-section -->

    </div><!-- /.abha-body -->

    <!-- ══════════════════════════════════════════
         GLOBAL: response banner + spinner
    ══════════════════════════════════════════ -->
    <div id="response-message" role="alert" aria-live="assertive" hidden></div>
    <div id="loading-spinner" aria-busy="false" aria-label="Loading" hidden>
        <span class="spinner-dot"></span>
        <span class="spinner-dot"></span>
        <span class="spinner-dot"></span>
    </div>

</div><!-- /.abha-wrapper -->