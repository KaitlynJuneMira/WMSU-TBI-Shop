@extends('admin.layout.layout')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Currencies</h4>
                        <a style="max-width: 150px; float: right; display: inline-block;" href="{{ url('admin/add-edit-currency') }}" class="btn btn-block btn-primary">Add Currency</a>
                        @if(Session::has('success_message'))
                          <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success: </strong> {{ Session::get('success_message')}}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                        @endif
                        <div class="table-responsive pt-3">
                            <table id="currencies" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>
                                            ID
                                        </th>
                                        <th>
                                            Currency Code
                                        </th>
                                        <th>
                                            Exchange Rate
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
                                  @foreach($currencies as $currency)
                                    <tr>
                                        <td>
                                            {{ $currency['id'] }}
                                        </td>
                                        <td>
                                            {{ $currency['currency_code'] }}
                                        </td>
                                        <td>
                                            {{ $currency['exchange_rate'] }}
                                        </td>
                                        <td>
                                            @if($currency['status']==1)
                                              <a class="updateCurrencyStatus" id="currency-{{ $currency['id'] }}" currency_id="{{ $currency['id'] }}" href="javascript:void(0)"><i style="font-size:25px;" class="mdi mdi-bookmark-check" status="Active"></i></a>
                                            @else
                                              <a class="updateCurrencyStatus" id="currency-{{ $currency['id'] }}" currency_id="{{ $currency['id'] }}" href="javascript:void(0)"><i style="font-size:25px;" class="mdi mdi-bookmark-outline" status="Inactive"></i></a>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ url('admin/add-edit-currency/'.$currency['id']) }}"><i style="font-size:25px;" class="mdi mdi-pencil-box"></i></a>
                                            <a href="javascript:void(0)" class="confirmDelete" module="currency" moduleid="{{ $currency['id'] }}"><i style="font-size:25px;" class="mdi mdi-file-excel-box"></i></a>
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