<?php
/**
 * OutputSubscriber.php
 *
 * PHP Version 5
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Deploy\Operation
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */

namespace Bragento\Magento\Composer\Installer\Deploy;

use Bragento\Magento\Composer\Installer\Deploy\Event\DeployEvent;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Script\PackageEvent;

/**
 * Class OutputSubscriber
 *
 * prints all deploy operations to output stream
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Deploy\Operation
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
class OutputSubscriber implements EventSubscriberInterface
{
    /**
     * _io
     *
     * @var IOInterface
     */
    protected $io;

    /**
     * @param IOInterface $io
     */
    public function __construct(IOInterface $io)
    {
        $this->io = $io;
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
            Events::PRE_DEPLOY_CORE_INSTALL      => 'onPreDeploy',
            Events::PRE_DEPLOY_CORE_UPDATE       => 'onPreDeploy',
            Events::PRE_DEPLOY_CORE_UNINSTALL    => 'onPreDeploy',
            Events::PRE_DEPLOY_MODULE_INSTALL    => 'onPreDeploy',
            Events::PRE_DEPLOY_MODULE_UPDATE     => 'onPreDeploy',
            Events::PRE_DEPLOY_MODULE_UNINSTALL  => 'onPreDeploy',
            Events::PRE_DEPLOY_THEME_INSTALL     => 'onPreDeploy',
            Events::PRE_DEPLOY_THEME_UPDATE      => 'onPreDeploy',
            Events::PRE_DEPLOY_THEME_UNINSTALL   => 'onPreDeploy'
        );
    }

    /**
     * onPreDeploy
     *
     * use operations toString method to output deploy events
     *
     * @param DeployEvent $event
     *
     */
    public function onPreDeploy(DeployEvent $event)
    {
        $operation = $event->getOperation();
        $this->io->write(sprintf("<info>%s</info>", $operation));
    }
}
