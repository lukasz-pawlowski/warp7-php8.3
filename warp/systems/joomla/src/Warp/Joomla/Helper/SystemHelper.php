<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

namespace Warp\Joomla\Helper;

use Warp\Warp;
use Warp\Helper\AbstractHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Folder;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;

/**
 * Joomla! system helper class, provides Joomla! CMS integration (http://www.joomla.org).
 */
class SystemHelper extends AbstractHelper
{
    /**
     * System application.
     *
     * @var object
     */
    public $application;

    /**
     * System document.
     *
     * @var object
     */
    public $document;

    /**
     * System language.
     *
     * @var object
     */
    public $language;

    /*
     * System root path.
     *
     * @var string
     */
    public $path;

    /*
     * System root url.
     *
     * @var string
     */
    public $url;

    /*
     * Cache path.
     *
     * @var string
     */
    public $cache_path;

    /*
     * Cache time.
     *
     * @var int
     */
    public $cache_time;

    /**
     * Dynamic style GET variable.
     *
     * @var string
     */
    protected $style = 'style';

    /**
     * Constructor.
     *
     * @param Warp $warp
     */
    public function __construct(Warp $warp)
    {
        parent::__construct($warp);

        jimport('joomla.filesystem.folder');

        // init vars
        $this->application = Factory::getApplication();
        $this->document    = Factory::getDocument();
        $this->language    = Factory::getLanguage();
        $this->path        = JPATH_ROOT;
        $this->url         = rtrim(Uri::root(false), '/');
        $this->cache_path  = $this->path . '/media/template';
        $this->cache_time  = max(Factory::getConfig()->get('cachetime') * 60, 86400);

        // set config or load defaults
        $this['config']->load($this['path']->path('theme:config.json') ? : $this['path']->path('theme:config.default.json'));

        // set cache directory
        if (!file_exists($this->cache_path)) {
            Folder::create($this->cache_path);
        }
    }

    /**
     * Initialize system.
     */
    public function init()
    {
        // set paths
        $this['path']
            ->register($this->path, 'site')
            ->register($this->path . '/administrator', 'admin')
            ->register($this->path . '/media/template', 'cache');

        // set theme support
        Factory::getConfig()->set('widgetkit', true);

        // set translations
        $this->language->load('tpl_warp', $this['path']->path('warp:systems/joomla'), null, true);

        // init site/admin
        if ($this->application->isClient('site'))
            $this->initSite();
        if ($this->application->isClient('admin'))
            $this->initAdmin();
    }

    /**
     * Initialize site.
     */
    public function initSite()
    {
        // get application
        $app = $this->application;

        // get user
        $user = Factory::getUser();

        // set config
        $this['config']['language']    = $this->document->language;
        $this['config']['direction']   = $this->document->direction;
        $this['config']['site_url']    = rtrim(Uri::root(), '/');
        $this['config']['site_name']   = $app->getCfg('sitename');
        $this['config']['datetime']    = HtmlHelper::_('date', 'now', 'Y-m-d');
        $this['config']['actual_date'] = HtmlHelper::_('date', 'now', Text::_('DATE_FORMAT_LC'));
        $this['config']['page_class']  = $app->getParams()->get('pageclass_sfx');

        // frontentediting
        $frontediting = $app->get('frontediting', 1);

        $this['config']['frontediting'] = $frontediting && $user->id && $user->authorise('core.edit', 'com_modules');
        $this['config']['frontendMenuEditing'] = ($frontediting == 2) && $user->authorise('core.edit', 'com_menus');

        // branding ?
        if ($this['config']->get('warp_branding', true)) {
            $this['template']->set('warp_branding', $this['config']['branding']);
        }

        // set layouts
        if ($layouts = $this['config']['layouts']) {

            $layout = 'default';
            $itemid = $app->input->getInt('Itemid', 0);

            // add menu item layout?
            foreach ($layouts as $key => $data) {
                if (isset($data['assignment']) && in_array($itemid, $data['assignment'])) {
                    $layout = $key;
                    break;
                }
            }

            $this['config']->setValues($layouts[$layout]);
        }

        // set dynamic style
        if ($this['config']['dynamic_style']) {

            if ($style = $app->input->get($this->style)) {
                $app->setUserState('_style', $style);
            }

            if ($style = $app->getUserState('_style') and $this['path']->path(sprintf('theme:styles/%s', $style))) {
                $this['config']['style'] = $style;
            }
        }

        // set theme style paths
        if ($style = $this['config']->get('style')) {
            foreach (array('css' => 'theme:styles/%s/css', 'js' => 'theme:styles/%s/js', 'layouts' => 'theme:styles/%s/layouts') as $name => $resource) {
                if ($p = $this['path']->path(sprintf($resource, $style))) {
                    $this['path']->register($p, $name);
                }
            }
        }

        // force show system output on search results
        if (strtolower($this->application->input->get('option')) == 'com_search') {
            $this['config']['system_output'] = 1;
        }
    }

