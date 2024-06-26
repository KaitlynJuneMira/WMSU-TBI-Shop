@extends('admin.layout.layout')
@section('content')
<div class="main-panel">
   <div class="content-wrapper">
      <div class="row">
         <div class="col-md-12 grid-margin">
            <div class="row">
               <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h3 class="font-weight-bold">Settings</h3>
               </div>
               <div class="col-12 col-xl-4">
                  <div class="justify-content-end d-flex">
                     <div class="dropdown flex-md-grow-1 flex-xl-grow-0">
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
               <div class="card-body">
                  <h4 class="card-title">{{ $title }}</h4>
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
                  <form class="forms-sample" @if(empty($currency['id'])) action="{{ url('admin/add-edit-currency') }}" @else action="{{ url('admin/add-edit-currency/'.$currency['id']) }}" @endif method="post" enctype="multipart/form-data">@csrf
                  <div class="form-group">
                     <label for="currency_code">Currency Code</label>
                     <input type="text" class="form-control" id="currency_code" placeholder="Enter Currency Code" name="currency_code" @if(!empty($currency['currency_code'])) value="{{ $currency['currency_code'] }}" @else value="{{ old('currency_code') }}" @endif>
                  </div>
                  <div class="form-group">
                     <label for="exchange_rate">Exchange Rate</label>
                     <input type="text" class="form-control" id="exchange_rate" placeholder="Enter Exchange Rate" name="exchange_rate" @if(!empty($currency['exchange_rate'])) value="{{ $currency['exchange_rate'] }}" @else value="{{ old('exchange_rate') }}" @endif>
                  </div>
                  <button type="submit" class="btn btn-primary mr-2">Submit</button>
                  <button type="reset" class="btn btn-light">Cancel</button>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection