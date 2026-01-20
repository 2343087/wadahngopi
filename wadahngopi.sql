/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cafes`;
CREATE TABLE `cafes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `google_maps_url` text COLLATE utf8mb4_unicode_ci,
  `whatsapp_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `has_wifi` tinyint(1) NOT NULL DEFAULT '0',
  `rating` decimal(3,2) DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cafe_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int unsigned NOT NULL,
  `type` enum('coffee','non-coffee','food') COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menus_cafe_id_foreign` (`cafe_id`),
  CONSTRAINT `menus_cafe_id_foreign` FOREIGN KEY (`cafe_id`) REFERENCES `cafes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=156 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cafe_id` bigint unsigned NOT NULL,
  `user_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` tinyint NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reviews_cafe_id_foreign` (`cafe_id`),
  CONSTRAINT `reviews_cafe_id_foreign` FOREIGN KEY (`cafe_id`) REFERENCES `cafes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('wadahngopi-cache-livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3', 'i:1;', 1768892820),
('wadahngopi-cache-livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3:timer', 'i:1768892820;', 1768892820);

INSERT INTO `cafes` (`id`, `name`, `description`, `address`, `google_maps_url`, `whatsapp_number`, `has_wifi`, `rating`, `image_path`, `created_at`, `updated_at`, `latitude`, `longitude`) VALUES
(1, 'Coffee & Co. - SOUL', 'Quibusdam cum deserunt omnis laborum. Qui nulla corporis debitis nobis. Doloremque veritatis et voluptates amet ducimus sed. Ex nisi ea nihil rerum.', 'City Centrum Mall, 1st Floor, Samarinda', 'https://maps.google.com/?q=224+Hane+View%0ADorthaland%2C+MA+17631', '+1-820-401-4747', 1, '4.90', 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27', '-0.50281200', '117.15124000'),
(2, 'Coffee & Co', 'Vero praesentium mollitia excepturi molestiae illo eveniet nemo. Ea sapiente iste dolorum vel. Inventore enim illo illo eveniet.', 'Jl. Mulawarman No.171, Samarinda', 'https://maps.google.com/?q=13935+Mae+Trail%0AKennithport%2C+MT+70819-2586', '1-712-664-5758', 1, '4.90', 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27', '-0.50158200', '117.15389000'),
(3, 'Fren.co Coffee & Eatery', 'Aut voluptatem molestiae architecto quis porro voluptatibus. Repellendus sit doloremque sit ad tempore dolorum excepturi. Quia possimus atque sequi maxime atque. Provident quia praesentium veniam aut magnam illo.', 'Jl. Siradj Salman No.6a, Samarinda', 'https://maps.google.com/?q=665+Nikolaus+Haven%0AJoesphchester%2C+DC+70338', '(360) 319-6455', 1, '4.50', 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27', '-0.49012300', '117.12345600'),
(4, '212 COFFEE & SPACE', 'Quo minima vero itaque animi cum et. Et nostrum sint quo reprehenderit maxime assumenda. Quia fugiat veritatis numquam omnis neque. Magni asperiores ipsa accusantium qui ut aut maiores odit.', 'Jl. Bung Tomo No.18c, Samarinda', 'https://maps.google.com/?q=9018+VonRueden+Prairie%0AWest+Prestonberg%2C+IL+72940-8722', '762.805.7840', 1, '4.60', 'https://images.unsplash.com/photo-1469957761103-5594cd39769a?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27', '-0.52345600', '117.11223300'),
(5, 'Jack House COFFEE & EATERY', 'Velit eius aspernatur amet fugiat voluptatem quam fugit. Ut voluptatibus doloremque ea fuga maiores ad deleniti. Porro ipsa quam et atque quasi molestiae. Ab vero odio aut non omnis distinctio.', 'Jl. RE Martadinata No.06, Samarinda', 'https://maps.google.com/?q=843+Loyce+Rapid+Suite+489%0AJuanaberg%2C+OR+98526-0012', '+1-220-503-3623', 1, '4.90', 'https://images.unsplash.com/photo-1445116572660-236099ec97a2?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27', '-0.50456700', '117.14567800'),
(6, 'Pluto House And Coffee', 'Eum eum assumenda distinctio id qui maiores. Doloribus ut mollitia sit sequi. Voluptatem provident magnam itaque ad laboriosam quod.', 'Jl. Angklung, Samarinda', 'https://maps.google.com/?q=81024+Wilson+Shore+Suite+268%0ALindgrenchester%2C+NM+83947-1113', '+1-432-731-1738', 1, '4.40', 'https://images.unsplash.com/photo-1507133750040-4a8f57021571?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27', '-0.48567800', '117.15678900'),
(7, 'Pot O Koffie', 'Delectus velit quo culpa eaque. Et voluptas aperiam sunt accusantium. Inventore nisi qui ut qui nostrum excepturi ut.', 'Jl. Angklung No.4, Samarinda', 'https://maps.google.com/?q=45672+Fisher+Manor%0AIsmaelburgh%2C+AK+64333', '1-618-960-9867', 1, '4.60', 'https://images.unsplash.com/photo-1521017432531-fbd92d768814?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27', '-0.48512300', '117.15789000'),
(8, 'Labricca Coffee', 'Quae quibusdam cum quaerat reprehenderit architecto qui cupiditate. Deserunt eveniet dolore quia non. Deserunt maiores vel eos quidem debitis consequuntur dolore. Fugiat in qui laudantium.', 'Jl. Gerilya, Samarinda', 'https://maps.google.com/?q=6401+Osinski+Station+Apt.+462%0AEast+Trycia%2C+TX+36377', '+19379376699', 1, '4.70', 'https://images.unsplash.com/photo-1525610553991-2bede1a236e2?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27', '-0.45678900', '117.17890100'),
(9, 'satukata coffee co.', 'Dolorem modi assumenda ducimus omnis cupiditate animi rerum. Dicta qui earum eaque sapiente tenetur consectetur est. Et accusantium ut rerum omnis id. Distinctio quos voluptas minus.', 'Jl. Basuki Rahmat, Samarinda', 'https://maps.google.com/?q=309+Rodriguez+Cove%0AMedhurstland%2C+NE+32955-1787', '(316) 732-6256', 1, '4.40', 'https://images.unsplash.com/photo-1481833761820-0509d3217039?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27', '-0.49123400', '117.14123400'),
(10, 'Titik Koma Antasari Samarinda', 'Esse quisquam eos sint dolor accusantium. Est rerum nihil eius quidem. Velit voluptatem voluptatem molestias enim. Id soluta modi ab vel.', 'Jl. P Antasari No.20 B, Samarinda', 'https://maps.google.com/?q=67599+Domingo+Springs+Suite+837%0AKinghaven%2C+AR+78293-5815', '+13326571904', 1, '4.80', 'https://images.unsplash.com/photo-1559925393-8be0ec41b504?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27', '-0.49567800', '117.13567800'),
(11, 'Kana Coffee', 'Voluptas molestiae officiis corporis necessitatibus eum. Tempora exercitationem pariatur nemo distinctio. Labore iure velit earum unde. Explicabo eos nisi recusandae quas quidem sunt dignissimos.', 'Jl. Muso Salim No.53, Samarinda', 'https://maps.google.com/?q=2990+Flo+Extension%0ABergnaumside%2C+MO+78609-6712', '+1 (909) 524-4434', 1, '4.80', 'https://images.unsplash.com/photo-1551887139-12a8627f8059?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27', '-0.49890100', '117.16123400'),
(12, 'YOU Coffee and Brunch', 'Et quae impedit aliquid et eum. Deserunt aut delectus nisi. Quae debitis commodi iusto repudiandae et fuga. Quo eos consectetur molestiae qui qui.', 'Jl. Gamelan No.2, Samarinda', 'https://maps.google.com/?q=35990+Morissette+Street+Suite+697%0AHillardfort%2C+KY+57648-5146', '+1-425-567-1902', 1, '4.80', 'https://images.unsplash.com/photo-1521017432531-fbd92d768814?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27', '-0.48123400', '117.15123400'),
(13, 'Althea Coffee & Co', 'Quo voluptatem molestiae id id delectus nesciunt aut commodi. At iure neque rerum ut. Repellat sit est et deleniti. Quod animi est et sit non autem dolorum.', 'Jl. Perjuangan No.99, Samarinda', 'https://maps.google.com/?q=3697+Erick+Fork+Suite+550%0ANew+Napoleonchester%2C+NM+60983-7064', '747.673.6147', 1, '4.90', 'https://images.unsplash.com/photo-1497935586351-b67a49e012bf?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27', '-0.47123400', '117.16123400'),
(14, 'KOPIKUMANA', 'Rerum quod vero illum cumque culpa sunt nostrum. Voluptas minus sit ut aut ea suscipit et provident. Dolor dicta labore sint. Quo molestiae beatae fugit.', 'Jl. Angklung No.06A, Samarinda', 'https://maps.google.com/?q=6544+Hamill+Corners+Suite+997%0ALake+Demetriusmouth%2C+NJ+22823', '(503) 461-6714', 1, '4.70', 'https://images.unsplash.com/photo-1453614512568-c4024d13c247?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27', '-0.48590100', '117.15567800'),
(15, 'Jakarta Loc. Coffe and Space', 'Natus quidem rerum eligendi. Libero ipsum nesciunt provident sed quos ad quia. Qui magni et quidem saepe.', 'Jl. Ar-Rasyidin 2, Samarinda', 'https://maps.google.com/?q=222+O%27Keefe+Tunnel%0ANorth+Jordonfurt%2C+KY+27958', '1-319-951-4339', 1, '4.70', 'https://images.unsplash.com/photo-1524350303359-29c67670732d?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27', '-0.51123400', '117.13123400'),
(16, 'Kong Djie Coffee Samarinda', 'Est velit vel magnam quia quidem. Id amet vero pariatur laudantium velit. Est quo molestiae architecto esse. Unde rerum minima error commodi ex dolor dolorem.', 'Jl. Niaga Utara, Samarinda', 'https://maps.google.com/?q=205+Stroman+Spurs%0ACaesarberg%2C+MT+80459', '+14323772043', 1, '4.30', 'https://images.unsplash.com/photo-1561047029-3000c6812c53?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27', '-0.50123400', '117.15123400'),
(17, 'Teras Roemah Samarinda', 'Sed aut cumque eos excepturi quis perferendis et. Unde ut animi quia magnam sapiente aliquid. Ab omnis aliquam nulla illum praesentium deleniti asperiores.', 'Gg. Alam Indah, Samarinda', 'https://maps.google.com/?q=36772+Bernhard+Lights%0AWest+Aglae%2C+MI+64466', '1-940-205-5677', 1, '4.50', 'https://images.unsplash.com/photo-1522012188892-24beb302783d?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27', '-0.49123400', '117.16123400'),
(18, '28 Coffee Samarinda ARH', 'Debitis esse optio est possimus. Totam quo natus neque qui consequatur. Est nemo impedit voluptatem similique tempore. Sit est sit similique voluptatum quaerat natus.', 'Jl. Aris Rahman Hakim No.14, Samarinda', 'https://maps.google.com/?q=466+Ezekiel+Oval%0AWest+Arnulfo%2C+SC+09799-9471', '586.970.3860', 1, '5.00', 'https://images.unsplash.com/photo-1504639725590-34d0984388bd?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27', '-0.48678900', '117.14123400'),
(19, 'MoveOnCafe', 'Eum aut at repellendus hic nulla numquam. Eaque odio dicta enim sint dolorum omnis et. Quam corrupti omnis eos. Quasi et est voluptas cupiditate.', 'Jl. Mawar No.S-15, Samarinda', 'https://maps.google.com/?q=1226+Schuster+Court%0ANew+Jerry%2C+IN+39748-6717', '(910) 719-1260', 1, '4.50', 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27', '-0.49678900', '117.15123400'),
(20, 'Saqa Coffee House And Space', 'Dolores omnis tempora dolor quaerat laudantium ipsum. Placeat nam nihil corrupti assumenda. Explicabo reiciendis provident et impedit.', 'Jl. Wijaya Kusuma 9A No.4, Samarinda', 'https://maps.google.com/?q=41062+Brown+Union+Apt.+560%0AHarberburgh%2C+CT+11232-3452', '272-389-4558', 1, '4.70', 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27', '-0.47678900', '117.11123400');



INSERT INTO `menus` (`id`, `cafe_id`, `name`, `price`, `type`, `image_path`, `created_at`, `updated_at`) VALUES
(1, 1, 'ut Kopi', 32893, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(2, 1, 'saepe Kopi', 32094, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(3, 1, 'minima Kopi', 49503, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(4, 1, 'iure Kopi', 36818, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(5, 1, 'quis Kopi', 21426, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(6, 1, 'velit Kopi', 17498, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(7, 1, 'error Kopi', 15367, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(8, 1, 'quia Kopi', 20886, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(9, 1, 'est Kopi', 15873, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(10, 2, 'repellendus Kopi', 44376, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(11, 2, 'eos Kopi', 47845, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(12, 2, 'itaque Kopi', 43567, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(13, 2, 'qui Kopi', 18740, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(14, 2, 'accusantium Kopi', 20395, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(15, 2, 'vero Kopi', 40137, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(16, 2, 'molestiae Kopi', 42721, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(17, 3, 'quis Kopi', 18158, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(18, 3, 'laudantium Kopi', 24366, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(19, 3, 'explicabo Kopi', 38112, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(20, 3, 'et Kopi', 34020, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(21, 3, 'est Kopi', 47333, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(22, 3, 'et Kopi', 45454, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(23, 3, 'id Kopi', 36016, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(24, 3, 'sit Kopi', 44422, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(25, 3, 'soluta Kopi', 47761, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(26, 3, 'est Kopi', 42643, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(27, 4, 'assumenda Kopi', 35156, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(28, 4, 'rerum Kopi', 42619, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(29, 4, 'voluptatem Kopi', 31274, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(30, 4, 'possimus Kopi', 37306, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(31, 4, 'cupiditate Kopi', 36090, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(32, 4, 'consequatur Kopi', 25562, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(33, 4, 'voluptas Kopi', 30953, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(34, 4, 'ad Kopi', 22523, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(35, 4, 'quasi Kopi', 27189, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(36, 5, 'recusandae Kopi', 23020, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(37, 5, 'assumenda Kopi', 28634, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(38, 5, 'amet Kopi', 22720, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(39, 5, 'laboriosam Kopi', 39766, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(40, 5, 'ducimus Kopi', 16699, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(41, 5, 'minima Kopi', 20560, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(42, 5, 'unde Kopi', 27458, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(43, 5, 'nisi Kopi', 49736, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(44, 5, 'magnam Kopi', 28112, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(45, 5, 'officiis Kopi', 19163, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(46, 6, 'nihil Kopi', 40170, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(47, 6, 'a Kopi', 30764, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(48, 6, 'quidem Kopi', 36080, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(49, 6, 'rerum Kopi', 36736, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(50, 6, 'praesentium Kopi', 27184, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(51, 6, 'quo Kopi', 17894, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(52, 6, 'quasi Kopi', 28153, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(53, 6, 'non Kopi', 21684, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(54, 6, 'ipsa Kopi', 32252, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(55, 7, 'excepturi Kopi', 49403, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(56, 7, 'et Kopi', 38405, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(57, 7, 'aut Kopi', 30209, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(58, 7, 'fugiat Kopi', 28718, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(59, 7, 'et Kopi', 30942, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(60, 7, 'autem Kopi', 37115, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(61, 7, 'magni Kopi', 38183, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(62, 7, 'eos Kopi', 28673, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(63, 8, 'ducimus Kopi', 26246, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(64, 8, 'accusantium Kopi', 45666, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(65, 8, 'temporibus Kopi', 44961, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(66, 8, 'eos Kopi', 45454, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(67, 8, 'ex Kopi', 27459, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(68, 8, 'et Kopi', 42197, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(69, 9, 'qui Kopi', 23729, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(70, 9, 'ipsum Kopi', 41540, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(71, 9, 'doloremque Kopi', 32962, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(72, 9, 'error Kopi', 46982, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(73, 9, 'nihil Kopi', 31324, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(74, 9, 'perspiciatis Kopi', 28761, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(75, 10, 'fugit Kopi', 29408, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(76, 10, 'excepturi Kopi', 18222, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(77, 10, 'corporis Kopi', 33193, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(78, 10, 'aut Kopi', 42590, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(79, 10, 'possimus Kopi', 20981, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(80, 10, 'quo Kopi', 28477, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(81, 10, 'et Kopi', 38379, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(82, 10, 'eos Kopi', 27087, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(83, 11, 'sunt Kopi', 23382, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(84, 11, 'porro Kopi', 23792, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(85, 11, 'eum Kopi', 20158, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(86, 11, 'dicta Kopi', 30756, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(87, 11, 'saepe Kopi', 24063, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(88, 11, 'tempore Kopi', 17397, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(89, 11, 'sed Kopi', 23689, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(90, 11, 'eum Kopi', 20674, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(91, 11, 'modi Kopi', 27968, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(92, 12, 'et Kopi', 37622, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(93, 12, 'consequatur Kopi', 26805, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(94, 12, 'expedita Kopi', 38604, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(95, 12, 'ullam Kopi', 23764, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(96, 12, 'et Kopi', 21024, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(97, 12, 'ducimus Kopi', 37924, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(98, 12, 'rerum Kopi', 40628, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(99, 13, 'hic Kopi', 25688, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(100, 13, 'ducimus Kopi', 17497, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(101, 13, 'perferendis Kopi', 43119, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(102, 13, 'adipisci Kopi', 44812, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(103, 13, 'quos Kopi', 21663, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(104, 13, 'qui Kopi', 26544, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(105, 13, 'et Kopi', 19164, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(106, 13, 'quos Kopi', 32263, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(107, 13, 'quo Kopi', 21510, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(108, 14, 'est Kopi', 43989, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(109, 14, 'est Kopi', 43876, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(110, 14, 'repudiandae Kopi', 43967, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(111, 14, 'quo Kopi', 40308, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(112, 14, 'ipsam Kopi', 27636, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(113, 15, 'aut Kopi', 37966, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(114, 15, 'sed Kopi', 17448, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(115, 15, 'est Kopi', 26529, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(116, 15, 'sed Kopi', 47600, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(117, 15, 'facere Kopi', 25198, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(118, 15, 'vel Kopi', 33744, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(119, 15, 'architecto Kopi', 45500, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(120, 15, 'aperiam Kopi', 42545, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(121, 16, 'incidunt Kopi', 46232, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(122, 16, 'sed Kopi', 16907, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(123, 16, 'voluptas Kopi', 42062, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(124, 16, 'in Kopi', 43124, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(125, 16, 'illo Kopi', 44115, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(126, 16, 'quis Kopi', 16619, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(127, 16, 'occaecati Kopi', 40330, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(128, 16, 'quisquam Kopi', 32328, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(129, 16, 'qui Kopi', 32409, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(130, 16, 'facere Kopi', 16982, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(131, 17, 'incidunt Kopi', 19085, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(132, 17, 'nemo Kopi', 35594, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(133, 17, 'fugit Kopi', 25350, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(134, 17, 'optio Kopi', 45387, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(135, 17, 'et Kopi', 49457, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(136, 17, 'placeat Kopi', 43267, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(137, 18, 'animi Kopi', 48903, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(138, 18, 'illo Kopi', 28668, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(139, 18, 'aliquam Kopi', 32024, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(140, 18, 'tempora Kopi', 23759, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(141, 18, 'et Kopi', 31677, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(142, 18, 'et Kopi', 28512, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(143, 18, 'eaque Kopi', 44929, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(144, 19, 'non Kopi', 32826, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(145, 19, 'alias Kopi', 23041, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(146, 19, 'maiores Kopi', 37331, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(147, 19, 'dolorem Kopi', 30965, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(148, 19, 'aut Kopi', 42217, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(149, 20, 'laboriosam Kopi', 34534, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(150, 20, 'autem Kopi', 29887, 'non-coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(151, 20, 'et Kopi', 20599, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(152, 20, 'dignissimos Kopi', 20004, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(153, 20, 'debitis Kopi', 34044, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(154, 20, 'non Kopi', 25205, 'food', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(155, 20, 'ipsam Kopi', 32824, 'coffee', 'https://images.unsplash.com/photo-1541167760496-1628856ab752?auto=format&fit=crop&q=80&w=800', '2026-01-20 07:03:27', '2026-01-20 07:03:27');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_20_044111_create_cafes_table', 1),
(5, '2026_01_20_044129_create_menus_table', 1),
(6, '2026_01_20_044133_create_reviews_table', 1),
(7, '2026_01_20_070130_add_coordinates_to_cafes_table', 1);

INSERT INTO `reviews` (`id`, `cafe_id`, `user_name`, `rating`, `comment`, `created_at`, `updated_at`) VALUES
(1, 1, 'Prof. Trinity Haag', 5, 'Vel aut ratione et molestiae ut similique harum.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(2, 1, 'Dr. Allie Kris Jr.', 5, 'Facilis accusantium odit voluptatibus id asperiores deserunt quo.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(3, 1, 'Mr. Enos Frami', 5, 'Consequatur aut in voluptas ipsa dolorem non aut porro.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(4, 2, 'Dominic Haley II', 4, 'Sunt repellat voluptatem eius.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(5, 2, 'Dr. Grayce Von DVM', 1, 'Sunt aliquid eligendi fugiat aperiam.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(6, 2, 'Ariel Gleichner', 2, 'Cumque deserunt rem voluptatibus.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(7, 3, 'Aron Reynolds', 5, 'Veniam omnis eaque amet et et qui quia aut.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(8, 3, 'Elinore VonRueden II', 3, 'Nostrum et quod unde qui tempore illo temporibus.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(9, 3, 'Lonie Feest', 4, 'Quos consequuntur eligendi qui magni dignissimos.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(10, 4, 'Walter Abshire', 1, 'Ullam vitae animi rerum dolores.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(11, 4, 'Mr. Nathen O\'Conner PhD', 4, 'Aliquid velit reiciendis consequatur qui molestiae numquam eaque rerum.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(12, 4, 'Madonna Schuppe', 5, 'Enim optio laborum nostrum doloremque dolorem quod.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(13, 5, 'Pierre Schumm', 1, 'Distinctio mollitia eveniet totam ipsa.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(14, 5, 'Trudie Eichmann II', 4, 'Quia est quibusdam quibusdam asperiores dolorem ducimus omnis.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(15, 5, 'Vance Hodkiewicz', 3, 'Tenetur voluptatibus facere omnis earum blanditiis dolores culpa dolores.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(16, 6, 'Shanelle Pfannerstill', 2, 'Sit debitis quia ea.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(17, 6, 'Lucy Koss', 3, 'Architecto dolor laborum autem sunt aperiam.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(18, 6, 'Mr. Xzavier Marvin', 3, 'Sit voluptatibus quod ad dolores ex ab.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(19, 7, 'Adella Windler', 4, 'Voluptatem doloribus dolore qui eos ullam illum quos.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(20, 7, 'Peggie Adams', 2, 'Consequatur ut rerum in saepe quidem.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(21, 7, 'Alessandra Rolfson', 3, 'Quidem repellendus placeat enim enim rem.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(22, 8, 'Hassan Hessel', 2, 'Assumenda quis et quia dolorem facere.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(23, 8, 'Prof. Brisa Pouros III', 2, 'Atque qui qui nostrum repudiandae vitae aliquam odio rerum.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(24, 8, 'Ms. Letha Zulauf III', 4, 'Occaecati qui rerum natus unde autem.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(25, 9, 'Jamil Marvin', 2, 'Error sed consequatur ut omnis doloribus sed beatae.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(26, 9, 'Bonita Schuppe DVM', 5, 'Illo porro provident eos ipsa molestiae rerum eligendi.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(27, 9, 'Serena Schneider', 5, 'Ut qui neque magnam iure ipsam soluta temporibus nesciunt.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(28, 9, 'Vallie Fisher', 4, 'Quia dolor omnis in cum.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(29, 9, 'Moshe Bergnaum', 5, 'Quam sunt error est sequi.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(30, 10, 'Adella Zboncak', 5, 'Optio enim ea harum ut hic minima architecto.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(31, 10, 'Travon Wisoky', 4, 'Et iusto officiis quisquam nesciunt dolorem nam.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(32, 10, 'Araceli Runte DDS', 3, 'Adipisci dolore velit et ipsam aut cupiditate tempore.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(33, 10, 'Prof. Christian Emmerich MD', 5, 'Et harum et iusto.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(34, 10, 'Ms. Noelia Gerlach', 4, 'Et ut quisquam earum quo inventore aspernatur.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(35, 11, 'Kelley Dach', 3, 'Quasi aut sunt magnam facere earum qui laborum.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(36, 11, 'Elsie Schumm', 5, 'Ipsa quia sit maiores eius hic in.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(37, 11, 'Leif Lang', 4, 'Similique beatae nulla reprehenderit rerum ut itaque ipsam adipisci.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(38, 11, 'Dr. Elliott Reichel Sr.', 1, 'Non quia illo id earum voluptates commodi dolore.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(39, 12, 'Hassie Feest', 4, 'Et maxime est minima dicta ipsa.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(40, 12, 'Kiel Tillman', 2, 'Perspiciatis cumque tempore et vel.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(41, 12, 'Ms. Alivia Nolan DDS', 4, 'In aut quis animi illum quisquam ea dolores.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(42, 12, 'Rudolph Corkery', 1, 'Aut laboriosam hic laboriosam beatae aspernatur quae culpa velit.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(43, 13, 'Kimberly Ledner', 4, 'Voluptatem enim et deleniti asperiores.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(44, 13, 'Leland Okuneva', 1, 'Rerum quas excepturi provident quia amet iusto.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(45, 13, 'Aleen Gerlach', 1, 'Voluptates quos est aut.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(46, 14, 'Bernie Zboncak Jr.', 5, 'Est in blanditiis et velit.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(47, 14, 'Beryl Hansen', 1, 'Tempore eligendi voluptatem iusto sapiente ipsum porro ut.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(48, 14, 'Prof. Wiley Stokes', 5, 'Impedit quia velit iste est ipsa necessitatibus.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(49, 15, 'Asia Goodwin', 4, 'Delectus id sint cumque nihil possimus amet.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(50, 15, 'Joanie Stark', 3, 'Alias fugiat aliquid harum qui libero laboriosam.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(51, 15, 'Dr. Keenan Reichel II', 1, 'Numquam neque dolor aut rem voluptate.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(52, 15, 'Prof. Dagmar Cormier III', 5, 'Sequi quia consequuntur similique.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(53, 15, 'Emmy Donnelly', 3, 'Consequuntur ea quidem eum omnis voluptates sint.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(54, 16, 'Chauncey Howe', 2, 'Temporibus qui iure beatae.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(55, 16, 'Stewart Lynch', 4, 'Aut voluptates qui hic delectus nostrum aut aut.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(56, 16, 'Daniela Quitzon', 5, 'Repellat in adipisci ipsa nam quis.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(57, 17, 'Rusty O\'Hara', 2, 'Dicta dolore aut cupiditate culpa mollitia.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(58, 17, 'Jay Ankunding', 2, 'Cum repellat est optio velit saepe sint magnam nostrum.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(59, 17, 'Willie Brown DDS', 3, 'Molestias ducimus illum blanditiis magni.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(60, 18, 'Juvenal Lemke', 3, 'Dolor nesciunt et veniam animi sed voluptas maiores quam.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(61, 18, 'Keira Hamill', 5, 'Ut est suscipit eaque corporis.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(62, 18, 'Cortez Nienow', 5, 'Dicta odio labore temporibus quibusdam enim adipisci.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(63, 18, 'Meagan Huels', 2, 'Aut tempora deserunt dolore aut delectus vero.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(64, 18, 'Xavier Brekke', 3, 'Et voluptatem voluptas id eaque quidem.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(65, 19, 'Alexa Hammes', 2, 'Qui non dolor autem dolor aliquam dolore voluptatem autem.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(66, 19, 'Laverne Okuneva', 5, 'Veritatis voluptatem nisi molestiae ut vel voluptatem nostrum voluptatem.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(67, 19, 'Elroy Schuster', 3, 'Architecto laborum quod iste perferendis voluptatibus et sit.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(68, 19, 'Prof. Mose Altenwerth I', 3, 'Incidunt veniam sapiente et et dolores tenetur.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(69, 20, 'Winnifred McDermott', 4, 'Qui dolorum quibusdam id aut minus.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(70, 20, 'Miss Brionna Swaniawski', 1, 'Porro quo atque non dolor sed ad.', '2026-01-20 07:03:27', '2026-01-20 07:03:27'),
(71, 20, 'Colby Schumm', 3, 'Molestiae beatae sit ut ipsam voluptate est.', '2026-01-20 07:03:27', '2026-01-20 07:03:27');
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('1r9tFiac2ZU4ChxXIdrymuyM2N3azCS1pg7MP859', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMlVzZTVWR1lxSHo0SVowTmwwTk02NDYxNlZhTWx2alR4dkpsSkN5YyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly93YWRhaG5nb3BpLnRlc3QvcHJvZmlsZSI7czo1OiJyb3V0ZSI7czo3OiJwcm9maWxlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1768894081),
('Ss3KuxlCSztI5fj2eizlVX1Lst6PLtczfgtQ9O3t', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiRE9KY25OS1M2SGxBbTBCc1dXNFdQMHhDQ0pDNFRyaHFnNVl5QnBlZyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjI6Imh0dHA6Ly93YWRhaG5nb3BpLnRlc3QiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjA6e31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiJjYjMwZjk1MWU2Nzg4ZDFmZTQ0MTM4MDFiNTA2OWFkNDdiNWFmNmM4NDVjMDJiYTEyOGQ2YTg1NWE2M2QxODNlIjt9', 1768893042);
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'WadahNgopi Admin', 'admin@wadahngopi.test', '2026-01-20 07:03:28', '$2y$12$.85lALK5mt.Xolc3JBoBh.dK1cpLqx0nkUqLX.F.eqR5D.RQePLhu', 'plkWBataCM', '2026-01-20 07:03:28', '2026-01-20 07:03:28');


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;