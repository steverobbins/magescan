Mage Scan
===

[![Join the chat at https://gitter.im/steverobbins/magescan](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/steverobbins/magescan?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

[![Master Build Status](https://img.shields.io/travis/steverobbins/magescan/master.svg?style=flat-square)](https://travis-ci.org/steverobbins/magescan)
[![Master Code Quality](https://img.shields.io/scrutinizer/g/steverobbins/magescan/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/steverobbins/magescan/?branch=master)
[![Master Code Coverage](https://codecov.io/gh/steverobbins/magescan/branch/master/graph/badge.svg)](https://codecov.io/gh/steverobbins/magescan)
[![Latest Stable Version](https://img.shields.io/packagist/v/steverobbins/magescan.svg?style=flat-square)](https://packagist.org/packages/steverobbins/magescan)
[![Master Dependancies](https://www.versioneye.com/user/projects/55e4bfec8c0f62001c000052/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/55e4bfec8c0f62001c000052)

The idea behind this is to evaluate the quality and security of a Magento site you don't have access to.  The scenario when you're interviewing a potential developer or vetting a new client and want to have an idea of what you're getting into.

![Screenshot](https://i.imgur.com/HfUiEK9.png)

# Installation

### .phar

* Download the [`magescan.phar` file from the releases page](https://github.com/steverobbins/magescan/releases)
* Run in command line with the `php` command

```
php magescan.phar scan:all www.example.com
```


### Source

* Clone this repository
* Install with composer

```
git clone https://github.com/steverobbins/magescan magescan
cd magescan
curl -sS https://getcomposer.org/installer | php
php composer.phar install
bin/magescan scan:all www.example.com
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

    $ magescan.phar scan:all store.example.com

## Commands

### `scan:all`

    $ magescan.phar scan:all [--insecure|-k] [--show-modules] <url>

Run all scans on the given `<url>`.

#### Options

##### `--format=FORMAT`

Specify a different output format.  Possible values:

* `default`
* `json`

##### `--insecure`, `-k`

If set, SSL certificates won't be validated

##### `--show-modules`

Lists all modules searched for, not just those found

### `scan:catalog`

    $ magescan.phar scan:catalog [--insecure|-k] <url>

Get catalog information

### `scan:modules`

    $ magescan.phar scan:modules [--insecure|-k] [--show-modules] <url>

Get installed modules

### `scan:patch`

    $ magescan.phar scan:patch [--insecure|-k] <url>

Get patch information

### `scan:server`

    $ magescan.phar scan:server [--insecure|-k] <url>

Check server technology

### `scan:sitemap`

    $ magescan.phar scan:sitemap [--insecure|-k] <url>

Check sitemap

### `scan:unreachable`

    $ magescan.phar scan:unreachable [--insecure|-k] <url>

Check unreachable paths

### `scan:version`

    $ magescan.phar scan:version [--insecure|-k] <url>

Get the version of a Magento installation


Show all modules that we tried to detect, not just those that were found

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
