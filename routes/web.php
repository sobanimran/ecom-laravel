<?php

use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\HomeController;

use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ProductImageController;
use App\Http\Controllers\admin\ProductSubCategoryController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\TempImag;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ShopController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/',[FrontController::class,'index'])->name('front.home');
Route::get('/shop/{categorySlug?}/{subCategorySlug?}',[ShopController::class,'index'])->name('front.shop');
//Route::get('/shop',[ShopController::class,'index'])->name('front.shop');


//Route::group(['prefix'=>'admin'],function(){});

   // routes for Admin 
Route::group(['prefix'=>'admin'],function(){
    Route::group(['middleware' => 'admin.guest'],function(){
    
        Route::get('/login',[AdminLoginController::class,'index'])->name('admin.login');
        Route::post('/authenticate',[AdminLoginController::class,'authenticate'])->name('admin.authenticate');
        
    });
 
    Route::group(['middleware' => 'admin.auth'],function(){
        Route::get('/dashboard',[HomeController::class,'index'])->name('admin.dashboard');
        Route::get('/logout',[HomeController::class,'logout'])->name('admin.logout');
        
        // categories route
        Route::get('/categories/create',[CategoryController::class,'create'])->name('categories.create');
        Route::get('/categories/{category}/edit',[CategoryController::class,'edit'])->name('categories.edit');
        Route::put('/categories/{category}',[CategoryController::class,'update'])->name('categories.update');
        Route::delete('/categories/{category}',[CategoryController::class,'destroy'])->name('categories.delete');
        Route::get('/categories',[CategoryController::class,'index'])->name('categories.index');
        Route::get('/categories/srore',[CategoryController::class,'store'])->name('categories.store');
        
        Route::post('/upload-Temp-Image',[TempImag::class,'create'])->name('temp-images.create');
        
        // sub_category routes
        Route::get('/sub-categories/create',[SubCategoryController::class,'create'])->name('sub-Categories.create');
        Route::post('/sub-categories/srore',[SubCategoryController::class,'store'])->name('sub-Categories.store');
        Route::get('/sub-categories',[SubCategoryController::class,'index'])->name('sub-categories.index');
        Route::get('/sub-categories/{subCategory}/edit',[SubCategoryController::class,'edit'])->name('sub-categories.edit');
        Route::put('/sub-categories/{subCategory}',[SubCategoryController::class,'update'])->name('sub-categories.update');
        Route::delete('/sub-categories/{subCategory}',[CategoryController::class,'destroy'])->name('sub-categories.delete');
    
        // brands route
        Route::get('/brands/create',[BrandController::class,'create'])->name('brands.create');
        Route::post('/brands',[BrandController::class,'store'])->name('brands.store');
        Route::get('/brands',[BrandController::class,'index'])->name('brands.index');
        Route::get('/brands/{brand}/edit',[BrandController::class,'edit'])->name('brand.edit');
        Route::put('/brands/{brand}',[BrandController::class,'update'])->name('brand.update');
   
   
        // products route
        Route::get('/product/create',[ProductController::class,'create'])->name('product.create');
        Route::get('/product-subcategories',[ProductSubCategoryController::class,'index'])->name('product-subcategories.index');
        Route::post('/products',[ProductController::class,'store'])->name('product.store');
        Route::get('/products',[ProductController::class,'index'])->name('products.index');
        Route::get('/product/{product}/edit',[ProductController::class,'edit'])->name('product.edit');
        Route::put('/product/{product}',[ProductController::class,'update'])->name('product.update');
        Route::delete('/product/{product}',[ProductController::class,'destroy'])->name('product.delete');

        Route::post('/products-image/update',[ProductImageController::class,'update'])->name('product-image.update');
        Route::delete('/products-image/delete',[ProductImageController::class,'destroy'])->name('product-image.delete');
   
    });
    Route::get('/getSlug',function(Request $request){
        $slug ='';
        if(!empty($request->title)){
           $slug =  Str::slug($request->title);
        }
        return response()->json([
            'status' => true,
            'slug' =>$slug,
        ]);
    })->name('getslug');

});
