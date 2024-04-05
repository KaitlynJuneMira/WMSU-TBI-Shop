@extends('admin.layout.layout')
@section('content')
<!-- Display all the sections profiled -->
<div class="main-panel">
   <div class="content-wrapper">
      <div class="row">
         <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
               <div class="card-body">
                  <h4 class="card-title">Sections</h4>
                  <a href="javascript:history.back()" class="back-link">
                  <i class="fas fa-arrow-left"></i> Back
                  </a>
                  <a style="max-width: 150px; float: right; display: inline-block;" href="{{ url('admin/add-edit-section') }}" class="btn btn-block btn-primary">Add Section</a>
                  @if(Session::has('success_message'))
                  <div class="alert alert-success alert-dismissible fade show" role="alert">
                     <strong>Success: </strong> {{ Session::get('success_message')}}
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                     </button>
                  </div>
                  @endif
                  <div class="table-responsive pt-3">
                     <table id="sections" class="table table-bordered">
                        <thead>
                           <tr>
                              <th>
                                 ID
                              </th>
                              <th>
                                 Name
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
                           @foreach($sections as $section)
                           <tr>
                              <td>
                                 {{ $section['id'] }}
                              </td>
                              <td>
                                 {{ $section['name'] }}
                              </td>
                              <!-- Mark the section as active or not -->
                              <td>
                                 @if($section['status']==1)
                                 <a class="updateSectionStatus" id="section-{{ $section['id'] }}" section_id="{{ $section['id'] }}" href="javascript:void(0)"><i style="font-size:25px;" class="mdi mdi-bookmark-check" status="Active"></i></a>
                                 @else
                                 <a class="updateSectionStatus" id="section-{{ $section['id'] }}" section_id="{{ $section['id'] }}" href="javascript:void(0)"><i style="font-size:25px;" class="mdi mdi-bookmark-outline" status="Inactive"></i></a>
                                 @endif
                              </td>
                              <td>
                                <!-- Edit the profiled section -->
                                 <a href="{{ url('admin/add-edit-section/'.$section['id']) }}"><i style="font-size:25px;" class="mdi mdi-pencil-box"></i></a>
                                <!--  Delete the profiled section -->
                                 <a href="javascript:void(0)" class="confirmDelete" module="section" moduleid="{{ $section['id'] }}"><i style="font-size:25px;" class="mdi mdi-file-excel-box"></i></a>
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