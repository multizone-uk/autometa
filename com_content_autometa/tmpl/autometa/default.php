<?php
/*
 * @package Regenerate Meta Descriptions
 * @version 1.1.28 autometa.php
 * @author Angus Fox
 * @copyright (C) 2025 - Multizone Limited
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/
defined('_JEXEC') or die;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
?>

<form action="<?php echo Route::_('index.php?option=com_autometa&task=regenerateAll'); ?>" method="post">
    <button type="submit" class="btn btn-primary">Regenerate All Meta Descriptions</button>
    <?php echo Session::getFormToken(); ?>
</form>
