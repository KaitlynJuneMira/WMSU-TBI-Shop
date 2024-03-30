<?php

use Illuminate\Support\Facades\Route;
use App\Models\Category;
use App\Models\CmsPage;

// K

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ExportController;
Route::get('/getOrdersData', [AdminController::class, 'getOrdersData']);
// K


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

Route::prefix('/admin')->namespace('App\Http\Controllers\Admin')->group(function(){
    // Admin Login Route
    Route::match(['get','post'],'login','AdminController@login');    
    Route::group(['middleware'=>['admin']],function(){
        // Admin Dashboard Route
        Route::get('dashboard','AdminController@dashboard');

        // pdf and excel
        Route::get('ExportSeller/{type}/{columns}/{column_names}','ExportController@adminSeller');
        Route::get('ExportProducts/{type}/{columns}/{column_names}','ExportController@adminProducts');
        Route::get('ExportCustomers/{type}/{columns}/{column_names}','ExportController@adminCustomers');
        Route::get('ExportOrders/{type}/{columns}/{column_names}','ExportController@adminOrders');
        

        // dashboard stuff
        Route::get('dashboard-adminoverallRevenue/{year}/{paid}','AdminController@adminoverallRevenueYear');
        Route::get('dashboard-admintopProducts/{year}/{paid}/{limit}','AdminController@admintopProductsYear');
        Route::get('dashboard-admintopSellers/{year}/{paid}','AdminController@admintopSellersYear');
        Route::get('dashboard-adminretension/{year}','AdminController@adminretensionYear');
        Route::get('dashboard-admintopCategory/{year}/{paid}','AdminController@admintopCategoryYear');
        Route::get('dashboard-adminfulfilledOrders/{year}/{paid}','AdminController@adminfulfilledOrdersYear');
        Route::get('dashboard-adminorderStatus/{year}','AdminController@adminorderStatusYear');

        Route::get('dashboard-drillAnalyticsRevenue/{year}/{vendor}/{paid}','AdminController@drillAnalyticsRevenueYear');

        Route::get('dashboard-getVendorDetails/{year}','AdminController@getVendorDetails');
        Route::get('dashboard-adminoverallRevenue','AdminController@adminoverallRevenue');
        Route::get('dashboard-admintopProducts','AdminController@admintopProducts');
        Route::get('dashboard-admintopSellers','AdminController@admintopSellers');
        Route::get('dashboard-adminretension','AdminController@adminretension');
        Route::get('dashboard-admintopCategory','AdminController@admintopCategory');
        Route::get('dashboard-adminfulfilledOrders','AdminController@adminfulfilledOrders');
        Route::get('dashboard-adminorderStatus','AdminController@adminorderStatus');




        Route::get('dashboard-vendorsales','AdminController@vendorsales');
        Route::get('dashboard-vendoraverageOrderValue','AdminController@vendoraverageOrderValue');
        Route::get('dashboard-vendortopSellingProducts','AdminController@vendortopSellingProducts');
        Route::get('dashboard-vendorinventoryTurnOver','AdminController@vendorinventoryTurnOver');
        Route::get('dashboard-vendororderStatus','AdminController@vendororderStatus');
        Route::get('dashboard-vendorsalesGrowthOverTime','AdminController@vendorsalesGrowthOverTime');

        Route::get('dashboard-vendorsales/{year}/{paid}','AdminController@vendorsalesYear');
        Route::get('dashboard-vendoraverageOrderValue/{year}/{paid}','AdminController@vendoraverageOrderValueYear');
        Route::get('dashboard-vendortopSellingProducts/{year}/{paid}','AdminController@vendortopSellingProductsYear');
        Route::get('dashboard-vendororderStatus/{year}','AdminController@vendororderStatusYear');
        Route::get('dashboard-vendorsalesGrowthOverTime/{year}','AdminController@vendorsalesGrowthOverTimeYear');
        Route::get('dashboard-vendorsalesGrowthOverTimePrev/{year}','AdminController@vendorsalesGrowthOverTimeYearPrev');
        
        // dashboart stufflog
        Route::get('dashboard-vendorinventoryTurnOver/{year}','AdminController@vendorinventoryTurnOverYear');
        // download dashboard
        Route::get('dashboard-adminoverallRevenueDownload/{type}/{year}/{paid}','AdminController@adminoverallRevenueYearDownload');
        Route::get('dashboard-admintopProductsDownload/{type}/{year}/{paid}/{limit}','AdminController@admintopProductsYearDownload');
        Route::get('dashboard-admintopSellersDownload/{type}/{year}/{paid}','AdminController@admintopSellersYearDownload');
        Route::get('dashboard-adminretensionDownload/{type}/{year}','AdminController@adminretensionYearDownload');
        Route::get('dashboard-admintopCategoryDownload/{type}/{year}/{paid}','AdminController@admintopCategoryYearDownload');
        Route::get('dashboard-adminfulfilledOrdersDownload/{type}/{year}/{paid}','AdminController@adminfulfilledOrdersYearDownload');
        Route::get('dashboard-adminorderStatusDownload/{type}/{year}','AdminController@adminorderStatusYearDownload');
        Route::get('dashboard-drillAnalyticsRevenueDownload/{type}/{year}/{vendor}/{paid}/{chartX}','AdminController@drillAnalyticsRevenueYearDownload');
        
        Route::get('dashboard-vendorsalesDownload/{type}/{year}/{paid}','AdminController@vendorsalesYearDownload');
        Route::get('dashboard-vendoraverageOrderValueDownload/{type}/{year}/{paid}','AdminController@vendoraverageOrderValueYearDownload');
        Route::get('dashboard-vendortopSellingProductsDownload/{type}/{year}/{paid}','AdminController@vendortopSellingProductsYearDownload');
        Route::get('dashboard-vendororderStatusDownload/{type}/{year}','AdminController@vendororderStatusYearDownload');

        Route::get('vendordashboard','AdminController@vendordashboard');

        // Update Admin Password
        Route::match(['get','post'],'update-admin-password','AdminController@updateAdminPassword');

        // Check Admin Password
        Route::post('check-admin-password','AdminController@checkAdminPassword');

        // Update Admin Details
        Route::match(['get','post'],'update-admin-details','AdminController@updateAdminDetails');

        // Update Vendor Details
        Route::match(['get','post'],'update-vendor-details/{slug}','AdminController@updateVendorDetails');

        // Update Vendor Commission
        Route::post('update-vendor-commission','AdminController@updateVendorCommission');

        // View Admins / Subadmins / Vendors
        Route::get('admins/{type?}','AdminController@admins');

        // View Vendor Details
        Route::get('view-vendor-details/{id}','AdminController@viewVendorDetails');

        // Update Admin Status
        Route::post('update-admin-status','AdminController@updateAdminStatus');

        // Admin Logout
        Route::get('logout','AdminController@logout');  

        // Sections
        Route::get('sections','SectionController@sections');
        Route::post('update-section-status','SectionController@updateSectionStatus');
        Route::get('delete-section/{id}','SectionController@deleteSection');
        Route::match(['get','post'],'add-edit-section/{id?}','SectionController@addEditSection');

        // Brands
        Route::get('brands','BrandController@brands');
        Route::post('update-brand-status','BrandController@updateBrandStatus');
        Route::get('delete-brand/{id}','BrandController@deleteBrand');
        Route::match(['get','post'],'add-edit-brand/{id?}','BrandController@addEditBrand');

        // Categories
        Route::get('categories','CategoryController@categories');
        Route::post('update-category-status','CategoryController@updateCategoryStatus');
        Route::match(['get','post'],'add-edit-category/{id?}','CategoryController@addEditCategory');
        Route::get('append-categories-level','CategoryController@appendCategoryLevel');
        Route::get('delete-category/{id}','CategoryController@deleteCategory');
        Route::get('delete-category-image/{id}','CategoryController@deleteCategoryImage');

        // Products
        Route::get('products','ProductsController@products');
        Route::post('update-product-status','ProductsController@updateProductStatus');
        Route::get('delete-product/{id}','ProductsController@deleteProduct');
        Route::match(['get','post'],'add-edit-product/{id?}','ProductsController@addEditProduct');
        Route::get('delete-product-image/{id}','ProductsController@deleteProductImage');
        Route::get('delete-product-video/{id}','ProductsController@deleteProductVideo');

        // Attributes
        Route::match(['get','post'],'add-edit-attributes/{id}','ProductsController@addAttributes');
        Route::post('update-attribute-status','ProductsController@updateAttributeStatus');
        Route::get('delete-attribute/{id}','ProductsController@deleteAttribute');
        Route::match(['get','post'],'edit-attributes/{id}','ProductsController@editAttributes');

        // Filters
        Route::get('filters','FilterController@filters');
        Route::get('filters-values','FilterController@filtersValues');
        Route::post('update-filter-status','FilterController@updateFilterStatus');
        Route::post('update-filter-value-status','FilterController@updateFilterValueStatus');
        Route::match(['get','post'],'add-edit-filter/{id?}','FilterController@addEditFilter');
        Route::match(['get','post'],'add-edit-filter-value/{id?}','FilterController@addEditFilterValue');
        Route::post('category-filters','FilterController@categoryFilters');

        // Images
        Route::match(['get','post'],'add-images/{id}','ProductsController@addImages');
        Route::post('update-image-status','ProductsController@updateImageStatus');
        Route::get('delete-image/{id}','ProductsController@deleteImage');

        // Banners
        Route::get('banners','BannersController@banners');
        Route::post('update-banner-status','BannersController@updateBannerStatus');
        Route::get('delete-banner/{id}','BannersController@deleteBanner');
        Route::match(['get','post'],'add-edit-banner/{id?}','BannersController@addEditBanner');

        // Coupons
        Route::get('coupons','CouponsController@coupons');
        Route::post('update-coupon-status','CouponsController@updateCouponStatus');
        Route::get('delete-coupon/{id}','CouponsController@deleteCoupon');
        Route::match(['get','post'],'add-edit-coupon/{id?}','CouponsController@addEditCoupon');

        // Users
        Route::get('users','UserController@users');
        Route::post('update-user-status','UserController@updateUserStatus');

        // CMS Pages
        Route::get('cms-pages','CmsController@cmspages');
        Route::post('update-cms-page-status','CmsController@updatePageStatus');
        Route::get('delete-page/{id}','CmsController@deletePage');
        Route::match(['get','post'],'add-edit-cms-page/{id?}','CmsController@addEditCmsPage');

        // Orders
        Route::get('orders','OrderController@orders');
        Route::get('orders/{id}','OrderController@orderDetails');
        Route::post('update-order-status','OrderController@updateOrderStatus');
        Route::post('update-order-item-status','OrderController@updateOrderItemStatus');
        Route::get('inbox', 'OrderController@inbox');

        //reply
        Route::get('reply/{messageid}', 'OrderController@reply');
        Route::post('reply/submit', 'OrderController@Replied')->name('reply.submit');

        // Order Invoices
        Route::get('orders/invoice/{id}','OrderController@viewOrderInvoice');
        Route::get('orders/invoice/pdf/{id}','OrderController@viewPDFInvoice');

        // Shipping Charges
        Route::get('shipping-charges','ShippingController@shippingCharges');
        Route::post('update-shipping-status','ShippingController@updateShippingStatus');
        Route::match(['get','post'],'edit-shipping-charges/{id}','ShippingController@editShippingCharges');

        // Newsletter Subscribers
        Route::get('subscribers','NewsletterController@subscribers');
        Route::post('update-subscriber-status','NewsletterController@updateSubscriberStatus');
        Route::get('delete-subscriber/{id}','NewsletterController@deleteSubscriber');
        Route::get('export-subscribers','NewsletterController@exportSubscribers');

        // Currencies
        Route::get('currencies','CurrencyController@currencies');
        Route::post('update-currency-status','CurrencyController@updateCurrencyStatus');
        Route::get('delete-currency/{id}','CurrencyController@deleteCurrency');
        Route::match(['get','post'],'add-edit-currency/{id?}','CurrencyController@addEditCurrency');

        // Ratings
        Route::get('ratings','RatingController@ratings');
        Route::post('update-rating-status','RatingController@updateRatingStatus');
        Route::get('delete-rating/{id}','RatingController@deleteRating');

    });
});

