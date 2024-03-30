<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        @if(Auth::guard('admin')->user()->type=="vendor")
        <li class="nav-item">
            <a @if(Session::get('page')=="vendordashboard") style="background:#4B49AC !important; color: #fff !important;" @endif class="nav-link" href="{{ url('admin/vendordashboard')}}">
            <i class="mdi mdi-view-dashboard menu-icon"></i>
            <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a @if(Session::get('page')=="update_personal_details" || Session::get('page')=="update_business_details" || Session::get('page')=="update_bank_details") style="background:#4B49AC !important; color: #fff !important;" @endif class="nav-link" data-toggle="collapse" href="#ui-vendors" aria-expanded="false" aria-controls="ui-vendors">
            <i class="mdi mdi-settings menu-icon"></i>
            <span class="menu-title">Settings</span>
            <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-vendors">
                <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #4B49AC !important;">
                    <li class="nav-item"> <a @if(Session::get('page')=="update_personal_details") style="background:#4B49AC !important; color: #fff !important;" @else style="background:#fff !important; color: #4B49AC !important;" @endif class="nav-link" href="{{ url('admin/update-vendor-details/personal') }}">Personal Details</a></li>
                    <li class="nav-item"> <a @if(Session::get('page')=="update_business_details") style="background:#4B49AC !important; color: #fff !important;" @else style="background:#fff !important; color: #4B49AC !important;" @endif class="nav-link" href="{{ url('admin/update-vendor-details/business') }}">Business Details</a></li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a @if(Session::get('page')=="sections" || Session::get('page')=="categories" || Session::get('page')=="products") style="background:#4B49AC !important; color: #fff !important;" @endif class="nav-link" data-toggle="collapse" href="#ui-catalogue" aria-expanded="false" aria-controls="ui-catalogue">
            <i class="mdi mdi-format-list-bulleted-type menu-icon"></i>
            <span class="menu-title">Catalogs</span>
            <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-catalogue">
                <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #4B49AC !important;"> 
                    <li class="nav-item"> <a @if(Session::get('page')=="products") style="background:#4B49AC !important; color: #fff !important;" @else style="background:#fff !important; color: #4B49AC !important;" @endif class="nav-link" href="{{ url('admin/products') }}">Products</a></li>  
                    <li class="nav-item"> <a @if(Session::get('page')=="coupons") style="background:#4B49AC !important; color: #fff !important;" @else style="background:#fff !important; color: #4B49AC !important;" @endif class="nav-link" href="{{ url('admin/coupons') }}">Coupons</a></li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a @if(Session::get('page')=="orders") style="background:#4B49AC !important; color: #fff !important;" @endif class="nav-link" href="{{ url('admin/orders') }}">
            <i class="mdi mdi-shopping menu-icon"></i>
            <span class="menu-title">Orders</span>
            <!-- <i class="menu-arrow"></i> -->
            </a>
            <!-- <div class="collapse" id="ui-orders">
                <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #4B49AC !important;">
                    <li class="nav-item"> <a @if(Session::get('page')=="orders") style="background:#4B49AC !important; color: #fff !important;" @else style="background:#fff !important; color: #4B49AC !important;" @endif class="nav-link" href="{{ url('admin/orders') }}">Orders</a></li>   
                </ul>
            </div> -->
        </li>
        <li class="nav-item">
            <a @if(Session::get('page')=="inbox") style="background:#4B49AC !important; color: #fff !important;" @endif class="nav-link" href="{{ url('admin/inbox') }}">
            <i class="fas fa-envelope menu-icon"></i>
            <span class="menu-title">Inbox</span>
            </a>
        </li>
        @else
        <li class="nav-item">
            <a @if(Session::get('page')=="dashboard") style="background:#4B49AC !important; color: #fff !important;" @endif class="nav-link" href="{{ url('admin/dashboard')}}">
            <i class="mdi mdi-view-dashboard menu-icon"></i>
            <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a @if(Session::get('page')=="update_admin_password" || Session::get('page')=="update_admin_details") style="background:#4B49AC !important; color: #fff !important;" @endif class="nav-link" data-toggle="collapse" href="#ui-settings" aria-expanded="false" aria-controls="ui-settings">
            <i class="mdi mdi-settings menu-icon"></i>
            <span class="menu-title">Settings</span>
            <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-settings">
                <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #4B49AC !important;">
                    <li class="nav-item"> <a @if(Session::get('page')=="update_admin_password") style="background:#4B49AC !important; color: #fff !important;" @else style="background:#fff !important; color: #4B49AC !important;" @endif class="nav-link" href="{{ url('admin/update-admin-password') }}">Update Password</a></li>
                    <li class="nav-item"> <a @if(Session::get('page')=="update_admin_details") style="background:#4B49AC !important; color: #fff !important;" @else style="background:#fff !important; color: #4B49AC !important;" @endif class="nav-link" href="{{ url('admin/update-admin-details') }}">Update Details</a></li> 
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a @if(Session::get('page')=="view_vendors") style="background:#4B49AC !important; color: #fff !important;" @endif class="nav-link" href="{{ url('admin/admins/vendor') }}">
            <i class="mdi mdi-account-circle menu-icon"></i>
            <span class="menu-title">Sellers</span>
            </a>
        </li>
        <li class="nav-item">
            <a @if(Session::get('page')=="sections" || Session::get('page')=="categories" || Session::get('page')=="products" || Session::get('page')=="coupons" || Session::get('page')=="filters") style="background:#4B49AC !important; color: #fff !important;" @endif class="nav-link" data-toggle="collapse" href="#ui-catalogue" aria-expanded="false" aria-controls="ui-catalogue">
            <i class="mdi mdi-format-list-bulleted-type menu-icon"></i>
            <span class="menu-title">Catalogs</span>
            <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-catalogue">
                <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #4B49AC !important;">
                    <li class="nav-item"> <a @if(Session::get('page')=="sections") style="background:#4B49AC !important; color: #fff !important;" @else style="background:#fff !important; color: #4B49AC !important;" @endif class="nav-link" href="{{ url('admin/sections') }}">Sections</a></li>
                    <li class="nav-item"> <a @if(Session::get('page')=="categories") style="background:#4B49AC !important; color: #fff !important;" @else style="background:#fff !important; color: #4B49AC !important;" @endif class="nav-link" href="{{ url('admin/categories') }}">Categories</a></li> 
                    <li class="nav-item"> <a @if(Session::get('page')=="brands") style="background:#4B49AC !important; color: #fff !important;" @else style="background:#fff !important; color: #4B49AC !important;" @endif class="nav-link" href="{{ url('admin/brands') }}">Brands</a></li>  
                    <li class="nav-item"> <a @if(Session::get('page')=="products") style="background:#4B49AC !important; color: #fff !important;" @else style="background:#fff !important; color: #4B49AC !important;" @endif class="nav-link" href="{{ url('admin/products') }}">Products</a></li> 
                    <li class="nav-item"> <a @if(Session::get('page')=="coupons") style="background:#4B49AC !important; color: #fff !important;" @else style="background:#fff !important; color: #4B49AC !important;" @endif class="nav-link" href="{{ url('admin/coupons') }}">Coupons</a></li>
                    <!-- <li class="nav-item"> <a @if(Session::get('page')=="filters") style="background:#4B49AC !important; color: #fff !important;" @else style="background:#fff !important; color: #4B49AC !important;" @endif class="nav-link" href="{{ url('admin/filters') }}">Filters</a></li>    -->
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a @if(Session::get('page')=="orders") style="background:#4B49AC !important; color: #fff !important;" @endif class="nav-link" href="{{ url('admin/orders') }}">
            <i class="mdi mdi-shopping menu-icon"></i>
            <span class="menu-title">Orders</span>
            </a>
        </li>
        <!-- <li class="nav-item">
            <a @if(Session::get('page')=="ratings") style="background:#4B49AC !important; color: #fff !important;" @endif class="nav-link" href="{{ url('admin/ratings') }}">
            <i class="mdi mdi-star-circle menu-icon"></i>
            <span class="menu-title">Ratings</span>
            </a>
        </li> -->
        <li class="nav-item">
        <a @if(Session::get('page')=="users") style="background:#4B49AC !important; color: #fff !important;" @endif class="nav-link" href="{{ url('admin/users') }}">
            <!-- <a @if(Session::get('page')=="users" || Session::get('page')=="Subscribers") style="background:#4B49AC !important; color: #fff !important;" @endif class="nav-link" data-toggle="collapse" href="#ui-users" aria-expanded="false" aria-controls="ui-users"> -->
            <i class="mdi mdi-account-multiple menu-icon"></i>
            <span class="menu-title">Customers</span>
            <!-- <i class="menu-arrow"></i> -->
            </a>
            <!-- <div class="collapse" id="ui-users">
                <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #4B49AC !important;">
                    <li class="nav-item"> <a @if(Session::get('page')=="users") style="background:#4B49AC !important; color: #fff !important;" @else style="background:#fff !important; color: #4B49AC !important;" @endif class="nav-link" href="{{ url('admin/users') }}">Users</a></li>
                    <li class="nav-item"> <a @if(Session::get('page')=="subscribers") style="background:#4B49AC !important; color: #fff !important;" @else style="background:#fff !important; color: #4B49AC !important;" @endif class="nav-link" href="{{ url('admin/subscribers') }}">Subscribers</a></li>   
                </ul>
            </div> -->
        </li>
        <li class="nav-item">
        <a @if(Session::get('page')=="banners") style="background:#4B49AC !important; color: #fff !important;" @endif class="nav-link" href="{{ url('admin/banners') }}">
            <i class="mdi mdi-view-dashboard menu-icon"></i>
            <span class="menu-title">Banners</span>
            <!-- <i class="menu-arrow"></i> -->
            </a>
            <!-- <div class="collapse" id="ui-banners">
                <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #4B49AC !important;">
                    <li class="nav-item"> <a @if(Session::get('page')=="banners") style="background:#4B49AC !important; color: #fff !important;" @else style="background:#fff !important; color: #4B49AC !important;" @endif class="nav-link" href="{{ url('admin/banners') }}">Home Page Banners</a></li>
                </ul>
            </div> -->
        </li>
        <li class="nav-item">
            <a @if(Session::get('page')=="cmspages") style="background:#4B49AC !important; color: #fff !important;" @endif class="nav-link" href="{{ url('admin/cms-pages') }}">
            <i class="mdi mdi-bookmark menu-icon"></i>
            <span class="menu-title">Pages</span>
            <!-- <i class="menu-arrow"></i> -->
            </a>
            <!-- <div class="collapse" id="ui-banners">
                <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #4B49AC !important;">
                    <li class="nav-item"> <a @if(Session::get('page')=="cmspages") style="background:#4B49AC !important; color: #fff !important;" @else style="background:#fff !important; color: #4B49AC !important;" @endif class="nav-link" href="{{ url('admin/cms-pages') }}">CMS Pages</a></li>
                </ul>
            </div> -->
        </li>
        <li class="nav-item">
            <a @if(Session::get('page')=="shipping") style="background:#4B49AC !important; color: #fff !important;" @endif class="nav-link" href="{{ url('admin/shipping-charges') }}">
            <i class="mdi mdi-truck-delivery menu-icon"></i>
            <span class="menu-title">Shipping Charges</span>
            <!-- <i class="menu-arrow"></i> -->
            </a>
            <!-- <div class="collapse" id="ui-shipping">
                <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #4B49AC !important;">
                    <li class="nav-item"> <a @if(Session::get('page')=="shipping") style="background:#4B49AC !important; color: #fff !important;" @else style="background:#fff !important; color: #4B49AC !important;" @endif class="nav-link" href="{{ url('admin/shipping-charges') }}">Shipping Charges</a></li>
                </ul>
            </div> -->
        </li>
        @endif
    </ul>
</nav>