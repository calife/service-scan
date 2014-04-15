#!/bin/bash
# mercoledì, 26. marzo 2014
#

########################################################################
#      --help
#      --action create  --keystore <keystore> --clearfile <clearfile>
#      --action show  --keystore <keystore>
#      --action export  --keystore <keystore> --clearfile <clearfile>
#      --action changekeypasswd   --keystore <keystore>

#      --action addentry  --keystore <keystore> --entry entry
#      --action removeentry  --keystore <keystore> --entry entry
#      --action verify --keystore <keystore>
#      --action sort --keystore <keystore>
#      --action removedup --keystore <keystore>

#      --action delete  --keystore <keystore>
#      --action help
########################################################################

export BASEDIR="/home/mpucci/workspace-php/service-scan/test/conf";
export KEYSTORE="$BASEDIR/keystore.pks";
export CLEARFILE="$BASEDIR/auth.csv";
export CLEARFILE_BKP="$BASEDIR/authBak.csv";
export ENTRYADD="172.16.1.62,22,root,ashssasjsa";
export ENTRYREMOVE="192.168.200.16";

#cat $CLEARFILE;

echo "###############################################";
echo "Test Case 1: ../tool/Keytool.php --action create --keystore $KEYSTORE --clearfile $CLEARFILE ";
../tool/Keytool.php --action create --keystore $KEYSTORE --clearfile $CLEARFILE
../tool/Keytool.php --action show --keystore $KEYSTORE
echo "###############################################";

echo "###############################################";
echo "Test Case 2: ../tool/Keytool.php --action addentry --keystore $KEYSTORE --entry $ENTRYADD";
../tool/Keytool.php --action addentry --keystore $KEYSTORE --entry $ENTRYADD
../tool/Keytool.php --action show --keystore $KEYSTORE
echo "###############################################";

echo "###############################################";
echo "Test Case 3: ../tool/Keytool.php --action removeentry --keystore $KEYSTORE --entry $ENTRYREMOVE";
../tool/Keytool.php --action removeentry --keystore $KEYSTORE --entry $ENTRYREMOVE
../tool/Keytool.php --action show --keystore $KEYSTORE
echo "###############################################";

exit;

echo "###############################################";
echo "Test Case 3: ../tool/Keytool.php --action sort --keystore $KEYSTORE";
../tool/Keytool.php --action sort --keystore $KEYSTORE
../tool/Keytool.php --action show --keystore $KEYSTORE
echo "###############################################";

echo "###############################################"; 
echo "Test Case 3: ../tool/Keytool.php --action removedup --keystore $KEYSTORE";
../tool/Keytool.php --action removedup --keystore $KEYSTORE ../tool/Keytool.php --action show --keystore $KEYSTORE 
echo "###############################################";

exit;

echo "###############################################";
echo "Test Case 3: ../tool/Keytool.php --action verify --keystore $KEYSTORE";
../tool/Keytool.php --action verify --keystore $KEYSTORE
echo "###############################################";

echo "###############################################";
echo "Test Case 3: ../tool/Keytool.php --action removedup --keystore $KEYSTORE";
../tool/Keytool.php --action removedup --keystore $KEYSTORE
echo "###############################################"

echo "###############################################";
echo "Test Case 3: ../tool/Keytool.php --action show --keystore $KEYSTORE";
../tool/Keytool.php --action show --keystore $KEYSTORE
echo "###############################################";

exit;

echo "###############################################";
echo "Test Case: ../tool/Keytool.php --help ";
../tool/Keytool.php --help
echo "###############################################";

echo "###############################################";
echo "Test Case 1: ../tool/Keytool.php --action help ";
../tool/Keytool.php --action help
echo "###############################################";

echo "###############################################";
echo "Test Case 2: ../tool/Keytool.php --action create --keystore $KEYSTORE --clearfile $CLEARFILE ";
../tool/Keytool.php --action create --keystore $KEYSTORE --clearfile $CLEARFILE
echo "###############################################";

echo "###############################################";
echo "Test Case 3: ../tool/Keytool.php --action show --keystore $KEYSTORE";
../tool/Keytool.php --action show --keystore $KEYSTORE
echo "###############################################";

echo "###############################################";
echo "Test Case 4: ../tool/Keytool.php --action export  --keystore $KEYSTORE --clearfile $CLEARFILE_BKP";
../tool/Keytool.php --action export  --keystore $KEYSTORE --clearfile $CLEARFILE_BKP
echo "-----BEGIN CLEARFILE DATA MESSAGE-----";
cat $CLEARFILE_BKP;
echo -en "\n-----END CLEARFILE DATA MESSAGE-----\n";
echo "###############################################"

echo "###############################################";
echo "Test Case 5: ../tool/Keytool.php --action changekeypasswd --keystore <keystore>"
../tool/Keytool.php --action changekeypasswd --keystore $KEYSTORE
echo "###############################################";

echo "###############################################";
echo "Test Case 6: ../tool/Keytool.php --action delete --keystore $KEYSTORE";
../tool/Keytool.php --action delete --keystore $KEYSTORE
echo "###############################################";

exit;
