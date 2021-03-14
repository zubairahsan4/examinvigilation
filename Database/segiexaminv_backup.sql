-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 25, 2018 at 08:46 PM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `segiexaminvigilation`
--

-- --------------------------------------------------------

--
-- Table structure for table `datesession`
--

CREATE TABLE `datesession` (
  `Session_ID` int(11) NOT NULL,
  `ExamDate` varchar(255) DEFAULT NULL,
  `ExamSession` varchar(255) DEFAULT NULL,
  `InvReq` int(11) DEFAULT '0',
  `InvAss` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `datesession`
--

INSERT INTO `datesession` (`Session_ID`, `ExamDate`, `ExamSession`, `InvReq`, `InvAss`) VALUES
(2, '2018-08-01', '09:00 - 12:00', 4, 1),
(6, '2018-08-01', '14:00 - 17:00', 1, 0),
(7, '2018-08-01', '18:00 - 21:00', 1, 1),
(8, '2018-04-24', '18:00 - 21:00', 1, 1),
(9, '2018-05-04', '14:00 - 17:00', 2, 0),
(10, '2018-05-02', '18:00 - 21:00', 1, 0),
(11, '2018-05-01', '14:00 - 17:00', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `diet`
--

CREATE TABLE `diet` (
  `Diet_ID` int(11) NOT NULL,
  `ExamDiet` varchar(255) DEFAULT NULL,
  `StartDate` varchar(255) DEFAULT NULL,
  `EndDate` varchar(255) DEFAULT NULL,
  `EDID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `diet`
--

INSERT INTO `diet` (`Diet_ID`, `ExamDiet`, `StartDate`, `EndDate`, `EDID`) VALUES
(1, 'AUG2018', '2018-08-01', '2018-08-14', 1),
(4, 'MAY2018', '2018-05-01', '2018-05-04', 1);

-- --------------------------------------------------------

--
-- Table structure for table `exam`
--

CREATE TABLE `exam` (
  `Exam_ID` int(11) NOT NULL,
  `Diet_ID` int(11) DEFAULT NULL,
  `Programme` varchar(255) DEFAULT NULL,
  `CourseName` varchar(255) DEFAULT NULL,
  `TaughtBy` varchar(255) DEFAULT NULL,
  `NStudents` int(11) DEFAULT NULL,
  `Session_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `exam`
--

INSERT INTO `exam` (`Exam_ID`, `Diet_ID`, `Programme`, `CourseName`, `TaughtBy`, `NStudents`, `Session_ID`) VALUES
(9, 1, 'DIBA', 'GLT', 'Zubair', 31, 2),
(11, 1, 'FIC', 'GLT', 'Zubair Ahsan', 39, 2),
(14, 1, 'fdg', 'sd', 'ddddddddddd', 2, 6),
(15, 1, 'dsf', 'dfs', 'fdg', 4, 7),
(17, 4, 'FIT', 'GLT', 'Zubair', 42, 9);

-- --------------------------------------------------------

--
-- Table structure for table `examdepartment`
--

CREATE TABLE `examdepartment` (
  `EDID` int(11) NOT NULL,
  `Username` varchar(50) DEFAULT NULL,
  `Keyword` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `examdepartment`
--

INSERT INTO `examdepartment` (`EDID`, `Username`, `Keyword`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `hrdepartment`
--

CREATE TABLE `hrdepartment` (
  `HRID` int(11) NOT NULL,
  `Username` varchar(50) DEFAULT NULL,
  `Keyword` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hrdepartment`
--

INSERT INTO `hrdepartment` (`HRID`, `Username`, `Keyword`) VALUES
(1, 'hradmin', 'hradmin');

-- --------------------------------------------------------

--
-- Table structure for table `invigilator`
--

CREATE TABLE `invigilator` (
  `I_ID` int(11) NOT NULL,
  `Session_ID` int(11) DEFAULT NULL,
  `Lecturer_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `invigilator`
--

INSERT INTO `invigilator` (`I_ID`, `Session_ID`, `Lecturer_ID`) VALUES
(38, 7, 25),
(40, 2, 25);

-- --------------------------------------------------------

--
-- Table structure for table `lecturer`
--

CREATE TABLE `lecturer` (
  `Lecturer_ID` int(11) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Department` varchar(255) DEFAULT NULL,
  `Username` varchar(50) DEFAULT NULL,
  `Keyword` varchar(255) DEFAULT NULL,
  `Email` varchar(50) DEFAULT NULL,
  `Phone` varchar(255) DEFAULT NULL,
  `Pin` varchar(5) DEFAULT NULL,
  `Active` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lecturer`
--

INSERT INTO `lecturer` (`Lecturer_ID`, `Name`, `Department`, `Username`, `Keyword`, `Email`, `Phone`, `Pin`, `Active`) VALUES
(25, 'John Doe', 'Who cares?', 'jdoe', '$2y$10$AtJwTyCpuxdEBAFEIOQIUOY9p6Vbb1yP./sebpnzDGm45oxsDB/..', 'zubairahsan4@gmail.com', '234325426', '35240', 1),
(31, 'Kumatha updated', 'IT update', 'kumatha', '$2y$10$7pqexVZrPqLlkCVS8N8V1ulakGqqxxyfNCuXxCFf6J6HiLqWXQwCG', 'kumatha@segi.edu.my', '01312423520000', '7504a', 1);

-- --------------------------------------------------------

--
-- Table structure for table `timetable`
--

CREATE TABLE `timetable` (
  `ID` int(11) NOT NULL,
  `Image_path` varchar(255) DEFAULT NULL,
  `Image_name` varchar(255) DEFAULT NULL,
  `Lecturer_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `timetable`
--

INSERT INTO `timetable` (`ID`, `Image_path`, `Image_name`, `Lecturer_ID`) VALUES
(17, 'images/', '30712613_2421402467885809_4533818050897510400_n.jpg', 25);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `datesession`
--
ALTER TABLE `datesession`
  ADD PRIMARY KEY (`Session_ID`);

--
-- Indexes for table `diet`
--
ALTER TABLE `diet`
  ADD PRIMARY KEY (`Diet_ID`),
  ADD KEY `EDID` (`EDID`);

--
-- Indexes for table `exam`
--
ALTER TABLE `exam`
  ADD PRIMARY KEY (`Exam_ID`),
  ADD KEY `Session_ID` (`Session_ID`),
  ADD KEY `Diet_ID` (`Diet_ID`);

--
-- Indexes for table `examdepartment`
--
ALTER TABLE `examdepartment`
  ADD PRIMARY KEY (`EDID`);

--
-- Indexes for table `hrdepartment`
--
ALTER TABLE `hrdepartment`
  ADD PRIMARY KEY (`HRID`);

--
-- Indexes for table `invigilator`
--
ALTER TABLE `invigilator`
  ADD PRIMARY KEY (`I_ID`),
  ADD KEY `Lecturer_ID` (`Lecturer_ID`),
  ADD KEY `Session_ID` (`Session_ID`);

--
-- Indexes for table `lecturer`
--
ALTER TABLE `lecturer`
  ADD PRIMARY KEY (`Lecturer_ID`);

--
-- Indexes for table `timetable`
--
ALTER TABLE `timetable`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Lecturer_ID` (`Lecturer_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `datesession`
--
ALTER TABLE `datesession`
  MODIFY `Session_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `diet`
--
ALTER TABLE `diet`
  MODIFY `Diet_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `exam`
--
ALTER TABLE `exam`
  MODIFY `Exam_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `examdepartment`
--
ALTER TABLE `examdepartment`
  MODIFY `EDID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `hrdepartment`
--
ALTER TABLE `hrdepartment`
  MODIFY `HRID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `invigilator`
--
ALTER TABLE `invigilator`
  MODIFY `I_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;
--
-- AUTO_INCREMENT for table `lecturer`
--
ALTER TABLE `lecturer`
  MODIFY `Lecturer_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
--
-- AUTO_INCREMENT for table `timetable`
--
ALTER TABLE `timetable`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `diet`
--
ALTER TABLE `diet`
  ADD CONSTRAINT `diet_ibfk_1` FOREIGN KEY (`EDID`) REFERENCES `examdepartment` (`EDID`);

--
-- Constraints for table `exam`
--
ALTER TABLE `exam`
  ADD CONSTRAINT `exam_ibfk_1` FOREIGN KEY (`Session_ID`) REFERENCES `datesession` (`Session_ID`),
  ADD CONSTRAINT `exam_ibfk_2` FOREIGN KEY (`Diet_ID`) REFERENCES `diet` (`Diet_ID`) ON DELETE CASCADE;

--
-- Constraints for table `invigilator`
--
ALTER TABLE `invigilator`
  ADD CONSTRAINT `invigilator_ibfk_1` FOREIGN KEY (`Lecturer_ID`) REFERENCES `lecturer` (`Lecturer_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `invigilator_ibfk_2` FOREIGN KEY (`Session_ID`) REFERENCES `datesession` (`Session_ID`) ON DELETE CASCADE;

--
-- Constraints for table `timetable`
--
ALTER TABLE `timetable`
  ADD CONSTRAINT `timetable_ibfk_1` FOREIGN KEY (`Lecturer_ID`) REFERENCES `lecturer` (`Lecturer_ID`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