    /**
     * Initialize administration area.
     */
    public function initAdmin()
    {
        // get xml's
        $tmpl_xml = $this['dom']->create($this['path']->path('theme:templateDetails.xml'), 'xml');
        $warp_xml = $this['dom']->create($this['path']->path('warp:warp.xml'), 'xml');

        // cache writable ?
        if (!file_exists($this->cache_path) || !is_writable($this->cache_path)) {
            $messages[] = "Cache not writable, please check directory permissions ({$this->cache_path})";
        }

        // update check
        if ($url = $warp_xml->first('updateUrl')->text()) {

            // create check urls
            $urls['tmpl'] = sprintf('%s?application=%s&version=%s&format=raw', $url, $tmpl_xml->first('name')->text() . '_j25', $tmpl_xml->first('version')->text());
            $urls['warp'] = sprintf('%s?application=%s&version=%s&format=raw', $url, 'warp', $warp_xml->first('version')->text());

            foreach ($urls as $type => $url) {

                // only check once a day
                $hash = md5($url . date('Y-m-d'));
                if ($this['option']->get("{$type}_check") != $hash) {
                    if ($request = $this['http']->get($url)) {
                        $this['option']->set("{$type}_check", $hash);
                        $this['option']->set("{$type}_data", $request['body']);
                    }
                }

                // decode response and set message
                if (($data = json_decode($this['option']->get("{$type}_data"))) && $data->status == 'update-available') {
                    $messages[] = $data->message;
                }
            }
        }

        // set messages
        if (isset($messages)) {
            $this['template']->set('messages', $messages);
        }
    }

    /**
     * Ajax callback.
     */
    public function ajaxCallback($task)
    {
        switch ($task) {

            case 'config':

                // init vars
                $file = $this['path']->path('theme:') . '/config.json';
                $json = isset($_POST['config']) ? $_POST['config'] : '{}';

                // save config file
                $message = ($json and null !== $config = json_decode($json, true) and !empty($config) and File::write($file, $json)) ? 'success' : 'failed';

                break;

            case 'files':

                // init vars
                $upload = \JRequest::getVar('files', false, 'FILES');

                if (!$upload) {
                    $message = 'No file was uploaded.';
                    break;
                }

                if ($upload['error']) {
                    switch ($upload['error']) {
                        case UPLOAD_ERR_INI_SIZE:
                            $message = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
                            break;
                        case UPLOAD_ERR_FORM_SIZE:
                            $message = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
                            break;
                        case UPLOAD_ERR_PARTIAL:
                            $message = 'The uploaded file was only partially uploaded.';
                            break;
                        case UPLOAD_ERR_NO_FILE:
                            $message = 'No file was uploaded.';
                            break;
                        case UPLOAD_ERR_NO_TMP_DIR:
                            $message = 'Missing a temporary folder.';
                            break;
                        case UPLOAD_ERR_CANT_WRITE:
                            $message = 'Failed to write file to disk.';
                            break;
                        case UPLOAD_ERR_EXTENSION:
                            $message = 'A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help.';
                            break;
                    }
                    break;
                }

                if (false === $contents = file_get_contents($upload['tmp_name'])) {
                    $message = 'Unable to read contents from temporary file.';
                    break;
                }

                if (false === $contents = base64_decode($contents)) {
                    $message = 'Base64 Decode failed.';
                    break;
                }

                if (null === $files = json_decode($contents, true)) {
                    $message = 'Unable to decode JSON from temporary file.';
                    break;
                }

                $path = $this['path']->path('theme:');

                $message = 'success';

                foreach ($files as $file => $data) {
                    if (File::write($path . $file, $data) === false) {
                        $message = sprintf('Unable to write file (%s).', $path . $file);
                        break;
                    }
                }

                // delete obsolete styles
                if ($message == 'success' && $path = $this['path']->path('theme:styles')) {
                    foreach (glob("$path/*/style.less") as $dir) {

                        $dir = dirname($dir);

                        if (!isset($files['/styles/'.basename($dir).'/style.less'])) {
                            Folder::delete($dir);
                        }
                    }
                }

                break;

            case 'styles':

                // render styles config
                echo $this['template']->render('config:layouts/styles');

                return;
        }

        if (isset($message)) {
            echo json_encode(compact('message'));
        }
    }

    /**
     * Get Information of com_content
     */
    public function getPageInfo($args = array())
    {
        // get application
        $app = $this->application;
        $result = array(
            'tags' => array()
        );

        $tags = '';
        $tags_helper = new \JHelperTags();

        if ($app->input->get('option') == 'com_content') {

            if ($app->input->get('view') == 'category') {
                $catid = $app->input->getInt('id', 0);
            }
            if ($app->input->get('view') == 'article') {
                $catid = $app->input->getInt('catid', 0);
                $itemid = $app->input->getInt('id', 0);
            }

            if (isset($catid)) {
                $tags = $tags_helper->getItemTags('com_content.category', $catid);
            }
            if (empty($tags) && isset($itemid)) {
                $tags = $tags_helper->getItemTags('com_content.article', $itemid);
            }
        }

        if (is_array($tags)) {
            foreach ($tags as $tag) {
                $result['tags'][] = $tag->title;
            }
        }

        return $result;
    }

    /**
     * Is current view a blog?
     *
     * @return boolean
     */
    public function isBlog()
    {
        // get application
        $app = $this->application;

        if ($app->input->get('option') == 'com_tags') {
            if (in_array($app->input->get('view'), array('tag'))) {
                return true;
            }
        }

        if ($app->input->get('option') == 'com_content') {
            if (in_array($app->input->get('view'), array('frontpage', 'article', 'archive', 'featured')) || ($app->input->get('view') == 'category' && $app->input->get('layout') == 'blog')) {
                return true;
            }
        }

        if ($app->input->get('option') == 'com_zoo' && !in_array($app->input->get('task'), array('submission', 'mysubmissions')) && $a = \App::getInstance('zoo')->zoo->getApplication() and $a->getGroup() == 'blog') {
            return true;
        }

        return false;
    }
}
