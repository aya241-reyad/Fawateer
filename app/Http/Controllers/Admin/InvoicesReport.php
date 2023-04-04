<?php

namespace App\Http\Controllers\Admin;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InvoicesReport extends Controller
{

     function __construct()
{
$this->middleware('permission: تقرير الفواتير', ['only' => ['index','search']]);


}


    public function index(){

        return view('reports.invoices');
    }


    public function search(Request $request)
    {
        $rdio = $request->rdio;
 if ($rdio == 1) {
// في حالة عدم تحديد تاريخ
        if ($request->type && $request->start_at =='' && $request->end_at =='') {
            
           $invoices = Invoice::select('*')->where('Status','=',$request->type)->get();
           $type = $request->type;
           return view('reports.invoices',compact('type'))->withDetails($invoices);
        }
        
        // في حالة تحديد تاريخ استحقاق
        else {
           
          $start_at = date($request->start_at);
          $end_at = date($request->end_at);
          $type = $request->type;
          
          $invoices = Invoice::whereBetween('invoice_Date',[$start_at,$end_at])->where('Status','=',$request->type)->get();
          return view('reports.invoices',compact('type','start_at','end_at'))->withDetails($invoices);
          
        }

    }  else {
        
        $invoices = Invoice::select('*')->where('invoice_number','=',$request->invoice_number)->get();
        return view('reports.invoices')->withDetails($invoices);
        
    }

}







}