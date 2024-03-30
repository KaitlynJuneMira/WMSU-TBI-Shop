<style>
  /* Modal */
.modal {
    display: none; 
    position: fixed; 
    z-index: 1000;  /* Adjusted z-index */
    left: 0;
    top: 0;
    width: 100%; 
    height: 100%; 
    overflow: auto; 
    background-color: rgba(0,0,0,0.4); 
}

.modal-content {
    background-color: #fefefe;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    max-width: 80%;
    max-height: 80%;
    overflow-y: auto;
    padding: 20px;
    border: 1px solid #888;
    border-radius: 10px;
    z-index: 1100; /* Adjusted z-index */
}

/* Close button */
.close {
    color: #aaa;
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    z-index: 1200; /* Adjusted z-index */
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
}

/* Terms and Conditions Text */
.terms-text {
    margin-top: 20px;
    font-size: 16px;
    line-height: 1.5;
}

</style>

@extends('front.layout.layout')
@section('content')
    <!-- Page Introduction Wrapper -->
    <div class="page-style-a">
        <div class="container">
            <div class="page-intro">
                <h2>Account</h2>
                <ul class="bread-crumb">
                    <li class="has-separator">
                        <i class="ion ion-md-home"></i>
                        <a href="{{ url('/') }}">Home</a>
                    </li>
                    <li class="is-marked">
                        <a href="account.html">Account</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- Page Introduction Wrapper /- -->
    <!-- Account-Page -->
    <div class="page-account u-s-p-t-80 p-2">
        <div class="container ">
             @if(Session::has('success_message'))
                  <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success: </strong> {{ Session::get('success_message')}}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                @endif
                @if(Session::has('error_message'))
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error: </strong> {{ Session::get('error_message')}}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                @endif
                @if($errors->any())
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error: </strong> <?php echo implode('', $errors->all('<div>:message</div>')); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                @endif
            <div class="row justify-content-center">
                <!-- Login -->
                <div class="col-lg-6" id="loginDiv">
                    <div class="login-wrapper">
                        <h2 class="account-h2 u-s-m-b-20">Login</h2>
                        <h6 class="account-h6 u-s-m-b-30">Welcome back! Sign in to your account.</h6>
                        <p id="login-error"></p>
                        <form id="loginForm" action="javascript:;" method="post">@csrf
                            <div class="u-s-m-b-30">
                                <label for="user-email">Email
                                    <span class="astk">*</span>
                                </label>
                                <input type="email" name="email" id="users-email" class="text-field" placeholder="Email">
                                <p id="login-email"></p>
                            </div>
                            <div class="u-s-m-b-30">
                                <label for="user-password">Password
                                    <span class="astk">*</span>
                                </label>
                                <input type="password" name="password" id="users-password" class="text-field" placeholder="Password">
                                <p id="login-password"></p>
                            </div>
                            <div class="m-b-45">
                                <button class="button button-outline-secondary w-100">Login</button>
                            </div>
                            <div class="group-inline u-s-m-b-30">
                                <div class="group-2 text-right">
                                    <div class="page-anchor">
                                        <div class="text-center"> <!-- Center the "Already have an account? Login" message -->
                                            <p>Don't have an account? <a href="#"id="showRegister">Register</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Login /- -->

                <!-- Register -->
                <div class="col-lg-6" id="registerDiv" style="display: none;">
                    <div class="reg-wrapper border rounded p-lg-4 p-2">
                        <h2 class="account-h2 u-s-m-b-20">Register</h2>
                        <h6 class="account-h6 u-s-m-b-30">By completing the registration process on this site, you will gain access to your order status and history.</h6>
                        <p id="register-success"></p>
                        <form id="registerForm" action="javascript:;" method="post">@csrf 
                        <div class="u-s-m-b-30">
                           <div class="row">
                                <div class="col-md-6">
                                    <label for="firstname">First Name
                                        <span class="astk">*</span>
                                    </label>
                                    <input type="text" id="firstname" name="firstname" class="text-field" placeholder="First Name">
                                    <p id="register-firstname"></p>
                                </div>
                                <div class="col-md-6">
                                    <label for="lastname">Last Name
                                        <span class="astk">*</span>
                                    </label>
                                    <input type="text" id="lastname" name="lastname" class="text-field" placeholder="Last Name">
                                    <p id="register-lastname"></p>
                                </div>
                           </div>
                        </div>
                        <div class="u-s-m-b-30">
                           <div class="row">
                                <div class="col-md-6">
                                <label for="middleinitial">Middle Initial</label>
                                    <input type="text" id="middleinitial" name="middleinitial" class="text-field" placeholder="Middle Initial">
                                    <p id="register-middleinitial"></p>
                                </div>
                                <div class="col-md-6">
                                <label for="suffix">Suffix (optional)</label>
                                    <input type="text" id="suffix" name="suffix" class="text-field" placeholder="Suffix (e.g, Jr.)">
                                    <p id="register-middleinitial"></p>
                                </div>
                           </div>
                        </div>

                        <div class="u-s-m-b-30">
                            <!-- Address Details -->
                            <div class="u-s-m-b-30">
                                <label for="delivery_address">Address Details
                                    <span class="astk">*</span>
                                </label>
                                <input type="text" name="delivery_address" id="delivery_address" class="text-field">
                                <p id="delivery-delivery_address"></p>
                            </div>
                            
                            <label for="delivery_barangay">Barangay
                                    <span class="astk">*</span>
                                </label>
                            <!-- Barangay -->
                            <div class="u-s-m-b-30">
                                <div class="select-box-wrapper w-100 align-items-center d-flex">
                                    <select class="select-box  w-100" id="delivery_barangay" name="delivery_barangay">
                                        <option value="">Select Barangay</option>
                                        @foreach($barangays as $zcbarangay)
                                            <option value="{{ $zcbarangay['barangay_name'] }}" @if($zcbarangay['barangay_name'] == $zcbarangay['barangay_name'] ) selected @endif>{{ $zcbarangay['barangay_name'] }}</option>
                                        @endforeach
                                    </select>
                                    <p id="delivery-delivery_barangay"></p>
                                </div>
                            </div>  

                        </div>


                                <div class="u-s-m-b-30">
                                    <label for="mobile">Mobile No.
                                        <span class="astk">*</span>
                                    </label>
                                    <input type="text" id="mobile" name="mobile" class="text-field" placeholder="Customer Mobile No.">
                                    <p id="register-mobile"></p>
                                </div>
                                <div class="u-s-m-b-30">
                                    <label for="user-email">Email
                                        <span class="astk">*</span>
                                    </label>
                                    <input type="email" id="user-email" name="email" class="text-field" placeholder="Customer Email">
                                    <p id="register-email"></p>
                                </div>
                                <div class="u-s-m-b-30">
                                    <label for="user-password">Password
                                        <span class="astk">*</span>
                                    </label>
                                    <input type="password" id="user-password" name="password" class="text-field" placeholder="Customer Password">
                                    <p id="register-password"></p>
                                </div>
                                <div class="u-s-m-b-30">
                                    <label for="user-password-confirm">Confirm Password
                                        <span class="astk">*</span>
                                    </label>
                                    <input type="password" id="user-password-confirm" name="password_confirmation" class="text-field" placeholder="Confirm Password">
                                    <p id="register-password-confirm"></p>
                                </div>
                                <div class="u-s-m-b-30">
                                    <input type="checkbox" class="check-box" id="accept" name="accept">
                                    <label class="label-text no-color" for="accept">I have carefully reviewed and hereby accept the
                                    <a href="#" id="termsLink" class="u-c-brand">terms and conditions</a>
                                    </label>
                                    <p id="register-accept"></p>
                                </div>
                                <div class="u-s-m-b-45">
                                    <button class="button button-primary w-100">Register</button>
                                </div>
                                <div class="group-inline u-s-m-b-30">
                                    <div class="group-2 text-right">
                                        <div class="page-anchor">
                                        <div class="text-center"> <!-- Center the "Already have an account? Login" message -->
                                            <p>Already have an account? <a href="#" id="showLogin">Login</a></p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Register /- -->
            </div>
        </div>
    </div>
    <!-- Account-Page /- -->

    <div id="termsModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h1>Terms and Conditions for the Western Mindanao State University Technology Business Incubator (WMSU-TBI) E-commerce Platform</h1>

