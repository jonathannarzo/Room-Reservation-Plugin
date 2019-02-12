<div class="wrap">
	<h2>Reservation Info</h2>

	<?php
		$name = "{$data->first_name} {$data->last_name}";
		$address = $data->address;
		$city = $data->city;
		$country = $data->country;
		$email = $data->email;
		$contact_no = $data->contact_no;

		$arrival = $data->arrival;
		$departure = $data->departure;
		$room = "{$data->room_type} (Qty: {$data->qty})";
		$total_amount = $data->rate;
		$number_of_nights = $data->number_of_nights;
		$status = $data->status;
		$confirmation = $data->confirmation_code;
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
			<tr><td>Room</td>				<td>:</td><td><?=$room?></td></tr>
			<tr><td>Status</td>				<td>:</td><td><?=$status?></td></tr>
			<tr><td>Confirmation</td>		<td>:</td><td><?=$confirmation?></td></tr>
		</table>
	</div>

</div>