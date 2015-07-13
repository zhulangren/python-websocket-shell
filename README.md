Websocket Shell
=======================


本项目从下面的项目修改而来


https://github.com/Pithikos/python-websocket-server.git


之前一直想让普通用户在未获得linux账号的前提下执行一些linux或mac上的shell


如服务器更新，app发布，配置数据更新等


此前的做法一直是winscp或putty脚本来实现，缺点是不安全，账户和密码都在明文的脚本里边放着



本项目解决了这个问题，普通用户通过点击网页就可以完成执行命令的操作了


用法说明：
1. 	将python-websocket-shell/web目录设置为网站的根目录


2.	python server.py 启动websocket的服务端

