<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Image;

class TempImag extends Controller
{
    public function create (Request $request){
        if(!empty($request->image)){
            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $newName = time().'.'.$ext;
            
            $tempImage = new TempImage();
            $tempImage->name = $newName;
            $tempImage->save();

            $image->move(public_path().'/temp',$newName);

            // genrate thumbnail
            $spath =public_path().'/temp/'.$newName ;
            $dpath =public_path().'/temp/thumb/'.$newName ;
            $image = Image::make($spath);
            $image->fit(300,275);
            $image->save($dpath);
            return response()->json([
                'status'=> true,
                'image_id' => $tempImage->id,
                'image_path' => asset('/temp/thumb/'.$newName),
                'message' => 'Image uploaded successfully'
            ]);
        }

    }
}
