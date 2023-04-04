<?php

namespace App\Http\Controllers\Admin;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InvoiceArcheiveController extends Controller
{
    function __construct()
{
$this->middleware('permission:ارشيف الفواتير', ['only' => ['index']]);
$this->middleware('permission:اضافة فاتورة', ['only' => ['create','store']]);

}
public function index(){

$invoices=Invoice::onlyTrashed()->get();
return view('invoices.archieveInvoice',compact('invoices'));
}

public function restore(Request $request)
    {
    $id = $request->invoice_id;
    $flight = Invoice::withTrashed()->where('id', $id)->restore();
    session()->flash('restore_invoice');
    return redirect('/invoices');
    }


public function destroy(Request $request){
$id = $request->invoice_id;
$invoice = Invoice::withTrashed()->where('id', $id)->first();
$invoice->forceDelete();
session()->flash('delete_invoice');
    return redirect('/archieve_invoices');


    }











}
