<?php
	if($this->items && count($this->items)>0){

?>
<hr>
<table class="bordered">
	<thead>
		<tr>
			<?php
			foreach($this->items[0] as $col=>$val){
				echo '<th>'.$col.'</th>';
			}
			?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($this->items as $i => $item): ?>

		<tr class="row<?php echo $i % 2?>">

			<?php
			foreach ($this->items[$i] as $col => $val) {
				echo '<td>' . $val . '</td>';
			}
			?>
		</tr>

		<?php endforeach ?>
	</tbody>
</table>
<?php
}
?>