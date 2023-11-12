@extends('admin.layouts.adminlayout')
@section('content')
	<!-- Content Header (Page header) -->
    <section class="content-header">					
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create Sub Category</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{route('sub-categories.index')}}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <form action="" name="subCategoryForm" id="subCategoryForm" method="">
            <div class="card">
                <div class="card-body">								
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="name">Category</label>
                                <select name="category" id="category" class="form-control">
                                    <option value="">Select A Category</option>
                                   @if (!empty($categories))
                                   @foreach ($categories as $category )
                                   <option {{($category->id==$subCategory->category_id)?'selected':''}} value="{{$category->id}}">{{$category->name}}</option>
                                       
                                   @endforeach
                                       
                                   @endif
                                </select>
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name">Name</label>
                                <input type="text" value="{{$subCategory->name}}" name="name" id="name" class="form-control" placeholder="Name">
                                <p></p>	
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="slug">Slug</label>
                                <input type="text" name="slug" value="{{$subCategory->slug}}"  id="slug" class="form-control" placeholder="Slug">	
                                <p></p>
                            </div>
                        </div>									
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status">status</label>
                                <select name="status" id="status" class="form-control">
                                    <option {{($subCategory->status==1)?'selected':''}}  value="1">Active</option>
                                    <option {{($subCategory->status==0)?'selected':''}}  value="0">Block</option>
                                </select>
                               
                            </div>
                            <div class="mb-3">
                                <label for="showNav">show Nav</label>
                                <select name="showNav" id="showNav" class="form-control">
                                    <option {{($subCategory->showNav=='Yes')?'selected':''}}  value="Yes">Yes</option>
                                    <option {{($subCategory->showNav=='No')?'selected':''}}  value="No">No</option>
                                </select>
                               
                            </div>
                        </div>									
                    </div>
                </div>							
            </div>
       
            <div class="pb-5 pt-3">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{route('sub-categories.index')}}" class="btn btn-outline-dark ml-3">Cancel</a>
            </div>
        </form>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
    
@endsection
@section('customjs')
    <script>
 $('#subCategoryForm').submit(function(e) {
            e.preventDefault();
            var element = $(this); 
            $("button[type=submit]").prop('disabled',true);
            $.ajax({
                url:' {{ route("sub-categories.update",$subCategory->id) }}',
                type: 'put',
                data: element.serializeArray(),
                dataType: 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled',false);
                    if(response["status"]==true){
                        window.location.href="{{ route('sub-categories.index') }}"
                        $("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html(null)
                        $("#slug").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html(null)
                        $("#category").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html(null)
                      
                    }else{
                        if(response['notfound']==true){
                            window.location.href="{{ route('sub-categories.index') }}" 
                        }

                        var errors = response['errors'];
                     //  alert(response['errors']['name'])
                     if(errors['name']){
                        $("#name").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['name'])
                    }else{
                         $("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html(null)
    
                     }
                     if(errors['slug']){
                        $("#slug").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['slug'])
                    }else{
                        $("#slug").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html(null)
                        
                     }
                     if(errors['category']){
                        $("#category").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['category'])
                    }else{
                        $("#category").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html(null)
                        
                     }
                    }

                },
                error: function(jqXHR, exception) {
                    console.log("something Went wrong");

                }

            })
        })




        $("#name").change(function(){
var element = $(this);
$("button[type=submit]").prop('disabled',true);
    $.ajax({
            url:' {{ route("getslug") }}',
            type: 'get',
            data: {title :element.val()},
            dataType: 'json',
            success: function(response) {
                $("button[type=submit]").prop('disabled',false)   
                if(response["status"]==true){
                    $("#slug").val(response["slug"]);
                }
            } })
})
    </script>
@endsection