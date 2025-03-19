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
    public function onContentBeforeSave($context, $article, $isNew)
    {
        // Ensure it's a Joomla article
        if ($context !== 'com_content.article') {
            return true;
        }

        // Skip if a meta description already exists
        if (!empty($article->metadesc)) {
            return true;
        }

        // Generate a meta description using the title and introtext
        $article->metadesc = $this->generateMetaDescription($article->title, $article->introtext);

        return true;
    }

    private function generateMetaDescription($title, $introtext)
    {
        $summary = $this->extractText($introtext);
        $metaDesc = trim($title);

        if (!empty($summary)) {
            $metaDesc .= ' - ' . $summary;
        }

        return mb_substr($metaDesc, 0, 140); // Trim to 140 characters which ought to be enough
    }

    private function extractText($text)
    {
        // Remove HTML tags, decode special characters, and clean up spaces
        $cleanText = trim(strip_tags(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8')));

        return $cleanText;
    }
}
