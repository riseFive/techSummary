>使用的是aws 中国  

安装私有registry
---
```apacheconfig
docker run -d -p 5000:5000 --restart always --name registry registry:2
```
>可以上[hub.docker.com](https://hub.docker.com/r/library/registry/)查看文档，一键安装

** 注意：aws默认不开启5000端口，自己要设置好安全组

本地构建image
---
```
docker build -t awsIP:5000/tag .
```
自己构建image的时候，一定要以registry的ip为前缀，比如你的ip是182.1.22.123，那么格式就是：`docker build -t 182.1.22.123:5000/tag .`,后面的tag 自己去一个名字就好了

push到自己的registry
---
```apacheconfig
docker push  awsIP:5000/tag
```
到这一步的时候我们可能应该推不上去，错误的解决方法在[这里](https://github.com/docker/distribution/issues/1874)。
1.在自己的本地创建一个 `/etc/docker/daemon.json`（如果你添加了docker 镜像加速，这个文件应该就有），添加下面内容：
```
{ "insecure-registries":["myregistry.example.com:5000"] }
```
然后重新启动`docker`,就可以`push`上去了

怎么查看自己的`registry` 列表?
---
>因为私有`registry`没有图形界面，所以你在本地推送上去，那也是看不见的,`docker` 官方也想到了(你以为都跟你一样制杖),给我们提供了一系列的`api`,供我们去查看

![](http://orvwtnort.bkt.clouddn.com/201721343/1524125146387.png) 
在本地查看
```apacheconfig
curl awsIP:5000/v2/_catalog
```




