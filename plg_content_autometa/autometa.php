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

// Bootstrap logging
$bootstrapLogFile = JPATH_ADMINISTRATOR . '/logs/plg_autometa_bootstrap.log';
@file_put_contents($bootstrapLogFile, '[' . date('Y-m-d H:i:s') . '] autometa.php loaded' . PHP_EOL, FILE_APPEND);

// Manually load the namespaced class before PSR-4 autoloader is ready
require_once __DIR__ . '/src/Extension/AutoMeta.php';

use Ezone\Plugin\Content\AutoMeta\Extension\AutoMeta;
use Joomla\CMS\Plugin\CMSPlugin;

// Create a simple class alias in the global namespace for Joomla to find
// This bridges the gap between Joomla's expectation and our namespaced class
class PlgContentAutometa extends AutoMeta
{
    // Inherit everything from the namespaced AutoMeta class
    // Log when this class is instantiated
    public function __construct(&$subject, $config = [])
    {
        parent::__construct($subject, $config);

        $logFile = JPATH_ADMINISTRATOR . '/logs/plg_autometa_bootstrap.log';
        @file_put_contents($logFile, '[' . date('Y-m-d H:i:s') . '] PlgContentAutometa instantiated!' . PHP_EOL, FILE_APPEND);
    }
}
