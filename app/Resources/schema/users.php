<?php

use Doctrine\DBAL\Schema\Schema;

/* @var Schema $schema */
$users = $schema->createTable('users');
$users->addColumn('id', 'string', ['length' => 36]);
$users->addColumn('email', 'string');
$users->addColumn('username', 'string');
$users->addColumn('password', 'string');
$users->addColumn('roles', 'text');
$users->setPrimaryKey(['id']);
$users->addUniqueIndex(['email']);
$users->addUniqueIndex(['username']);

return $users;
