<?php
	$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
	$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
?>
<form method="GET">
	<table class="room-reservation-check-date-table">
		<tr>
			<td style="width:100px;">Start Date</td>
			<td><input type="text" name="start_date" id="txtFromDate" value="<?=$start_date?>" autocomplete="off" /></td>
		</tr>
		<tr>
			<td>End Date</td>
			<td><input type="text" name="end_date" id="txtToDate" value="<?=$end_date?>" autocomplete="off" /></td>
		</tr>
		<tr>
			<td></td>
			<td><button name="check_room_availability" type="submit" class="room-reservation-button">Check Availability</button></td>
		</tr>
	</table>
</form>
<div class="room-reservation-separator"></div>