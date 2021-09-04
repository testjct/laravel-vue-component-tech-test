<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Auth;

class ProductController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display View
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('product');
    }

    /**
     * Get all products
     *
     * @return \Illuminate\Http\Response
     */
    public function get(Request $request)
    {
        $products = Product::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();
        return response()->json($products);
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return response()->json($request->image);
        $items = $request->validate([
            'name' => 'required',
            'year' =>'required|numeric|between:1990,'.date('Y'),
            'image' =>'required|image',              
          ],[            
            'name.required' => 'Name is required!',
            'year.required' =>'Manufactured year is required!',
            'image.required' =>'Image is required!',
            'image.image' =>'Image is required!',
          ]);

        
        $fileName = 'placeholder.png';
        $product = new Product();
        $product->name = $request->name;
        $product->manufactured_year = $request->year;
        $product->user_id = Auth::id();

        if($request->hasFile('image')) {
            $fileName = time().'_'.$request->image->getClientOriginalName();
            $request->image->move(public_path('/files'), $fileName);
        }

        $product->image = $fileName;
        if($product->save()){
            return response()->json(['message' => "Product added successfully!"], 200);
        } else {
            return response()->json(['message' => "Internal server error"]);
        }
        
    }

    /**
     * Update the specified product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $items = $request->validate([
            'name' => 'required',
            'year' =>'required|numeric',
            'image' =>'image',              
          ],[            
            'name.required' => 'Name is required!',
            'year.required' =>'Manufactured year is required!',
            'image.image' =>'Image is required!',
          ]);

        
        $product = Product::find($id);
        $product->name = $request->name;
        $product->manufactured_year = $request->year;
        $product->user_id = Auth::id();

        if($request->hasFile('image')) {
            $path = public_path()."/files/".$product->image;
            if (file_exists($path)){
                unlink($path);
            }

            $fileName = time().'_'.$request->image->getClientOriginalName();
            $request->image->move(public_path('/files'), $fileName);
            $product->image = $fileName;
        }
        
        if($product->save()){
            return response()->json(['message' => "Product updated successfully!"], 200);
        } else {
            return response()->json(['message' => "Internal server error"]);
        }
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::where('id',$id)->first();

        $path = public_path()."/files/".$product->image;
        if (file_exists($path)){
            unlink($path);
        }

        if($product->delete()){
            return response()->json(true);
        } else {
            return response()->json(false);
        }
    }
}
