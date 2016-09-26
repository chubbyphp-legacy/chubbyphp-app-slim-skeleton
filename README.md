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
