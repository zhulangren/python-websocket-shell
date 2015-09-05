#!/bin/bash
function check_process()
{
	PIDS=`ps -ef |grep $1 |grep -v grep | awk '{print $2}'`
	if [ "$PIDS" != "" ]; then
		echo 1111111111111111
		return 1;
	fi
	echo 2222222222222222
	return 0;
}


#check_process test
#if [ "$?" = "1" ]; then
#	echo 'dddddddddddddddddddddddddddddd\n'
#
#fi 
