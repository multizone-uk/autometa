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
use Joomla\CMS\Log\Log;

class AutometaModelAutometa extends BaseDatabaseModel
{
    /**
     * Batch size for processing articles
     */
    const BATCH_SIZE = 100;

    public function regenerateAllMetaDescriptions()
    {
        $db = Factory::getDbo();
        $totalProcessed = 0;
        $offset = 0;
        $errors = 0;

        try {
            // Get total count
            $countQuery = $db->getQuery(true)
                ->select('COUNT(*)')
                ->from('#__content');
            $db->setQuery($countQuery);
            $total = (int) $db->loadResult();

            // Process in batches
            while ($offset < $total) {
                $query = $db->getQuery(true)
                    ->select(['id', 'title', 'introtext'])
                    ->from('#__content')
                    ->setLimit(self::BATCH_SIZE, $offset);
                $db->setQuery($query);
                $articles = $db->loadObjectList();

                if (empty($articles)) {
                    break;
                }

                // Loop through batch and update meta descriptions
                foreach ($articles as $article) {
                    try {
                        $metaDesc = $this->generateMetaDescription($article->title, $article->introtext);

                        $updateQuery = $db->getQuery(true)
                            ->update('#__content')
                            ->set($db->quoteName('metadesc') . ' = ' . $db->quote($metaDesc))
                            ->where($db->quoteName('id') . ' = ' . (int) $article->id);
                        $db->setQuery($updateQuery);
                        $db->execute();

                        $totalProcessed++;
                    } catch (\Exception $e) {
                        $errors++;
                        Log::add('Failed to update article ' . $article->id . ': ' . $e->getMessage(), Log::WARNING, 'com_autometa');
                    }
                }

                $offset += self::BATCH_SIZE;
            }

            Log::add("Processed {$totalProcessed} articles with {$errors} errors", Log::INFO, 'com_autometa');

            return [
                'processed' => $totalProcessed,
                'errors' => $errors,
                'total' => $total
            ];

        } catch (\Exception $e) {
            Log::add('Failed to regenerate meta descriptions: ' . $e->getMessage(), Log::ERROR, 'com_autometa');
            throw $e;
        }
    }

    /**
     * Generate a meta description from title and introtext
     *
     * @param   string  $title      The article title
     * @param   string  $introtext  The article intro text (may contain HTML)
     *
     * @return  string  Meta description limited to 160 characters
     */
    private function generateMetaDescription($title, $introtext)
    {
        $summary = $this->extractText($introtext);
        $metaDesc = trim($title);

        if (!empty($summary)) {
            $metaDesc .= ' - ' . $summary;
        }

        return $this->truncateAtWordBoundary($metaDesc, 160);
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
