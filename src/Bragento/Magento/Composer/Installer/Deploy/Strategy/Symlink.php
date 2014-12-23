<?php
/**
 * Symlink.php
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

use Bragento\Magento\Composer\Installer\Deploy\Mapping\Mappable;
use Bragento\Magento\Composer\Installer\Deploy\Mapping\MappableTrait;
use Bragento\Magento\Composer\Installer\DI\FilesystemAwareInterface;
use Bragento\Magento\Composer\Installer\DI\FilesystemAwareTrait;

/**
 * Class Symlink
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento\Magento\Composer\Installer\Deploy\Strategy
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */
class Symlink implements Deployable, Mappable, FilesystemAwareInterface
{
    use FilesystemAwareTrait;
    use MappableTrait;

    /**
     * deploy
     *
     * @return mixed
     */
    public function deploy()
    {
        foreach ($this->getMapping() as $source => $target) {
            $this->getFilesystem()->symlink(
                $this->getFilesystem()->makePathRelative($target, $source),
                $target
            );
        }
    }
}
