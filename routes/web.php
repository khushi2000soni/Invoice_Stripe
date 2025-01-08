<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataBaseBackupController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportCategoryController;
use App\Http\Controllers\ReportCustomerController;
use App\Http\Controllers\ReportProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Livewire\OnlineStatus;
use App\Models\Customer;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;


// Authentication Routes
Route::group(['middleware' => 'guest'], function () {
    Route::get('/',[LoginController::class,'index'])->name('login');
    Route::get('/login',[LoginController::class,'index'])->name('login');
    Route::post('/login',[LoginController::class,'login'])->name('authenticate');
    Route::get('/forgot-password',[ForgotPasswordController::class,'index'])->name('forgot.password');
    Route::post('/forgot-pass-mail',[ForgotPasswordController::class,'sendResetLinkEmail'])->name('password_mail_link');
    Route::get('reset-password/{token}/{email}', [ResetPasswordController::class,'showform'])->name('resetPassword');
    Route::post('/reset-password',[ResetPasswordController::class,'resetpass'])->name('reset-new-password');

});

Route::middleware(['auth','PreventBackHistory'])->group(function () {
    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');
    Route::get('/logout',[LoginController::class,'logout'])->name('logout');
    Route::resource('/roles',RoleController::class);
    Route::get('/profiles',[UserController::class,'showprofile'])->name('user.profile');
    Route::post('/profile-update', [UserController::class,'updateprofile'])->name('profile.update');
    Route::post('/profile-image', [UserController::class,'updateprofileImage'])->name('profile-image.update');
    Route::get('/change-password',[UserController::class,'showchangepassform'])->name('user.change-password');
    Route::post('/change-password',[UserController::class,'updatePassword'])->name('reset-password');
    Route::resource('/address',AddressController::class);
    Route::get('/address-printView/{address_id?}',[AddressController::class,'printView'])->name('address.print');
    Route::get('/address-export/{address_id?}',[AddressController::class,'export'])->name('address.export');

    Route::resource('/categories',CategoryController::class);
    Route::get('/categories-printView/{address_id?}',[CategoryController::class,'printView'])->name('categories.print');
    Route::get('categories-export/{address_id?}',[CategoryController::class,'export'])->name('categories.export');

    Route::resource('/staff',UserController::class);
    Route::get('/staff/password/{id}',[UserController::class,'staffpassword'])->name('staff.password');
    Route::put('/staff/password/{id}',[UserController::class,'staffUpdatePass'])->name('staff.change-password');
    Route::get('/staff-printView',[UserController::class,'printView'])->name('staff.print');
    Route::get('staff-export/',[UserController::class,'export'])->name('staff.export');

    Route::get('/staff/typeindex/{type?}',[UserController::class,'typeindex'])->name('staff.typeindex');
    Route::patch('/staff/{staff}/rejoin',[UserController::class,'rejoin'])->name('staff.rejoin');

    Route::resource('/customers',CustomerController::class);
    Route::get('/customers/index/{address_id?}',[CustomerController::class,'index'])->name('customer.index');
    Route::get('/customers-printView/{address_id?}',[CustomerController::class,'printView'])->name('customers.print');
    Route::get('/customers-export/{address_id?}',[CustomerController::class,'export'])->name('customers.export');
    Route::get('/phone-book',[CustomerController::class,'showPhoneBook'])->name('showPhoneBook');
    Route::get('/phone-book-printView/{address_id?}',[CustomerController::class,'PhoneBookprintView'])->name('PhoneBook.print');
    Route::get('/phone-book-export/{address_id?}',[CustomerController::class,'PhoneBookexport'])->name('PhoneBook.export');


    Route::get('/products/index/{category_id?}', [ProductController::class,'index'])->name('products.index');
    Route::resource('/products',ProductController::class);
    Route::get('/products/merge/{id}',[ProductController::class,'mergeForm'])->name('products.showMerge');
    Route::post('/products/merge',[ProductController::class,'mergeProduct'])->name('products.merge');
    Route::get('/products-printView/{category_id?}/{product_id?}',[ProductController::class,'printView'])->name('products.print');
    Route::get('/products-export/{category_id?}/{product_id?}',[ProductController::class,'export'])->name('products.export');

    Route::resource('/device',DeviceController::class);

    Route::resource('/orders',OrderController::class);
    Route::delete('/orders/{id}/{type?}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::get('/orders-printView',[OrderController::class,'allinvoicePrintView'])->where('customer_id', '.*')->name('orders.allprint');
    Route::get('/orders-export',[OrderController::class,'allinvoiceExport'])->where('customer_id', '.*')->name('orders.allexport');
    Route::get('/orders/print/{order}/{type?}',[OrderController::class,'printView'])->name('orders.print');
    Route::get('/orders/detail-print/{order}/{type?}',[OrderController::class,'orderdetailprint'])->name('orders.orderdetailprint');
    Route::patch('/orders/{order}/restore',[OrderController::class,'restore'])->name('orders.restore');
    Route::get('/get-orders/{type?}', [OrderController::class,'getTypeOrder'])->name('orders.getTypeOrder');
    Route::get('/orders/{order}/generate-pdf/{type?}',[OrderController::class,'generatePdf'])->name('orders.generate-pdf');
    Route::get('/print-pdf/{order}/{type?}',[OrderController::class,'printPDF'])->name('orders.print-pdf');
    Route::get('/share-email/{order}',[OrderController::class,'shareEmail'])->name('orders.share-email');
    Route::post('/share-email/{order}',[OrderController::class,'sendshareEmail'])->name('orders.send-email');
    Route::get('/share-whatsapp/{order}',[OrderController::class,'shareWhatsApp'])->name('orders.share-whatsapp');

    Route::get('/reports/category',[ReportCategoryController::class,'index'])->name('reports.category');
    Route::get('/reports/category/products', [ReportCategoryController::class,'CategoryProductReport'])->name('reports.category.products');
    Route::get('/reports/category/piechart',[ReportCategoryController::class,'getCategoryChartData'])->name('reports.category.piechart');
    Route::get('/reports/category-printView',[ReportCategoryController::class,'CatgoryReportPrintView'])->name('reports.category.print');
    Route::get('/reports/category-export',[ReportCategoryController::class,'CatgoryReportExport'])->name('reports.category.export');
    Route::get('/reports/category/product-printView',[ReportCategoryController::class,'CatgoryProductReportPrintView'])->name('reports.category.product.print');
    Route::get('/reports/category/product-export',[ReportCategoryController::class,'CatgoryProductReportExport'])->name('reports.category.product.export');


    Route::get('/modified/customer/index',[ReportCustomerController::class,'index'])->name('modified.customer.index');
    Route::put('/modified/customer/{customer}/approve', [ReportCustomerController::class, 'approve'])->name('modified.customers.approve');
    Route::get('/modified/product/index',[ReportProductController::class,'index'])->name('modified.product.index');
    Route::put('/modified/product/{product}/approve', [ReportProductController::class, 'approve'])->name('modified.product.approve');

    Route::get('/fetch-report-data', [DashboardController::class,'fetchReportData'])->name('fetchReportData');
    Route::get('/getSoldProducts', [ReportCategoryController::class,'getSoldProducts'])->name('getSoldProducts');
    Route::get('/settings/{tab?}',[SettingController::class,'index'])->name('settings');
    Route::post('/settings/update',[SettingController::class,'update'])->name('settings.update');

    Route::get('/backups', [DataBaseBackupController::class,'index'])->name('backup.index');
    Route::post('/backups/create', [DataBaseBackupController::class, 'runBackupEmailCommand'])->name('backups.create');
    Route::post('/backups/restore', [DataBaseBackupController::class, 'restoreBackup'])->name('backups.restore');
    Route::post('/backups/delete', [DataBaseBackupController::class, 'deleteBackup'])->name('backups.delete');
    Route::get('/backups/download/{fileName}', [DataBaseBackupController::class, 'downloadBackup'])->name('backups.download');
    Route::post('/backups/upload', [DataBaseBackupController::class, 'uploadBackup'])->name('backups.upload');


});

Route::get('/check-connectivity', function() {
    try {
      $response = file_get_contents('https://8.8.8.8');
      if($response){
        return response()->json(['status' => true]);
      }
      else{
        return response()->json(['status' => false]);
      }

    } catch (\Exception $e) {
      return response()->json(['status' => false]);
    }
});

Route::get('/phpinfo', function () {
    phpinfo();
});

Route::get('/refresh', function () {
    // Run Artisan commands
    Artisan::call('optimize:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    return 'App refreshed successfully!';
});
