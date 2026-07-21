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

ALTER TABLE comptes
ADD COLUMN pourcentage_epargne NUMERIC;

CREATE TABLE epargne (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    compte_id INTEGER UNIQUE,
    solde NUMERIC NOT NULL DEFAULT 0
    CHECK (solde >=0)
);
CREATE TABLE types_operations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL UNIQUE,
    libelle TEXT NOT NULL,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
, `actif` INTEGER NOT NULL DEFAULT 1);
CREATE TABLE baremes_frais (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation_id INTEGER NOT NULL,
    montant_min NUMERIC NOT NULL,
    montant_max NUMERIC,
    commission NUMERIC,
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
);

CREATE TABLE `operateurs` (
        `id` INTEGER PRIMARY KEY AUTOINCREMENT,
        `nom` TEXT NOT NULL,
        `code` TEXT NOT NULL UNIQUE,
        `actif` INTEGER NOT NULL DEFAULT 1,
        `date_creation` DATETIME NOT NULL DEFAULT 'CURRENT_TIMESTAMP'
);
CREATE TABLE `prefixes_operateur` (
        `id` INTEGER PRIMARY KEY AUTOINCREMENT,
        `prefixe` TEXT NOT NULL UNIQUE,
        `operateur_id` INTEGER NOT NULL,
        `actif` INTEGER NOT NULL DEFAULT 1,
        `date_creation` DATETIME NOT NULL DEFAULT 'CURRENT_TIMESTAMP',
        CONSTRAINT `prefixes_operateur_operateur_id_foreign` FOREIGN KEY (`operateur_id`) REFERENCES `operateurs`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE remise (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    pourcentage NUMERIC NOT NULL,
    date_creation DATETIME NOT NULL DEFAULT 'CURRENT_TIMESTAMP',
    actif INTEGER NOT NULL DEFAULT 1
);

INSERT INTO remise (pourcentage) values (5);

CREATE TABLE operations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,

    reference TEXT NOT NULL UNIQUE,

    type_operation_id INTEGER NOT NULL,

    compte_source_id INTEGER,

    telephone_destinataire TEXT,

    montant NUMERIC NOT NULL,

    frais NUMERIC NOT NULL DEFAULT 0,

    montant_total NUMERIC NOT NULL,

    statut TEXT NOT NULL DEFAULT 'VALIDEE'
        CHECK (
            statut IN (
                'EN_ATTENTE',
                'VALIDEE',
                'ANNULEE',
                'ECHOUEE'
            )
        ),

    motif TEXT,

    date_operation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    date_modification DATETIME,

    FOREIGN KEY (type_operation_id)
        REFERENCES types_operations(id),

    FOREIGN KEY (compte_source_id)
        REFERENCES comptes(id)
);

INSERT INTO operateurs (id,nom,code,actif,date_creation) VALUES
(1,'Yas Madagascar','YAS',1,CURRENT_TIMESTAMP),
(2,'Orange Madagascar','ORANGE',1,CURRENT_TIMESTAMP),
(3,'Airtel Madagascar','AIRTEL',1,CURRENT_TIMESTAMP);

INSERT INTO baremes_frais
(
    type_operation_id,
    montant_min,
    montant_max,
    commission,
    frais,
    actif
)
VALUES
(1,        1001,      5000,      0,   150,   1),
(1,        5001,      10000,     0,   275,   1),
(1,       10001,      20000,     0,   550,   1),
(1,       20001,      25000,     0,   650,   1),
(1,       25001,      50000,     0,   1300,  1),
(1,       50001,      100000,    0,   1900,  1),
(1,      100001,      250000,    0,   3400,  1),
(1,      250001,      500000,    0,   4700,  1),
(1,      500001,     1000000,    0,   8800,  1),
(1,     1000001,     2000000,    0,   14700, 1),
(1,     2000001,     3000000,    0,   19600, 1),
(1,     3000001,     4000000,    0,   24500, 1),
(1,     4000001,     5000000,    0,   29400, 1),
(1,     5000001,     6000000,    0,   34300, 1),
(1,     6000001,     7000000,    0,   39200, 1),
(1,     7000001,     8000000,    0,   44100, 1),
(1,     8000001,     9000000,    0,   49000, 1),
(1,     9000001,    10000000,    0,   53900, 1),
(1,    10000001,    11000000,    0,   59000, 1),
(1,    11000001,    12000000,    0,   64000, 1),
(1,    12000001,    13000000,    0,   69000, 1),
(1,    13000001,    14000000,    0,   74000, 1),
(1,    14000001,    15000000,    0,   79000, 1),
(1,    15000001,    16000000,    0,   84000, 1),
(1,    16000001,    17000000,    0,   89000, 1),
(1,    17000001,    18000000,    0,   94000, 1);

