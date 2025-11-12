<?php
/**
 * @package     Regenerate Meta Descriptions
 * @subpackage  com_autometa
 * @version     1.2.1
 * @author      Angus Fox
 * @copyright   (C) 2025 - Multizone Limited
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;

// Get data from view
$stats = $this->statistics;
$params = $this->pluginParams;
?>

<div class="row">
    <div class="col-md-12">
        <!-- Current Settings Section -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title"><?php echo Text::_('COM_AUTOMETA_CURRENT_SETTINGS'); ?></h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3"><?php echo Text::_('COM_AUTOMETA_MAX_LENGTH'); ?></dt>
                    <dd class="col-sm-9"><?php echo (int) $params->get('max_length', 160); ?> <?php echo Text::_('COM_AUTOMETA_CHARACTERS'); ?></dd>

                    <dt class="col-sm-3"><?php echo Text::_('COM_AUTOMETA_SEPARATOR'); ?></dt>
                    <dd class="col-sm-9"><code><?php echo htmlspecialchars($params->get('separator', ' - ')); ?></code></dd>

                    <dt class="col-sm-3"><?php echo Text::_('COM_AUTOMETA_INCLUDE_TITLE'); ?></dt>
                    <dd class="col-sm-9"><?php echo $params->get('use_title', 1) ? Text::_('JYES') : Text::_('JNO'); ?></dd>

                    <dt class="col-sm-3"><?php echo Text::_('COM_AUTOMETA_INCLUDE_CONTENT'); ?></dt>
                    <dd class="col-sm-9"><?php echo $params->get('use_content', 1) ? Text::_('JYES') : Text::_('JNO'); ?></dd>
                </dl>
                <div class="alert alert-info">
                    <span class="icon-info-circle" aria-hidden="true"></span>
                    <?php echo Text::_('COM_AUTOMETA_SETTINGS_NOTE'); ?>
                </div>
            </div>
        </div>

        <!-- Statistics Section -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title"><?php echo Text::_('COM_AUTOMETA_STATISTICS'); ?></h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3"><?php echo Text::_('COM_AUTOMETA_TOTAL_ARTICLES'); ?></dt>
                    <dd class="col-sm-9"><strong><?php echo number_format($stats['total']); ?></strong></dd>

                    <dt class="col-sm-3"><?php echo Text::_('COM_AUTOMETA_WITH_META'); ?></dt>
                    <dd class="col-sm-9">
                        <strong><?php echo number_format($stats['with_meta']); ?></strong>
                        <span class="text-muted">(<?php echo $stats['percentage']; ?>%)</span>
                    </dd>

                    <dt class="col-sm-3"><?php echo Text::_('COM_AUTOMETA_WITHOUT_META'); ?></dt>
                    <dd class="col-sm-9">
                        <strong><?php echo number_format($stats['without_meta']); ?></strong>
                        <span class="text-muted">(<?php echo 100 - $stats['percentage']; ?>%)</span>
                    </dd>
                </dl>

                <!-- Progress bar -->
                <?php if ($stats['total'] > 0) : ?>
                <div class="progress" style="height: 25px;">
                    <div class="progress-bar bg-success" role="progressbar"
                         style="width: <?php echo $stats['percentage']; ?>%"
                         aria-valuenow="<?php echo $stats['percentage']; ?>"
                         aria-valuemin="0" aria-valuemax="100">
                        <?php echo $stats['percentage']; ?>% <?php echo Text::_('COM_AUTOMETA_COMPLETE'); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Analytics Section (Subscription Required) -->
        <?php if ($this->hasSubscription) : ?>
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title"><?php echo Text::_('COM_AUTOMETA_ANALYTICS'); ?></h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Regeneration History -->
                    <div class="col-md-6 mb-4 mb-md-0">
                        <h4 class="h5 mb-3"><?php echo Text::_('COM_AUTOMETA_REGENERATION_HISTORY'); ?></h4>
                        <?php if (!empty($this->regenerationHistory)) : ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th><?php echo Text::_('COM_AUTOMETA_DATE'); ?></th>
                                        <th><?php echo Text::_('COM_AUTOMETA_USER'); ?></th>
                                        <th><?php echo Text::_('COM_AUTOMETA_ARTICLES_PROCESSED'); ?></th>
                                        <th><?php echo Text::_('COM_AUTOMETA_SUCCESS_RATE'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($this->regenerationHistory as $log) : ?>
                                    <?php
                                        $successRate = $log->articles_total > 0
                                            ? round(($log->articles_success / $log->articles_total) * 100, 1)
                                            : 0;
                                        $date = Factory::getDate($log->created_at);
                                    ?>
                                    <tr>
                                        <td>
                                            <small><?php echo $date->format('Y-m-d H:i'); ?></small>
                                        </td>
                                        <td>
                                            <small><?php echo htmlspecialchars($log->user_name ?? 'Unknown'); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?php echo $log->articles_success; ?></span>
                                            <?php if ($log->empty_only) : ?>
                                            <small class="text-muted">(<?php echo Text::_('COM_AUTOMETA_EMPTY_ONLY_SHORT'); ?>)</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $successRate == 100 ? 'bg-success' : 'bg-warning'; ?>">
                                                <?php echo $successRate; ?>%
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else : ?>
                        <p class="text-muted"><?php echo Text::_('COM_AUTOMETA_NO_HISTORY'); ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Top Regenerated Articles -->
                    <div class="col-md-6">
                        <h4 class="h5 mb-3"><?php echo Text::_('COM_AUTOMETA_TOP_REGENERATED_ARTICLES'); ?></h4>
                        <?php if (!empty($this->topArticles)) : ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th><?php echo Text::_('COM_AUTOMETA_ARTICLE_TITLE'); ?></th>
                                        <th><?php echo Text::_('COM_AUTOMETA_REGENERATION_COUNT'); ?></th>
                                        <th><?php echo Text::_('COM_AUTOMETA_HITS'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($this->topArticles as $article) : ?>
                                    <?php $date = $article->last_regenerated_at ? Factory::getDate($article->last_regenerated_at) : null; ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($article->article_title); ?></strong>
                                            <?php if ($date) : ?>
                                            <br><small class="text-muted">
                                                <?php echo Text::_('COM_AUTOMETA_LAST_REGENERATED'); ?>:
                                                <?php echo $date->format('Y-m-d'); ?>
                                            </small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary"><?php echo $article->regeneration_count; ?>×</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo number_format($article->last_hits_count ?? 0); ?></span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else : ?>
                        <p class="text-muted"><?php echo Text::_('COM_AUTOMETA_NO_ARTICLE_HISTORY'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Regeneration Options Section -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title"><?php echo Text::_('COM_AUTOMETA_REGENERATION_OPTIONS'); ?></h3>
            </div>
            <div class="card-body">
                <form action="<?php echo Route::_('index.php?option=com_autometa&task=regenerate'); ?>" method="post" id="adminForm" name="adminForm">

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="empty_only" id="empty_only" value="1">
                        <label class="form-check-label" for="empty_only">
                            <strong><?php echo Text::_('COM_AUTOMETA_EMPTY_ONLY'); ?></strong><br>
                            <small class="text-muted"><?php echo Text::_('COM_AUTOMETA_EMPTY_ONLY_DESC'); ?></small>
                        </label>
                    </div>

                    <div class="alert alert-warning">
                        <span class="icon-warning" aria-hidden="true"></span>
                        <strong><?php echo Text::_('COM_AUTOMETA_WARNING'); ?></strong>
                        <?php echo Text::_('COM_AUTOMETA_WARNING_DESC'); ?>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <span class="icon-refresh" aria-hidden="true"></span>
                            <?php echo Text::_('COM_AUTOMETA_REGENERATE_NOW'); ?>
                        </button>
                    </div>

                    <?php echo HTMLHelper::_('form.token'); ?>
                </form>
            </div>
        </div>

        <!-- Standard Subscription Conversion Card -->
        <div class="card mb-3 border-primary">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h3 class="card-title mb-3"><?php echo Text::_('COM_AUTOMETA_STANDARD_TITLE'); ?></h3>
                        <p class="text-muted mb-4"><?php echo Text::_('COM_AUTOMETA_STANDARD_SUBTITLE'); ?></p>

                        <!-- Features List -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-2">
                                <span class="icon-check text-success" aria-hidden="true"></span>
                                <?php echo Text::_('COM_AUTOMETA_STANDARD_FEATURE_ONE'); ?>
                            </div>
                            <div class="col-md-6 mb-2">
                                <span class="icon-check text-success" aria-hidden="true"></span>
                                <?php echo Text::_('COM_AUTOMETA_STANDARD_FEATURE_TWO'); ?>
                            </div>
                            <div class="col-md-6 mb-2">
                                <span class="icon-check text-success" aria-hidden="true"></span>
                                <?php echo Text::_('COM_AUTOMETA_STANDARD_FEATURE_THREE'); ?>
                            </div>
                            <div class="col-md-6 mb-2">
                                <span class="icon-check text-success" aria-hidden="true"></span>
                                <?php echo Text::_('COM_AUTOMETA_STANDARD_FEATURE_FOUR'); ?>
                            </div>
                        </div>

                        <!-- CTA Buttons -->
                        <div class="d-flex flex-wrap gap-3">
                            <a href="https://www.multizone.co.uk/subscription?extension=&utm_source=download&utm_medium=premium_redirect&utm_campaign=conversion_card" class="btn btn-primary btn-lg">
                                <span class="icon-shopping-cart" aria-hidden="true"></span>
                                <?php echo Text::_('COM_AUTOMETA_STANDARD_PRIMARY_BUTTON'); ?>
                            </a>
                            <a href="/index.php/component/subsmgr?view=extensions" class="btn btn-outline-secondary btn-lg">
                                <span class="icon-grid" aria-hidden="true"></span>
                                <?php echo Text::_('COM_AUTOMETA_STANDARD_SECONDARY_BUTTONE'); ?>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-4 text-center mt-4 mt-lg-0">
                        <!-- Pricing Card -->
                        <div class="card bg-primary text-white">
                            <div class="card-body p-4">
                                <h4 class="card-title"><?php echo Text::_('COM_AUTOMETA_STANDARD_BOX_TITLE'); ?></h4>
                                <div class="display-6 fw-bold mb-2"><?php echo Text::_('COM_AUTOMETA_STANDARD_BOX_AMOUNT'); ?></div>
                                <p class="mb-3"><?php echo Text::_('COM_AUTOMETA_STANDARD_BOX_PERIOD'); ?></p>
                                <a href="https://www.multizone.co.uk/subscription?plan=premium&extension=" class="btn btn-light btn-lg w-100">
                                    <?php echo Text::_('COM_AUTOMETA_STANDARD_BOX_BUTTON'); ?>
                                </a>
                                <div class="mt-3">
                                    <small><?php echo Text::_('COM_AUTOMETA_STANDARD_GUARANTEE'); ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
