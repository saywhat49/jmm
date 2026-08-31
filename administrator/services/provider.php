<?php
defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Saywhat49\Component\Jmm\Administrator\Extension\JmmComponent;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class () implements ServiceProviderInterface {
    public function register(Container $container): void
    {
        $container->registerServiceProvider(new MVCFactory('\\Saywhat49\\Component\\Jmm'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('\\Saywhat49\\Component\\Jmm'));
        $container->registerServiceProvider(new RouterFactory('\\Saywhat49\\Component\\Jmm'));

        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                $component = new JmmComponent($container->get(ComponentDispatcherFactoryInterface::class));
                $component->setMVCFactory($container->get(MVCFactoryInterface::class));

                return $component;
            }
        );
    }
};