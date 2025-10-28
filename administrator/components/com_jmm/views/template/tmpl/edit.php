<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;

// Charger les assets JavaScript pour Joomla 5
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useScript('form.validate');
$wa->useScript('keepalive');
$wa->useScript('core');

// Déterminer le dossier des templates
if (isset($this->item->title)) {
	$templateFolder = '/components/com_jmm/templates/' . $this->item->title . '/images';
} else {
	$templateFolder = '/images';
}
?>
<form action="<?php echo Route::_('index.php?option=com_jmm&view=template&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
    <div class="row">
        <div class="col-md-8">
            <fieldset class="adminform">
                <ul class="adminformList">
                    <li>
                        <?php echo $this->form->getLabel('id'); ?>
                        <?php echo $this->form->getInput('id'); ?>
                    </li>
                    <li>
                        <?php echo $this->form->getLabel('title'); ?>
                        <?php echo $this->form->getInput('title'); ?>
                        <br><br>
                    </li>
                    <li>
                        <?php echo $this->form->getLabel('php'); ?>
                        <?php echo $this->form->getInput('php'); ?>
                        <br><br>
                    </li>
                    <li>
                        <?php echo $this->form->getLabel('css'); ?>
                        <?php echo $this->form->getInput('css'); ?>
                        <br><br>
                    </li>
                    <li>
                        <?php echo $this->form->getLabel('js'); ?>
                        <?php echo $this->form->getInput('js'); ?>
                    </li>
                </ul>
            </fieldset>
        </div>
        <div class="col-md-4">
            <fieldset class="adminform">
                <iframe width="100%" height="700" frameborder="0" scrolling="no" src="index.php?option=com_media&amp;view=media&amp;tmpl=component&amp;path=<?php echo $templateFolder; ?>"></iframe>
            </fieldset>
        </div>
    </div>
    
    <input type="hidden" name="task" value="" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
