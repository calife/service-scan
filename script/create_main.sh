#!/bin/bash
#
# Script di creazione dello schema
#

sqlplus system/system@supportosv @drop_schema.sql

sqlplus system/system@supportosv @create_schema.sql

exit;