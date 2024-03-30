@extends('admin.layout.layout')
@section('content')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Chat</h4>
                        <div class="chat-container">
                            <div class="border border-dark-light m-3 p-3 rounded">
                                <!-- Display chat messages here -->
                                <table>
                                    
                                   <thead>
                                   <tr>
                                        <td style="widthL 5px;">
                                                <span style="color: #333; font-weight: bold;">
                                                     @php 
                                                        $client_name = \App\Models\User::where('id', $message->user_id)->first();
                                                    @endphp
                                                    {{ $client_name->name }}:
                                        </span>
                                        </td>
                                        <td>
                                            {{ $message->message }}
                                        </td>
                                    </tr>
                                   </thead>

                                   <tbody>
                                   @foreach($replies as $row)
                                        <tr>
                                            <td style="widthL 5px;">
                                                    <span style="color: #333; font-weight: bold;">
                                                    @if ($row->sender_id == Auth::guard('admin')->user()->vendor_id)
                                                        You:
                                                    @else
                                                    @php 
                                                        $user_name = \App\Models\User::where('id', $row->sender_id)->first();
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

                                </table>
                                <!-- Display chat messages here -->
                            </div>
                                <div class="border border-dark-light m-3 p-3 rounded">
                                    <form action="{{ route('reply.submit') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="message_id" value="{{ $message->id }}" hidden>
                                        <input type="hidden" name="receiver_id" value="{{ $message->user_id }}" hidden>
                                        <input type="text" name="message" id="message" class="form-control" placeholder="Type your message here...">
                                        <!-- <label class="input-group-text btn btn-primary" for="image">
                                            <input type="file" id="image" name="image" style="display:none;"> Upload Image
                                        </label> -->
                                            <div class="input-group-append m-3 justify-content-end">
                                                <button type="submit" class="btn btn-success">Send</button>
                                            </div>
                                    </form>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
