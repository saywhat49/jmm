<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

/**
 * Table view class
 */
class JMMViewTable extends HtmlView
{
	/**
	 * The table rows
	 *
	 * @var    array
	 */
	protected $rows;
	
	/**
	 * The table columns
	 *
	 * @var    array
	 */
	protected $columns;
	
	/**
	 * The pagination object
	 *
	 * @var    object
	 */
	protected $pagination;
	
	/**
	 * The theme name
	 *
	 * @var    string
	 */
	protected $theme;
	
	/**
	 * The component parameters
	 *
	 * @var    object
	 */
	protected $params;
	
	/**
	 * The current theme URL
	 *
	 * @var    string
	 */
	protected $curThemeURL;
	
	/**
	 * The site table details
	 *
	 * @var    object
	 */
	protected $siteTableDetails;
	
	/**
	 * Whether to use default pagination
	 *
	 * @var    boolean
	 */
	protected $defaultPagination;
	
	/**
	 * The theme base URL
	 *
	 * @var    string
	 */
	protected $themeBaseURL;
	
	/**
	 * Display the view
	 *
	 * @param   string  $tmpl  The template file to use
	 *
	 * @return  void
	 */
	function display($tmpl = null) 
	{	
		$app = Factory::getApplication();
		
		$this->rows = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->columns = $this->get('Columns');
		$this->siteTableDetails = $this->get('SiteTableDetails');	
		$this->defaultPagination = true;	
		$this->params = $app->getParams();
		$this->theme = $this->params->get('theme');
		
		$document = Factory::getDocument();
		$document->addStyleSheet(Uri::root() . 'media/com_jmm/css/jmm.css');
		$document->addScript(Uri::root() . 'media/com_jmm/js/jquery-1.7.2.min.js');		
		
		$this->curThemeURL = Uri::root() . 'components/com_jmm/templates/' . $this->theme;
		$this->themeBaseURL = Uri::root() . 'components/com_jmm/templates/' . $this->theme;
		
		parent::display($tmpl);
	}
}
