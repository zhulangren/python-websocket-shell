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

def message_dispatch(client,message):
	try:
		pids= os.popen("ps -ef | grep %s |grep -v grep | awk '{print $2}'" % (cmd_dic[message]))
		pids=pids.read();
		if(pids==""):
			if(cmd_dic.has_key(message)):
				logstr="%s account:%s ip:%s cmd:%s\n" %( time.strftime( '%Y-%m-%d %X',time.localtime(time.time())),client['account'],client['address'],cmd_dic[message])
				print(logstr)
				server.send_message(client,logstr)
				subp=subprocess.Popen(cmd_dic[message],shell=True,stdout=subprocess.PIPE)
				while subp.poll()==None:
				    server.send_message(client,subp.stdout.readline())
			else:
				server.send_message(client,"key don't exist!!!\n")
		else:
			server.send_message(client,"进程已经在运行了，请等待执行完成!!!\n")		
	except Exception as e:
			print("ERROR: message_dispatch: "+str(e))
# Called when a client sends a message
def message_received(client, server, message):
	if len(message) > 200:
		message = message[:200]+'..'
	if(message.startswith('account')):

		bison_key="45a1df1c9e2656e4f4c742cf-4753775d";
		tokenstr=message.split('|')
		token=tokenstr[3]
		account=tokenstr[1]
		tokenstr="%s%s%s" % (bison_key,tokenstr[1],tokenstr[2])
		m=hashlib.md5()
		m.update(tokenstr)
		ptoken=m.hexdigest()
		print("token:%s,ptoken:%s,str:%s\n" % (token,ptoken, tokenstr))
		if(ptoken==token):
			client['account']=account
			client['islogin']=True
		return
	if(client['islogin']==False):
		print("%s you must login!!!!\n" % (time.strftime( '%Y-%m-%d %X',time.localtime(time.time()))))
		server.send_message(client,"%s you must login!!!!\n" % (time.strftime( '%Y-%m-%d %X',time.localtime(time.time()))))
		return	
	message_dispatch(client,message)



PORT=8009
server = WebsocketServer(PORT)
server.set_fn_new_client(new_client)
server.set_fn_client_left(client_left)
server.set_fn_message_received(message_received)
server.run_forever()

