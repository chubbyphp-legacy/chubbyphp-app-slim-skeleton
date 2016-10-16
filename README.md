# chubbyphp/chubbyphp-app-slim-skeleton

## Description

A slim 3 skeleton to build web applications (not apis) with authentication, crud, locale.

## Requirements

 * php: ~7.0
 * chubbyphp/chubbyphp-csrf: ~1.0@dev
 * chubbyphp/chubbyphp-error-handler: ~1.0@dev
 * chubbyphp/chubbyphp-model: ~1.0@dev
 * chubbyphp/chubbyphp-session: ~1.0@dev
 * chubbyphp/chubbyphp-security: ~1.0@dev
 * chubbyphp/chubbyphp-translation: ~1.0@dev
 * chubbyphp/chubbyphp-validation: ~1.0@dev
 * doctrine/dbal: ^2.5.5
 * monolog/monolog: ~1.21.0
 * ramsey/uuid: ~3.5
 * silex/providers: ^2.0.3
 * slim/slim: ~3.5
 * slim/twig-view: ^2.1.1
 * symfony/console: ~3.1
 * willdurand/negotiation: ~2.1

## Installation

### Create project

```{.sh}
composer create-project chubbyphp/chubbyphp-app-slim-skeleton <myproject> master --prefer-dist
```

### Initialize git

```{.sh}
cd <myproject>
git init
```

### Install vagrant

```{.sh}
git submodule update --init -- vagrant-php
git submodule update --remote -- vagrant-php
```

### Start vagrant

```{.sh}
cd vagrant-php
vagrant up
```

### Install vendors

```{.sh}
vagrant ssh -c "composer.phar install"
```

### chubbyphp vendors

 - [chubbyphp-csrf][1]
 - [chubbyphp-error-handler][2]
 - [chubbyphp-model][3]
 - [chubbyphp-security][4]
 - [chubbyphp-session][5]
 - [chubbyphp-translation][6]
 - [chubbyphp-validation][7]


### Create MYSQL database

```{.sh}
vagrant ssh -c "echo 'CREATE DATABASE slim_demo;' | mysql"
```

### Create table(s)

```{.sql}
CREATE TABLE `users` (
  `id` char(36) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `roles` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_idx` (`username`),
  UNIQUE KEY `email_idx` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

### Add default user

```{.sql}
INSERT INTO `users` VALUES (
    'e450f557-f911-446a-8657-fbc801b2be37',
    'admin@admin.admin',
    'admin@admin.admin',
    '$2y$10$zXfRRDa2u9WxgB0noAnk1u281vVwNwjNcH5WCRdu8I70aBk23TS6G',
    '["ADMIN"]'
);
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
