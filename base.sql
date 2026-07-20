CREATE TABLE prefixes_operateur (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe TEXT NOT NULL UNIQUE,
    actif INTEGER NOT NULL DEFAULT 1
        CHECK (actif IN (0, 1)),
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

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
);

CREATE TABLE types_operations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL UNIQUE,
    libelle TEXT NOT NULL,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE baremes_frais (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation_id INTEGER NOT NULL,
    montant_min NUMERIC NOT NULL,
    montant_max NUMERIC,
    frais NUMERIC NOT NULL DEFAULT 0,
    actif INTEGER NOT NULL DEFAULT 1
        CHECK (actif IN (0, 1)),
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CHECK (montant_min >= 0),
    CHECK (montant_max IS NULL OR montant_max >= montant_min),
    CHECK (frais >= 0),

    FOREIGN KEY (type_operation_id)
        REFERENCES types_operations(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

CREATE TABLE operations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    reference TEXT NOT NULL UNIQUE,
    type_operation_id INTEGER NOT NULL,
    compte_source_id INTEGER,
    compte_destination_id INTEGER,

    montant NUMERIC NOT NULL,
    frais NUMERIC NOT NULL DEFAULT 0,
    montant_total NUMERIC NOT NULL,

    statut TEXT NOT NULL DEFAULT 'VALIDEE'
        CHECK (statut IN ('EN_ATTENTE', 'VALIDEE', 'ANNULEE', 'ECHOUEE')),

    motif TEXT,
    date_operation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CHECK (montant > 0),
    CHECK (frais >= 0),
    CHECK (montant_total >= montant),

    FOREIGN KEY (type_operation_id)
        REFERENCES types_operations(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    FOREIGN KEY (compte_source_id)
        REFERENCES comptes(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    FOREIGN KEY (compte_destination_id)
        REFERENCES comptes(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

CREATE TABLE mouvements_comptes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    operation_id INTEGER NOT NULL,
    compte_id INTEGER NOT NULL,
    sens TEXT NOT NULL
        CHECK (sens IN ('CREDIT', 'DEBIT')),
    montant NUMERIC NOT NULL
        CHECK (montant > 0),
    solde_avant NUMERIC NOT NULL,
    solde_apres NUMERIC NOT NULL,
    libelle TEXT,
    date_mouvement DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (operation_id)
        REFERENCES operations(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    FOREIGN KEY (compte_id)
        REFERENCES comptes(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

php spark make:migration PrefixesOperateur
php spark make:migration Comptes
php spark make:migration TypesOperations
php spark make:migration BaremesFrais
php spark make:migration Operations
php spark make:migration MouvementsComptes