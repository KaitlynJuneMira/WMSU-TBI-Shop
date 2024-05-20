@extends('admin.layout.layout')
@section('content')
<!-- Display the seller's business information -->
<div class="main-panel">
   <div class="content-wrapper">
      <div class="row">
         <div class="col-md-12 grid-margin">
            <div class="row">
               <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h3 class="font-weight-bold">Seller Details</h3>
                  <h6 class="font-weight-normal mb-0"><a href="{{ url('admin/admins/vendor') }}">Back to Sellers</a></h6>
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
                  <h4 class="card-title">Personal Information</h4>
                  <div class="form-group">
                     <label>Email</label>
                     <input class="form-control" value="{{ $vendorDetails['email'] }}" readonly="">
                  </div>
                  <div class="form-group">
                     <label for="vendor_name">Name</label>
                     <input type="text" class="form-control" value="{{ $vendorDetails['name'] }}" readonly="">
                  </div>
                  <div class="form-group">
                     <label for="vendor_address">Address Details</label>
                     <input type="text" class="form-control" value="{{ $vendor ? $vendor['address'] : ''}}" readonly="">
                  </div>
                  <div class="form-group">
                     <label for="vendor_city">City</label>
                     <input type="text" class="form-control" value="{{ $vendor ? $vendor['city'] : ''}}" readonly="">
                  </div>
                  <div class="form-group">
                     <label for="vendor_barangay">Barangay</label>
                     <input type="text" class="form-control" value="{{ $vendor ? $vendor['barangay'] : ''}}" readonly="">
                  </div>
                  <div class="form-group">
                     <label for="vendor_country">Shop Province</label>
                     <input type="text" class="form-control" value="{{ $vendor ? $vendor['country'] : ''}}" readonly="">
                  </div>
                  <div class="form-group">
                     <label for="vendor_pincode">Pincode</label>
                     <input type="text" class="form-control" value="{{ $vendor ? $vendor['pincode'] : ''}}" readonly="">
                  </div>
                  <div class="form-group">
                     <label for="vendor_mobile">Mobile</label>
                     <input type="text" class="form-control" value="{{  $vendorDetails['mobile'] }}" readonly="">
                  </div>
                  @if(!empty($vendorDetails['image']))
                  <div class="form-group">
                     <label for="vendor_image">Photo</label>
                     <br><img style="width: 200px;" src="{{ url('admin/images/photos/'.$vendorDetails['image']) }}">
                  </div>
                  @endif
               </div>
            </div>
         </div>
         <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
               <div class="card-body">
                  <h4 class="card-title">Business Information</h4>
                  <div class="form-group">
                     <label for="vendor_name">Shop Name</label>
                     <input type="text" class="form-control" value="{{ $vendorBusiness ? $vendorBusiness['shop_name'] : ''}}" readonly="">
                  </div>
                  <div class="form-group">
                     <label for="vendor_address">Shop Address</label>
                     <input type="text" class="form-control" value="{{ $vendorBusiness ? $vendorBusiness['shop_address'] : ''}}" readonly="">
                  </div>
                  <div class="form-group">
                     <label for="vendor_city">Shop City</label>
                     <input type="text" class="form-control" value="{{ $vendorBusiness ? $vendorBusiness['shop_city'] : ''}}" readonly="">
                  </div>
                  <div class="form-group">
                     <label for="vendor_state">Shop Barangay</label>
                     <input type="text" class="form-control" value="{{ $vendorBusiness ? $vendorBusiness['shop_barangay'] : ''}}" readonly="">
                  </div>
                  <div class="form-group">
                     <label for="vendor_country">Shop Shop Province</label>
                     <input type="text" class="form-control" value="{{ $vendorBusiness ? $vendorBusiness['shop_country'] : ''}}" readonly="">
                  </div>
                  <div class="form-group">
                     <label for="vendor_pincode">Shop Zipcode</label>
                     <input type="text" class="form-control"  value="{{ $vendorBusiness ? $vendorBusiness['shop_pincode'] : ''}}" readonly="">
                  </div>
                  <div class="form-group">
                     <label for="vendor_mobile">Shop Contact No.</label>
                     <input type="text" class="form-control" value="{{ $vendorBusiness ? $vendorBusiness['shop_mobile'] : ''}}" readonly="">
                  </div>
                  @if(!empty($vendorBusiness['address_proof_image']))
                  <div class="form-group">
                     <label for="business_image">Business Permit</label>
                     <br><img style="width: 200px;" src="{{ url('admin/images/proofs/'.$vendorBusiness['address_proof_image']) }}">
                  </div>
                  @endif

                  @if(!empty($vendorBusiness['bir_image']))
                  <div class="form-group">
                     <label for="BIR_image">BIR Permit</label>
                     <br><img style="width: 200px;" src="{{ url('admin/images/proofs/'.$vendorBusiness['bir_image']) }}">
                  </div>
                  @endif

                  @if(!empty($vendorBusiness['dti_image']))
                  <div class="form-group">
                     <label for="DTI_image">DTI Permit</label>
                     <br><img style="width: 200px;" src="{{ url('admin/images/proofs/'.$vendorBusiness['dti_image']) }}">
                  </div>
                  @endif

                  @if(!empty($vendorBusiness['permit_proof_image']))
                  <div class="form-group">
                     <label for="premit_image">DTI Permit</label>
                     <br><img style="width: 200px;" src="{{ url('admin/images/proofs/'.$vendorBusiness['permit_proof_image']) }}">
                  </div>
                  @endif
               </div>
            </div>
         </div>
        
      </div>
   </div>
</div>
@endsection