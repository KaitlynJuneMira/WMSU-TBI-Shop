<?php use App\Models\Category; ?>
@extends('admin.layout.layout')
@section('content')
<!-- Display filters -->
<div class="main-panel">
   <div class="content-wrapper">
      <div class="row">
         <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
               <div class="card-body">
                  <h4 class="card-title">Filters</h4>
                  <a style="max-width: 163px; float: right; display: inline-block;" href="{{ url('admin/filters-values') }}" class="btn btn-block btn-primary">View Filter Values</a>
                  <a style="max-width: 169px; float: left; display: inline-block;" href="{{ url('admin/add-edit-filter') }}" class="btn btn-block btn-primary">Add Filter Columns</a>
                  @if(Session::has('success_message'))
                  <div class="alert alert-success alert-dismissible fade show" role="alert">
                     <strong>Success: </strong> {{ Session::get('success_message')}}
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                     </button>
                  </div>
                  @endif
                  <div class="table-responsive pt-3">
                     <table id="filters" class="table table-bordered">
                        <thead>
                           <tr>
                              <th>
                                 ID
                              </th>
                              <th>
                                 Filter Name
                              </th>
                              <th>
                                 Filter Column
                              </th>
                              <th>
                                 Categories
                              </th>
                              <th>
                                 Status
                              </th>
                              <th>
                                 Actions
                              </th>
                           </tr>
                        </thead>
                        <tbody>
                           @foreach($filters as $filter)
                           <tr>
                              <td>
                                 {{ $filter['id'] }}
                              </td>
                              <td>
                                 {{ $filter['filter_name'] }}
                              </td>
                              <td>
                                 {{ $filter['filter_column'] }}
                              </td>
                              <td>
                                 <?php
                                    $catIds = explode(",",$filter['cat_ids']);
                                    foreach ($catIds as $key => $catId) {
                                        $category_name = Category::getCategoryName($catId);
                                        echo $category_name. " ";
                                    }
                                    ?>
                              </td>
                              <td>
                                 @if($filter['status']==1)
                                 <a class="updateFilterStatus" id="filter-{{ $filter['id'] }}" filter_id="{{ $filter['id'] }}" href="javascript:void(0)"><i style="font-size:25px;" class="mdi mdi-bookmark-check" status="Active"></i></a>
                                 @else
                                 <a class="updateFilterStatus" id="filter-{{ $filter['id'] }}" filter_id="{{ $filter['id'] }}" href="javascript:void(0)"><i style="font-size:25px;" class="mdi mdi-bookmark-outline" status="Inactive"></i></a>
                                 @endif
                              </td>
                              <td>
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