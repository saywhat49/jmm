<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class JMMViewJMM extends JView
{

	
	function display($tmpl=null) 
	{
                
        JFactory::getApplication()->redirect('index.php?option=com_jmm&view=tables');
		parent::display($tmpl);
	}
	
}
