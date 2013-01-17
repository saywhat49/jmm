<?php
defined('_JEXEC') or die('Restricted access');
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
<form action="index.php?option=com_jmm&amp;view=table" method="post" id="adminForm" name="adminForm">
<div class="jmm-pagination">
	<?php echo $this->pagination->getPagesLinks(); ?>
</div>
<input type="hidden" name="option" value="com_jmm" />
<input type="hidden" name="view" value="table" />
<?php echo JHtml::_('form.token');?>
</form>
<?php
}
?>