-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 15, 2024 at 03:52 PM
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
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `doc`
--

CREATE TABLE `doc` (
  `iddoc` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  `speciality` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `doc`
--

INSERT INTO `doc` (`iddoc`, `iduser`, `speciality`) VALUES
(1, 19, 'sdsdsdsd'),
(6, 32, 'cardiologue');

-- --------------------------------------------------------

--
-- Table structure for table `rendezvous`
--

CREATE TABLE `rendezvous` (
  `rdv_id` int(11) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `medecin_id` int(11) DEFAULT NULL,
  `date_rdv` datetime DEFAULT NULL,
  `status` enum('à venir','terminé','annulé') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `age` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `type` enum('admin','client','doctor') NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `gender`, `age`, `email`, `mobile`, `type`, `password`, `created_at`) VALUES
(11, 'tima', 'benchenina', 'female', 20, 'tima@mail.com', '0555555555', 'admin', '$2y$10$h4b04gaKoHaUjlpOkO7rcOpoS3Ri14FsltX7A/REraOA9hKAyW8da', '2024-11-19 13:56:11'),
(12, 'test1', 'test1', 'male', 50, 'test@mail.com', '0560889543', 'client', '$2y$10$k9CtD.oxU0EBeRlB9GyeZOe.cmckPU1qeq1fDdjbzQD8iJQ0rNoti', '2024-11-22 18:26:01'),
(19, 'test7', 'test7', 'male', 44, 'test7@mai.com', '0452233669', 'doctor', '$2y$10$LiY0LaMwOrrkcj4yW0WnYujM1iu4epI5qDY6gW7.mRPENlblGQ9V.', '2024-11-22 22:11:08'),
(20, 'test8', 'test8', 'male', 55, 'test8@maiil.com', '0455223364', 'doctor', '$2y$10$Fbw/Hgl2dq3McMbbTed.xeabaeNqmvEfQxQl/kIVEFXEfIg1/NrEm', '2024-11-22 22:46:15'),
(31, 'younes', 'abdessamad', 'male', 21, 'younes@mail.com', '0560889569', 'admin', '$2y$10$0RNjOM/2PFZehYjdE2o9Qef1mOZtiDuwqEtpps8DHts/B3JhwSe5m', '2024-11-23 20:16:26'),
(32, 'test9', 'test9', 'male', 22, 'test9@mail.com', '0556669999', 'doctor', '$2y$10$qMhBV6zaXfpPQb7u/.//FO3ossez9rc72Kv8FG9HTVQyba4ROBLGe', '2024-11-23 20:18:57'),
(33, 'yassine', 'kay', 'male', 22, 'yassine@mail.com', '0555555555', 'client', '$2y$10$QOwBl5bGjUa4zpCr6guDN.Z.LslW41hqWz67XloRV1yyzmEcVTQZG', '2024-12-15 14:48:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `doc`
--
ALTER TABLE `doc`
  ADD PRIMARY KEY (`iddoc`),
  ADD KEY `user_doc` (`iduser`);

--
-- Indexes for table `rendezvous`
--
ALTER TABLE `rendezvous`
  ADD PRIMARY KEY (`rdv_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `medecin_id` (`medecin_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `doc`
--
ALTER TABLE `doc`
  MODIFY `iddoc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `rendezvous`
--
ALTER TABLE `rendezvous`
  MODIFY `rdv_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `doc`
--
ALTER TABLE `doc`
  ADD CONSTRAINT `user_doc` FOREIGN KEY (`iduser`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_doc` FOREIGN KEY (`iduser`) REFERENCES `users` (`id`);

--
-- Constraints for table `rendezvous`
--
ALTER TABLE `rendezvous`
  ADD CONSTRAINT `rendezvous_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `rendezvous_ibfk_2` FOREIGN KEY (`medecin_id`) REFERENCES `doc` (`iddoc`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
