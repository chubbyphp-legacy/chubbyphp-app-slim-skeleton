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

INSERT INTO `users` VALUES (
    'e450f557-f911-446a-8657-fbc801b2be37',
    'admin@admin.admin',
    'admin@admin.admin',
    '$2y$10$zXfRRDa2u9WxgB0noAnk1u281vVwNwjNcH5WCRdu8I70aBk23TS6G',
    '["ADMIN"]'
);
