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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::addIncludePath(JPATH_COMPONENT.'/helpers');


?>

<?php
$app = Factory::getApplication();
$app->input->set('layout', 'blog');
?>

<?php if ($this->params->get('show_page_heading') || $this->params->get('page_subheading') || $this->params->get('show_description', 1) || $this->params->def('show_description_image', 1) || $this->params->get('show_category_title', 1)) : ?>
<div class="uk-grid">
	<div class="uk-width-1-1">
		<div class="uk-panel uk-panel-header">

			<?php if ($this->params->get('show_page_heading')) : ?>
			<h1 class="tm-title"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
			<?php endif; ?>

			<?php if ($this->params->get('page_subheading')) : ?>
			<h2><?php echo $this->escape($this->params->get('page_subheading')); ?></h2>
			<?php endif; ?>

			<?php if ($this->params->get('show_category_title', 1)) : ?>
			<h3 class="uk-h3"><?php echo $this->category->title;?></h3>
			<?php endif; ?>

			<?php if ($this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) :?>
			<div class="uk-clearfix">

				<?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
					<img src="<?php echo $this->category->getParams()->get('image'); ?>" alt="<?php echo $this->category->getParams()->get('image'); ?>" class="uk-align-right">
				<?php endif; ?>

				<?php if ($this->params->get('show_description') && $this->category->description) echo HTMLHelper::_('content.prepare', $this->category->description, '', 'com_content.category'); ?>

				<?php

					if ($this->params->get('show_tags', 1) && !empty($this->category->tags->itemTags)) {
						JLoader::register('TagsHelperRoute', JPATH_BASE . '/components/com_tags/helpers/route.php');
						echo '<p>'.Text::_('TPL_WARP_TAGS').': ';
						foreach ($this->category->tags->itemTags as $i => $tag) {
							if (in_array($tag->access, JAccess::getAuthorisedViewLevels(Factory::getUser()->get('id')))) {
								if($i > 0) echo ', ';
								echo '<a href="'.Route::_(TagsHelperRoute::getTagRoute($tag->tag_id . ':' . $tag->alias)).'">'.$this->escape($tag->title).'</a>';
							}
						}
						echo '</p>';
					}

				?>
			</div>
			<?php endif; ?>

		</div>
	</div>
</div>
<?php endif; ?>

<?php if (empty($this->lead_items) && empty($this->link_items) && empty($this->intro_items)) : ?>
	<?php if ($this->params->get('show_no_articles', 1)) : ?>
		<div class="uk-alert"><?php echo Text::_('COM_CONTENT_NO_ARTICLES'); ?></div>
	<?php endif; ?>
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
<div class="uk-grid">
	<div class="uk-width-1-1">
		<div class="uk-panel uk-panel-header">
			<h3 class="uk-panel-title"><?php echo Text::_('COM_CONTENT_MORE_ARTICLES'); ?></h3>
			<ul class="uk-list">
				<?php foreach ($this->link_items as &$item) : ?>
				<li><a href="<?php echo Route::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catid)); ?>"><?php echo $item->title; ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>
<?php endif; ?>

<?php if (($this->params->def('show_pagination', 1) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
<?php echo $this->pagination->getPagesLinks(); ?>
<?php endif; ?>
