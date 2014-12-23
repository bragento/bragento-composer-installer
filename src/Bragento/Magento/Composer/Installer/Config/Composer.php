<?php
/**
 * Configuration.php
 *
 * PHP Version 5
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento_MagentoComposerInstaller
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */

namespace Bragento\Magento\Composer\Installer\Config;

use Bragento\Magento\Composer\Installer\Deploy\Strategy\Loader;
use Composer\Package\Package;
use Eloquent\Composer\Configuration\Element\Configuration as EloquentConfiguration;
use Underscore\Types\Arrays;

/**
 * Class Configuration
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento\Magento\Composer\Installer\Config
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */
class Composer extends EloquentConfiguration
{
    const DEPLOY_STRATEGY_KEY = 'magento-deploystrategy';
    const DEPLOY_STRATEGY_OVERWRITE_KEY = 'magento-deploystrategy-overwrite';
    const MAP_KEY = 'map';

    /**
     * getDeployStrategy
     *
     * @return string|null
     */
    public function getDeployStrategy()
    {
        return $this->getIfIsSet(
            $this->extra(),
            self::DEPLOY_STRATEGY_KEY,
            Loader::DEFAULT_STRATEGY
        );
    }

    /**
     * getDeployStrategyOverwrites
     *
     * @return array|null
     */
    public function getDeployStrategyOverwrites()
    {
        return $this->getIfIsSet(
            $this->extra(),
            self::DEPLOY_STRATEGY_OVERWRITE_KEY,
            []
        );
    }

    /**
     * getDeployStrategyOverwrite
     *
     * @param Package $package
     *
     * @return mixed
     */
    public function getDeployStrategyOverwrite(Package $package)
    {
        return $this->getIfIsSet(
            $this->getDeployStrategyOverwrites(),
            $package->getName(),
            Loader::DEFAULT_STRATEGY
        );
    }

    /**
     * getMap
     *
     * @return array
     */
    public function getMap()
    {
        return $this->getIfIsSet($this->extra(), self::MAP_KEY, []);
    }

    /**
     * getIfIsSet
     *
     * @param array $array
     * @param       $key
     * @param null  $default
     *
     * @return mixed
     */
    protected function getIfIsSet(array $array, $key, $default = null)
    {
        return Arrays::has($array, $key) ? $array[$key] : $default;
    }
}
