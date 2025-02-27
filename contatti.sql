DROP DATABASE IF EXISTS my_collegio;

CREATE DATABASE my_collegio DEFAULT CHARACTER SET = utf8;

USE my_collegio;

CREATE TABLE tdocente (
    id_contatto                 BIGINT              NOT NULL    AUTO_INCREMENT,
    nome                        VARCHAR(20)         NOT NULL,
    cognome                     VARCHAR(20)         NOT NULL,
    email                       VARCHAR(50)         NOT NULL   UNIQUE,
    user_password               VARCHAR(255)         NOT NULL, -- sarebbe 60 ma per scalabilità uso 255
    PRIMARY KEY(id_contatto),
    INDEX idocente (nome, cognome)
) ENGINE = InnoDB;

CREATE TABLE tcollegiodocenti(
    id_collegio                 BIGINT              NOT NULL    AUTO_INCREMENT,
    data_collegio               DATE                NOT NULL,
    ora_inizio                  TIME                NOT NULL,
    ora_fine                    TIME                NOT NULL,
    descrizione                 VARCHAR(255)        NOT NULL,
    PRIMARY KEY(id_collegio)
) ENGINE = InnoDB;

CREATE TABLE tproposta(
    id_proposta                 BIGINT              NOT NULL    AUTO_INCREMENT,
    descrizione                 VARCHAR(255)        NOT NULL,
    titolo                      VARCHAR(50)         NOT NULL,
    PRIMARY KEY(id_proposta)
) ENGINE = InnoDB;

CREATE TABLE tvotazione(
    id_votazione                BIGINT              NOT NULL    AUTO_INCREMENT,
    descrizione                 VARCHAR(255)        NOT NULL,
    ora_inizio                  TIME                NOT NULL,
    ora_fine                    TIME                NOT NULL,
    id_proposta                 BIGINT              NOT NULL,
    id_contatto                 BIGINT              NOT NULL,
    PRIMARY KEY(id_votazione),
    FOREIGN KEY(id_contatto) REFERENCES tdocente(id_contatto),
    FOREIGN KEY(id_proposta) REFERENCES tproposta(id_proposta)
) ENGINE = InnoDB;

CREATE TABLE partecipa(
    id_collegio                 BIGINT              NOT NULL,
    id_docente                  BIGINT              NOT NULL,
    ora_entrata                 TIME                NOT NULL,
    ora_uscita                  TIME                NOT NULL,
    PRIMARY KEY(id_collegio, id_docente),
    FOREIGN KEY(id_collegio) REFERENCES tcollegiodocenti(id_collegio),
    FOREIGN KEY(id_docente) REFERENCES tdocente(id_contatto)
) ENGINE = InnoDB;

CREATE TABLE effettua(
    id_docente                  BIGINT              NOT NULL,
    id_votazione                BIGINT              NOT NULL,
    voto                        INT                 NOT NULL,
    ora                         TIME                NOT NULL,
    PRIMARY KEY(id_docente, id_votazione),
    FOREIGN KEY(id_docente) REFERENCES tdocente(id_contatto),
    FOREIGN KEY(id_votazione) REFERENCES tvotazione(id_votazione)
) ENGINE = InnoDB;
