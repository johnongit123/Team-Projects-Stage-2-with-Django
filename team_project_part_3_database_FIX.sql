-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 12, 2024 at 03:38 PM
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
-- Database: `team18_final`
--

-- --------------------------------------------------------

--
-- Table structure for table `auth_group`
--

CREATE TABLE `auth_group` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_group_permissions`
--

CREATE TABLE `auth_group_permissions` (
  `id` bigint(20) NOT NULL,
  `group_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_permission`
--

CREATE TABLE `auth_permission` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `content_type_id` int(11) NOT NULL,
  `codename` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auth_permission`
--

INSERT INTO `auth_permission` (`id`, `name`, `content_type_id`, `codename`) VALUES
(1, 'Can add log entry', 1, 'add_logentry'),
(2, 'Can change log entry', 1, 'change_logentry'),
(3, 'Can delete log entry', 1, 'delete_logentry'),
(4, 'Can view log entry', 1, 'view_logentry'),
(5, 'Can add permission', 2, 'add_permission'),
(6, 'Can change permission', 2, 'change_permission'),
(7, 'Can delete permission', 2, 'delete_permission'),
(8, 'Can view permission', 2, 'view_permission'),
(9, 'Can add group', 3, 'add_group'),
(10, 'Can change group', 3, 'change_group'),
(11, 'Can delete group', 3, 'delete_group'),
(12, 'Can view group', 3, 'view_group'),
(13, 'Can add user', 4, 'add_user'),
(14, 'Can change user', 4, 'change_user'),
(15, 'Can delete user', 4, 'delete_user'),
(16, 'Can view user', 4, 'view_user'),
(17, 'Can add content type', 5, 'add_contenttype'),
(18, 'Can change content type', 5, 'change_contenttype'),
(19, 'Can delete content type', 5, 'delete_contenttype'),
(20, 'Can view content type', 5, 'view_contenttype'),
(21, 'Can add session', 6, 'add_session'),
(22, 'Can change session', 6, 'change_session'),
(23, 'Can delete session', 6, 'delete_session'),
(24, 'Can view session', 6, 'view_session'),
(25, 'Can add chat', 7, 'add_chat'),
(26, 'Can change chat', 7, 'change_chat'),
(27, 'Can delete chat', 7, 'delete_chat'),
(28, 'Can view chat', 7, 'view_chat'),
(29, 'Can add employee', 8, 'add_employee'),
(30, 'Can change employee', 8, 'change_employee'),
(31, 'Can delete employee', 8, 'delete_employee'),
(32, 'Can view employee', 8, 'view_employee'),
(33, 'Can add chat chat recipients', 9, 'add_chatchatrecipients'),
(34, 'Can change chat chat recipients', 9, 'change_chatchatrecipients'),
(35, 'Can delete chat chat recipients', 9, 'delete_chatchatrecipients'),
(36, 'Can view chat chat recipients', 9, 'view_chatchatrecipients'),
(37, 'Can add message', 10, 'add_message'),
(38, 'Can change message', 10, 'change_message'),
(39, 'Can delete message', 10, 'delete_message'),
(40, 'Can view message', 10, 'view_message'),
(41, 'Can add chat message', 11, 'add_chatmessage'),
(42, 'Can change chat message', 11, 'change_chatmessage'),
(43, 'Can delete chat message', 11, 'delete_chatmessage'),
(44, 'Can view chat message', 11, 'view_chatmessage'),
(45, 'Can add chat date time', 12, 'add_chatdatetime'),
(46, 'Can change chat date time', 12, 'change_chatdatetime'),
(47, 'Can delete chat date time', 12, 'delete_chatdatetime'),
(48, 'Can view chat date time', 12, 'view_chatdatetime'),
(49, 'Can add employee block', 13, 'add_employeeblock'),
(50, 'Can change employee block', 13, 'change_employeeblock'),
(51, 'Can delete employee block', 13, 'delete_employeeblock'),
(52, 'Can view employee block', 13, 'view_employeeblock'),
(53, 'Can add group', 14, 'add_group'),
(54, 'Can change group', 14, 'change_group'),
(55, 'Can delete group', 14, 'delete_group'),
(56, 'Can view group', 14, 'view_group'),
(57, 'Can add group admin', 15, 'add_groupadmin'),
(58, 'Can change group admin', 15, 'change_groupadmin'),
(59, 'Can delete group admin', 15, 'delete_groupadmin'),
(60, 'Can view group admin', 15, 'view_groupadmin'),
(61, 'Can add group chat', 16, 'add_groupchat'),
(62, 'Can change group chat', 16, 'change_groupchat'),
(63, 'Can delete group chat', 16, 'delete_groupchat'),
(64, 'Can view group chat', 16, 'view_groupchat'),
(65, 'Can add invitation', 17, 'add_invitation'),
(66, 'Can change invitation', 17, 'change_invitation'),
(67, 'Can delete invitation', 17, 'delete_invitation'),
(68, 'Can view invitation', 17, 'view_invitation'),
(69, 'Can add invitation admin', 18, 'add_invitationadmin'),
(70, 'Can change invitation admin', 18, 'change_invitationadmin'),
(71, 'Can delete invitation admin', 18, 'delete_invitationadmin'),
(72, 'Can view invitation admin', 18, 'view_invitationadmin'),
(73, 'Can add invitation recipients', 19, 'add_invitationrecipients'),
(74, 'Can change invitation recipients', 19, 'change_invitationrecipients'),
(75, 'Can delete invitation recipients', 19, 'delete_invitationrecipients'),
(76, 'Can view invitation recipients', 19, 'view_invitationrecipients'),
(77, 'Can add invitation chat', 20, 'add_invitationchat'),
(78, 'Can change invitation chat', 20, 'change_invitationchat'),
(79, 'Can delete invitation chat', 20, 'delete_invitationchat'),
(80, 'Can view invitation chat', 20, 'view_invitationchat'),
(81, 'Can add message receiver', 21, 'add_messagereceiver'),
(82, 'Can change message receiver', 21, 'change_messagereceiver'),
(83, 'Can delete message receiver', 21, 'delete_messagereceiver'),
(84, 'Can view message receiver', 21, 'view_messagereceiver'),
(85, 'Can add message sender', 22, 'add_messagesender'),
(86, 'Can change message sender', 22, 'change_messagesender'),
(87, 'Can delete message sender', 22, 'delete_messagesender'),
(88, 'Can view message sender', 22, 'view_messagesender'),
(89, 'Can add notification', 23, 'add_notification'),
(90, 'Can change notification', 23, 'change_notification'),
(91, 'Can delete notification', 23, 'delete_notification'),
(92, 'Can view notification', 23, 'view_notification'),
(93, 'Can add notification chat', 24, 'add_notificationchat'),
(94, 'Can change notification chat', 24, 'change_notificationchat'),
(95, 'Can delete notification chat', 24, 'delete_notificationchat'),
(96, 'Can view notification chat', 24, 'view_notificationchat'),
(97, 'Can add report', 25, 'add_report'),
(98, 'Can change report', 25, 'change_report'),
(99, 'Can delete report', 25, 'delete_report'),
(100, 'Can view report', 25, 'view_report'),
(101, 'Can add reported employee', 26, 'add_reportedemployee'),
(102, 'Can change reported employee', 26, 'change_reportedemployee'),
(103, 'Can delete reported employee', 26, 'delete_reportedemployee'),
(104, 'Can view reported employee', 26, 'view_reportedemployee'),
(105, 'Can add reporting employee', 27, 'add_reportingemployee'),
(106, 'Can change reporting employee', 27, 'change_reportingemployee'),
(107, 'Can delete reporting employee', 27, 'delete_reportingemployee'),
(108, 'Can view reporting employee', 27, 'view_reportingemployee'),
(109, 'Can add users', 28, 'add_users'),
(110, 'Can change users', 28, 'change_users'),
(111, 'Can delete users', 28, 'delete_users'),
(112, 'Can view users', 28, 'view_users'),
(113, 'Can add projects', 29, 'add_projects'),
(114, 'Can change projects', 29, 'change_projects'),
(115, 'Can delete projects', 29, 'delete_projects'),
(116, 'Can view projects', 29, 'view_projects'),
(117, 'Can add tasks', 30, 'add_tasks'),
(118, 'Can change tasks', 30, 'change_tasks'),
(119, 'Can delete tasks', 30, 'delete_tasks'),
(120, 'Can view tasks', 30, 'view_tasks'),
(121, 'Can add project_ task', 31, 'add_project_task'),
(122, 'Can change project_ task', 31, 'change_project_task'),
(123, 'Can delete project_ task', 31, 'delete_project_task'),
(124, 'Can view project_ task', 31, 'view_project_task'),
(125, 'Can add project_ employee', 32, 'add_project_employee'),
(126, 'Can change project_ employee', 32, 'change_project_employee'),
(127, 'Can delete project_ employee', 32, 'delete_project_employee'),
(128, 'Can view project_ employee', 32, 'view_project_employee'),
(129, 'Can add user_ tasks', 33, 'add_user_tasks'),
(130, 'Can change user_ tasks', 33, 'change_user_tasks'),
(131, 'Can delete user_ tasks', 33, 'delete_user_tasks'),
(132, 'Can view user_ tasks', 33, 'view_user_tasks'),
(133, 'Can add project_ graph', 34, 'add_project_graph'),
(134, 'Can change project_ graph', 34, 'change_project_graph'),
(135, 'Can delete project_ graph', 34, 'delete_project_graph'),
(136, 'Can view project_ graph', 34, 'view_project_graph');

