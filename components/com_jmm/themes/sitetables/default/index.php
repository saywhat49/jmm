<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
?>
<table class="bordered">
	<thead>
		<tr>
			<?php
			foreach ($cols as $col) {
				echo '<th>' . htmlspecialchars($col, ENT_QUOTES, 'UTF-8') . '</th>';
			}
			?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($rows as $i => $row): ?>
		<tr class="row<?php echo $i % 2 ?>">
			<?php
			foreach ($row as $col => $val) {
				echo '<td>' . htmlspecialchars($val, ENT_QUOTES, 'UTF-8') . '</td>';
			}
			?>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
