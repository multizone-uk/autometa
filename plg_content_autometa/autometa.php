<?php
/*
 * @package Automatic Meta Description on Save
 * @version 1.1.28 autometa.php
 * @author Angus Fox
 * @copyright (C) 2025 - Multizone Limited
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/
defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;

class PlgContentAutoMeta extends CMSPlugin
{
    /**
     * Event triggered before saving content
     *
     * @param   string   $context  The context of the content being passed to the plugin
     * @param   object   $article  The article object
     * @param   boolean  $isNew    If the article is new
     *
     * @return  boolean  True on success
     */
    public function onContentBeforeSave($context, $article, $isNew)
    {
        // Ensure it's a Joomla article
        if ($context !== 'com_content.article') {
            return true;
        }

        // Check if we should overwrite existing descriptions
        $overwriteExisting = (bool) $this->params->get('overwrite_existing', 0);

        // Skip if a meta description already exists and we're not overwriting
        if (!empty($article->metadesc) && !$overwriteExisting) {
            return true;
        }

        // Generate a meta description using the title and introtext
        $article->metadesc = $this->generateMetaDescription($article->title, $article->introtext);

        return true;
    }

    /**
     * Generate a meta description from title and introtext
     *
     * @param   string  $title      The article title
     * @param   string  $introtext  The article intro text (may contain HTML)
     *
     * @return  string  Meta description
     */
    private function generateMetaDescription($title, $introtext)
    {
        // Get plugin parameters
        $maxLength = (int) $this->params->get('max_length', 160);
        $separator = $this->params->get('separator', ' - ');
        $useTitle = (bool) $this->params->get('use_title', 1);
        $useContent = (bool) $this->params->get('use_content', 1);

        $metaDesc = '';

        // Build meta description based on settings
        if ($useTitle) {
            $metaDesc = trim($title);
        }

        if ($useContent) {
            $summary = $this->extractText($introtext);

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

        return $this->truncateAtWordBoundary($metaDesc, $maxLength);
    }

    /**
     * Extract clean text from HTML content
     *
     * @param   string  $text  Text containing HTML
     *
     * @return  string  Clean text
     */
    private function extractText($text)
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
     */
    private function truncateAtWordBoundary($text, $maxLength)
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
}
