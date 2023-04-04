<?php

namespace App\Http\Controllers\Admin;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\InvoiceDetails;
use App\Models\InvoiceAttachment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class InvoiceDetailsController extends Controller
{
public function details($id){
$invoice=Invoice::find($id);
$details=InvoiceDetails::where('Invoice_id',$id)->get();
$attachments=InvoiceAttachment::where('Invoice_id',$id)->get();
return view('invoices.invoiceDetails',compact('invoice','details','attachments'));
}


public function get_file($invoice_number,$file_name){

$st="Attachments";
$pathToFile = public_path($st.'/'.$invoice_number.'/'.$file_name);
return response()->download($pathToFile);

}

public function openFile($invoice_number,$file_name){
$st="Attachments";
$pathToFile = public_path($st.'/'.$invoice_number.'/'.$file_name);
return response()->file($pathToFile);
}






}
