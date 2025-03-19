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

class AutometaController extends BaseController
{
    public function regenerateAll()
    {
        $model = $this->getModel('Autometa', 'AutometaModel', ['ignore_request' => true]);

        if ($model) {
            $model->regenerateAllMetaDescriptions();
            Factory::getApplication()->enqueueMessage('All meta descriptions regenerated.', 'message');
        } else {
            Factory::getApplication()->enqueueMessage('Error: Could not load model.', 'error');
        }

        $this->setRedirect('index.php?option=com_autometa');
    }
}
