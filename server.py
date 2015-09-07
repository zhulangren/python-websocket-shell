#-*- coding:utf-8 -*-
from websocket_server import WebsocketServer
import subprocess
import os
import time
import sys
import json
import hashlib
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
			logstr="%s account:%s ip:%s cmd:%s\n" %( time.strftime( '%Y-%m-%d %X',time.localtime(time.time())),client['account'],client['address'],message)
			print(logstr)
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
			print("%s Error format \n" %  (time.strftime( '%Y-%m-%d %X',time.localtime(time.time()))))
			return;

		token=tokenstr[3]
		ptime=int(tokenstr[2])
		if(abs( ptime-time.time()) > 300):
			print("%s timeout time1:%d,time2:%d \n" %  (time.strftime( '%Y-%m-%d %X',time.localtime(time.time())) ,ptime,time.time()) )
			return;

		account=tokenstr[1]
		tokenstr="%s%s%s" % (bison_key,tokenstr[1],tokenstr[2])
		m=hashlib.md5()
		m.update(tokenstr)
		ptoken=m.hexdigest()
		print("token:%s,ptoken:%s,time:%d,str:%s\n" % (token,ptoken,time.time(), tokenstr))
		if(ptoken==token):
			client['account']=account
			client['power']=account_dic[account]['power'];
			client['islogin']=True
		return
	if(client['islogin']==False):
		print("%s you must login!!!!\n" % (time.strftime( '%Y-%m-%d %X',time.localtime(time.time()))))
		server.send_message(client,"%s you must login!!!!\n" % (time.strftime( '%Y-%m-%d %X',time.localtime(time.time()))))
		return	
	#print("p1:%d,p2:%d \n" %(get_index_power(int(message)),client['power']))
	#检测账号是否有权限执行这个命令	
	bison_str=message.split('@:')
	if(len(bison_str)<2):
			print("%s Error format %s\n" %  (time.strftime( '%Y-%m-%d %X',time.localtime(time.time())),message))
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
			print("%s don't have the shell %s!!!!\n" % (time.strftime( '%Y-%m-%d %X',time.localtime(time.time())),bison_str[1]))
			server.send_message(client,"%s don't have the shell!!!!\n" % (time.strftime( '%Y-%m-%d %X',time.localtime(time.time()))))
			return

		if shell_power!=client['power']:
			print("%s don't have the power!!!!\n" % (time.strftime( '%Y-%m-%d %X',time.localtime(time.time()))))
			server.send_message(client,"%s don't have the power!!!!\n" % (time.strftime( '%Y-%m-%d %X',time.localtime(time.time()))))
			return
	elif bison_id=='cmd':
		if client['power']!=1:
			print("%s don't have the power!!!!\n" % (time.strftime( '%Y-%m-%d %X',time.localtime(time.time()))))
			server.send_message(client,"%s don't have the power!!!!\n" % (time.strftime( '%Y-%m-%d %X',time.localtime(time.time()))))
			return

#todo 禁止执行rm，mv，delete passwd命令

	message_dispatch(client,bison_cmd,issingle)



PORT=8009
server = WebsocketServer(PORT)
server.set_fn_new_client(new_client)
server.set_fn_client_left(client_left)
server.set_fn_message_received(message_received)
server.run_forever()

