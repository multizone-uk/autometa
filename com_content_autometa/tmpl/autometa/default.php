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
    </div>
</div>
