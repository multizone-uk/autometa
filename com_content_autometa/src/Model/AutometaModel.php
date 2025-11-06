<?php
/**
 * @package     Regenerate Meta Descriptions
 * @subpackage  com_autometa
 * @version     1.2.1
 * @author      Angus Fox
 * @copyright   (C) 2025 - Multizone Limited
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Ezone\Component\AutoMeta\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Ezone\Plugin\Content\AutoMeta\Helper\MetaDescriptionHelper;

/**
 * AutoMeta Model
 *
 * @since  1.0.0
 */
class AutometaModel extends BaseDatabaseModel
{
    /**
     * Batch size for processing articles
     *
     * @var    integer
     * @since  1.0.0
     */
    const BATCH_SIZE = 100;

    /**
     * Regenerate meta descriptions with optional filtering
     *
     * @param   boolean  $emptyOnly  Only process articles with empty meta descriptions
     *
     * @return  array  Array with processed, errors, and total counts
     *
     * @since   1.2.1
     * @throws  \Exception
     */
    public function regenerateMetaDescriptions(bool $emptyOnly = false): array
    {
        $db = $this->getDatabase();
        $totalProcessed = 0;
        $offset = 0;
        $errors = 0;

        try {
            // Build base query
            $countQuery = $db->getQuery(true)
                ->select('COUNT(*)')
                ->from($db->quoteName('#__content'));

            // Add filter for empty descriptions if requested
            if ($emptyOnly) {
                $countQuery->where(
                    '(' . $db->quoteName('metadesc') . ' = ' . $db->quote('') .
                    ' OR ' . $db->quoteName('metadesc') . ' IS NULL)'
                );
            }

            $db->setQuery($countQuery);
            $total = (int) $db->loadResult();

            // Process in batches
            while ($offset < $total) {
                $query = $db->getQuery(true)
                    ->select([
                        $db->quoteName('id'),
                        $db->quoteName('title'),
                        $db->quoteName('introtext'),
                        $db->quoteName('metadesc')
                    ])
                    ->from($db->quoteName('#__content'))
                    ->setLimit(self::BATCH_SIZE, $offset);

                // Add same filter
                if ($emptyOnly) {
                    $query->where(
                        '(' . $db->quoteName('metadesc') . ' = ' . $db->quote('') .
                        ' OR ' . $db->quoteName('metadesc') . ' IS NULL)'
                    );
                }

                $db->setQuery($query);
                $articles = $db->loadObjectList();

                if (empty($articles)) {
                    break;
                }

                // Loop through batch and update meta descriptions
                foreach ($articles as $article) {
                    try {
                        // Use shared helper that respects plugin settings
                        $metaDesc = MetaDescriptionHelper::generate($article->title, $article->introtext);

                        $updateQuery = $db->getQuery(true)
                            ->update($db->quoteName('#__content'))
                            ->set($db->quoteName('metadesc') . ' = ' . $db->quote($metaDesc))
                            ->where($db->quoteName('id') . ' = ' . (int) $article->id);
                        $db->setQuery($updateQuery);
                        $db->execute();

                        $totalProcessed++;
                    } catch (\Exception $e) {
                        $errors++;
                        Log::add(
                            'Failed to update article ' . $article->id . ': ' . $e->getMessage(),
                            Log::WARNING,
                            'com_autometa'
                        );
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
     * Get statistics about meta descriptions
     *
     * @return  array  Statistics array
     *
     * @since   1.2.1
     */
    public function getStatistics(): array
    {
        $db = $this->getDatabase();

        try {
            // Total articles
            $totalQuery = $db->getQuery(true)
                ->select('COUNT(*)')
                ->from($db->quoteName('#__content'));
            $db->setQuery($totalQuery);
            $total = (int) $db->loadResult();

            // Articles with meta descriptions
            $withMetaQuery = $db->getQuery(true)
                ->select('COUNT(*)')
                ->from($db->quoteName('#__content'))
                ->where($db->quoteName('metadesc') . ' != ' . $db->quote(''))
                ->where($db->quoteName('metadesc') . ' IS NOT NULL');
            $db->setQuery($withMetaQuery);
            $withMeta = (int) $db->loadResult();

            $withoutMeta = $total - $withMeta;
            $percentage = $total > 0 ? round(($withMeta / $total) * 100, 1) : 0;

            return [
                'total' => $total,
                'with_meta' => $withMeta,
                'without_meta' => $withoutMeta,
                'percentage' => $percentage
            ];

        } catch (\Exception $e) {
            Log::add('Failed to get statistics: ' . $e->getMessage(), Log::ERROR, 'com_autometa');
            return [
                'total' => 0,
                'with_meta' => 0,
                'without_meta' => 0,
                'percentage' => 0
            ];
        }
    }

    /**
     * Legacy method for backwards compatibility
     *
     * @return  array  Array with processed, errors, and total counts
     *
     * @since   1.0.0
     * @deprecated  1.2.1  Use regenerateMetaDescriptions() instead
     */
    public function regenerateAllMetaDescriptions(): array
    {
        return $this->regenerateMetaDescriptions(false);
    }
}
