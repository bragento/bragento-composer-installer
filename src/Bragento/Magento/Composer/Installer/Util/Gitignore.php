<?php
/**
 * Gitignore.php
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

/**
 * Class Gitignore
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento\Magento\Composer\Installer\Util
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */
class Gitignore
{
    const LINE_BREAK = '\r\n';
    /**
     * @var string[]
     */
    protected $lines;

    /**
     * @var boolean
     */
    protected $hasChanges;

    /**
     * @var
     */
    protected $filePath;

    /**
     * @param string $filePath
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;

        if (file_exists($filePath)) {
            $this->lines = array_flip(file($filePath, FILE_IGNORE_NEW_LINES));
        }
    }

    /**
     * __desctruct
     *
     * @return void
     */
    public function __desctruct()
    {
        if ($this->hasChanges()) {
            file_put_contents(
                $this->getFilePath(),
                implode(
                    self::LINE_BREAK,
                    array_flip($this->lines)
                )
            );
        }
    }

    /**
     * addEntry
     *
     * @param string $entry
     *
     * @return void
     */
    public function addEntry($entry)
    {
        $entry = $this->normalizeEntry($entry);
        if (!$this->hasEntry($entry)) {
            $this->lines[$entry] = $entry;
            $this->setHasChanges();
        }
    }

    /**
     * removeEntry
     *
     * @param string $entry
     *
     * @return void
     */
    public function removeEntry($entry)
    {
        $entry = $this->normalizeEntry($entry);
        if (!$this->hasEntry($entry)) {
            unset($this->lines[$entry]);
            $this->setHasChanges();
        }
    }

    /**
     * hasEntry
     *
     * @param $entry
     *
     * @return bool
     */
    public function hasEntry($entry)
    {
        return isset($this->lines[$this->normalizeEntry($entry)]);
    }

    /**
     * getFilePath
     *
     * @return string
     */
    protected function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * normalizeEntry
     *
     * @param $entry
     *
     * @return string
     */
    protected function normalizeEntry($entry)
    {
        return trim($entry);
    }

    /**
     * setHasChanges
     *
     * @return void
     */
    protected function setHasChanges()
    {
        $this->hasChanges = true;
    }

    /**
     * unsetHasChanges
     *
     * @return void
     */
    protected function unsetHasChanges()
    {
        $this->hasChanges = false;
    }

    /**
     * hasChanges
     *
     * @return bool
     */
    protected function hasChanges()
    {
        return $this->hasChanges;
    }
}
