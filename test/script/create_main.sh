#!/bin/bash
#
# Script di creazione dello schema
#

sqlplus system/system@xe @drop_schema.sql

sqlplus system/system@xe @create_schema.sql

exit;