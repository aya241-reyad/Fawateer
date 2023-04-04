<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Section;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
function __construct()
{
$this->middleware('permission:المنتجات', ['only' => ['index']]);
$this->middleware('permission: اضافة منتج', ['only' => ['store']]);
$this->middleware('permission:تعديل منتج ', ['only' => ['update']]);
$this->middleware('permission:حذف منتج', ['only' => ['destroy']]);

}




    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products=Product::all();
        $sections=Section::all();

        return view ('products.index',compact('products','sections'));
   
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'Product_name' => 'required|max:255',
             'description' => 'required',
             'section_id' => 'required|exists:sections,id',
        ],[

            'Product_name.required' =>'يرجي ادخال اسم المنتج',
            'description.required' =>'يرجي ادخال البيان',
            'section_id.required' =>'يرجي تحديد القسم ',


        ]);

            Product::create([
                'Product_name' => $request->Product_name,
                'description' => $request->description,
                'section_id' => $request->section_id,

            ]);
            session()->flash('Add', 'تم اضافة المنتج بنجاح ');
            return redirect('/products');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

    
        $sec_id = Section::where('section_name', $request->section_name)->first()->id;
        $validatedData = $request->validate([
            'Product_name' => 'required|max:255',
            'description' => 'required',
            
        ],[

            'Product_name.required' =>'يرجي ادخال اسم المنتج',
            'description.required' =>'يرجي ادخال البيان',
            
        ]);

        $product = Product::findOrFail($request->pro_id);
        $product->update([
            'Product_name' => $request->Product_name,
            'description' => $request->description,
            'section_id' => $sec_id,
        ]);

        session()->flash('edit','تم تعديل المنتج بنجاج');
        return redirect('/products');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        Product::find( $request->pro_id)->delete();
        session()->flash('delete','تم حذف المنتج بنجاح');
        return redirect('/products');
    }
}
