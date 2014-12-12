<?php
/**
 * Validate.php
 *
 * PHP Version 5
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento_MagentoComposerInstaller
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */

namespace Bragento\Magento\Composer\Installer\Deploy;

use Bragento\Magento\Composer\Installer\Project\Config;
use Bragento\Magento\Composer\Installer\Util\Filesystem;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;

/**
 * Class Validate
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento\Magento\Composer\Installer\Deploy
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */
class Validate implements EventSubscriberInterface
{
    /**
     * @var IOInterface
     */
    protected $io;

    /**
     * @var Filesystem
     */
    protected $fs;

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
     * * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::POST_DEPLOY => 'checkDeployment'
        );
    }

    /**
     * validateDeployment
     *
     * @return void
     */
    public function checkDeployment()
    {
        $root = Config::getInstance()->getMagentoRootDir();
        $deployedPackages = Manager::getInstance()->getDeployedPackages();
        foreach ($deployedPackages as $package) {
            $state = State::load($package);
            if (!isset($state[State::MAPPINGS_KEY])) {
                continue;
            }
            $map = $state[State::MAPPINGS_KEY];

            foreach ($map as $source => $destination) {
                $deployedFile = $this->getFs()
                    ->joinFileUris($root, $destination);

                if (file_exists($deployedFile)) {
                    if (is_link($deployedFile)) {
                        if (!readlink($deployedFile)) {
                            $this->getIo()
                                ->write(sprintf('<error>symlink broken: %s</error>',
                                        $deployedFile));
                        }
                    }
                } else {
                    $this->getIo()
                        ->write(sprintf('<error>file not found: %s</error>',
                                $deployedFile));
                }
            }
        }
    }

    /**
     * getFs
     *
     * @return Filesystem
     */
    protected function getFs()
    {
        if (null === $this->fs) {
            $this->fs = new Filesystem();
        }

        return $this->fs;
    }

    /**
     * getIo
     *
     * @return IOInterface
     */
    protected function getIo()
    {
        return $this->io;
    }
}
