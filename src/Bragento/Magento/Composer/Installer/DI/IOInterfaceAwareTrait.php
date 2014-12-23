<?php
/**
 * IOInterfaceAwareTrait.php
 *
 * PHP Version 5
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento_MagentoComposerInstaller
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */

namespace Bragento\Magento\Composer\Installer\DI;

use Composer\IO\IOInterface;

trait IOInterfaceAwareTrait
{
    /**
     * @var IOInterface
     */
    private $ioInterface;

    /**
     * setIOInterface
     *
     * @param IOInterface $io
     *
     * @return void
     */
    public function setIOInterface(IOInterface $io)
    {
        $this->ioInterface = $io;
    }

    /**
     * getIOInterface
     *
     * @return IOInterface
     */
    public function getIOInterface()
    {
        return $this->ioInterface;
    }

    /**
     * write
     *
     * @param        $messages
     * @param string $type
     */
    public function write($messages, $type = 'info')
    {
        $this->ioInterface->write("<{$type}>" . $messages . "</{$type}>");
    }

    /**
     * writeVerbose
     *
     * @param        $messages
     * @param string $type
     */
    public function writeVerbose($messages, $type = 'info')
    {
        if ($this->ioInterface->isVerbose()) {
            $this->write($messages, $type);
        }
    }

    /**
     * writeVeryVerbose
     *
     * @param        $messages
     * @param string $type
     *
     * @return void
     */
    public function writeVeryVerbose($messages, $type = 'info')
    {
        if ($this->ioInterface->isVeryVerbose()) {
            $this->write($messages, $type);
        }
    }

    /**
     * writeDebug
     *
     * @param        $messages
     * @param string $type
     *
     * @return void
     */
    public function writeDebug($messages, $type = 'info')
    {
        if ($this->ioInterface->isDebug()) {
            $this->write($messages, $type);
        }
    }
}
