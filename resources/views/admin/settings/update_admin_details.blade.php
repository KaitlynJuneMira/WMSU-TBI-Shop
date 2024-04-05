@extends('admin.layout.layout')
@section('content')
<!-- Update the admin details -->
<div class="main-panel">
<div class="content-wrapper">
   <div class="row">
      <div class="col-md-12 grid-margin">
         <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
               <h3 class="font-weight-bold">Settings</h3>
               <a href="javascript:history.back()" class="back-link">
               <i class="fas fa-arrow-left"></i> Back
               </a>
            </div>
         </div>
      </div>
   </div>
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-body">
               <h4 class="card-title">Update Admin Details</h4>
               @if(Session::has('error_message'))
               <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <strong>Error: </strong> {{ Session::get('error_message')}}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               @endif
               @if(Session::has('success_message'))
               <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <strong>Success: </strong> {{ Session::get('success_message')}}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               @endif
               @if($errors->any())
               <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                  @endforeach
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               @endif
               <form class="forms-sample" action="{{ url('admin/update-admin-details') }}" method="post" enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label>Admin Username/Email</label>
                           <input class="form-control" value="{{ Auth::guard('admin')->user()->email }}" readonly="">
                        </div>
                        <div class="form-group">
                           <label>Admin Type</label>
                           <input class="form-control" value="{{ Auth::guard('admin')->user()->type }}" readonly="">
                        </div>
                        <div class="form-group">
                           <label for="admin_name">Name</label>
                           <input type="text" class="form-control" id="admin_name" placeholder="Enter Name" name="admin_name" value="{{ Auth::guard('admin')->user()->name }}">
                        </div>
                        <div class="form-group">
                           <label for="admin_mobile">Mobile</label>
                           <input type="text" class="form-control" id="admin_mobile" placeholder="Enter 10 Digit Mobile Number" name="admin_mobile" value="{{ Auth::guard('admin')->user()->mobile }}" required="" maxlength="11" minlength="11">
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="admin_image">Admin Photo</label>
                           <div class="custom-file">
                              <input type="file" class="custom-file-input" id="admin_image" name="admin_image" onchange="previewImage(event)">
                              <label class="custom-file-label" for="admin_image">Choose file</label>
                           </div>
                           <div class="mt-2 row">
                              @if(!empty(Auth::guard('admin')->user()->image))
                              <input type="hidden" name="current_admin_image" value="{{ Auth::guard('admin')->user()->image }}">
                              <div class="col-md-6">
                                 <label>Current Image:</label>
                                 <div>
                                    <img src="{{ url('admin/images/photos/'.Auth::guard('admin')->user()->image) }}" alt="current_profile" class="img-thumbnail" style="max-width: 100%;">
                                 </div>
                              </div>
                              @endif
                              <div class="col-md-6" id="imagePreview" style="display: none;">
                                 <label>New Image Preview:</label>
                                 <div>
                                    <img id="newImagePreview" src="#" alt="new_profile" class="img-thumbnail" style="max-width: 100%;">
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <button type="submit" class="btn btn-primary mr-2">Save</button>
                     <button type="reset" class="btn btn-light">Cancel</button>
               </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection