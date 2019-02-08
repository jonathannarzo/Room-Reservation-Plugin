jQuery(document).ready(function($){
	if ($('#images-selected').length > 0 && $('#images-selected').val() != null)
	{
		var images = $('#images-selected').val().split(',');
		var images_view = [];
		for (var i = 0; i < images.length; i++) {
			images_view.push('<img src="'+images[i]+'" style="width:200px;" />')
		}
		$('#twbs_photos_to_upload').html(images_view.join(''));
	}

	if ($('#room_type_id').length > 0 && $('#room_description').length > 0)
	{
		$('#room_type_id').change(function(event) {
			var description = $(this).find('option:selected').attr('description');
			$('#room_description').html(description);
		});
	}
});