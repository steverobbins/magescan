#!/bin/bash

ROOT=/var/www/html/magescan.steverobbins.com
REQUEST=$ROOT/build/request
BIN_BOX=$ROOT/build/box.phar
BIN_COMPOSER=/usr/local/bin/composer
REPO_URL=https://github.com/steverobbins/magescan.git
REPO_FOLDER=$ROOT/build/magescan
PHAR_DEST=$ROOT/public/download/magescan.phar
VERSION=$ROOT/public/download/version

if [ ! -f $REQUEST ]; then
  exit 0
fi

rm -rf $REPO_FOLDER $REQUEST
git clone $REPO_URL $REPO_FOLDER
cd $REPO_FOLDER
$BIN_COMPOSER --no-dev install
$BIN_BOX build -c box.json
rm -f $PHAR_DEST
mv $REPO_FOLDER/magescan.phar $PHAR_DEST
php $PHAR_DEST --version | sed -r 's/Mage Scan version //g' > $VERSION
echo Done
