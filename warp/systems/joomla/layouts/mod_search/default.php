<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// get application

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$app = Factory::getApplication();

// get item id
$itemid = intval($params->get('set_itemid', 0));
$id     = implode('-', array('search', $module->id, uniqid()));

?>

<form id="<?php echo $id; ?>" class="uk-search" action="<?php echo Route::_('index.php'); ?>" method="post" <?php if($module->position !== 'offcanvas'):?>data-uk-search="{'source': '<?php echo Route::_("index.php?option=com_search&tmpl=raw&type=json&ordering=&searchphrase=all");?>', 'param': 'searchword', 'msgResultsHeader': '<?php echo Text::_("TPL_WARP_SEARCH_RESULTS"); ?>', 'msgMoreResults': '<?php echo Text::_("TPL_WARP_SEARCH_MORE"); ?>', 'msgNoResults': '<?php echo Text::_("TPL_WARP_SEARCH_NO_RESULTS"); ?>', flipDropdown: 1}"<?php endif;?>>
	<input class="uk-search-field" type="text" name="searchword" placeholder="<?php echo Text::_('TPL_WARP_SEARCH'); ?>">
	<input type="hidden" name="task"   value="search">
	<input type="hidden" name="option" value="com_search">
	<input type="hidden" name="Itemid" value="<?php echo $itemid > 0 ? $itemid : $app->input->getInt('Itemid'); ?>">
</form>
