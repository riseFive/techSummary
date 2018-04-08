基本命令：
-
Nginx 启动之后，可以使用以下命令控制:

```
nginx -s <signal>
```
其中-s意思是向主进程发送信号，signal可以为以下四个中的一个:
>1.`stop` — 快速关闭  
2.`quit` — 优雅关闭  
3.`reload` — 重新加载配置文件  
4.`reopen` — 重新打开日志文件  

当运行`nginx -s quit`时，`Nginx` 会等待工作进程处理完成当前请求，然后将其关闭。当你修改配置文件后，并不会立即生效，而是等待重启或者收到`nginx -s reload`信号。

当 `Nginx` 收到 `nginx -s reload` 信号后，首先检查配置文件的语法。语法正确后，主线程会开启新的工作线程并向旧的工作线程发送关闭信号，如果语法不正确，则主线程回滚变化并继续使用旧的配置。当工作进程收到主进程的关闭信号后，会在处理完当前请求之后退出。

一份完整的配置：
-
```apacheconfig
#定义 Nginx 运行的用户和用户组,默认由 nobody 账号运行。 
user  nobody;
#nginx进程数，建议设置为等于CPU总核心数。可以和worker_cpu_affinity配合
worker_processes  1;
#全局错误日志定义类型，[ debug | info | notice | warn | error | crit ]
#error_log  logs/error.log;
#error_log  logs/error.log  notice;
#error_log  logs/error.log  info;
#进程文件
#pid        logs/nginx.pid;
# 一个nginx进程打开的最多文件描述符(句柄)数目，理论值应该是最多打开文件数（系统的值ulimit -n）与nginx进程数相除，
# 但是nginx分配请求并不均匀，所以建议与ulimit -n的值保持一致。
worker_rlimit_nofile 65535;
#工作模式与连接数上限

events 
{  
# 参考事件模型，use [ kqueue | rtsig | epoll | /dev/poll | select | poll ];   
# epoll模型是Linux 2.6以上版本内核中的高性能网络I/O模型，如果跑在FreeBSD上面，就用kqueue模型。
#use epoll; 
#connections 20000;  # 每个进程允许的最多连接数
   
# 单个进程最大连接数（最大连接数=连接数*进程数）该值受系统进程最大打开文件数限制，需要使用命令ulimit -n 查看当前设置
 worker_connections 65535;

}

#设定http服务器

http {
  
#文件扩展名与文件类型映射表   
#include是个主模块指令，可以将配置文件拆分并引用，可以减少主配置文件的复杂度
include       mime.types;   
#默认文件类型
default_type  application/octet-strean; 
#charset utf-8; #默认编码
  
#定义虚拟主机日志的格式

    
#log_format  main  '$remote_addr - $remote_user [$time_local] "$request" '    
#                  '$status $body_bytes_sent "$http_referer" '   
#                  '"$http_user_agent" "$http_x_forwarded_for"';
    
#定义虚拟主机访问日志  
#access_log  logs/access.log  main;
   
#开启高效文件传输模式，sendfile指令指定nginx是否调用sendfile函数来输出文件，对于普通应用设为 on，如果用来进行下载等应用磁盘IO重负载应用，可设置为off，以平衡磁盘与网络I/O处理速度，降低系统的负载。注意：如果图片显示不正常把这个改成off。
sendfile        on;  
#autoindex on; #开启目录列表访问，合适下载服务器，默认关闭。
    
#防止网络阻塞  
#tcp_nopush     on;
    
#长连接超时时间，单位是秒，默认为0
keepalive_timeout  65;


    
# gzip压缩功能设置
 
#开启gzip压缩输出
gzip on;

#最小压缩文件大小
gzip_min_length 1k;
 
#压缩缓冲区
gzip_buffers    4 16k;
 

#压缩版本（默认1.1，前端如果是squid2.5请使用1.0）
gzip_http_version 1.0;
 
#压缩等级
gzip_comp_level 6;
 
   
#压缩类型，默认就已经包含text/html，所以下面就不用再写了，写上去也不会有问题，但是会有一个warn。

gzip_types text/plain text/css text/javascript application/json application/javascript application/x-javascript application/xml;

#和http头有关系，加个vary头，给代理服务器用的，有的浏览器支持压缩，有的不支持，所以避免浪费不支持的也压缩，所以根据客户端的HTTP头来判断，是否需要压缩
gzip_vary on;
   
#limit_zone crawler $binary_remote_addr 10m; #开启限制IP连接数的时候需要使用


    
# http_proxy服务全局设置

client_max_body_size   10m;
client_body_buffer_size   128k;
proxy_connect_timeout   75;
proxy_send_timeout   75;
proxy_read_timeout   75;
proxy_buffer_size   4k;
proxy_buffers   4 32k;
proxy_busy_buffers_size   64k;
proxy_temp_file_write_size  64k;
proxy_temp_path   /usr/local/nginx/proxy_temp 12;   
# 设定负载均衡后台服务器列表 
  upstream  backend.com {
 #ip_hash; # 指定支持的调度算法        
# upstream 的负载均衡，weight 是权重，可以根据机器配置定义权重。weigth 参数表示权值，权值越高被分配到的几率越大。
 server   192.168.10.100:8080 max_fails=2 fail_timeout=30s;
 server   192.168.10.101:8080 max_fails=2 fail_timeout=30s;  
}


    
#虚拟主机的配置
server{       
#监听端口
listen      80;       
#域名可以有多个，用空格隔开
server_name  localhost fontend.com;        
# Server Side Include，通常称为服务器端嵌入      
#ssi on;        
#默认编码       
#charset utf-8;      
#定义本虚拟主机的访问日志       
#access_log  logs/host.access.log  main;       
# 因为所有的地址都以 / 开头，所以这条规则将匹配到所有请求
location /{
  root   html;
  index  index.html index.htm;       
}        
#error_page  404              /404.html;
      
# redirect server error pages to the static page /50x.html       
#

error_page   500 502 503 504 /50x.html;

location = /50x.html {
            root   html;
}
     
# 图片缓存时间设置
location ~ .*.(gif|jpg|jpeg|png|bmp|swf|)${
         expires 10d;      
}
       
# JS和CSS缓存时间设置
 location ~ .*.(js|css)?${
          expires 1h;      
}
       
#代理配置
        
# proxy the PHP scripts to Apache listening on 127.0.0.1:80     
#location /proxy/ {        
#    proxy_pass   http://127.0.0.1;        
#}


        
# pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
        
#location ~ .php$ {      
#    root           html;        
#    fastcgi_pass   127.0.0.1:9000;        
#    fastcgi_index  index.php;       
#    fastcgi_param  SCRIPT_FILENAME  /scripts$fastcgi_script_name;        
#    include        fastcgi_params;        
#}
        
# deny access to .htaccess files, if Apache's document root       
# concurs with nginx's one        
#        
#location ~ /.ht {        
#    deny  all;        
#}
   
}  

}
```

