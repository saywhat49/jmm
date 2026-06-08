<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jmm
 */

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('bootstrap.tooltip');
?>

<div class="com-jmm j-main-container">
    <h1>Joomla MySQL Manager</h1>

    <p>
        Structure Joomla 5 PSR-4 active.
        Cette vue est une base moderne à remplacer progressivement par la logique historique de JMM.
    </p>

    <ul>
        <li><a href="index.php?option=com_jmm&amp;view=databases">Bases de données</a></li>
        <li><a href="index.php?option=com_jmm&amp;view=tables">Tables</a></li>
        <li><a href="index.php?option=com_jmm&amp;view=sql">Requête SQL</a></li>
    </ul>
</div>
