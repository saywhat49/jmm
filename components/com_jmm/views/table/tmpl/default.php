<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

$rows = $this->rows;
$cols = $this->columns;
$params = $this->params;
$document = Factory::getDocument();

// Définir les constantes au lieu de les utiliser directement
$ds = DIRECTORY_SEPARATOR;

$themeFile = JPATH_COMPONENT . $ds . 'templates' . $ds . $this->theme . $ds . 'index.php';
$themeCSSFile = JPATH_COMPONENT . $ds . 'templates' . $ds . $this->theme . $ds . 'css' . $ds . 'default.css';
$themeJSFile = JPATH_COMPONENT . $ds . 'templates' . $ds . $this->theme . $ds . 'js' . $ds . 'custom.js';

if (file_exists($themeCSSFile)) {
	$document->addStyleSheet($this->themeBaseURL . '/css/default.css');
}

if (file_exists($themeJSFile)) {
	$document->addScript($this->themeBaseURL . '/js/custom.js');
}

if (file_exists($themeFile)) {
	require_once($themeFile);
} else {
	$themeFile = JPATH_COMPONENT . $ds . 'templates' . $ds . 'default' . $ds . 'index.php';	
	require_once($themeFile);
}

// Display pagination if enabled
if ($this->defaultPagination) {
?>
<form action="index.php?option=com_jmm&amp;view=table" method="post" id="adminForm" name="adminForm">
    <div class="jmm-pagination">
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
    <input type="hidden" name="option" value="com_jmm" />
    <input type="hidden" name="view" value="table" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
<?php
}
?>
