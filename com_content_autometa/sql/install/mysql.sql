--
-- Table structure for table `#__autometa_regeneration_log`
--

CREATE TABLE IF NOT EXISTS `#__autometa_regeneration_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `articles_total` int(11) NOT NULL DEFAULT 0,
  `articles_success` int(11) NOT NULL DEFAULT 0,
  `articles_failed` int(11) NOT NULL DEFAULT 0,
  `empty_only` tinyint(1) NOT NULL DEFAULT 0,
  `processing_time` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__autometa_article_stats`
--

CREATE TABLE IF NOT EXISTS `#__autometa_article_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `article_title` varchar(255) NOT NULL,
  `regeneration_count` int(11) NOT NULL DEFAULT 0,
  `last_regenerated_at` datetime DEFAULT NULL,
  `last_regenerated_by` int(11) DEFAULT NULL,
  `last_hits_count` int(11) DEFAULT NULL,
  `first_regenerated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_article_id` (`article_id`),
  KEY `idx_regeneration_count` (`regeneration_count`),
  KEY `idx_last_regenerated_at` (`last_regenerated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
