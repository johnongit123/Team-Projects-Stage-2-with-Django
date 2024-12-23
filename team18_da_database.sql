-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2024 at 03:02 PM
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
-- Database: `team18_da_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `project-employee table`
--

CREATE TABLE `project-employee table` (
  `ProjectID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project-employee table`
--

INSERT INTO `project-employee table` (`ProjectID`, `UserID`) VALUES
(1, 1),
(1, 2),
(1, 2),
(3, 3),
(18, 4);

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `ProjectID` int(11) NOT NULL,
  `ProjectName` varchar(255) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `DueDate` date DEFAULT NULL,
  `Status` varchar(50) DEFAULT NULL,
  `ManagerID` int(11) DEFAULT NULL,
  `Colour` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`ProjectID`, `ProjectName`, `Description`, `DueDate`, `Status`, `ManagerID`, `Colour`) VALUES
(1, 'ProjectA', 'Description for ProjectA', '2024-03-16', 'Complete', 1, '#9963d9'),
(2, 'ProjectB', 'Description for ProjectB', '2024-04-15', 'Not Started', 1, '#9963d9'),
(3, 'ProjectC', 'Description for ProjectC', '2024-05-20', 'Complete', 2, 'purple'),
(4, 'ProjectTest', 'DECSKDFDS ', '2024-02-28', 'Not Started', 1, 'royalblue'),
(5, 'Project 1 - a', 'fdjsdfhjbdf', '2024-02-23', 'Not Started', 1, '#2fa90e'),
(18, 'ProjectA', 'xcvxcv', '2024-02-21', 'Not Started', NULL, 'royalblue');

-- --------------------------------------------------------

--
-- Table structure for table `project_graph_table`
--

CREATE TABLE `project_graph_table` (
  `ProjectID` int(11) NOT NULL,
  `GraphID` int(11) NOT NULL,
  `graph_title` varchar(255) NOT NULL,
  `graph_content` text NOT NULL,
  `graph_type` enum('bar_chart','pie_chart','column_chart') NOT NULL,
  `graph_filter` enum('completed','not started','ongoing','overdue','paused') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `taskprojects`
--

CREATE TABLE `taskprojects` (
  `TaskID` int(11) NOT NULL,
  `ProjectID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `taskprojects`
--

INSERT INTO `taskprojects` (`TaskID`, `ProjectID`) VALUES
(1, 1),
(2, 2),
(3, 3),
(26, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 18),
(34, 1),
(35, 1),
(36, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `TaskID` int(11) NOT NULL,
  `TaskName` varchar(255) NOT NULL,
  `TaskDescription` text DEFAULT NULL,
  `TaskStatus` varchar(50) DEFAULT NULL,
  `DueDate` date DEFAULT NULL,
  `IsPrivate` tinyint(1) NOT NULL,
  `TaskDuration` int(10) NOT NULL,
  `Colour` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`TaskID`, `TaskName`, `TaskDescription`, `TaskStatus`, `DueDate`, `IsPrivate`, `TaskDuration`, `Colour`) VALUES
(1, 'Task1', 'Description for Task1', 'Ongoing', '2024-03-05', 0, 5, 'royalblue'),
(2, 'Task2', 'Description for Task2', 'Not Started', '2024-04-18', 0, 4, 'purple'),
(3, 'Task3', 'Description for Task3', 'Completed', '2024-05-22', 1, 6, 'royalblue'),
(26, 'HEY', 'akj jkds dsdjsdfjkdsf sd fksjkd fskjd fksd jfskd fjskd jfkjsd f sjkdfd jkfjk sdlfj ksdfjk lsdkjl fsjk fsjd f djskfdsjf', 'Complete', '2024-03-14', 0, 4, 'royalblue'),
(30, 'TEST - no way', 'hey', 'Complete', '2024-03-01', 0, 2, '#2fa90e'),
(31, 'TEST 2', 'heys fkjsdfsd kfsdf sdf ', 'Complete', '2024-02-18', 0, 4, '#9963d9'),
(32, 'WOO', 'wndsfjksdfj k;sdf k;sdf sdfkj; sfd ', 'Complete', '2024-03-08', 0, 4, '#2fa90e'),
(33, 'CREAY', 'WHY NOT', 'Paused', '2024-02-20', 1, 4, '#2fa90e'),
(34, 'TEST 3', 'hh', 'Not Started', '2024-03-02', 0, 78, '#c03a00'),
(35, 'HEY', 'ssfgdsdfg', 'Not Started', '2024-02-24', 0, 2, '#2fa90e'),
(36, 'TEST', 'dfhgdfhgdfhg', 'Not Started', '2024-02-10', 0, 3, '#2fa90e');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `UserType` varchar(50) NOT NULL,
  `isMod` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `Username`, `Email`, `Password`, `UserType`, `isMod`) VALUES
