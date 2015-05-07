#!/bin/bash

ROOT=/var/www/html/mga.project.steverobbins.name
REQUEST=$ROOT/build/request
BIN_BOX=$ROOT/build/box.phar
BIN_COMPOSER=/usr/local/bin/composer
REPO_URL=https://github.com/steverobbins/magento-guest-audit.git
REPO_FOLDER=$ROOT/build/mga
PHAR_DEST=$ROOT/public/download/mga.phar
VERSION=$ROOT/build/version

if [ ! -f $REQUEST ]; then
  exit 0
fi

rm -rf $REPO_FOLDER $REQUEST
git clone $REPO_URL $REPO_FOLDER
cd $REPO_FOLDER
$BIN_COMPOSER --no-dev install
$BIN_BOX build -c box.json
rm -f $PHAR_DEST
mv $REPO_FOLDER/mga.phar $PHAR_DEST
php $PHAR_DEST --version > $VERSION
echo Done
