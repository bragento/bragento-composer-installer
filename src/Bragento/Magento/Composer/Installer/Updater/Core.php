<?php
/**
 * Core.php
 *
 * PHP Version 5
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Updater
 * @author    David Verholen <david@verholen.com>
 * @copyright 2014 David Verholen
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */

namespace Bragento\Magento\Composer\Installer\Updater;

use Bragento\Magento\Composer\Installer\Deploy\Events;
use Bragento\Magento\Composer\Installer\Project\Config;
use Bragento\Magento\Composer\Installer\Deploy;
use Bragento\Magento\Composer\Installer\Util\Filesystem;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Script\PackageEvent;


/**
 * Class Core
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Updater
 * @author    David Verholen <david@verholen.com>
 * @copyright 2014 David Verholen
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
class Core implements EventSubscriberInterface
{
    /**
     * _persistent
     *
     * files and directories that should persist
     * over core update
     *
     * @var array
     */
    protected $_persistent
        = array(
            'var',
            'media',
            'app/etc/local.xml'
        );

    /**
     * _backupDir
     *
     * @var string
     */
    protected $_backupDir;

    /**
     * _fs
     *
     * @var Filesystem
     */
    protected $_fs;

    function __construct()
    {
        $this->_fs = new Filesystem();
        do {
            $this->_backupDir = uniqid('bkp');
        } while (file_exists($this->_backupDir));
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     * * The method name to call (priority defaults to 0)
     * * An array composed of the method name to call and the priority
     * * An array of arrays composed of the method names to call and respective
     *   priorities, or 0 if unset
     *
     * For instance:
     *
     * * array('eventName' => 'methodName')
     * * array('eventName' => array('methodName', $priority))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::PRE_DEPLOY_CORE_UPDATE  => 'onPreDeployCoreUpdate',
            Events::POST_DEPLOY_CORE_UPDATE => 'onPostDeployCoreUpdate'
        );
    }

    /**
     * onPreDeployCoreUpdate
     *
     * @param PackageEvent $event
     *
     * @return void
     */
    public function onPreDeployCoreUpdate(PackageEvent $event)
    {
        $this->getFs()->ensureDirectoryExists($this->_backupDir);
        foreach ($this->_persistent as $dir) {
            if (file_exists($this->getMagentoSubDir($dir))) {
                $this->getFs()->ensureDirectoryExists(
                    dirname($this->getBackupSubDir($dir))
                );
                $this->getFs()->rename(
                    $this->getMagentoSubDir($dir),
                    $this->getBackupSubDir($dir)
                );
            }
        }
    }

    /**
     * onPostDeployCoreUpdate
     *
     * @param PackageEvent $event
     *
     * @return void
     */
    public function onPostDeployCoreUpdate(PackageEvent $event)
    {
        foreach ($this->_persistent as $dir) {
            if (file_exists($this->getBackupSubDir($dir))) {
                if (file_exists($this->getMagentoSubDir($dir))) {
                    if (is_dir($this->getMagentoSubDir($dir))) {
                        $this->getFs()->rremove(
                            $this->getMagentoSubDir($dir)
                        );
                    } else {
                        unlink($this->getMagentoSubDir($dir));
                    }
                }
                $this->getFs()->rename(
                    $this->getBackupSubDir($dir),
                    $this->getMagentoSubDir($dir)
                );
            }
        }
        $this->getFs()->rremove($this->_backupDir);
    }

    /**
     * getMagentoSubDir
     *
     * @param $dir
     *
     * @return string
     */
    protected function getMagentoSubDir($dir)
    {
        return Config::getInstance()->getMagentoRootDir()
        . DIRECTORY_SEPARATOR
        . $dir;
    }

    /**
     * getBackupSubDir
     *
     * @param $dir
     *
     * @return string
     */
    protected function getBackupSubDir($dir)
    {
        return $this->_backupDir
        . DIRECTORY_SEPARATOR
        . $dir;
    }

    /**
     * getFs
     *
     * @return Filesystem
     */
    protected function getFs()
    {
        return $this->_fs;
    }
}
