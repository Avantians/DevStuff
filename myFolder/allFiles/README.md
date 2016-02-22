Creating Database Tables
==============
- Change Database information in _config.php

Table for `Colors`
--------------

```ruby
DROP TABLE IF EXISTS `Colors`;
CREATE TABLE `Colors` (
  `id` int(11) unsigned NOT NULL,
  `color` varchar(12) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `Colors`
  ADD PRIMARY KEY (`id`, `color`),
  ADD UNIQUE KEY `id` (`id`);

ALTER TABLE `Colors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

INSERT INTO `Colors` (`id`, `color`) VALUES
('1', 'Blue'),
('2', 'Green'),
('3', 'Indigo'),
('4', 'Orange'),
('5', 'Red'),
('6', 'Violet'),
('7', 'Yellow');

ALTER TABLE `Colors` AUTO_INCREMENT=8;
```

Table for `Votes`
--------------

```ruby
DROP TABLE IF EXISTS `Votes`;
CREATE TABLE `Votes` (
  `id` int(11) unsigned NOT NULL,
  `city` varchar(32) NOT NULL,
  `color` varchar(12) NOT NULL,
  `votes` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `Votes` (`id`, `city`, `color`, `votes`) VALUES
(1, 'Anchorage', 'Blue', 10000),
(2, 'Anchorage', 'Yellow', 15000),
(3, 'Brooklyn', 'Red', 100000),
(4, 'Brooklyn', 'Blue', 250000),
(5, 'Detroit', 'Red', 160000),
(6, 'Selma', 'Yellow', 15000),
(7, 'Selma', 'Violet', 5000);

ALTER TABLE `Votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

ALTER TABLE `Votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Votes` AUTO_INCREMENT=8;
```
