-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 08, 2024 at 01:32 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

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
-- Table structure for table `chat-chat recipients table`
--

CREATE TABLE `chat-chat recipients table` (
  `Chat-Chat Recipients ID` bigint(20) NOT NULL,
  `Chat ID` mediumint(9) NOT NULL,
  `Chat Recipients ID` int(11) NOT NULL,
  `Chat Muted` tinyint(1) NOT NULL,
  `Chat Favourited` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat-chat recipients table`
--

INSERT INTO `chat-chat recipients table` (`Chat-Chat Recipients ID`, `Chat ID`, `Chat Recipients ID`, `Chat Muted`, `Chat Favourited`) VALUES
(1, 0, 1, 0, 0),
(2, 0, 2, 0, 0),
(3, 1, 1, 0, 0),
(4, 1, 3, 0, 0),
(5, 2, 1, 0, 0),
(6, 2, 2, 0, 0),
(7, 2, 3, 0, 0),
(8, 3, 4, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `chat-date/time table`
--

CREATE TABLE `chat-date/time table` (
  `Chat ID` mediumint(9) NOT NULL,
  `Date Created` date NOT NULL,
  `Time Created` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat-message table`
--

CREATE TABLE `chat-message table` (
  `Chat ID` mediumint(9) NOT NULL,
  `Message ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat-message table`
--

INSERT INTO `chat-message table` (`Chat ID`, `Message ID`) VALUES
(0, 0),
(0, 1),
(1, 2),
(2, 3),
(2, 5),
(2, 39),
(2, 40),
(2, 41);

-- --------------------------------------------------------

--
-- Table structure for table `chat table`
--

CREATE TABLE `chat table` (
  `Chat ID` mediumint(9) NOT NULL,
  `Chat Name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat table`
--

INSERT INTO `chat table` (`Chat ID`, `Chat Name`) VALUES
(0, 'Glyn & Catrin'),
(1, 'Glyn & Haamid'),
(2, 'Text-Chat Subsystem'),
(3, 'Data-Analytics Subsystem');

-- --------------------------------------------------------

--
-- Table structure for table `employee-block table`
--

CREATE TABLE `employee-block table` (
  `Blocking Employee ID` int(11) NOT NULL,
  `Blocked Employee ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee-block table`
--

INSERT INTO `employee-block table` (`Blocking Employee ID`, `Blocked Employee ID`) VALUES
(1, 6);

-- --------------------------------------------------------

--
-- Table structure for table `employee table`
--

CREATE TABLE `employee table` (
  `Employee ID` int(11) NOT NULL,
  `Employee First Name` tinytext NOT NULL,
  `Employee Surname` tinytext NOT NULL,
  `Employee Email` tinytext NOT NULL,
  `Employee Password` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee table`
--

INSERT INTO `employee table` (`Employee ID`, `Employee First Name`, `Employee Surname`, `Employee Email`, `Employee Password`) VALUES
(1, 'Glyn', 'Coffin', 'glyn.c@makeitall.com', '12345678'),
(2, 'Catrin', 'Llywelyn', 'catrin.l@makeitall.com', '87654321'),
(3, 'Haamid', 'Jillani', 'haamid.j@makeitall.com', '24681357'),
(4, 'Ryan', 'Lightowler', 'ryan.l@makeitall.com', '13572468'),
(5, 'Jonathan', 'Thanni', 'jonathan.t@makeitall.com', '86427531'),
(6, 'Alex', 'Lester', 'alex.l@makeitall.com', '75318642');

-- --------------------------------------------------------

--
-- Table structure for table `group-admin table`
--

CREATE TABLE `group-admin table` (
  `Group ID` smallint(6) NOT NULL,
  `Admin ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group-admin table`
--

INSERT INTO `group-admin table` (`Group ID`, `Admin ID`) VALUES
(1, 1),
(1, 2),
(2, 4);

-- --------------------------------------------------------

--
-- Table structure for table `group-chat table`
--

CREATE TABLE `group-chat table` (
  `Group ID` smallint(6) NOT NULL,
  `Chat ID` mediumint(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group-chat table`
--

INSERT INTO `group-chat table` (`Group ID`, `Chat ID`) VALUES
(1, 2),
(2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `group table`
--

CREATE TABLE `group table` (
  `Group ID` smallint(6) NOT NULL,
  `Group Name` text NOT NULL,
  `Group Description` mediumtext NOT NULL,
  `Group Created Date` date NOT NULL,
  `Group Created Time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group table`
--

INSERT INTO `group table` (`Group ID`, `Group Name`, `Group Description`, `Group Created Date`, `Group Created Time`) VALUES
(1, 'Text-Chat Subsystem', 'This group chat is used for members of the text-chat subsystem to communicate', '2024-04-23', '11:39:34'),
(2, 'Data-Analytics Subsystem', 'This group chat is used for members of the data-analytics subsystem to communicate', '2024-04-23', '11:42:34');

-- --------------------------------------------------------

--
-- Table structure for table `invitation-admin table`
--

CREATE TABLE `invitation-admin table` (
  `Invitation ID` int(11) NOT NULL,
  `Admin ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invitation-admin table`
--

INSERT INTO `invitation-admin table` (`Invitation ID`, `Admin ID`) VALUES
(1, 1),
(2, 4);

-- --------------------------------------------------------

--
-- Table structure for table `invitation-chat table`
--

CREATE TABLE `invitation-chat table` (
  `Invitation ID` int(11) NOT NULL,
  `Chat ID` mediumint(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invitation-chat table`
--

INSERT INTO `invitation-chat table` (`Invitation ID`, `Chat ID`) VALUES
(1, 2),
(2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `invitation-recipients table`
--

CREATE TABLE `invitation-recipients table` (
  `Invitation ID` int(11) NOT NULL,
  `Recipient ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invitation-recipients table`
--

INSERT INTO `invitation-recipients table` (`Invitation ID`, `Recipient ID`) VALUES
(1, 2),
(1, 3),
(2, 5),
(2, 6);

-- --------------------------------------------------------

--
-- Table structure for table `invitation table`
--

CREATE TABLE `invitation table` (
  `Invitation ID` int(11) NOT NULL,
  `Invitation Text` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invitation table`
--

INSERT INTO `invitation table` (`Invitation ID`, `Invitation Text`) VALUES
(1, 'Glyn has invited you to the Text-Chat Subsystem Groupchat'),
(2, 'Ryan has invited you to the Data-Analytics Subsystem Groupchat');

-- --------------------------------------------------------

--
-- Table structure for table `message-receiver table`
--

CREATE TABLE `message-receiver table` (
  `Message ID` bigint(20) NOT NULL,
  `Receiver ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message-receiver table`
--

INSERT INTO `message-receiver table` (`Message ID`, `Receiver ID`) VALUES
(0, 2),
(1, 1),
(2, 1),
(3, 2),
(3, 3),
(5, 1),
(5, 3),
(39, 1),
(39, 3),
(40, 1),
(40, 3),
(41, 1),
(41, 3);

-- --------------------------------------------------------

--
-- Table structure for table `message-sender table`
--

CREATE TABLE `message-sender table` (
  `Message ID` bigint(20) NOT NULL,
  `Sender ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message-sender table`
--

INSERT INTO `message-sender table` (`Message ID`, `Sender ID`) VALUES
(0, 1),
(1, 2),
(2, 3),
(3, 1),
(5, 2),
(39, 2),
(40, 2),
(41, 2);

-- --------------------------------------------------------

--
-- Table structure for table `message table`
--

CREATE TABLE `message table` (
  `Message ID` bigint(20) NOT NULL,
  `Message Text` longtext NOT NULL,
  `Message Media` text NOT NULL,
  `Message Date` date NOT NULL,
  `Message Time` time NOT NULL,
  `Message Drafted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message table`
--

INSERT INTO `message table` (`Message ID`, `Message Text`, `Message Media`, `Message Date`, `Message Time`, `Message Drafted`) VALUES
(0, 'Hi Catrin, how are you?', '', '2024-04-23', '11:45:37', 0),
(1, 'I\'m good, how are you?', '', '2024-04-23', '11:48:32', 0),
(2, 'Bad Message', '', '2024-04-23', '13:01:59', 0),
(3, 'Hello everyone, welcome to Text-Chat Subsystem Groupchat', '', '2024-04-23', '13:07:11', 0),
(5, 'hey guys', '', '2024-04-26', '15:10:00', 0),
(39, 'welcome', '', '2024-04-29', '21:51:33', 0),
(40, 'hello', '', '2024-04-29', '22:41:44', 0),
(41, 'this is a test', '', '2024-04-29', '23:21:36', 0);

-- --------------------------------------------------------

--
-- Table structure for table `notification-chat table`
--

CREATE TABLE `notification-chat table` (
  `Notification ID` bigint(20) NOT NULL,
  `Chat ID` mediumint(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification-chat table`
--

INSERT INTO `notification-chat table` (`Notification ID`, `Chat ID`) VALUES
(1, 0),
(2, 0),
(3, 2),
(4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `notification table`
--

CREATE TABLE `notification table` (
  `Notification ID` bigint(20) NOT NULL,
  `Notification Text` text NOT NULL,
  `Notification Read` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification table`
--

INSERT INTO `notification table` (`Notification ID`, `Notification Text`, `Notification Read`) VALUES
(1, 'Glyn sent: Hi Catrin, how are you?', 0),
(2, 'Catrin sent: I\'m good, how are you?', 0),
(3, 'Glyn sent: Hello everyone, welcome to Text-Chat Subsystem Groupchat', 0),
(4, 'Haamid sent: Bad Message', 0);

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
-- Table structure for table `reported-employee table`
--

CREATE TABLE `reported-employee table` (
  `Report ID` bigint(20) NOT NULL,
  `Reported Employee ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reported-employee table`
--

INSERT INTO `reported-employee table` (`Report ID`, `Reported Employee ID`) VALUES
(0, 3),
(14, 3);

-- --------------------------------------------------------

--
-- Table structure for table `reporting-employee table`
--

CREATE TABLE `reporting-employee table` (
  `Report ID` bigint(20) NOT NULL,
  `Reporting Employee ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reporting-employee table`
--

INSERT INTO `reporting-employee table` (`Report ID`, `Reporting Employee ID`) VALUES
(0, 1),
(14, 2);

-- --------------------------------------------------------

--
-- Table structure for table `report table`
--

CREATE TABLE `report table` (
  `Report ID` bigint(20) NOT NULL,
  `Report Reason` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `report table`
--

INSERT INTO `report table` (`Report ID`, `Report Reason`) VALUES
(0, 'Haamid sent: Bad Message'),
(14, 'rude message');

--
-- Indexes for dumped tables
--

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
-- Indexes for table `chat-chat recipients table`
--
ALTER TABLE `chat-chat recipients table`
  ADD PRIMARY KEY (`Chat-Chat Recipients ID`),
  ADD KEY `Chat Recipients ID (Chat-Chat Recipients -> Employee)` (`Chat Recipients ID`);

--
-- Indexes for table `chat-date/time table`
--
ALTER TABLE `chat-date/time table`
  ADD PRIMARY KEY (`Chat ID`);

--
-- Indexes for table `chat-message table`
--
ALTER TABLE `chat-message table`
  ADD PRIMARY KEY (`Chat ID`,`Message ID`),
  ADD KEY `Message ID (Chat-Message -> Message)` (`Message ID`);

--
-- Indexes for table `chat table`
--
ALTER TABLE `chat table`
  ADD PRIMARY KEY (`Chat ID`);

--
-- Indexes for table `employee-block table`
--
ALTER TABLE `employee-block table`
  ADD PRIMARY KEY (`Blocking Employee ID`,`Blocked Employee ID`),
  ADD KEY `Blocked Employee ID (Employee-Block -> Employee)` (`Blocked Employee ID`);

--
-- Indexes for table `employee table`
--
ALTER TABLE `employee table`
  ADD PRIMARY KEY (`Employee ID`);

--
-- Indexes for table `group-admin table`
--
ALTER TABLE `group-admin table`
  ADD PRIMARY KEY (`Group ID`,`Admin ID`),
  ADD KEY `Admin ID (Group-Admin -> Employee)` (`Admin ID`);

--
-- Indexes for table `group-chat table`
--
ALTER TABLE `group-chat table`
  ADD PRIMARY KEY (`Group ID`,`Chat ID`),
  ADD KEY `Chat ID (Group-Chat -> Chat)` (`Chat ID`);

--
-- Indexes for table `group table`
--
ALTER TABLE `group table`
  ADD PRIMARY KEY (`Group ID`);

--
-- Indexes for table `invitation-admin table`
--
ALTER TABLE `invitation-admin table`
  ADD PRIMARY KEY (`Invitation ID`,`Admin ID`),
  ADD KEY `Admin ID (Invitation-Admin -> Group-Admin)` (`Admin ID`);

--
-- Indexes for table `invitation-chat table`
--
ALTER TABLE `invitation-chat table`
  ADD PRIMARY KEY (`Invitation ID`,`Chat ID`),
  ADD KEY `Chat ID (Invitation-Chat -> Group-Chat)` (`Chat ID`);

--
-- Indexes for table `invitation-recipients table`
--
ALTER TABLE `invitation-recipients table`
  ADD PRIMARY KEY (`Invitation ID`,`Recipient ID`),
  ADD KEY `Recipient ID (Invitation-Recipients -> Employee)` (`Recipient ID`);

--
-- Indexes for table `invitation table`
--
ALTER TABLE `invitation table`
  ADD PRIMARY KEY (`Invitation ID`);

--
-- Indexes for table `message-receiver table`
--
ALTER TABLE `message-receiver table`
  ADD PRIMARY KEY (`Message ID`,`Receiver ID`);

--
-- Indexes for table `message-sender table`
--
ALTER TABLE `message-sender table`
  ADD PRIMARY KEY (`Message ID`,`Sender ID`);

--
-- Indexes for table `message table`
--
ALTER TABLE `message table`
  ADD PRIMARY KEY (`Message ID`);

--
-- Indexes for table `notification-chat table`
--
ALTER TABLE `notification-chat table`
  ADD PRIMARY KEY (`Notification ID`,`Chat ID`),
  ADD KEY `Chat ID (Notification-Chat -> Chat-Chat Recipients)` (`Chat ID`);

--
-- Indexes for table `notification table`
--
ALTER TABLE `notification table`
  ADD PRIMARY KEY (`Notification ID`);

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
-- Indexes for table `reported-employee table`
--
ALTER TABLE `reported-employee table`
  ADD PRIMARY KEY (`Report ID`,`Reported Employee ID`),
  ADD KEY `Reported Employee ID (Reported-Employee -> Employee)` (`Reported Employee ID`);

--
-- Indexes for table `reporting-employee table`
--
ALTER TABLE `reporting-employee table`
  ADD PRIMARY KEY (`Report ID`,`Reporting Employee ID`),
  ADD KEY `Reporting Employee ID (Reporting-Employee Employee)` (`Reporting Employee ID`);

--
-- Indexes for table `report table`
--
ALTER TABLE `report table`
  ADD PRIMARY KEY (`Report ID`);

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
-- AUTO_INCREMENT for table `chat-chat recipients table`
--
ALTER TABLE `chat-chat recipients table`
  MODIFY `Chat-Chat Recipients ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `chat table`
--
ALTER TABLE `chat table`
  MODIFY `Chat ID` mediumint(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `employee table`
--
ALTER TABLE `employee table`
  MODIFY `Employee ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `group table`
--
ALTER TABLE `group table`
  MODIFY `Group ID` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `invitation table`
--
ALTER TABLE `invitation table`
  MODIFY `Invitation ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `message table`
--
ALTER TABLE `message table`
  MODIFY `Message ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `notification table`
--
ALTER TABLE `notification table`
  MODIFY `Notification ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- AUTO_INCREMENT for table `report table`
--
ALTER TABLE `report table`
  MODIFY `Report ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
-- Constraints for table `chat-date/time table`
--
ALTER TABLE `chat-date/time table`
  ADD CONSTRAINT `Chat ID (Chat-Date/Time -> Chat)` FOREIGN KEY (`Chat ID`) REFERENCES `chat table` (`Chat ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `chat-message table`
--
ALTER TABLE `chat-message table`
  ADD CONSTRAINT `Chat ID (Chat-Message -> Chat)` FOREIGN KEY (`Chat ID`) REFERENCES `chat table` (`Chat ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Message ID (Chat-Message -> Message)` FOREIGN KEY (`Message ID`) REFERENCES `message table` (`Message ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `employee-block table`
--
ALTER TABLE `employee-block table`
  ADD CONSTRAINT `Blocked Employee ID (Employee-Block -> Employee)` FOREIGN KEY (`Blocked Employee ID`) REFERENCES `employee table` (`Employee ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Blocking Employee ID (Employee-Block -> Employee)` FOREIGN KEY (`Blocking Employee ID`) REFERENCES `employee table` (`Employee ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `group-admin table`
--
ALTER TABLE `group-admin table`
  ADD CONSTRAINT `Admin ID (Group-Admin -> Employee)` FOREIGN KEY (`Admin ID`) REFERENCES `employee table` (`Employee ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Group ID (Group-Admin -> Group)` FOREIGN KEY (`Group ID`) REFERENCES `group table` (`Group ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `group-chat table`
--
ALTER TABLE `group-chat table`
  ADD CONSTRAINT `Chat ID (Group-Chat -> Chat)` FOREIGN KEY (`Chat ID`) REFERENCES `chat table` (`Chat ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Group ID (Group-Chat -> Group)` FOREIGN KEY (`Group ID`) REFERENCES `group table` (`Group ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invitation-admin table`
--
ALTER TABLE `invitation-admin table`
  ADD CONSTRAINT `Admin ID (Invitation-Admin -> Group-Admin)` FOREIGN KEY (`Admin ID`) REFERENCES `group-admin table` (`Admin ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Invitation ID (Invitation-Admin -> Invitation))` FOREIGN KEY (`Invitation ID`) REFERENCES `invitation table` (`Invitation ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invitation-chat table`
--
ALTER TABLE `invitation-chat table`
  ADD CONSTRAINT `Chat ID (Invitation-Chat -> Group-Chat)` FOREIGN KEY (`Chat ID`) REFERENCES `group-chat table` (`Chat ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invitation-recipients table`
--
ALTER TABLE `invitation-recipients table`
  ADD CONSTRAINT `Invitation ID (Invitation-Recipients -> Invitation))` FOREIGN KEY (`Invitation ID`) REFERENCES `invitation table` (`Invitation ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Recipient ID (Invitation-Recipients -> Employee)` FOREIGN KEY (`Recipient ID`) REFERENCES `employee table` (`Employee ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `message-receiver table`
--
ALTER TABLE `message-receiver table`
  ADD CONSTRAINT `Message ID (Message-Receiver -> Message)` FOREIGN KEY (`Message ID`) REFERENCES `message table` (`Message ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `message-sender table`
--
ALTER TABLE `message-sender table`
  ADD CONSTRAINT `Message ID (Message-Sender -> Message)` FOREIGN KEY (`Message ID`) REFERENCES `message table` (`Message ID`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Constraints for table `reported-employee table`
--
ALTER TABLE `reported-employee table`
  ADD CONSTRAINT `Report ID (Reported-Employee -> Report))` FOREIGN KEY (`Report ID`) REFERENCES `report table` (`Report ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Reported Employee ID (Reported-Employee -> Employee)` FOREIGN KEY (`Reported Employee ID`) REFERENCES `employee table` (`Employee ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

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
