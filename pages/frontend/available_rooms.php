<div class="room-cart-container" style="display: none;">
	<div class="room-cart-header">Selected Room/s</div>
	<div class="room-cart-content">
		<div class="room-cart">
			<?php
				$not_empty_cart = ( ! empty($_SESSION['reservation_cart']));
				if ($not_empty_cart) include(JMN_RR_DIR.'pages/frontend/room_cart.php');
			?>
		</div>
	
		<form method="GET">
			<input type="hidden" name="start_date" value="<?=$start_date?>" />
			<input type="hidden" name="end_date" value="<?=$end_date?>" />
			<?php
				$url_param = array();
				foreach ($_GET as $key => $value) $url_param[] = $key.'='.$value;
			?>
			<a href="?<?php echo implode('&', $url_param) ?>&reset_room_cart=true" class="room-reservation-button">Reset Cart</a>

			<button class="room-reservation-button" name="checkoutroom" type="submit">Check-out</button>
		</form>

	</div>
</div>

<table class="room-reservation-avalable-rooms-table">
	<thead>
		<tr>
			<th style="width: 10%;">Room type</th>
			<th style="width: 25%;">Description</th>
			<th style="width: 10%;">Rate</th>
			<th style="width: 10%;">Qty</th>
			<th style="width: 35%;">Photos</th>
			<th style="width: 10%;"></th>
		</tr>
	</thead>
	<tbody>
	<?php if (count($data) > 0) : ?>
		<?php foreach ($data as $item) : ?>
			<tr id="room_cart<?=$item['id']?>">
				<td><?=$item['room_type'] ?></td>
				<td><?=$item['description'] ?></td>
				<td><?=$item['rate'] ?></td>
				<td class="available_qty"><?=$item['remaining'] ?></td>
				<td>
					<?php $images = explode(',', $item['image']); ?>

					<?php foreach ($images as $img) : ?>
						<img src="<?=$img?>" alt="<?=$item['description']?>" style="height: 50px;" />
					<?php endforeach; ?>
				</td>
				<td>
					<?php
						$item['start_date'] = $start_date;
						$item['end_date'] = $end_date;
					?>
					<button class="room-reservation-button btn-room-cart" data='<?=json_encode($item)?>'>Add to Cart</button>
				</td>
			</tr>
		<?php endforeach; ?>
	<?php else : ?>
		<tr>
			<td colspan="6">No Record found.</td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>