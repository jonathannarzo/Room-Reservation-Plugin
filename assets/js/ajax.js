jQuery(document).ready(function($){

	check_selected_room_count();

	$(document).on('click', '.btn-room-cart', function(){
		var button = $(this);
		button.prop('disabled', true);

		var room_data = $(this).attr('data');
		var json_data = $.parseJSON(room_data);
		var data = {};
		data['action'] = ajaxRequest.ajaxfunction;
		data['process_method'] = 'add_to_cart';
		data['data'] = json_data;
		
		$.ajax({
			url: ajaxRequest.ajaxurl,
			dataType: 'json',
			type: 'POST',
			data: data,
			success:function(sdata){
				if (sdata.success)
				{
					get_room_cart(data['data']);
					$('#room_cart'+sdata.id).hide();
					button.prop('disabled', false);
					$('.room-cart-container').show();
				}
			}
		});
	});

	$(document).on('click', '.remove-room-from-cart', function(){
		var parent_tr = $(this).parents('tr.row-selected-rooms');
		var data = {};
		data['action'] = ajaxRequest.ajaxfunction;
		data['process_method'] = 'remove_room_from_cart';
		data['key'] = $(this).attr('data');
		
		$.ajax({
			url: ajaxRequest.ajaxurl,
			dataType: 'json',
			type: 'POST',
			data: data,
			success:function(data){
				if (data.success)
				{
					parent_tr.remove();
					window.location.href="";
				}
			}
		});
	});

	$(document).on('submit', '#formTest', function(){
		var form_data = wp_ajax_data($(this).serializeArray());
		console.log(form_data);
		$.ajax({
			url: ajaxRequest.ajaxurl,
			dataType: 'json',
			type: 'POST',
			data: $.param(form_data),
			success:function(data){}
		});

		return false;
	});


});

function wp_ajax_data(serializedData)
{
	serializedData.push({name: 'action', value: ajaxRequest.ajaxfunction});
	return serializedData;
}

function get_room_cart(d)
{
	data = {};
	data['action'] = ajaxRequest.ajaxfunction;
	data['process_method'] = 'get_room_cart';
	data['start_date'] = d['start_date'];
	data['end_date'] = d['end_date'];
	jQuery.ajax({
		url: ajaxRequest.ajaxurl,
		type: 'POST',
		data: data,
		success:function(data){
			jQuery('.room-cart').html(data);
		}
	});
}

function check_selected_room_count()
{
	jQuery(document).ready(function($){
		if ($('.row-selected-rooms').length > 0)
		{
			$('.room-cart-container').show();
		}
		else
		{
			$('.room-cart-container').hide();	
		}
	});
}