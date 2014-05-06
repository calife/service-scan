--
-- DESCRIPTION
-- 
-- Script di creazione dello schema per la banca dati di memorizzazione delle istanze
-- 
-- mercoledì, 30. aprile 2014
--

SET ECHO OFF

SPOOL ./spool.lst

REM =======================================================
REM cleanup section
REM =======================================================
DROP USER phpins CASCADE;

SPOOL OFF;

QUIT;