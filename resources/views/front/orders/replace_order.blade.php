@extends('front.layout.layout')

@section('content')

<!-- Page Introduction Wrapper -->
<div class="page-style-a">
    <div class="container">
        <div class="page-intro">
        <h2>Replace or Refund {{ $item_details->product_name }}</h2>

            <ul class="bread-crumb">
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

<div class="container">
   
    <div class="border border-dark-light m-3 p-3 rounded">
    <form action="{{ url('replace-order/RRlist')}}" method="post" enctype="multipart/form-data">
    
        <input type="hidden" name="product_id" value="{{$item_details->product_id}}">
        <input type="hidden" name="seller_id" value="{{$item_details->vendor_id}}">
        <input type="hidden" name="customer_id" value="{{$item_details->user_id}}">
        
        @csrf
            <div class="row">
                <div class="col-md-6">
                    <!-- Text Inputs -->
                   <h5 class="text-center">Type your reasons below:</h5>
                    <textarea class="text-area" name="message" id="message" placeholder="Type Here..." minlength="0" maxlength="100" cols="40" rows="10"></textarea>
                </div>

                <div class="col-md-6">
                    <div>
                        <h5 class="text-center">Video Proof</h5>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="color: #555;">Select Option:</label><br>
                        <input type="radio" id="replace" name="option" value="replace">
                        <label for="replace">Replace</label><br>
                        <input type="radio" id="refund" name="option" value="refund">
                        <label for="refund">Refund</label><br>
                    </div>
                    <!-- Video Input -->
                    <div style="margin-bottom: 15px;">
                        <label for="proofvideo" style="color: #555;">Video (Max 100 MB):</label><br>
                        <input type="file" id="proofvideo" name="proofvideo" accept="video/*" style="width: calc(100% - 22px); padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-size: 16px;" onchange="previewVideo(this)">
                    </div>

                    <!-- Video Preview (if needed) -->
                    <div id="videoPreview" style="margin-bottom: 15px; display: none;">
                        <video id="previewVideo" controls style="max-width: 100%; margin-top: 10px;"></video>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <input type="submit" value="Submit" style="width: 100%; background-color: #d90429; color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection


<script>
   function previewVideo(input, previewId) {
    var preview = document.getElementById(previewId);
    var videoFile = input.files[0];
    var validVideoTypes = ['video/mp4', 'video/webm', 'video/ogg']; // Add more video types if needed

    if (videoFile && validVideoTypes.includes(videoFile.type)) {
        var reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(videoFile);
    } else {
        preview.src = "";
        preview.style.display = 'none';
        alert("Please select a valid video file (MP4, WebM, OGG).");
    }
}

</script>
