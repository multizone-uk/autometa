<?php
/**
 * @package     Regenerate Meta Descriptions
 * @subpackage  com_autometa
 * @version     1.2.1
 * @author      Angus Fox
 * @copyright   (C) 2025 - Multizone Limited
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Ezone\Component\AutoMeta\Administrator\View\Autometa;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Ezone\Plugin\Content\AutoMeta\Helper\MetaDescriptionHelper;

/**
 * View for AutoMeta component
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * Statistics array
     *
     * @var    array
     * @since  1.2.1
     */
    protected $statistics;

    /**
     * Plugin parameters
     *
     * @var    \Joomla\Registry\Registry
     * @since  1.2.1
     */
    protected $pluginParams;

    /**
     * Regeneration history
     *
     * @var    array
     * @since  2.1.0
     */
    protected $regenerationHistory;

    /**
     * Top regenerated articles
     *
     * @var    array
     * @since  2.1.0
     */
    protected $topArticles;

    /**
     * Whether user has subscription access
     *
     * @var    boolean
     * @since  2.1.0
     */
    protected $hasSubscription;

    /**
     * Display the view
     *
     * @param   string  $tpl  The name of the template file to parse
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function display($tpl = null): void
    {
        // Get model
        $model = $this->getModel();

        // Get statistics
        $this->statistics = $model->getStatistics();

        // Get plugin parameters
        $this->pluginParams = MetaDescriptionHelper::getPluginParams();

        // Check subscription access
        $this->hasSubscription = $model->hasSubscriptionAccess();

        // Get analytics data if user has subscription
        if ($this->hasSubscription) {
            $this->regenerationHistory = $model->getRegenerationHistory(5);
            $this->topArticles = $model->getTopRegeneratedArticles(5);
        }

        // Set the toolbar
        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar
     *
     * @return  void
     *
     * @since   1.0.0
     */
    protected function addToolbar(): void
    {
        ToolbarHelper::title(Text::_('COM_AUTOMETA_MANAGER'), 'articles');
    }
}
