<?php
/**
 * @package     Automatic Meta Description on Save
 * @subpackage  plg_content_autometa
 * @version     1.2.1
 * @author      Angus Fox
 * @copyright   (C) 2025 - Multizone Limited
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Ezone\Plugin\Content\AutoMeta\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

/**
 * Shared utility class for generating meta descriptions
 * Used by both the plugin (on save) and component (bulk regeneration)
 *
 * @since  1.2.1
 */
class MetaDescriptionHelper
{
    /**
     * Generate a meta description from title and introtext
     *
     * @param   string        $title       The article title
     * @param   string        $introtext   The article intro text (may contain HTML)
     * @param   Registry|null $params      Plugin parameters (optional, will load from plugin if null)
     *
     * @return  string  Meta description
     *
     * @since   1.2.1
     */
    public static function generate(string $title, string $introtext, ?Registry $params = null): string
    {
        // Get plugin parameters if not provided
        if ($params === null) {
            $plugin = PluginHelper::getPlugin('content', 'autometa');
            $params = new Registry($plugin ? $plugin->params : '{}');
        }

        // Get plugin parameters
        $maxLength = (int) $params->get('max_length', 160);
        $separator = $params->get('separator', ' - ');
        $useTitle = (bool) $params->get('use_title', 1);
        $useContent = (bool) $params->get('use_content', 1);

        $metaDesc = '';

        // Build meta description based on settings
        if ($useTitle) {
            $metaDesc = trim($title);
        }

        if ($useContent) {
            $summary = self::extractText($introtext);

            if (!empty($summary)) {
                if (!empty($metaDesc)) {
                    $metaDesc .= $separator;
                }
                $metaDesc .= $summary;
            }
        }

        // Fallback if both options are disabled or result is empty
        if (empty($metaDesc)) {
            $metaDesc = trim($title);
        }

        return self::truncateAtWordBoundary($metaDesc, $maxLength);
    }

    /**
     * Extract clean text from HTML content
     *
     * @param   string  $text  Text containing HTML
     *
     * @return  string  Clean text
     *
     * @since   1.2.1
     */
    public static function extractText(string $text): string
    {
        // Remove HTML tags, decode special characters, and clean up spaces
        $cleanText = trim(strip_tags(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8')));

        // Collapse multiple spaces and newlines
        $cleanText = preg_replace('/\s+/', ' ', $cleanText);

        return $cleanText;
    }

    /**
     * Truncate text at word boundary
     *
     * @param   string   $text      Text to truncate
     * @param   integer  $maxLength Maximum length
     *
     * @return  string  Truncated text
     *
     * @since   1.2.1
     */
    public static function truncateAtWordBoundary(string $text, int $maxLength): string
    {
        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }

        // Cut at max length
        $truncated = mb_substr($text, 0, $maxLength);

        // Find last space to avoid cutting mid-word
        $lastSpace = mb_strrpos($truncated, ' ');

        if ($lastSpace !== false && $lastSpace > ($maxLength * 0.8)) {
            $truncated = mb_substr($truncated, 0, $lastSpace);
        }

        // Add ellipsis if text was truncated
        $truncated = rtrim($truncated, '.,;:!?') . '...';

        return $truncated;
    }

    /**
     * Get plugin parameters
     *
     * @return  Registry  Plugin parameters
     *
     * @since   1.2.1
     */
    public static function getPluginParams(): Registry
    {
        $plugin = PluginHelper::getPlugin('content', 'autometa');
        return new Registry($plugin ? $plugin->params : '{}');
    }
}
