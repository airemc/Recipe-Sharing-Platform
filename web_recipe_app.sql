-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost
-- Üretim Zamanı: 23 Kas 2025, 17:55:54
-- Sunucu sürümü: 10.4.28-MariaDB
-- PHP Sürümü: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `web_recipe_app`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'Soups'),
(2, 'Main Courses'),
(3, 'Desserts'),
(4, 'Salads'),
(5, 'Appetizers');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `recipes`
--

CREATE TABLE `recipes` (
  `recipe_id` int(11) NOT NULL,
  `recipe_name` varchar(255) NOT NULL,
  `ingredients` text NOT NULL,
  `instructions` text NOT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `added_user_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `recipes`
--

INSERT INTO `recipes` (`recipe_id`, `recipe_name`, `ingredients`, `instructions`, `picture`, `added_user_id`, `category_id`) VALUES
(2, 'Tomato Soup', '1 tbsp Olive Oil\r\n1 Onion, chopped\r\n2 cloves Garlic, minced\r\n1 can (800g) Crushed Tomatoes\r\n500ml Vegetable Broth\r\n100ml Cream (optional)\r\nSalt and pepper to taste', '1. Heat olive oil in a large pot over medium heat.\r\n2. Add chopped onion and cook until soft (about 5 minutes). Add garlic and cook for 1 more minute.\r\n3. Pour in the crushed tomatoes and vegetable broth.\r\n4. Bring to a boil, then reduce heat and let it simmer for 15-20 minutes.\r\n5. Use an immersion blender to blend the soup until smooth.\r\n6. If using, stir in the cream. Add salt and pepper to taste.', 'uploads/691365b376c33_pexels-foodie-factor-162291-539451.jpg', 1, 1),
(3, 'Garlic Lemon Chicken Breast', '2 boneless, skinless Chicken Breasts\r\n2 tbsp Olive Oil\r\n3 cloves Garlic, minced\r\n1/4 cup Chicken Broth\r\nJuice of 1 Lemon\r\n1 tbsp Parsley, chopped\r\nSalt and pepper', '1. Season chicken breasts with salt and pepper on both sides.\r\n2. Heat olive oil in a skillet over medium-high heat.\r\n3. Cook chicken for 5-7 minutes per side, until golden brown and cooked through. Remove chicken from the skillet and set aside.\r\n4. In the same skillet, add the minced garlic and cook for 30 seconds until fragrant.\r\n5. Pour in the chicken broth and lemon juice, scraping up any brown bits from the bottom of the pan.\r\n6. Bring the sauce to a simmer and let it reduce slightly for 2-3 minutes.\r\n7. Return the chicken to the skillet and spoon the sauce over it.\r\n8. Garnish with fresh parsley before serving.', 'uploads/6913658cb3129_pexels-harry-dona-2338407.jpg', 1, 2),
(4, 'Quick Greek Salad', '1 Cucumber, chopped\r\n1 cup Cherry Tomatoes, halved\r\n1/2 Red Onion, thinly sliced\r\n1/2 cup Kalamata Olives\r\n1/2 cup Feta Cheese, crumbled\r\nFor Dressing: 2 tbsp Olive Oil, 1 tbsp Red Wine Vinegar, 1/2 tsp Dried Oregano, Salt and pepper', '1. In a large bowl, combine the chopped cucumber, cherry tomatoes, red onion, and olives.\r\n2. In a separate small bowl, whisk together the dressing ingredients: olive oil, red wine vinegar, oregano, salt, and pepper.\r\n3. Pour the dressing over the vegetable mixture and toss gently to combine.\r\n4. Sprinkle the crumbled feta cheese on top just before serving.', 'uploads/691365665f26e_pexels-iina-luoto-460132-1211887.jpg', 1, 4),
(5, '5-Minute Chocolate Mug Cake', '4 tbsp All-Purpose Flour\r\n4 tbsp Sugar\r\n2 tbsp Unsweetened Cocoa Powder\r\n1/4 tsp Baking Powder\r\n3 tbsp Milk\r\n2 tbsp Vegetable Oil\r\n1/4 tsp Vanilla Extract\r\n(Optional: 1 tbsp Chocolate Chips)', '1. In a large, microwave-safe mug, add all the dry ingredients (flour, sugar, cocoa powder, baking powder) and whisk them together.\r\n2. Add the wet ingredients (milk, oil, vanilla) to the mug.\r\n3. Whisk until the batter is smooth and there are no lumps.\r\n4. (Optional: Stir in the chocolate chips).\r\n5. Microwave on high for 90 seconds (for a 1000W microwave).\r\n6. Let it cool for a minute before eating. Be careful, the mug will be hot.', 'uploads/690d9f98092e1_pexels-zdenek-rosenthaler-1395581-2764272.jpg', 1, 3);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `reviews`
--

INSERT INTO `reviews` (`review_id`, `recipe_id`, `user_id`, `rating`, `comments`, `created_at`) VALUES
(1, 5, 1, 5, '', '2025-11-17 16:16:23'),
(2, 3, 1, 5, '', '2025-11-17 16:18:46');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `user_password`, `user_email`) VALUES
(1, 'airem', '$2y$10$lt/ECPe7tMPzwL5Zljui1OlX15iGgcRLGo15TbnjxKTlx5sM2Oq/i', 'airemcolak@gmail.com');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Tablo için indeksler `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`recipe_id`),
  ADD KEY `added_user_id` (`added_user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Tablo için indeksler `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `recipe_id` (`recipe_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_name` (`user_name`),
  ADD UNIQUE KEY `user_email` (`user_email`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `recipes`
--
ALTER TABLE `recipes`
  MODIFY `recipe_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`added_user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `recipes_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Tablo kısıtlamaları `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`recipe_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
