<?php
/**
 * DI.php
 *
 * PHP Version 5
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento_MagentoComposerInstaller
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */

namespace Bragento\Magento\Composer\Installer;

use Bragento\Magento\Composer\Installer\Config\Composer;
use Bragento\Magento\Composer\Installer\Config\Reader;
use Bragento\Magento\Composer\Installer\Deploy\Service;
use Bragento\Magento\Composer\Installer\Deploy\Strategy;
use Bragento\Magento\Composer\Installer\Deploy\Mapping;
use Composer\IO\IOInterface;
use DI\Container;
use DI\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Underscore\Types\Arrays;
use Underscore\Types\String;

/**
 * Class DI
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento\Magento\Composer\Installer
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */
class App
{
    const DI_NAMESPACE = '\\Bragento\\Magento\\Composer\\Installer\\DI\\';

    const AWARE_INTERFACE_SUFFIX = 'AwareInterface';
    const AWARE_INTERFACE_SETTER_PREFIX = 'set';

    const COMPOSER = 'Composer';
    const IOINTERFACE = 'IOInterface';
    const DEPLOYSERVICE = 'Deploy\Service';
    const FILESYSTEM = 'Filesystem';
    const STRATEGYLOADER = 'StrategyLoader';
    const MAPPINGLOADER = 'MappingLoader';
    const CONFIG = 'Config';

    const COMPOSER_JSON_PATH = './composer.json';

    /**
     * @var Container
     */
    private static $container;

    /**
     * @var bool
     */
    private static $initialized = false;

    /**
     * @var array
     */
    private static $awareInterfaces
        = [
            self::COMPOSER,
            self::IOINTERFACE,
            self::FILESYSTEM
        ];

    /**
     * get
     *
     * @param $name
     *
     * @return mixed|null
     * @throws \DI\NotFoundException
     */
    final public static function get($name)
    {
        if (false === self::$initialized
            || false === self::$container->has($name)
        ) {
            return null;
        }

        $object = self::$container->get($name);
        $object = self::injectAwareInterfaces($object);

        return $object;
    }

    /**
     * init
     *
     * @param \Composer\Composer $composer
     * @param IOInterface                 $io
     *
     */
    final public static function init(
        \Composer\Composer $composer,
        IOInterface $io
    ) {
        $containerBuilder = new ContainerBuilder();
        self::$container = $containerBuilder->build();

        self::$container->set(self::COMPOSER, $composer);
        self::$container->set(self::IOINTERFACE, $io);
        self::$container->set(self::DEPLOYSERVICE, new Service());
        self::$container->set(self::FILESYSTEM, new Filesystem());
        self::$container->set(self::STRATEGYLOADER, new Strategy\Loader());
        self::$container->set(self::MAPPINGLOADER, new Mapping\Loader());
        self::$container->set(
            self::CONFIG,
            (new Reader())->read(self::COMPOSER_JSON_PATH)
        );

        self::$initialized = true;
    }

    /**
     * injectAwareInterfaces
     *
     * @param $object
     *
     * @return mixed
     * @throws \DI\NotFoundException
     */
    protected static function injectAwareInterfaces($object)
    {
        foreach (self::$awareInterfaces as $awareInterface) {
            $interface = Arrays::implode([
                self::DI_NAMESPACE,
                String::toPascalCase($awareInterface),
                self::AWARE_INTERFACE_SUFFIX
            ]);

            $setter = Arrays::implode([
                self::AWARE_INTERFACE_SETTER_PREFIX,
                String::toPascalCase($awareInterface)
            ]);

            if ($object instanceof $interface) {
                $object->$setter(self::$container->get($awareInterface));
            }
        }

        return $object;
    }

    /**
     * getDeployService
     *
     * @return Service
     */
    public static function getDeployService()
    {
        return self::get(self::DEPLOYSERVICE);
    }

    /**
     * getComposer
     *
     * @return Composer
     */
    public static function getComposer()
    {
        return self::get(self::COMPOSER);
    }

    /**
     * getIOInterface
     *
     * @return IOInterface
     */
    public static function getIOInterface()
    {
        return self::get(self::IOINTERFACE);
    }

    /**
     * getStrategyLoader
     *
     * @return Strategy\Loader
     */
    public static function getStrategyLoader()
    {
        return self::get(self::STRATEGYLOADER);
    }

    /**
     * getStrategyLoader
     *
     * @return Mapping\Loader
     */
    public static function getMappingLoader()
    {
        return self::get(self::MAPPINGLOADER);
    }

    /**
     * getConfig
     *
     * @return Composer
     */
    public static function getConfig()
    {
        return self::get(self::CONFIG);
    }
}
