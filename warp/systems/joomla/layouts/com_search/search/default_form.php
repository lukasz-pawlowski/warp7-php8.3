<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('bootstrap.tooltip');

$lang = Factory::getLanguage();
$upper_limit = $lang->getUpperLimitSearchWord();
?>
<form class="uk-form uk-margin-bottom" id="searchForm" action="<?php echo Route::_('index.php?option=com_search');?>" method="post">

	<div class="uk-card uk-card-default">

		<fieldset>
			<div class="uk-form-row">
				<label for="search_searchword"><?php echo Text::_('COM_SEARCH_SEARCH_KEYWORD'); ?></label>
				<input type="text" name="searchword" placeholder="<?php echo Text::_('COM_SEARCH_SEARCH_KEYWORD'); ?>" id="search-searchword" size="30" maxlength="<?php echo $upper_limit; ?>" value="<?php echo $this->escape($this->origkeyword); ?>" class="inputbox" />
				<button name="Search" onclick="this.form.submit()" class="uk-button uk-button-primary" title="<?php echo HTMLHelper::tooltipText('COM_SEARCH_SEARCH');?>"><?php echo HTMLHelper::tooltipText('COM_SEARCH_SEARCH');?></button>
				<input type="hidden" name="task" value="search" />
			</div>

		</fieldset>

		<fieldset>
			<legend><?php echo Text::_('COM_SEARCH_FOR');?></legend>

			<div class="uk-form-row">
				<?php echo $this->lists['searchphrase']; ?>
			</div>

			<div class="uk-form-row">
				<label for="ordering" class="ordering"><?php echo Text::_('COM_SEARCH_ORDERING');?></label>
				<?php echo $this->lists['ordering'];?>
			</div>
		</fieldset>

		<?php if ($this->params->get('search_areas', 1)) : ?>
		<fieldset>
			<legend><?php echo Text::_('COM_SEARCH_SEARCH_ONLY');?></legend>
			<div class="uk-form-row">
			<?php foreach ($this->searchareas['search'] as $val => $txt) :
				$checked = is_array($this->searchareas['active']) && in_array($val, $this->searchareas['active']) ? 'checked="checked"' : '';
			?>
			<label for="area-<?php echo $val;?>">
				<input type="checkbox" name="areas[]" value="<?php echo $val;?>" id="area-<?php echo $val;?>" <?php echo $checked;?> >
				<?php echo Text::_($txt).'&nbsp;'; ?>
			</label>
			<?php endforeach; ?>
			</div>
		</fieldset>
		<?php endif; ?>

	</div>

	<div class="uk-margin-top<?php echo $this->params->get('pageclass_sfx'); ?>">
		<?php if (!empty($this->searchword)):?>
		<?php echo Text::plural('COM_SEARCH_SEARCH_KEYWORD_N_RESULTS', '<span class="uk-badge uk-badge-info">'. $this->total. '</span>');?>
		<?php endif;?>

		<?php if ($this->total > 0) : ?>

			<div class="uk-float-right uk-clearfix">
				<label for="limit"><?php echo Text::_('JGLOBAL_DISPLAY_NUM'); ?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>

		<?php endif; ?>

	</div>

</form>