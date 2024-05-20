@extends('admin.layout.layout')
@section('content')
<!-- Display all of the orders -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Orders</h4>
                        <div class='row d-flex justify-content-between'>
                            <a href="javascript:history.back()" class="back-link mx-3">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <button class="btn btn-success mx-3" data-toggle="modal" data-target="#downloadProductModal">Download</button>
                            <div class="modal fade" id="downloadProductModal" tabindex="-1" role="dialog" aria-labelledby="downloadProductModalLabel" aria-hidden="true" wire:ignore.self>
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <!-- Create downloadable files for all of the orders -->
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
                                                ['table_name'=>'Order-ID','column_name'=>'o.id'],
                                                ['table_name'=>'Order-Date','column_name'=>'o.created_at'],
                                                ['table_name'=>'Customer-Name','column_name'=>'u.name'],
                                                ['table_name'=>'Customer-Email','column_name'=>'u.email'],
                                                ['table_name'=>'Ordered-Products','column_name'=>'product_qty'],
                                                ['table_name'=>'Product-Price','column_name'=>'product_price'],
                                                ['table_name'=>'Order-Amount','column_name'=>'o.grand_total'],
                                                ['table_name'=>'Order-Status','column_name'=>'o.order_status'],
                                                ['table_name'=>'Payment-Methods','column_name'=>'o.payment_method']
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
                                    window.location.href = '/admin/ExportOrders/'+export_type+'/'+encoded_columns+'/'+encoded_column_names;
                                 });
                            </script>
                        </div>
                        <div class="table-responsive pt-3">
                            <table id="orders" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>
                                            Order ID
                                        </th>
                                        <th>
                                            Order Date
                                        </th>
                                        <th>
                                            Customer Name
                                        </th>
                                        <th>
                                            Customer Email
                                        </th>
                                        <th>
                                            Ordered Products
                                        </th>
                                        <th>
                                            Order Amount
                                        </th>
                                        <th>
                                            Order Status
                                        </th>
                                        <th>
                                            Payment Methods
                                        </th>
                                        <th>
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                  @foreach($orders as $order)
                                  @if($order['orders_products'])
                                    <tr>
                                        <td>
                                            {{ $order['id'] }}
                                        </td>
                                        <td>
                                            {{ date('Y-m-d h:i:s', strtotime($order['created_at'])) }}
                                        </td>
                                        <td>
                                            {{ $order['name'] }}
                                        </td>
                                        <td>
                                            {{ $order['email'] }}
                                        </td>
                                        <td>
                                            @foreach($order['orders_products'] as $product)
                                                {{ $product['product_code'] }} ({{ $product['product_qty']}})<br>
                                            @endforeach
                                        </td>
                                        <td>
                                        {{ number_format($order['grand_total'], 2) }}

                                        </td>
                                        <td>
                                            {{ $order['order_status'] }}
                                        </td>
                                        <td>
                                            {{ $order['payment_method'] }}
                                        </td>
                                        <td>
                                            <a title="View Order Details" href="{{ url('admin/orders/'.$order['id']) }}"><i style="font-size:25px;" class="mdi mdi-file-document"></i></a>
                                            &nbsp;&nbsp;
                                            <a target="_blank" title="View Order Invoice" href="{{ url('admin/orders/invoice/'.$order['id']) }}"><i style="font-size:25px;" class="mdi mdi-printer"></i></a>
                                            &nbsp;&nbsp;
                                            <a target="_blank" title="Print PDF Invoice" href="{{ url('admin/orders/invoice/pdf/'.$order['id']) }}"><i style="font-size:25px;" class="mdi mdi-file-pdf"></i></a>
                                        </td>
                                    </tr>
                                    @endif
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