如何配置location
-
location 指令接受两种类型的参数：
>1.前缀字符串（路径名称）  
>2.正则表达式

对于前缀字符串参数， `URIs` 必须严格的以它开头。例如对于 `/some/path/` 参数，可以匹配` /some/path/document.html `，但是不匹配 `/my-site/some/path`，因为` /my-site/some/path` 不以 `/some/path/` 开头。
```
location /some/path/ {
    ...
}
```
对于正则表达式，以 `~` 开头表示大小写敏感，以 `~*` 开头表示大小写不敏感。注意路径中的 `.` 要写成` \. `。例如一个匹配以 `.html` 或者` .htm `结尾的` URI` 的 `location`：
```apacheconfig
location ~ \.html? {
    ...
}
```
正则表达式的优先级大于前缀字符串。如果找到匹配的前缀字符串，仍继续搜索正则表达式，但如果前缀字符串以 `^~`开头，则不再检查正则表达式。

具体的搜索匹配流程如下：

>1.将 `URI `与所有的前缀字符串进行比较。  
>2.`=` 修饰符表明` URI `必须与前缀字符串相等（不是开始，而是相等），如果找到，则搜索停止。  
3.如果找到的最长前缀匹配字符串以 `^~` 开头，则不再搜索正则表达式是否匹配。
4.存储匹配的最长前缀字符串。  
5.测试对比 `URI` 与正则表达式。  
6.找到第一个匹配的正则表达式后停止。  
7.如果没有正则表达式匹配，使用 4 存储的前缀字符串对应的 location。  

`=` 修饰符拥有最高的优先级。如网站首页访问频繁，我们可以专门定义一个 `location` 来减少搜索匹配次数（因为搜索到` = `修饰的匹配的 `location` 将停止搜索），提高速度：
```apacheconfig
location = / {
    ...
}
```

如何配置反向代理？
-
```apacheconfig
#对"/"启动反向代理
location / {
# 设置要代理的 uri，注意最后的 /。可以是 Unix 域套接字路径，也可以是正则表达式。
proxy_pass http://127.0.0.1:3000;

# 设置后端服务器“Location”响应头和“Refresh”响应头的替换文本
proxy_redirect off;

 
# 获取用户的真实 IP 地址
proxy_set_header X-Real-IP $remote_addr;

#后端的Web服务器可以通过 X-Forwarded-For 获取用户真实IP，多个 nginx 反代的情况下，例如 CDN。参见：http://gong1208.iteye.com/blog/1559835 和 http://bbs.linuxtone.org/thread-9050-1-1.html
proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;

#以下是一些反向代理的配置，可选。
# 允许重新定义或者添加发往后端服务器的请求头。
proxy_set_header Host $host;
 
#允许客户端请求的最大单文件字节数
client_max_body_size 10m;

#缓冲区代理缓冲用户端请求的最大字节数，
client_body_buffer_size 128k;

#nginx跟后端服务器连接超时时间(代理连接超时)
proxy_connect_timeout 90;

#后端服务器数据回传时间(代理发送超时)
proxy_send_timeout 90;

#连接成功后，后端服务器响应时间(代理接收超时)
proxy_read_timeout 90;

#设置代理服务器（nginx）保存用户头信息的缓冲区大小
proxy_buffer_size 4k;
 
#proxy_buffers缓冲区，网页平均在32k以下的设置
proxy_buffers 4 32k;
 
#高负荷下缓冲大小（proxy_buffers*2）
proxy_busy_buffers_size 64k;
#设定缓存文件夹大小，大于这个值，将从upstream服务器传
proxy_temp_file_write_size 64k;
}
```

