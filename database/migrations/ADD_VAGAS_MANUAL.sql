-- Script SQL para adicionar tabela tb_vagas e campos de vinculação em tb_termos
-- Execute este script manualmente no phpMyAdmin ou via linha de comando

-- 1. Criar tabela tb_vagas
CREATE TABLE IF NOT EXISTS `tb_vagas` (
  `id_vaga` int(11) NOT NULL AUTO_INCREMENT,
  `numero_vaga` varchar(20) NOT NULL,
  `atividades` text NOT NULL,
  `nome_orientador` varchar(100) NOT NULL,
  `cargo_orientador` varchar(100) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_termino` date NOT NULL,
  `horario` varchar(50) NOT NULL,
  `fk_id_local` int(11) NOT NULL,
  `fk_id_empresa` int(11) NOT NULL,
  `lotacao` varchar(150) NOT NULL,
  `valor_bolsa` decimal(10,2) NOT NULL,
  `valor_auxilio_transporte` decimal(10,2) NOT NULL,
  `status` enum('disponivel','preenchida','expirada') NOT NULL DEFAULT 'disponivel',
  `fk_id_termo` int(11) DEFAULT NULL,
  `vinculo_tipo` varchar(20) DEFAULT NULL,
  `descricao` text,
  `publicada_em` date DEFAULT NULL,
  `remunerada` tinyint(1) NOT NULL DEFAULT '1',
  `tipo_vaga` varchar(30) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_vaga`),
  UNIQUE KEY `tb_vagas_numero_vaga_unique` (`numero_vaga`),
  KEY `tb_vagas_fk_id_local_foreign` (`fk_id_local`),
  KEY `tb_vagas_fk_id_empresa_foreign` (`fk_id_empresa`),
  KEY `tb_vagas_fk_id_termo_foreign` (`fk_id_termo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Adicionar foreign keys para tb_vagas
ALTER TABLE `tb_vagas`
  ADD CONSTRAINT `tb_vagas_fk_id_local_foreign` FOREIGN KEY (`fk_id_local`) REFERENCES `tb_local` (`id_local`),
  ADD CONSTRAINT `tb_vagas_fk_id_empresa_foreign` FOREIGN KEY (`fk_id_empresa`) REFERENCES `tb_empresas` (`id_empresa`),
  ADD CONSTRAINT `tb_vagas_fk_id_termo_foreign` FOREIGN KEY (`fk_id_termo`) REFERENCES `tb_termos` (`id_termo`) ON DELETE SET NULL;

-- 3. Adicionar campos em tb_termos para vinculação com vagas
ALTER TABLE `tb_termos` 
  ADD COLUMN `fk_id_vaga` int(11) NULL AFTER `fk_id_supervisor_fixo`,
  ADD COLUMN `vinculo` enum('vinculado','nao_vinculado') NOT NULL DEFAULT 'nao_vinculado' AFTER `fk_id_vaga`;

-- 4. Adicionar foreign key para fk_id_vaga em tb_termos
ALTER TABLE `tb_termos`
  ADD CONSTRAINT `tb_termos_fk_id_vaga_foreign` FOREIGN KEY (`fk_id_vaga`) REFERENCES `tb_vagas` (`id_vaga`) ON DELETE SET NULL;

-- Fim do script
