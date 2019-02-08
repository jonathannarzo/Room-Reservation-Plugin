jQuery(document).ready(function($) {
	$('.datepicker').datetimepicker({
		timepicker: false,
		format: 'Y-m-d',
	});

	$("#txtFromDate").datetimepicker({
		timepicker: false,
		format: 'Y-m-d',
		minDate: 0,
		onShow:function(currentDateTime){
			this.setOptions({
				maxDate:$("#txtToDate").val() ? $("#txtToDate").val() : false
			});
		}
	});
	$("#txtToDate").datetimepicker({
		timepicker: false,
		format: 'Y-m-d',
		minDate: 0,
		onShow:function(currentDateTime){
			this.setOptions({
				minDate:$("#txtFromDate").val() ? $("#txtFromDate").val() : false
			});
		}
	});
});