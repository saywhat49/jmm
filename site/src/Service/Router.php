<?php
namespace Saywhat49\Component\Jmm\Site\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\StandardRules;

class Router extends RouterView
{
    public function __construct($app = null, $menu = null)
    {
        $table = new RouterViewConfiguration('table');
        $table->setKey('site_table_id');
        $this->registerView($table);

        parent::__construct($app, $menu);
        $this->attachRule(new StandardRules($this));
    }
}