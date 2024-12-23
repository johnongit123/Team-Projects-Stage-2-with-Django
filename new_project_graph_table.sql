-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 11, 2024 at 01:14 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `team project part 3 database`
--

-- --------------------------------------------------------

--
-- Table structure for table `project_graph_table`
--

CREATE TABLE `project_graph_table` (
  `ProjectID` int(11) NOT NULL,
  `GraphID` int(11) NOT NULL,
  `graph_title` varchar(255) NOT NULL,
  `graph_content` enum('count','duration') NOT NULL,
  `graph_type` enum('bar','pie','column') NOT NULL,
  `graph_filter_complete` tinyint(1) NOT NULL,
  `graph_filter_not_started` tinyint(1) NOT NULL,
  `graph_filter_ongoing` tinyint(1) NOT NULL,
  `graph_filter_overdue` tinyint(1) NOT NULL,
  `graph_filter_paused` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_graph_table`
--

INSERT INTO `project_graph_table` (`ProjectID`, `GraphID`, `graph_title`, `graph_content`, `graph_type`, `graph_filter_complete`, `graph_filter_not_started`, `graph_filter_ongoing`, `graph_filter_overdue`, `graph_filter_paused`) VALUES
(1, 1, 'Bar Chart', 'duration', 'bar', 1, 1, 0, 0, 0),
(1, 2, 'Title', 'count', 'pie', 0, 0, 0, 0, 0),
(1, 3, 'New Column Chart', 'duration', 'column', 0, 0, 0, 0, 0),
(1, 4, '', 'count', 'pie', 0, 0, 0, 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `project_graph_table`
--
ALTER TABLE `project_graph_table`
  ADD PRIMARY KEY (`GraphID`),
  ADD KEY `fk_ProjectID` (`ProjectID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `project_graph_table`
--
ALTER TABLE `project_graph_table`
  MODIFY `GraphID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `project_graph_table`
--
ALTER TABLE `project_graph_table`
  ADD CONSTRAINT `fk_ProjectID` FOREIGN KEY (`ProjectID`) REFERENCES `projects` (`ProjectID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
