<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{

    public function index (Request $request){
        $subCategories = SubCategory::select('sub_categories.*','categories.name as categoryName')
                                    ->latest('sub_categories.id')
                                    ->leftJoin('categories','categories.id','sub_categories.category_id');
        if(!empty($request->get('keyword'))){
            $subCategories = $subCategories->where('sub_categories.name','like','%'.$request->get('keyword').'%')->orWhere('categories.name','like','%'.$request->get('keyword').'%');
        }
        $subCategories =$subCategories->paginate(10);
      //  dd($categories);
        return view('admin.subCategory.list',compact('subCategories'));
    }

    public function create (){
        $categories = Category::orderBy('name','ASC')->get();
     //   $data['categories']=$categories; we also use $data instead of campact(categories)
       // dd($data);
        return view('admin.subCategory.create',compact('categories'));
    }
    public function store (Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:sub_categories',
            'category' => 'required',
            'status' => 'required'
        ]);
        if($validator->passes()){
            $subCategory = new SubCategory();
            $subCategory->name = $request->name; 
            $subCategory->slug = $request->slug; 
            $subCategory->status = $request->status;
            $subCategory->showNav = $request->showNav;
            $subCategory->category_id = $request->category;
            $subCategory->save();

            $request->session()->flash('success','sub Category created successfully');
            return response([
                'status'=>true,
                'message'=>'sub Category created successfully'
                
            ]);
        }else{
            return response([
                'status'=>false,
                'errors'=> $validator->errors()
            ]);
        }
     
    }

    public function edit ($id, Request $request){
        $subCategory = SubCategory::find($id);
        if(empty($subCategory)){
            $request->session()->flash('error','Sub Category Not Found');
            return redirect()->route('sub-categories.index');
        }
        $categories = Category::orderBy('name','ASC')->get();
        return view('admin.subCategory.edit',compact('subCategory','categories'));
 
        
    }
    public function update ($id , Request $request) {
        $subCategory = SubCategory::find($id);
        if(empty($subCategory)){
            $request->session()->flash('error','Sub Category Not Found');
            return response([
                'status'=>false,
                'notfound'=>true,
                'message'=>'sub Category not found'
                
            ]);
        }
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:sub_categories,slug,'.$id.',id',
            'category' => 'required',
            'status' => 'required'
        ]);
        if($validator->passes()){
         
            $subCategory->name = $request->name; 
            $subCategory->slug = $request->slug; 
            $subCategory->status = $request->status;
            $subCategory->showNav = $request->showNav;
            $subCategory->category_id = $request->category;
            $subCategory->save();

            $request->session()->flash('success','sub Category updated successfully');
            return response([
                'status'=>true,
                'message'=>'sub Category updated successfully'
                
            ]);
        }else{
            return response([
                'status'=>false,
                'errors'=> $validator->errors()
            ]);
        }
     

    }


    public function destroy ($id , Request $request){
        $subCategory = SubCategory::find($id);
        if(empty($subCategory)){
            $request->session()->flash('error','Sub Category Not fouund');
            return response()->json([
                'status'=> true,
                'message'=>'Sub CategoryNot found'
             ]);
        };       

  
     $subCategory->delete();
     $request->session()->flash('success','Sub Category deleted successfully');
     return response()->json([
        'status'=> true,
        'message'=>'Sub Category Deleted successfully'
     ]);
        
    }

}
