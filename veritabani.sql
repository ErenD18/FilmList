SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `testdb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `testdb`;

DROP TABLE IF EXISTS `filmler`;
CREATE TABLE IF NOT EXISTS `filmler` (
  `id` int NOT NULL AUTO_INCREMENT,
  `baslik` varchar(255) NOT NULL,
  `aciklama` text,
  `resim_yolu` varchar(255) DEFAULT NULL,
  `sayfa_adi` varchar(100) NOT NULL,
  `eklenme_tarihi` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sayfa_adi` (`sayfa_adi`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `filmler` (`id`, `baslik`, `aciklama`, `resim_yolu`, `sayfa_adi`, `eklenme_tarihi`) VALUES
(1, 'OLDBOY', '15 yıl boyunca nasıl hapse atıldığını, uyuşturulduğunu ve işkence gördüğünü bilmeyen çaresiz bir adam, kendisini kaçıranlardan intikam almaya çalışır.', 'img/oldboy.jpeg', 'oldboy', '2025-12-15 19:42:29'),
(2, 'Aşk ve Gurur', 'Gürcü döneminde toprak sahibi İngiliz soyluları arasında geçen bir aşk ve yaşam öyküsü. Bay Bennet, baskıcı karısı ve beş kızıyla Hertfordshire\'da yaşayan bir beyefendidir.', 'img/askvegurur.jpg', 'askvegurur', '2025-12-15 19:42:29'),
(3, 'CSM the Movie: Reze Arc', 'Şeytanlar, avcılar ve gizli düşmanlar arasındaki acımasız bir savaşta, Reze adında gizemli bir kız Denji\'nin dünyasına adım atar.', 'img/chainsawman.jpg', 'csm', '2025-12-15 19:42:29');

DROP TABLE IF EXISTS `kullanici_filmler`;
CREATE TABLE IF NOT EXISTS `kullanici_filmler` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kullanici_id` int NOT NULL,
  `film_id` int NOT NULL,
  `durum` enum('izledim','izlemeyi dusunuyorum') DEFAULT NULL,
  `puan` int DEFAULT NULL,
  `eklenme_tarihi` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `guncellenme_tarihi` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_kullanici_film` (`kullanici_id`,`film_id`),
  KEY `film_id` (`film_id`)
) ;

INSERT INTO `kullanici_filmler` (`id`, `kullanici_id`, `film_id`, `durum`, `puan`, `eklenme_tarihi`, `guncellenme_tarihi`) VALUES
(1, 4, 1, 'izledim', 10, '2025-12-15 19:47:36', '2025-12-15 19:47:36');

DROP TABLE IF EXISTS `roller`;
CREATE TABLE IF NOT EXISTS `roller` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rol_adi` varchar(50) NOT NULL,
  `aciklama` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rol_adi` (`rol_adi`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `roller` (`id`, `rol_adi`, `aciklama`) VALUES
(1, 'admin', 'Tam yetki - Film ekleme/silme, kullanıcı yönetimi'),
(2, 'moderator', 'Orta yetki - Film ekleme/silme'),
(3, 'uye', 'Temel yetki - Film izleme ve puanlama');

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol_id` int DEFAULT '3',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `rol_id` (`rol_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `users` (`id`, `username`, `password`, `rol_id`, `created_at`) VALUES
(4, 'admin', '$2y$10$WsO2qOreBjxeuK8ZFeBWZuMSdIU0gL5wrqapV3.ez0yPmdjgBDmUq', 1, '2025-12-15 19:44:28'),
(5, 'moderator', '$2y$10$kCtDhMXJwMjutcOFru1zaOvcyGJWB5FMt4hwr.le/dUv8LUUQRHuG', 2, '2025-12-15 19:44:38'),
(7, 'test_uye', '$2y$10$AQcFym5ZkB5vHirBQqJkTu7HfXq7b8Fs1E.0SRgkzsTJCFN5m7eaO', 3, '2025-12-15 19:44:51');

ALTER TABLE `kullanici_filmler`
  ADD CONSTRAINT `kullanici_filmler_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kullanici_filmler_ibfk_2` FOREIGN KEY (`film_id`) REFERENCES `filmler` (`id`) ON DELETE CASCADE;

ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roller` (`id`);
COMMIT;