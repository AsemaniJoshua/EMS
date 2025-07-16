-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 16, 2025 at 06:12 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.4.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `exam_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `email`, `username`, `first_name`, `last_name`, `password_hash`, `created_at`) VALUES
(1, 'admin@app.ems.com', 'admin', 'Gilbert', 'Kukah', '$2y$12$CSWpzv6.SeRUp8RcStt/2uM6Zi1t1OdqRY18OZ/Up11VfF0UhOggW', '2025-07-06 22:10:28'),
(2, 'admin1@example.com', 'admin_user1', 'Alice', 'Smith', 'hashed_password_admin1', '2025-07-07 10:52:21'),
(3, 'admin2@example.com', 'admin_user2', 'Bob', 'Johnson', 'hashed_password_admin2', '2025-07-07 10:52:21');

-- --------------------------------------------------------

--
-- Table structure for table `choices`
--

DROP TABLE IF EXISTS `choices`;
CREATE TABLE `choices` (
  `choice_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `choice_text` varchar(255) NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `choices`
--

INSERT INTO `choices` (`choice_id`, `question_id`, `choice_text`, `is_correct`) VALUES
(1, 1, 'Hello World', 1),
(2, 1, 'HelloWorld', 0),
(3, 1, 'Hello+World', 0),
(4, 2, 'Queue', 0),
(5, 2, 'Stack', 1),
(6, 2, 'Array', 0),
(7, 3, 'A fixed value', 0),
(8, 3, 'A named storage location for data', 1),
(9, 3, 'A function', 0),
(13, 5, 'Nodes', 1),
(14, 5, 'Arrays', 0),
(15, 5, 'Objects', 0),
(16, 6, 'Sausage', 0),
(17, 7, '1', 0),
(18, 7, '2', 1),
(19, 7, '3', 0),
(20, 7, '4', 0),
(32, 10, 'Mahama', 0),
(33, 10, 'Nana Adu', 0),
(34, 10, 'Rudith', 1),
(35, 8, 'Rudith', 0),
(36, 8, 'Juliet', 0),
(37, 8, 'Augustina', 0),
(38, 8, 'Lily', 1),
(39, 11, 'True', 1),
(40, 11, 'False', 0),
(43, 12, '1', 0),
(44, 12, '2', 1),
(45, 4, 'O(n)', 0),
(46, 4, 'O(log n)', 0),
(47, 4, 'O(1)', 1),
(54, 14, '43', 0),
(55, 14, '38', 1),
(56, 15, 'tounkna', 1),
(57, 15, 'dkljdiihjkn', 0);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `department_id` int(11) NOT NULL,
  `credits` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `code`, `title`, `department_id`, `credits`, `program_id`, `level_id`, `semester_id`) VALUES
(1, 'CS101', 'Introduction to Programming', 1, 3, 1, 100, 1),
(2, 'EE201', 'Circuit Analysis', 2, 4, 2, 200, 1),
(3, 'CS202', 'Data Structures', 1, 3, 1, 200, 2),
(4, 'BA101', 'Principles of Management', 3, 3, 3, 100, 1);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments` (
  `department_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `name`, `description`) VALUES
(1, 'Computer Science', 'Department focusing on computing and software development.'),
(2, 'Electrical Engineering', 'Department focusing on electrical systems and electronics.'),
(3, 'Business Administration', 'Department focusing on management and business studies.');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

