Mage Scan
===

[![Master Build Status](https://img.shields.io/travis/steverobbins/magescan/master.svg?style=flat-square)](https://travis-ci.org/steverobbins/magescan)
[![Master Code Quality](https://img.shields.io/scrutinizer/g/steverobbins/magescan/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/steverobbins/magescan/?branch=master)
[![Master Code Coverage](https://img.shields.io/coveralls/steverobbins/magescan/master.svg?style=flat-square)](https://coveralls.io/r/steverobbins/magescan?branch=master)
[![Latest Stable Version](https://img.shields.io/packagist/v/steverobbins/magescan.svg?style=flat-square)](https://packagist.org/packages/steverobbins/magescan)
[![Master Dependancies](https://www.versioneye.com/user/projects/5507a68b66e561507b0001ff/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/5507a68b66e561507b0001ff)

The idea behind this is to evaluate the quality and security of a Magento site you don't have access to.  The scenario when you're interviewing a potential developer or vetting a new client and want to have an idea of what you're getting into.

![Screenshot](http://i.imgur.com/hL9bE1S.png)

# Disclaimer

Since we can't see the code base, this tool makes assumptions and takes guesses.  Information reported isn't guaranteed to be correct.

For in depth analyses, consider:

* [mageaudit](https://github.com/steverobbins/mageaudit)
* [Magento Project Mess Detector (for n98-magerun)](https://github.com/AOEpeople/mpmd)
* [magniffer](https://github.com/magento-ecg/magniffer)
* [Magento Coding Standard](https://github.com/magento-ecg/coding-standard)
* [magecheck](https://github.com/gknoppe-guidance/magecheck)
* [magento-check](http://www.magentocommerce.com/knowledge-base/entry/how-do-i-know-if-my-server-is-compatible-with-magento)

# Installation

### .phar

* Download the [`magescan.phar`](http://magescan.project.steverobbins.name/download/magescan.phar) file
* Run in command line with the `php` command

```
curl -o magescan.phar http://magescan.project.steverobbins.name/download/magescan.phar
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

    $ magescan scan store.example.com

## Commands

### `scan`

    scan [--all-paths] [--show-modules] <url>

Scans the given `<url>`.

#### Options

##### `--all-paths`

Checks additional paths that should not be accesible (this will make the scan take longer)

##### `--show-modules`

Show all modules that we tried to detect, not just those that were found

# Support

Please [create an issue](https://github.com/steverobbins/magescan/issues/new) for all bugs and feature requests

# Contributing

Fork this repository and send a pull request to the `dev` branch

# License

[Creative Commons Attribution 4.0 International](https://creativecommons.org/licenses/by/4.0/)
