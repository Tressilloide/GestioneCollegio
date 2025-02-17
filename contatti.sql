DROP DATABASE IF EXISTS my_contatti;

CREATE DATABASE my_contatti DEFAULT CHARACTER SET = utf8;

USE my_contatti;

CREATE TABLE tcontatto (
    id_contatto                 BIGINT              NOT NULL    AUTO_INCREMENT,
    nome                        VARCHAR(20)         NOT NULL,
    cognome                     VARCHAR(20)         NOT NULL,
    email                       VARCHAR(50)         NOT NULL   UNIQUE,
    user_password                   VARCHAR(255)         NOT NULL, -- sarebbe 60 ma per scalabilità uso 255
    PRIMARY KEY(id_contatto),
    INDEX icontatto (nome, cognome)
) ENGINE = InnoDB;