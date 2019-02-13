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
								<input type="text" name="room_type" value="<?php echo $input->room_type ?>" />
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