<h2>1. Acceptance of Terms</h2>
<p>1.1. By accessing or using the WMSU-TBI e-commerce platform, you agree to be bound by these terms and conditions. If you do not agree with any part of these terms, you may not access the platform.</p>

<h2>2. Purpose of the Platform</h2>
<p>2.1. The WMSU-TBI e-commerce platform aims to provide an affordable, secure, and user-friendly online marketplace for MSMEs in the agriculture industry.</p>
<p>2.2. The platform utilizes technologies such as AJAX, HTML/CSS, JavaScript, PHP, Bootstrap, and MySQL to enhance the shopping experience and facilitate the transition from traditional selling to modern e-commerce.</p>

<h2>3. Features and Functionality</h2>
<p>3.1. The platform includes features such as detailed product sales monitoring, efficient inventory management, and integrated data analytics for MSMEs.</p>
<p>3.2. Users can browse, purchase, and receive updates on ordered products through the platform.</p>

<h2>4. User Responsibilities</h2>
<p>4.1. Users are responsible for maintaining the confidentiality of their login credentials and ensuring the security of their account.</p>
<p>4.2. Users agree not to engage in any activity that may disrupt or interfere with the proper functioning of the platform.</p>

<h2>5. Data Privacy and Security</h2>
<p>5.1. The WMSU-TBI e-commerce platform implements security measures to protect user data and transactions.</p>
<p>5.2. Users consent to the collection and use of their personal information in accordance with the platform's privacy policy.</p>

