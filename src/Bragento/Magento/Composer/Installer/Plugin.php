<?php
/**
 * Plugin.php
 *
 * PHP Version 5
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer
 * @author    David Verholen <david@verholen.com>
 * @copyright 2014 David Verholen
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */

namespace Bragento\Magento\Composer\Installer;

use Bragento\Magento\Composer\Installer\Deploy\Manager;
use Bragento\Magento\Composer\Installer\Installer;
use Bragento\Magento\Composer\Installer\Project\Config;
use Bragento\Magento\Composer\Installer\Util\Filesystem;
use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Script\CommandEvent;
use Composer\Plugin\PluginInterface;
use Composer\Script\ScriptEvents;


/**
 * Class Plugin
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer
 * @author    David Verholen <david@verholen.com>
 * @copyright 2014 David Verholen
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
class Plugin implements PluginInterface, EventSubscriberInterface
{
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
     * * array('eventName' => array(array(
     * *    'methodName1',
     * *    $priority
     * *), array('methodName2'))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            ScriptEvents::POST_INSTALL_CMD => 'onPostInstallCmd',
            ScriptEvents::POST_UPDATE_CMD  => 'onPostUpdateCmd'
        );
    }

    /**
     * Apply plugin modifications to composer
     *
     * @param Composer    $composer Composer Instance
     * @param IOInterface $io       IO Interface
     *
     * @return void
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        Deploy\Strategy\Factory::init($composer, $io);

        Installer\Factory::init(
            $composer,
            $io,
            Deploy\Manager::getInstance(),
            new Filesystem()
        );

        Config::init($composer);

        $composer->getInstallationManager()->addInstaller(
            Installer\Factory::get(Installer\Types::MAGENTO_CORE)
        );

        $composer->getInstallationManager()->addInstaller(
            Installer\Factory::get(Installer\Types::MAGENTO_MODULE)
        );

        $composer->getInstallationManager()->addInstaller(
            Installer\Factory::get(Installer\Types::MAGENTO_THEME)
        );

        $composer->getEventDispatcher()
            ->addSubscriber(new Deploy\OutputSubscriber($io));

        $composer->getEventDispatcher()
            ->addSubscriber(new Updater\Core());
    }

    /**
     * onPostInstallCmd
     *
     * @param CommandEvent $event
     *
     * @return void
     */
    public function onPostInstallCmd(CommandEvent $event)
    {
        Deploy\Manager::getInstance()->doDeploy();
    }

    /**
     * onPostUpdateCmd
     *
     * @param CommandEvent $event
     *
     * @return void
     */
    public function onPostUpdateCmd(CommandEvent $event)
    {
        Deploy\Manager::getInstance()->doDeploy();
    }
} 
