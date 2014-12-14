# Magento Composer Installer

[![Build Status](https://travis-ci.org/bragento/bragento-composer-installer.svg?branch=develop)](https://travis-ci.org/bragento/bragento-composer-installer)
[![Code Climate](https://codeclimate.com/github/bragento/bragento-composer-installer.png)](https://codeclimate.com/github/bragento/bragento-composer-installer)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bragento/bragento-composer-installer/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/bragento/bragento-composer-installer/?branch=develop)

[![Latest Stable Version](https://poser.pugx.org/bragento/magento-composer-installer/v/stable.svg)](https://packagist.org/packages/bragento/magento-composer-installer) [![License](https://poser.pugx.org/bragento/magento-composer-installer/license.svg)](https://packagist.org/packages/bragento/magento-composer-installer)



## Supported Types:

magento-module  
magento-core  
magento-theme (currently just the same as magento-module)  



## Core Installer

### Installation

to Install the Magento Core require magento/core in your composer.json.  
Magento will be copied to the Magento root dir (Default: 'magento')  

```json
{
    "require": {  
        "bragento/magento-composer-installer": "~1",  
        "magento/core": "~1.9"  
    },  
    "extra": {  
        "magento-root-dir": "magento"  
    }  
}
```

### Persistent Files

By default, the Installer will erase the whole magento root dir on every update  
There are several persistent files and directories, that will be backed up and restored after core deployment:  

var  
media  
app/etc/local.xml  
.gitignore  
.gitattributes  
.gitmodules  
.git  
modman  
composer.json  
composer.lock  
.htaccess  
.htpasswd  

You can also define additional Files to be persistent such as local Modules or other configuration files in the root dir

```json
{
    "require": {  
        "bragento/magento-composer-installer": "~1",  
        "magento/core": "~1.9"  
    },  
    "extra": {  
        "magento-root-dir": "magento",
        "persistent-files": [
            "somefile",
            "app/code/local/Vendor/SomeModule",
            "app/etc/modules/Vendor_SomeModule.xml"
        ]
    }  
}
```


## Module Installer

### Install a Module

Just require the Modules in your composer.json.

Modules are currently all deployed with symlink strategy (Except under Windows they should be copied - not tested yet)  
Support for deploying all or just some specific modules by copy strategy will be added later

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
        "bragento/magento-composer-installer": "~1",  
        "magento/core": "~1.9",
        "firegento/magesetup": "~2",
        "danslo/api-import": "~1"
    },  
    "extra": {  
        "magento-root-dir": "magento"  
    }  
}
```

### Change Deploy Strategy

By default, all Modules are deployed by symlink. You can change this behaviour with the config key 'magento-deploystrategy'  

Possible values are:  
symlink  
copy  
none  (will just install the Module but not deploy it to magento root)

```json
{ 
    "extra": {  
        "magento-deploystrategy": "copy"  
    }  
}
```

### Overwrite Deploy Strategy per Module

You can also overwrite the Deploy Strategy for specific Modules under the config key magento-deploystrategy-overwrite  

```json
{ 
    "repositories": [
            {
                "type": "composer",
                "url": "packages.firegento.com"
            }
        ],
        "require": {  
            "bragento/magento-composer-installer": "~1",  
            "magento/core": "~1.9",
            "firegento/magesetup": "~2"
        },  
    "extra": {  
        "magento-deploystrategy": "symlink",
        "magento-deploystrategy-overwrite": {
            "firegento/magesetup": "copy"
        }
    }  
}
```

## Contributing

make pull requests solely from the develop Branch.  
run the tests before making a pull request
