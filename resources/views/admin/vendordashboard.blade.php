@extends('admin.layout.vendorlayout')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                        <h3 class="font-weight-bold">Welcome {{ Auth::guard('admin')->user()->name }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 grid-margin transparent">
                <div class="row">
                    <div class="col-md-6 mb-4 stretch-card transparent">
                        <div class="card card-tale">
                            <div class="card-body">
                                <p class="mb-4">Sales</p>
                                <p class="fs-30 mb-2">{{$salesTotal}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4 stretch-card transparent">
                        <div class="card card-dark-blue">
                            <div class="card-body">
                                <p class="mb-4">Current Inventory</p>
                                <p class="fs-30 mb-2">{{$productsCount}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 grid-margin transparent">
                <div class="row">
                    <div class="col-md-6 mb-4 stretch-card transparent">
                        <div class="card card-tale">
                            <div class="card-body">
                                <p class="mb-4">Pending Orders</p>
                                <p class="fs-30 mb-2">{{$ordersCount}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4 stretch-card transparent">
                        <div class="card card-dark-blue">
                            <div class="card-body">
                                <p class="mb-4">Coupons Available</p>
                                <p class="fs-30 mb-2">{{$couponsCount}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class = 'row'>
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <p class="card-title">Sales</p>
                        </div>
                            <div id="sales-legend" class="chartjs-legend mt-4 mb-2"></div>
                            <div class="row d-flex justify-content-end my-2">
                                 <select class="form-control col-2 mr-2" id="export-type-sales" required aria-label="Default select example">
                                    <option selected value="EXCEL">EXCEL</option>
                                    <option value="CSV">CSV</option>
                                    <option value="PDF">PDF</option>
                                </select>
                                <button class="btn btn-success mr-3" id="download-sales">Download</button>
                            </div>
                            <div class="row d-flex">
                                <button class="btn btn-outline-dark col-2 ml-5"  onclick="rendervendorsales()">Refresh</button>
                                <select class="form-control col-2 mx-2" name="" onchange="rendervendorsales()" id="vendorYear" @if($years) value="$years[0]->year" @endif>
                                    @foreach($years as $key =>$value)
                                        <option value="{{$value->year}}">{{$value->year}}</option>
                                    @endforeach
                                </select>
                                <div class="col-3">
                                    <div class="form-check ">
                                        <input class="form-check-input" type="checkbox" value="1" id="vendorsalesPaid" onchange="rendervendorsales()">
                                        <label class="form-check-label" for="flexCheckDefault">
                                            Paid
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <canvas id="vendorsales"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <p class="card-title">Average Order Value</p>
                        </div>
                            <div id="sales-legend" class="chartjs-legend mt-4 mb-2"></div>
                            <div class="row d-flex justify-content-end my-2">
                                 <select class="form-control col-2 mr-2" id="export-type-average-order-value" required aria-label="Default select example">
                                    <option selected value="EXCEL">EXCEL</option>
                                    <option value="CSV">CSV</option>
                                    <option value="PDF">PDF</option>
                                </select>
                                <button class="btn btn-success mr-3" id="download-average-order-value">Download</button>
                            </div>
                            <div class="row d-flex">
                                <button class="btn btn-outline-dark col-2 ml-5"  onclick="rendervendorAve()">Refresh</button>
                                <select class="form-control col-2 mx-2" name="" onchange="rendervendorAve()" id="AverageOrderValue" @if($years) value="$years[0]->year" @endif>
                                    @foreach($years as $key =>$value)
                                        <option value="{{$value->year}}">{{$value->year}}</option>
                                    @endforeach
                                </select>
                                <div class="col-3">
                                    <div class="form-check ">
                                        <input class="form-check-input" type="checkbox" value="1" id="vendorAvePaid" onchange="rendervendorAve()">
                                        <label class="form-check-label" for="flexCheckDefault">
                                            Paid
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <canvas id="vendorave"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <p class="card-title">Top Selling Products</p>
                        </div>
                            <div id="sales-legend" class="chartjs-legend mt-4 mb-2"></div>
                            <div class="row d-flex justify-content-end my-2">
                                 <select class="form-control col-2 mr-2" id="export-type-top-selling-products" required aria-label="Default select example">
                                    <option selected value="EXCEL">EXCEL</option>
                                    <option value="CSV">CSV</option>
                                    <option value="PDF">PDF</option>
                                </select>
                                <button class="btn btn-success mr-3" id="download-top-selling-products">Download</button>
                            </div>
                            <div class="row d-flex">
                                <button class="btn btn-outline-dark col-2 ml-5"  onclick="rendervendorTop()">Refresh</button>
                                <select class="form-control col-2 mx-2" name="" onchange="rendervendorTop()" id="TopSellingProducts" @if($years) value="$years[0]->year" @endif>
                                    @foreach($years as $key =>$value)
                                        <option value="{{$value->year}}">{{$value->year}}</option>
                                    @endforeach
                                </select>
                                <div class="col-3">
                                    <div class="form-check ">
                                        <input class="form-check-input" type="checkbox" value="1" id="vendorTopPaid" onchange="rendervendorTop()">
                                        <label class="form-check-label" for="flexCheckDefault">
                                            Paid
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <canvas id="topselling"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6 grid-margin stretch-card d-none">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <p class="card-title">Inventory Turn Over</p>
                        </div>
                            <div id="sales-legend" class="chartjs-legend mt-4 mb-2"></div>
                            <div class="row d-flex justify-content-end my-2">
                                <select class="form-control col-2 mr-2" id="export-type-inventory-turn-over" required aria-label="Default select example">
                                    <option selected value="EXCEL">EXCEL</option>
                                    <option value="CSV">CSV</option>
                                    <option value="PDF">PDF</option>
                                </select>
                                <button class="btn btn-success mr-3" id="download-inventory-turn-over">Download</button>
                            </div>
                            <div class="row d-flex">
                                <button class="btn btn-outline-dark col-2 ml-5"  onclick="renderCategory()">Refresh</button>
                                <select class="form-control col-2 mx-2" name="" onchange="renderCategory()" id="topCategory" @if($years) value="$years[0]->year" @endif>
                                    @foreach($years as $key =>$value)
                                        <option value="{{$value->year}}">{{$value->year}}</option>
                                    @endforeach
                                </select>
                            </div>
                        <canvas id="inventory"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <p class="card-title">Order Status</p>
                        </div>
                            <div id="sales-legend" class="chartjs-legend mt-4 mb-2"></div>
                            <div class="row d-flex justify-content-end my-2">
                                    <select class="form-control col-2 mr-2" id="export-type-order-status" required aria-label="Default select example">
                                        <option selected value="EXCEL">EXCEL</option>
                                        <option value="CSV">CSV</option>
                                        <option value="PDF">PDF</option>
                                    </select>
                                <button class="btn btn-success mr-3" id="download-order-status">Download</button>
                            </div>
                            <div class="row d-flex">
                                <button class="btn btn-outline-dark col-2 ml-5"  onclick="renderCancelation()">Refresh</button>
                                <select class="form-control col-2 mx-2" name="" onchange="renderCancelation()" id="OrderStatusYear" @if($years) value="$years[0]->year" @endif>
                                    @foreach($years as $key =>$value)
                                        <option value="{{$value->year}}">{{$value->year}}</option>
                                    @endforeach
                                </select>
                            </div>
                        <canvas id="cancelation"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <p class="card-title">Sales Growth Over Time</p>
                        </div>
                            <div id="sales-legend" class="chartjs-legend mt-4 mb-2"></div>
                            <div class="row d-flex justify-content-end my-2">
                                 <select class="form-control col-2 mr-2" id="export-type" required aria-label="Default select example">
                                    <option selected value="EXCEL">EXCEL</option>
                                    <option value="CSV">CSV</option>
                                    <option value="PDF">PDF</option>
                                </select>
                                <button class="btn btn-success mr-3" >Download</button>
                            </div>
                            <div class="row d-flex">
                            <button class="btn btn-outline-dark col-2  ml-5"  onclick="renderSalesGrownth()">Refresh</button>
                            <select class="form-control col-2 mx-2" name="" onchange="renderSalesGrownth()" id="SalesGrowthOverTime" @if($years) value="$years[0]->year" @endif>
                                @foreach($years as $key =>$value)
                                    <option value="{{$value->year}}">{{$value->year}}</option>
                                @endforeach
                            </select>
                        </div>
                        <canvas id="salesgrowth"></canvas>
                    </div>
                </div>
            </div>
        </div>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script>
        $('#download-sales').click(function(e){
            var year = $('#vendorYear').val()
            var paid = $('#vendorsalesPaid').is(":checked")
            var export_type = $('#export-type-sales').val();
            e.preventDefault(); 
            window.location.href = 'dashboard-vendorsalesDownload/'+export_type+'/'+year+'/'+paid;
        });
        $('#download-average-order-value').click(function(e){
            var year = $('#AverageOrderValue').val()
            var paid = $('#vendorAvePaid').is(":checked")
            var export_type = $('#export-type-average-order-value').val();
            e.preventDefault(); 
            window.location.href = 'dashboard-vendoraverageOrderValueDownload/'+export_type+'/'+year+'/'+paid;
        });
        $('#download-top-selling-products').click(function(e){
            var year = $('#TopSellingProducts').val()
            var paid = $('#vendorTopPaid').is(":checked")
            var export_type = $('#export-type-top-selling-products').val();
            e.preventDefault(); 
            window.location.href = 'dashboard-vendortopSellingProductsDownload/'+export_type+'/'+year+'/'+paid;
        });
        $('#download-order-status').click(function(e){
            var year = $('#OrderStatusYear').val()
            var export_type = $('#export-type-order-status').val();
            e.preventDefault(); 
            window.location.href = 'dashboard-vendororderStatusDownload/'+export_type+'/'+year
        });
        
    </script>
</div>
@endsection
