-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 04 Nis 2021, 14:47:45
-- Sunucu sürümü: 10.1.32-MariaDB
-- PHP Sürümü: 7.2.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `teknasyon`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `application`
--

CREATE TABLE `application` (
  `id` int(11) NOT NULL,
  `endpoint` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `application`
--

INSERT INTO `application` (`id`, `endpoint`, `created_at`, `updated_at`) VALUES
(1, 'http://example.com/app1/', '2021-04-03 15:23:50', '2021-04-03 15:23:50'),
(2, 'http://example.com/app2/', '2021-04-03 15:23:50', '2021-04-03 15:23:50'),
(3, 'http://example.com/app3/', '2021-04-03 15:23:50', '2021-04-03 15:23:50'),
(4, 'http://example.com/app4/', '2021-04-03 15:23:50', '2021-04-03 15:23:50'),
(5, 'http://example.com/app5/', '2021-04-03 15:23:50', '2021-04-03 15:23:50');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `callback`
--

CREATE TABLE `callback` (
  `id` int(11) NOT NULL,
  `device_id` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `appId` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `event` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `is_status` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `callback`
--

INSERT INTO `callback` (`id`, `device_id`, `appId`, `event`, `is_status`, `created_at`, `updated_at`) VALUES
(1, '3', '3', '3', '0', '2021-04-04 15:24:55', '2021-04-04 15:27:55'),
(2, '2', '1', '3', '0', '2021-04-04 15:25:47', '2021-04-04 15:27:54');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `device`
--

CREATE TABLE `device` (
  `id` int(11) NOT NULL,
  `uid` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `appId` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `language` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `operating_system` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `device`
--

INSERT INTO `device` (`id`, `uid`, `appId`, `language`, `operating_system`) VALUES
(1, '1', '3', '1', 'Android'),
(2, '2', '1', '1', 'Android'),
(3, '2', '3', '1', 'Android'),
(17, '1', '1', '1', 'Android');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `subscription`
--

CREATE TABLE `subscription` (
  `id` int(11) NOT NULL,
  `device_id` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `receipt` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `is_status` int(1) DEFAULT NULL COMMENT '1 = started , 2 = renewed , 3 = canceled',
  `expired_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `subscription`
--

INSERT INTO `subscription` (`id`, `device_id`, `receipt`, `is_status`, `expired_at`, `created_at`, `updated_at`) VALUES
(1, '3', '123456789', 3, '2021-04-04 15:26:18', '2021-04-04 15:26:18', '2021-04-04 15:27:54'),
(2, '2', '1234567897', 3, '2021-04-04 15:26:51', '2021-04-04 15:26:51', '2021-04-04 15:27:54');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `token`
--

CREATE TABLE `token` (
  `id` int(11) NOT NULL,
  `device_id` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `client_token` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `token`
--

INSERT INTO `token` (`id`, `device_id`, `client_token`, `created_at`, `updated_at`) VALUES
(1, '1', '$2y$10$G1B9/.PBKXu5nY9dZAhq2O6s8MyRp4EshI0xtyzTDGkVmMZFiFn5i', '2021-04-04 15:24:09', '2021-04-04 15:24:09'),
(2, '2', '$2y$10$F9ahgoP/yDPrTNJ591BRoeQ3k/M6OVen2yoyin7FwRE6dvY9ctaRC', '2021-04-04 15:24:22', '2021-04-04 15:24:22'),
(3, '3', '$2y$10$XAnR7qBYBzJM2Zl6o0.RkeMCzMOwT3VNja7EZixVaxEqB5Hb2eXfm', '2021-04-04 15:24:30', '2021-04-04 15:24:30'),
(17, '17', '$2y$10$jBeq2LOT2R/HKPs0GrzsDOdgR51gvMGVDIH5LO6JtpUt3H3EE8KUa', '2021-04-04 15:36:56', '2021-04-04 15:36:56');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `application`
--
ALTER TABLE `application`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `callback`
--
ALTER TABLE `callback`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `device`
--
ALTER TABLE `device`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `subscription`
--
ALTER TABLE `subscription`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `application`
--
ALTER TABLE `application`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `callback`
--
ALTER TABLE `callback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `device`
--
ALTER TABLE `device`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Tablo için AUTO_INCREMENT değeri `subscription`
--
ALTER TABLE `subscription`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `token`
--
ALTER TABLE `token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
