<?php
/**
 * @package     Automatic Meta Description on Save
 * @subpackage  plg_content_autometa
 * @version     1.2.1
 * @author      Angus Fox
 * @copyright   (C) 2025 - Multizone Limited
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Ezone\Plugin\Content\AutoMeta\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Ezone\Plugin\Content\AutoMeta\Helper\MetaDescriptionHelper;

/**
 * Automatic Meta Description Plugin
 *
 * @since  1.0.0
 */
class AutoMeta extends CMSPlugin
{
    /**
     * Load plugin language files automatically
     *
     * @var    boolean
     * @since  1.0.0
     */
    protected $autoloadLanguage = true;

    /**
     * Constructor
     *
     * @param   object  $subject  The object to observe
     * @param   array   $config   An optional associative array of configuration settings
     *
     * @since   1.0.0
     */
    public function __construct(&$subject, $config = [])
    {
        parent::__construct($subject, $config);

        // Initialize file logging
        Log::addLogger(
            [
                'text_file' => 'plg_autometa.php',
                'text_entry_format' => '{DATE} {TIME} {PRIORITY} {MESSAGE}'
            ],
            Log::ALL,
            ['plg_autometa']
        );

        Log::add('AutoMeta plugin constructor called', Log::INFO, 'plg_autometa');

        // Show message only if debug mode is enabled
        if ($this->params->get('debug_mode', 0)) {
            try {
                Factory::getApplication()->enqueueMessage('AutoMeta plugin constructor called', 'info');
            } catch (\Exception $e) {
                Log::add('Could not enqueue message: ' . $e->getMessage(), Log::WARNING, 'plg_autometa');
            }
        }
    }

    /**
     * Event triggered before saving content
     *
     * @param   string   $context  The context of the content being passed to the plugin
     * @param   object   $article  The article object
     * @param   boolean  $isNew    If the article is new
     *
     * @return  boolean  True on success
     *
     * @since   1.0.0
     */
    public function onContentBeforeSave($context, $article, $isNew)
    {
        // Check if debug mode is enabled
        $debugMode = (bool) $this->params->get('debug_mode', 0);

        // Log that event was triggered (always log to file)
        $message = 'AutoMeta plugin triggered for context: ' . $context . ' | isNew: ' . ($isNew ? 'true' : 'false');
        Log::add($message, Log::INFO, 'plg_autometa');

        // Only show UI message if debug mode is enabled
        if ($debugMode) {
            Factory::getApplication()->enqueueMessage($message, 'info');
        }

        // Log article details for debugging
        Log::add('Article title: ' . ($article->title ?? 'N/A'), Log::INFO, 'plg_autometa');
        Log::add('Current metadesc: ' . ($article->metadesc ?? 'empty'), Log::INFO, 'plg_autometa');

        // Ensure it's a Joomla article
        if ($context !== 'com_content.article') {
            Log::add('Context mismatch - skipping. Expected: com_content.article, Got: ' . $context, Log::INFO, 'plg_autometa');

            if ($debugMode) {
                Factory::getApplication()->enqueueMessage('AutoMeta: Skipping (context: ' . $context . ')', 'warning');
            }
            return true;
        }

        // Check if we should overwrite existing descriptions
        $overwriteExisting = (bool) $this->params->get('overwrite_existing', 0);
        Log::add('Overwrite existing: ' . ($overwriteExisting ? 'yes' : 'no'), Log::INFO, 'plg_autometa');

        // Skip if a meta description already exists and we're not overwriting
        if (!empty($article->metadesc) && !$overwriteExisting) {
            $skipMessage = 'AutoMeta: Skipping - meta description already exists';
            Log::add($skipMessage, Log::INFO, 'plg_autometa');

            if ($debugMode) {
                Factory::getApplication()->enqueueMessage($skipMessage, 'info');
            }
            return true;
        }

        // Generate a meta description using the shared helper
        $article->metadesc = MetaDescriptionHelper::generate($article->title, $article->introtext, $this->params);

        $successMessage = 'AutoMeta: Generated description: ' . substr($article->metadesc, 0, 50) . '...';
        Log::add($successMessage, Log::INFO, 'plg_autometa');

        if ($debugMode) {
            Factory::getApplication()->enqueueMessage($successMessage, 'success');
        }

        return true;
    }
}
