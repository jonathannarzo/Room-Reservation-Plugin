jQuery(document).ready(function($) {
	$('.datepicker').datetimepicker({
		timepicker: false,
		format: 'Y-m-d',
	});

	$("#room_reserve_page_form #txtFromDate").datetimepicker({
		timepicker: false,
		format: 'Y-m-d',
		minDate: 0,
		onShow:function(currentDateTime){
			this.setOptions({
				maxDate:$("#room_reserve_page_form #txtToDate").val() ? $("#room_reserve_page_form #txtToDate").val() : false
			});
		}
	});
	$("#room_reserve_page_form #txtToDate").datetimepicker({
		timepicker: false,
		format: 'Y-m-d',
		minDate: 0,
		onShow:function(currentDateTime){
			this.setOptions({
				minDate:$("#room_reserve_page_form #txtFromDate").val() ? $("#room_reserve_page_form #txtFromDate").val() : false
			});
		}
	});

	$("#room_reserve_widget_form #txtFromDate").datetimepicker({
		timepicker: false,
		format: 'Y-m-d',
		minDate: 0,
		onShow:function(currentDateTime){
			this.setOptions({
				maxDate:$("#room_reserve_widget_form #txtToDate").val() ? $("#room_reserve_widget_form #txtToDate").val() : false
			});
		}
	});
	$("#room_reserve_widget_form #txtToDate").datetimepicker({
		timepicker: false,
		format: 'Y-m-d',
		minDate: 0,
		onShow:function(currentDateTime){
			this.setOptions({
				minDate:$("#room_reserve_widget_form #txtFromDate").val() ? $("#room_reserve_widget_form #txtFromDate").val() : false
			});
		}
	});
	
});