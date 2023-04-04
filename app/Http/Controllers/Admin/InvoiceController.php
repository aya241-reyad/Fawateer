<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Invoice;
use App\Models\Section;
use Illuminate\Http\Request;
use App\Exports\InvoiceExport;
use App\Models\InvoiceDetails;
use App\Models\InvoiceAttachment;
use App\Notifications\AddInvoice;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AddInvoiceNotification;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
function __construct()
{
$this->middleware('permission:قائمة الفواتير', ['only' => ['index']]);
$this->middleware('permission:اضافة فاتورة', ['only' => ['create','store']]);
$this->middleware('permission:تعديل الفاتورة', ['only' => ['edit','update']]);
$this->middleware('permission:حذف الفاتورة', ['only' => ['destroy']]);
$this->middleware('permission:ارشفة الفاتورة', ['only' => ['destroy']]);
$this->middleware('permission:تغير حالة الدفع', ['only' => ['show','change_status']]);
$this->middleware('permission:الفواتير المدفوعة', ['only' => ['paidInvoice']]);
$this->middleware('permission:الفواتير الغير مدفوعة', ['only' => ['nonPaidInvoice']]);
$this->middleware('permission:الفواتير المدفوعة جزئيا', ['only' => ['partialPaidInvoice']]);
$this->middleware('permission: طباعةالفاتورة', ['only' => ['printInvoice']]);
$this->middleware('permission: تصدير EXCEL', ['only' => ['export']]);
}


    public function index()
    {
    $invoices=Invoice::all();
    return view('invoices.index',compact('invoices'));


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    $sections=Section::all();
    return view('invoices.addinvoice',compact('sections'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)

    {
    Invoice::create([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
            'user' => auth()->user()->name
        ]);

$invoice_id = Invoice::latest()->first()->id;
        InvoiceDetails::create([
            'Invoice_id' => $invoice_id,
            'invoice_number' => $request->invoice_number,
            'product' => $request->product,
            'Section' => $request->Section,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
            'user' => (Auth::user()->name),
        ]);

        if ($request->hasFile('pic')) {

            $invoice_id = Invoice::latest()->first()->id;
            $image = $request->file('pic');
            $file_name = $image->getClientOriginalName();
            $invoice_number = $request->invoice_number;

            $attachments = new InvoiceAttachment();
            $attachments->file_name = $file_name;
            $attachments->invoice_number = $invoice_number;
            $attachments->Created_by = Auth::user()->name;
            $attachments->invoice_id = $invoice_id;
            $attachments->save();

            // move pic
            $imageName = $request->pic->getClientOriginalName();
            $request->pic->move(public_path('Attachments/' . $invoice_number), $imageName);

            // notify by mail 
            // $user=User::first();
            // Notification::send($user, new AddInvoice($invoice_id));

            $user = User::get();
            Notification::send( $user, new AddInvoiceNotification($invoice_id));


            return redirect('/invoices');
        }
    }











    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $invoice=Invoice::where('id', $id)->first();
        return view('invoices.changestatus',compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    $invoice = Invoice::where('id', $id)->first();
    $sections =Section::all();
    return view('invoices.editinvoice', compact('sections', 'invoice'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'note' => $request->note,

        ]);

        session()->flash('edit', 'تم تعديل الفاتورة بنجاح');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
    $id = $request->invoice_id;
    $invoice=Invoice::where('id',$id)->first();
    $attachment=InvoiceAttachment::where('invoice_id',$id)->get();
    $details=InvoiceDetails::where('invoice_id',$id)->get();
    $id_page =$request->id_page;
    if(!$id_page==2){
    if (!empty($attachment->invoice_number)) {

            Storage::disk('public_uploads')->deleteDirectory($attachment->invoice_number);
        }
        $attachment->each->delete();
        $details->each->delete();
        $invoice->forceDelete();
    session()->flash('delete_invoice');
    return redirect('/invoices');
    }else{
    $invoice->delete();
    session()->flash('archive_invoice');
    return redirect('/invoices');




    }
    
    }


public function getProducts($id){
$products =DB::table('products')->where('section_id',$id)->pluck('product_name','id');
return json_encode($products);
}

public function change_status(Request $request, $id){
$invoice=Invoice::where('id',$id)->first();
if ($request->Status === 'مدفوعة') {
            $invoice->update([
                'value_status' => 1,
                'Status' => $request->Status,
                'Payment_Date' => $request->Payment_Date,
            ]);

            InvoiceDetails::create([
                'Invoice_id' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'Status' => $request->Status,
                'value_status' => 1,
                'note' => $request->note,
                'Payment_Date' => $request->Payment_Date,
                'user' => (Auth::user()->name),
            ]);
        }else {
            $invoice->update([
                'value_status' => 3,
                'Status' => $request->Status,
                'Payment_Date' => $request->Payment_Date,
            ]);
            InvoiceDetails::create([
                'Invoice_id' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'Status' => $request->Status,
                'value_status' => 3,
                'note' => $request->note,
                'Payment_Date' => $request->Payment_Date,
                'user' => (Auth::user()->name),
            ]);
        }
        session()->flash('Status_Update');
        return redirect('/invoices');

        }


        public function paidInvoice(){
        $invoices=Invoice::where('Value_Status',1)->get();
        return view('invoices.paidInvoice',compact('invoices'));
        
        }

        public function nonPaidInvoice(){
        $invoices=Invoice::where('Value_Status',2)->get();
        return view('invoices.nonPaidInvoice',compact('invoices'));
        
        }
        public function partialPaidInvoice(){
            $invoices=Invoice::where('Value_Status',3)->get();
        return view('invoices.nonPaidInvoice',compact('invoices'));
        }

        public function printInvoice($id){

        $invoices = Invoice::where('id', $id)->first();
        return view('invoices.printInvoice',compact('invoices'));

        }


    public function export() 
    {
        return Excel::download(new InvoiceExport, 'invoices.xlsx');
    }


public function markAsReadAll(Request $request){

$userUnreadNotification= auth()->user()->unreadNotifications;

        if($userUnreadNotification) {
            $userUnreadNotification->markAsRead();
            return back();
        }
}





}















