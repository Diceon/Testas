-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 12, 2019 at 10:24 AM
-- Server version: 10.1.37-MariaDB
-- PHP Version: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `testas`
--

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `id` int(11) NOT NULL,
  `answer` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`id`, `answer`) VALUES
(1, 'Linus Torvalds'),
(2, 'Bill Gates'),
(3, 'Steve Jobs'),
(4, '1991'),
(5, '1985'),
(6, '1976'),
(13, '333'),
(14, '444'),
(15, '55'),
(19, '999'),
(20, '888'),
(21, '777');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Linux'),
(2, 'Windows'),
(3, 'Apple');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `question` varchar(255) CHARACTER SET utf8 COLLATE utf8_lithuanian_ci NOT NULL,
  `answer` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `question`, `answer`) VALUES
(1, 'Linux OS įkūrėjas', 1),
(2, 'Windows OS įkūrėjas', 2),
(3, 'Apple įkūrėjas', 3),
(4, 'Kada atsirado Linux OS?', 4),
(5, 'Kada atsirado Windows OS?', 5),
(6, 'Kada atsirado Apple?', 6),
(9, '123', 13),
(11, '000', 19);

-- --------------------------------------------------------

--
-- Table structure for table `question_answers`
--

CREATE TABLE `question_answers` (
  `id` int(11) NOT NULL,
  `question` int(11) NOT NULL,
  `answer` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `question_answers`
--

INSERT INTO `question_answers` (`id`, `question`, `answer`) VALUES
(1, 1, 2),
(2, 1, 3),
(3, 1, 1),
(4, 4, 4),
(5, 4, 5),
(6, 4, 6),
(13, 9, 13),
(14, 9, 14),
(15, 9, 15),
(19, 11, 19),
(20, 11, 20),
(21, 11, 21);

-- --------------------------------------------------------

--
-- Table structure for table `question_categories`
--

CREATE TABLE `question_categories` (
  `id` int(11) NOT NULL,
  `question` int(11) NOT NULL,
  `category` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `question_categories`
--

INSERT INTO `question_categories` (`id`, `question`, `category`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3),
(4, 4, 1),
(5, 5, 2),
(6, 6, 3),
(9, 9, 1),
(11, 11, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `admin` tinyint(1) NOT NULL,
  `register_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `admin`, `register_date`) VALUES
(1, '123', '123', 1, '2019-03-07 20:03:31');

-- --------------------------------------------------------

--
-- Table structure for table `user_tests`
--

CREATE TABLE `user_tests` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_test_answers`
--

CREATE TABLE `user_test_answers` (
  `test` int(11) NOT NULL,
  `question` int(11) DEFAULT NULL,
  `answer` int(11) DEFAULT NULL,
  `answer_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_questions_answer_id` (`answer`);

--
-- Indexes for table `question_answers`
--
ALTER TABLE `question_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_question_answers_question_id` (`question`),
  ADD KEY `fk_question_answers_answer_id` (`answer`);

--
-- Indexes for table `question_categories`
--
ALTER TABLE `question_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_question_categories_question_id` (`question`),
  ADD KEY `fk_question_categories_category_id` (`category`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_tests`
--
ALTER TABLE `user_tests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_tests_user_id` (`user`),
  ADD KEY `fk_user_tests_category_id` (`category`);

--
-- Indexes for table `user_test_answers`
--
ALTER TABLE `user_test_answers`
  ADD KEY `fk_user_test_answers_question_id` (`question`),
  ADD KEY `fk_user_test_answers_test_id` (`test`),
  ADD KEY `fk_user_test_answers_answer_id` (`answer`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `question_answers`
--
ALTER TABLE `question_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `question_categories`
--
ALTER TABLE `question_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_tests`
--
ALTER TABLE `user_tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `fk_questions_answer_id` FOREIGN KEY (`answer`) REFERENCES `answers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `question_answers`
--
ALTER TABLE `question_answers`
  ADD CONSTRAINT `fk_question_answers_answer_id` FOREIGN KEY (`answer`) REFERENCES `answers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_question_answers_question_id` FOREIGN KEY (`question`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `question_categories`
--
ALTER TABLE `question_categories`
  ADD CONSTRAINT `fk_question_categories_category_id` FOREIGN KEY (`category`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `fk_question_categories_question_id` FOREIGN KEY (`question`) REFERENCES `questions` (`id`);

--
-- Constraints for table `user_tests`
--
ALTER TABLE `user_tests`
  ADD CONSTRAINT `fk_user_tests_category_id` FOREIGN KEY (`category`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `fk_user_tests_user_id` FOREIGN KEY (`user`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_test_answers`
--
ALTER TABLE `user_test_answers`
  ADD CONSTRAINT `fk_user_test_answers_answer_id` FOREIGN KEY (`answer`) REFERENCES `answers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_user_test_answers_question_id` FOREIGN KEY (`question`) REFERENCES `questions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_user_test_answers_test_id` FOREIGN KEY (`test`) REFERENCES `user_tests` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
