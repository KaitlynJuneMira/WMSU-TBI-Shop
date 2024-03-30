@extends('front.layout.layout')
@section('content')
<div class="page-style-a">
    <div class="container">
        <div class="page-intro">
            <ul class="bread-crumb">
                <li class="has-separator">
                    <i class="ion ion-md-home"></i>
                    <a href="{{ url('/') }}">Home</a>
                </li>
                <li class="is-marked">
                    <a href="#">Messages</a>
                </li> 
            </ul>
        </div>
    </div>
</div>

<!-- Message form -->
<div class="container">
    <div class="border border-dark-light m-3 p-3 rounded">
        <div class="border border-dark m-3 p-3 rounded">
            <table class="table">
                <tbody>
                <thead>
                    <tr>
                            <td style="width: 5px;">
                                <span style="color: #333; font-weight: bold;">   @if ($message->user_id == Auth::id())
                                        You:
                                        @endif</span>
                            </td>
                            <td>
                                {{$message->message}}
                            </td>
                        </tr>
                </thead>
                <tbody>
                    @foreach($replies as $row)
                        <tr>
                            <td style="widthL 5px;">
                                    <span style="color: #333; font-weight: bold;">
                                    @if ($row->sender_id == Auth::id())
                                        You:
                                    @else
                                        @php 
                                            $user_name = \App\Models\Vendor::where('id', $row->sender_id)->first();
                                            
                                        @endphp
                                        {{ $user_name->name }}:
                                    @endif
                                    </span>
                            </td>
                            <td>
                                {{ $row->message }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                </tbody>
            </table>
        </div>
        <div class=" m-3 p-3">
            <form action="{{ route('message.submit') }}" method="POST">
                @csrf
                <input type="hidden" name="message_id" value="{{ $message->id }}" hidden>
                <input type="hidden" name="vendor_id" value="{{ $message->vendor_id }}" hidden>
                <input type="hidden" name="user_id" value="{{ Auth::id() }}" hidden>
                <input type="text" name="message" class="form-control" placeholder="Type your message...">
                <div class="input-group-append m-3 p-3 justify-content-end">
                <button class="btn btn-primary" type="submit">Send</button>    
            </form>
        </div>
    </div>
</div>


    </div>
<!-- Message form -->

@endsection