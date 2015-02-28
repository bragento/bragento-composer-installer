<?php
/**
 * Events.php
 *
 * PHP Version 5
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Deploy
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */

namespace Bragento\Magento\Composer\Installer\Deploy;

/**
 * Class Events
 *
 * @category  Bragento_MagentoComposerInstaller
 * @package   Bragento\Magento\Composer\Installer\Deploy
 * @author    David Verholen <david.verholen@brandung.de>
 * @copyright 2014 Brandung GmbH & Co. KG
 * @license   http://opensource.org/licenses/OSL-3.0 OSL-3.0
 * @link      http://www.brandung.de
 */
final class Events
{
    const PRE_DEPLOY_CORE_INSTALL = 'pre-deploy-magento-core-install';
    const POST_DEPLOY_CORE_INSTALL = 'post-deploy-magento-core-install';
    const PRE_DEPLOY_CORE_UPDATE = 'pre-deploy-magento-core-update';
    const POST_DEPLOY_CORE_UPDATE = 'post-deploy-magento-core-update';
    const PRE_DEPLOY_CORE_UNINSTALL = 'pre-deploy-magento-core-uninstall';
    const POST_DEPLOY_CORE_UNINSTALL = 'post-deploy-magento-core-uninstall';

    const PRE_DEPLOY_MODULE_INSTALL = 'pre-deploy-magento-module-install';
    const POST_DEPLOY_MODULE_INSTALL = 'post-deploy-magento-module-install';
    const PRE_DEPLOY_MODULE_UPDATE = 'pre-deploy-magento-module-update';
    const POST_DEPLOY_MODULE_UPDATE = 'post-deploy-magento-module-update';
    const PRE_DEPLOY_MODULE_UNINSTALL = 'pre-deploy-magento-module-uninstall';
    const POST_DEPLOY_MODULE_UNINSTALL = 'post-deploy-magento-module-uninstall';

    const PRE_DEPLOY_THEME_INSTALL = 'pre-deploy-magento-theme-install';
    const POST_DEPLOY_THEME_INSTALL = 'post-deploy-magento-theme-install';
    const PRE_DEPLOY_THEME_UPDATE = 'pre-deploy-magento-theme-update';
    const POST_DEPLOY_THEME_UPDATE = 'post-deploy-magento-theme-update';
    const PRE_DEPLOY_THEME_UNINSTALL = 'pre-deploy-magento-theme-uninstall';
    const POST_DEPLOY_THEME_UNINSTALL = 'post-deploy-magento-theme-uninstall';

    const PRE_DEPLOY = 'pre-deploy-magento';
    const POST_DEPLOY = 'post-deploy-magento';
}
