CREATE TABLE `lists` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB;

CREATE TABLE tasks (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('todo','done') NOT NULL DEFAULT 'todo',
  `list_id` int(11) unsigned NOT NULL,
  PRIMARY KEY(`id`, `list_id`)
) ENGINE=InnoDB;

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB;

ALTER TABLE `tasks`
	ADD FOREIGN KEY (list_id) REFERENCES lists(id)
	ON DELETE CASCADE
	ON UPDATE CASCADE;
	
ALTER TABLE `lists`
	ADD FOREIGN KEY (user_id) REFERENCES users(id)
	ON DELETE CASCADE
	ON UPDATE CASCADE;
	
	
INSERT INTO `users` (`id`, `name`, `password`, `email`) VALUES
(1, 'Kasper', '$2y$10$KkEgonAT9pZ5NMIsLYTQjejqCAlIvG9UYLa/XeHPP.U/G6DpH7Z3i', 'kasper@test.com'),
(2, 'Jesper', '$2y$10$KkEgonAT9pZ5NMIsLYTQjejqCAlIvG9UYLa/XeHPP.U/G6DpH7Z3i', 'jesper@test.com'),
(3, 'Jonatan', '$2y$10$KkEgonAT9pZ5NMIsLYTQjejqCAlIvG9UYLa/XeHPP.U/G6DpH7Z3i', 'jonatan@test.com');