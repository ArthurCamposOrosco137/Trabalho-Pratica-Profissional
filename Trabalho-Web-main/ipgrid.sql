-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 24, 2026 at 08:35 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ipgrid`
--

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `email`, `senha`, `criado_em`) VALUES
(1, 'teste1', 'teste@teste.com', '$2y$10$AHRVrnQKI4qE0021hRJnTO9vHa6iFt/hm9LZHGlfOnioMoAD71mjC', '2026-03-24 19:31:16'),
(2, 'IAN', 'ianlucapereira2007@gmail.com', '$2y$10$7zCMwsarryhHGgT/iPzUzedoegEBvHOhInijLHdrF4DuaCiXDvRem', '2026-03-24 19:31:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;





-- Tabela de sub-redes
CREATE TABLE `subredes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `rede` varchar(45) NOT NULL COMMENT 'Ex: 192.168.1.0',
  `mascara` int(3) NOT NULL COMMENT 'CIDR: 24',
  `descricao` text,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `rede` (`rede`, `mascara`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de IPs
CREATE TABLE `ips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subrede_id` int(11) NOT NULL,
  `endereco` varchar(45) NOT NULL COMMENT 'Ex: 192.168.1.10',
  `hostname` varchar(255) DEFAULT NULL,
  `descricao` text,
  `responsavel` varchar(100) DEFAULT NULL,
  `status` enum('livre','em_uso','reservado','expirado') DEFAULT 'livre',
  `reservado_em` timestamp NULL DEFAULT NULL,
  `expiracao` timestamp NULL DEFAULT NULL,
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `endereco` (`endereco`),
  KEY `subrede_id` (`subrede_id`),
  FOREIGN KEY (`subrede_id`) REFERENCES `subredes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de logs (auditoria)
CREATE TABLE `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) NOT NULL,
  `acao` varchar(255) NOT NULL,
  `detalhes` text,
  `ip` varchar(45) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;