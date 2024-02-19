<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;

?>

<?php foreach ($list as $item) : ?>
	<?php require ModuleHelper::getLayoutPath('mod_articles_news', '_item'); ?>
<?php endforeach;