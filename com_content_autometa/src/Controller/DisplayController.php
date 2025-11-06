<?php
/**
 * @package     Regenerate Meta Descriptions
 * @subpackage  com_autometa
 * @version     1.1.28
 * @author      Angus Fox
 * @copyright   (C) 2025 - Multizone Limited
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Ezone\Component\AutoMeta\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;

/**
 * Default Controller for AutoMeta component
 *
 * @since  1.0.0
 */
class DisplayController extends BaseController
{
    /**
     * The default view for the display method
     *
     * @var    string
     * @since  1.0.0
     */
    protected $default_view = 'autometa';

    /**
     * Regenerate all meta descriptions
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function regenerateAll()
    {
        // Check CSRF token
        Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

        $app = Factory::getApplication();
        $user = $app->getIdentity();

        // Check user permissions
        if (!$user->authorise('core.edit', 'com_content') && !$user->authorise('core.admin')) {
            $app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
            $this->setRedirect('index.php?option=com_autometa');
            return;
        }

        $model = $this->getModel('Autometa');

        if ($model) {
            try {
                $result = $model->regenerateAllMetaDescriptions();

                if ($result['errors'] > 0) {
                    $app->enqueueMessage(
                        sprintf(
                            Text::_('COM_AUTOMETA_MSG_REGENERATE_PARTIAL'),
                            $result['processed'],
                            $result['total'],
                            $result['errors']
                        ),
                        'warning'
                    );
                } else {
                    $app->enqueueMessage(
                        sprintf(Text::_('COM_AUTOMETA_MSG_REGENERATE_SUCCESS'), $result['processed']),
                        'message'
                    );
                }
            } catch (\Exception $e) {
                $app->enqueueMessage(Text::sprintf('COM_AUTOMETA_ERROR_REGENERATE', $e->getMessage()), 'error');
            }
        } else {
            $app->enqueueMessage(Text::_('COM_AUTOMETA_ERROR_MODEL_LOAD'), 'error');
        }

        $this->setRedirect('index.php?option=com_autometa');
    }
}
