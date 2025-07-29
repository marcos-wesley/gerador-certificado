
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


