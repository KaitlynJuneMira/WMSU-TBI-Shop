@extends('admin.layout.layout')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">Shipping Charges</h3>
                        <!-- <h6 class="font-weight-normal mb-0">Update Admin Password</h6> -->
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="justify-content-end d-flex">
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
                  
                  <form class="forms-sample" action="{{ url('admin/edit-shipping-charges/'.$shippingDetails['id']) }}" method="post">@csrf
                    <div class="form-group">
                      <label for="country">Barangay</label>
                      <input type="text" class="form-control" value="{{ $shippingDetails['barangay'] }}" readonly="">
                    </div>
                    <div class="form-group">
                      <label for="0_500g">Rate (0-500g)</label>
                      <input type="text" class="form-control" id="0_500g" placeholder="Enter Shipping Rate" name="0_500g" value="{{ $shippingDetails['0_500g'] }}">
                    </div>
                    <div class="form-group">
                      <label for="501_1000g">Rate (501-1000g)</label>
                      <input type="text" class="form-control" id="501_1000g" placeholder="Enter Shipping Rate" name="501_1000g" value="{{ $shippingDetails['501_1000g'] }}">
                    </div>
                    <div class="form-group">
                      <label for="1001_2000g">Rate (1001-2000g)</label>
                      <input type="text" class="form-control" id="1001_2000g" placeholder="Enter Shipping Rate" name="1001_2000g" value="{{ $shippingDetails['1001_2000g'] }}">
                    </div>
                    <div class="form-group">
                      <label for="2001_5000g">Rate (2001-5000g)</label>
                      <input type="text" class="form-control" id="2001_5000g" placeholder="Enter Shipping Rate" name="2001_5000g" value="{{ $shippingDetails['2001_5000g'] }}">
                    </div>
                    <div class="form-group">
                      <label for="above_5000g">Rate (Above 5000g)</label>
                      <input type="text" class="form-control" id="above_5000g" placeholder="Enter Shipping Rate" name="above_5000g" value="{{ $shippingDetails['above_5000g'] }}">
                    </div>
                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
                    <button type="reset" class="btn btn-light">Cancel</button>
                  </form>
                </div>
              </div>
            </div>
            
          </div>
    </div>
    <!-- content-wrapper ends -->
    <!-- partial:partials/_footer.html -->
    <!-- partial -->
</div>
@endsection