-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 02, 2024 at 05:25 PM
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
-- Database: `look`
--

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `company_id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `company_username` varchar(50) NOT NULL,
  `company_email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `company_number` varchar(50) DEFAULT NULL,
  `company_address` text DEFAULT NULL,
  `company_facebook` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`company_id`, `company_name`, `company_username`, `company_email`, `password`, `profile_picture`, `company_number`, `company_address`, `company_facebook`, `created_at`) VALUES
(1, 'VXI PH', 'vxi_company', 'careersph@vxi.com', '$2y$10$4ODc/pfluUdQuckQF.5W8eluEppdWagQPGIHD0fI0WyE7lBLAbe8i', '1733074563_vxi.png', '(02) 899 2200', '', 'https://www.facebook.com/VXIPH', '2024-12-01 18:29:45');

-- --------------------------------------------------------

--
-- Table structure for table `company_background`
--

CREATE TABLE `company_background` (
  `background_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `background_text` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_culture`
--

CREATE TABLE `company_culture` (
  `culture_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `culture_text` text DEFAULT NULL,
  `benefits_text` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_culture`
--

INSERT INTO `company_culture` (`culture_id`, `company_id`, `culture_text`, `benefits_text`, `created_at`, `updated_at`) VALUES
(1, 1, 'VXI fosters a people-first culture where the emphasis is on talent development, innovation, and providing top-tier customer care solutions. This culture is built on values like excellence, integrity, agility, and teamwork, ensuring every employee feels valued and empowered. Collaboration across diverse teams is a cornerstone, fostering respectful debate and shared goals. The company also prioritizes inventiveness, encouraging curiosity and exploration to improve customer experiences and internal processes. VXI’s dedication to its employees and clients is evident in its innovative training programs and a robust framework for professional growth, which align with its mission of delivering exceptional customer service solutions globally.', 'VXI offers an array of benefits to ensure employee satisfaction and well-being. These include:\r\n\r\n- Comprehensive Healthcare Coverage: Medical, dental, and vision plans.\r\n- Competitive Compensation Packages.\r\n- Employee Wellness Programs.\r\n- Professional Development: Including training programs and career advancement opportunities.\r\n- Paid Time Off (PTO) and paid holidays.\r\n- Employee Discounts.\r\n- Diversity and Inclusion Initiatives: Recognized as one of America’s Greatest Workplaces for Diversity in 2024.\r\n- State-of-the-art Facilities: Featuring an innovation lab and global Centers of Excellence.\r\n- Performance-based Incentives \r\n\r\nVXI’s commitment to creating a nurturing and inclusive work environment has earned it numerous awards, including recognition as the Best Employer of the Year and for its customer service excellence​', '2024-12-02 14:23:22', '2024-12-02 14:27:07');

-- --------------------------------------------------------

--
-- Table structure for table `company_gallery`
--

CREATE TABLE `company_gallery` (
  `gallery_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `image_type` enum('background','culture') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_jobs`
--

CREATE TABLE `company_jobs` (
  `job_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `job_title` varchar(255) NOT NULL,
  `job_description` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `salary_range` varchar(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `employment_type` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','closed') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_jobs`
--

INSERT INTO `company_jobs` (`job_id`, `company_id`, `job_title`, `job_description`, `requirements`, `salary_range`, `location`, `employment_type`, `created_at`, `updated_at`, `status`) VALUES
(1, 1, 'Junior Web Developer', 'Assist in the development and maintenance of websites and web applications under the supervision of senior developers. Participate in coding, debugging, and testing activities.', '- Enrolled in an IT or Computer Science program.\r\n- Basic knowledge of HTML, CSS, and JavaScript.\r\n- Familiarity with frameworks such as Bootstrap or Tailwind is a plus.\r\n- Strong problem-solving and communication skills.\r\n', '₱8,000 - ₱10,000 per month (Allowance for Interns).', 'Quezon City', 'Internship', '2024-12-02 14:18:25', '2024-12-02 14:18:25', 'active'),
(2, 1, 'IT Support Specialist', 'Provide technical support to clients and resolve hardware and software issues. Assist in setting up and maintaining IT systems.', '- Currently pursuing an IT-related degree. \r\n- Basic understanding of operating systems and networking concepts.\r\n- Good troubleshooting skills.\r\n- Customer service orientation.', '₱12,000 - ₱15,000 per month.', 'Quezon City', 'Part-time', '2024-12-02 14:20:59', '2024-12-02 14:20:59', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `event_date` date NOT NULL,
  `location` varchar(255) NOT NULL,
  `venue` varchar(255) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `registration_url` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `title`, `description`, `event_date`, `location`, `venue`, `image_url`, `registration_url`, `created_at`, `updated_at`) VALUES
(1, 'AI Summit London 2023', 'Exploring advancements in AI and machine learning technologies.', '2023-06-15', 'London, UK', 'Excel London', 'images/events/ai-summit.jpg', 'https://aisummit.com', '2024-12-02 18:24:52', '2024-12-02 22:08:04'),
(2, 'Google Cloud Next 2023', 'Showcasing the latest in Google Cloud innovations.', '2023-08-29', 'San Francisco, USA', 'Moscone Center', 'images/events/google-cloud.jpg', 'https://cloud.google.com/next', '2024-12-02 18:24:52', '2024-12-02 22:08:04'),
(3, 'Microsoft Ignite 2023', 'A conference focusing on cloud, data, and AI innovations.', '2023-11-12', 'Seattle, USA', 'Seattle Convention Center', 'images/events/microsoft-ignite.jpg', 'https://ignite.microsoft.com', '2024-12-02 18:24:52', '2024-12-02 22:08:04'),
(4, 'React Native EU 2023', 'Europe’s largest React Native conference.', '2023-09-07', 'Wroclaw, Poland', 'Centrum Kongresowe', 'images/events/react-native.jpg', 'https://react-native.eu', '2024-12-02 18:24:52', '2024-12-02 22:08:04'),
(5, 'Droidcon New York 2023', 'A global Android developer conference.', '2023-09-14', 'New York, USA', 'Pier 36', 'images/events/droidcon.jpg', 'https://droidcon.com', '2024-12-02 18:24:52', '2024-12-02 22:08:05'),
(6, 'AWS re:Invent 2023', 'Amazon’s flagship event on cloud computing and infrastructure.', '2023-11-27', 'Las Vegas, USA', 'Venetian Las Vegas', 'images/events/aws-reinvent.jpg', 'https://reinvent.awsevents.com', '2024-12-02 18:24:52', '2024-12-02 22:08:05'),
(7, 'Swift Island 2023', 'Hands-on sessions for Apple’s APIs and frameworks.', '2023-09-04', 'Texel, Netherlands', 'De Krim Texel', 'images/events/swift-island.jpg', 'https://swiftisland.nl', '2024-12-02 18:24:52', '2024-12-02 22:08:05'),
(8, 'iOSDevUK 2023', 'Workshops and talks on iOS development.', '2023-09-04', 'Aberystwyth, UK', 'University of Aberystwyth', 'images/events/iosdev.jpg', 'https://iosdevuk.com', '2024-12-02 18:24:52', '2024-12-02 22:08:05'),
(9, 'KubeCon + CloudNativeCon 2023', 'Cloud-native technology enthusiasts gather.', '2023-10-24', 'Chicago, USA', 'McCormick Place', 'images/events/kubecon.jpg', 'https://kubecon.io', '2024-12-02 18:24:52', '2024-12-02 22:08:05'),
(10, 'CES 2023', 'Showcasing consumer electronics and IT innovations.', '2023-01-05', 'Las Vegas, USA', 'Las Vegas Convention Center', 'images/events/ces.jpg', 'https://ces.tech', '2024-12-02 18:24:52', '2024-12-02 22:08:05'),
(11, 'try! Swift NYC 2023', 'AI and Swift/iOS Development conference.', '2023-09-05', 'New York, USA', 'Marriott Marquis', 'images/events/try-swift.jpg', 'https://tryswift.co', '2024-12-02 18:25:12', '2024-12-02 22:08:05'),
(12, 'TechCrunch Disrupt 2023', 'A global startup and innovation conference.', '2023-09-18', 'San Francisco, USA', 'Moscone Center', 'images/events/techcrunch.jpg', 'https://techcrunch.com/disrupt', '2024-12-02 18:25:12', '2024-12-02 22:08:05'),
(13, 'FOSSASIA Summit 2023', 'Open-source technologies and software development.', '2023-03-15', 'Singapore', 'Lifelong Learning Institute', 'images/events/fossasia.jpg', 'https://fossasia.org', '2024-12-02 18:25:12', '2024-12-02 22:08:05'),
(14, 'DEF CON 31', 'A renowned cybersecurity conference.', '2023-08-10', 'Las Vegas, USA', 'Caesars Forum', 'images/events/defcon.jpg', 'https://defcon.org', '2024-12-02 18:25:12', '2024-12-02 22:08:05'),
(15, 'PyCon US 2023', 'Python enthusiasts gather to share insights.', '2023-04-19', 'Salt Lake City, USA', 'Salt Palace Convention Center', 'images/events/pycon.jpg', 'https://pycon.org', '2024-12-02 18:25:12', '2024-12-02 22:08:05'),
(16, 'Web Summit 2023', 'One of the largest tech conferences globally.', '2023-11-13', 'Lisbon, Portugal', 'Altice Arena', 'images/events/web-summit.jpg', 'https://websummit.com', '2024-12-02 18:25:12', '2024-12-02 22:08:05'),
(17, 'Game Developers Conference 2023', 'The latest in game development and design.', '2023-03-20', 'San Francisco, USA', 'Moscone Center', 'images/events/gdc.jpg', 'https://gdconf.com', '2024-12-02 18:25:12', '2024-12-02 22:08:05'),
(18, 'Black Hat USA 2023', 'A premier information security conference.', '2023-08-05', 'Las Vegas, USA', 'Mandalay Bay', 'images/events/blackhat.jpg', 'https://blackhat.com', '2024-12-02 18:25:12', '2024-12-02 22:08:05'),
(19, 'Google I/O 2023', 'Unveiling Google’s latest innovations.', '2023-05-10', 'Mountain View, USA', 'Shoreline Amphitheatre', 'images/events/google-io.jpg', 'https://io.google', '2024-12-02 18:25:12', '2024-12-02 22:08:05'),
(20, 'Apple WWDC 2023', 'The latest in Apple development tools.', '2023-06-05', 'Online', 'Virtual Event', 'images/events/wwdc.jpg', 'https://developer.apple.com/wwdc', '2024-12-02 18:25:12', '2024-12-02 22:08:06');

-- --------------------------------------------------------

--
-- Table structure for table `event_tags`
--

CREATE TABLE `event_tags` (
  `tag_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `tag_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_tags`
--

INSERT INTO `event_tags` (`tag_id`, `event_id`, `tag_name`) VALUES
(1, 1, 'AI'),
(2, 2, 'Cloud Computing'),
(3, 3, 'AI'),
(4, 4, 'React Native'),
(5, 5, 'Android'),
(6, 6, 'Cloud'),
(7, 7, 'Swift'),
(8, 8, 'iOS'),
(9, 9, 'Cloud-Native'),
(10, 10, 'Consumer Electronics'),
(11, 11, 'Swift'),
(12, 12, 'Startups'),
(13, 13, 'Open Source'),
(14, 14, 'Cybersecurity'),
(15, 15, 'Python'),
(16, 16, 'Tech'),
(17, 17, 'Game Development'),
(18, 18, 'Cybersecurity'),
(19, 19, 'Google'),
(20, 20, 'Apple');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `contact_details` varchar(255) DEFAULT NULL,
  `home_address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `student_number` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `username`, `password`, `profile_picture`, `contact_details`, `home_address`, `created_at`, `student_number`) VALUES
(1, 'Charles James', 'Walet', 'waletcharlesjames3@gmail.com', 'charlesjames', '$2y$10$K9IAEyfaiCkj8vnCayxgwezLBJkkbbNGHvfhqGkCYLAzBflph7vV.', '1733072152_1x1.png', '0994 713 7570', '26 A Araw St. Sitio Maligaya Project 8, Brgy. Bahay Toro, Quezon City', '2024-11-30 14:33:05', '23-1880');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`company_id`),
  ADD UNIQUE KEY `company_username` (`company_username`),
  ADD UNIQUE KEY `company_email` (`company_email`),
  ADD UNIQUE KEY `unique_company_email` (`company_email`),
  ADD UNIQUE KEY `unique_company_username` (`company_username`);

--
-- Indexes for table `company_background`
--
ALTER TABLE `company_background`
  ADD PRIMARY KEY (`background_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `company_culture`
--
ALTER TABLE `company_culture`
  ADD PRIMARY KEY (`culture_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `company_gallery`
--
ALTER TABLE `company_gallery`
  ADD PRIMARY KEY (`gallery_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `company_jobs`
--
ALTER TABLE `company_jobs`
  ADD PRIMARY KEY (`job_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `event_tags`
--
ALTER TABLE `event_tags`
  ADD PRIMARY KEY (`tag_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`),
  ADD KEY `username` (`username`),
  ADD KEY `student_number` (`student_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `company_background`
--
ALTER TABLE `company_background`
  MODIFY `background_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_culture`
--
ALTER TABLE `company_culture`
  MODIFY `culture_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `company_gallery`
--
ALTER TABLE `company_gallery`
  MODIFY `gallery_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_jobs`
--
ALTER TABLE `company_jobs`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `event_tags`
--
ALTER TABLE `event_tags`
  MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `company_background`
--
ALTER TABLE `company_background`
  ADD CONSTRAINT `company_background_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`);

--
-- Constraints for table `company_culture`
--
ALTER TABLE `company_culture`
  ADD CONSTRAINT `company_culture_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`);

--
-- Constraints for table `company_gallery`
--
ALTER TABLE `company_gallery`
  ADD CONSTRAINT `company_gallery_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`);

--
-- Constraints for table `company_jobs`
--
ALTER TABLE `company_jobs`
  ADD CONSTRAINT `company_jobs_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`);

--
-- Constraints for table `event_tags`
--
ALTER TABLE `event_tags`
  ADD CONSTRAINT `event_tags_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
