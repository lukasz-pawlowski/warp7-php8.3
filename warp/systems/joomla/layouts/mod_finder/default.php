<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// get warp
global $warp;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$id = implode('-', array('search', $module->id, uniqid()));

?>

<form id="<?php echo $id; ?>" class="uk-search" action="<?php echo Route::_($route); ?>" method="get">
	<input class="uk-search-field" type="text" name="q" placeholder="<?php echo Text::_('TPL_WARP_SEARCH'); ?>" autocomplete="off">
	<?php echo modFinderHelper::getGetFields($route, (int) $params->get('set_itemid')); ?>
</form>
