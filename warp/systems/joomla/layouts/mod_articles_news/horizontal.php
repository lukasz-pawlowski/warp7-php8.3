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
	<ul class="uk-grid" uk-height-match uk-grid>
		<?php for ($i = 0, $count = count($list); $i < $count; $i ++) : ?>
		<?php $item = $list[$i]; ?>
		<li class="uk-width-1-<?php echo $count ?>@m">
			<?php require ModuleHelper::getLayoutPath('mod_articles_news', '_item'); ?>
		</li>
		<?php endfor; ?>
	</ul>
<?php endif;
