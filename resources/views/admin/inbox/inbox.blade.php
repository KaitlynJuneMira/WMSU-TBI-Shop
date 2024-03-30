@extends('admin.layout.layout')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 calss="card-title">Inbox</h4>
                        <a href="javascript:history.back()" class="back-link">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>

                        <div class="table-resposive pt-3">
                            <table id="inbox"  class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>
                                            User name
                                        </th>
                                        <th>
                                            Item Name
                                        </th>
                                        <th>
                                            Message
                                        </th>
                                        <th>
                                            Service
                                        </th>
                                        <th>
                                            Video Proof
                                        </th>
                                        <th>
                                            Status
                                        </th>
                                        <th>
                                            Date Created
                                        </th>
                                        <th>
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tickets as $ticket)
                                    <tr>
                                        <td>
                                            <!-- Retrieve and display client name -->
                                            @php 
                                                $client_name = \App\Models\User::where('id', $ticket['user_id'])->first();
                                            @endphp
                                            {{ $client_name->name }}
                                        </td>
                                        <td>
                                            <!-- Retrieve and display product name -->
                                            @php 
                                                $product_name = \App\Models\Product::where('id', $ticket['orders_products_id'])->first();
                                            @endphp
                                            {{ $product_name->product_name }}
                                        </td>
                                        <td>{{$ticket['message']}}</td>
                                        <td>{{$ticket['service']}}</td>
                                           <td>
                
                                          <!-- Displays the video proof from the user -->
                                        @if($ticket->videoContent !== null)
                                            <video width="320" height="240" controls>
                                                <source src="data:video/mp4;base64,{{ base64_encode($ticket->videoContent) }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        @else
                                            <p>No video available</p>
                                        @endif
                                        
                                        </td>
                                        <td>{{$ticket['status']}}</td>
                                       <td>{{ \Carbon\Carbon::parse($ticket['created_at'])->format('Y/m/d H:i:s') }}</td>
                                        <td>
                                        <a href="{{ url('admin/reply/'. $ticket['id'])}}" class="btn btn-primary">Reply</a>
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