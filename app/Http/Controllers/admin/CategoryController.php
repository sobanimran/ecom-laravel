<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
//use GdImage;
//use Intervention\Image\Facades\Image;
use Image;



class CategoryController extends Controller
{
    public function index (Request $request){
        $categories = Category::latest();
        if(!empty($request->get('keyword'))){
            $categories = $categories->where('name','like','%'.$request->get('keyword').'%');
        }
        $categories =$categories->paginate(10);
      //  dd($categories);
        return view('admin.category.list',compact('categories'));
    }
    public function create (){
        return view('admin.category.create');
    }
    public function store (Request $request){
        $validator =Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:categories',

        ]);
        if($validator->passes()){
            $category = new Category();
            $category->name = $request->name; 
            $category->slug = $request->slug; 
            $category->status = $request->status;
            $category->showNav = $request->showNav;
            $category->save();

            // save image here 
            if(!empty($request->image_id)){
                $tempimag = TempImage::find($request->image_id);
                $extArray = explode('.',$tempimag->name);
                $ext=last($extArray);
                $newImageName =$category->id.'.'.$ext;
                $sourcePath = public_path().'/temp/'.$tempimag->name;
                $destinationPath = public_path().'/uploads/category/'.$newImageName;
                File::copy($sourcePath,$destinationPath);
                
                // genrate image thumbnail
                $destinationPath = public_path().'/uploads/category/thumb/'.$newImageName;
                $img = Image::make($sourcePath);
                // resize image to fixed size
                //$img->resize(450, 600);
                // add callback functionality to retain maximal original image size
                    $img->fit(800, 600, function ($constraint) {
                        $constraint->upsize();
                    });
                $img->save($destinationPath);
               
                $category->image =$newImageName;
                $category->save();
                } 

            $request->session()->flash('success','Category Added Successfully');
            return response()->json([
                'status'=> true,
                'message' => 'Category Added Successfully'
            ]);
        }else{
            return response()->json([
                'status'=> false,
                'errors' => $validator->errors()
            ]);
        }
        
    }
    public function edit ($id, Request $request){
        $category = Category::find($id);
        if(empty($category)){
            $request->session()->flash('error','Category Not Found');
            return redirect()->route('categories.index');
        }
        return view('admin.category.edit',compact('category'));
 
        
    }
    public function update ($id, Request $request){
        $category = Category::find($id);
        if(empty($category)){
            $request->session()->flash('error','Category Not Found');
            return response()->json([
                'status'=>false,
                'notfound'=>true,
                'message'=>'Category Not found'
        
             ]);
        }
        $validator =Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$id.',id',

        ]);
        if($validator->passes()){
           
            $category->name = $request->name; 
            $category->slug = $request->slug; 
            $category->status = $request->status;
            $category->showNav = $request->showNav;
            $category->save();
            
            $oldImage = $category->image;
          //  dd($oldImage) ;
            // save image here 
            if(!empty($request->image_id)){
                $tempimag = TempImage::find($request->image_id);
                $extArray = explode('.',$tempimag->name);
                $ext=last($extArray);

                $newImageName =$category->id.'-'.time().'.'.$ext;
                $sourcePath = public_path().'/temp/'.$tempimag->name;
                $destinationPath = public_path().'/uploads/category/'.$newImageName;
                File::copy($sourcePath,$destinationPath);
                
                // genrate image thumbnail
                $destinationPath = public_path().'/uploads/category/thumb/'.$newImageName;
                $img = Image::make($sourcePath);
                // resize image to fixed size
                //$img->resize(450, 600);
                // add callback functionality to retain maximal original image size
                    $img->fit(450, 600, function ($constraint) {
                        $constraint->upsize();
                    });
                $img->save($destinationPath);
               
                $category->image =$newImageName;
                $category->save();

                //Delet old image
                File::delete(public_path().'/uploads/category/thumb/'.$oldImage);
                File::delete(public_path().'/uploads/category/'.$oldImage);
                } 

            $request->session()->flash('success','Category Update Successfully');
            return response()->json([
                'status'=> true,
                'message' => 'Category Updated Successfully'
            ]);
        }else{
            return response()->json([
                'status'=> false,
                'errors' => $validator->errors()
            ]);
        }
        
    }
    public function destroy ($id , Request $request){
        $category = Category::find($id);
        if(empty($category)){
            $request->session()->flash('error','Category Not fouund');
            return response()->json([
                'status'=> true,
                'message'=>'CategoryNot found'
             ]);
        }        

     //Delet old image
     File::delete(public_path().'/uploads/category/thumb/'.$category->image);
     File::delete(public_path().'/uploads/category/'.$category->image);
     $category->delete();
     $request->session()->flash('success','Category deleted successfully');
     return response()->json([
        'status'=> true,
        'message'=>'Category Deleted successfully'
     ]);
        
    }
}
