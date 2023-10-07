SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/* Create user */
CREATE TABLE IF NOT EXISTS `user` (
  `uid` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user` (`uid`, `username`, `password`) VALUES
(1, 'admin', '$2a$10$0FHEQ5/cplO3eEKillHvh.y009Wsf4WCKvQHsZntLamTUToIBe.fG');

/* Create remember me */
CREATE TABLE IF NOT EXISTS `remember_me` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(8) UNSIGNED NOT NULL,
  `selector_hash` varchar(16) NOT NULL,
  `pw_hash` varchar(255) NOT NULL,
  `expiry_date` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `remember_me` ADD CONSTRAINT `Remember_User` FOREIGN KEY (`user_id`) REFERENCES `user`(`uid`) ON DELETE CASCADE ON UPDATE CASCADE;