INSERT INTO baremes_frais
(
    type_operation_id,
    montant_min,
    montant_max,
    commission,
    frais,
    actif
)
VALUES
(2,       100,      1000,      20.00,  70,    1),
(2,      1001,      5000,       5.00,  70,    1),
(2,      5001,     10000,       5.00, 150,    1),
(2,     10001,     25000,       4.00, 250,    1),
(2,     25001,     50000,       3.00, 500,    1),
(2,     50001,    100000,       2.00,1000,    1),
(2,    100001,    250000,       1.40,1900,    1),
(2,    250001,    500000,       1.00,1900,    1),
(2,    500001,   1000000,       0.85,3200,    1),
(2,   1000001,   2000000,       0.60,3800,    1),
(2,   2000001,   3000000,       0.48,5000,    1),
(2,   3000001,   4000000,       0.49,6300,    1),
(2,   4000001,   5000000,       0.48,7500,    1),
(2,   5000001,   6000000,       0.45,9400,    1),
(2,   6000001,   7000000,       0.43,10700,   1),
(2,   7000001,   8000000,       0.41,12500,   1),
(2,   8000001,   9000000,       0.39,14400,   1),
(2,   9000001,  10000000,       0.37,15700,   1),
(2,  10000001,  11000000,       0.35,17500,   1),
(2,  11000001,  12000000,       0.34,18800,   1),
(2,  12000001,  13000000,       0.33,20000,   1),
(2,  13000001,  14000000,       0.32,21300,   1),
(2,  14000001,  15000000,       0.31,23200,   1),
(2,  15000001,  16000000,       0.30,25000,   1),
(2,  16000001,  17000000,       0.29,26300,   1),
(2,  17000001,  18000000,       0.28,28200,   1);

INSERT INTO comptes (telephone, solde, statut, date_creation)
VALUES
('0321000001', 2500000, 'ACTIF',  '2026-01-02 08:15:00'),
('0321000002', 1800000, 'ACTIF',  '2026-01-03 09:10:00'),
('0321000003', 5200000, 'ACTIF',  '2026-01-04 10:20:00'),
('0321000004', 760000,  'ACTIF',  '2026-01-05 11:30:00'),
('0321000005', 9800000, 'ACTIF',  '2026-01-06 08:45:00'),
('0321000006', 3400000, 'ACTIF',  '2026-01-07 09:20:00'),
('0321000007', 1200000, 'ACTIF',  '2026-01-08 13:00:00'),
('0321000008', 6500000, 'ACTIF',  '2026-01-09 14:30:00'),
('0321000009', 800000,  'ACTIF',  '2026-01-10 15:10:00'),
('0321000010', 4300000, 'BLOQUE', '2026-01-11 09:00:00'),
('0321000011', 250000,  'ACTIF',  '2026-01-12 08:00:00'),
('0321000012', 1500000, 'ACTIF',  '2026-01-13 10:00:00'),
('0321000013', 3700000, 'ACTIF',  '2026-01-14 12:15:00'),
('0321000014', 8900000, 'ACTIF',  '2026-01-15 14:00:00'),
('0321000015', 600000,  'ACTIF',  '2026-01-16 08:20:00'),
('0321000016', 2400000, 'ACTIF',  '2026-01-17 09:50:00'),
('0321000017', 7100000, 'ACTIF',  '2026-01-18 11:30:00'),
('0321000018', 5300000, 'ACTIF',  '2026-01-19 15:40:00'),
('0321000019', 920000,  'ACTIF',  '2026-01-20 08:35:00'),
('0321000020', 10000000,'BLOQUE', '2026-01-21 09:15:00'),
('0321000021', 2200000, 'ACTIF',  '2026-01-22 10:00:00'),
('0321000022', 4800000, 'ACTIF',  '2026-01-23 11:10:00'),
('0321000023', 1300000, 'ACTIF',  '2026-01-24 13:40:00'),
('0321000024', 7800000, 'ACTIF',  '2026-01-25 15:00:00'),
('0321000025', 3600000, 'ACTIF',  '2026-01-26 16:20:00'),

