Websocket Shell
=======================
 web目录可以部署在任意的http服务器上，打开页面的浏览器需要跟server.py运行服务器在一个内网，因为他们需要建立socket链接
 客户端的JavaScript通过websocket跟服务端的python建立链接，发送命令的id，服务端寻找id对应的命令然后执行
 执行的结果实时回显在客户端的页面上

本项目从下面的项目修改而来


https://github.com/Pithikos/python-websocket-server.git


之前一直想让普通用户在未获得linux账号的前提下执行一些linux或mac上的shell


如服务器更新，app发布，配置数据更新等


此前的做法一直是winscp或putty脚本来实现，缺点是不安全，账户和密码都在明文的脚本里边放着

本项目解决了这个问题，普通用户通过点击网页就可以完成执行命令的操作了


用法说明：

1. 	将python-websocket-shell/web目录设置为网站的根目录
2. 	修改web/config.json的列表和账号为你自己的
3. 	修改server.py对应的列表id和脚本路径
4. 	./start.sh 启动websocket的服务端
5. 	修改nginx的配置不允许访问config.json


web目录为root的nginx配置写法如下，location后面的位置是从web的根目录开始的

	location =/config.json{ 
		return 404; 
	} 
	
apache的配置请自行百度

之所以没选择数据库来配置只是为了让网站更容易配置，本项目的初衷就是为了更方便的让普通用户访问脚本

![image](https://github.com/zhulangen/python-websocket-shell/blob/master/shell.jpg)
