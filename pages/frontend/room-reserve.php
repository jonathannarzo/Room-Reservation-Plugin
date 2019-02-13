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
		$rooms[] = "{$item->room_type} (Qty: {$item->reserved_qty})";
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
		<tr><td>Room/s</td>				<td>:</td><td><?=implode('<br/>', $rooms)?></td></tr>
	</table>
	
	<p></p>
	
	<?php echo str_repeat('*', 34); ?>
</div>

<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
	<input type="hidden" name="cmd" value="_xclick" />
	<input type="hidden" name="business" value="<?=$settings->paypal_email?>" />
	<input type="hidden" name="item_name" value="Rooms Reserve" />
	<input type="hidden" name="item_number" value="<?=$confirmation; ?>" />
	<input type="hidden" name="amount" value="<?=$total_amount; ?>" />
	<input type="hidden" name="no_shipping" value="1" />
	<input type="hidden" name="no_note" value="1" />
	<input type="hidden" name="currency_code" value="PHP" />
	<input type="hidden" name="lc" value="GB" />
	<input type="hidden" name="bn" value="PP-BuyNowBF" />
	<input type="image" src="<?=JMN_RR_URL.'assets/images/paypal_button.png'?>" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!" />
	<img alt="fdff" border="0" src="<?=JMN_RR_URL.'assets/images/paypal_button.png'?>" width="1" height="1" />
	<!-- Payment confirmed -->
	<input type="hidden" name="return" value="<?=site_url("?page_id={$settings->booking_page}&payconfirm={$confirmation}")?>" />
	<!-- Payment cancelled -->
	<input type="hidden" name="cancel_return" value="<?=site_url("?page_id={$settings->booking_page}&cancel={$confirmation}")?>" />
	<input type="hidden" name="rm" value="2" />
	<input type="hidden" name="notify_url" value="<?=site_url("?page_id={$settings->booking_page}&notify={$confirmation}")?>" />
</form>