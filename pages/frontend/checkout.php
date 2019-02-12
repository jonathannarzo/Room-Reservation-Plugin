<h1>Check out</h1>

<?php if ($conflict_count > 0) : ?>
	<div style="color:red;"><?=$conflict_count?> Reservation/s has been removed because it is already Unavailable.</div>
	<p></p>
<?php endif; ?>

<div class="room-cart-container" style="display: none;">
	<div class="room-cart-header">Selected Room/s</div>
	<div class="room-cart-content">
		
		<b><?=date('F j, Y', strtotime($start_date))?> to <?=date('F j, Y', strtotime($end_date))?></b>

		<div class="room-cart">
			<?php include(JMN_RR_DIR.'pages/frontend/room_cart.php'); ?>
		</div>

	</div>
</div>

<p></p>

<div class="room-cart-container">
	<div class="room-cart-header">Personal Details</div>
	<div class="room-cart-content">

		<form method="POST">
			<input type="hidden" name="start_date" value="<?=$start_date?>" />
			<input type="hidden" name="end_date" value="<?=$end_date?>" />

			<table class="room-reservation-check-date-table">
				<tr>
					<td style="width:100px;">First Name</td>
					<td><input type="text" name="first_name"  autocomplete="off" /></td>
				</tr>
				<tr>
					<td>Last Name</td>
					<td><input type="text" name="last_name" autocomplete="off" /></td>
				</tr>
				<tr>
					<td>Address</td>
					<td><input type="text" name="address" autocomplete="off" /></td>
				</tr>
				<tr>
					<td>City</td>
					<td><input type="text" name="city" autocomplete="off" /></td>
				</tr>
				<tr>
					<td>Country</td>
					<td><input type="text" name="country" autocomplete="off" /></td>
				</tr>
				<tr>
					<td>Email</td>
					<td><input type="text" name="email" autocomplete="off" /></td>
				</tr>
				<tr>
					<td>Contact</td>
					<td><input type="text" name="contact_no" autocomplete="off" /></td>
				</tr>
				<tr>
					<td></td>
					<td><button name="submit_checkout" type="submit" class="room-reservation-button">Checkout</button></td>
				</tr>
			</table>
		</form>

	</div>
</div>