--
-- Script di creazione della banca dati per la memorizzazione delle istanze software
--

SET FEEDBACK 1
SET NUMWIDTH 10
SET LINESIZE 80
SET TRIMSPOOL ON
SET TAB OFF
SET PAGESIZE 100
SET ECHO OFF

REM ********************************************************************
REM Create the HOSTS table to hold host information for each Virtual/Physical Host

Prompt ******  Creating HOSTS table ....

CREATE TABLE PHPINS.HOSTS ( 
    HOST_ID        	NUMBER NOT NULL,
    HOST_NAME      	VARCHAR2(25) NULL,
    NETWORK_ADDRESS	VARCHAR2(25) NOT NULL,
    CURRENT_DATE   	VARCHAR2(48) NULL,
    PRIMARY KEY(HOST_ID)
);

REM ********************************************************************
REM Create the INSTANCES table to hold instance information for each Software Instance

Prompt ******  Creating GENERIC INSTANCES table ....

CREATE TABLE PHPINS.INSTANCES ( 
    INSTANCE_ID	NUMBER NOT NULL,
    PRIMARY KEY(INSTANCE_ID)
	);

Prompt ******  Creating HOSTS TO INSTANCES table ....

CREATE TABLE PHPINS.INSTANCES_HOSTS ( 
	HOST_ID    	NUMBER(15,5) NULL,
	INSTANCE_ID	NUMBER(15,5) NULL 
	);

ALTER TABLE PHPINS.INSTANCES_HOSTS
	ADD ( CONSTRAINT FK_INSTANCES
	FOREIGN KEY(INSTANCE_ID)
	REFERENCES PHPINS.INSTANCES(INSTANCE_ID)
	ON DELETE CASCADE
	 );

ALTER TABLE PHPINS.INSTANCES_HOSTS
	ADD ( CONSTRAINT FK_HOSTS
	FOREIGN KEY(HOST_ID)
	REFERENCES PHPINS.HOSTS(HOST_ID)
	ON DELETE CASCADE
	 );



-- [initScript:protected] => /etc/init.d/siemens
--                             [initScriptFileContent:protected] => 
--                             [catalinaHome:protected] => /usr/local/siemens
--                             [isRunning:protected] => 1
--                             [existsDeploy:protected] => 1
--                             [javaCmdLine:protected] => nobody   28063  0.0 24.0 724436 368720 ?       Sl   Mar17  52:50 /usr/local/java/bin/java -Djava.util.logging.config.file=/usr/local/siemens/conf/logging.properties -Duser.language=it -Duser.region=IT -Duser.country=IT -server -Xms512m -Xmx512m -Djava.util.logging.manager=org.apache.juli.ClassLoaderLogManager -Djavax.net.ssl.trustStore=/etc/tomcat/truststore.jks -Djava.endorsed.dirs=/usr/local/siemens/common/endorsed -classpath :/usr/local/siemens/bin/bootstrap.jar:/usr/local/siemens/bin/commons-logging-api.jar -Dcatalina.base=/usr/local/siemens -Dcatalina.home=/usr/local/siemens -Djava.io.tmpdir=/usr/local/siemens/temp org.apache.catalina.startup.Bootstrap start
--                             [tcpIpPortsArray:protected] => 8080 
--                             [beInstanceArray:protected] => oracle 10.10.203.8 1521 siemens 
--                             [instanceName:protected] => siemens

Prompt ******  Creating FE_INSTANCES table ....

CREATE TABLE PHPINS.FE_INSTANCES ( 
    INSTANCE_ID  	NUMBER NOT NULL,
    INSTANCE_NAME	VARCHAR2(25) NULL,
    INITSCRIPTFILECONTENT 	VARCHAR2(1024) NULL,
    INITSCRIPT 	VARCHAR2(128) NULL,
    CATALINAHOME	VARCHAR2(125) NULL,
    ISRUNNING	VARCHAR2(25) NULL,
    EXISTSDEPLOY	VARCHAR2(25) NULL,
    JAVACMDLINE	VARCHAR2(1024) NULL,
    TCPIPPORTSARRAY	VARCHAR2(125) NULL,
	BEINSTANCEARRAY	VARCHAR2(125) NULL,
    PRIMARY KEY(INSTANCE_ID)
);

