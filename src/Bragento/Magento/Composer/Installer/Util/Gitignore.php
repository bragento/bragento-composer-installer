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
    const LINE_BREAK = "\r\n";

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
        $this->reload();
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
    public function __destruct()
    {
        $this->persist();
    }

    /**
     * reload
     *
     * @return $this
     */
    public function reload()
    {
        $this->lines = array();
        if (file_exists($this->getFilePath())) {
            $this->lines = array_flip(
                file(
                    $this->getFilePath(),
                    FILE_IGNORE_NEW_LINES
                )
            );
        }

        return $this;
    }

    /**
     * saveChanges
     *
     * @return void
     */
    public function persist()
    {
        if ($this->hasChanges()) {
            file_put_contents(
                $this->getFilePath(),
                implode(
                    self::LINE_BREAK,
                    $this->getEntries()
                )
            );
        }
        $this->unsetHasChanges();
        $this->reload();
    }

    /**
     * addEntry
     *
     * @param string $entry
     *
     * @return $this
     */
    public function addEntry($entry)
    {
        $entry = $this->normalizeEntry($entry);
        if (!$this->isIgnored($entry)) {
            $this->lines[$entry] = $entry;
            $this->setHasChanges();
        }

        return $this;
    }

    /**
     * addEntries
     *
     * @param array $entries
     *
     * @return $this
     */
    public function addEntries(array $entries)
    {
        foreach ($entries as $entry) {
            $this->addEntry($entry);
        }

        return $this;
    }

    /**
     * removeEntry
     *
     * @param string $entry
     *
     * @return $this
     */
    public function removeEntry($entry)
    {
        $entry = $this->normalizeEntry($entry);
        if ($this->hasEntry($entry)) {
            unset($this->lines[$entry]);
            $this->setHasChanges();
        }

        return $this;
    }

    /**
     * removeEntries
     *
     * @param array $entries
     *
     * @return $this
     */
    public function removeEntries(array $entries)
    {
        foreach ($entries as $entry) {
            $this->removeEntry($entry);
        }

        return $this;
    }

    /**
     * getEntries
     *
     * @return string[]
     */
    public function getEntries()
    {
        return array_keys($this->lines);
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
     * isIgnored
     *
     * @param $entry
     *
     * @return bool
     */
    public function isIgnored($entry)
    {
        $entry = $this->normalizeEntry($entry);
        if ($this->hasEntry($entry)) {
            return true;
        } else {
            $entryPathParts = Filesystem::getInstance()->getPathParts($entry);
            foreach ($this->getEntries() as $givenEntry) {
                $givenEntryParts = Filesystem::getInstance()
                    ->getPathParts($givenEntry);

                if (!count($givenEntryParts)) {
                    continue;
                }

                $i = 0;
                while (
                    array_shift($givenEntryParts) === $entryPathParts[$i++]
                ) {
                    if (!count($givenEntryParts)) {
                        return true;
                    }
                }
            }
        }

        return false;
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
