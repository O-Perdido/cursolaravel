-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 12/11/2025 às 15:59
-- Versão do servidor: 5.7.23-23
-- Versão do PHP: 8.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `ebcp_db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracoes`
--

CREATE TABLE `configuracoes` (
  `id_configuracao` bigint(20) UNSIGNED NOT NULL,
  `chave` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` text COLLATE utf8mb4_unicode_ci,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'texto',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_alteracao_termo`
--

CREATE TABLE `tb_alteracao_termo` (
  `id_alteracao` int(11) NOT NULL,
  `data_alteracao` date DEFAULT NULL,
  `fk_id_termo` int(11) DEFAULT NULL,
  `fk_id_supervisor` int(11) DEFAULT NULL,
  `nome_orientador_alteracao` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cargo_orientador_alteracao` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_fim_estagio_alteracao` date DEFAULT NULL,
  `valor_bolsa_alteracao` decimal(10,2) DEFAULT NULL,
  `auxilio_transporte_alteracao` decimal(10,2) DEFAULT NULL,
  `horario_alteracao` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `desc_atividades_alteracao` text COLLATE utf8mb4_unicode_ci,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `old_fk_id_supervisor` int(11) DEFAULT NULL,
  `old_nome_orientador` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `old_cargo_orientador` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `old_data_fim_estagio` date DEFAULT NULL,
  `old_horario` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `old_valor_bolsa` decimal(10,2) DEFAULT NULL,
  `old_auxilio_transporte` decimal(10,2) DEFAULT NULL,
  `old_desc_atividades` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_cidade`
--

CREATE TABLE `tb_cidade` (
  `id_cidade` int(11) NOT NULL,
  `nm_cidade` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cd_ibge` int(11) DEFAULT NULL,
  `fk_id_estado` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_concessoes_recesso`
--

CREATE TABLE `tb_concessoes_recesso` (
  `id_concessao` int(10) UNSIGNED NOT NULL,
  `fk_id_termo` int(11) NOT NULL,
  `data_inicio_recesso` date NOT NULL,
  `data_fim_recesso` date NOT NULL,
  `total_dias` int(11) NOT NULL,
  `data_concessao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fk_id_usuario` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('ativo','excluido') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativo',
  `motivo_exclusao` text COLLATE utf8mb4_unicode_ci,
  `data_exclusao` timestamp NULL DEFAULT NULL,
  `fk_id_usuario_exclusao` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_dados_ebcp`
--

CREATE TABLE `tb_dados_ebcp` (
  `id_ebcp` int(11) NOT NULL,
  `nome_ebcp` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endereço_ebcp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep_ebcp` varchar(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_ebcp` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contato_ebcp` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cnpj_ebcp` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nome_representante` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `tb_dados_ebcp`
--

INSERT INTO `tb_dados_ebcp` (`id_ebcp`, `nome_ebcp`, `endereço_ebcp`, `cep_ebcp`, `email_ebcp`, `contato_ebcp`, `cnpj_ebcp`, `nome_representante`, `updated_at`, `created_at`) VALUES
(1, 'EBCP CONSULTORIA LTDA', 'RUA WENCESLAU BRAZ 332, VILA MOEMA - TUBARÃO - SC', '88705-070', 'contato@ebcpconsultoria.com.br', '(48) 9 9146-8761', '41.813.282/0001-23', 'MOACIR AGUIAR', NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_empresas`
--

CREATE TABLE `tb_empresas` (
  `id_empresa` int(11) NOT NULL,
  `nome_empresa` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_cnpj` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_telefone` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_celular` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_cep` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endereco` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_endereco` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complemento_endereco` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bairro` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fk_id_cidade` int(11) DEFAULT NULL,
  `nome_representante` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cargo_representante` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cpf_representante` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_apolice` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nome_seguradora` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `taxa_percentual` decimal(10,2) DEFAULT NULL,
  `taxa_fixa` decimal(10,2) DEFAULT NULL,
  `tipo_taxa` enum('fixa','percentual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_escolas`
--

CREATE TABLE `tb_escolas` (
  `id_escola` int(11) NOT NULL,
  `nome_escola` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_cnpj` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_telefone` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_celular` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_cep` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endereco` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_endereco` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complemento_endereco` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bairro` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fk_id_cidade` int(11) DEFAULT NULL,
  `nome_representante` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cargo_representante` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cpf_representante` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_apolice` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nome_seguradora` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_estado`
--

CREATE TABLE `tb_estado` (
  `id_estado` int(11) NOT NULL,
  `nm_estado` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uf_estado` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cd_uf` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_estagiarios`
--

CREATE TABLE `tb_estagiarios` (
  `id_estagiario` int(11) NOT NULL,
  `nome_estagiario` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_cpf` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `numero_telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_celular` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_cep` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endereco` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_endereco` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complemento_endereco` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bairro` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fk_id_cidade` int(11) DEFAULT NULL,
  `curso` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nivel_curso` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `area_de_estagio` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nome_mae` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_documento` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comprovante_residencia` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comprovante_escolar` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_pis` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_chave_pix` enum('CPF','EMAIL','TELEFONE','ALEATORIA') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chave_pix` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instituicao_ensino` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_folhas_pagamento`
--

CREATE TABLE `tb_folhas_pagamento` (
  `id_folha_pagamento` int(11) NOT NULL,
  `numero_folha` int(11) DEFAULT NULL,
  `data_folha` date DEFAULT NULL,
  `vencimento_folha` date DEFAULT NULL,
  `ano_referencia` year(4) DEFAULT NULL,
  `mes_referencia` tinyint(4) DEFAULT NULL,
  `fk_id_empresa` int(11) DEFAULT NULL,
  `fk_id_local` int(20) DEFAULT NULL,
  `total_bolsa_mes` decimal(20,2) DEFAULT NULL,
  `total_auxilio_transporte_mes` decimal(20,2) DEFAULT NULL,
  `total_recesso` decimal(20,2) DEFAULT NULL,
  `total_taxa_adm` decimal(20,2) DEFAULT NULL,
  `total_folha` decimal(20,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tipo_calculo_auxilio_transporte` enum('mensal','diario') COLLATE utf8_unicode_ci DEFAULT 'mensal',
  `tipo_calculo_recesso` enum('original','com_saldo') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'original',
  `dias_uteis` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_folhas_termos`
--

CREATE TABLE `tb_folhas_termos` (
  `id` int(11) NOT NULL,
  `fk_id_termo` int(11) DEFAULT '0',
  `fk_id_folha` int(11) DEFAULT '0',
  `dias_trabalhados` tinyint(4) DEFAULT NULL,
  `valor_bolsa` decimal(20,2) DEFAULT NULL,
  `valor_bolsa_mes` decimal(20,2) DEFAULT NULL,
  `valor_auxilio_transporte` decimal(20,2) DEFAULT NULL,
  `valor_auxilio_transporte_mes` decimal(20,2) DEFAULT NULL,
  `valor_recesso` decimal(20,2) DEFAULT NULL,
  `taxa_adm` decimal(20,2) DEFAULT NULL,
  `descontos` decimal(20,2) DEFAULT NULL,
  `total` decimal(20,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_local`
--

CREATE TABLE `tb_local` (
  `id_local` int(11) NOT NULL,
  `descricao` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fk_id_empresa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_representantes`
--

CREATE TABLE `tb_representantes` (
  `id_representante` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cargo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cpf` varchar(14) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `representavel_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `representavel_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_rescisao`
--

CREATE TABLE `tb_rescisao` (
  `id_rescisao` int(11) NOT NULL,
  `fk_id_termo` int(11) DEFAULT NULL,
  `data_rescisao` date DEFAULT NULL,
  `motivo` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_supervisores`
--

CREATE TABLE `tb_supervisores` (
  `id_supervisor` int(11) NOT NULL,
  `nome_supervisor` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fk_id_empresa` int(11) DEFAULT NULL,
  `area_formacao` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tempo_experiencia` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cpf_supervisor` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_termos`
--

CREATE TABLE `tb_termos` (
  `id_termo` int(11) NOT NULL,
  `numero_termo` int(11) DEFAULT NULL,
  `ano_termo` year(4) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `fk_id_estagiario` int(11) DEFAULT NULL,
  `fk_id_empresa` int(11) DEFAULT NULL,
  `fk_id_escola` int(11) DEFAULT NULL,
  `fk_id_supervisor` int(11) DEFAULT NULL,
  `fk_id_supervisor_fixo` int(11) DEFAULT NULL,
  `desc_atividades` text COLLATE utf8mb4_unicode_ci,
  `desc_atividades_fixo` text COLLATE utf8mb4_unicode_ci,
  `nome_orientador` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nome_orientador_fixo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cargo_orientador` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cargo_orientador_fixo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_inicio_estagio` date DEFAULT NULL,
  `data_fim_estagio` date DEFAULT NULL,
  `data_fim_estagio_fixo` date DEFAULT NULL,
  `valor_bolsa` decimal(10,2) DEFAULT NULL,
  `valor_bolsa_fixo` decimal(10,2) DEFAULT NULL,
  `auxilio_transporte_fixo` decimal(10,2) DEFAULT NULL,
  `auxilio_transporte` decimal(10,2) DEFAULT NULL,
  `horario_fixo` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `horario` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `lotacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zapsign_doc_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zapsign_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zapsign_enviado_em` timestamp NULL DEFAULT NULL,
  `fk_id_local` int(11) DEFAULT NULL,
  `saldo_recesso` int(11) DEFAULT '30'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verification_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verification_expires_at` timestamp NULL DEFAULT NULL,
  `current_team_id` bigint(20) UNSIGNED DEFAULT NULL,
  `profile_photo_path` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `nivel` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fk_id_empresa` int(11) DEFAULT NULL,
  `fk_id_estagiario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Índices de tabela `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Índices de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`id_configuracao`),
  ADD UNIQUE KEY `configuracoes_chave_unique` (`chave`);

--
-- Índices de tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Índices de tabela `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Índices de tabela `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Índices de tabela `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Índices de tabela `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Índices de tabela `tb_alteracao_termo`
--
ALTER TABLE `tb_alteracao_termo`
  ADD PRIMARY KEY (`id_alteracao`),
  ADD KEY `tb_alteracao_termo_ibfk_1` (`fk_id_termo`),
  ADD KEY `tb_alteracao_termo_ibfk_2` (`fk_id_supervisor`);

--
-- Índices de tabela `tb_cidade`
--
ALTER TABLE `tb_cidade`
  ADD PRIMARY KEY (`id_cidade`),
  ADD KEY `fk_id_estado` (`fk_id_estado`);

--
-- Índices de tabela `tb_concessoes_recesso`
--
ALTER TABLE `tb_concessoes_recesso`
  ADD PRIMARY KEY (`id_concessao`),
  ADD KEY `tb_concessoes_recesso_fk_id_termo_foreign` (`fk_id_termo`),
  ADD KEY `tb_concessoes_recesso_fk_id_usuario_foreign` (`fk_id_usuario`),
  ADD KEY `tb_concessoes_recesso_fk_id_usuario_exclusao_foreign` (`fk_id_usuario_exclusao`);

--
-- Índices de tabela `tb_dados_ebcp`
--
ALTER TABLE `tb_dados_ebcp`
  ADD PRIMARY KEY (`id_ebcp`);

--
-- Índices de tabela `tb_empresas`
--
ALTER TABLE `tb_empresas`
  ADD PRIMARY KEY (`id_empresa`),
  ADD KEY `fk_id_cidade` (`fk_id_cidade`);

--
-- Índices de tabela `tb_escolas`
--
ALTER TABLE `tb_escolas`
  ADD PRIMARY KEY (`id_escola`),
  ADD KEY `fk_id_cidade` (`fk_id_cidade`);

--
-- Índices de tabela `tb_estado`
--
ALTER TABLE `tb_estado`
  ADD PRIMARY KEY (`id_estado`);

--
-- Índices de tabela `tb_estagiarios`
--
ALTER TABLE `tb_estagiarios`
  ADD PRIMARY KEY (`id_estagiario`),
  ADD KEY `fk_id_cidade` (`fk_id_cidade`);

--
-- Índices de tabela `tb_folhas_pagamento`
--
ALTER TABLE `tb_folhas_pagamento`
  ADD PRIMARY KEY (`id_folha_pagamento`),
  ADD UNIQUE KEY `tb_folhas_pagamento_ibfk_2` (`fk_id_local`) USING BTREE,
  ADD KEY `tb_folhas_pagamento_ibfk_1` (`fk_id_empresa`);

--
-- Índices de tabela `tb_folhas_termos`
--
ALTER TABLE `tb_folhas_termos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tb_folhas_termos_ibfk_1` (`fk_id_termo`),
  ADD KEY `tb_folhas_termos_ibfk_2` (`fk_id_folha`);

--
-- Índices de tabela `tb_local`
--
ALTER TABLE `tb_local`
  ADD PRIMARY KEY (`id_local`),
  ADD KEY `fk_id_empresa` (`fk_id_empresa`);

--
-- Índices de tabela `tb_representantes`
--
ALTER TABLE `tb_representantes`
  ADD PRIMARY KEY (`id_representante`),
  ADD KEY `tb_representantes_representavel_type_representavel_id_index` (`representavel_type`,`representavel_id`),
  ADD KEY `tb_representantes_email_index` (`email`);

--
-- Índices de tabela `tb_rescisao`
--
ALTER TABLE `tb_rescisao`
  ADD PRIMARY KEY (`id_rescisao`),
  ADD KEY `fk_id_termo` (`fk_id_termo`) USING BTREE;

--
-- Índices de tabela `tb_supervisores`
--
ALTER TABLE `tb_supervisores`
  ADD PRIMARY KEY (`id_supervisor`),
  ADD KEY `fk_id_empresa` (`fk_id_empresa`);

--
-- Índices de tabela `tb_termos`
--
ALTER TABLE `tb_termos`
  ADD PRIMARY KEY (`id_termo`),
  ADD KEY `fk_id_estagiario` (`fk_id_estagiario`),
  ADD KEY `fk_id_empresa` (`fk_id_empresa`),
  ADD KEY `fk_id_escola` (`fk_id_escola`),
  ADD KEY `fk_id_supervisor` (`fk_id_supervisor`),
  ADD KEY `tb_termos_ibfk_5` (`fk_id_supervisor_fixo`),
  ADD KEY `fk_id_local` (`fk_id_local`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `fk_id_empresa` (`fk_id_empresa`),
  ADD KEY `fk_id_estagiario` (`fk_id_estagiario`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  MODIFY `id_configuracao` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_alteracao_termo`
--
ALTER TABLE `tb_alteracao_termo`
  MODIFY `id_alteracao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_concessoes_recesso`
--
ALTER TABLE `tb_concessoes_recesso`
  MODIFY `id_concessao` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_empresas`
--
ALTER TABLE `tb_empresas`
  MODIFY `id_empresa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_escolas`
--
ALTER TABLE `tb_escolas`
  MODIFY `id_escola` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_estagiarios`
--
ALTER TABLE `tb_estagiarios`
  MODIFY `id_estagiario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_folhas_pagamento`
--
ALTER TABLE `tb_folhas_pagamento`
  MODIFY `id_folha_pagamento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_folhas_termos`
--
ALTER TABLE `tb_folhas_termos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_local`
--
ALTER TABLE `tb_local`
  MODIFY `id_local` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_representantes`
--
ALTER TABLE `tb_representantes`
  MODIFY `id_representante` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_rescisao`
--
ALTER TABLE `tb_rescisao`
  MODIFY `id_rescisao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_supervisores`
--
ALTER TABLE `tb_supervisores`
  MODIFY `id_supervisor` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_termos`
--
ALTER TABLE `tb_termos`
  MODIFY `id_termo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `tb_alteracao_termo`
--
ALTER TABLE `tb_alteracao_termo`
  ADD CONSTRAINT `tb_alteracao_termo_ibfk_1` FOREIGN KEY (`fk_id_termo`) REFERENCES `tb_termos` (`id_termo`),
  ADD CONSTRAINT `tb_alteracao_termo_ibfk_2` FOREIGN KEY (`fk_id_supervisor`) REFERENCES `tb_supervisores` (`id_supervisor`);

--
-- Restrições para tabelas `tb_cidade`
--
ALTER TABLE `tb_cidade`
  ADD CONSTRAINT `tb_cidade_ibfk_1` FOREIGN KEY (`fk_id_estado`) REFERENCES `tb_estado` (`id_estado`);

--
-- Restrições para tabelas `tb_concessoes_recesso`
--
ALTER TABLE `tb_concessoes_recesso`
  ADD CONSTRAINT `tb_concessoes_recesso_fk_id_termo_foreign` FOREIGN KEY (`fk_id_termo`) REFERENCES `tb_termos` (`id_termo`) ON DELETE CASCADE,
  ADD CONSTRAINT `tb_concessoes_recesso_fk_id_usuario_exclusao_foreign` FOREIGN KEY (`fk_id_usuario_exclusao`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tb_concessoes_recesso_fk_id_usuario_foreign` FOREIGN KEY (`fk_id_usuario`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `tb_empresas`
--
ALTER TABLE `tb_empresas`
  ADD CONSTRAINT `tb_empresas_ibfk_1` FOREIGN KEY (`fk_id_cidade`) REFERENCES `tb_cidade` (`id_cidade`);

--
-- Restrições para tabelas `tb_escolas`
--
ALTER TABLE `tb_escolas`
  ADD CONSTRAINT `tb_escolas_ibfk_1` FOREIGN KEY (`fk_id_cidade`) REFERENCES `tb_cidade` (`id_cidade`);

--
-- Restrições para tabelas `tb_estagiarios`
--
ALTER TABLE `tb_estagiarios`
  ADD CONSTRAINT `tb_estagiarios_ibfk_1` FOREIGN KEY (`fk_id_cidade`) REFERENCES `tb_cidade` (`id_cidade`);

--
-- Restrições para tabelas `tb_folhas_pagamento`
--
ALTER TABLE `tb_folhas_pagamento`
  ADD CONSTRAINT `tb_folhas_pagamento_ibfk_1` FOREIGN KEY (`fk_id_empresa`) REFERENCES `tb_empresas` (`id_empresa`),
  ADD CONSTRAINT `tb_folhas_pagamento_ibfk_2` FOREIGN KEY (`fk_id_local`) REFERENCES `tb_local` (`id_local`);

--
-- Restrições para tabelas `tb_folhas_termos`
--
ALTER TABLE `tb_folhas_termos`
  ADD CONSTRAINT `tb_folhas_termos_ibfk_1` FOREIGN KEY (`fk_id_termo`) REFERENCES `tb_termos` (`id_termo`),
  ADD CONSTRAINT `tb_folhas_termos_ibfk_2` FOREIGN KEY (`fk_id_folha`) REFERENCES `tb_folhas_pagamento` (`id_folha_pagamento`);

--
-- Restrições para tabelas `tb_local`
--
ALTER TABLE `tb_local`
  ADD CONSTRAINT `tb_local_ibfk_1` FOREIGN KEY (`fk_id_empresa`) REFERENCES `tb_empresas` (`id_empresa`);

--
-- Restrições para tabelas `tb_rescisao`
--
ALTER TABLE `tb_rescisao`
  ADD CONSTRAINT `tb_rescisao_ibfk_1` FOREIGN KEY (`fk_id_termo`) REFERENCES `tb_termos` (`id_termo`);

--
-- Restrições para tabelas `tb_supervisores`
--
ALTER TABLE `tb_supervisores`
  ADD CONSTRAINT `tb_supervisores_ibfk_1` FOREIGN KEY (`fk_id_empresa`) REFERENCES `tb_empresas` (`id_empresa`);

--
-- Restrições para tabelas `tb_termos`
--
ALTER TABLE `tb_termos`
  ADD CONSTRAINT `tb_termos_ibfk_1` FOREIGN KEY (`fk_id_estagiario`) REFERENCES `tb_estagiarios` (`id_estagiario`),
  ADD CONSTRAINT `tb_termos_ibfk_2` FOREIGN KEY (`fk_id_empresa`) REFERENCES `tb_empresas` (`id_empresa`),
  ADD CONSTRAINT `tb_termos_ibfk_3` FOREIGN KEY (`fk_id_escola`) REFERENCES `tb_escolas` (`id_escola`),
  ADD CONSTRAINT `tb_termos_ibfk_4` FOREIGN KEY (`fk_id_supervisor`) REFERENCES `tb_supervisores` (`id_supervisor`),
  ADD CONSTRAINT `tb_termos_ibfk_5` FOREIGN KEY (`fk_id_supervisor_fixo`) REFERENCES `tb_supervisores` (`id_supervisor`),
  ADD CONSTRAINT `tb_termos_ibfk_6` FOREIGN KEY (`fk_id_local`) REFERENCES `tb_local` (`id_local`);

--
-- Restrições para tabelas `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `tb_users_ibfk_1` FOREIGN KEY (`fk_id_empresa`) REFERENCES `tb_empresas` (`id_empresa`),
  ADD CONSTRAINT `tb_users_ibfk_2` FOREIGN KEY (`fk_id_estagiario`) REFERENCES `tb_estagiarios` (`id_estagiario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
