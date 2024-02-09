<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

namespace Warp\Helper;

use Warp\Warp;

/**
 * Helper base class.
 */
abstract class AbstractHelper implements \ArrayAccess
{
    /**
     * @var Warp
     */
    protected $warp;

    /**
     * Constructor.
     *
     * @param Warp $warp
     */
    public function __construct(Warp $warp)
    {
        $this->warp = $warp;
    }

    /* ArrayAccess interface implementation */

    public function offsetGet(mixed $name) : mixed
    {
        return $this->warp[$name];
    }

    public function offsetSet(mixed $name,mixed $helper) : void
    {
        $this->warp[$name] = $helper;
    }

    public function offsetUnset(mixed $name) : void
    {
        unset($this->warp[$name]);
    }

    public function offsetExists(mixed $name) : bool
    {
        return !empty($this->warp[$name]);
    }
}
