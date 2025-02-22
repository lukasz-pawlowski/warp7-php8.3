<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

// Create a shortcut for params.
$item 	 = $this->item;
$params  = $item->params;
$canEdit = $this->item->params->get('access-edit');
$args    = include(__DIR__.'/../article_defaults.php');

// template args
$args = array_merge($args, array(
	'permalink' => Route::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid), true, -1),
	'image' => isset($images->image_intro) ? htmlspecialchars($images->image_intro) : '',
	'image_alignment' => !isset($images->float_intro) || empty($images->float_intro) ? htmlspecialchars($params->get('float_intro')) : htmlspecialchars($images->float_intro),
	'image_alt' => isset($images->image_intro_alt) ? htmlspecialchars($images->image_intro_alt) : '',
	'image_caption' => isset($images->image_intro_caption) ? htmlspecialchars($images->image_intro_caption) : '',
	'article' => $this->item->introtext,
	'is_column_item' => (isset($this->item->is_column_item)) ? $this->item->is_column_item : 0
));

// set edit
$args['edit']  = $canEdit ? HTMLHelper::_('icon.edit', $this->item, $params) : '';
$args['edit'] .= $params->get('show_print_icon') ? HTMLHelper::_('icon.print_popup', $this->item, $params) : '';
$args['edit'] .= $params->get('show_email_icon') ? HTMLHelper::_('icon.email', $this->item, $params) : '';

// set url
if ($params->get('access-view')) {
	$link = Route::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid));
} else {
	$menu = Factory::getApplication()->getMenu();
	$active = $menu->getActive();
	$itemId = $active->id;
	$link1 = Route::_('index.php?option=com_users&view=login&Itemid=' . $itemId);
	$returnURL = ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid);
	$link = new Uri($link1);
	$link->setVar('return', base64_encode($returnURL));
}
$args['url'] = $link;

// set more
if ($params->get('show_readmore') && $this->item->readmore) {
	if (!$params->get('access-view')) {
		$args['more'] = Text::_('COM_CONTENT_REGISTER_TO_READ_MORE');
	} elseif ($readmore = $this->item->alternative_readmore) {
		$args['more'] = $readmore;
	} else {
		$args['more'] = Text::_('TPL_WARP_CONTINUE_READING');
	}
}

// render template
echo $warp['template']->render('article', $args);
