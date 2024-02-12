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
use Joomla\CMS\Uri\Uri;

// get warp
$warp = require(__DIR__.'/warp.php');

// set messages
$title   = $this->title;
$error   = $this->error->getCode();
$message = $this->error->getMessage();

// set 404 messages
if ($error == '404') {
	$title   = Text::_('TPL_WARP_404_PAGE_TITLE');
	$message = Text::sprintf('TPL_WARP_404_PAGE_MESSAGE', Uri::root(false), $warp['config']->get('site_name'));
}

// render error layout
echo $warp['template']->render('error', compact('title', 'error', 'message'));