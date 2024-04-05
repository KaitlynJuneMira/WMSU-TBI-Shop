@extends('admin.layout.layout')
@section('content')
<!-- Add and adit product attributes -->
<div class="main-panel">
   <div class="content-wrapper">
      <div class="row">
         <div class="col-md-12 grid-margin">
            <div class="row">
               <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h4 class="card-title">Attributes</h4>
                  <p id="stockWarning" style="color: red;"></p>
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
                <!-- Attributes profiling -->
               <div class="card-body">
                  <h4 class="card-title">Add Attributes</h4>
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
                  <form class="forms-sample" action="{{ url('admin/add-edit-attributes/'.$product['id']) }}" method="post">
                     @csrf
                     <div class="form-group" style="text-align: center;">
                        @if(!empty($product['product_image']))
                        <img style="width: 150px;" src="{{ url('front/images/product_images/small/'.$product['product_image']) }}">
                        @else
                        <img style="width: 150px;" src="{{ url('front/images/product_images/small/no-image.png') }}">
                        @endif
                     </div>
                     <div class="form-group">
                        <label for="product_name" style="width: 100px;">Product Name</label>
                        {{ $product['product_name'] }}
                     </div>
                     <div class="form-group">
                        <label for="product_color" style="width: 100px;">Product Color</label>
                        {{ $product['product_color'] }}
                     </div>
                     <div class="form-group">
                        <label for="product_code" style="width: 100px;">Product Code: </label>
                        <input type="text" name="sku[]" value="{{ $product['product_code'] }}" style="width: 120px;" placeholder="Code" required="" />
                     </div>
                     <div class="form-group">
                        <label for="product_price" style="width: 100px;">Product Price: </label>
                        <input type="text" name="price[]" placeholder="Price in Pesos" style="width: 120px;" required="" />
                     </div>
                     <div class="form-group">
                        <label for="product_size" style="width: 100px;">Product Size: </label>
                        <input type="number" id="size_number" name="size_number[]" placeholder="Size" style="width: 80px;" onchange="combineSize()" required="" />
                        <select name="size_measurement[]" id="size_measurement" style="width: 120px;" onchange="combineSize()" required="">
                           <option value="kg">kg</option>
                           <option value="g">g</option>
                           <option value="lbs">lbs</option>
                           <option value="sack">sack</option>
                        </select>
                        <input type="hidden" name="size[]" id="combined_size" />
                     </div>
                     <div class="form-group">
                        <label for="product_size" style="width: 100px;">Available Stock: </label>
                        <input type="text" name="stock[]" placeholder="AvailStock" style="width: 120px;" required="" />
                     </div>
                     <button type="submit" class="btn btn-primary mr-2">Submit</button>
                     <button type="reset" class="btn btn-light">Delete Edit</button>
                  </form>
                  <br><br>
                  <h4 class="card-title">Product Attributes</h4>
                  <form method="post" action="{{ url('admin/edit-attributes/'.$product['id']) }}">
                     @csrf
                     <table id="products" class="table table-bordered">
                        <thead>
                           <tr>
                              <th>
                                 ID
                              </th>
                              <th>
                                 Size
                              </th>
                              <th>
                                 Code
                              </th>
                              <th>
                                 Price
                              </th>
                              <th>
                                 Stock
                              </th>
                              <th>
                                 Actions
                              </th>
                           </tr>
                        </thead>
                        <tbody>
                           @foreach($product['attributes'] as $attribute)
                           <input style="display:none;" type="text" name="attributeId[]" value="{{ $attribute['id'] }}">
                           <tr>
                              <td>
                                 {{ $attribute['id'] }}
                              </td>
                              <td>
                                 {{ $attribute['size'] }}
                              </td>
                              <td>
                                 {{ $attribute['sku'] }}
                              </td>
                              <td>
                                 <input type="text" name="price[]" value="Php {{ number_format($attribute['price'], 2) }}" required="" style="width: 120px;">
                              </td>
                              <td>
                                 <input type="number" name="stock[]" value="{{ $attribute['stock'] }}" required="" style="width: 70px;">
                              </td>
                              <!-- Set if it is active or not -->
                              <td>
                                 @if($attribute['status']==1)
                                 <a class="updateAttributeStatus" id="attribute-{{ $attribute['id'] }}" attribute_id="{{ $attribute['id'] }}" href="javascript:void(0)"><i style="font-size:25px;" class="mdi mdi-bookmark-check" status="Active"></i></a>
                                 @else
                                 <a class="updateAttributeStatus" id="attribute-{{ $attribute['id'] }}" attribute_id="{{ $attribute['id'] }}" href="javascript:void(0)"><i style="font-size:25px;" class="mdi mdi-bookmark-outline" status="Inactive"></i></a>
                                 @endif
                              </td>
                           </tr>
                           @endforeach 
                        </tbody>
                     </table>
                     <button type="submit" class="btn btn-primary">Update Attributes</button>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
<script>
   function combineSize() {
       var numberInput = document.getElementById("size_number").value;
       var measurementSelect = document.getElementById("size_measurement");
       var measurement = measurementSelect.options[measurementSelect.selectedIndex].value;
       // Adjust the measurement value for "sack"
       if (measurement === "sack") {
           measurement = measurement;
       }
       var combinedSize = numberInput + measurement;
       document.getElementById("combined_size").value = combinedSize;
   }
   function formatCurrency(input) {
   // Remove non-numeric characters and convert to float
   let value = parseFloat(input.value.replace(/[^\d.-]/g, ''));
   
   // Format with comma as thousand separator and 2 decimal places
   input.value = 'Php ' + value.toLocaleString('en-US');
   }
   document.addEventListener('DOMContentLoaded', function() {
       const stockInputs = document.querySelectorAll('input[name="stock[]"]');
       const stockWarning = document.getElementById('stockWarning');
   
       if (stockInputs && stockWarning) {
           stockInputs.forEach(function(stockInput) {
               stockInput.addEventListener('input', function() {
                   let showWarning = false;
                   stockInputs.forEach(function(input) {
                       const stockValue = parseFloat(input.value);
                       if (stockValue < 5) {
                           showWarning = true;
                       }
                   });
   
                   if (showWarning) {
                       stockWarning.textContent = 'Stock is low!';
                   } else {
                       stockWarning.textContent = '';
                   }
               });
           });
   
           // Check on page load
           let showWarning = false;
           stockInputs.forEach(function(input) {
               const stockValue = parseFloat(input.value);
               if (stockValue < 5) {
                   showWarning = true;
               }
           });
   
           if (showWarning) {
               stockWarning.textContent = 'Stock is low!';
           }
       } else {
           console.error('Stock inputs or warning element not found.');
       }
   });
</script>