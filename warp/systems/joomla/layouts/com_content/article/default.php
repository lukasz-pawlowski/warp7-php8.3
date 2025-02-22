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
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

// Create a shortcut for params.
$item    = $this->item;
$params  = $item->params;
$urls	 = json_decode($item->urls);
$canEdit = $params->get('access-edit');
$user	 = Factory::getUser();
$args 	 = include(__DIR__.'/../article_defaults.php');

// get view
$menu = Factory::getApplication()->getMenu()->getActive();
$view = is_object($menu) && isset($menu->query['view']) ? $menu->query['view'] : null;

if ($view == 'article') $args['permalink'] = '';

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers');


if ($this->params->get('show_page_heading')) {
	echo '<h1>'.$this->escape($this->params->get('page_heading')).'</h1>';
}

// set author_url
if (!empty($this->item->contactid) && $params->get('link_author') == true) {
	$needle = 'index.php?option=com_contact&view=contact&id=' . $this->item->contactid;
	$menu = Factory::getApplication()->getMenu();
	$item = $menu->getItems('link', $needle, true);
	$args['author_url'] = !empty($item) ? $needle . '&Itemid=' . $item->id : $needle;
}

// set article
$article = "";
if ($params->get('access-view')) {

	if (isset($urls) AND ((!empty($urls->urls_position) AND ($urls->urls_position=='0')) OR ($params->get('urls_position')=='0' AND empty($urls->urls_position) ))
		OR (empty($urls->urls_position) AND (!$params->get('urls_position')))) {
			$article .= $this->loadTemplate('links');
	}

	$article .= $this->item->text;

	if (isset($urls) AND ((!empty($urls->urls_position)  AND ($urls->urls_position=='1')) OR ( $params->get('urls_position')=='1') )) {
		$article .= $this->loadTemplate('links');
	}

// optional teaser intro text for guests
} elseif ($params->get('show_noauth') == true AND $user->get('guest')) {

	$article .= $this->item->introtext;

	// optional link to let them register to see the whole article.
	if ($params->get('show_readmore') && $this->item->fulltext != null) {
		$link1 = Route::_('index.php?option=com_users&view=login');
		$link = new Uri($link1);
		$article .= '<p class="links">';
		$article .= '<a href="'.$link.'">';
		$attribs = json_decode($this->item->attribs);

		if ($attribs->alternative_readmore == null) {
			$article .= Text::_('COM_CONTENT_REGISTER_TO_READ_MORE');
		} elseif ($readmore = $this->item->alternative_readmore) {
			$article .= $readmore;
			if ($params->get('show_readmore_title', 0) != 0) {
				$article .= HTMLHelper::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
			}
		} elseif ($params->get('show_readmore_title', 0) == 0) {
			$article .= Text::sprintf('COM_CONTENT_READ_MORE_TITLE');
		} else {
			$article .= Text::_('COM_CONTENT_READ_MORE');
			$article .= HTMLHelper::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
		}

		$article .= '</a></p>';
	}
}

$args['article'] = $article;

// set tags
$tags = '';
if ($params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) {
	JLoader::register('TagsHelperRoute', JPATH_BASE . '/components/com_tags/helpers/route.php');
	foreach ($this->item->tags->itemTags as $i => $tag) {
		if (in_array($tag->access, JAccess::getAuthorisedViewLevels(Factory::getUser()->get('id')))) {
			if($i > 0) $tags .= ', ';
			$tags .= '<a href="'.Route::_(TagsHelperRoute::getTagRoute($tag->tag_id . ':' . $tag->alias)).'">'.$this->escape($tag->title).'</a>';
		}
	}

}

$args['tags'] = $tags;

// set edit
if (!$this->print) {
	$attrs = array('class' => 'uk-margin-right');
	$args['edit']  = $canEdit ? HTMLHelper::_('icon.edit', $this->item, $params, $attrs) : '';
	$args['edit'] .= $params->get('show_print_icon') ? HTMLHelper::_('icon.print_popup', $this->item, $params, $attrs) : '';
	$args['edit'] .= $params->get('show_email_icon') ? HTMLHelper::_('icon.email', $this->item, $params, $attrs) : '';
} else {
	$args['edit'] = HTMLHelper::_('icon.print_screen', $this->item, $params);
}

// set previous and next
if (!empty($this->item->pagination)) {
	$args['previous'] = ($prev = $this->item->prev) ? $prev : '';
	$args['next'] = ($next = $this->item->next) ? $next : '';
}

// render template
echo $warp['template']->render('article', $args);
