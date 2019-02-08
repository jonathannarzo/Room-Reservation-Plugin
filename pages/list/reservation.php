<div class="wrap">
	<h2>Room Reservations</h2>
	<form action="" method="get">
		<?php
			$this->records_obj->search_box( __( 'Search' ), 'example' );
			foreach ($_GET as $key => $value)
			{
				if( 's' !== $key )
				{
					echo "<input type='hidden' name='$key' value='$value' />";
				}
			}
		?>
	</form>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
					<form method="post">
						<?php
							$this->records_obj->prepare_items();
							$this->records_obj->display();
						?>
					</form>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>
</div>