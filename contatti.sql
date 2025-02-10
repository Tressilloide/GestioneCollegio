DROP DATABASE IF EXISTS my_contatti;

CREATE DATABASE my_contatti DEFAULT CHARACTER SET = utf8;

USE my_contatti;

CREATE TABLE tcontatti (
    id_contatti                 BIGINT              NOT NULL    AUTO_INCREMENT,
    nome                        VARCHAR(20)         NOT NULL,
    cognome                     VARCHAR(20)         NOT NULL,
    codice_fiscale              CHAR(16)            NOT NULL    UNIQUE,
    data_nascita                DATE,
    email                       VARCHAR(50)         NOT NULL   UNIQUE,
    user_password                   VARCHAR(255)         NOT NULL, -- sarebbe 60 ma per scalabilità uso 255
    PRIMARY KEY(id_contatti),
    INDEX icontatti (nome, cognome)
) ENGINE = InnoDB;