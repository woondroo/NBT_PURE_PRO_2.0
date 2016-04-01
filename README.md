# NBT PURE PROJECT
Pure front site source code.

### Description

1. 代码部分，你可以先阅读附件中的框架代码，这是一个基础的框架，可以直接配置nginx进行测试，nginx配置如下：
server{
	listen 80;
	server_name www.puretest.com;
	index index.html index.htm index.php default.html default.htm default.php;
	root  /home/wwwroot/puretest;

	location / {
		if (!-e $request_filename){
			rewrite ^/(.*) /index.php last;
		}
	}

	location ~ .*\.(php|php5)?$
	{
		fastcgi_split_path_info ^(.+\.php)(/.+)$;
		fastcgi_pass unix:/var/run/php5-fpm.sock;
		fastcgi_index index.php;
		include fastcgi_params;
	}

	location ~ .*\.(ico|gif|jpg|jpeg|png|bmp|swf)$
	{
		expires      30d;
	}

	location ~ .*\.(js|css)?$
	{
		expires      12h;
	}

	access_log off;
}

将项目直接解压访问配置的地址就能运行起来。

2. 框架大体目录结构如下：
├── api.php (执行定时脚本/后台脚本时的入口文件，已经屏蔽web访问)
	├── css
	├── fonts
	├── images
	├── index.php (web访问的入口文件)
	├── js
	├── protected (核心文件)
	│   ├── config (配置/工具文件)
	│   ├── controllers (控制层)
	│   ├── framework (框架核心代码)
	│   ├── libs (小部件，可重复使用的部件)
	│   ├── models (数据模型/逻辑层)
	│   └── views (视图层)
	└── shell
	    └── test.sh (可执行脚本文件)
