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

use Composer\DependencyResolver\Operation\OperationInterface;
use Composer\EventDispatcher\Event;

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
     * @param string             $name
     * @param OperationInterface $operation
     */
    public function __construct(
        $name,
        OperationInterface $operation
    ) {
        parent::__construct($name);

        $this->operation = $operation;
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
}
