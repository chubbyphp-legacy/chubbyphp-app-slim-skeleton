# chubbyphp/chubbyphp-app-slim-skeleton

## Description

A slim 3 skeleton to build web applications (not apis) with authentication, crud, locale.

## Requirements

 * php: ~7.0
 * [chubbyphp/chubbyphp-csrf][1]: ~1.0
 * [chubbyphp/chubbyphp-error-handler][2]: ~1.0
 * [chubbyphp/chubbyphp-model][3]: ~1.0
 * [chubbyphp/chubbyphp-session][4]: ~1.0
 * [chubbyphp/chubbyphp-security][5]: ~1.0
 * [chubbyphp/chubbyphp-translation][6]: ~1.0
 * [chubbyphp/chubbyphp-validation][7]: ~1.0
 * [doctrine/dbal][8]: ^2.5.5
 * [monolog/monolog][9]: ~1.21.0
 * [ramsey/uuid][10]: ~3.5
 * [silex/providers][11]: ^2.0.3
 * [slim/slim][12]: ~3.5
 * [slim/twig-view][13]: ^2.1.1
 * [symfony/console][14]: ~3.1
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
echo "CREATE DATABASE slim_skeleton;" | mysql --user=root --password=root
```

### Import data

```{.sh}
bin/console dbal:import slim_skeleton.sql
```

### Login

admin // admin@admin.admin

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
[13]: https://github.com/slimphp/Twig-View
[14]: https://github.com/symfony/console
[15]: https://github.com/willdurand/Negotiation
