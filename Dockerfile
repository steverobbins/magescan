# Using PHP 7.2.* Alpine as base image

FROM php:7.2-alpine

# Setup default build arguments

ARG RELEASE_VERSION=1.12.9

# Install magescan phar executable

RUN wget -O /usr/bin/magescan https://github.com/steverobbins/magescan/releases/download/v${RELEASE_VERSION}/magescan.phar
RUN chmod +x /usr/bin/magescan

# Check installed version during image build

RUN magescan --version 

# Setup entrypoint

ENTRYPOINT [ "/usr/bin/magescan" ]


