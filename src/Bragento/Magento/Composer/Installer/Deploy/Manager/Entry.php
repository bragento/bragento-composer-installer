<?php
/**
 * Entry.php
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

namespace Bragento\Magento\Composer\Installer\Deploy\Manager;

use Bragento\Magento\Composer\Installer\Deploy\Strategy\AbstractStrategy;
use Bragento\Magento\Composer\Installer\Deploy\Strategy;


/**
 * Class Entry
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Deploy
 * @author    David Verholen <david@verholen.com>
 * @copyright 2014 David Verholen
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
class Entry
{
    /**
     * deployStrategy
     *
     * @var AbstractStrategy
     */
    protected $_deployStrategy;

    /**
     * entry constructor
     *
     * @param Strategy\AbstractStrategy $deployStrategy deploy strategy
     */
    function __construct(
        AbstractStrategy $deployStrategy
    )
    {
        $this->_deployStrategy = $deployStrategy;
    }

    /**
     * DeployStrategy
     *
     * @return AbstractStrategy
     */
    public function getDeployStrategy()
    {
        return $this->_deployStrategy;
    }
} 
