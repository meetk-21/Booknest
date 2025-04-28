-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 03, 2025 at 02:02 PM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `booknest`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
CREATE TABLE IF NOT EXISTS `books` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `author` varchar(100) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `description`, `price`, `created_at`) VALUES
(7, 'Never Let Me Go', 'Kazuo Ishiguro', 'A haunting and thought-provoking novel about a group of students at a boarding school, as they slowly uncover the unsettling truth about their purpose in life.', 7.99, '2025-04-01 15:23:45'),
(6, 'A Man Called Ove', 'Fredrik Backman', 'A heartwarming and humorous story about a grumpy old man whose life is transformed by the unexpected relationships he forms with his new neighbours.', 7.99, '2025-04-01 15:23:45'),
(5, 'The Ocean at the End of the Lane', 'Neil Gaiman', 'A poignant and eerie tale about a man who returns to his childhood home and begins to remember fantastical, otherworldly events that he had long forgotten.', 7.99, '2025-04-01 15:23:45'),
(4, 'Circe', 'Madeline Miller', 'A fresh and empowering retelling of the myth of Circe, the enchantress from Greek mythology, exploring her journey of transformation, love, and defiance against the gods.', 9.49, '2025-04-01 15:23:45'),
(3, 'Big Little Lies', 'Liane Moriarty', 'A suspenseful and darkly comedic exploration of the lives of three women, whose seemingly perfect lives unravel due to a tragic event at a school trivia night.', 6.99, '2025-04-01 15:23:45'),
(1, 'The Night Circus', 'Erin Morgenstern', 'A beautifully written, enchanting tale of a magical circus that opens only at night, where two illusionists are bound in a mysterious and competitive game.', 8.99, '2025-04-01 15:23:45'),
(2, 'The Midnight Library\r\n', 'Matt Haig', 'A woman dissatisfied with her life discovers a library between life and death where she can explore alternate versions of her life and see how different choices might have turned out.', 8.49, '2025-04-01 15:23:45'),
(8, 'The Haunting of Hill House', 'Shirley Jackson\r\n', 'A chilling classic of supernatural horror about a group of people who stay in a supposedly haunted house, where strange and terrifying events begin to unfold. It’s a tale of fear, isolation, and madness.', 7.99, '2025-04-01 15:23:45'),
(9, 'The Woman in White', 'Wilkie Collins', 'One of the earliest mystery novels, this gripping story follows Walter Hartright, who encounters a mysterious woman in white on a dark London street, leading him into a web of intrigue, deception, and hidden identities.', 6.99, '2025-04-01 15:23:45'),
(10, 'The Priory of the Orange Tree', 'Samantha Shannon', 'Four women from different lands unite to face an ancient dragon threatening their world.', 10.49, '2025-04-01 15:23:45');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `book_id` int NOT NULL,
  `user_id` int NOT NULL,
  `rating` int DEFAULT NULL,
  `review` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `book_id` (`book_id`),
  KEY `user_id` (`user_id`)
) ;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `book_id`, `user_id`, `rating`, `review`, `created_at`) VALUES
(1, 0, 1, 3, 'its good', '2025-04-01 15:25:56'),
(2, 0, 1, 4, 'hfth', '2025-04-01 17:01:53'),
(3, 0, 1, 4, 'hmm', '2025-04-03 13:48:13'),
(4, 0, 1, 4, 'hmm', '2025-04-03 13:48:13'),
(5, 0, 1, 4, 'hmm', '2025-04-03 13:48:13'),
(6, 0, 1, 4, 'hmm', '2025-04-03 13:48:13'),
(7, 0, 1, 4, 'hmm', '2025-04-03 13:48:23');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

ALTER TABLE users ADD COLUMN is_admin TINYINT(1) DEFAULT 0;
ALTER TABLE users ADD COLUMN active TINYINT(1) DEFAULT 1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `created_at`) VALUES
(1, 'root', '$2y$10$Ww9uM/R7WhaUMO3wbsS9L.TRPoZQGHXUKIOAPnnDE07ifmMhMhX5a', 'andy@andy.com', '2025-03-20 22:48:18'),
(2, '', '$2y$10$JNyrEobZgQ6FvBmzxGEgl.sh6wPTfc8lCtb7I..1nbhMDxVlqairO', '', '2025-04-01 17:36:09'),
(3, 'meet', '$2y$10$So0UZv/uE6KH348zsiCsqutZwtoVU8Fxi/CvUvC8u4YcnPtjNN4HW', 'meet@gmail.com', '2025-04-01 17:37:56');
COMMIT;

-- Make the root user an admin (assuming root user has ID 1)
UPDATE users SET is_admin = 1 WHERE id = 1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



