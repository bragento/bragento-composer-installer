<?php
/**
 * Manager.php
 *
 * PHP Version 5
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Deploy
 * @author    David Verholen <david@verholen.com>
 * @copyright 2014 David Verholen
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */

namespace Bragento\Magento\Composer\Installer\Deploy;

use Bragento\Magento\Composer\Installer\Deploy\Manager\Entry;
use Bragento\Magento\Composer\Installer\Installer\Types;


/**
 * Class Manager
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Deploy
 * @author    David Verholen <david@verholen.com>
 * @copyright 2014 David Verholen
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
class Manager
{
    /**
     * instance
     *
     * @var Manager
     */
    protected static $_instance;

    /**
     * entries
     *
     * @var Entry[]
     */
    protected $_moduleEntries;

    /**
     * entries
     *
     * @var Entry[]
     */
    protected $_themeEntries;

    /**
     * _coreEntry
     *
     * @var Entry
     */
    protected $_coreEntry;

    /**
     * private construct for singleton
     */
    private function __construct()
    {
        $this->_moduleEntries = array();
        $this->_themeEntries = array();
    }

    /**
     * getInstance
     *
     * @return Manager
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new Manager();
        }

        return self::$_instance;
    }

    /**
     * addEntry
     *
     * only the first package with type
     * magento-core will be added
     *
     * @param Entry $entry the Entry to add
     *
     * @return void
     */
    public function addEntry(Entry $entry)
    {
        switch ($entry->getDeployStrategy()->getPackage()->getType()) {
            case Types::MAGENTO_CORE:
                $this->_coreEntry = $entry;
                break;

            case Types::MAGENTO_MODULE:
                $this->_moduleEntries[] = $entry;
                break;

            case Types::MAGENTO_THEME:
                $this->_themeEntries = $entry;
                break;
        }
    }

    /**
     * doDeploy
     *
     * deploy core, then modules, then themes
     *
     * @return void
     */
    public function doDeploy()
    {
        if (null !== $this->_coreEntry) {
            $this->_coreEntry->getDeployStrategy()->doDeploy();
            $this->_coreEntry = null;
        }

        while (count($this->_moduleEntries)) {
            /** @var Entry $moduleEntry */
            $moduleEntry = array_pop($this->_moduleEntries);
            $moduleEntry->getDeployStrategy()->doDeploy();
        }

        while (count($this->_themeEntries)) {
            /** @var Entry $themeEntry */
            $themeEntry = array_pop($this->_moduleEntries);
            $themeEntry->getDeployStrategy()->doDeploy();
        }
    }
} 
