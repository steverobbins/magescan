Mage Scan
===

[![Join the chat at https://gitter.im/steverobbins/magescan](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/steverobbins/magescan?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

[![Master Build Status](https://img.shields.io/travis/steverobbins/magescan/master.svg?style=flat-square)](https://travis-ci.org/steverobbins/magescan)
[![Master Code Quality](https://img.shields.io/scrutinizer/g/steverobbins/magescan/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/steverobbins/magescan/?branch=master)
[![Master Code Coverage](https://img.shields.io/coveralls/steverobbins/magescan/master.svg?style=flat-square)](https://coveralls.io/r/steverobbins/magescan?branch=master)
[![Latest Stable Version](https://img.shields.io/packagist/v/steverobbins/magescan.svg?style=flat-square)](https://packagist.org/packages/steverobbins/magescan)
[![Master Dependancies](https://www.versioneye.com/user/projects/5550f5c506c3183941000002/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/5550f5c506c3183941000002)

The idea behind this is to evaluate the quality and security of a Magento site you don't have access to.  The scenario when you're interviewing a potential developer or vetting a new client and want to have an idea of what you're getting into.

![Screenshot](http://i.imgur.com/dGyZsq4.png)

# Installation

### .phar

* Download the [`magescan.phar`](http://magescan.steverobbins.com/download/magescan.phar) file
* Run in command line with the `php` command

```
curl -o magescan.phar http://magescan.steverobbins.com/download/magescan.phar
php magescan.phar scan www.example.com
```


### Source

* Clone this repository
* Install with composer

```
git clone https://github.com/steverobbins/magescan magescan
cd magescan
curl -sS https://getcomposer.org/installer | php
php composer.phar install
bin/magescan scan www.example.com
```

### n98-magerun

Clone into your `~/.n98-magerun/modules` directory

```
mkdir -p ~/.n98-magerun/modules
git clone https://github.com/steverobbins/magescan ~/.n98-magerun/modules/magescan
magerun magescan:scan store.example.com
```

### Composer

```
composer require steverobbins/magescan --dev
```

### Include in your project

Add the following to your `composer.json`

```
"require": {
    "steverobbins/magescan": "dev-master"
}
```

# Usage

    $ magescan.phar scan store.example.com

## Commands

### `scan`

    $ magescan.phar scan [--all-paths] [--insecure] [--show-modules] <url>

Scans the given `<url>`.

#### Options

##### `--all-paths`

Checks additional paths that should not be accesible (this will make the scan take longer)

##### `--insecure`, `-k`

If set, SSL certificates won't be validated

##### `--show-modules`

Show all modules that we tried to detect, not just those that were found

### `selfupdate`

    $ magescan.phar selfupdate

Updates the phar file to the latest version.

# Disclaimer

Since we can't see the code base, this tool makes assumptions and takes guesses.  Information reported isn't guaranteed to be correct.

For in depth analyses, consider:

* [mageaudit](https://github.com/steverobbins/mageaudit)
* [Magento Project Mess Detector (for n98-magerun)](https://github.com/AOEpeople/mpmd)
* [magniffer](https://github.com/magento-ecg/magniffer)
* [Magento Coding Standard](https://github.com/magento-ecg/coding-standard)
* [magecheck](https://github.com/gknoppe-guidance/magecheck)
* [magento-check](http://www.magentocommerce.com/knowledge-base/entry/how-do-i-know-if-my-server-is-compatible-with-magento)

# Support

Please [create an issue](https://github.com/steverobbins/magescan/issues/new) for all bugs and feature requests

# Contributing

Fork this repository and send a pull request to the `dev` branch

# License

[Creative Commons Attribution 4.0 International](https://creativecommons.org/licenses/by/4.0/)
