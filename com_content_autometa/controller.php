<?php
/*
 * @package Regenerate Meta Descriptions
 * @version 1.1.28 autometa.php
 * @author Angus Fox
 * @copyright (C) 2025 - Multizone Limited
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;

class AutometaController extends BaseController
{
    public function regenerateAll()
    {
        // Check CSRF token
        Session::checkToken() or jexit('Invalid Token');

        $app = Factory::getApplication();
        $user = $app->getIdentity();

        // Check user permissions
        if (!$user->authorise('core.edit', 'com_content') && !$user->authorise('core.admin')) {
            $app->enqueueMessage('You do not have permission to perform this action.', 'error');
            $this->setRedirect('index.php?option=com_autometa');
            return;
        }

        $model = $this->getModel('Autometa', 'AutometaModel', ['ignore_request' => true]);

        if ($model) {
            try {
                $result = $model->regenerateAllMetaDescriptions();

                if ($result['errors'] > 0) {
                    $app->enqueueMessage(
                        sprintf('Processed %d of %d articles. %d errors occurred. Check logs for details.',
                            $result['processed'],
                            $result['total'],
                            $result['errors']
                        ),
                        'warning'
                    );
                } else {
                    $app->enqueueMessage(
                        sprintf('Successfully regenerated meta descriptions for %d articles.', $result['processed']),
                        'message'
                    );
                }
            } catch (\Exception $e) {
                $app->enqueueMessage('Error: ' . $e->getMessage(), 'error');
            }
        } else {
            $app->enqueueMessage('Error: Could not load model.', 'error');
        }

        $this->setRedirect('index.php?option=com_autometa');
    }
}
