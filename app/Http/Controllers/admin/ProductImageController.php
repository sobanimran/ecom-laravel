<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Image;
class ProductImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request/*  */)
    {
        //dd($request->product_id);
        $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $spath = $image->getPathName();
        

        $productImage = new ProductImage();
        $productImage->product_id = $request->product_id;
        $productImage->image = 'Null';
        $productImage->save();
       
        $imageName=$request->product_id.'-'.$productImage->id.'-'.time().'.'.$ext;
        $productImage->image = $imageName;
        $productImage->save();


         //large Image
         
         $dpath = public_path().'/uploads/product/large/'.$imageName;

         $image =Image::make($spath);
       
        // resize the image to a width of 300 and constrain aspect ratio (auto height)
        $image->resize(1400, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $image->save($dpath);
        
        // small image
      
        $dpath = public_path().'/uploads/product/small/'.$imageName;

        $image =Image::make($spath);
      
       // resize the image to a width of 300 and constrain aspect ratio (auto height)
       $image->fit(300, 300);
       $image->save($dpath);

        return response()->json([
            'status'=>true,
            'image_id'=>$productImage->id,
            'image_path'=>asset('/uploads/product/small/'.$productImage->image),
            'message' => 'Image Saved successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $productImage = ProductImage::find($request->id);
        if(!empty($productImage)){
        // delete  images from folder
        File::delete(public_path('uploads/product/large/'.$productImage->image));
        File::delete(public_path('uploads/product/small/'.$productImage->image));

        // delete from db
        $productImage->delete();
        
        return response()->json([
            'status'=>true,
            'message' => 'Image Delete  successfully',
        ]);
    }
    return response()->json([
        'status'=>false,
        'message' => 'no image found on this id',
    ]);
    }
}