('0372000001', 1700000, 'ACTIF',  '2026-01-27 08:30:00'),
('0372000002', 2500000, 'ACTIF',  '2026-01-28 09:00:00'),
('0372000003', 4300000, 'ACTIF',  '2026-01-29 10:45:00'),
('0372000004', 980000,  'ACTIF',  '2026-01-30 11:20:00'),
('0372000005', 8600000, 'ACTIF',  '2026-01-31 13:00:00'),
('0372000006', 5200000, 'ACTIF',  '2026-02-01 09:10:00'),
('0372000007', 2400000, 'ACTIF',  '2026-02-02 10:30:00'),
('0372000008', 700000,  'ACTIF',  '2026-02-03 11:50:00'),
('0372000009', 9400000, 'ACTIF',  '2026-02-04 14:00:00'),
('0372000010', 3800000, 'BLOQUE', '2026-02-05 15:20:00'),
('0372000011', 1100000, 'ACTIF',  '2026-02-06 08:40:00'),
('0372000012', 2600000, 'ACTIF',  '2026-02-07 09:30:00'),
('0372000013', 6900000, 'ACTIF',  '2026-02-08 10:50:00'),
('0372000014', 4700000, 'ACTIF',  '2026-02-09 12:00:00'),
('0372000015', 820000,  'ACTIF',  '2026-02-10 13:30:00'),
('0372000016', 5600000, 'ACTIF',  '2026-02-11 14:40:00'),
('0372000017', 3100000, 'ACTIF',  '2026-02-12 08:20:00'),
('0372000018', 9100000, 'ACTIF',  '2026-02-13 09:50:00'),
('0372000019', 1450000, 'ACTIF',  '2026-02-14 11:30:00'),
('0372000020', 6700000, 'BLOQUE', '2026-02-15 15:10:00'),
('0372000021', 2900000, 'ACTIF',  '2026-02-16 08:15:00'),
('0372000022', 5400000, 'ACTIF',  '2026-02-17 09:40:00'),
('0372000023', 1250000, 'ACTIF',  '2026-02-18 10:20:00'),
('0372000024', 7300000, 'ACTIF',  '2026-02-19 13:45:00'),
('0372000025', 4100000, 'ACTIF',  '2026-02-20 15:30:00');

PRAGMA foreign_keys = ON;

BEGIN TRANSACTION;

-- Facultatif : supprimer les anciennes opérations
DELETE FROM operations;

DELETE FROM sqlite_sequence
WHERE name = 'operations';

-- =========================================================
-- 1. INSERTION DE 150 RETRAITS
-- =========================================================

WITH RECURSIVE sequence(n) AS (
    SELECT 1

    UNION ALL

    SELECT n + 1
    FROM sequence
    WHERE n < 150
),

comptes_actifs AS (
    SELECT
        id,
        telephone,

        ROW_NUMBER() OVER (
            ORDER BY id
        ) AS numero_ligne,

        COUNT(*) OVER () AS nombre_comptes

    FROM comptes

    WHERE statut = 'ACTIF'
      AND (
          telephone LIKE '032%'
          OR telephone LIKE '037%'
      )
),

