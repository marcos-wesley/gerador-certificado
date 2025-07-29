
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS `courses` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `workload` VARCHAR(50) NOT NULL,
    `date` DATE NOT NULL,
    `responsible` VARCHAR(255) NOT NULL,
    `description` TEXT
);

CREATE TABLE IF NOT EXISTS `participants` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS `presences` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `course_id` INT NOT NULL,
    `participant_id` INT NOT NULL,
    `is_present` BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`),
    FOREIGN KEY (`participant_id`) REFERENCES `participants`(`id`)
);

CREATE TABLE IF NOT EXISTS `certificates` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `course_id` INT NOT NULL,
    `participant_id` INT NOT NULL,
    `unique_code` VARCHAR(255) NOT NULL UNIQUE,
    `issue_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `file_path` VARCHAR(255) NOT NULL,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`),
    FOREIGN KEY (`participant_id`) REFERENCES `participants`(`id`)
);



-- Tabela de modelos de certificado
CREATE TABLE IF NOT EXISTS `certificate_templates` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `file_path` VARCHAR(255) NOT NULL,
    `fields_config` JSON NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL
);

-- Adicionar coluna template_id na tabela courses
ALTER TABLE `courses` ADD COLUMN `template_id` INT DEFAULT NULL;
ALTER TABLE `courses` ADD FOREIGN KEY (`template_id`) REFERENCES `certificate_templates`(`id`) ON DELETE SET NULL;

-- Inserir modelo padrão
INSERT IGNORE INTO `certificate_templates` (`name`, `description`, `file_path`, `fields_config`) VALUES 
('Modelo Padrão', 'Modelo padrão do sistema com layout simples e elegante', 'templates/default.html', '{"participant_name":{"label":"Nome do Participante","x":50,"y":45,"font_size":24,"font_weight":"bold","color":"#333333"},"course_name":{"label":"Nome do Curso","x":50,"y":55,"font_size":18,"font_weight":"bold","color":"#666666"},"workload":{"label":"Carga Horária","x":30,"y":65,"font_size":14,"font_weight":"normal","color":"#666666"},"course_date":{"label":"Data do Curso","x":70,"y":65,"font_size":14,"font_weight":"normal","color":"#666666"},"responsible":{"label":"Responsável","x":50,"y":80,"font_size":16,"font_weight":"bold","color":"#333333"},"issue_date":{"label":"Data de Emissão","x":20,"y":90,"font_size":12,"font_weight":"normal","color":"#999999"},"unique_code":{"label":"Código de Validação","x":80,"y":90,"font_size":12,"font_weight":"normal","color":"#999999"}}');

