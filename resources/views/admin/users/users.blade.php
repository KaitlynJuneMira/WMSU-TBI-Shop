@extends('admin.layout.layout')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Users</h4>
                        <div class='row d-flex justify-content-between'>
                            <a href="javascript:history.back()" class="back-link mx-3 me-auto align-items-center">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <div class="col-4 d-flex justify-content-end">
                                <button class="btn btn-success mx-3" data-toggle="modal" data-target="#downloadProductModal">Download</button>
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
                                                ['table_name'=>'ID','column_name'=>'id'],
                                                ['table_name'=>'Name','column_name'=>'name'],
                                                // ['table_name'=>'Address','column_name'=>'address'],
                                                ['table_name'=>'City','column_name'=>'city'],
                                                ['table_name'=>'Barangay','column_name'=>'barangay'],
                                                ['table_name'=>'Country','column_name'=>'country'],
                                                ['table_name'=>'Pincode','column_name'=>'pincode'],
                                                ['table_name'=>'Mobile','column_name'=>'mobile'],
                                                ['table_name'=>'Email','column_name'=>'email'],
                                                ['table_name'=>'Verified-At','column_name'=>'email_verified_at'],
                                                ['table_name'=>'Status','column_name'=>'status']
                                           
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
                                    window.location.href = '/admin/ExportCustomers/'+export_type+'/'+encoded_columns+'/'+encoded_column_names;
                                 });
                            </script>
                        </div>
                        <!-- <p class="card-description">
                            Add class <code>.table-bordered</code>
                        </p> -->
                        @if(Session::has('success_message'))
                          <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success: </strong> {{ Session::get('success_message')}}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                        @endif
                        <div class="table-responsive pt-3">
                            <table id="users" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>
                                            ID
                                        </th>
                                        <th>
                                            Name
                                        </th>
                                        <th>
                                            Address
                                        </th>
                                        <th>
                                            Barangay
                                        </th>
                                        <th>
                                            City
                                        </th>
                                        <th>
                                            Province
                                        </th>
                                        <th>
                                            Zipcode
                                        </th>
                                        <th>
                                            Mobile
                                        </th>
                                        <th>
                                            Email
                                        </th>
                                        <th>
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                  @foreach($users as $user)
                                    <tr>
                                        <td>
                                            {{ $user['id'] }}
                                        </td>
                                        <td>
                                            {{ $user['name'] }}
                                        </td>
                                        <td>
                                            {{ $user['address'] }}
                                        </td>
                                        <td>
                                            {{ $user['city'] }}
                                        </td>
                                        <td>
                                            {{ $user['barangay'] }}
                                        </td>
                                        <td>
                                            {{ $user['country'] }}
                                        </td>
                                        <td>
                                            {{ $user['pincode'] }}
                                        </td>
                                        <td>
                                            {{ $user['mobile'] }}
                                        </td>
                                        <td>
                                            {{ $user['email'] }}
                                        </td>
                                        <td>
                                            @if($user['status']==1)
                                              <a class="updateUserStatus" id="user-{{ $user['id'] }}" user_id="{{ $user['id'] }}" href="javascript:void(0)"><i style="font-size:25px;" class="mdi mdi-bookmark-check" status="Active"></i></a>
                                            @else
                                              <a class="updateUserStatus" id="user-{{ $user['id'] }}" user_id="{{ $user['id'] }}" href="javascript:void(0)"><i style="font-size:25px;" class="mdi mdi-bookmark-outline" status="Inactive"></i></a>
                                            @endif
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
</div>
@endsection