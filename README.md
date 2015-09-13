Websocket Shell
=======================
 web目录可以部署在任意的http服务器上
 
 web客户端可以通过注册账号和修改密码修改config.json的账号的信息
 
 账号权限信息和可执行命令由超级用户在管理界面修改
 
 python通过配置文件config.cfg配置的url路径读取config.json的内容，主要用于校验
 
 在python运行期间如果有新注册账户登录就重新读取url内容

 这里解释一下用到的两个bison_key

 config.php用到的key主要用来加密密码和配置数据的接口，python获取数据的时候要用到同样的key
 
 login.php用到的key用来和时间戳生成一个token，JavaScript拿着这个token去和python建立连接，python在校验这个token的合法性时
 需要用到同样的key

 
 因为打开页面的浏览器需要跟server.py运行服务器在一个内网，因为他们需要建立socket链接
 
 客户端的JavaScript通过websocket跟服务端的python建立链接，发送命令的id，服务端寻找id对应的命令然后执行
 
 执行的结果实时回显在客户端的页面上

本项目从下面的项目修改而来
https://github.com/Pithikos/python-websocket-server.git

之前一直想让普通用户在未获得linux账号的前提下执行一些linux或mac上的shell
如服务器更新，app发布，配置数据更新等

此前的做法一直是winscp或putty脚本来实现，缺点是不安全也不够灵活，账户和密码都在明文的脚本里边放着

本项目解决了这个问题，普通用户通过点击网页就可以完成执行命令的操作了
跟jenkins(http://jenkins-ci.org/ )的功能可能有重合，有时间了我去试下，不过这个项目更轻便一些




用法说明：

1. 	将python-websocket-shell/web目录设置为网站的根目录
2. 	删掉没必要的账号，注册新的账号，修改账号权限，数值越小权限越大，0是超级用户可以自由执行命令，不过这个“自由”仍然有限制
3. 	超级用户通过管理页面修改脚本对应的权限值
4. 	修改web/config.json的adrress为server.py监听的ip和端口，JavaScript要根据它与python服务器建立websocket连接
5. 	./start.sh 启动websocket的服务端
6. 	修改nginx的配置不允许访问config.json，现在即使允许访问也看不到密码，为防止暴力破解密码还是不允许的好

启动服务 ./start.sh

停止服务 ./start.sh stop

web目录为root的nginx配置写法如下，location后面的位置是从web的根目录开始的

	location =/config.json{ 
		return 404; 
	} 
	
apache的配置

	<Directory "/Library/WebServer/Documents">
	    Options FollowSymLinks Multiviews
	    MultiviewsMatch Any
	    AllowOverride None
	    Require all granted
	    <Files ~ "\.json$">
	       Order allow,deny
	       Deny from all
	    </Files>
	</Directory>


之所以没选择数据库来配置只是为了让网站更容易配置，本项目的初衷就是为了更方便的让普通用户访问脚本

![image](https://raw.githubusercontent.com/zhulangen/python-websocket-shell/master/shell.jpg)


**Thanks**

https://github.com/Pithikos/python-websocket-server.git
