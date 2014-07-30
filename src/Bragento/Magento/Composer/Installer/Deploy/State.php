<?php
/**
 * State.php
 *
 * PHP Version 5
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Deploy
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */

namespace Bragento\Magento\Composer\Installer\Deploy;

use Bragento\Magento\Composer\Installer\Deploy\Strategy\AbstractStrategy;
use Bragento\Magento\Composer\Installer\Project\Config;
use Bragento\Magento\Composer\Installer\Util\Filesystem;
use Composer\Package\PackageInterface;


/**
 * Class State
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Deploy
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
class State
{
    const MAPPINGS_KEY = 'mappings';

    /**
     * target dir for state files
     */
    const STATE_DIR = '.mage-deploy';

    /**
     * _fs
     *
     * @var Filesystem
     */
    protected $_fs;

    /**
     * _state
     *
     * @var array
     */
    protected $_state;

    /**
     * package
     *
     * @var PackageInterface
     */
    protected $_package;

    /**
     * _deployStrategy
     *
     * @var AbstractStrategy
     */
    protected $_deployStrategy;

    /**
     * _saveOnDestruct
     *
     * @var Boolean
     */
    protected $_saveOnDestruct;

    /**
     * load on construct
     *
     * @param AbstractStrategy $strategy
     */
    function __construct(AbstractStrategy $strategy)
    {
        $this->_saveOnDestruct = true;
        $this->_deployStrategy = $strategy;
        $this->_state = $this->load();
    }

    /**
     * save on destruct
     */
    function __destruct()
    {
        if ($this->_saveOnDestruct) {
            $this->_save($this->_state);
        }
    }

    /**
     * setMapping
     *
     * @param array $map
     *
     * @return void
     */
    public function setMapping(array $map)
    {
        $this->set(self::MAPPINGS_KEY, $map);
    }

    /**
     * getMapping
     *
     * @return mixed
     */
    public function getMapping()
    {
        return $this->get(self::MAPPINGS_KEY);
    }

    /**
     * set
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    protected function set($key, $value)
    {
        $this->_state[$key] = $value;
    }

    /**
     * get
     *
     * @param $key
     *
     * @return mixed
     */
    protected function get($key)
    {
        return isset($this->_state[$key])
            ? $this->_state[$key]
            : null;
    }

    /**
     * save
     *
     * @param array $state
     *
     * @return void
     */
    protected function _save(array $state)
    {
        $this->getFs()->ensureDirectoryExists(
            dirname($this->getStateFilePath())
        );
        file_put_contents(
            $this->getStateFilePath(),
            json_encode($state)
        );
    }

    /**
     * save
     *
     * public function for manual save, since hhvm does not
     * support automated invocation of __destruct on
     * shutdown
     *
     * @return void
     *
     * @todo check if hhvm now supports automated __destruct
     */
    public function save()
    {
        $this->_save($this->_state);
    }

    /**
     * delete
     *
     * @return void
     */
    public function delete()
    {
        if (file_exists($this->getStateFilePath())) {
            unlink($this->getStateFilePath());
        }
        $this->_saveOnDestruct = false;
    }

    /**
     * load
     *
     * @return array
     */
    protected function load()
    {
        if (file_exists($this->getStateFilePath())) {
            return (array)json_decode(
                file_get_contents(
                    $this->getStateFilePath()
                )
            );
        }

        return array();
    }

    /**
     * getFs
     *
     * @return Filesystem
     */
    protected function getFs()
    {
        if (null === $this->_fs) {
            $this->_fs = new Filesystem();
        }

        return $this->_fs;
    }

    /**
     * getMappingsFilePath
     *
     * @return string
     */
    protected function getStateFilePath()
    {
        $mappingsDir = $this->getFs()->joinFileUris(
            Config::getInstance()->getVendorDir(),
            self::STATE_DIR
        );

        return $this->getFs()->joinFileUris(
            $mappingsDir,
            str_replace('/', '_', $this->getPackage()->getName())
        );
    }

    /**
     * getPackage
     *
     * @return PackageInterface
     */
    protected function getPackage()
    {
        if (null === $this->_package) {
            $this->_package = $this->getDeployStrategy()->getPackage();
        }
        return $this->_package;
    }

    /**
     * getDeployStrategy
     *
     * @return AbstractStrategy
     */
    protected function getDeployStrategy()
    {
        return $this->_deployStrategy;
    }
} 
