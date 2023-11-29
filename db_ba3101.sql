-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 22, 2023 at 12:31 PM
-- Server version: 8.0.31
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `db_ba3101`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbadmin`
--

DROP TABLE IF EXISTS `tbadmin`;
CREATE TABLE IF NOT EXISTS `tbadmin` (
  `emp_ID` int NOT NULL,
  `userName` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Password` varchar(50) NOT NULL,
  KEY `emp_ID` (`emp_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbadmin`
--

INSERT INTO `tbadmin` (`emp_ID`, `userName`, `Password`) VALUES
(1, 'admin', '123');

-- --------------------------------------------------------

--
-- Table structure for table `tbclient`
--

DROP TABLE IF EXISTS `tbclient`;
CREATE TABLE IF NOT EXISTS `tbclient` (
  `plate_Number` varchar(50) NOT NULL,
  `vehicle_Type` char(50) DEFAULT NULL,
  `student_ID` int DEFAULT NULL,
  `emp_ID` int DEFAULT NULL,
  `guest_ID` int DEFAULT NULL,
  `Contact` varchar(11) NOT NULL,
  `type` char(50) DEFAULT NULL,
  `fileName` varchar(50) NOT NULL,
  `filePath` varchar(50) NOT NULL,
  PRIMARY KEY (`plate_Number`),
  KEY `student_ID` (`student_ID`),
  KEY `emp_ID` (`emp_ID`),
  KEY `guest_ID` (`guest_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbclient`
--

INSERT INTO `tbclient` (`plate_Number`, `vehicle_Type`, `student_ID`, `emp_ID`, `guest_ID`, `Contact`, `type`, `fileName`, `filePath`) VALUES
('BAV-163', 'Coupe', NULL, 2, NULL, '09931254621', 'Professor', '', ''),
('BAK-875', 'Crossover', NULL, 3, NULL, '09919134636', 'Professor', '', ''),
('BAT-347', 'Hatchback', NULL, 4, NULL, '09944701129', 'Professor', '', ''),
('DAD-808', 'Micro', NULL, 5, NULL, '09556312349', 'Professor', '', ''),
('IDF-891', 'MUX', 2135588, NULL, NULL, '09877361128', 'Student', '', ''),
('VFA-175', 'Mazda 3', 2137452, NULL, NULL, '09053127397', 'Student', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `tbempinfo`
--

DROP TABLE IF EXISTS `tbempinfo`;
CREATE TABLE IF NOT EXISTS `tbempinfo` (
  `empid` int NOT NULL AUTO_INCREMENT,
  `lastname` varchar(25) NOT NULL,
  `firstname` varchar(25) NOT NULL,
  `department` varchar(20) NOT NULL,
  PRIMARY KEY (`empid`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbempinfo`
--

INSERT INTO `tbempinfo` (`empid`, `lastname`, `firstname`, `department`) VALUES
(1, 'aguila', 'nina', 'cics'),
(2, 'alimoren', 'dioneces', 'cics'),
(3, 'balazon', 'francis', 'cics'),
(4, 'melo', 'jonnah', 'cics'),
(5, 'amorado', 'ryndel', 'cics');

-- --------------------------------------------------------

--
-- Table structure for table `tbguestinfo`
--

DROP TABLE IF EXISTS `tbguestinfo`;
CREATE TABLE IF NOT EXISTS `tbguestinfo` (
  `guest_ID` int NOT NULL AUTO_INCREMENT,
  `lastname` varchar(50) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  PRIMARY KEY (`guest_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblogs`
--

DROP TABLE IF EXISTS `tblogs`;
CREATE TABLE IF NOT EXISTS `tblogs` (
  `plate_Number` varchar(50) NOT NULL,
  `recordDate` date DEFAULT NULL,
  `time_In` time DEFAULT NULL,
  `time_Out` time DEFAULT NULL,
  KEY `plate_Number` (`plate_Number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tblogs`
--

INSERT INTO `tblogs` (`plate_Number`, `recordDate`, `time_In`, `time_Out`) VALUES
('VFA-175', '2023-11-06', '08:00:00', '17:00:00'),
('BAK-875', '2023-11-08', '10:08:52', '10:08:54'),
('BAV-163', '2023-11-09', '09:34:18', '09:35:25'),
('VFA-175', '2023-11-09', '09:34:12', '08:33:03'),
('BAT-347', '2023-11-08', '10:06:20', '13:34:39'),
('BAK-875', '2023-11-08', '10:06:15', '10:08:40'),
('DAD-808', '2023-11-08', '10:06:07', '10:06:29'),
('BAV-163', '2023-11-08', '10:06:05', '13:34:43'),
('VFA-175', '2023-11-21', '10:19:30', '10:31:31');

-- --------------------------------------------------------

--
-- Table structure for table `tbstaff`
--

DROP TABLE IF EXISTS `tbstaff`;
CREATE TABLE IF NOT EXISTS `tbstaff` (
  `emp_ID` int NOT NULL,
  `userName` varchar(50) NOT NULL,
  `Password` varchar(50) NOT NULL,
  KEY `emp_ID` (`emp_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbstaff`
--

INSERT INTO `tbstaff` (`emp_ID`, `userName`, `Password`) VALUES
(2, 'staff1', '123'),
(3, 'staff2', '123');

-- --------------------------------------------------------

--
-- Table structure for table `tbstudinfo`
--

DROP TABLE IF EXISTS `tbstudinfo`;
CREATE TABLE IF NOT EXISTS `tbstudinfo` (
  `studid` int NOT NULL AUTO_INCREMENT,
  `lastname` varchar(25) NOT NULL,
  `firstname` varchar(25) NOT NULL,
  `course` varchar(20) NOT NULL,
  PRIMARY KEY (`studid`)
) ENGINE=MyISAM AUTO_INCREMENT=2137460 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbstudinfo`
--

INSERT INTO `tbstudinfo` (`studid`, `lastname`, `firstname`, `course`) VALUES
(2135588, 'Cuya', 'Lester', 'BSIT'),
(2137452, 'Acda', 'Rafael', 'BSIT'),
(2133057, 'Alday', 'Keon', 'BSIT'),
(2136679, 'Castillo', 'Dianne', 'BSIT'),
(2132984, 'Caniete', 'Cristel', 'BSIT');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
