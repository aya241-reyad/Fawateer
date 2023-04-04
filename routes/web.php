<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ClientReport;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\InvoicesReport;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\InvoiceDetailsController;
use App\Http\Controllers\Admin\InvoiceArcheiveController;
use App\Http\Controllers\Admin\InvoiceAttachmentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard',[HomeController::class, 'index'] )->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('invoices', InvoiceController::class);
    Route::resource('sections', SectionController::class);
    Route::resource('products', ProductController::class);
    Route::get('/section/{id}', [InvoiceController::class, 'getProducts']);
    Route::get('/InvoicesDetails/{id}', [InvoiceDetailsController::class, 'details']);
    Route::get('/view_file/{invoice_number}/{file_name}', [InvoiceDetailsController::class, 'openFile']);
    Route::get('/get_file/{invoice_number}/{file_name}', [InvoiceDetailsController::class, 'get_file']);
    Route::post('/destroy_file', [InvoiceAttachmentController::class,'destroy'])->name('destroy_file');;
    Route::post('/InvoiceAttachments', [InvoiceAttachmentController::class,'save']);
    Route::patch('/update_status/{id}', [InvoiceController::class,'change_status'])->name('update_status');
    Route::get('/paid_invoice', [InvoiceController::class, 'paidInvoice']);
    Route::get('/non_paid_invoice', [InvoiceController::class, 'nonPaidInvoice']);
    Route::get('/paid_partial_invoice', [InvoiceController::class, 'partialPaidInvoice']);
    Route::get('/archieve_invoices', [InvoiceArcheiveController::class, 'index']);
    Route::patch('/restore_archieved_invoices', [InvoiceArcheiveController::class,'restore'])->name('restore_invoice');
    Route::delete('/delete_archieve_invoices', [InvoiceArcheiveController::class, 'destroy'])->name('delete_archieve_invoices');
    Route::get('/print_invoice/{id}', [InvoiceController::class, 'printInvoice']);
    Route::get('invoice/export', [InvoiceController::class, 'export'])->name('invoice/export');
    Route::resource('roles',RoleController::class);
    Route::resource('users',UserController::class);
    Route::get('mark_as_read_all', [InvoiceController::class, 'markAsReadAll'])->name('mark_as_read_all');
});
//report
Route::middleware('auth')->group(function () {
     Route::get('invoices_report', [InvoicesReport::class, 'index']);
     Route::post('Search_invoices', [InvoicesReport::class, 'search']);
     Route::get('clients_report', [ClientReport::class, 'index']);
     Route::post('Search_clients', [ClientReport::class, 'search']);
    
    });

require __DIR__.'/auth.php';