donnees_retraits AS (
    SELECT
        s.n,

        c.id AS compte_source_id,

        CASE ((s.n - 1) % 15)
            WHEN 0  THEN 3000
            WHEN 1  THEN 7500
            WHEN 2  THEN 15000
            WHEN 3  THEN 23000
            WHEN 4  THEN 40000
            WHEN 5  THEN 80000
            WHEN 6  THEN 180000
            WHEN 7  THEN 350000
            WHEN 8  THEN 750000
            WHEN 9  THEN 1500000
            WHEN 10 THEN 2500000
            WHEN 11 THEN 3500000
            WHEN 12 THEN 4500000
            WHEN 13 THEN 5500000
            ELSE 7500000
        END AS montant,

        CASE
            WHEN s.n % 41 = 0 THEN 'ANNULEE'
            WHEN s.n % 47 = 0 THEN 'ECHOUEE'
            WHEN s.n % 37 = 0 THEN 'EN_ATTENTE'
            ELSE 'VALIDEE'
        END AS statut,

        datetime(
            '2026-01-01 08:00:00',
            '+' || ((s.n * 11) % 200) || ' days',
            '+' || ((s.n * 29) % 720) || ' minutes'
        ) AS date_operation

    FROM sequence s

    INNER JOIN comptes_actifs c
        ON c.numero_ligne = (
            ((s.n - 1) % c.nombre_comptes) + 1
        )
),

retraits_avec_frais AS (
    SELECT
        d.*,

        COALESCE(
            (
                SELECT b.frais
                FROM baremes_frais b

                WHERE b.type_operation_id = 1
                  AND b.actif = 1
                  AND d.montant >= b.montant_min
                  AND (
                      b.montant_max IS NULL
                      OR d.montant <= b.montant_max
                  )

                ORDER BY b.montant_min DESC
                LIMIT 1
            ),
            0
        ) AS frais

    FROM donnees_retraits d
)

INSERT INTO operations (
    reference,
    type_operation_id,
    compte_source_id,
    telephone_destinataire,
    montant,
    frais,
    montant_total,
    statut,
    motif,
    date_operation,
    date_creation,
    date_modification
)
SELECT
    'RET-2026-' || printf('%05d', n),

    1,

    compte_source_id,

    NULL,

    montant,

    frais,

    montant + frais,

    statut,

    CASE statut
        WHEN 'VALIDEE' THEN 'Retrait effectué avec succès'
        WHEN 'ANNULEE' THEN 'Retrait annulé par le client'
        WHEN 'ECHOUEE' THEN 'Échec du retrait'
        ELSE 'Retrait en cours de traitement'
    END,

    date_operation,

    date_operation,

    CASE
        WHEN statut IN ('ANNULEE', 'ECHOUEE') THEN
            datetime(date_operation, '+15 minutes')
        ELSE NULL
    END

FROM retraits_avec_frais;

-- =========================================================
-- 2. INSERTION DE 350 TRANSFERTS
--
-- Répartition :
-- 20 % vers Orange 032
-- 20 % vers Orange 037
-- 30 % vers Telma 034
-- 30 % vers Airtel 033
-- =========================================================

WITH RECURSIVE sequence(n) AS (
    SELECT 1

    UNION ALL

    SELECT n + 1
    FROM sequence
    WHERE n < 350
),

comptes_actifs AS (
    SELECT
        id,
        telephone,

        ROW_NUMBER() OVER (
            ORDER BY id
        ) AS numero_ligne,

        COUNT(*) OVER () AS nombre_comptes

    FROM comptes

    WHERE statut = 'ACTIF'
      AND (
          telephone LIKE '032%'
          OR telephone LIKE '037%'
      )
),

