#-*- coding:utf-8 -*-
from websocket_server import WebsocketServer
import subprocess
import os
import time
import sys
import json
# Called for every client connecting (after handshake)
def new_client(client, server):
	address="a new client has joined us ip:%s port:%d\n" % (client['address'])
	server.send_message_to_all(address)


# Called for every client disconnecting
def client_left(client, server):
	global f
	print("Client(%d) disconnected" % client['id'])
	f.close()

f1 = file("web/config.json")
s = json.load(f1)
f1.close
cmd_dic=s['servershell']

def message_dispatch(client,message):
	global f
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
		client['account']=message.strip('account:')
		return
	message_dispatch(client,message)


f = open("./websocket_server.log", 'w+')
sys.stdout=f;
PORT=8009
server = WebsocketServer(PORT)
server.set_fn_new_client(new_client)
server.set_fn_client_left(client_left)
server.set_fn_message_received(message_received)
server.run_forever()

