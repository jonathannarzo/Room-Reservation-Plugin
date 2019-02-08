<div class="wrap">
	<h2>Reservation Info</h2>

	<?php
		$rooms = array();
		$total_amount = 0;
		foreach ($data as $key => $item)
		{
			$name = "{$item->first_name} {$item->last_name}";
			$address = $item->address;
			$city = $item->city;
			$country = $item->country;
			$email = $item->email;
			$contact_no = $item->contact_no;

			$arrival = $item->arrival;
			$departure = $item->departure;
			$room[] = "{$item->room_type} (Room: {$item->room_number}) [status: {$item->status}]";
			$total_amount += $item->rate;
			$number_of_nights = $item->number_of_nights;
			$confirmation = $item->confirmation_code;
		}
	?>


	<div class="room-reservation-plain">
		<h3>Personal Details</h3>
		<table>
			<tr><td>Name</td>		<td>:</td><td><?=$name?></td></tr>
			<tr><td>Address</td>	<td>:</td><td><?=$address?></td></tr>
			<tr><td>City</td>		<td>:</td><td><?=$city?></td></tr>
			<tr><td>Country</td>	<td>:</td><td><?=$country?></td></tr>
			<tr><td>Email</td>		<td>:</td><td><?=$email?></td></tr>
			<tr><td>Contact No</td>	<td>:</td><td><?=$contact_no?></td></tr>
		</table>

		<h3>Reservation Details</h3>
		<table>
			<tr><td>Arrival</td>			<td>:</td><td><?=date('F j, Y', strtotime($arrival))?></td></tr>
			<tr><td>Departure</td>			<td>:</td><td><?=date('F j, Y', strtotime($departure))?></td></tr>
			<tr><td>Number of Night/s</td>	<td>:</td><td><?=$number_of_nights?></td></tr>
			<tr><td>Total Amount</td>		<td>:</td><td><?=$total_amount?></td></tr>
			<tr><td>Room/s</td>				<td>:</td><td><?=implode('<br/>', $room)?></td></tr>
			<tr><td>Confirmation</td>		<td>:</td><td><?=$confirmation?></td></tr>
		</table>
	</div>

</div>