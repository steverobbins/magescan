Magento Guest Audit
===

The idea behind this is to evaluate the quality and security of a Magento site you don't have access to.  The scenario when you're interviewing a potential developer or vetting a new client and want to have an idea of what you're getting into.

![Screenshot](http://i.imgur.com/uC1ZD8i.png)

# Build Status

[![Latest Stable Version](https://img.shields.io/packagist/v/steverobbins/magento-guest-audit.svg)](https://packagist.org/packages/steverobbins/magento-guest-audit)
[![Dependency Status](https://www.versioneye.com/user/projects/5507a68b66e561507b0001ff/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5507a68b66e561507b0001ff)

#### Latest Release

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/steverobbins/magento-guest-audit/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/steverobbins/magento-guest-audit/?branch=master)
[![Build Status](https://travis-ci.org/steverobbins/magento-guest-audit.svg?branch=master)](https://travis-ci.org/steverobbins/magento-guest-audit)
[![Coverage Status](https://coveralls.io/repos/steverobbins/magento-guest-audit/badge.svg?branch=master)](https://coveralls.io/r/steverobbins/magento-guest-audit?branch=master)

#### Development

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/steverobbins/magento-guest-audit/badges/quality-score.png?b=dev)](https://scrutinizer-ci.com/g/steverobbins/magento-guest-audit/?branch=dev)
[![Build Status](https://travis-ci.org/steverobbins/magento-guest-audit.svg?branch=dev)](https://travis-ci.org/steverobbins/magento-guest-audit)
[![Coverage Status](https://coveralls.io/repos/steverobbins/magento-guest-audit/badge.svg?branch=dev)](https://coveralls.io/r/steverobbins/magento-guest-audit?branch=dev)

# Installation

### From Source with Composer

* Clone this repository
* Install with composer

```
git clone https://github.com/steverobbins/magento-guest-audit mga
cd mga
curl -sS https://getcomposer.org/installer | php
php composer.phar install
```

### As a Composer Dependancy

```
composer require steverobbins/magento-guest-audit --dev
```

# Usage

    $ ./bin/mga scan store.example.com

## Commands

### `scan`

    scan [--all-paths] <url>

Scans the given `<url>`.

#### Options

##### `--all-paths`

Checks additional paths that should not be accesible (this will make the scan take longer)

# Support

Please [create an issue](https://github.com/steverobbins/magento-guest-audit/issues/new) for all bugs and feature requests

# Contributing

Fork this repository and send a pull request to the `dev` branch

# License

[Creative Commons Attribution 4.0 International](https://creativecommons.org/licenses/by/4.0/)
