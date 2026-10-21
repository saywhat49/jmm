<?php
/**
 * @package		JMM
 * @license		GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
?>
<table class="bordered">
	<thead>
		<tr>
			<?php
			foreach($cols as $col){
				echo '<th>' . htmlspecialchars((string) $col, ENT_QUOTES, 'UTF-8') . '</th>';
			}
			?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($rows as $i => $row): ?>

		<tr class="row<?php echo $i % 2?>">

			<?php
			foreach ($row as $col => $val) {
				echo '<td>' . htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') . '</td>';
			}
			?>
		</tr>

		<?php endforeach ?>
	</tbody>
</table>