(1, 'Manager1', 'manager1@example.com', 'password123', 'Manager', 0),
(2, 'User1', 'user1@example.com', 'password456', 'User', 0),
(3, 'User2', 'user2@example.com', 'password789', 'User', 1),
(4, 'user', 'user@example.com', '07c8af9c49f018f6d6e1f1f67418c2a86c0e8dbe0e67b7a02a3dcb45d0637437', 'User', 0);

-- --------------------------------------------------------

--
-- Table structure for table `usertasks`
--

CREATE TABLE `usertasks` (
  `UserID` int(11) NOT NULL,
  `TaskID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usertasks`
--

INSERT INTO `usertasks` (`UserID`, `TaskID`) VALUES
(1, 26),
(1, 31),
(1, 35),
(1, 36),
(2, 2),
(2, 30),
(2, 32),
(3, 3),
(4, 33);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `project-employee table`
--
ALTER TABLE `project-employee table`
  ADD KEY `UserID` (`UserID`),
  ADD KEY `ProjectID` (`ProjectID`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`ProjectID`),
  ADD KEY `ManagerID` (`ManagerID`);

--
-- Indexes for table `project_graph_table`
--
ALTER TABLE `project_graph_table`
  ADD PRIMARY KEY (`GraphID`),
  ADD KEY `fk_ProjectID` (`ProjectID`);

--
-- Indexes for table `taskprojects`
--
ALTER TABLE `taskprojects`
  ADD PRIMARY KEY (`TaskID`,`ProjectID`),
  ADD KEY `projectid_from_projects_to_taskproject` (`ProjectID`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`TaskID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`);

--
-- Indexes for table `usertasks`
--
ALTER TABLE `usertasks`
  ADD PRIMARY KEY (`UserID`,`TaskID`),
  ADD KEY `TaskID` (`TaskID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `ProjectID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `project_graph_table`
--
ALTER TABLE `project_graph_table`
  MODIFY `GraphID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `TaskID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `project-employee table`
--
ALTER TABLE `project-employee table`
  ADD CONSTRAINT `projectID_from_projects_to_project-emp` FOREIGN KEY (`ProjectID`) REFERENCES `projects` (`ProjectID`),
  ADD CONSTRAINT `userID_from_users_to_project-emp` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `project_graph_table`
--
ALTER TABLE `project_graph_table`
  ADD CONSTRAINT `fk_ProjectID` FOREIGN KEY (`ProjectID`) REFERENCES `projects` (`ProjectID`);

--
-- Constraints for table `taskprojects`
--
ALTER TABLE `taskprojects`
  ADD CONSTRAINT `projectid_from_projects_to_taskproject` FOREIGN KEY (`ProjectID`) REFERENCES `projects` (`ProjectID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `taskid_from_tasks_to_taskproject` FOREIGN KEY (`TaskID`) REFERENCES `tasks` (`TaskID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `usertasks`
--
ALTER TABLE `usertasks`
  ADD CONSTRAINT `fk_TaskID` FOREIGN KEY (`TaskID`) REFERENCES `tasks` (`TaskID`),
  ADD CONSTRAINT `fk_UserID` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
