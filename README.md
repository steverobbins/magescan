[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/steverobbins/magento-guest-audit/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/steverobbins/magento-guest-audit/?branch=master)

Magento Guest Audit
===

The idea behind this is to evaluate the quality and security of a Magento site you don't have access to.  The scenario when you're interviewing a potential developer or vetting a new client and want to have an idea of what you're getting into.

## Installation

* Clone the repository
* Install with composer

```
git clone https://github.com/steverobbins/magento-guest-audit mga
cd mga
composer install
```

## Usage

    $ ./bin/mga scan store.example.com

If you have access to the production environment, try [mageaudit](https://github.com/steverobbins/mageaudit).