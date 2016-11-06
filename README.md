# chubbyphp/chubbyphp-app-slim-skeleton

## Description

A slim 3 skeleton to build web applications (not apis) with authentication, crud, locale.

## Requirements

 * php: ~7.0
 * [bitexpert/prophiler-psr7-middleware][16]: ~0.3.0
 * [chubbyphp/chubbyphp-csrf][1]: ~1.0
 * [chubbyphp/chubbyphp-error-handler][2]: ~1.1@beta
 * [chubbyphp/chubbyphp-lazy][18]: ~1.0@beta
 * [chubbyphp/chubbyphp-model][3]: ~1.1@beta
 * [chubbyphp/chubbyphp-session][4]: ~1.0
 * [chubbyphp/chubbyphp-security][5]: ~1.0
 * [chubbyphp/chubbyphp-translation][6]: ~1.0
 * [chubbyphp/chubbyphp-validation][7]: ~1.0
 * [doctrine/dbal][8]: ^2.5.5
 * [fabfuel/prophiler][17]: dev-feature/php7
 * [monolog/monolog][9]: ~1.21.0
 * [ramsey/uuid][10]: ~3.5
 * [silex/providers][11]: ^2.0.3
 * [slim/slim][12]: ~3.5
 * [symfony/console][14]: ~3.1
 * [twig/twig][12]: ~1.27.0
 * [willdurand/negotiation][15]: ~2.1

## Installation

### With vagrant-php

#### Download

```{.sh}
wget https://github.com/chubbyphp/chubbyphp-app-slim-skeleton/archive/master.zip
unzip chubbyphp-app-slim-skeleton.zip
mv chubbyphp-app-slim-skeleton <myproject>
```

#### Initialize git

```{.sh}
cd <myproject>
git init
```

#### Install vagrant

```{.sh}
git submodule update --init -- vagrant-php
git submodule update --remote -- vagrant-php
```

#### Start vagrant

```{.sh}
cd vagrant-php
vagrant up
```

#### Install vendors

```{.sh}
vagrant ssh -c "composer.phar install"
```

### With php on host

```{.sh}
composer create-project chubbyphp/chubbyphp-app-slim-skeleton myproject dev-master --prefer-dist
```

## Setup

### Create database

```{.sh}
bin/console slim-skeleton:database:create
```

### Create / Update schema

```{.sh}
bin/console slim-skeleton:database:schema:update --dump --force
```

### Create user

```{.sh}
bin/console slim-skeleton:user:create admin@admin.admin admin ADMIN
```

### Login

admin@admin.admin // admin

[1]: https://github.com/chubbyphp/chubbyphp-csrf
[2]: https://github.com/chubbyphp/chubbyphp-error-handler
[3]: https://github.com/chubbyphp/chubbyphp-model
[4]: https://github.com/chubbyphp/chubbyphp-security
[5]: https://github.com/chubbyphp/chubbyphp-session
[6]: https://github.com/chubbyphp/chubbyphp-translation
[7]: https://github.com/chubbyphp/chubbyphp-validation
[8]: https://github.com/doctrine/dbal
[9]: https://github.com/Seldaek/monolog
[10]: https://github.com/ramsey/uuid
[11]: https://github.com/silexphp/Silex-Providers
[12]: https://github.com/slimphp/Slim
[13]: https://github.com/twigphp/Twig
[14]: https://github.com/symfony/console
[15]: https://github.com/willdurand/Negotiation
[16]: https://github.com/bitExpert/prophiler-psr7-middleware
[17]: https://github.com/fabfuel/prophiler
[18]: https://github.com/chubbyphp/chubbyphp-lazy
