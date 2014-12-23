<?php
/**
 * Loader.php
 *
 * PHP Version 5
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento_MagentoComposerInstaller
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */

namespace Bragento\Magento\Composer\Installer\Deploy\Strategy;

use Bragento\Magento\Composer\Installer\App;
use Bragento\Magento\Composer\Installer\Deploy\Mapping\Mappable;
use Composer\Package\Package;
use Underscore\Types\Arrays;

/**
 * Class Loader
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento\Magento\Composer\Installer\Deploy\Strategy
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */
class Loader
{
    const COPY = 'copy';
    const SYMLINK = 'symlink';
    const NONE = 'none';

    const DEFAULT_STRATEGY = self::SYMLINK;

    /**
     * @var array
     */
    protected $strategyMap
        = [
            self::COPY    => '\\Bragento\\Magento\\Composer\\Installer\\Deploy\\Strategy\\Copy',
            self::SYMLINK => '\\Bragento\\Magento\\Composer\\Installer\\Deploy\\Strategy\\Symlink',
            self::NONE    => '\\Bragento\\Magento\\Composer\\Installer\\Deploy\\Strategy\\None'
        ];

    /**
     * @var array
     */
    protected $strategyCache = [];

    /**
     * loadDeployStrategy
     *
     * @param Package $package
     *
     * @return Deployable
     */
    public function loadDeployStrategy(Package $package)
    {
        if (false === Arrays::has($this->strategyCache, $package->getName())) {
            $strategy = $this->getStrategy($package);
            $this->validateStrategy($strategy);
            /** @var Deployable|Mappable $strategyClass */
            $strategyClass = new $this->strategyMap[$strategy]();
            $strategyClass->setMapping($this->getMapping($package));
            $this->strategyCache[$package->getName()] = $package;
        }

        return $this->strategyCache[$package->getName()];
    }

    /**
     * getMapping
     *
     * @param Package $package
     *
     * @return array
     */
    protected function getMapping(Package $package)
    {
        return App::getMappingLoader()->loadMapping($package);
    }

    /**
     * validateStrategy
     *
     * @param $strategy
     *
     * @return bool
     * @throws DeployStrategyNotFoundException
     */
    protected function validateStrategy($strategy)
    {
        if (!Arrays::has($this->strategyMap, $strategy)) {
            throw new DeployStrategyNotFoundException($strategy);
        }
    }

    /**
     * getGlobalStrategy
     *
     * @return string
     */
    protected function getGlobalStrategy()
    {
        return App::getConfig()->getDeployStrategy();
    }

    /**
     * getPackageStrategy
     *
     * @param Package $package
     *
     * @return string
     */
    protected function getPackageStrategy(Package $package)
    {
        return App::getConfig()->getDeployStrategyOverwrite($package);
    }

    /**
     * getStrategy
     *
     * @param Package $package
     *
     * @return string
     */
    protected function getStrategy(Package $package)
    {
        return
            $this->getGlobalStrategy() !== $this->getPackageStrategy($package)
                ? $this->getPackageStrategy($package)
                : $this->getGlobalStrategy();
    }
}
