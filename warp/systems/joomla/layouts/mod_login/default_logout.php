<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.keepalive');

?>

<?php if ($type == 'logout') : ?>

	<form class="uk-form" action="<?php echo Route::_('index.php', true, $params->get('usesecure')); ?>" method="post">

		<?php if ($params->get('greeting')) : ?>
		<div class="uk-form-row">
			<?php if ($params->get('name') == 0) : {
				echo Text::sprintf('MOD_LOGIN_HINAME', htmlspecialchars($user->get('name')));
			} else : {
				echo Text::sprintf('MOD_LOGIN_HINAME', htmlspecialchars($user->get('username')));
			} endif; ?>
		</div>
		<?php endif; ?>

		<div class="uk-form-row">
			<button class="uk-button uk-button-primary" value="<?php echo Text::_('JLOGOUT'); ?>" name="Submit" type="submit"><?php echo Text::_('JLOGOUT'); ?></button>
		</div>

		<input type="hidden" name="option" value="com_users">
		<input type="hidden" name="task" value="user.logout">
		<input type="hidden" name="return" value="<?php echo $return; ?>">
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>

<?php endif; ?>
