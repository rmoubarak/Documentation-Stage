SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `acces` (
`id` int(10) UNSIGNED NOT NULL,
`utilisateur_id` int(10) UNSIGNED NOT NULL,
`date` datetime NOT NULL,
`ip` varchar(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `actualite` (
`id` int(10) UNSIGNED NOT NULL,
`utilisateur_id` int(10) UNSIGNED NOT NULL,
`created_at` date NOT NULL,
`titre` varchar(128) NOT NULL,
`libelle` longtext NOT NULL,
`statut` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `actualite` (`id`, `utilisateur_id`, `created_at`, `titre`, `libelle`, `statut`) VALUES
(4, 1, '2022-05-16', 'Ceci est un exemple d\'actualité dynamique', 'Voir Administration\r\n\r\nLorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'En ligne'),
(5, 1, '2022-05-16', 'De Finibus Bonorum et Malorum', 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 'En ligne');

CREATE TABLE `direction` (
`id` varchar(3) NOT NULL,
`pole_id` int(10) NOT NULL,
`created_at` datetime NOT NULL,
`updated_at` datetime DEFAULT NULL,
`deleted_at` datetime DEFAULT NULL,
`libelle` varchar(128) NOT NULL,
`sigle` varchar(8) NOT NULL,
`actif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `direction` (`id`, `pole_id`, `created_at`, `updated_at`, `deleted_at`, `libelle`, `sigle`, `actif`) VALUES
('11A', 11, '2022-03-30 15:30:28', NULL, NULL, 'Direction de la communication et des relations publiques', 'DCRP', 1),
('11B', 11, '2022-03-30 15:30:28', NULL, '2023-08-01 11:19:09', 'Direction des relations institutionnelles', 'DIRRI', 0),
('30A', 30, '2022-03-30 15:30:28', NULL, NULL, 'Direction de la création artistique et des pratiques culturelles', 'DCAPC', 1),
('30B', 30, '2022-03-30 15:30:28', NULL, NULL, 'Direction des sports, de la jeunesse et de la vie associative', 'DSJVA', 1),
('30G', 30, '2022-05-16 10:35:15', NULL, NULL, 'Direction des antennes régionales et de la relation aux usagers', 'DARRU', 1),
('31A', 31, '2022-03-30 15:30:28', NULL, NULL, 'Direction politiques éducatives', 'DDPE', 1),
('31B', 31, '2022-03-30 15:30:28', NULL, NULL, 'Direction fonctionnement des établissements', 'DFE', 1),
('31C', 31, '2022-03-30 15:30:28', NULL, NULL, 'Mission information et proximité avec les lycées', 'MIPL', 1),
('31D', 31, '2022-03-30 15:30:28', NULL, NULL, 'Direction équipements et patrimoine lycées', 'DEPL', 1),
('31E', 31, '2022-05-16 10:35:15', NULL, NULL, 'Mission Proch\'Orientation', 'MPO', 1),
('31F', 31, '2022-05-16 10:35:15', NULL, NULL, 'Direction de la recherche, de l\'enseignement supérieur et des formations sanitaires et sociales', 'DRESS', 1),
('31G', 31, '2022-05-16 10:35:15', NULL, NULL, 'Direction de l\'apprentissage et de l\'alternance', 'DIRAA', 1),
('32B', 32, '2022-03-30 15:30:28', NULL, NULL, 'Direction de la mer, des ports et du littoral', 'DMPL', 1),
('32E', 32, '2022-05-16 10:35:15', NULL, NULL, 'Direction des services de transport', 'DST', 1),
('32F', 32, '2022-05-16 10:35:15', NULL, NULL, 'Direction des infrastructures de mobilités et du Canal Seine Nord Europe', 'DIMCSNE', 1),
('33A', 33, '2022-03-30 15:30:28', NULL, '2022-12-12 14:38:09', 'Agence Hauts-de-France 2020  2040', 'AHDF', 0),
('33B', 33, '2022-03-30 15:30:28', NULL, NULL, 'Direction de l\'aménagement du territoire et du logement', 'DATL', 1),
('33C', 33, '2022-03-30 15:30:28', '2022-12-12 14:38:09', NULL, 'Direction de l\'eau et la biodiversité', 'DEBIO', 1),
('33E', 33, '2022-03-30 15:30:28', NULL, NULL, 'Direction agriculture et développement rural', 'DADR', 1),
('33F', 33, '2022-03-30 15:30:28', NULL, NULL, 'Direction de la santé', 'DSAN', 1),
('33G', 33, '2022-03-30 15:30:28', NULL, NULL, 'Mission transition numérique', 'MTN', 1),
('33H', 33, '2022-05-16 10:35:15', NULL, NULL, 'Mission ingénierie touristique et attractivité', 'MITA', 1),
('34A', 34, '2022-03-30 15:30:28', NULL, NULL, 'Direction de la formation professionnelle', 'DFP', 1),
('34C', 34, '2022-03-30 15:30:28', NULL, '2022-12-12 14:38:09', 'Direction de l\'appui aux entreprises', 'DAEN', 0),
('34D', 34, '2022-03-30 15:30:28', NULL, '2022-12-12 14:38:09', 'Direction des partenariats économiques, de l\'artisanat et de la pêche', 'DPEAP', 0),
('34G', 34, '2022-03-30 15:30:28', NULL, '2023-08-01 11:19:09', 'Direction troisième révolution industrielle', 'DTRI', 0),
('34H', 34, '2022-03-30 15:30:28', NULL, '2022-12-12 14:38:09', 'Mission \"Hauts-de-France financement\"', 'MHDFF', 0),
('34I', 34, '2022-03-30 15:30:28', '2022-12-12 14:38:09', NULL, 'Direction de l\'emploi', 'DEMP', 1),
('34J', 34, '2022-03-30 15:30:28', NULL, '2023-08-01 11:19:09', 'Direction de l\'innovation et de la performance industrielle', 'DIPI', 0),
('34K', 34, '2022-12-12 14:38:09', NULL, NULL, 'Direction des entreprises', 'DEN', 1),
('34L', 34, '2022-12-12 14:38:09', NULL, NULL, 'Direction de la transformation de l\'économie régionale', 'DTER', 1),
('35A', 35, '2022-03-30 15:30:28', NULL, NULL, 'Direction Europe', 'DEU', 1),
('35B', 35, '2022-03-30 15:30:28', NULL, NULL, 'Direction des relations internationales', 'DRI', 1),
('35C', 35, '2022-03-30 15:30:28', NULL, NULL, 'Mission auprès de l\'Union européenne', 'MUE', 1),
('35D', 35, '2022-12-12 14:38:09', NULL, NULL, 'Agence Hauts-de-France 2020  2040', 'AHDF', 1),
('35E', 35, '2022-12-12 14:38:09', NULL, NULL, 'Direction REV3', 'DREV3', 1),
('40A', 40, '2022-03-30 15:30:28', NULL, NULL, 'Direction des finances', 'DIRFI', 1),
('40B', 40, '2022-03-30 15:30:28', NULL, NULL, 'Direction de l\'achat public', 'DIRAP', 1),
('40C', 40, '2022-03-30 15:30:28', NULL, NULL, 'Direction des affaires juridiques', 'DAJ', 1),
('40D', 40, '2022-03-30 15:30:28', NULL, NULL, 'Direction des systèmes d\'information', 'DSI', 1),
('41A', 41, '2022-03-30 15:30:28', NULL, NULL, 'Direction des moyens institutionnels', 'DMI', 1),
('41B', 41, '2022-03-30 15:30:28', '2022-12-12 14:38:09', NULL, 'Direction du patrimoine et de la sécurité', 'DPS', 1),
('41C', 41, '2022-03-30 15:30:28', NULL, NULL, 'Direction accueil et gestion des manifestations', 'DAGM', 1),
('41D', 41, '2022-03-30 15:30:28', NULL, '2022-12-12 14:38:09', 'Direction de la sécurité, de la sureté et de la gestion des risques', 'DSSGR', 0),
('41E', 41, '2022-05-16 10:35:15', NULL, NULL, 'Mission pôle de conservation BNF', '', 1),
('43A', 43, '2022-05-16 10:35:15', NULL, NULL, 'Direction des ressources humaines', 'DRH', 1),
('43B', 43, '2022-05-16 10:35:15', NULL, '2023-08-01 11:19:09', 'Direction de la cohésion et de la communication interne', 'DCCI', 0),
('43C', 43, '2022-05-16 10:35:15', NULL, NULL, 'Direction de l\'inspection générale', 'DIG', 1),
('43D', 43, '2022-05-16 10:35:15', NULL, NULL, 'Mission fusion et projet d\'administration', 'MFPA', 1),
('43E', 43, '2022-05-16 10:35:15', NULL, NULL, 'Direction qualité et performance', 'DQP', 1),
('43F', 43, '2022-05-16 10:35:15', NULL, NULL, 'Direction de l\'audit', 'DAU', 1),
('43G', 43, '2022-12-12 14:38:09', NULL, NULL, 'Mission évolution sur les méthodes de travail', 'MEMT', 1);

CREATE TABLE `pole` (
`id` int(10) NOT NULL,
`created_at` datetime NOT NULL,
`updated_at` datetime DEFAULT NULL,
`deleted_at` datetime DEFAULT NULL,
`libelle` varchar(128) NOT NULL,
`sigle` varchar(8) NOT NULL,
`actif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `pole` (`id`, `created_at`, `updated_at`, `deleted_at`, `libelle`, `sigle`, `actif`) VALUES
(11, '2022-03-30 15:30:28', NULL, NULL, 'Cabinet', 'CAB', 1),
(30, '2022-03-30 15:30:28', NULL, NULL, 'Pôle Proximité, rayonnement, culture et sport', 'PPRCS', 1),
(31, '2022-03-30 15:30:28', NULL, NULL, 'Pôle Education et avenir des jeunes', 'PEAJ', 1),
(32, '2022-03-30 15:30:28', NULL, NULL, 'Pôle Mobilités, infrastructures et ports', 'PMIP', 1),
(33, '2022-03-30 15:30:28', NULL, NULL, 'Pôle Territoires et transitions', 'PTT', 1),
(34, '2022-03-30 15:30:28', NULL, NULL, 'Pôle Travail : entreprises et emploi', 'PEE', 1),
(35, '2022-03-30 15:30:28', '2022-12-12 14:38:09', NULL, 'Pôle Stratégie régionale', 'PSR', 1),
(40, '2022-03-30 15:30:28', NULL, NULL, 'Pôle Ressources', 'PR', 1),
(41, '2022-03-30 15:30:28', NULL, NULL, 'Pôle Supports techniques', 'PTEC', 1),
(43, '2022-05-16 10:35:15', NULL, NULL, 'Pôle Compétences et accompagnement interne', 'PCAI', 1);

CREATE TABLE `utilisateur` (
`id` int(10) UNSIGNED NOT NULL,
`n1_utilisateur_id` int(10) UNSIGNED DEFAULT NULL,
`direction_id` varchar(3) DEFAULT NULL,
`pole_id` int(10) DEFAULT NULL,
`created_at` datetime NOT NULL,
`updated_at` datetime DEFAULT NULL,
`civilite` varchar(3) DEFAULT NULL,
`nom` varchar(64) NOT NULL,
`prenom` varchar(32) NOT NULL,
`email` varchar(128) NOT NULL,
`login` varchar(128) NOT NULL,
`password` varchar(255) DEFAULT NULL,
`telephone` varchar(15) DEFAULT NULL,
`matricule` varchar(16) DEFAULT NULL,
`role` varchar(32) NOT NULL,
`actif` tinyint(1) NOT NULL DEFAULT 0,
`token` varchar(255) DEFAULT NULL,
`token_date` datetime DEFAULT NULL,
`auth_code` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `utilisateur` (`id`, `n1_utilisateur_id`, `direction_id`, `pole_id`, `created_at`, `updated_at`, `civilite`, `nom`, `prenom`, `email`, `login`, `password`, `telephone`, `matricule`, `role`, `actif`, `token`, `token_date`, `auth_code`) VALUES
(1, NULL, '40D', NULL, '2019-04-12 00:00:00', '2023-09-08 15:55:25', 'M.', 'BOURGEOIS', 'David', 'david.bourgeois@hautsdefrance.fr', 'dbourgeo', NULL, '', '17407W', 'Super administrateur', 1, '41a59389150798c0c33eb175be2f53ab52032df9', '2022-06-09 11:27:38', ''),
(2, NULL, '40D', NULL, '2019-04-16 08:57:23', '2020-04-23 09:04:04', 'M.', 'LOUCHET', 'Olivier', 'olivier.louchet@hautsdefrance.fr', 'olouchet', NULL, '0374276110', NULL, 'Super administrateur', 1, NULL, NULL, ''),
(3, NULL, '40D', NULL, '2020-04-24 11:00:07', '2022-02-17 08:50:43', 'M.', 'COQUET', 'Christophe', 'christophe.coquet@hautsdefrance.fr', 'ccoquet', NULL, '76069', NULL, 'Administrateur', 1, 'ab825a3983db834fa737690e9925943af52a5985', '2023-08-16 10:02:29', ''),
(4, NULL, '40D', NULL, '2020-04-24 11:00:28', '2021-08-03 16:38:34', 'M.', 'DUPUIS', 'Claude', 'claude.dupuis@hautsdefrance.fr', 'cdupuis', NULL, '76095', NULL, 'Administrateur', 1, NULL, NULL, ''),
(5, NULL, '40D', NULL, '2020-10-09 14:48:03', '2021-01-21 14:17:54', 'M.', 'DELISLE', 'Stéphane', 'stephane.delisle@hautsdefrance.fr', 'sdelisle', NULL, '76065', NULL, 'Administrateur', 1, NULL, NULL, ''),
(6, NULL, '40D', NULL, '2021-11-25 15:28:50', '2022-03-30 15:31:26', 'M.', 'VARLET', 'William', 'william.varlet@hautsdefrance.fr', 'wvarlet', NULL, NULL, NULL, 'Administrateur', 1, NULL, NULL, '');


ALTER TABLE `acces`
ADD PRIMARY KEY (`id`),
ADD KEY `fk_acces_utilisateur1_idx` (`utilisateur_id`);

ALTER TABLE `actualite`
ADD PRIMARY KEY (`id`),
ADD KEY `fk_actualite_utilisateur1_idx` (`utilisateur_id`);

ALTER TABLE `direction`
ADD PRIMARY KEY (`id`),
ADD KEY `fk_direction_pole1_idx` (`pole_id`);

ALTER TABLE `pole`
ADD PRIMARY KEY (`id`);

ALTER TABLE `utilisateur`
ADD PRIMARY KEY (`id`),
ADD KEY `direction_id` (`direction_id`),
ADD KEY `pole_id` (`pole_id`),
ADD KEY `n1_utilisateur_id` (`n1_utilisateur_id`);


ALTER TABLE `acces`
MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `actualite`
MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `pole`
MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

ALTER TABLE `utilisateur`
MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;


ALTER TABLE `acces`
ADD CONSTRAINT `fk_acces_utilisateur1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `actualite`
ADD CONSTRAINT `fk_actualite_utilisateur1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `direction`
ADD CONSTRAINT `fk_direction_pole1` FOREIGN KEY (`pole_id`) REFERENCES `pole` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `utilisateur`
ADD CONSTRAINT `utilisateur_ibfk_1` FOREIGN KEY (`direction_id`) REFERENCES `direction` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
ADD CONSTRAINT `utilisateur_ibfk_2` FOREIGN KEY (`pole_id`) REFERENCES `pole` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `utilisateur_ibfk_3` FOREIGN KEY (`n1_utilisateur_id`) REFERENCES `utilisateur` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `direction` CHANGE `pole_id` `pole_id` INT(10) NULL DEFAULT NULL;
INSERT INTO `direction` (`id`, `pole_id`, `created_at`, `updated_at`, `deleted_at`, `libelle`, `sigle`, `actif`) VALUES ('', NULL, '2024-07-31 11:37:00.000000', NULL, NULL, 'Conseil économique, social et environnemental régional', 'CESER', '1');
ALTER TABLE `utilisateur` ADD `fonction` VARCHAR(128) NULL DEFAULT NULL AFTER `matricule`;
ALTER TABLE `utilisateur` ADD `show_menu` TINYINT(1) NULL DEFAULT '1' AFTER `auth_code`;
ALTER TABLE `actualite` ADD `fichier` VARCHAR(128) NULL DEFAULT NULL AFTER `libelle`;
ALTER TABLE `actualite` ADD `updated_at` DATETIME NULL DEFAULT NULL AFTER `created_at`;


SET FOREIGN_KEY_CHECKS=1;
COMMIT;