<h2>6. Feedback and Improvement</h2>
<p>6.1. Users are encouraged to provide feedback on their experience with the platform to help improve its features and functionality.</p>
<p>6.2. The platform may undergo updates and enhancements based on user feedback and technological advancements.</p>

<h2>7. Limitation of Liability</h2>
<p>7.1. The WMSU-TBI and its affiliates shall not be liable for any damages arising out of or in connection with the use of the e-commerce platform.</p>
<p>7.2. In no event shall the WMSU-TBI be liable for any indirect, consequential, or incidental damages, including but not limited to loss of profits, data, or goodwill.</p>

<h2>8. Governing Law</h2>
<p>8.1. These terms and conditions shall be governed by and construed in accordance with the laws of Philippines, without regard to its conflict of law provisions.</p>

<h2>9. Changes to Terms and Conditions</h2>
<p>9.1. The WMSU-TBI reserves the right to modify or replace these terms and conditions at any time. Users will be notified of any changes, and continued use of the platform constitutes acceptance of the revised terms.</p>

<h2>10. Contact Information</h2>
<p>10.1. For inquiries or concerns about these terms and conditions, please contact the WMSU-TBI at (062) 993 1314.</p>

<h3>By accessing or using the WMSU-TBI e-commerce platform, you acknowledge that you have read, understood, and agree to be bound by these terms and conditions.</h3>
<button id="acceptTermsBtn" class="button button-primary w-100">Accept Terms and Conditions</button>
</div>    
</div>
</div>

<script>
    // Assuming you have references to the password and confirm password input fields
    var passwordInput = document.getElementById('userpassword');
    var confirmPasswordInput = document.getElementById('user-password-confirm');

    // Assuming you have a reference to the paragraph element where validation messages will be displayed
    var confirmPasswordError = document.getElementById('register-password-confirm');

    // Function to check if passwords match
    function validatePasswords() {
        var password = passwordInput.value;
        var confirmPassword = confirmPasswordInput.value;

        if (password !== confirmPassword) {
            confirmPasswordError.textContent = "Passwords do not match";
            confirmPasswordError.style.color = "red";
        } else {
            confirmPasswordError.textContent = ""; // Clear any previous error messages
        }
    }

    // Event listener to call the validatePasswords function whenever the confirm password input changes
    confirmPasswordInput.addEventListener('input', validatePasswords);

    document.getElementById('showRegister').addEventListener('click', function (e) {
        e.preventDefault();
        document.getElementById('loginDiv').style.display = 'none';
        document.getElementById('registerDiv').style.display = 'block';
    });

    document.getElementById('showLogin').addEventListener('click', function (e) {
        e.preventDefault();
        document.getElementById('loginDiv').style.display = 'block';
        document.getElementById('registerDiv').style.display = 'none';
    });

   // Get the modal
var modal = document.getElementById("termsModal");

// Get the button that opens the modal
var termsLink = document.getElementById("termsLink");

// Get the accept button
var acceptBtn = document.getElementById("acceptTermsBtn");

// Get the checkbox for accepting terms and conditions
var termsCheckbox = document.getElementById("accept");

// Get the <span> element that closes the modal
var closeBtn = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
termsLink.onclick = function() {
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
closeBtn.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks on the accept button
acceptBtn.onclick = function() {
    // Close the modal
    modal.style.display = "none";
    // Check the terms and conditions checkbox
    termsCheckbox.checked = true;
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}


        // Assuming you have references to the password and confirm password input fields
        var passwordInput = document.getElementById('userpassword');
        var confirmPasswordInput = document.getElementById('user-password-confirm');

        // Assuming you have a reference to the paragraph element where validation messages will be displayed
        var confirmPasswordError = document.getElementById('register-password-confirm');

        // Function to check if passwords match
        function validatePasswords() {
            var password = passwordInput.value;
            var confirmPassword = confirmPasswordInput.value;

            if (password !== confirmPassword) {
                confirmPasswordError.textContent = "Passwords do not match";
                confirmPasswordError.style.color = "red";
            } else {
                confirmPasswordError.textContent = ""; // Clear any previous error messages
            }
        }

        // Event listener to call the validatePasswords function whenever the confirm password input changes
        confirmPasswordInput.addEventListener('input', validatePasswords);

         document.getElementById('showRegister').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('loginDiv').style.display = 'none';
            document.getElementById('registerDiv').style.display = 'block';
        });

        document.getElementById('showLogin').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('loginDiv').style.display = 'block';
            document.getElementById('registerDiv').style.display = 'none';
        });

    
    </script>
@endsection
