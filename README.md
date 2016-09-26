# slim-skeleton

## Description

A slim skeleton to build web applications (not apis) with authentication, crud, locale.

## Requirements

 * php: ~7.0

## Installation

### Checkout the repository

```{.sh}
git clone https://github.com/dominikzogg/slim-skeleton.git
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
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_idx` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```
