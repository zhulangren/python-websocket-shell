#!/bin/bash
function check_process
{
	PIDS=`ps -ef |grep -v $$ |grep $1 |grep -v grep | wc -l`
	echo $PIDS

	if [ "$PIDS" = "1" ]; then
		return 0;
	fi
	return 1;
}
check_process "$1"
if [ "$?" = "1" ]; then
        echo '已经在执行了，请等待执行完毕'
	exit 0
fi 

sh $1
