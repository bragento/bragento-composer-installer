<?php
/**
 * Plugin.php
 *
 * PHP Version 5
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */

namespace Bragento\Magento\Composer\Installer;

use Bragento\Magento\Composer\Installer\Deploy\Manager;
use Bragento\Magento\Composer\Installer\Project\Config;
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
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
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
        Deploy\Manager::init($composer);
        Config::init($composer);

        $this->initEventSubscribers($composer, $io);
    }

    /**
     * initEventSubscribers
     *
     * @param Composer    $composer
     * @param IOInterface $io
     *
     * @return void
     */
    protected function initEventSubscribers(Composer $composer, IOInterface $io)
    {
        $ed = $composer->getEventDispatcher();
        $ed->addSubscriber(Deploy\Manager::getInstance());
        $ed->addSubscriber(new Deploy\OutputSubscriber($io));
        $ed->addSubscriber(new Updater\Core());
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
        $event->getIO()->write('<info>post install:</info>');
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
        $event->getIO()->write('<info>post update:</info>');
        Deploy\Manager::getInstance()->doDeploy();
    }
} 
