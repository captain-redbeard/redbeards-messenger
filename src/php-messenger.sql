-- phpMyAdmin SQL Dump
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 03, 2016 at 11:31 PM

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `php-messenger`
--

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `contact_id` int(11) NOT NULL,
  `contact_guid` varchar(32) NOT NULL,
  `contact_alias` varchar(64) NOT NULL,
  `user_guid` varchar(32) NOT NULL,
  `made_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `contact_requests`
--

CREATE TABLE `contact_requests` (
  `request_id` int(11) NOT NULL,
  `request_guid` varchar(6) NOT NULL,
  `request_name` varchar(64) NOT NULL,
  `user_guid` varchar(32) NOT NULL,
  `expire` tinyint(2) NOT NULL DEFAULT '1',
  `made_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `conversation_id` int(11) NOT NULL,
  `conversation_guid` varchar(32) NOT NULL,
  `contact_guid` varchar(32) NOT NULL,
  `user_guid` varchar(32) NOT NULL,
  `made_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `user_id` int(11) NOT NULL,
  `made_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `conversation_guid` varchar(32) NOT NULL,
  `user1_guid` varchar(32) NOT NULL,
  `user2_guid` varchar(32) NOT NULL,
  `direction` tinyint(1) NOT NULL DEFAULT '0',
  `message` blob NOT NULL,
  `made_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_guid` varchar(32) NOT NULL,
  `username` varchar(64) NOT NULL,
  `password` varchar(256) NOT NULL,
  `secret_key` varchar(32) NOT NULL,
  `activation` varchar(32) NOT NULL,
  `timezone` varchar(128) NOT NULL DEFAULT 'UTC',
  `mfa_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `password_reset` tinyint(1) NOT NULL DEFAULT '0',
  `last_load` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  `made_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`contact_id`),
  ADD KEY `contact_guid` (`contact_guid`),
  ADD KEY `user_guid` (`user_guid`),
  ADD KEY `contact_id` (`contact_id`);

--
-- Indexes for table `contact_requests`
--
ALTER TABLE `contact_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `request_id` (`request_id`),
  ADD KEY `request_guid` (`request_guid`),
  ADD KEY `user_guid` (`user_guid`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`conversation_id`),
  ADD KEY `conversation_id` (`conversation_id`),
  ADD KEY `conversation_guid` (`conversation_guid`),
  ADD KEY `contact_guid` (`contact_guid`),
  ADD KEY `user_guid` (`user_guid`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `conversation_guid` (`conversation_guid`),
  ADD KEY `user1_guid` (`user1_guid`),
  ADD KEY `user2_guid` (`user2_guid`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `user_guid` (`user_guid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `contact_requests`
--
ALTER TABLE `contact_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `conversation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
