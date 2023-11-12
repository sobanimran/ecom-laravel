<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SubCategory;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Image;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::latest()->with('product_image');
        if (!empty($request->get('keyword'))) {
            $products = $products->where('title', 'like', '%' . $request->get('keyword') . '%');
        }
        $products = $products->paginate(10);
        //  dd($products);
        return view('admin.products.list', compact('products'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brand::orderBy('name', 'ASC')->get();
        return view('admin.products.create', compact('categories', 'brands'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_feature' => 'required|in:Yes,No',
        ];

        if ($request->track_qty == 'Yes' && !empty($request->track_qty)) {
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->passes()) {
            $product = new Product;
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->description = $request->description;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_feature;
            $product->save();

            // Save Gallary Image
            if (!empty($request->image_array)) {
                foreach ($request->image_array as $temp_img_id) {
                    $temp_img_info = TempImage::find($temp_img_id);
                    $extArray = explode('.', $temp_img_info->name);
                    //168540927.jpg
                    $ext = last($extArray); // jpg gif jpeg




                    $productImage = new ProductImage();
                    $productImage->product_id = $product->id;
                    $productImage->image = 'Null';
                    $productImage->save();

                    $imageName = $product->id . '-' . $productImage->id . '-' . time() . '.' . $ext;
                    $productImage->image = $imageName;
                    $productImage->save();

                    // generate image thumbnail product
                    //large Image
                    $spath = public_path() . '/temp/' . $temp_img_info->name;
                    $dpath = public_path() . '/uploads/product/large/' . $imageName;

                    $image = Image::make($spath);

                    // resize the image to a width of 300 and constrain aspect ratio (auto height)
                    $image->resize(1400, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $image->save($dpath);

                    // small image

                    $dpath = public_path() . '/uploads/product/small/' . $imageName;

                    $image = Image::make($spath);

                    // resize the image to a width of 300 and constrain aspect ratio (auto height)
                    $image->fit(300, 300);
                    $image->save($dpath);

                }
            }

            $request->session()->flash('success', "product Added successfuly");

            return response()->json([
                'status' => true,
                'message' => "product Added successfully"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
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
    public function edit(string $id, Request $request)
    {
        $product = Product::find($id);
        if (empty($product)) {
            $request->session()->flash('error', 'Product  Not Found');
            return redirect()->route('products.index');
        }

        $product_images = ProductImage::where('product_id', $product->id)->get();
        $subcategories = SubCategory::where('category_id', $product->category_id)->get();
        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brand::orderBy('name', 'ASC')->get();

        return view('admin.products.edit', compact('categories', 'brands', 'product', 'subcategories', 'product_images')); //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);

        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products,slug,' . $id . ',id',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products,sku,' . $id . ',id',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_feature' => 'required|in:Yes,No',
        ];

        if ($request->track_qty == 'Yes' && !empty($request->track_qty)) {
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->passes()) {

            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->description = $request->description;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_feature;
            $product->save();

            // Save Gallary Image


            $request->session()->flash('success', "product updated successfuly");

            return response()->json([
                'status' => true,
                'message' => "product updated successfully"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        $product = Product::find($id);

        if (empty($product)) {
            $request->session()->flash('error', "product missing in database");
            return response()->json([
                'status' => false,
                'notfound' => true

            ]);
        }
        $productImages = ProductImage::where('product_id', $product->id)->get();
        if (!empty($productImages)) {
            foreach ($productImages as $productImage) {
                File::delete(public_path('uploads/product/large/' . $productImage->image));
                File::delete(public_path('uploads/product/small/' . $productImage->image));
            }
            ProductImage::where('product_id', $product->id)->delete();
        }
        $product->delete();
        //$request->session()->flash('success', "Product deleted successfully");
        return response()->json([
            'status' => true,
            'message' => "Product deleted successfully"

        ]);


    }
}