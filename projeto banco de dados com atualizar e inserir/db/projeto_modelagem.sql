-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 31-Jan-2024 às 16:08
-- Versão do servidor: 8.0.36-0ubuntu0.22.04.1
-- versão do PHP: 8.1.2-1ubuntu2.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `projeto_modelagem`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` bigint NOT NULL,
  `nm_categoria` varchar(70) COLLATE utf8mb4_general_ci NOT NULL,
  `dt_cadastro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nm_categoria`, `dt_cadastro`) VALUES
(93, 'CAMISAS', '2023-11-23 09:30:38'),
(96, 'Celulares', '2023-11-28 09:21:22');

-- --------------------------------------------------------

--
-- Estrutura da tabela `marcas`
--

CREATE TABLE `marcas` (
  `id_marca` bigint NOT NULL,
  `nm_marca` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `marcas`
--

INSERT INTO `marcas` (`id_marca`, `nm_marca`) VALUES
(34, 'NIKE'),
(35, 'ADIDAS'),
(36, 'PUMA');

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtos`
--

CREATE TABLE `produtos` (
  `id_produto` bigint NOT NULL,
  `id_categoria` bigint NOT NULL,
  `id_marca` bigint DEFAULT NULL,
  `id_prod_tam` bigint NOT NULL,
  `nm_produto` varchar(70) COLLATE utf8mb4_general_ci NOT NULL,
  `ds_descricao` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `vl_valor` decimal(12,2) NOT NULL,
  `nr_estoque` int NOT NULL,
  `dt_cadastro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `produtos`
--

INSERT INTO `produtos` (`id_produto`, `id_categoria`, `id_marca`, `id_prod_tam`, `nm_produto`, `ds_descricao`, `vl_valor`, `nr_estoque`, `dt_cadastro`) VALUES
(548, 93, 34, 0, 'sdfsdf', 'sdf', '11.00', 11, '2023-11-30 11:52:13');

-- --------------------------------------------------------

--
-- Estrutura da tabela `prod_tam`
--

CREATE TABLE `prod_tam` (
  `id_prod_tam` bigint NOT NULL,
  `id_produto` bigint NOT NULL,
  `id_tamanho` bigint NOT NULL,
  `dt_cadastro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `prod_tam`
--

INSERT INTO `prod_tam` (`id_prod_tam`, `id_produto`, `id_tamanho`, `dt_cadastro`) VALUES
(1, 548, 19, '2023-11-30 11:52:13'),
(2, 548, 20, '2023-11-30 11:52:13'),
(3, 548, 21, '2023-11-30 11:52:13');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tamanhos`
--

CREATE TABLE `tamanhos` (
  `id_tam` bigint NOT NULL,
  `nm_tam` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ds_status` enum('Ativo','Inativo') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `tamanhos`
--

INSERT INTO `tamanhos` (`id_tam`, `nm_tam`, `ds_status`) VALUES
(19, 'PP', 'Ativo'),
(20, 'P', 'Ativo'),
(21, 'M', 'Ativo'),
(22, 'G', 'Ativo'),
(23, 'GG', 'Ativo'),
(24, 'XG', 'Ativo'),
(25, 'XGG', 'Ativo');

-- --------------------------------------------------------

--
-- Estrutura da tabela `teste`
--

CREATE TABLE `teste` (
  `id` int NOT NULL,
  `nome` varchar(220) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `teste`
--

INSERT INTO `teste` (`id`, `nome`) VALUES
(1, ''),
(2, 'DROP TABLE teste'),
(3, 'DROP TABLE teste'),
(4, 'DROP TABLE teste;'),
(5, 'DROP TABLE teste;');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Índices para tabela `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`id_marca`);

--
-- Índices para tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id_produto`),
  ADD KEY `id_categoria` (`id_categoria`),
  ADD KEY `index_marca` (`id_marca`);

--
-- Índices para tabela `prod_tam`
--
ALTER TABLE `prod_tam`
  ADD PRIMARY KEY (`id_prod_tam`),
  ADD KEY `id_produtos` (`id_produto`),
  ADD KEY `prod_tama_ibfk_2` (`id_tamanho`);

--
-- Índices para tabela `tamanhos`
--
ALTER TABLE `tamanhos`
  ADD PRIMARY KEY (`id_tam`);

--
-- Índices para tabela `teste`
--
ALTER TABLE `teste`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT de tabela `marcas`
--
ALTER TABLE `marcas`
  MODIFY `id_marca` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id_produto` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=549;

--
-- AUTO_INCREMENT de tabela `prod_tam`
--
ALTER TABLE `prod_tam`
  MODIFY `id_prod_tam` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `tamanhos`
--
ALTER TABLE `tamanhos`
  MODIFY `id_tam` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de tabela `teste`
--
ALTER TABLE `teste`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`),
  ADD CONSTRAINT `produtos_m_id_2` FOREIGN KEY (`id_marca`) REFERENCES `marcas` (`id_marca`) ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `prod_tam`
--
ALTER TABLE `prod_tam`
  ADD CONSTRAINT `prod_tam_ibfk_1` FOREIGN KEY (`id_produto`) REFERENCES `produtos` (`id_produto`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `prod_tama_ibfk_2` FOREIGN KEY (`id_tamanho`) REFERENCES `tamanhos` (`id_tam`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
