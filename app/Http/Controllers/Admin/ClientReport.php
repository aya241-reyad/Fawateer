<?php

namespace App\Http\Controllers\Admin;

use App\Models\Invoice;
use App\Models\Section;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClientReport extends Controller
{

  
     function __construct()
{
$this->middleware('permission:تقرير العملاء', ['only' => ['index','search']]);


}

public function index(){
$sections=Section::all();
return view('reports.client',compact('sections'));
}


public function search(Request $request){

if ($request->Section && $request->product && $request->start_at =='' && $request->end_at=='') {

       
      $invoices = Invoice::select('*')->where('section','=',$request->Section)->where('product','=',$request->product)->get();
      $sections = Section::all();
       return view('reports.client',compact('sections'))->withDetails($invoices);

    
     }

      else {
       
       $start_at = date($request->start_at);
       $end_at = date($request->end_at);

      $invoices = invoices::whereBetween('invoice_Date',[$start_at,$end_at])->where('section','=',$request->Section)->where('product','=',$request->product)->get();
       $sections = sections::all();
       return view('reports.client',compact('sections'))->withDetails($invoices);

      
     }

}

}
