<table class="bordered">
	<thead>
		<tr>
			<?php
			foreach($cols as $col){
				echo '<th>'.$col.'</th>';
			}
			?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($rows as $i => $row): ?>

		<tr class="row<?php echo $i % 2?>">

			<?php
			foreach ($row as $col => $val) {
				echo '<td>' . $val . '</td>';
			}
			?>
		</tr>

		<?php endforeach ?>
	</tbody>
</table>

