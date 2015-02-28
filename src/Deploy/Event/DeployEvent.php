<?php
/**
 * DeployEvent.php
 *
 * PHP Version 5
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento_MagentoComposerInstaller
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */

namespace Bragento\Magento\Composer\Installer\Deploy\Event;

use Composer\Composer;
use Composer\DependencyResolver\Operation\OperationInterface;
use Composer\EventDispatcher\Event;
use Composer\IO\IOInterface;

/**
 * Class DeployEvent
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento\Magento\Composer\Installer\Deploy\Event
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */
class DeployEvent extends Event
{

    /**
     * @var OperationInterface
     */
    protected $operation;

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @var IOInterface
     */
    protected $io;

    /**
     * @param string             $name
     * @param Composer           $composer
     * @param IOInterface        $io
     * @param OperationInterface $operation
     */
    public function __construct(
        $name,
        Composer $composer,
        IOInterface $io,
        OperationInterface $operation
    ) {
        parent::__construct($name);

        $this->operation = $operation;
        $this->composer = $composer;
        $this->io = $io;
    }

    /**
     * @return OperationInterface
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * @param OperationInterface $operation
     */
    public function setOperation($operation)
    {
        $this->operation = $operation;
    }

    /**
     * @return Composer
     */
    public function getComposer()
    {
        return $this->composer;
    }

    /**
     * @param Composer $composer
     */
    public function setComposer($composer)
    {
        $this->composer = $composer;
    }

    /**
     * @return IOInterface
     */
    public function getIo()
    {
        return $this->io;
    }

    /**
     * @param IOInterface $io
     */
    public function setIo($io)
    {
        $this->io = $io;
    }
}
