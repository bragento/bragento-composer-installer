<?php
/**
 * Uri.php
 *
 * PHP Version 5
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento_MagentoComposerInstaller
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */

namespace Bragento\Magento\Composer\Installer\Util;

use Underscore\Types\Arrays;
use Underscore\Types\String;

/**
 * Class Uri
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento\Magento\Composer\Installer\Util
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */
class Uri
{
    const DS = DIRECTORY_SEPARATOR;

    /**
     * join
     *
     * @param array $parts
     *
     * @return mixed
     */
    public static function join(array $parts)
    {
        $normalizedParts = [];
        foreach ($parts as $part) {
            $normalizedParts[] = self::getParts($part);
        }
        return self::normalize(
            Arrays::implode(
                self::DS,
                $normalizedParts
            )
        );
    }

    /**
     * getParts
     *
     * @param $path
     *
     * @return array
     */
    public function getParts($path)
    {
        return preg_split(
            '/\\\|\//',
            self::normalize($path),
            null,
            PREG_SPLIT_NO_EMPTY
        );
    }

    /**
     * normalize
     *
     * @param $path
     *
     * @return mixed
     */
    public static function normalize($path)
    {
        $path = String::replace(
            $path,
            array('/./', '\\.\\', '\\'),
            DIRECTORY_SEPARATOR
        );

        $doubleDs = DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR;
        do {
            $path = str_replace($doubleDs, DIRECTORY_SEPARATOR, $path);
        } while (false !== strpos($path, $doubleDs));

        return $path;
    }
}
