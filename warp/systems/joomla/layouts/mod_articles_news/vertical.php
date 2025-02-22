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

<?php if (count($list) > 0) : ?>
<ul class="uk-list uk-list-line">
	<?php for ($i = 0, $n = count($list); $i < $n; $i ++) : ?>
	<?php $item = $list[$i]; ?>
	<li><?php require ModuleHelper::getLayoutPath('mod_articles_news', '_item'); ?></li>
	<?php endfor; ?>
</ul>
<?php endif;