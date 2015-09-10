#-*- coding:utf-8 -*-
from websocket_server import WebsocketServer
import subprocess
import os
import time
import sys
import json
import hashlib
import logging
import urllib2
import urllib
import ConfigParser

logging.basicConfig(filename='webshell.log',level=logging.DEBUG, format='%(asctime)s %(message)s', datefmt='%m/%d/%Y %I:%M:%S %p')

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



def get_config_data():
	data = {}
	data['zhulangren'] = 'zhulangren'
	data['time'] = time.time()
	bison_key="2da2d990f2abad8-f0f6d6e46556d7-9ad"
	tokenstr="%s%s%s" % (bison_key,data['time'],data['zhulangren'])
	m=hashlib.md5()
	m.update(tokenstr)
	data['token']=m.hexdigest()
	post_data = urllib.urlencode(data)
	req = urllib2.urlopen(config_url, post_data)
	content = req.read()
	s = json.loads(content)
	if(s['flag']==-1):
		logging.debug("配置数据时间戳超时")
	elif(s['flag']==-2):
		logging.debug("配置数据密码错误")
	else:
		s=s['data']
	return s


#检查超级用户有没有执行非法指令
def check_exclude_cmd(client, cmd):
	for ex in exclude_cmd:
		if(cmd.count(ex) >0):
			logging.debug("非法命令：%s" % ex)
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
			logging.debug(logstr)
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
			logging.debug("Error format")
			return;

		token=tokenstr[3]
		ptime=int(tokenstr[2])
		if(abs( ptime-time.time()) > 300):
			logging.debug("timeout time1:%d,time2:%d" %  (ptime,time.time()) )
			return;

		account=tokenstr[1]
		tokenstr="%s%s%s" % (bison_key,tokenstr[1],tokenstr[2])
		m=hashlib.md5()
		m.update(tokenstr)
		ptoken=m.hexdigest()
		#logging.debug("token:%s,ptoken:%s,time:%d,str:%s\n" % (token,ptoken,time.time(), tokenstr))
		global cmd_dic,account_dic,shell_dic,exclude_cmd
		if(ptoken==token):
			if(account_dic.has_key(account)==False):
				s= get_config_data()
				cmd_dic=s['servershell']
				account_dic=s['account']
				shell_dic=s['shell']
				exclude_cmd=s['exclude_cmd']
				logging.debug("重新获取账号配置数据")
			if(account_dic.has_key(account)==False):
				logging.debug("账号不存在")
				return

			client['account']=account
			client['power']=account_dic[account]['power'];
			client['islogin']=True
		return
	if(client['islogin']==False):
		logging.debug("you must login!!!!")
		return	
	#logging.debug(p2:%d \n" %(get_index_power(int(message)),client['power']))
	#检测账号是否有权限执行这个命令	
	bison_str=message.split('@:')
	if(len(bison_str)<2):
			logging.debug(" Error format %s" %  message)
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
			logging.debug("don't have the shell %s!!!!" % (bison_str[1]))
			return

		if shell_power!=client['power'] and client['power'] !=0:
			logging.debug("don't have the power!!!!")
			return
	elif bison_id=='cmd':
		if client['power']!=0:
			logging.debug("don't have the power %s %d!!!!" % (client['account'],client['power']))
			return


	if (check_exclude_cmd(client, bison_cmd)==False):
		message_dispatch(client,bison_cmd,issingle)
	else:
		logging.debug("命令包含非法字符")
		return

config=ConfigParser.ConfigParser();
config.read('config.cfg')
config_url=config.get("info","url")		
s= get_config_data()
cmd_dic=s['servershell']
account_dic=s['account']
shell_dic=s['shell']
exclude_cmd=s['exclude_cmd']



PORT=8009
server = WebsocketServer(PORT)
server.set_fn_new_client(new_client)
server.set_fn_client_left(client_left)
server.set_fn_message_received(message_received)
server.run_forever()

