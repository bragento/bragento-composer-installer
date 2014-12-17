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
    /**
     * unix line break
     */
    const LINE_BREAK = '\r\n';

    /**
     * Directory Separators
     */
    const DS = '\\/';

    /**
     * Gitignore File Name
     */
    const FILENAME = '.gitignore';

    /**
     * @var Gitignore[]
     */
    protected static $instances;

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
    protected function __construct($filePath)
    {
        $this->filePath = $filePath;

        if (file_exists($filePath)) {
            $this->lines = array_flip(file($filePath, FILE_IGNORE_NEW_LINES));
        }
    }

    /**
     * edit
     *
     * @param $filePath
     *
     * @return Gitignore
     */
    public static function edit($filePath)
    {
        $filePath = rtrim(trim($filePath), self::DS);
        if (!isset(self::$instances[$filePath])) {
            self::$instances[$filePath] = new Gitignore($filePath);
        }

        return self::$instances[$filePath];
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
     * addEntries
     *
     * @param array $entries
     *
     * @return void
     */
    public function addEntries(array $entries)
    {
        foreach ($entries as $entry) {
            $this->addEntry($entry);
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
     * removeEntries
     *
     * @param array $entries
     *
     * @return void
     */
    public function removeEntries(array $entries)
    {
        foreach ($entries as $entry) {
            $this->removeEntry($entry);
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
