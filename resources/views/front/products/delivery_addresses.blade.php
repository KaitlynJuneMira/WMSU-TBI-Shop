    <!-- Form-Fields /- -->
    <h4 class="section-h4 deliveryText">Add New Delivery Address</h4>
    <div class="u-s-m-b-24">
        <input type="checkbox" class="check-box" id="ship-to-different-address" data-toggle="collapse" data-target="#showdifferent">
        @if(count($deliveryAddresses)>0)
            <label class="label-text newAddress" for="ship-to-different-address">Ship to a different address?</label>
        @else
            <label class="label-text newAddress" for="ship-to-different-address">Check to add Delivery Address</label>
        @endif
    </div>

    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 100%; max-height: 100%; width: 400px; height: 400px;">
                <div class="modal-content shadow">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to remove this address?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelRemove" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmRemove">Remove</button>
                </div>
                </div>
            </div>
        </div>


    <div class="collapse" id="showdifferent">
        <!-- Form-Fields -->
        <form id="addressAddEditForm" action="javascript:;" method="post">@csrf
            <input type="hidden" name="delivery_id">
            <div class="group-inline u-s-m-b-13">
                <div class="group-1 u-s-p-r-16">
                    <label for="first-name-extra">Name (ex:Name Surname)
                        <span class="astk">*</span>
                    </label>
                    <input type="text" name="delivery_name" id="delivery_name" class="text-field">
                    <p id="delivery-delivery_name"></p>
                </div>
                <div class="group-2">
                    <label for="last-name-extra">Address 
                        <span class="astk">*</span>
                    </label>
                    <input type="text" name="delivery_address" id="delivery_address" class="text-field">
                    <p id="delivery-delivery_address"></p>
                </div>
            </div>
            <div class="group-inline u-s-m-b-13">
                <div class="group-1 u-s-p-r-16">
                    <label for="first-name-extra">City
                        
                    </label>
                    <input type="text" name="delivery_city" id="delivery_city" class="text-field" value="Zamboanga City" readonly="">
                    <p id="delivery-delivery_city"></p>
                </div>
                <div class="group-2">
                    <label for="last-name-extra">Barangay
                        <span class="astk">*</span>
                    </label>
                    <div class="select-box-wrapper">
                    <select class="select-box" id="delivery_barangay" name="delivery_barangay">
                        <option value="">Select Barangay</option>
                    @foreach($barangay as $zcbarangay)
                    <option value="{{ $zcbarangay['barangay_name'] }}" @if($zcbarangay['barangay_name']==$zcbarangay['barangay_name']) selected @endif>{{ $zcbarangay['barangay_name'] }}</option>
                    @endforeach
                  </select>
                  <p id="delivery-delivery_barangay"></p>
                </div>
                </div>
            </div>
            <div class="u-s-m-b-13">
                <label for="select-country-extra">Province
                    
                </label>
                <input type="text" id="delivery_country" name="delivery_country" class="text-field" value="Zamboanga Del Sur" readonly="">
                  <p id="delivery-delivery_country"></p>
            </div>
            <div class="u-s-m-b-13">
                <label for="postcode-extra">Zip code
                    
                </label>
                <input type="text" id="delivery_pincode" name="delivery_pincode" class="text-field" value="7000" readonly="">
                <p id="delivery-delivery_pincode"></p>
            </div>
            <div class="u-s-m-b-13">
                <label for="postcode-extra">Mobile
                    <span class="astk">*</span>
                </label>
                <input type="text" id="delivery_mobile" name="delivery_mobile" class="text-field" max="11" value ="09">
                <p id="delivery-delivery_mobile"></p>
            </div>
            <div class="u-s-m-b-13">
                <button style="width:100%;" type="submit" class="button button-outline-secondary">Save</button>
            </div>
        </form>
        <!-- Form-Fields /- -->
    </div>
    <div>
        <label for="order-notes">Order Notes</label>
        <textarea class="text-area" id="order-notes" placeholder="Notes about your order, e.g. special notes for delivery."></textarea>
    </div>



    

