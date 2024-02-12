<?php
/**
* @package   yoo_master2
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;

// get warp
$warp = require(__DIR__.'/warp.php');

// render offline layout
echo $warp['template']->render('offline', array('title' => Text::_('TPL_WARP_OFFLINE_PAGE_TITLE'), 'error' => 'Offline', 'message' => Text::_('TPL_WARP_OFFLINE_PAGE_MESSAGE')));