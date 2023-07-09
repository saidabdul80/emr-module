-- This table definition is loaded and then executed when the OpenEMR interface's install button is clicked.
CREATE TABLE IF NOT EXISTS  `pghd_observation` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(200) NOT NULL,
  `subject` varchar(200) NOT NULL COMMENT 'Patient id',
  `category` varchar(200) NOT NULL COMMENT 'conforms with fhirs server',
  `code` varchar(300) NOT NULL,
  `value` varchar(200) NOT NULL,
  `effective` datetime(6) DEFAULT NULL COMMENT 'effective date',
  `device` varchar(200) DEFAULT NULL,
  `interpretation` varchar(1000) DEFAULT NULL,
  `name` varchar(1000) DEFAULT NULL,
  `components` longtext DEFAULT NULL COMMENT 'json',
   PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pghd_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pid` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scope` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access_token` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refresh_token` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS  `pghd_wearable` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `wearable` varchar(200) NOT NULL,
  `secrete_key` varchar(200) NOT NULL,  
  `client_id` varchar(200) NOT NULL,  
   PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 