Route::get('orders/invoice/download/{id}','App\Http\Controllers\Admin\OrderController@viewPDFInvoice');

Route::namespace('App\Http\Controllers\Front')->group(function(){
    Route::get('/','IndexController@index');

    // Listing/Categories Routes
    $catUrls = Category::select('url')->where('status',1)->get()->pluck('url')->toArray();
    /*dd($catUrls); die;*/
    foreach ($catUrls as $key => $url) {
        Route::match(['get','post'],'/'.$url,'ProductsController@listing');
    }

    // CMS Pages Routes
    $cmsUrls = CmsPage::select('url')->where('status',1)->get()->pluck('url')->toArray();
    foreach($cmsUrls as $url){
        Route::get($url,'CmsController@cmsPage');
    }


    // Vendor Products
    Route::get('/products/{vendorid}','ProductsController@vendorListing');

    // Product Detail Page
    Route::get('/product/{id}','ProductsController@detail');
    Route::get('/index/{id}','ProductsController@indexdetail');

    // Get Product Attribute Price
    Route::post('get-product-price','ProductsController@getProductPrice');

    // Vendor Login/Register
    Route::get('vendor/login-register','VendorController@loginRegister');

    // Vendor Register
    Route::post('vendor/register','VendorController@vendorRegister');
    Route::get('vendor/login-register','VendorController@showBarangayTable');

    // Confirm Vendor Account
    Route::get('vendor/confirm/{code}','VendorController@confirmVendor');

    // Add to Cart Route
    Route::post('cart/add','ProductsController@cartAdd');

    // Cart Route
    Route::get('cart','ProductsController@cart');

    // Update Cart Item Quantity
    
    Route::match(['get', 'post'], '/cart/update', [ProductsController::class, 'update']);


    // Delete Cart Item
    Route::post('cart/delete','ProductsController@cartDelete');

    // User Login/Register
    Route::get('user/login-register',['as'=>'login','uses'=>'UserController@loginRegister']);

    // User Register
    Route::post('user/register','UserController@userRegister');
    Route::get('user/login-register','UserController@showBarangayTable');

    // Search Products
    Route::get('search-products','ProductsController@listing');

    // Check Pincode
    Route::post('check-pincode','ProductsController@checkPincode');

    // Contact Page
    Route::match(['GET','POST'],'contact','CmsController@contact');

    // Add Susbcriber Email
    Route::post('add-subscriber-email','NewsletterController@addSubscriber');

    // Add Rating/Review
    Route::post('add-rating','RatingController@addRating');

    Route::group(['middleware'=>['auth']],function(){
        // User Account
        Route::match(['GET','POST'],'user/account','UserController@userAccount');

        // User Update Password
        Route::post('user/update-password','UserController@userUpdatePassword');

        // Apply Coupon
        Route::post('/apply-coupon','ProductsController@applyCoupon');

        // Checkout
        Route::match(['GET','POST'],'/checkout','ProductsController@checkout');

        // Get Delivery Address
        Route::post('get-delivery-address','AddressController@getDeliveryAddress');

        // Save Delivery Address
        Route::post('save-delivery-address','AddressController@saveDeliveryAddress');

        // Remove Delivery Address
        Route::post('remove-delivery-address','AddressController@removeDeliveryAddress');

        // Thanks
        Route::get('thanks','ProductsController@thanks');

        // Users Orders
        Route::get('user/orders/{product_id?}','OrderController@orders');
        Route::match(['get','post'],'cancel-product/{product_id?}','OrderController@cancelProduct');
        Route::match(['get','post'],'cancel-order/{product_id?}','OrderController@cancelOrder');//cancel order
        Route::get('replace-order/{product_id?}', 'OrderController@replaceProduct');//replace form
        Route::post('replace-order/RRlist', 'OrderController@returnQry');
        Route::get('/Inbox', 'OrderController@Inbox')->name('inbox_page');

        //Message
        Route::any('orders/inbox', 'OrderController@view_inbox');
        Route::get('message/message/{messagelist}', 'OrderController@message');
        Route::post('message/message/submit', 'OrderController@messageReply')->name('message.submit');


        // Paypal Routes
        Route::get('paypal','PaypalController@paypal');
        Route::post('pay', 'PaypalController@pay')->name('payment');
        Route::get('success','PaypalController@success');
        Route::get('error','PaypalController@error');

        // iyzipay Routes
        Route::get('iyzipay','IyzipayController@iyzipay');
        Route::get('iyzipay/pay','IyzipayController@pay');

         // GCash Routes
         Route::match(['GET','POST'], 'gcash/pay', 'GcashController@pay')->name('gcash.pay');
         Route::get('/payment/success', 'GcashController@success');
         Route::get('/payment/thanks', 'GcashController@thanks')->name('payment.success');
         Route::get('gcash/error', 'GcashController@error');


    });

    // User Login
    Route::post('user/login','UserController@userLogin');

    // User Forgot Password
    Route::match(['get','post'],'user/forgot-password','UserController@forgotPassword');

    // User Logout
    Route::get('user/logout','UserController@userLogout');

    // Confirm User Account
    Route::get('user/confirm/{code}','UserController@confirmAccount');

});


