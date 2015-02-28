<?php
/**
 * Core.php
 *
 * PHP Version 5
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Updater
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */

namespace Bragento\Magento\Composer\Installer\Updater;

use Bragento\Magento\Composer\Installer\Deploy\Event\DeployEvent;
use Bragento\Magento\Composer\Installer\Deploy\Events;
use Bragento\Magento\Composer\Installer\Project\Config;
use Bragento\Magento\Composer\Installer\Util\Filesystem;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Script\PackageEvent;

/**
 * Class Core
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Updater
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
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
    protected $persistent
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
    protected $backupDir;

    /**
     * _fs
     *
     * @var Filesystem
     */
    protected $fs;

    /**
     * construct
     */
    public function __construct()
    {
        $this->fs = new Filesystem();
        do {
            $this->backupDir = uniqid('bkp');
        } while (file_exists($this->backupDir));
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
            Events::PRE_DEPLOY_CORE_UPDATE     => 'backupFiles',
            Events::PRE_DEPLOY_CORE_UNINSTALL  => 'backupFiles',
            Events::PRE_DEPLOY_CORE_INSTALL    => 'backupFiles',
            Events::POST_DEPLOY_CORE_UPDATE    => 'restoreBackup',
            Events::POST_DEPLOY_CORE_UNINSTALL => 'restoreBackup',
            Events::POST_DEPLOY_CORE_INSTALL   => 'restoreBackup'
        );
    }

    /**
     * onPreDeployCoreUpdate
     *
     * @param DeployEvent $event
     *
     * @return void
     */
    public function backupFiles(DeployEvent $event)
    {
        $this->printInfo('backup persistent files', $event->getIO());
        $this->getFs()->ensureDirectoryExists($this->getBackupDir());
        $this->moveFiles(
            Config::getInstance()->getMagentoRootDir(),
            $this->getBackupDir(),
            $event->getIO()
        );
    }

    /**
     * onPostDeployCoreUpdate
     *
     * @param DeployEvent $event
     *
     * @return void
     */
    public function restoreBackup(DeployEvent $event)
    {
        $this->printInfo('restore persistent files', $event->getIO());
        $this->moveFiles(
            $this->getBackupDir(),
            Config::getInstance()->getMagentoRootDir(),
            $event->getIO()
        );
        $this->getFs()->remove($this->getBackupDir());
    }

    /**
     * printInfo
     *
     * @param string      $text
     * @param IOInterface $io
     *
     * @return void
     */
    protected function printInfo($text, IOInterface $io)
    {
        $io->write(sprintf("<info>%s</info>", $text));
    }

    /**
     * getFs
     *
     * @return Filesystem
     */
    protected function getFs()
    {
        return $this->fs;
    }

    /**
     * getBackupDir
     *
     * @return string
     */
    protected function getBackupDir()
    {
        return $this->backupDir;
    }

    /**
     * moveFiles
     *
     * @param             $sourceRoot
     * @param             $targetRoot
     * @param IOInterface $io
     *
     * @return void
     */
    protected function moveFiles($sourceRoot, $targetRoot, IOInterface $io)
    {
        foreach ($this->getFiles() as $item) {
            $source = $this->getFs()->joinFileUris($sourceRoot, $item);
            $target = $this->getFs()->joinFileUris($targetRoot, $item);
            if (file_exists($source) && !is_link($source)) {
                $this->printInfo($item, $io);
                if (file_exists($target)) {
                    $this->getFs()->remove(
                        $target
                    );
                }
                $this->getFs()->ensureDirectoryExists(dirname($target));
                $this->getFs()->rename(
                    $source,
                    $target
                );
            }
        }
    }

    /**
     * getFiles
     *
     * @return array
     */
    protected function getFiles()
    {
        return array_merge(
            $this->persistent,
            Config::getInstance()->getPersistentFiles()
        );
    }
}
