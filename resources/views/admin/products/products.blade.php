@extends('admin.layout.layout')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Products</h4>
                        <div class='row d-flex justify-content-between'>
                            <a href="javascript:history.back()" class="back-link mx-3 me-auto align-items-center">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <div class="col-4 d-flex justify-content-end">
                                <button class="btn btn-success mx-3" data-toggle="modal" data-target="#downloadProductModal">Download</button>
                                <a style="" href="{{ url('admin/add-edit-product') }}" class="btn btn-primary">Add Product</a>
                            </div>
                           
                            
                            
                            <div class="modal fade" id="downloadProductModal" tabindex="-1" role="dialog" aria-labelledby="downloadProductModalLabel" aria-hidden="true" wire:ignore.self>
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="downloadProductModalLabel">Download</h5>
                                        </div>
                                        <div class="modal-body">
                                            <h6 class=" mt-3" >
                                                EXPORT TYPE
                                            </h6>
                                            <select class="form-control" id="export-type" required aria-label="Default select example">
                                                <option selected value="EXCEL">EXCEL</option>
                                                <option value="CSV">CSV</option>
                                                <option value="PDF">PDF</option>
                                            </select>
                                            <?php $rows = [
                                                ['table_name'=>'ID','column_name'=>'p.id'],
                                                ['table_name'=>'Name','column_name'=>'product_name'],
                                                ['table_name'=>'Code','column_name'=>'product_code'],
                                                ['table_name'=>'Color','column_name'=>'product_color'],
                                                ['table_name'=>'Price','column_name'=>'product_price'],
                                                ['table_name'=>'Discount','column_name'=>'product_discount'],
                                                ['table_name'=>'Weight','column_name'=>'product_weight'],
                                                ['table_name'=>'Group-Code','column_name'=>'group_code'],
                                                // ['table_name'=>'is-Bestseller','column_name'=>'is_bestseller'],
                                                ['table_name'=>'Section-Name','column_name'=>'s.name'],
                                                ['table_name'=>'Category-Name','column_name'=>'category_name'],
                                                // ['table_name'=>'Category-Discount','column_name'=>'category_discount'],
                                                ['table_name'=>'Vendor-Name','column_name'=>'v.name'],
                                                ['table_name'=>'Vendor-Email','column_name'=>'v.email'],
                                                ['table_name'=>'Vendor-No','column_name'=>'v.mobile'],
                                                ['table_name'=>'Status','column_name'=>'p.status']
                                                ];
                                            ?>
                                            <h6 class=" mt-3" >
                                                Columns
                                            </h6>
                                            <fieldset id="checkArray" class="mt-2">
                                            @foreach($rows as $key => $value)
                                                <div class="form-group">
                                                    <div class="form-check">
                                                        <input class="form-check-input" checked type="checkbox" id="row-{{$value['table_name']}}" value="{{$value['table_name']}}">
                                                        <label class="form-check-label"  for="gridCheck">
                                                            {{$value['table_name']}}
                                                        </label>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </fieldset>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button"  class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-success " id="downloadSeller">Download</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
                            <script>
                                var rows = [
                                @foreach($rows as $key => $value)
                                    @if($loop->last)
                                        '{{$value['column_name']}}'
                                    @else
                                        '{{$value['column_name']}}',
                                    @endif
                                @endforeach
                                ];
                                var column_names = [
                                @foreach($rows as $key => $value)
                                    @if($loop->last)
                                        '{{$value['table_name']}}'
                                    @else
                                        '{{$value['table_name']}}',
                                    @endif
                                @endforeach
                                ];
                                var export_type;
                                var columns = [];
                                $('#downloadSeller').click(function(e){
                                    export_type = $('#export-type').val();
                                    columns = [];
                                    temp_column_names = [];
                                    for (let index = 0; index < column_names.length; index++) {
                                        const element = column_names[index];
                                        if($('#row-'+element).is(':checked')){
                                            columns.push(rows[index]);
                                            temp_column_names.push(column_names[index]);
                                        }
                                    }
                                    var encoded_columns = encodeURIComponent(JSON.stringify(columns));
                                    var encoded_column_names = encodeURIComponent(JSON.stringify(temp_column_names));
                                    e.preventDefault(); 
                                    window.location.href = '/admin/ExportProducts/'+export_type+'/'+encoded_columns+'/'+encoded_column_names;
                                 });
                            </script>
                        </div>

                        @if(Session::has('success_message'))
                          <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success: </strong> {{ Session::get('success_message')}}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                        @endif
                        <div class="table-responsive pt-3">
                            <table id="products" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Product Name</th>
                                        <th>Product Code</th>
                                        <th>Product Color</th>
                                        <th>Product Image</th>
                                        <th>Category</th>
                                        <th>Section</th>
                                        <th>Added by</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($products as $product)
                                        <tr>
                                            <td>{{ $product['id'] ?? 'N/A' }}</td>
                                            <td>{{ $product['product_name'] ?? 'N/A' }}</td>
                                            <td>{{ $product['product_code'] ?? 'N/A' }}</td>
                                            <td>{{ $product['product_color'] ?? 'N/A' }}</td>
                                            <td>
                                                @if(!empty($product['product_image']))
                                                    <img style="width: 120px; height: 120px;" src="{{ asset('front/images/product_images/small/'.$product['product_image']) }}">
                                                @else
                                                    <img style="width: 120px; height: 120px;" src="{{ asset('front/images/product_images/small/no-image.png') }}">
                                                @endif
                                            </td>
                                            <td>{{ $product['category']['category_name'] ?? 'N/A' }}</td> 
                                            <td>{{ $product['section']['name'] ?? 'N/A' }}</td>  
                                            <td>
                                                @if($product['admin_type']=="vendor")
                                                    <a target="_blank" href="{{ url('admin/view-vendor-details/'.$product['admin_id']) }}">{{ ucfirst($product['admin_type']) }}</a>
                                                @else
                                                    {{ ucfirst($product['admin_type']) }}    
                                                @endif
                                            </td>  
                                            <td>
                                                @if($product['status']==1)
                                                  <a class="updateProductStatus" id="product-{{ $product['id'] }}" product_id="{{ $product['id'] }}" href="javascript:void(0)"><i style="font-size:25px;" class="mdi mdi-bookmark-check" status="Active"></i></a>
                                                @else
                                                  <a class="updateProductStatus" id="product-{{ $product['id'] }}" product_id="{{ $product['id'] }}" href="javascript:void(0)"><i style="font-size:25px;" class="mdi mdi-bookmark-outline" status="Inactive"></i></a>
                                                @endif
                                            </td>
                                            <td>
                                                <a title="Edit Product" href="{{ url('admin/add-edit-product/'.$product['id']) }}"><i style="font-size:25px;" class="mdi mdi-pencil-box"></i></a>
                                                <a title="Add Attributes" href="{{ url('admin/add-edit-attributes/'.$product['id']) }}"><i style="font-size:25px;" class="mdi mdi-plus-box"></i></a>
                                                <a title="Add Images" href="{{ url('admin/add-images/'.$product['id']) }}"><i style="font-size:25px;" class="mdi mdi-library-plus"></i></a>
                                                <a href="javascript:void(0)" class="confirmDelete" module="product" moduleid="{{ $product['id'] }}"><i style="font-size:25px;" class="mdi mdi-file-excel-box"></i></a>
                                            </td>
                                        </tr>
                                        @endforeach 
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- content-wrapper ends -->
    <!-- partial:../../partials/_footer.html -->
    <!-- <footer class="footer">
        <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2021.  Premium <a href="https://www.bootstrapdash.com/" target="_blank">Bootstrap admin template</a> from BootstrapDash. All rights reserved.</span>
            <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted & made with <i class="ti-heart text-danger ml-1"></i></span>
        </div>
    </footer> -->
    <!-- partial -->
</div>
@endsection
