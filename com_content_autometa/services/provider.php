<?php
/**
 * @package     Regenerate Meta Descriptions
 * @subpackage  com_autometa
 * @version     1.2.1
 * @author      Angus Fox
 * @copyright   (C) 2025 - Multizone Limited
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Ezone\Component\AutoMeta\Extension\AutoMetaComponent;

return new class () implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   1.2.0
     */
    public function register(Container $container): void
    {
        $container->registerServiceProvider(new MVCFactory('\\Ezone\\Component\\AutoMeta'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('\\Ezone\\Component\\AutoMeta'));

        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                $component = new AutoMetaComponent($container->get(ComponentDispatcherFactoryInterface::class));
                $component->setMVCFactory($container->get(MVCFactoryInterface::class));

                return $component;
            }
        );
    }
};
