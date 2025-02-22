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
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

// Activate the highlighter if enabled.
if (!empty($this->query->highlight) && $this->params->get('highlight_terms', 1)) {
	HTMLHelper::_('behavior.highlighter', $this->query->highlight);
}

$app = Factory::getApplication();

?>

<?php if (($this->suggested && $this->params->get('show_suggested_query', 1)) || ($this->explained && $this->params->get('show_explained_query', 1))) : ?>
<p>

	<?php
		// Display the suggested search query.
		if ($this->suggested && $this->params->get('show_suggested_query', 1)) {
			// Replace the base query string with the suggested query string.
			$uri = Uri::getInstance($this->query->toURI());
			$uri->setVar('q', $this->suggested);

			// Compile the suggested query link.
			$link	= '<a href="' . Route::_($uri->toString(array('path', 'query'))) . '">'
					. $this->escape($this->suggested)
					. '</a>';

			echo Text::sprintf('COM_FINDER_SEARCH_SIMILAR', $link);
		}
		// Display the explained search query.
		elseif ($this->explained && $this->params->get('show_explained_query', 1)) {
			echo $this->explained;
		}
	?>

</p>
<?php endif; ?>

<?php if ($this->total == 0) : ?>

	<h1 class="title"><?php echo Text::_('COM_FINDER_SEARCH_NO_RESULTS_HEADING'); ?></h1>

	<?php if ($app->getLanguageFilter()) : ?>
		<p><?php echo Text::sprintf('COM_FINDER_SEARCH_NO_RESULTS_BODY_MULTILANG', $this->escape($this->query->input)); ?></p>
	<?php else : ?>
		<p><?php echo Text::sprintf('COM_FINDER_SEARCH_NO_RESULTS_BODY', $this->escape($this->query->input)); ?></p>
	<?php endif; ?>

<?php else : ?>

	<?php
		// Prepare the pagination string.  Results X - Y of Z
		$total	= (int) $this->pagination->get('total');
		$limit	= (int) $this->pagination->get('limit') * $this->pagination->pagesTotal;
		$limit	= (int) ($limit > $total ? $total : $limit);
	?>

	<br id="highlighter-start">
	<?php
		for ($i = 0, $n = count($this->results); $i < $n; $i++) {
			$this->result	= &$this->results[$i];
			$layout			= $this->getLayoutFile($this->result->layout);
			echo $this->loadTemplate($layout);
		}
	?>
	<br id="highlighter-end">

	<?php echo $this->pagination->getPagesLinks(); ?>

<?php endif;