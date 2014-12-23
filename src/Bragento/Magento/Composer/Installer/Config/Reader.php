<?php
/**
 * Reader.php
 *
 * PHP Version 5
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento_MagentoComposerInstaller
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */

namespace Bragento\Magento\Composer\Installer\Config;

use Eloquent\Composer\Configuration\ConfigurationReader;
use Eloquent\Composer\Configuration\Exception\ConfigurationExceptionInterface;
use Eloquent\Composer\Configuration\ObjectAccess;
use stdClass;

/**
 * Class Reader
 *
 * @category Bragento_MagentoComposerInstaller
 * @package  Bragento\Magento\Composer\Installer\Config
 * @author   David Verholen <david@verholen.com>
 * @license  http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link     http://github.com/davidverholen
 */
class Reader extends ConfigurationReader
{
    /**
     * Read a Composer configuration file.
     *
     * @param string $path The configuration file path.
     *
     * @return Composer                     The parsed configuration.
     * @throws ConfigurationExceptionInterface If there is a problem reading the configuration.
     */
    public function read($path)
    {
        $data = $this->readJson($path);
        $this->validator()->validate($data);

        return $this->createConfiguration($data);
    }

    /**
     * Create a configuration object from the supplied JSON data.
     *
     * @param ObjectAccess $data The parsed JSON data.
     *
     * @return Composer The newly created configuration object.
     */
    protected function createConfiguration(ObjectAccess $data)
    {
        $autoloadData = new ObjectAccess(
            $data->getDefault('autoload', new stdClass)
        );

        return new Composer(
            $data->getDefault('name'),
            $data->getDefault('description'),
            $data->getDefault('version'),
            $data->getDefault('type'),
            $data->getDefault('keywords'),
            $data->getDefault('homepage'),
            $this->createTime($data->getDefault('time')),
            $this->arrayize($data->getDefault('license')),
            $this->createAuthors($data->getDefault('authors')),
            $this->createSupport($data->getDefault('support')),
            $this->objectToArray($data->getDefault('require')),
            $this->objectToArray($data->getDefault('require-dev')),
            $this->objectToArray($data->getDefault('conflict')),
            $this->objectToArray($data->getDefault('replace')),
            $this->objectToArray($data->getDefault('provide')),
            $this->objectToArray($data->getDefault('suggest')),
            $this->createAutoloadPsr($autoloadData->getDefault('psr-4')),
            $this->createAutoloadPsr($autoloadData->getDefault('psr-0')),
            $autoloadData->getDefault('classmap'),
            $autoloadData->getDefault('files'),
            $data->getDefault('include-path'),
            $data->getDefault('target-dir'),
            $this->createStability($data->getDefault('minimum-stability')),
            $data->getDefault('prefer-stable'),
            $this->createRepositories($data->getDefault('repositories')),
            $this->createProjectConfiguration($data->getDefault('config')),
            $this->createScripts($data->getDefault('scripts')),
            $data->getDefault('extra'),
            $data->getDefault('bin'),
            $this->createArchiveConfiguration($data->getDefault('archive')),
            $data->data()
        );
    }
}
