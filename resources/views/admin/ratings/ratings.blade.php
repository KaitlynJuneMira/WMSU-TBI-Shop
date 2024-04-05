@extends('admin.layout.layout')
@section('content')
<!-- Display all the products rated -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Ratings</h4>
                        @if(Session::has('success_message'))
                          <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success: </strong> {{ Session::get('success_message')}}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                        @endif
                        <div class="table-responsive pt-3">
                            <table id="ratings" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>
                                            ID
                                        </th>
                                        <th>
                                            Product Name
                                        </th>
                                        <th>
                                            User Email
                                        </th>
                                        <th>
                                            Review
                                        </th>
                                        <th>
                                            Rating
                                        </th>
                                        <th>
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                  @foreach($ratings as $rating)
                                    <tr>
                                        <td>
                                            {{ $rating['id'] }}
                                        </td>
                                        <td>
                                            <a target="_blank" href="{{ url('product/'.$rating['product']['id']) }}">{{ $rating['product']['product_name'] }}</a>
                                        </td>
                                        <td>
                                            {{ $rating['user']['email'] }}
                                        </td>
                                        <td>
                                            {{ $rating['review'] }}
                                        </td>
                                        <td>
                                            {{ $rating['rating'] }}    
                                        </td>
                                        <!-- Display the rated or not -->
                                        <td>
                                            @if($rating['status']==1)
                                              <a class="updateRatingStatus" id="rating-{{ $rating['id'] }}" rating_id="{{ $rating['id'] }}" href="javascript:void(0)"><i style="font-size:25px;" class="mdi mdi-bookmark-check" status="Active"></i></a>
                                            @else
                                              <a class="updateRatingStatus" id="rating-{{ $rating['id'] }}" rating_id="{{ $rating['id'] }}" href="javascript:void(0)"><i style="font-size:25px;" class="mdi mdi-bookmark-outline" status="Inactive"></i></a>
                                            @endif
                                            <a href="javascript:void(0)" class="confirmDelete" module="rating" moduleid="{{ $rating['id'] }}"><i style="font-size:25px;" class="mdi mdi-file-excel-box"></i></a>
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