如何配置负载均衡？
-
```apacheconfig
upstream backend.com{
 ip_hash;
 server 192.168.11.1:80;
 server 192.168.11.11.80 down;
 server 192.168.11.123:8009 max_fails=3 fail_timeout=20s;
 server 192.168.11.1234:8080;
}
```
upstream是Nginx的HTTP Upstream模块，这个模块通过一个简单的调度算法来实现客户端IP到后端服务器的负载均衡。
Nginx的负载均衡模块目前支持4种调度算法：

>1.轮询（默认）。每个请求按时间顺序逐一分配到不同的后端服务器，如果后端某台服务器宕机，故障系统被自动剔除，使用户访问不受影响。Weight 指定轮询权值，Weight值越大，分配到的访问机率越高，主要用于后端每个服务器性能不均的情况下。

>2.ip_hash。每个请求按访问IP的hash结果分配，这样来自同一个IP的访客固定访问一个后端服务器，有效解决了动态网页存在的session共享问题。

>3.fair。这是比上面两个更加智能的负载均衡算法。此种算法可以依据页面大小和加载时间长短智能地进行负载均衡，也就是根据后端服务器的响应时间来分配请求，响应时间短的优先分配。Nginx本身是不支持fair的，如果需要使用这种调度算法，必须下载Nginx的upstream_fair模块。

>4.url_hash。此方法按访问url的hash结果来分配请求，使每个url定向到同一个后端服务器，可以进一步提高后端缓存服务器的效率。Nginx本身是不支持url_hash的，如果需要使用这种调度算法，必须安装Nginx 的hash软件包。


FastCGI代理
-
`Nginx` 可用于将请求路由到 `FastCGI` 服务器。快速通用网关接口`（Fast Common Gateway Interface／FastCGI`）是一种让交互程序与`Web`服务器通信的协议。因此 `Nginx` 可以将请求路由到 `FastCGI `运行的应用程序，如 PHP 程序。

使用 `FastCGI` 服务器的最基本的 `Nginx` 配置包括使用 `fastcgi_pass` 指令而不是 `proxy_pass` 指令，以及使用 `fastcgi_param `指令来设置传递给 `FastCGI` 服务器的参数。 假设`FastCGI`服务器可在 `localhost:9000 `上访问。 以上一节中的代理服务器配置为基础，使用`fastcgi_pass`指令替换`proxy_pass`指令，并将参数更改为 `localhost:9000` 。 在 PHP 中， `SCRIPT_FILENAME `参数用于确定脚本名称，而 `QUERY_STRING` 参数用于传递请求参数。 生成的配置将是：
```apacheconfig


server {
    location / {
        fastcgi_pass  localhost:9000;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param QUERY_STRING    $query_string;
    }

    location ~ \.(gif|jpg|png)$ {
        root /data/images;
    }
}
```

这将设置一个服务器，将路由除静态图像以外的所有请求到运行在 `localhost:9000` 的 `FastCGI` 服务器。

参考阅读
-
[你真的了解如何将 Nginx 配置为Web服务器吗](https://lufficc.com/blog/configure-nginx-as-a-web-server)  
[Nginx 使用札记](https://mp.weixin.qq.com/s?__biz=MzI4MDEwNzAzNg==&mid=2649444723&idx=1&sn=02997ea12ef8266fa4a33f3215d73154&chksm=f3a27000c4d5f9163d4329c135bc229aab1264b7d7425f9521cb033931a4437ce11fa2a6d705&scene=0&key=aaeb8d633a843aba523365dee8fda73d87845f44192824d8dd4cf84871e726ac055a4c083904c4a04f8e6f501cde3a0c43cd9daf9363fb036b4d520694e12b10b0f63f3d9c34031a98a7ec950b86fd9f&ascene=0&uin=MTE3NTM3MDM2Mg%3D%3D&devicetype=iMac+MacBookPro11%2C4+OSX+OSX+10.12+build(16A323)&version=12020810&nettype=WIFI&lang=zh_CN&fontScale=100&pass_ticket=VEYdd070JaMcsHgioMplIHzBk7iHQ3r53j%2F9TOeAROreljg6UXBJbdMPe1UBEcmI)