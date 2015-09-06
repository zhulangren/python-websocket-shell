#!/bin/bash
#WORKDIR=$(dirname `readlink -f $0`)
WORKDIR=$(cd "$(dirname "$0")"; pwd)
cd $WORKDIR


appname="server";
function stop()
{
	pid=$(ps aux | grep $WORKDIR | grep $appname | grep -v grep | awk '{print $2}')
	if [ -n "$pid" ] ; then
		kill -9 $pid 
		echo "kill server $pid"
	fi
	pid=$(ps aux | grep $WORKDIR | grep $appname | grep -v grep | awk '{print $2}')
	while [ -n "$pid" ] 
		do
				sleep 1
				pid=$(ps aux | grep $WORKDIR | grep $appname | grep -v grep | awk '{print $2}')
				echo "$WORKDIR/$appname.py still alive "
		done
	echo "$WORKDIR/$appname.py  had been killed"
}

function start()
{
	#首先看下进程是否存在,如果存在不再次启动而是提示先关闭
	pid=$(ps aux | grep $WORKDIR | grep $appname | grep -v grep  | awk '{print $2}')
	if [ -n "$pid" ] ; then
		echo "$WORKDIR/$appname.py still alive please kill it first!!!"
		return 1;
	fi
	#切换到脚本所在目录
	cd $WORKDIR
	rm debug.log
	nohup python -u $WORKDIR/$appname.py > ./debug.log 2>&1 &
	echo "server is start"
}


case "$1" in
		start)

                start;;

        restart)
                stop;
                start;;

        stop)
                stop ;;
		
		help)
				echo "start|restart|stop";;
        *)
                stop;
                start ;;

esac

