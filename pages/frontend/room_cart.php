<?php $number_of_nights = RoomReservationApp\Includes\Common_Helper::get_num_nights($start_date, $end_date); ?>
<table class="room-reservation-avalable-rooms-table">
	<thead>
		<tr>
			<th>Room Type</th>
			<th>Qty</th>
			<th>Rate</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	<?php
		$total = 0;
		foreach ($_SESSION['reservation_cart'] as $key => $item) :
		$total += $item['rate'];
	?>
		<tr class="row-selected-rooms">
			<td><?=$item['room_type']?></td>
			<td><?=$item['reserve_qty']?></td>
			<td><?=number_format($item['rate'], 2)?></td>
			<td><button class="room-reservation-button button-small remove-room-from-cart" data="<?=$key?>">Remove</button></td>
		</tr>
	<?php endforeach; ?>
	<tr>
		<td><b>TOTAL</b></td>
		<td><?=number_format($total, 2)?> <b>x</b> <?=$number_of_nights?> night/s</td>
		<td><?=number_format($total * $number_of_nights, 2)?></td>
		<td></td>
	</tr>
	</tbody>
</table>