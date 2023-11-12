<?php
use App\Models\Category;
function getCategories(){
   return  Category::orderBy('id','DESC')
   ->with('sub_category')
   ->where('status',1)
   ->where('showNav','Yes')
   ->get();
}
?>