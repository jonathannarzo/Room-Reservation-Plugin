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
								<label for="stats_excel">Room Type :</label>
							</th>
							<td>
								<select name="room_type_id" id="room_type_id" required>
									<option value=""></option>
									<?php
										foreach ($room_types as $item)
										{
											$selected = ($item->id == $input->room_type_id) ? 'selected' : '';
											echo "<option value='{$item->id}' description='{$item->description}' {$selected}>{$item->room_type}</option>";
										}
									?>
								</select>
							</td>
						</tr>
						<tr class="form-field">
							<th scope="row">
								<label for="stats_excel">Description :</label>
							</th>
							<td>
								<?php
									$content = get_option('description', $input->description);
									$settings = array(
										'textarea_rows' => 10,
										'media_buttons' => FALSE,
									);
									wp_editor($content, 'description', $settings);
								?>
							</td>
						</tr>
						<tr class="form-field">
							<th scope="row">
								<label for="stats_excel">Quantity :</label>
							</th>
							<td>
								<input type="number" name="qty" value="<?php echo $input->qty ?>" required />
								<p>Number of rooms</p>
							</td>
						</tr>
						<tr class="form-field">
							<th scope="row">
								<label for="stats_excel">Rate :</label>
							</th>
							<td>
								<input type="text" name="rate" value="<?php echo $input->rate ?>" required />
							</td>
						</tr>
						<tr class="form-field">
							<th scope="row">
								<label for="stats_excel">Image :</label>
							</th>
							<td>
								<input type="hidden" id="images-selected" name="image" value="<?php echo $input->image ?>" />
								<button id="upload-button">Choose Image</button>
								<p></p>
								<div id="twbs_photos_to_upload"></div>
								<div id="twbs_count_selection"></div>
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