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
use Joomla\CMS\Component\ComponentHelper;
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
        $startTime = microtime(true);
        $logId = null;

        try {
            // Log regeneration start
            $logId = $this->logRegenerationStart($emptyOnly);
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
                        $db->quoteName('metadesc'),
                        $db->quoteName('hits')
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

                        // Log article stats
                        $this->logArticleStats($article->id, $article->title, $article->hits);

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

            // Log regeneration completion
            if ($logId) {
                $processingTime = round(microtime(true) - $startTime, 2);
                $this->logRegenerationComplete($logId, $total, $totalProcessed, $errors, $processingTime);
            }

            // Clean up old analytics data
            $this->cleanupOldAnalytics();

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

    /**
     * Log the start of a regeneration process
     *
     * @param   boolean  $emptyOnly  Whether empty-only option was used
     *
     * @return  integer  The log ID
     *
     * @since   2.1.0
     */
    protected function logRegenerationStart(bool $emptyOnly): int
    {
        $db = $this->getDatabase();
        $user = Factory::getApplication()->getIdentity();

        try {
            $query = $db->getQuery(true)
                ->insert($db->quoteName('#__autometa_regeneration_log'))
                ->columns([
                    $db->quoteName('user_id'),
                    $db->quoteName('created_at'),
                    $db->quoteName('empty_only')
                ])
                ->values(
                    (int) $user->id . ', ' .
                    $db->quote(Factory::getDate()->toSql()) . ', ' .
                    (int) $emptyOnly
                );

            $db->setQuery($query);
            $db->execute();

            return $db->insertid();
        } catch (\Exception $e) {
            Log::add('Failed to log regeneration start: ' . $e->getMessage(), Log::WARNING, 'com_autometa');
            return 0;
        }
    }

    /**
     * Log the completion of a regeneration process
     *
     * @param   integer  $logId           The log ID
     * @param   integer  $total           Total articles
     * @param   integer  $success         Successful updates
     * @param   integer  $failed          Failed updates
     * @param   float    $processingTime  Processing time in seconds
     *
     * @return  void
     *
     * @since   2.1.0
     */
    protected function logRegenerationComplete(int $logId, int $total, int $success, int $failed, float $processingTime): void
    {
        $db = $this->getDatabase();

        try {
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__autometa_regeneration_log'))
                ->set([
                    $db->quoteName('articles_total') . ' = ' . (int) $total,
                    $db->quoteName('articles_success') . ' = ' . (int) $success,
                    $db->quoteName('articles_failed') . ' = ' . (int) $failed,
                    $db->quoteName('processing_time') . ' = ' . (float) $processingTime
                ])
                ->where($db->quoteName('id') . ' = ' . (int) $logId);

            $db->setQuery($query);
            $db->execute();
        } catch (\Exception $e) {
            Log::add('Failed to log regeneration completion: ' . $e->getMessage(), Log::WARNING, 'com_autometa');
        }
    }

    /**
     * Log or update article statistics
     *
     * @param   integer  $articleId     Article ID
     * @param   string   $articleTitle  Article title
     * @param   integer  $hits          Current hit count
     *
     * @return  void
     *
     * @since   2.1.0
     */
    protected function logArticleStats(int $articleId, string $articleTitle, int $hits): void
    {
        $db = $this->getDatabase();
        $user = Factory::getApplication()->getIdentity();
        $now = Factory::getDate()->toSql();

        try {
            // Check if record exists
            $query = $db->getQuery(true)
                ->select($db->quoteName('id'))
                ->from($db->quoteName('#__autometa_article_stats'))
                ->where($db->quoteName('article_id') . ' = ' . (int) $articleId);

            $db->setQuery($query);
            $exists = $db->loadResult();

            if ($exists) {
                // Update existing record
                $query = $db->getQuery(true)
                    ->update($db->quoteName('#__autometa_article_stats'))
                    ->set([
                        $db->quoteName('regeneration_count') . ' = ' . $db->quoteName('regeneration_count') . ' + 1',
                        $db->quoteName('last_regenerated_at') . ' = ' . $db->quote($now),
                        $db->quoteName('last_regenerated_by') . ' = ' . (int) $user->id,
                        $db->quoteName('last_hits_count') . ' = ' . (int) $hits,
                        $db->quoteName('article_title') . ' = ' . $db->quote($articleTitle)
                    ])
                    ->where($db->quoteName('article_id') . ' = ' . (int) $articleId);
            } else {
                // Insert new record
                $query = $db->getQuery(true)
                    ->insert($db->quoteName('#__autometa_article_stats'))
                    ->columns([
                        $db->quoteName('article_id'),
                        $db->quoteName('article_title'),
                        $db->quoteName('regeneration_count'),
                        $db->quoteName('last_regenerated_at'),
                        $db->quoteName('last_regenerated_by'),
                        $db->quoteName('last_hits_count'),
                        $db->quoteName('first_regenerated_at')
                    ])
                    ->values(
                        (int) $articleId . ', ' .
                        $db->quote($articleTitle) . ', ' .
                        '1, ' .
                        $db->quote($now) . ', ' .
                        (int) $user->id . ', ' .
                        (int) $hits . ', ' .
                        $db->quote($now)
                    );
            }

            $db->setQuery($query);
            $db->execute();
        } catch (\Exception $e) {
            Log::add('Failed to log article stats: ' . $e->getMessage(), Log::WARNING, 'com_autometa');
        }
    }

    /**
     * Get recent regeneration history
     *
     * @param   integer  $limit  Number of records to return
     *
     * @return  array  Array of regeneration log entries
     *
     * @since   2.1.0
     */
    public function getRegenerationHistory(int $limit = 5): array
    {
        $db = $this->getDatabase();

        try {
            $query = $db->getQuery(true)
                ->select([
                    'l.' . $db->quoteName('id'),
                    'l.' . $db->quoteName('created_at'),
                    'l.' . $db->quoteName('articles_total'),
                    'l.' . $db->quoteName('articles_success'),
                    'l.' . $db->quoteName('articles_failed'),
                    'l.' . $db->quoteName('empty_only'),
                    'l.' . $db->quoteName('processing_time'),
                    'u.' . $db->quoteName('name', 'user_name')
                ])
                ->from($db->quoteName('#__autometa_regeneration_log', 'l'))
                ->leftJoin(
                    $db->quoteName('#__users', 'u') .
                    ' ON ' . $db->quoteName('l.user_id') . ' = ' . $db->quoteName('u.id')
                )
                ->order($db->quoteName('l.created_at') . ' DESC')
                ->setLimit($limit);

            $db->setQuery($query);
            return $db->loadObjectList() ?: [];
        } catch (\Exception $e) {
            Log::add('Failed to get regeneration history: ' . $e->getMessage(), Log::WARNING, 'com_autometa');
            return [];
        }
    }

    /**
     * Get top regenerated articles
     *
     * @param   integer  $limit  Number of records to return
     *
     * @return  array  Array of article statistics
     *
     * @since   2.1.0
     */
    public function getTopRegeneratedArticles(int $limit = 5): array
    {
        $db = $this->getDatabase();

        try {
            $query = $db->getQuery(true)
                ->select([
                    's.' . $db->quoteName('article_id'),
                    's.' . $db->quoteName('article_title'),
                    's.' . $db->quoteName('regeneration_count'),
                    's.' . $db->quoteName('last_regenerated_at'),
                    's.' . $db->quoteName('last_hits_count'),
                    'u.' . $db->quoteName('name', 'user_name')
                ])
                ->from($db->quoteName('#__autometa_article_stats', 's'))
                ->leftJoin(
                    $db->quoteName('#__users', 'u') .
                    ' ON ' . $db->quoteName('s.last_regenerated_by') . ' = ' . $db->quoteName('u.id')
                )
                ->order($db->quoteName('s.regeneration_count') . ' DESC')
                ->setLimit($limit);

            $db->setQuery($query);
            return $db->loadObjectList() ?: [];
        } catch (\Exception $e) {
            Log::add('Failed to get top regenerated articles: ' . $e->getMessage(), Log::WARNING, 'com_autometa');
            return [];
        }
    }

    /**
     * Clean up old analytics data based on retention period
     *
     * @return  void
     *
     * @since   2.1.0
     */
    protected function cleanupOldAnalytics(): void
    {
        $db = $this->getDatabase();
        $params = ComponentHelper::getParams('com_autometa');
        $retentionDays = (int) $params->get('analytics_retention_days', 365);

        try {
            $cutoffDate = Factory::getDate("-{$retentionDays} days")->toSql();

            // Delete old regeneration logs
            $query = $db->getQuery(true)
                ->delete($db->quoteName('#__autometa_regeneration_log'))
                ->where($db->quoteName('created_at') . ' < ' . $db->quote($cutoffDate));

            $db->setQuery($query);
            $db->execute();

            // Note: We don't delete article stats as they are summary data, not time-series
        } catch (\Exception $e) {
            Log::add('Failed to cleanup old analytics: ' . $e->getMessage(), Log::WARNING, 'com_autometa');
        }
    }

    /**
     * Check if user has subscription access (placeholder)
     *
     * @return  boolean  True if user has subscription access
     *
     * @since   2.1.0
     */
    public function hasSubscriptionAccess(): bool
    {
        // TODO: Implement actual subscription check
        // For now, return true for development
        return true;
    }
}
