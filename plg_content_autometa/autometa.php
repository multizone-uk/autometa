<?php
/**
 * @package     Automatic Meta Description on Save
 * @subpackage  plg_content_autometa
 * @version     1.2.0
 * @author      Angus Fox
 * @copyright   (C) 2025 - Multizone Limited
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Log\Log;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;

// Bootstrap logging - verify this file is being loaded by Joomla
$bootstrapLogFile = JPATH_ADMINISTRATOR . '/logs/plg_autometa_bootstrap.log';
$bootstrapMessage = '[' . date('Y-m-d H:i:s') . '] autometa.php file loaded by Joomla' . PHP_EOL;
@file_put_contents($bootstrapLogFile, $bootstrapMessage, FILE_APPEND);

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
        // Initialize logging first
        Log::addLogger(
            [
                'text_file' => 'plg_autometa.php',
                'text_entry_format' => '{DATE} {TIME} {PRIORITY} {MESSAGE}'
            ],
            Log::ALL,
            ['plg_autometa']
        );

        Log::add('AutoMeta service provider register() called', Log::INFO, 'plg_autometa');

        $container->set(
            PluginInterface::class,
            function (Container $container) {
                Log::add('AutoMeta service provider factory function called', Log::INFO, 'plg_autometa');

                $dispatcher = $container->get(DispatcherInterface::class);
                Log::add('Dispatcher retrieved', Log::INFO, 'plg_autometa');

                $pluginData = PluginHelper::getPlugin('content', 'autometa');
                Log::add('Plugin data: ' . ($pluginData ? 'found' : 'NOT FOUND'), Log::INFO, 'plg_autometa');

                $plugin = new \Ezone\Plugin\Content\AutoMeta\Extension\AutoMeta(
                    $dispatcher,
                    (array) $pluginData
                );
                Log::add('Plugin instance created', Log::INFO, 'plg_autometa');

                $plugin->setApplication(Factory::getApplication());
                Log::add('Application set on plugin', Log::INFO, 'plg_autometa');

                return $plugin;
            }
        );

        Log::add('AutoMeta service provider registration complete', Log::INFO, 'plg_autometa');
    }
};
