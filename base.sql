CREATE TABLE utilisateurs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL,
    prenom TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    mot_de_passe TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'OPERATEUR',
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE comptes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    telephone TEXT NOT NULL UNIQUE,
    solde NUMERIC NOT NULL DEFAULT 0
        CHECK (solde >= 0),
    statut TEXT NOT NULL DEFAULT 'ACTIF'
        CHECK (statut IN ('ACTIF', 'BLOQUE', 'FERME')),
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME
)
CREATE TABLE types_operations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL UNIQUE,
    libelle TEXT NOT NULL,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
, `actif` INTEGER NOT NULL DEFAULT 1)
CREATE TABLE baremes_frais (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation_id INTEGER NOT NULL,
    montant_min NUMERIC NOT NULL,
    montant_max NUMERIC,
    frais NUMERIC NOT NULL DEFAULT 0,
    actif INTEGER NOT NULL DEFAULT 1
        CHECK (actif IN (0, 1)),
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, `commission` NUMERIC NOT NULL DEFAULT 0,

    CHECK (montant_min >= 0),
    CHECK (montant_max IS NULL OR montant_max >= montant_min),
    CHECK (frais >= 0),

    FOREIGN KEY (type_operation_id)
        REFERENCES types_operations(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
)
CREATE TABLE `operateurs` (
        `id` INTEGER PRIMARY KEY AUTOINCREMENT,
        `nom` TEXT NOT NULL,
        `code` TEXT NOT NULL UNIQUE,
        `actif` INTEGER NOT NULL DEFAULT 1,
        `date_creation` DATETIME NOT NULL DEFAULT 'CURRENT_TIMESTAMP'
)
CREATE TABLE `prefixes_operateur` (
        `id` INTEGER PRIMARY KEY AUTOINCREMENT,
        `prefixe` TEXT NOT NULL UNIQUE,
        `operateur_id` INTEGER NOT NULL,
        `actif` INTEGER NOT NULL DEFAULT 1,
        `date_creation` DATETIME NOT NULL DEFAULT 'CURRENT_TIMESTAMP',
        CONSTRAINT `prefixes_operateur_operateur_id_foreign` FOREIGN KEY (`operateur_id`) REFERENCES `operateurs`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
)