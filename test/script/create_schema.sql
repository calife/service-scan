--
-- DESCRIPTION
-- 
-- Script di creazione dello schema per la banca dati di memorizzazione delle istanze
-- 
-- mercoledì, 30. aprile 2014
--

SET ECHO OFF

PROMPT
PROMPT Definire la password per lo user phpins :
DEFINE pass     = &1
PROMPT

SPOOL ./spool.lst

REM =======================================================
REM create user phpins
REM =======================================================
CREATE USER phpins IDENTIFIED BY "&PASS";

REM =======================================================
REM grants from phpins schema
REM =======================================================
GRANT CONNECT, RESOURCE TO phpins;
GRANT AQ_ADMINISTRATOR_ROLE, AQ_USER_ROLE TO phpins;
GRANT EXECUTE ON DBMS_AQ TO phpins;
GRANT CREATE TYPE TO phpins;

REM =======================================================
REM create phpins schema objects
REM =======================================================

--
-- create tables, sequences and constraint
--

@@phpins_cre.sql

--
-- populate tables
--

-- @?/demo/schema/human_resources/phpins_popul

--
-- create indexes
--

-- @?/demo/schema/human_resources/phpins_idx

--
-- create procedural objects
--

-- @?/demo/schema/human_resources/phpins_code

--
-- add comments to tables and columns
--

-- @?/demo/schema/human_resources/phpins_comnt


SPOOL OFF;

QUIT;