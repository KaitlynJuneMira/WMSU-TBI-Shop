@extends('front.layout.layout')
@section('content')
<div class="page-style-a">
    <div class="container">
        <div class="page-intro">
            <ul class="bread-crumb">
            <h2>Inbox</h2>

                <li class="has-separator">
                    <i class="ion ion-md-home"></i>
                    <a href="{{ url('/') }}">Home</a>
                </li>
                <li class="is-marked">
                    <a href="#">Orders</a>
                </li>
            </ul>
        </div>
    </div>
</div>


<div class="page-cart u-s-p-t-80">
    
    <div class="container">
        <div class="back" onclick="goBack()">
        <i class="fas fa-arrow-left"></i> Back
    </div>
        <div class="row">
            <table class="table table-striped table-borderless">
                <tr class="table-danger">
                    <th>Message ID</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Date Created</th>
                    <th>Actions</th>
                </tr>
                @foreach($message_details as $messagelist)
                <tr>
                    <td>{{$messagelist->id}}</td>
                    <td>{{$messagelist->message}}</td>
                    <td>{{$messagelist->status}}</td>
                    <td>{{$messagelist->created_at}}</td>
                    <td>
                        <a href="{{ url('message/message/'. $messagelist->id) }}"><u>Message</u></a>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>

<script>
	// Apply Coupon
  function goBack() {
        window.history.back();
    }
</script>

@endsection