donnees_transferts AS (
    SELECT
        s.n,

        c.id AS compte_source_id,

        c.telephone AS telephone_source,

        CASE (s.n % 10)

            -- 20 % vers Orange 032
            WHEN 0 THEN
                '032' || printf(
                    '%07d',
                    3000000 + s.n
                )

            WHEN 1 THEN
                '032' || printf(
                    '%07d',
                    4000000 + s.n
                )

            -- 20 % vers Orange 037
            WHEN 2 THEN
                '037' || printf(
                    '%07d',
                    3000000 + s.n
                )

            WHEN 3 THEN
                '037' || printf(
                    '%07d',
                    4000000 + s.n
                )

            -- 30 % vers Telma 034
            WHEN 4 THEN
                '034' || printf(
                    '%07d',
                    2000000 + s.n
                )

            WHEN 5 THEN
                '034' || printf(
                    '%07d',
                    3000000 + s.n
                )

            WHEN 6 THEN
                '034' || printf(
                    '%07d',
                    4000000 + s.n
                )

            -- 30 % vers Airtel 033
            WHEN 7 THEN
                '033' || printf(
                    '%07d',
                    2000000 + s.n
                )

            WHEN 8 THEN
                '033' || printf(
                    '%07d',
                    3000000 + s.n
                )

            ELSE
                '033' || printf(
                    '%07d',
                    4000000 + s.n
                )

        END AS telephone_destinataire,

        CASE ((s.n - 1) % 18)
            WHEN 0  THEN 500
            WHEN 1  THEN 3000
            WHEN 2  THEN 7500
            WHEN 3  THEN 18000
            WHEN 4  THEN 40000
            WHEN 5  THEN 80000
            WHEN 6  THEN 180000
            WHEN 7  THEN 350000
            WHEN 8  THEN 750000
            WHEN 9  THEN 1500000
            WHEN 10 THEN 2500000
            WHEN 11 THEN 3500000
            WHEN 12 THEN 4500000
            WHEN 13 THEN 5500000
            WHEN 14 THEN 6500000
            WHEN 15 THEN 8500000
            WHEN 16 THEN 10500000
            ELSE 12500000
        END AS montant,

        CASE
            WHEN s.n % 43 = 0 THEN 'ANNULEE'
            WHEN s.n % 53 = 0 THEN 'ECHOUEE'
            WHEN s.n % 37 = 0 THEN 'EN_ATTENTE'
            ELSE 'VALIDEE'
        END AS statut,

        datetime(
            '2026-01-01 07:30:00',
            '+' || ((s.n * 13) % 200) || ' days',
            '+' || ((s.n * 23) % 780) || ' minutes'
        ) AS date_operation

    FROM sequence s

    INNER JOIN comptes_actifs c
        ON c.numero_ligne = (
            ((s.n - 1) % c.nombre_comptes) + 1
        )
),

transferts_avec_frais AS (
    SELECT
        d.*,

        COALESCE(
            (
                SELECT b.frais
                FROM baremes_frais b

                WHERE b.type_operation_id = 2
                  AND b.actif = 1
                  AND d.montant >= b.montant_min
                  AND (
                      b.montant_max IS NULL
                      OR d.montant <= b.montant_max
                  )

                ORDER BY b.montant_min DESC
                LIMIT 1
            ),
            0
        ) AS frais

    FROM donnees_transferts d
)

INSERT INTO operations (
    reference,
    type_operation_id,
    compte_source_id,
    telephone_destinataire,
    montant,
    frais,
    montant_total,
    statut,
    motif,
    date_operation,
    date_creation,
    date_modification
)
SELECT
    'TRF-2026-' || printf('%05d', n),

    2,

    compte_source_id,

    telephone_destinataire,

    montant,

    frais,

    montant + frais,

    statut,

    CASE
        WHEN telephone_destinataire LIKE '034%' THEN
            'Transfert vers Telma'

        WHEN telephone_destinataire LIKE '033%' THEN
            'Transfert vers Airtel'

        WHEN telephone_destinataire LIKE '032%'
          OR telephone_destinataire LIKE '037%' THEN
            'Transfert vers Orange'

        ELSE
            'Transfert mobile money'
    END,

    date_operation,

    date_operation,

    CASE
        WHEN statut IN ('ANNULEE', 'ECHOUEE') THEN
            datetime(date_operation, '+10 minutes')
        ELSE NULL
    END

FROM transferts_avec_frais;

COMMIT;