<?php
/**
 * MapEntity.php
 *
 * PHP Version 5
 *
 * @category bragento-composer-installer
 * @package  bragento-composer-installer
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */

namespace Bragento\Magento\Composer\Installer\Mapping;

/**
 * Class MapEntity
 *
 * @category bragento-composer-installer
 * @package  Bragento\Magento\Composer\Installer\Mapping
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */
class MapEntity
{
    /**
     * @var string
     */
    protected $source;

    /**
     * @var string
     */
    protected $target;

    /**
     * MapEntity constructor.
     *
     * @param string $source
     * @param string $target
     */
    public function __construct($source, $target)
    {
        $this->source = $source;
        $this->target = $target;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param string $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }
}