DROP TABLE IF EXISTS `exams`;
CREATE TABLE `exams` (
  `exam_id` int(11) NOT NULL,
  `exam_code` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `department_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `status` enum('Pending','Approved','Rejected','Draft','Completed') DEFAULT 'Pending',
  `duration_minutes` int(11) NOT NULL,
  `pass_mark` decimal(5,2) DEFAULT 50.00,
  `total_marks` int(11) NOT NULL,
  `start_datetime` datetime DEFAULT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `max_attempts` int(11) DEFAULT 1,
  `randomize` tinyint(1) DEFAULT 0,
  `show_results` tinyint(1) DEFAULT 1,
  `anti_cheating` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`exam_id`, `exam_code`, `title`, `description`, `department_id`, `program_id`, `semester_id`, `course_id`, `teacher_id`, `status`, `duration_minutes`, `pass_mark`, `total_marks`, `start_datetime`, `end_datetime`, `max_attempts`, `randomize`, `show_results`, `anti_cheating`, `created_at`, `approved_by`, `approved_at`) VALUES
(1, 'CS101-F24-MID', 'CS101 Midterm Exam', 'Midterm exam for Introduction to Programming', 1, 1, 1, 1, 1, 'Completed', 60, 50.00, 100, '2024-11-01 10:00:00', '2024-11-01 11:00:00', 1, 1, 1, 1, '2025-07-07 10:52:23', 1, '2024-10-25 09:00:00'),
(2, 'EE201-F24-FINAL', 'EE201 Final Exam', 'Final exam for Circuit Analysis', 2, 2, 1, 2, 2, 'Approved', 90, 60.00, 100, '2024-12-10 14:00:00', '2024-12-10 15:30:00', 1, 1, 1, 1, '2025-07-07 10:52:23', NULL, NULL),
(3, 'CS202-S25-QUIZ1', 'CS202 Quiz 1', 'First quiz for Data Structures', 1, 1, 2, 3, 3, 'Approved', 30, 40.00, 50, '2025-03-05 09:00:00', '2025-03-05 09:30:00', 2, 1, 1, 0, '2025-07-07 10:52:23', 1, '2025-02-28 11:00:00'),
(4, '343', 'dadasa', 'wwsss', 1, 1, 3, 3, 6, 'Pending', 120, 50.00, 100, '2025-07-15 18:24:00', '2025-07-15 20:24:00', 1, 0, 1, 1, '2025-07-15 18:27:37', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exam_registrations`
--

DROP TABLE IF EXISTS `exam_registrations`;
CREATE TABLE `exam_registrations` (
  `registration_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_registrations`
--

INSERT INTO `exam_registrations` (`registration_id`, `exam_id`, `student_id`, `registered_at`) VALUES
(1, 1, 1, '2024-10-28 10:00:00'),
(2, 1, 2, '2024-10-29 11:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `levels`
--

DROP TABLE IF EXISTS `levels`;
CREATE TABLE `levels` (
  `level_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `levels`
--

INSERT INTO `levels` (`level_id`, `name`) VALUES
(100, 'Level 100'),
(200, 'Level 200'),
(300, 'Level 300'),
(400, 'Level 400');

-- --------------------------------------------------------

--
-- Stand-in structure for view `live_results`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `live_results`;
CREATE TABLE `live_results` (
`registration_id` int(11)
,`total_questions` bigint(21)
,`correct_answers` decimal(22,0)
,`incorrect_answers` decimal(22,0)
,`score_percentage` decimal(28,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `seen` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `message`, `created_at`, `seen`) VALUES
(1, 1, 'Your CS101 Midterm results are available.', '2024-11-01 11:05:00', 0),
(2, 2, 'Your CS101 Midterm results are available.', '2024-11-01 11:05:00', 0),
(3, 1, 'CS202 Quiz 1 is now open for attempts.', '2025-03-04 15:00:00', 1),
(4, 3, 'EE201 Final Exam is scheduled for Dec 10, 2024.', '2024-11-20 10:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `token` varchar(10) NOT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

DROP TABLE IF EXISTS `programs`;
CREATE TABLE `programs` (
  `program_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `department_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`program_id`, `name`, `description`, `department_id`) VALUES
(1, 'BSc Computer Science', 'Undergraduate program in Computer Science', 1),
(2, 'BSc Electrical Engineering', 'Undergraduate program in Electrical Engineering', 2),
(3, 'BBA Business Administration', 'Undergraduate program in Business Administration', 3);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

DROP TABLE IF EXISTS `questions`;
CREATE TABLE `questions` (
  `question_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `sequence_number` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`question_id`, `exam_id`, `question_text`, `sequence_number`) VALUES
(1, 1, 'What is the output of print(\"Hello\" + \" World\")?', 1),
(2, 1, 'Which data structure uses LIFO principle?', 2),
(3, 1, 'What is a variable in programming?', 3),
(4, 3, 'What is the time complexity of searching an element in a sorted array using binary search?', 1),
(5, 3, 'A linked list is a collection of what?', 2),
(6, 3, 'What is a noun?', 3),
(7, 3, 'What is the time complexity of searching an element in a sorted array using binary search?', 4),
(8, 2, 'What is the name of the girl?', 1),
(10, 2, 'This is a new question', 2),
(11, 2, 'Hello World, how are you doing?', 3),
(12, 2, 'gdhjkhvjcrvgbhnj', 4),
(14, 4, 'Hello World', 1),
(15, 4, 'apoiergnvderirgerddakj', 2);

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

DROP TABLE IF EXISTS `results`;
CREATE TABLE `results` (
  `result_id` int(11) NOT NULL,
  `registration_id` int(11) NOT NULL,
  `total_questions` int(11) DEFAULT NULL,
  `correct_answers` int(11) DEFAULT NULL,
  `incorrect_answers` int(11) DEFAULT NULL,
  `score_percentage` decimal(5,2) DEFAULT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`result_id`, `registration_id`, `total_questions`, `correct_answers`, `incorrect_answers`, `score_percentage`, `completed_at`) VALUES
(1, 1, 3, 2, 1, 66.67, '2024-11-01 10:55:00'),
(2, 2, 3, 2, 1, 66.67, '2024-11-01 10:58:00');

-- --------------------------------------------------------

--
-- Table structure for table `semesters`
--

DROP TABLE IF EXISTS `semesters`;
CREATE TABLE `semesters` (
  `semester_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `semesters`
--

INSERT INTO `semesters` (`semester_id`, `name`, `start_date`, `end_date`) VALUES
(1, 'Fall 2024', '2024-09-01', '2024-12-15'),
(2, 'Spring 2025', '2025-01-15', '2025-05-10'),
(3, 'Summer 2025', '2025-06-01', '2025-07-31');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `index_number` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female') DEFAULT NULL,
  `status` enum('active','inactive','graduated') DEFAULT 'active',
  `level_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `resetOnLogin` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `index_number`, `username`, `first_name`, `last_name`, `email`, `phone_number`, `password_hash`, `date_of_birth`, `gender`, `status`, `level_id`, `program_id`, `department_id`, `resetOnLogin`, `created_at`, `updated_at`) VALUES
(1, '10001', 'student_alice', 'Alice', 'Wonder', 'alice@example.com', '0201112233', 'hashed_password_alice', '2003-05-10', 'female', 'inactive', 100, 1, 1, 0, '2025-07-07 10:52:22', '2025-07-09 16:35:52'),
(2, '10002', 'student_bob', 'Bob', 'Builder', 'bob@example.com', '0204445566', 'hashed_password_bob', '2002-11-20', 'male', 'inactive', 200, 1, 1, 0, '2025-07-07 10:52:22', '2025-07-09 16:36:06'),
(4, '5221040415', 'sykukah', 'SIMON', 'KUKAH', 'sykukah@gmail.com', '0246706020', '$2y$12$CADTP8MrZGnQO2jsfQz0rOVWVLHePDzrGc0v7xNuZge4nDwhWyEDO', '2025-07-23', 'male', 'active', 300, 1, 1, 0, '2025-07-09 16:00:35', '2025-07-09 16:35:36');

-- --------------------------------------------------------

--
-- Table structure for table `student_answers`
--

DROP TABLE IF EXISTS `student_answers`;
CREATE TABLE `student_answers` (
  `answer_id` int(11) NOT NULL,
  `registration_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `choice_id` int(11) NOT NULL,
  `answered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_answers`
--

INSERT INTO `student_answers` (`answer_id`, `registration_id`, `question_id`, `choice_id`, `answered_at`) VALUES
(1, 1, 1, 1, '2024-11-01 10:10:00'),
(2, 1, 2, 5, '2024-11-01 10:12:00'),
(3, 1, 3, 7, '2024-11-01 10:15:00'),
(6, 2, 1, 2, '2024-11-01 10:20:00'),
(7, 2, 2, 5, '2024-11-01 10:22:00'),
(8, 2, 3, 8, '2024-11-01 10:25:00');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

DROP TABLE IF EXISTS `teachers`;
CREATE TABLE `teachers` (
  `teacher_id` int(11) NOT NULL,
  `staff_id` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `department_id` int(11) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `resetOnLogin` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`teacher_id`, `staff_id`, `email`, `phone_number`, `username`, `first_name`, `last_name`, `password_hash`, `department_id`, `status`, `resetOnLogin`, `created_at`, `updated_at`) VALUES
(1, 'TCH001', 'teacher1@example.com', '0241234567', 'dr_adams', 'John', 'Adams', 'hashed_password_teacher1', 1, 'active', 0, '2025-07-07 10:52:21', '2025-07-07 10:52:21'),
(2, 'TCH002', 'teacher2@example.com', '0242345678', 'prof_brown', 'Rudith', 'Brown', 'hashed_password_teacher2', 2, 'active', 1, '2025-07-07 10:52:21', '2025-07-08 14:26:08'),
(3, 'TCH003', 'teacher3@example.com', '0243456789', 'Anthony', 'Emily Gomez', 'Davis Garcia', 'hashed_password_teacher3', 1, 'inactive', 0, '2025-07-07 10:52:21', '2025-07-16 14:26:43'),
(5, 'TCH7894566', 'sykukah@gmail.com', '0246706020', 'sykukah@gmail.com', 'SIMON', 'KUKAH', '$2y$12$ZxHRB5xMWvOmhV9r2.UAOOfTO3tCxIoMe.Uy2KZCjCSgV.RlqQ5Zu', 2, 'active', 0, '2025-07-07 11:04:52', '2025-07-07 11:04:52'),
(6, 'adsklj', 'teacher@app.ems.com', '556', 'micheal', 'Micheal', 'Adu', '$2y$12$gi/qhAXSyUXdAJFicnvDJeY7olrbuUppexQgX5FGn7h9az/g1Qbd.', 1, 'active', 0, '2025-07-08 16:15:59', '2025-07-12 14:32:13');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_courses`
--

DROP TABLE IF EXISTS `teacher_courses`;
CREATE TABLE `teacher_courses` (
  `teacher_course_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `assigned_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure for view `live_results`
--
DROP TABLE IF EXISTS `live_results`;

DROP VIEW IF EXISTS `live_results`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `live_results`  AS SELECT `sa`.`registration_id` AS `registration_id`, count(`sa`.`question_id`) AS `total_questions`, sum(case when `c`.`is_correct` then 1 else 0 end) AS `correct_answers`, sum(case when `c`.`is_correct` = 0 then 1 else 0 end) AS `incorrect_answers`, round(sum(case when `c`.`is_correct` then 1 else 0 end) / count(`sa`.`question_id`) * 100,2) AS `score_percentage` FROM (`student_answers` `sa` join `choices` `c` on(`sa`.`choice_id` = `c`.`choice_id`)) GROUP BY `sa`.`registration_id` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `choices`
--
ALTER TABLE `choices`
  ADD PRIMARY KEY (`choice_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `level_id` (`level_id`),
  ADD KEY `semester_id` (`semester_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`exam_id`),
  ADD UNIQUE KEY `exam_code` (`exam_code`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `semester_id` (`semester_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `exam_registrations`
--
ALTER TABLE `exam_registrations`
  ADD PRIMARY KEY (`registration_id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `levels`
--
ALTER TABLE `levels`
  ADD PRIMARY KEY (`level_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `notifications_ibfk_1` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`program_id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`result_id`),
  ADD KEY `registration_id` (`registration_id`);

--
-- Indexes for table `semesters`
--
ALTER TABLE `semesters`
  ADD PRIMARY KEY (`semester_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `index_number` (`index_number`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `level_id` (`level_id`);

--
-- Indexes for table `student_answers`
--
ALTER TABLE `student_answers`
  ADD PRIMARY KEY (`answer_id`),
  ADD KEY `registration_id` (`registration_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `choice_id` (`choice_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`teacher_id`),
  ADD UNIQUE KEY `staff_id` (`staff_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `teacher_courses`
--
ALTER TABLE `teacher_courses`
  ADD PRIMARY KEY (`teacher_course_id`),
  ADD KEY `fk_teacher` (`teacher_id`),
  ADD KEY `fk_course` (`course_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `choices`
--
ALTER TABLE `choices`
  MODIFY `choice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `exam_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `exam_registrations`
--
ALTER TABLE `exam_registrations`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `levels`
--
ALTER TABLE `levels`
  MODIFY `level_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=401;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `program_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `semesters`
--
ALTER TABLE `semesters`
  MODIFY `semester_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `student_answers`
--
ALTER TABLE `student_answers`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `teacher_courses`
--
ALTER TABLE `teacher_courses`
  MODIFY `teacher_course_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `choices`
--
ALTER TABLE `choices`
  ADD CONSTRAINT `choices_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`);

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`),
  ADD CONSTRAINT `courses_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`),
  ADD CONSTRAINT `courses_ibfk_3` FOREIGN KEY (`level_id`) REFERENCES `levels` (`level_id`),
  ADD CONSTRAINT `courses_ibfk_4` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`semester_id`);

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`),
  ADD CONSTRAINT `exams_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`),
  ADD CONSTRAINT `exams_ibfk_3` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`semester_id`),
  ADD CONSTRAINT `exams_ibfk_4` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`),
  ADD CONSTRAINT `exams_ibfk_5` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`),
  ADD CONSTRAINT `exams_ibfk_6` FOREIGN KEY (`approved_by`) REFERENCES `admins` (`admin_id`);

--
-- Constraints for table `exam_registrations`
--
ALTER TABLE `exam_registrations`
  ADD CONSTRAINT `exam_registrations_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`exam_id`),
  ADD CONSTRAINT `exam_registrations_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

--
-- Constraints for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD CONSTRAINT `password_reset_tokens_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `password_reset_tokens_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `password_reset_tokens_ibfk_3` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`admin_id`) ON DELETE CASCADE;

--
-- Constraints for table `programs`
--
ALTER TABLE `programs`
  ADD CONSTRAINT `programs_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`);

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`exam_id`);

--
-- Constraints for table `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `results_ibfk_1` FOREIGN KEY (`registration_id`) REFERENCES `exam_registrations` (`registration_id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`),
  ADD CONSTRAINT `students_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`),
  ADD CONSTRAINT `students_ibfk_3` FOREIGN KEY (`level_id`) REFERENCES `levels` (`level_id`);

--
-- Constraints for table `student_answers`
--
ALTER TABLE `student_answers`
  ADD CONSTRAINT `student_answers_ibfk_1` FOREIGN KEY (`registration_id`) REFERENCES `exam_registrations` (`registration_id`),
  ADD CONSTRAINT `student_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`),
  ADD CONSTRAINT `student_answers_ibfk_3` FOREIGN KEY (`choice_id`) REFERENCES `choices` (`choice_id`);

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`);

--
-- Constraints for table `teacher_courses`
--
ALTER TABLE `teacher_courses`
  ADD CONSTRAINT `fk_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
