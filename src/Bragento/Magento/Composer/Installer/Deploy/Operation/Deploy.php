<?php
/**
 * Deploy.php
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

namespace Bragento\Magento\Composer\Installer\Deploy\Operation;

use Composer\DependencyResolver\Operation\OperationInterface;
use Composer\Package\PackageInterface;
use Symfony\Component\Finder\SplFileInfo;


/**
 * Class Deploy
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Deploy\Operation
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
class Deploy implements OperationInterface
{
    /**
     * package
     *
     * @var PackageInterface
     */
    protected $_package;

    /**
     * deployAction
     *
     * @var string
     */
    protected $_deployAction;

    /**
     * sourceDir
     *
     * @var SplFileInfo
     */
    protected $_sourceDir;

    /**
     * destDir
     *
     * @var SplFileInfo
     */
    protected $_destDir;

    /**
     * construct deploy Operation
     *
     * @param PackageInterface $package      Package to deploy
     * @param string           $deployAction Deploy Action
     * @param SplFileInfo      $sourceDir    Source Directory
     * @param SplFileInfo      $destDir      Destination Directory
     */
    function __construct(
        PackageInterface $package,
        $deployAction,
        SplFileInfo $sourceDir,
        SplFileInfo $destDir
    ) {
        $this->_package = $package;
        $this->_deployAction = $deployAction;
        $this->_sourceDir = $sourceDir;
        $this->_destDir = $destDir;
    }

    /**
     * Returns job type.
     *
     * @return string
     */
    public function getJobType()
    {
        return sprintf(
            "%s-%s",
            $this->_package->getType(),
            $this->_deployAction
        );
    }

    /**
     * Returns operation reason.
     *
     * @return string
     */
    public function getReason()
    {
        return sprintf(
            "%s %s Deployment",
            $this->_package->getType(),
            $this->_deployAction
        );
    }

    /**
     * Serializes the operation in a human readable format
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            "Deployment of %s %s (%s) [%s]",
            $this->_package->getType(),
            $this->_package->getName(),
            $this->_package->getPrettyVersion(),
            $this->_deployAction
        );
    }

    /**
     * getPackage
     *
     * @return PackageInterface
     */
    public function getPackage()
    {
        return $this->_package;
    }

    /**
     * DestDir
     *
     * @return SplFileInfo
     */
    public function getDestDir()
    {
        return $this->_destDir;
    }

    /**
     * SourceDir
     *
     * @return SplFileInfo
     */
    public function getSourceDir()
    {
        return $this->_sourceDir;
    }

    /**
     * getDeployAction
     *
     * @return string
     */
    public function getDeployAction()
    {
        return $this->_deployAction;
    }
}
