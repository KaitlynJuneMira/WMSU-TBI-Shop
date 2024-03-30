$(document).ready(function(){
	$("#getPrice").change(function(){
		var size = $(this).val();
		var product_id = $(this).attr("product-id");
		var currency = $(this).attr("currency");
		if(currency=="" || currency==undefined){
			var currency = "PHP";	
		}
		$.ajax({
			headers: {
        		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    		},
			url:'/get-product-price',
			data:{size:size,product_id:product_id,currency:currency},
			type:'post',
			success:function(resp){
				
				if(resp['discount']>0){
					$(".getAttributePrice").html("<div class='price'><h4>"+currency+" "+resp['final_price']+"</h4></div><div class='original-price'><span>Original Price: </span><span>"+currency+" "+resp['product_price']+"</span></div>");
				}else{
					$(".getAttributePrice").html("<div class='price'><h4>"+currency+" "+resp['final_price']+"</h4></div>");
				}
			},error:function(){
				alert("Error");
			}
		});
	});

	// Update Cart Items Qty
	$(document).on('click','.updateCartItem',function(){
		if($(this).hasClass('plus-a')){
			// Get Qty
			var quantity = $(this).data('qty');
			// increase the qty by 1
			new_qty = parseInt(quantity) + 1;
			/*alert(new_qty);*/
		}
		if($(this).hasClass('minus-a')){
			// Get Qty
			var quantity = $(this).data('qty');
			// Check Qty is atleast 1
			if(quantity<=1){
				alert("Item quantity must be 1 or greater!");
				return false;
			}
			// increase the qty by 1
			new_qty = parseInt(quantity) - 1;
			/*alert(new_qty);*/
		}
		var cartid = $(this).data('cartid');
		var currency = $(this).attr("currency");
		$.ajax({
			headers: {
        		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    		},
			data:{cartid:cartid,qty:new_qty,currency:currency},
			url:'/cart/update',
			type:'POST',
			success:function(resp){
				$(".totalCartItems").html(resp.totalCartItems);
				if(resp.status==false){
					alert(resp.message);
				}
				$("#appendCartItems").html(resp.view);
				$("#appendHeaderCartItems").html(resp.headerview);
			},error:function(){
				// alert("Error");
				
			}
		});
	});
	

	// Delete Cart Item
	$(document).on('click','.deleteCartitem',function(){
		var cartid = $(this).data('cartid');
		var currency = $(this).attr("currency");
		var result = confirm("Are you sure to delete this Cart Item?");
		if(result){
			$.ajax({
				headers: {
	        		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	    		},
	    		data:{cartid:cartid,currency:currency},
	    		url:'/cart/delete',
	    		type:'post',
	    		success:function(resp){
	    			$(".totalCartItems").html(resp.totalCartItems);
					$("#appendCartItems").html(resp.view);
					$("#appendHeaderCartItems").html(resp.headerview);
				},error:function(){
					alert("Error");
				}
			})	
		}
		
	});

	// Show Loader at the time of order placement
	$(document).on('click','#placeOrder',function(){
		$(".loader").show();
	});

	// Register Form Validation
	$("#registerForm").submit(function(){
		$(".loader").show();
		var formdata = $(this).serialize();
		$.ajax({
			url:"/user/register",
			type:"POST",
			data:formdata,
			success:function(resp){
				if(resp.type=="error"){
					$(".loader").hide();
					$.each(resp.errors,function(i,error){
						$("#register-"+i).attr('style','color:red');
						$("#register-"+i).html(error);
					setTimeout(function(){
						$("#register-"+i).css({
							'display':'none'
						});
					},3000);
					});
				}else if(resp.type=="success"){
					/*alert(resp.message);*/
					$(".loader").hide();
					$("#register-success").attr('style','color:green');
					$("#register-success").html(resp.message);
				}
				
			},error:function(){
				alert("Error");
			}
		})
	});

	// Account Form Validation
	$("#accountForm").submit(function(){
		$(".loader").show();
		var formdata = $(this).serialize();
		$.ajax({
			url:"/user/account",
			type:"POST",
			data:formdata,
			success:function(resp){
				if(resp.type=="error"){
					$(".loader").hide();
					$.each(resp.errors,function(i,error){
						$("#account-"+i).attr('style','color:red');
						$("#account-"+i).html(error);
					setTimeout(function(){
						$("#account-"+i).css({
							'display':'none'
						});
					},3000);
					});
				}else if(resp.type=="success"){
					/*alert(resp.message);*/
					$(".loader").hide();
					$("#account-success").attr('style','color:green');
					$("#account-success").html(resp.message);
					setTimeout(function(){
						$("#account-success").css({
							'display':'none'
						});
					},3000);
				}
				
			},error:function(){
				alert("Error");
			}
		})
	});

	// Password Form Validation
	$("#passwordForm").submit(function(){
		$(".loader").show();
		var formdata = $(this).serialize();
		$.ajax({
			url:"/user/update-password",
			type:"POST",
			data:formdata,
			success:function(resp){
				if(resp.type=="error"){
					$(".loader").hide();
					$.each(resp.errors,function(i,error){
						$("#password-"+i).attr('style','color:red');
						$("#password-"+i).html(error);
					setTimeout(function(){
						$("#password-"+i).css({
							'display':'none'
						});
					},3000);
					});
				}else if(resp.type=="incorrect"){
					$(".loader").hide();
					$("#password-error").attr('style','color:red');
					$("#password-error").html(resp.message);
					setTimeout(function(){
						$("#password-error").css({
							'display':'none'
						});
					},3000);
				}else if(resp.type=="success"){
					/*alert(resp.message);*/
					$(".loader").hide();
					$("#password-success").attr('style','color:green');
					$("#password-success").html(resp.message);
					setTimeout(function(){
						$("#password-success").css({
							'display':'none'
						});
					},3000);
				}
				
			},error:function(){
				alert("Error");
			}
		})
	});

	// Login Form Validation
	$("#loginForm").submit(function(){
		var formdata = $(this).serialize();
		$.ajax({
			url:"/user/login",
			type:"POST",
			data:formdata,
			success:function(resp){
				if(resp.type=="error"){
					$.each(resp.errors,function(i,error){
						$("#login-"+i).attr('style','color:red');
						$("#login-"+i).html(error);
					setTimeout(function(){
						$("#login-"+i).css({
							'display':'none'
						});
					},3000);
					});
				}else if(resp.type=="incorrect"){
					/*alert(resp.message);*/	
					$("#login-error").attr('style','color:red');
					$("#login-error").html(resp.message);
				}else if(resp.type=="inactive"){
					/*alert(resp.message);*/	
					$("#login-error").attr('style','color:red');
					$("#login-error").html(resp.message);
				}else if(resp.type=="success"){
					window.location.href = resp.url;	
				}
			},error:function(){
				alert("Error");
			}
		})
	});

	// Forgot Password Form Validation
	$("#forgotForm").submit(function(){
		$(".loader").show();
		var formdata = $(this).serialize();
		$.ajax({
			url:"/user/forgot-password",
			type:"POST",
			data:formdata,
			success:function(resp){
				if(resp.type=="error"){
					$(".loader").hide();
					$.each(resp.errors,function(i,error){
						$("#forgot-"+i).attr('style','color:red');
						$("#forgot-"+i).html(error);
					setTimeout(function(){
						$("#forgot-"+i).css({
							'display':'none'
						});
					},3000);
					});
				}else if(resp.type=="success"){
					/*alert(resp.message);*/
					$(".loader").hide();
					$("#forgot-success").attr('style','color:green');
					$("#forgot-success").html(resp.message);
				}
				
			},error:function(){
				alert("Error");
			}
		})
	});

	// Apply Coupon
    $("#ApplyCoupon").submit(function(){
    	var user = $(this).attr("user");
    	/*alert(user);*/
    	if(user==1){
    		// do nothing
    	}else{
    		alert("Please login to apply Coupon!");
    		return false;
    	}
    	var code = $("#code").val();
    	$.ajax({
    		headers: {
        		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    		},
    		type:'post',
    		data:{code:code},
    		url:'/apply-coupon',
    		success:function(resp){
    			if(resp.message!=""){
    				alert(resp.message);
    			}
    			$(".totalCartItems").html(resp.totalCartItems);
				$("#appendCartItems").html(resp.view);
				$("#appendHeaderCartItems").html(resp.headerview);
				if(resp.couponAmount>0){
					$(".couponAmount").text("PHP "+resp.couponAmount);
				}else{
					$(".couponAmount").text("PHP 0");
				}
				if(resp.grand_total>0){
					$(".grand_total").text("PHP "+resp.grand_total);
				}
    		},error:function(){
    			alert("Error");
    		}
    	})
    });

    // Edit Delivery Address
    $(document).on('click','.editAddress',function(){
    	var addressid = $(this).data("addressid");
    	$.ajax({
    		headers: {
        		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    		},
    		data:{addressid:addressid},
    		url:'/get-delivery-address',
    		type:'post',
    		success:function(resp){
    			$("#showdifferent").removeClass("collapse");
    			$(".newAddress").hide();
    			$(".deliveryText").text("Edit Delivery Address");
    			$('[name=delivery_id]').val(resp.address['id']);
    			$('[name=delivery_name]').val(resp.address['name']);
    			$('[name=delivery_address]').val(resp.address['address']);
    			$('[name=delivery_city]').val(resp.address['city']);
    			$('[name=delivery_state]').val(resp.address['state']);
    			$('[name=delivery_country]').val(resp.address['country']);
    			$('[name=delivery_pincode]').val(resp.address['pincode']);
    			$('[name=delivery_mobile]').val(resp.address['mobile']);
    		},error:function(){
    			alert("Error");
    		}
    	});
    });

    // Save Delivery Address
    $(document).on('submit',"#addressAddEditForm",function(){
    	var formdata = $("#addressAddEditForm").serialize();
    	$.ajax({
    		url: '/save-delivery-address',
    		type:'post',
    		data:formdata,
    		success:function(resp){
    			if(resp.type=="error"){
					$(".loader").hide();
					$.each(resp.errors,function(i,error){
						$("#delivery-"+i).attr('style','color:red');
						$("#delivery-"+i).html(error);
					setTimeout(function(){
						$("#delivery-"+i).css({
							'display':'none'
						});
					},3000);
					});
				}else{
					$("#deliveryAddresses").html(resp.view);
					window.location.href = "checkout";	
				}
    		},error:function(){
    			alert("Error");
    		}
    	});
    })

    // Remove Delivery Address
    $(document).on('click','.removeAddress',function(){
		var addressid = $(this).data("addressid");
		
		// Show modal confirmation
		$('#confirmationModal').modal('show');
	
		// Handle confirmation
		$('#confirmRemove').on('click', function() {
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:'/remove-delivery-address',
				type:'post',
				data:{addressid:addressid},
				success:function(resp){
					$("#deliveryAddresses").html(resp.view);
					window.location.href = "checkout";        
				},
				error:function(){
					alert("Error");
				}
			});
			// Close modal after AJAX request
			$('#confirmationModal').modal('hide');
		});
	});
	
	// Optional: Close modal when cancel button is clicked
	$('#cancelRemove').on('click', function() {
		$('#confirmationModal').modal('hide');
	});
	
    // Calculate Grand Total
    $("input[name=address_id").bind('change',function(){
    	var shipping_charges = $(this).attr("shipping_charges");
    	var total_price = $(this).attr("total_price");
    	var coupon_amount = $(this).attr("coupon_amount");
    	$(".shipping_charges").html("PHP "+shipping_charges);
    	var codpincodeCount = $(this).attr("codpincodeCount");
    	var prepaidpincodeCount = $(this).attr("prepaidpincodeCount");
    	if(codpincodeCount>0){
    		$(".codMethod").show();
    	}else{
    		$(".codMethod").hide();
    	}
    	if(prepaidpincodeCount>0){
    		$(".prepaidMethod").show();
    	}else{
    		$(".prepaidMethod").hide();
    	}
    	if(coupon_amount==""){
    		coupon_amount = 0;
    	}
    	$(".couponAmount").html("PHP "+coupon_amount);
    	var grand_total = parseInt(total_price) + parseInt(shipping_charges) - parseInt(coupon_amount);
    	/*alert(grand_total);*/
    	$(".grand_total").html("PHP "+grand_total);
    });

    // Verify Pincode at Detail Page
    $("#checkPincode").click(function(){
    	var pincode = $("#pincode").val();
    	if(pincode==""){
    		alert("Please enter Pincode");
    		return false;
    	}
    	$.ajax({
    		headers: {
        		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    		},
    		type:'post',
    		data:{pincode:pincode},
    		url:'/check-pincode',
    		success:function(resp){
    			alert(resp);
    		},error:function(){
    			alert("Error");
    		}
    	})
    });


});


function get_filter(class_name){
	var filter = [];
	$('.'+class_name+':checked').each(function(){
		filter.push($(this).val());
	});
	return filter;
}

function addSubscriber(){
	var subscriber_email = $("#subscriber_email").val();
	/*alert(subscriber_email);*/
	var mailFormat =  /\S+@\S+\.\S+/;
  	if (subscriber_email.match(mailFormat)) {
   
  	}else{
    	alert("Please enter valid Email!");
    	return false;
  	}
  	$.ajax({
  		headers: {
    		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
  		type:'post',
  		url:'add-subscriber-email',
  		data:{subscriber_email:subscriber_email},
  		success:function(resp){
  			if(resp=="exists"){
  				alert("Your email is already exists for Newsletter Subscription");
  			}else if(resp=="saved"){
  				alert("Thanks for Subscribing");
  			}
  		},error:function(){
  			alert("Error");
  		}
  	});
}