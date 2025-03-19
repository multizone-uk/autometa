<?php
/*
 * @package Regenerate Meta Descriptions
 * @version 1.1.28 autometa.php
 * @author Angus Fox
 * @copyright (C) 2025 - Multizone Limited
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Factory;

class AutometaModelAutometa extends BaseDatabaseModel
{
    public function regenerateAllMetaDescriptions()
    {
        $db = Factory::getDbo();

        // Select all articles
        $query = $db->getQuery(true)
            ->select(['id', 'title', 'introtext'])
            ->from('#__content');
        $db->setQuery($query);
        $articles = $db->loadObjectList();

        // Loop through each article and update meta description
        foreach ($articles as $article) {
            $metaDesc = $this->generateMetaDescription($article->title, $article->introtext);

            // Update query
            $updateQuery = $db->getQuery(true)
                ->update('#__content')
                ->set($db->quoteName('metadesc') . ' = ' . $db->quote($metaDesc))
                ->where($db->quoteName('id') . ' = ' . (int) $article->id);
            $db->setQuery($updateQuery);
            $db->execute();
        }
    }

    // Generate a meta description from title + introtext
    private function generateMetaDescription($title, $introtext)
    {
        // Remove HTML tags and trim
        $cleanIntro = strip_tags($introtext);
        $cleanIntro = trim($cleanIntro);

        // Limit intro text to 140 characters
        $summary = mb_substr($cleanIntro, 0, 140);

        return $title . ' - ' . $summary;
    }
}
