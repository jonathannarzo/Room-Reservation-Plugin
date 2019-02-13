<div class="wrap">
		
	<h2><?php echo $title ?></h2>
	<form method="post">
		<?php echo $record_id ?>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div class="form-wrap">
					<table class="form-table">
						<tr class="form-field">
							<th scope="row">
								<label for="booking_page">Booking Page :</label>
							</th>
							<td>
								<select name="booking_page" id="booking_page" required>
									<option value="">-Select a page-</option>
								<?php foreach ($all_pages as $item) : ?>
									<option value="<?=$item->ID?>" <?=($item->ID == $input->booking_page) ? 'selected': ''?>><?=$item->post_title?></option>
								<?php endforeach; ?>
								</select>
								<p>This page must contain the <b>[room_reservation_plugin]</b> shortcode</p>
							</td>
						</tr>
						<tr class="form-field">
							<th scope="row">
								<label for="paypal_email">Paypal Email :</label>
							</th>
							<td>
								<input id="paypal_email" type="text" name="paypal_email" value="<?php echo $input->paypal_email ?>" />
							</td>
						</tr>
						<tr class="form-field">
							<th scope="row"></th>
							<td>
								<input type="submit" class="button button-primary" name="<?php echo $type ?>" value="<?php echo $button_title ?>" />
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</form>
	
</div>