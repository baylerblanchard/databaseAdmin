\c db55

-- Create the schema if it does not exist, and set the search_path.
CREATE SCHEMA IF NOT EXISTS rps;
SET SEARCH_PATH TO rps;

-----------------------------------------------------------
-- Begin overall transaction
-----------------------------------------------------------
BEGIN;

    ------------------------------------------------------------------
    -- Drop Tables Block
    -- Drop in reverse order to handle foreign key dependencies.
    ------------------------------------------------------------------
    DROP TABLE IF EXISTS tbl_rnds;
    DROP TABLE IF EXISTS tbl_gms;
    DROP TABLE IF EXISTS tbl_errata;
    DROP TABLE IF EXISTS tbl_players;

    ------------------------------------------------------------------
    -- Create tbl_players Block
    -- Wrap the table creation in a DO block with exception handling.
    ------------------------------------------------------------------
    DO $$ 
    BEGIN
        CREATE TABLE tbl_players (
            fld_p_id_pk CHAR(16),
            fld_p_doc TIMESTAMP DEFAULT NOW(),
            CONSTRAINT players_pk PRIMARY KEY (fld_p_id_pk)
        );
    EXCEPTION WHEN OTHERS THEN
        RAISE EXCEPTION 'P0005: Error creating table tbl_players';
    END $$;

    ------------------------------------------------------------------
    -- Create tbl_gms Block
    -- Wrap the table creation in a DO block with exception handling.
    ------------------------------------------------------------------
    DO $$ 
    BEGIN
        CREATE TABLE tbl_gms (
            fld_game_id INTEGER,
            fld_p1_id CHAR(16) NOT NULL,
            fld_p2_id CHAR(16) NOT NULL,
            fld_doc TIMESTAMP DEFAULT NOW(),
            CONSTRAINT games_pk PRIMARY KEY (fld_game_id),
            CONSTRAINT games_p1_fk FOREIGN KEY (fld_p1_id) REFERENCES tbl_players (fld_p_id_pk),
            CONSTRAINT games_p2_fk FOREIGN KEY (fld_p2_id) REFERENCES tbl_players (fld_p_id_pk),
            CONSTRAINT games_unique_pair UNIQUE (fld_p1_id, fld_p2_id),
            CONSTRAINT games_order_ck CHECK (fld_p1_id < fld_p2_id)
        );
    EXCEPTION WHEN OTHERS THEN
        RAISE EXCEPTION 'P0006: Error creating table tbl_gms';
    END $$;

    ------------------------------------------------------------------
    -- Create tbl_rnds Block
    -- Wrap the table creation in a DO block with exception handling.
    ------------------------------------------------------------------
    DO $$ 
    BEGIN
        CREATE TABLE tbl_rnds (
            fld_round_id SERIAL,
            fld_game_id INTEGER,
            fld_player_1_token CHAR(1),
            fld_player_2_token CHAR(1),
            fld_doc TIMESTAMP DEFAULT NOW(),
            CONSTRAINT rounds_pk PRIMARY KEY (fld_round_id),
            CONSTRAINT rounds_game_fk FOREIGN KEY (fld_game_id) REFERENCES tbl_gms (fld_game_id),
            CONSTRAINT rounds_token_ck1 CHECK (fld_player_1_token IN ('R', 'P', 'S')),
            CONSTRAINT rounds_token_ck2 CHECK (fld_player_2_token IN ('R', 'P', 'S'))
        );
    EXCEPTION WHEN OTHERS THEN
        RAISE EXCEPTION 'P0007: Error creating table tbl_rnds';
    END $$;

    ------------------------------------------------------------------
    -- Create tbl_errata Block
    -- Wrap the table creation in a DO block with exception handling.
    ------------------------------------------------------------------
    DO $$ 
    BEGIN
        CREATE TABLE tbl_errata (
            fld_doc TIMESTAMP DEFAULT NOW(),
            fld_message TEXT,
            CONSTRAINT errata_doc_nn CHECK (fld_doc IS NOT NULL)
        );
    EXCEPTION WHEN OTHERS THEN
        RAISE EXCEPTION 'P0008: Error creating table tbl_errata';
    END $$;

-----------------------------------------------------------
-- Commit the overall transaction
-----------------------------------------------------------
COMMIT;

-- End of file.
