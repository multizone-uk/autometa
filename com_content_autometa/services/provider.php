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

// Manually load classes before autoloader is ready
// Load plugin helper first (Model depends on it)
$pluginHelperPath = JPATH_PLUGINS . '/content/autometa/src/Helper/MetaDescriptionHelper.php';
if (file_exists($pluginHelperPath)) {
    require_once $pluginHelperPath;
}

require_once __DIR__ . '/../src/Extension/AutoMetaComponent.php';
require_once __DIR__ . '/../src/Controller/DisplayController.php';
require_once __DIR__ . '/../src/Model/AutometaModel.php';
require_once __DIR__ . '/../src/View/Autometa/HtmlView.php';

use Ezone\Component\AutoMeta\Administrator\Extension\AutoMetaComponent;

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
        // Register namespace with Joomla's autoloader
        \JLoader::registerNamespace('Ezone\\Component\\AutoMeta\\Administrator', JPATH_ADMINISTRATOR . '/components/com_autometa/src', false, false, 'psr4');

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