-- --------------------------------------------------------

--
-- Table structure for table `auth_user`
--

CREATE TABLE `auth_user` (
  `id` int(11) NOT NULL,
  `password` varchar(128) NOT NULL,
  `last_login` datetime(6) DEFAULT NULL,
  `is_superuser` tinyint(1) NOT NULL,
  `username` varchar(150) NOT NULL,
  `first_name` varchar(150) NOT NULL,
  `last_name` varchar(150) NOT NULL,
  `email` varchar(254) NOT NULL,
  `is_staff` tinyint(1) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `date_joined` datetime(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_user_groups`
--

CREATE TABLE `auth_user_groups` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_user_user_permissions`
--

CREATE TABLE `auth_user_user_permissions` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(3, 1, 1, 0, 0),
(4, 1, 3, 0, 0),
(5, 2, 1, 0, 0),
(6, 2, 2, 0, 0),
(7, 2, 3, 0, 0),
(8, 3, 4, 0, 0),
(10, 4, 2, 0, 0),
(11, 4, 1, 0, 0),
(12, 5, 2, 0, 1);

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
(2, 41),
(2, 43),
(2, 44),
(4, 45),
(5, 46);

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
(3, 'Data-Analytics Subsystem'),
(4, 'Catrin & Glyn'),
(5, 'Best Group');

-- --------------------------------------------------------

--
-- Table structure for table `django_admin_log`
--

CREATE TABLE `django_admin_log` (
  `id` int(11) NOT NULL,
  `action_time` datetime(6) NOT NULL,
  `object_id` longtext DEFAULT NULL,
  `object_repr` varchar(200) NOT NULL,
  `action_flag` smallint(5) UNSIGNED NOT NULL CHECK (`action_flag` >= 0),
  `change_message` longtext NOT NULL,
  `content_type_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `django_content_type`
--

CREATE TABLE `django_content_type` (
  `id` int(11) NOT NULL,
  `app_label` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `django_content_type`
--

INSERT INTO `django_content_type` (`id`, `app_label`, `model`) VALUES
(1, 'admin', 'logentry'),
(29, 'analytics', 'projects'),
(32, 'analytics', 'project_employee'),
(34, 'analytics', 'project_graph'),
(31, 'analytics', 'project_task'),
(30, 'analytics', 'tasks'),
(28, 'analytics', 'users'),
(33, 'analytics', 'user_tasks'),
(3, 'auth', 'group'),
(2, 'auth', 'permission'),
(4, 'auth', 'user'),
(7, 'chat', 'chat'),
(9, 'chat', 'chatchatrecipients'),
(12, 'chat', 'chatdatetime'),
(11, 'chat', 'chatmessage'),
(8, 'chat', 'employee'),
(13, 'chat', 'employeeblock'),
(14, 'chat', 'group'),
(15, 'chat', 'groupadmin'),
(16, 'chat', 'groupchat'),
(17, 'chat', 'invitation'),
(18, 'chat', 'invitationadmin'),
(20, 'chat', 'invitationchat'),
(19, 'chat', 'invitationrecipients'),
(10, 'chat', 'message'),
(21, 'chat', 'messagereceiver'),
(22, 'chat', 'messagesender'),
(23, 'chat', 'notification'),
(24, 'chat', 'notificationchat'),
(25, 'chat', 'report'),
(26, 'chat', 'reportedemployee'),
(27, 'chat', 'reportingemployee'),
(5, 'contenttypes', 'contenttype'),
(6, 'sessions', 'session');

-- --------------------------------------------------------

--
-- Table structure for table `django_migrations`
--

CREATE TABLE `django_migrations` (
  `id` bigint(20) NOT NULL,
  `app` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `applied` datetime(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `django_migrations`
--

INSERT INTO `django_migrations` (`id`, `app`, `name`, `applied`) VALUES
(1, 'contenttypes', '0001_initial', '2024-05-09 13:24:51.492969'),
(2, 'auth', '0001_initial', '2024-05-09 13:24:51.690650'),
(3, 'admin', '0001_initial', '2024-05-09 13:24:51.731522'),
(4, 'admin', '0002_logentry_remove_auto_add', '2024-05-09 13:24:51.737967'),
(5, 'admin', '0003_logentry_add_action_flag_choices', '2024-05-09 13:24:51.747269'),
(6, 'contenttypes', '0002_remove_content_type_name', '2024-05-09 13:24:51.796878'),
(7, 'auth', '0002_alter_permission_name_max_length', '2024-05-09 13:24:51.818880'),
(8, 'auth', '0003_alter_user_email_max_length', '2024-05-09 13:24:51.831955'),
(9, 'auth', '0004_alter_user_username_opts', '2024-05-09 13:24:51.840006'),
(10, 'auth', '0005_alter_user_last_login_null', '2024-05-09 13:24:51.861494'),
(11, 'auth', '0006_require_contenttypes_0002', '2024-05-09 13:24:51.862602'),
(12, 'auth', '0007_alter_validators_add_error_messages', '2024-05-09 13:24:51.872190'),
(13, 'auth', '0008_alter_user_username_max_length', '2024-05-09 13:24:51.884659'),
(14, 'auth', '0009_alter_user_last_name_max_length', '2024-05-09 13:24:51.896219'),
(15, 'auth', '0010_alter_group_name_max_length', '2024-05-09 13:24:51.911005'),
(16, 'auth', '0011_update_proxy_permissions', '2024-05-09 13:24:51.932101'),
(17, 'auth', '0012_alter_user_first_name_max_length', '2024-05-09 13:24:51.941460'),
(18, 'sessions', '0001_initial', '2024-05-09 13:24:51.953140');

-- --------------------------------------------------------

--
-- Table structure for table `django_session`
--

CREATE TABLE `django_session` (
  `session_key` varchar(40) NOT NULL,
  `session_data` longtext NOT NULL,
  `expire_date` datetime(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `django_session`
--

INSERT INTO `django_session` (`session_key`, `session_data`, `expire_date`) VALUES
('63kmm26izhqxffc3mri7l6qpwzl78w23', 'eyJlbXBsb3llZV9pZCI6Mn0:1s55T9:f4HCbetvOeAUh3bcnLiqvRSYessbFgDVrMSDU3yitcA', '2024-05-23 15:14:03.116920');

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
(2, 4),
(3, 2);

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
(2, 3),
(3, 5);

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
(2, 'Data-Analytics Subsystem', 'This group chat is used for members of the data-analytics subsystem to communicate', '2024-04-23', '11:42:34'),
(3, 'Best Group', 'This group chat is used for members of the text-chat subsystem to communicate', '2024-05-09', '15:19:48');

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
(2, 4),
(3, 2);

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
(2, 3),
(3, 5);

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
(1, 3),
(2, 5),
(2, 6),
(3, 1),
(3, 3);

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
(2, 'Ryan has invited you to the Data-Analytics Subsystem Groupchat'),
(3, 'Catrin has invited you to the Best Group Groupchat');

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
(41, 3),
(43, 1),
(43, 3),
(44, 1),
(44, 3),
(45, 1);

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
(41, 2),
(43, 2),
(44, 2),
(45, 2),
(46, 2);

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
(41, 'this is a test', '', '2024-04-29', '23:21:36', 0),
(43, 'ejfnbns', '', '2024-05-09', '16:14:43', 0),
(44, 'sdjfbsn', '', '2024-05-09', '16:15:21', 0),
(45, 'hello glyn', '', '2024-05-09', '16:17:59', 0),
(46, 'hola best group, wagwan\r\n', '', '2024-05-09', '16:20:07', 0);

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
(4, 1),
(5, 2),
(6, 2),
(7, 2),
(8, 4),
(9, 5);

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
(4, 'Haamid sent: Bad Message', 0),
(5, 'Catrin sent: hello again', 0),
(6, 'Catrin sent: ejfnbns', 0),
(7, 'Catrin sent: sdjfbsn', 0),
(8, 'Catrin sent: hello glyn', 0),
(9, 'Catrin sent: hola best group, wagwan\r\n', 0);

-- --------------------------------------------------------

--
-- Table structure for table `project-employee table`
--

CREATE TABLE `project-employee table` (
  `ProjectID` int(11) NOT NULL,
  `Employee ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project-employee table`
--

INSERT INTO `project-employee table` (`ProjectID`, `Employee ID`) VALUES
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
(14, 3),
(15, 3);

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
(14, 2),
(15, 2);

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
(14, 'rude message'),
(15, 'Bad Message');

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
-- Table structure for table `usertasks`
--

CREATE TABLE `usertasks` (
  `Employee ID` int(11) NOT NULL,
  `TaskID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usertasks`
--

INSERT INTO `usertasks` (`Employee ID`, `TaskID`) VALUES
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
-- Indexes for table `auth_group`
--
ALTER TABLE `auth_group`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `auth_group_permissions`
--
ALTER TABLE `auth_group_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `auth_group_permissions_group_id_permission_id_0cd325b0_uniq` (`group_id`,`permission_id`),
  ADD KEY `auth_group_permissio_permission_id_84c5c92e_fk_auth_perm` (`permission_id`);

--
-- Indexes for table `auth_permission`
--
ALTER TABLE `auth_permission`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `auth_permission_content_type_id_codename_01ab375a_uniq` (`content_type_id`,`codename`);

--
-- Indexes for table `auth_user`
--
ALTER TABLE `auth_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `auth_user_groups`
--
ALTER TABLE `auth_user_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `auth_user_groups_user_id_group_id_94350c0c_uniq` (`user_id`,`group_id`),
  ADD KEY `auth_user_groups_group_id_97559544_fk_auth_group_id` (`group_id`);

--
-- Indexes for table `auth_user_user_permissions`
--
ALTER TABLE `auth_user_user_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `auth_user_user_permissions_user_id_permission_id_14a6b632_uniq` (`user_id`,`permission_id`),
  ADD KEY `auth_user_user_permi_permission_id_1fbb5f2c_fk_auth_perm` (`permission_id`);

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
-- Indexes for table `django_admin_log`
--
ALTER TABLE `django_admin_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `django_admin_log_content_type_id_c4bce8eb_fk_django_co` (`content_type_id`),
  ADD KEY `django_admin_log_user_id_c564eba6_fk_auth_user_id` (`user_id`);

--
-- Indexes for table `django_content_type`
--
ALTER TABLE `django_content_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `django_content_type_app_label_model_76bd3d3b_uniq` (`app_label`,`model`);

--
-- Indexes for table `django_migrations`
--
ALTER TABLE `django_migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `django_session`
--
ALTER TABLE `django_session`
  ADD PRIMARY KEY (`session_key`),
  ADD KEY `django_session_expire_date_a5c62663` (`expire_date`);

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
  ADD KEY `UserID` (`Employee ID`),
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
-- Indexes for table `usertasks`
--
ALTER TABLE `usertasks`
  ADD PRIMARY KEY (`Employee ID`,`TaskID`),
  ADD KEY `TaskID` (`TaskID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auth_group`
--
ALTER TABLE `auth_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `auth_group_permissions`
--
ALTER TABLE `auth_group_permissions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `auth_permission`
--
ALTER TABLE `auth_permission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT for table `auth_user`
--
ALTER TABLE `auth_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `auth_user_groups`
--
ALTER TABLE `auth_user_groups`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `auth_user_user_permissions`
--
ALTER TABLE `auth_user_user_permissions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat-chat recipients table`
--
ALTER TABLE `chat-chat recipients table`
  MODIFY `Chat-Chat Recipients ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `chat table`
--
ALTER TABLE `chat table`
  MODIFY `Chat ID` mediumint(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `django_admin_log`
--
ALTER TABLE `django_admin_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `django_content_type`
--
ALTER TABLE `django_content_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `django_migrations`
--
ALTER TABLE `django_migrations`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `employee table`
--
ALTER TABLE `employee table`
  MODIFY `Employee ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `group table`
--
ALTER TABLE `group table`
  MODIFY `Group ID` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `invitation table`
--
ALTER TABLE `invitation table`
  MODIFY `Invitation ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `message table`
--
ALTER TABLE `message table`
  MODIFY `Message ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `notification table`
--
ALTER TABLE `notification table`
  MODIFY `Notification ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `ProjectID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `project_graph_table`
--
ALTER TABLE `project_graph_table`
  MODIFY `GraphID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `report table`
--
ALTER TABLE `report table`
  MODIFY `Report ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `TaskID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auth_group_permissions`
--
ALTER TABLE `auth_group_permissions`
  ADD CONSTRAINT `auth_group_permissio_permission_id_84c5c92e_fk_auth_perm` FOREIGN KEY (`permission_id`) REFERENCES `auth_permission` (`id`),
  ADD CONSTRAINT `auth_group_permissions_group_id_b120cbf9_fk_auth_group_id` FOREIGN KEY (`group_id`) REFERENCES `auth_group` (`id`);

--
-- Constraints for table `auth_permission`
--
ALTER TABLE `auth_permission`
  ADD CONSTRAINT `auth_permission_content_type_id_2f476e4b_fk_django_co` FOREIGN KEY (`content_type_id`) REFERENCES `django_content_type` (`id`);

--
-- Constraints for table `auth_user_groups`
--
ALTER TABLE `auth_user_groups`
  ADD CONSTRAINT `auth_user_groups_group_id_97559544_fk_auth_group_id` FOREIGN KEY (`group_id`) REFERENCES `auth_group` (`id`),
  ADD CONSTRAINT `auth_user_groups_user_id_6a12ed8b_fk_auth_user_id` FOREIGN KEY (`user_id`) REFERENCES `auth_user` (`id`);

--
-- Constraints for table `auth_user_user_permissions`
--
ALTER TABLE `auth_user_user_permissions`
  ADD CONSTRAINT `auth_user_user_permi_permission_id_1fbb5f2c_fk_auth_perm` FOREIGN KEY (`permission_id`) REFERENCES `auth_permission` (`id`),
  ADD CONSTRAINT `auth_user_user_permissions_user_id_a95ead1b_fk_auth_user_id` FOREIGN KEY (`user_id`) REFERENCES `auth_user` (`id`);

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
-- Constraints for table `django_admin_log`
--
ALTER TABLE `django_admin_log`
  ADD CONSTRAINT `django_admin_log_content_type_id_c4bce8eb_fk_django_co` FOREIGN KEY (`content_type_id`) REFERENCES `django_content_type` (`id`),
  ADD CONSTRAINT `django_admin_log_user_id_c564eba6_fk_auth_user_id` FOREIGN KEY (`user_id`) REFERENCES `auth_user` (`id`);

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
  ADD CONSTRAINT `fk_EmployeeID` FOREIGN KEY (`Employee ID`) REFERENCES `employee table` (`Employee ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `projectID_from_projects_to_project-emp` FOREIGN KEY (`ProjectID`) REFERENCES `projects` (`ProjectID`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `EmployeeID_from_employee_to_usertasks` FOREIGN KEY (`Employee ID`) REFERENCES `employee table` (`Employee ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_TaskID` FOREIGN KEY (`TaskID`) REFERENCES `tasks` (`TaskID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
