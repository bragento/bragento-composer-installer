# Magento Composer Installer

[![Build Status](https://travis-ci.org/bragento/bragento-composer-installer.svg?branch=develop)](https://travis-ci.org/bragento/bragento-composer-installer)
[![Code Climate](https://codeclimate.com/github/bragento/bragento-composer-installer.png)](https://codeclimate.com/github/bragento/bragento-composer-installer)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bragento/bragento-composer-installer/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/bragento/bragento-composer-installer/?branch=develop)
[![Test Coverage](https://codeclimate.com/github/bragento/bragento-composer-installer/coverage.png)](https://codeclimate.com/github/bragento/bragento-composer-installer)

Redesign of [magento-hackathon/magento-composer-installer](https://github.com/magento-hackathon/magento-composer-installer)

[![Latest Stable Version](https://poser.pugx.org/bragento/magento-composer-installer/v/stable.svg)](https://packagist.org/packages/bragento/magento-composer-installer) [![Total Downloads](https://poser.pugx.org/bragento/magento-composer-installer/downloads.svg)](https://packagist.org/packages/bragento/magento-composer-installer) [![Latest Unstable Version](https://poser.pugx.org/bragento/magento-composer-installer/v/unstable.svg)](https://packagist.org/packages/bragento/magento-composer-installer) [![License](https://poser.pugx.org/bragento/magento-composer-installer/license.svg)](https://packagist.org/packages/bragento/magento-composer-installer)



## Supported Types:

magento-module  
magento-core  
magento-theme (currently just the same as magento-module)  



## Core Installer

to Install the Magento Core require magento/core in your composer.json.  
Magento will be copied to the Magento root dir (Default: 'magento')  

```json
{
    "require": {  
        "bragento/magento-composer-installer": "@dev",  
        "magento/core": "1.9.1.0"  
    },  
    "extra": {  
        "magento-root-dir": "magento"  
    }  
}
```


## Module Installer

Just require the Modules in your composer.json.

Modules are currently all deployed with symlink strategy (Except under Windows they should be copied - not tested yet)  
Support for deploying all or just som specific modules by copy strategy will be added later

Many composer installable Magento extensions are listed under <a href="http://packages.firegento.com">packages.firegento.com</a>  
There is also an example of how to add a package directly from a github (or any git) Repository

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "packages.firegento.com"
        },
        {
            "type": "git",
            "url": "https://github.com/danslo/ApiImport.git"
        }
    ],
    "require": {  
        "bragento/magento-composer-installer": "@dev",  
        "magento/core": "1.9.1.0",
        "firegento/magesetup": "@stable",
        "danslo/api-import": "@stable"
    },  
    "extra": {  
        "magento-root-dir": "magento"  
    }  
}
```
