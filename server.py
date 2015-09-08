#-*- coding:utf-8 -*-
from websocket_server import WebsocketServer
import subprocess
import os
import time
import sys
import json
import hashlib
default_encoding = 'utf-8'
if sys.getdefaultencoding() != default_encoding:
    reload(sys)
    sys.setdefaultencoding(default_encoding)


# Called for every client connecting (after handshake)
def new_client(client, server):
	address="a new client has joined us ip:%s port:%d\n" % (client['address'])
	server.send_message_to_all(address)


# Called for every client disconnecting
def client_left(client, server):
	print("Client(%d) disconnected" % client['id'])

f1 = file("web/config.json")
s = json.load(f1)
f1.close
cmd_dic=s['servershell']
account_dic=s['account']
shell_dic=s['shell']
exclude_cmd=s['exclude_cmd']



#对log进行简单封装
def bison_log(client,str):
	logstr="%s %s\n" % (time.strftime( '%Y-%m-%d %X',time.localtime(time.time())),str)
	server.send_message(client,logstr)
	print(logstr)

#检查超级用户有没有执行非法指令
def check_exclude_cmd(client, cmd):
	for ex in exclude_cmd:
		if(cmd.count(ex) >0):
			bison_log(client,"非法命令：%s" % ex)
			return True
	return False

#根据账号的id返回账号对应的权限值
def get_index_power(index):
	for k in shell_dic.keys():
		if shell_dic[k]['index'] ==index:
			return shell_dic[k]['power']
	return 0

def message_dispatch(client,message,issingle):
	try:
		pids=""
		if(issingle==True):
			pids= os.popen("ps -ef | grep %s |grep -v grep | awk '{print $2}'" % (message))
			pids=pids.read()
		if(pids=="" or issingle==False):
			logstr="account:%s ip:%s cmd:%s" %(client['account'],client['address'],message)
			bison_log(client,logstr)
			server.send_message(client,logstr)
			subp=subprocess.Popen(message,shell=True,stdout=subprocess.PIPE)
			while subp.poll()==None:
				for line in iter(subp.stdout.readline,''):
			    		server.send_message(client,line)
			

		else:
			server.send_message(client,"进程已经在运行了，请等待执行完成 %s!!!\n" % message)		
	except Exception as e:
			print("ERROR: message_dispatch: "+str(e))
# Called when a client sends a message
def message_received(client, server, message):
	if len(message) > 200:
		message = message[:200]+'..'
	if(message.startswith('account')):
		bison_key="45a1df1c9e2656e4f4c742cf-4753775d";
		tokenstr=message.split('|')
		if(len(tokenstr)<4):
			bison_log(client,"Error format")
			return;

		token=tokenstr[3]
		ptime=int(tokenstr[2])
		if(abs( ptime-time.time()) > 300):
			bison_log(client,"timeout time1:%d,time2:%d" %  (ptime,time.time()) )
			return;

		account=tokenstr[1]
		tokenstr="%s%s%s" % (bison_key,tokenstr[1],tokenstr[2])
		m=hashlib.md5()
		m.update(tokenstr)
		ptoken=m.hexdigest()
		#bison_log(client,"token:%s,ptoken:%s,time:%d,str:%s\n" % (token,ptoken,time.time(), tokenstr))
		if(ptoken==token):
			client['account']=account
			client['power']=account_dic[account]['power'];
			client['islogin']=True
		return
	if(client['islogin']==False):
		bison_log(client,"you must login!!!!")
		return	
	#bison_log("p1:%d,p2:%d \n" %(get_index_power(int(message)),client['power']))
	#检测账号是否有权限执行这个命令	
	bison_str=message.split('@:')
	if(len(bison_str)<2):
			bison_log(client," Error format %s" %  message)
			return;

	bison_id=bison_str[0]
	bison_cmd=bison_str[1]
	issingle=False
	if bison_id=='shell':
		issingle=True
		shell_power=get_index_power(int(bison_cmd))
		if cmd_dic.has_key(bison_cmd):
			bison_cmd=cmd_dic[bison_cmd]
		else:
			bison_log(client,"don't have the shell %s!!!!" % (bison_str[1]))
			return

		if shell_power!=client['power'] and client['power'] !=0:
			bison_log(client,"don't have the power!!!!")
			return
	elif bison_id=='cmd':
		if client['power']!=0:
			bison_log(client,"don't have the power %s %d!!!!" % (client['account'],client['power']))
			return


	if (check_exclude_cmd(client, bison_cmd)==False):
		message_dispatch(client,bison_cmd,issingle)
	else:
		bison_log(client,"命令包含非法字符")
		return


PORT=8009
server = WebsocketServer(PORT)
server.set_fn_new_client(new_client)
server.set_fn_client_left(client_left)
server.set_fn_message_received(message_received)
server.run_forever()

