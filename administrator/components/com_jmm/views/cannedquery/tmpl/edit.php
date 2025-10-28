<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;

// Charger les assets JavaScript pour Joomla 5
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useScript('form.validate');
$wa->useScript('keepalive');
?>
<form action="<?php echo Route::_('index.php?option=com_jmm&view=cannedquery&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
<div class="width-60 fltlft">
	<fieldset class="adminform">
		<ul class="adminformList">
		<?php foreach($this->form->getFieldset() as $field):?>
			<li>
				<?php echo $field->label;?>
				<?php echo $field->input;?>
			</li>
		<?php endforeach ?>
		</ul>
	</fieldset>
</div>
<input type="hidden" name="task" value="" />
<?php echo HTMLHelper::_('form.token');?>
</form>
