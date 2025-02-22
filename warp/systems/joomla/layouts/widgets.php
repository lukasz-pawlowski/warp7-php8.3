<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

use Joomla\CMS\Factory;

// render widgets
$widgets    = $this['widgets']->load($position);
$count      = count($widgets);
$output     = array();
$config     = $this['config'];
$displays   = array_fill_keys(array('small', 'medium', 'large'), array());
$responsive = $config->get("grid.{$position}.responsive", '') ?: 'small';
$stacked    = array_diff($keys = array_keys($displays), array_slice($keys, array_search($responsive, $keys)));

$user = Factory::getUser();

foreach ($widgets as $index => $widget) {


	// set widget params
	$params           = array();
	$params['count']  = $count;
	$params['order']  = $index + 1;
	$params['first']  = $params['order'] == 1;
	$params['last']   = $params['order'] == $count;
    $params['suffix'] = $widget->parameter->moduleclass_sfx ?? " ";

	// pass through menu params
	if ($widget->menu) {
		$widget->nav_settings = array('scrollspy' => false);
	}

    $params = array_merge($params, $config->get('widgets.'.$widget->id, array()));

	// render widget
	$output[] = $this->render('widget', compact('widget', 'params'));

    if ($this['config']['frontediting'] && $user->authorise('core.edit', 'com_modules.module.' . $widget->id)) {

        \JLayoutHelper::render('joomla.edit.frontediting_modules', array(
            'moduleHtml'   => &$output[$index],
            'module'       => $widget,
            'position'     => $position,
            'menusediting' => $this['config']['frontendMenuEditing']
        ));
    }

    foreach ($displays as $name => &$display) {
        if ($config->get("widgets.{$widget->id}.display.{$name}", true)) {
            $display[] = $index;
        }
    }
}

// render widget layout
echo (isset($layout) && $layout) ? $this->render("grid/{$layout}", array('widgets' => $output, 'displays' => $displays, 'stacked' => $stacked)) : implode("\n", $output);
