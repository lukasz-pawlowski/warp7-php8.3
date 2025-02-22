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

HTMLHelper::addIncludePath(JPATH_COMPONENT.'/helpers');

?>

<?php if ($this->params->get('show_page_heading', 1)) : ?>
<div class="page-header"><h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1></div>
<?php endif; ?>

<?php

// init vars
$articles = '';

// leading articles
if (!empty($this->lead_items)) {
	$articles  .= '<div class="uk-grid tm-leading-article"><div class="uk-width-1-1">';
	foreach ($this->lead_items as $item) {
		$this->item = $item;
		$articles  .= $this->loadTemplate('item');
	}
	$articles  .= '</div></div>';
}

// intro articles
$num_columns = $this->params->get('num_columns', 2);
$columns 	 = array();
$i = 0;

foreach ($this->intro_items as $item) {
	$column = $i++ % $num_columns;

	if (!isset($columns[$column])) {
		$columns[$column] = '';
	}

	$this->item = $item;
	$this->item->is_column_item = ($num_columns > 1);
	$columns[$column] .= $this->loadTemplate('item');
}

// render intro columns
if ($count = count($columns)) {
	$articles  .= '<div class="uk-grid" uk-height-match uk-grid>';
	for ($i = 0; $i < $count; $i++) {
		$articles .= '<div class="uk-width-1-'.$count.'@m">'.$columns[$i].'</div>';
	}
	$articles  .= '</div>';
}

if ($articles) echo $articles;

?>

<?php if (!empty($this->link_items)) : ?>
<h3><?php echo Text::_('COM_CONTENT_MORE_ARTICLES'); ?></h3>
<ul class="uk-list">
	<?php foreach ($this->link_items as &$item) : ?>
	<li><a href="<?php echo Route::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug)); ?>"><?php echo $item->title; ?></a></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if (($this->params->def('show_pagination', 1) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
<?php echo $this->pagination->getPagesLinks(); ?>
<?php endif; ?>
