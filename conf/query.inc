<?php

define("QUERY_SYSDATE","select sysdate from dual");
define("QUERY_SYSDATE_TO_CHAR","select to_char (sysdate, 'YYYY-MM-DD HH24:MI:SS') as now from dual");
define("QUERY_SYSTIMESTAMP","select systimestamp from dual");
define("QUERY_USER","select user from dual");

define("QUERY_AFM_USERS","select * from afm_users");
define("QUERY_AFM_ROLES","select * from afm_roles");

define("QUERY_AFM_DOCS","SELECT * FROM afm_docs where table_name= :p1  and field_name= :p2 and pkey_value= :p3 ");
define("QUERY_AFM_DOCVERS","SELECT * FROM afm_docvers where table_name= :p1  and field_name= :p2 and pkey_value= :p3 ");
define("QUERY_AFM_DOCVERSARCH","SELECT * FROM afm_docs where table_name= :p1 and field_name= :p2 and pkey_value= :p3 ");
define("QUERY_COUNT_AFM_DOCVERS","SELECT count(*) FROM afm_docvers  ");
define("QUERY_COUNT_WR","SELECT count(*) FROM wr  ");
define("QUERY_MAX_WRID_FROM_WR","SELECT MAX(wr_id) FROM wr  ");
define("QUERY_WR","SELECT * FROM wr where wr_id = :p1 ");

