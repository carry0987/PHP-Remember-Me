SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS members (
  `member_id` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_name` varchar(255) NOT NULL,
  `member_password` varchar(255) NOT NULL,
  `member_email` varchar(255) NOT NULL,
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `members` (`member_id`, `member_name`, `member_password`, `member_email`) VALUES
(1, 'admin', '$2a$10$0FHEQ5/cplO3eEKillHvh.y009Wsf4WCKvQHsZntLamTUToIBe.fG', 'user@gmail.com');

CREATE TABLE IF NOT EXISTS tbl_token_auth (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(8) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `selector_hash` varchar(255) NOT NULL,
  `expiry_date` int(20) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `tbl_token_auth` ADD CONSTRAINT `Token_User` FOREIGN KEY (`user_id`) REFERENCES `members`(`member_id`) ON DELETE CASCADE ON UPDATE CASCADE;