ALTER TABLE "PHPINS"."FE_INSTANCES"
	ADD ( CONSTRAINT "FK_FE_INSTANCES"
	FOREIGN KEY("INSTANCE_ID")
	REFERENCES "PHPINS"."INSTANCES"("INSTANCE_ID")
	ON DELETE CASCADE
	 );



 -- [oracleHome:protected] => /opt/oracle/product/10.2.0/db_1
 --                            [runningAtStartup:protected] => 1
 --                            [existsDatafile:protected] => 
 --                            [isRunning:protected] => 1
 --                            [instanceName:protected] => pmiprd

Prompt ******  Creating BE_INSTANCES table ....

CREATE TABLE PHPINS.BE_INSTANCES ( 
    INSTANCE_ID  	NUMBER NOT NULL,
    INSTANCE_NAME	VARCHAR2(25) NULL,
    ISRUNNING	VARCHAR2(25) NULL,
    RUNNINGATSTARTUP	VARCHAR2(25) NULL,
    EXISTSDATAFILE	VARCHAR2(25) NULL,
    ORACLE_HOME	VARCHAR2(1024) NULL,
    PRIMARY KEY(INSTANCE_ID)
);

ALTER TABLE "PHPINS"."BE_INSTANCES"
	ADD ( CONSTRAINT "FK_BE_INSTANCES"
	FOREIGN KEY("INSTANCE_ID")
	REFERENCES "PHPINS"."INSTANCES"("INSTANCE_ID")
	ON DELETE CASCADE
	 );


CREATE SEQUENCE PHPINS.HOSTS_S
    INCREMENT BY 1
    START WITH 1
    NOMAXVALUE
    NOMINVALUE
    NOCYCLE
    CACHE 5
    ORDER
/

CREATE TRIGGER PHPINS.HOSTS_T BEFORE INSERT ON PHPINS.HOSTS FOR EACH ROW WHEN ( NEW.HOST_ID IS NULL )
BEGIN  SELECT HOSTS_S.NEXTVAL INTO :NEW.HOST_ID FROM DUAL; END;
/

CREATE SEQUENCE PHPINS.INSTANCES_S
    INCREMENT BY 1
    START WITH 1
    NOMAXVALUE
    NOMINVALUE
    NOCYCLE
    CACHE 5
    ORDER
/

CREATE TRIGGER PHPINS.INSTANCES_T BEFORE INSERT ON PHPINS.INSTANCES FOR EACH ROW WHEN ( NEW.INSTANCE_ID IS NULL )
BEGIN  SELECT INSTANCES_S.NEXTVAL INTO :NEW.INSTANCE_ID FROM DUAL; END;
/

Prompt ******  Creating SERVICES table ....


CREATE TABLE PHPINS.SERVICES (
    SERVICE_ID  NUMBER NOT NULL,
    PROXY_ID            NUMBER(15,5) NULL,
    HEADREQUIRED        VARCHAR2(25) NULL,
        ADRESS  VARCHAR2(25) NULL,
        PORT NUMBER(15,5) NULL,
    PRIMARY KEY(SERVICE_ID)
        );

ALTER TABLE PHPINS.SERVICES
        ADD ( CONSTRAINT FK_SERVICES_HOSTS
        FOREIGN KEY(PROXY_ID)
        REFERENCES PHPINS.HOSTS(HOST_ID)
        ON DELETE CASCADE
         );

CREATE SEQUENCE PHPINS.SERVICES_S
    INCREMENT BY 1
    START WITH 1
    NOMAXVALUE
    NOMINVALUE
    NOCYCLE
    CACHE 5
    ORDER
/

CREATE OR REPLACE TRIGGER PHPINS.SERVICES_T BEFORE INSERT ON PHPINS.SERVICES FOR EACH ROW WHEN ( NEW.SERVICE_ID IS NULL )
BEGIN  SELECT SERVICES_S.NEXTVAL INTO :NEW.SERVICE_ID FROM DUAL; END;
/

Prompt ******  QUITTING  ....


Prompt ******  QUITTING  ....
