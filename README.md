# chubbyphp/chubbyphp-app-slim-skeleton

## Description

A slim 3 skeleton to build web applications (not apis) with authentication, crud, locale.

## Requirements

 * php: ~7.0
 * [chubbyphp/chubbyphp-csrf][1]: ~1.1
 * [chubbyphp/chubbyphp-deserialization][2]: ~1.0
 * [chubbyphp/chubbyphp-deserialization-model][3]: ~1.0
 * [chubbyphp/chubbyphp-lazy][4]: ~1.1
 * [chubbyphp/chubbyphp-model][5]: ~3.0
 * [chubbyphp/chubbyphp-model-doctrine-dbal][6]: ~1.0
 * [chubbyphp/chubbyphp-session][7]: ~1.0
 * [chubbyphp/chubbyphp-security][8]: ~1.1
 * [chubbyphp/chubbyphp-translation][9]: ~1.1
 * [chubbyphp/chubbyphp-validation][10]: ~2.0
 * [chubbyphp/chubbyphp-validation-model][11]: ~1.0
 * [doctrine/dbal][12]: ^2.5.5
 * [monolog/monolog][13]: ~1.21
 * [ramsey/uuid][14]: ~3.5
 * [silex/providers][15]: ^2.0.3
 * [slim/slim][16]: ~3.5
 * [symfony/console][17]: ~3.1
 * [twig/twig][18]: ~1.27.0
 * [willdurand/negotiation][19]: ~2.1

## Installation

### With vagrant-php

#### Install `create-slim3-project` command

[create-slim3-project][19]

#### Create project

```{.sh}
create-slim3-project --name=myproject --vagrantIp=10.15.10.15
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
[2]: https://github.com/chubbyphp/chubbyphp-deserialization
[3]: https://github.com/chubbyphp/chubbyphp-deserialization-model
[4]: https://github.com/chubbyphp/chubbyphp-lazy
[5]: https://github.com/chubbyphp/chubbyphp-model
[6]: https://github.com/chubbyphp/chubbyphp-model-doctrine-dbal
[7]: https://github.com/chubbyphp/chubbyphp-session
[8]: https://github.com/chubbyphp/chubbyphp-security
[9]: https://github.com/chubbyphp/chubbyphp-translation
[10]: https://github.com/chubbyphp/chubbyphp-validation
[11]: https://github.com/chubbyphp/chubbyphp-validation-model
[12]: https://github.com/doctrine/dbal
[13]: https://github.com/Seldaek/monolog
[14]: https://github.com/ramsey/uuid
[15]: https://github.com/silexphp/Silex-Providers
[16]: https://github.com/slimphp/Slim
[17]: https://github.com/symfony/console
[18]: https://github.com/twigphp/Twig
[19]: https://github.com/willdurand/Negotiation
