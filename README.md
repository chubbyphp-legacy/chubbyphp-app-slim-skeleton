# chubbyphp/chubbyphp-app-slim-skeleton

## Description

A slim 3 skeleton to build web applications (not apis) with authentication, crud, locale.

## Requirements

 * php: ~7.0

## Installation

### Checkout the repository

```{.sh}
git clone https://github.com/chubbyphp/chubbyphp-app-slim-skeleton.git
```

### Install vagrant

```{.sh}
cd slim-skeleton
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
