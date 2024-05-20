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
    <div class="page-account u-s-p-t-80">
        <div class="container">
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

            <div class="row justify-content-center"> <!-- Center the form horizontally -->
                <!-- Login -->
                <div class="col-lg-6" id="loginSection">
                    <div class="login-wrapper">
                        <h2 class="account-h2 u-s-m-b-20">Seller Login</h2>
                        <h6 class="account-h6 u-s-m-b-30">Welcome back! Sign in to your account.</h6>
                        <form action="{{ url('admin/login') }}" method="post">@csrf
                            <div class="u-s-m-b-30">
                                <label for="vendor-email">Email
                                    <span class="astk">*</span>
                                </label>
                                <input type="email" name="email" id="vendor-email" class="text-field" placeholder="Email">
                            </div>
                            <div class="u-s-m-b-30">
                                <label for="vendorpassword">Password
                                    <span class="astk">*</span>
                                </label>
                                <div class="password-toggle-row">
                                    <input type="password" id="vendorpassword" name="password" class="text-field" placeholder="Seller Password">
                                </div>
                                <div class="toggle-switch">
                                    <input type="checkbox" id="showPassword1" class="toggle-checkbox" onclick="togglePasswordVisibility('vendorpassword')">
                                    <label for="showPassword1" class="toggle-label"></label>
                                    <label for="showPassword1" class="toggle-text">Show</label>
                                </div>
                            </div>

                            <div class="m-b-45">
                                <button class="button button-outline-secondary w-100">Login</button>
                            </div>
                            <div class="text-center"> <!-- Center the "Already have an account? Login" message -->
                                <p>Don't have an account? <a href="#" id="showRegister">Register</a></p>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Login /- -->

                <!-- Register -->
                <div class="col-lg-6" id="registerSection" style="display: none;">
                    <div class="reg-wrapper">
                        <h2 class="account-h2 u-s-m-b-20">Register Seller Account</h2>
                        <h6 class="account-h6 u-s-m-b-30">Upon successful registration on our site and after being authorized by the administrator, you can gain access to your vendor account, allowing you to start selling your products.</h6>
             <form id="vendorForm" action="{{ url('/vendor/register') }}" method="post" enctype="multipart/form-data">@csrf 
                    <div class="u-s-m-b-30">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="first_name">First Name
                                    <span class="astk">*</span>
                                </label>
                                <input type="text" id="first_name" name="first_name" class="text-field form-control" placeholder="Seller First Name" autocomplete="off">
                            </div>
                            <div class="col-md-6">
                                <label for="last_name">Last Name
                                    <span class="astk">*</span>
                                </label>
                                <input type="text" id="last_name" name="last_name" class="text-field form-control" placeholder="Seller Last Name" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="u-s-m-b-30">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="middle_initial">Middle Initial (Optional)</label>
                                <input type="text" id="middle_initial" name="middle_initial" class="text-field form-control" placeholder="Middle Initial" autocomplete="off">
                            </div>
                            <div class="col-md-6">
                                <label for="suffix">Suffix (Optional)</label>
                                <input type="text" id="suffix" name="suffix" class="text-field form-control" placeholder="Suffix" autocomplete="off">
                            </div>
                        </div>
                    </div>

                            <div class="u-s-m-b-30">
                                <label for="mobile">Mobile No.
                                    <span class="astk">*</span>
                                </label>
                                <input type="text" id="mobile" name="mobile" class="text-field" placeholder="Seller Mobile No." autocomplete="off">
                            </div>
                            <div class="u-s-m-b-30">
                                <label for="email">Email
                                    <span class="astk">*</span>
                                </label>
                                <input type="email" id="email" name="email" class="text-field" placeholder="Seller Email" autocomplete="off">
                            </div>
                            <div class="u-s-m-b-30">
                                <label for="password">Password
                                    <span class="astk">*</span>
                                </label>
                                <div class="password-toggle">
                                    <input type="password" id="password" name="password" class="text-field" placeholder="Seller Password" autocomplete="off">
                                    <input type="checkbox" id="showPassword2" class="toggle-checkbox" onclick="togglePasswordVisibility('vendorregpassword')">
                                    <label for="showPassword2" class="toggle-label"></label>
                                    <label for="showPassword2" class="toggle-text">Show</label>
                                </div>
                            </div>
                            <div class="u-s-m-b-30">
                                <label for="password_confirmation">Confirm Password
                                    <span class="astk">*</span>
                                </label>
                                <input type="password" id="user-password-confirm" name="password_confirmation" class="text-field" placeholder="Confirm Password" onkeyup="validatePassword()" autocomplete="off">
                                <p id="register-password-confirm"></p>
                            </div class="terms">
                            
                        
                            <div class="u-s-m-b-45" id="nextbtn">
                                <button class="button button-primary w-100">Next</button>
                            </div>
                            <div class="text-center"> <!-- Center the "Already have an account? Login" message -->
                                <p>Already have an account? <a href="#" id="showLogin">Login</a></p>
                            </div>


                                    
                            
                            </div>
                        </div>
                        <!-- Register /- -->

                        <!-- Vendor Details Form -->
                        <div class="row" id="shopSection" style="display: none;">
                            <div class="col-lg-12">
                                <h2 class="account-h2 u-s-m-b-20">Seller Shop Details</h2>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="u-s-m-b-30">
                                                <label for="shopname">Shop Name
                                                    <span class="astk">*</span>
                                                </label>
                                                <input type="text" id="shopname" name="shopname" class="text-field" placeholder="Shop Name">
                                            </div>
                                            <div class="u-s-m-b-30">
                                                <label for="vendorshopdetails">Shop Address Details
                                                    <span class="astk">*</span>
                                                </label>
                                                <input type="text" id="vendorshopdetails" name="vendorshopdetails" class="text-field" placeholder="Shop Address Details">
                                            </div>
                                        
                                            <!-- barangay -->
                                            <div class="form-group">
                                                <label for="vendorshopbarangay">Shop Barangay</label>
                                                <select class="form-control" id="vendorshopbarangay" name="vendorshopbarangay"  style="color: #495057;">
                                                        <option value="">Select Barangay</option>
                                                        @foreach($barangays as $zcbarangay)
                                                            <option value="{{ $zcbarangay['barangay_name'] }}" @if($zcbarangay['barangay_name']==$zcbarangay['barangay_name']) selected @endif>{{ $zcbarangay['barangay_name'] }}</option>
                                                        @endforeach
                                                </select> 
                                            </div>
                                            <div class="u-s-m-b-30">
                                                <label for="vendorshopcontact">Shop Contact Number
                                                    <span class="astk">*</span>
                                                </label>
                                                <input type="text" id="vendorshopcontact" name="vendorshopcontact" class="text-field" placeholder="Shop Contact Number">
                                            </div>
                                            <!-- <div class="u-s-m-b-30">
                                                <label for="vendorshoplicense">Business License Number
                                                    <span class="astk">*</span>
                                                </label>
                                                <input type="text" id="vendorshoplicense" name="vendorshoplicense" class="text-field" placeholder="Business License Number">
                                            </div> -->
                                        </div>
                                        <div class="col-md-6">
                                            <div class="u-s-m-b-30">
                                                <label for="shop_gov_id">Government Issued ID</label>
                                                    <span class="astk">*</span>
                                                <input type="file" class="form-control" id="shop_gov_id" name="shop_gov_id">
                                            </div>
                                            <div class="u-s-m-b-30">
                                                <label for="shop_permit_id">Business Permit</label>
                                                    <span class="astk">*</span>
                                                <input type="file" class="form-control" id="shop_permit_id" name="shop_permit_id">
                                            </div>
                                            <div class="u-s-m-b-30">
                                                <label for="shop_bir_id">BIR Permit</label>
                                                    <span class="astk">*</span>
                                                <input type="file" class="form-control" id="shop_bir_id" name="shop_bir_id">
                                            </div>
                                            <div class="u-s-m-b-30">
                                                <label for="shop_dti_id">DTI Permit</label>
                                                    <span class="astk">*</span>
                                                <input type="file" class="form-control" id="shop_dti_id" name="shop_dti_id">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Remaining form fields -->

                                    <div id="temrsbtn">
                                        <input type="checkbox" class="check-box" id="accept" name="accept">
                                        <label class="label-text no-color" for="accept">I have carefully reviewed and hereby accept the
                                            <a href="terms-and-conditions.html" class="u-c-brand">terms and conditions.</a>
                                        </label>
                                    </div>
                                    <div class="u-s-m-b-45" id="regsiterbtn">
                                        <button class="button button-primary w-100">Register</button>
                                    </div>
                                    <div class="m-b-45" id="back">
                                        <button class="button button-outline-secondary w-100">Back</button>
                                    </div>
                        </form>
                    </div>
                </div>


                <!-- Vendor Details Form -->
            </div>
        </div>
    </div>
    <!-- Account-Page /- -->

    <script>
        document.getElementById('showRegister').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('loginSection').style.display = 'none';
            document.getElementById('regsiterbtn').style.display = 'none';
            document.getElementById('shopSection').style.display = 'none';
            document.getElementById('temrsbtn').style.display = 'none';
            document.getElementById('registerSection').style.display = 'block';
        });

        document.getElementById('back').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('loginSection').style.display = 'none';
            document.getElementById('regsiterbtn').style.display = 'none';
            document.getElementById('shopSection').style.display = 'none';
            document.getElementById('temrsbtn').style.display = 'block';
            document.getElementById('registerSection').style.display = 'block';
        });

        document.getElementById('showLogin').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('loginSection').style.display = 'block';
            document.getElementById('regsiterbtn').style.display = 'none';
            document.getElementById('shopSection').style.display = 'none';
            document.getElementById('registerSection').style.display = 'none';
        });

        document.getElementById('nextbtn').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('loginSection').style.display = 'none';
            document.getElementById('regsiterbtn').style.display = 'block';
            document.getElementById('shopSection').style.display = 'block';
            document.getElementById('temrsbtn').style.display = 'block      ';
            document.getElementById('registerSection').style.display = 'none';
        });

        function togglePasswordVisibility(passwordFieldId) {
        var passwordField = document.getElementById(passwordFieldId);
        var showCheckbox = document.getElementById('showPassword1');
        if (passwordFieldId === 'vendorregpassword') {
            showCheckbox = document.getElementById('showPassword2');
        }
        if (showCheckbox.checked) {
            passwordField.type = "text";
        } else {
            passwordField.type = "password";
        }
    }

    //gets fullname for default shopname
    document.addEventListener("DOMContentLoaded", function() {
        var firstNameInput = document.getElementById("first_name");
        var lastNameInput = document.getElementById("last_name");
        var middleInitialInput = document.getElementById("middle_initial");
        var suffixInput = document.getElementById("suffix");
        var shopNameInput = document.getElementById("shopname");

        function updateShopName() {
            var firstName = firstNameInput.value.trim();
            var lastName = lastNameInput.value.trim();
            var middleInitial = middleInitialInput.value.trim();
            var suffix = suffixInput.value.trim();

            var fullName = firstName + " " + lastName;
            if (middleInitial) {
                fullName += " " + middleInitial;
            }
            if (suffix) {
                fullName += " " + suffix;
            }

            shopNameInput.value = fullName;
        }

        firstNameInput.addEventListener("input", updateShopName);
        lastNameInput.addEventListener("input", updateShopName);
        middleInitialInput.addEventListener("input", updateShopName);
        suffixInput.addEventListener("input", updateShopName);
    });
</script>

@endsection
