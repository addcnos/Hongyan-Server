![Hongyan-logo (1)](https://user-images.githubusercontent.com/9858714/186065048-af4209ff-a76b-48ea-8c55-b92ee187a331.png)
![LICENSE](https://img.shields.io/badge/license-MIT-green)
![Language](https://img.shields.io/badge/Language-PHP-blue.svg)

## 鸿雁简介


数睿鸿雁，是一款企业级正式版即时通讯软件。基于Gatewayworker+LaravelS实现，兼具稳定的网络通信、动态扩容缩容、业务快速集成特点。在业务生产实践中，我们不断丰富鸿雁功能，打磨细节，提升其稳定性与可扩展性。现正式开源鸿雁，回馈开源社区，共享技术成果。  

## 鸿雁特点

* 公司级开源：安全稳定、功能丰富、易于定制及扩展
* 丰富的SDK：提供Web、Android、iOS、Flutter各版本，可一键零成本接入
* 云部署：支持百万用户同时在线使用，可动态扩容缩容
* 全链路开源：公开技术细节，提供服务部署、安全风控的解决方案

## 技术架构

![1f07e9fd7d5ca3f17da29f6576a2fccb.png](https://picossali.oss-cn-beijing.aliyuncs.com/YD20210721-153237.png)

#### 架构描述

1. 令牌注册。客户端访问后台LaravelBusiness（以下简称LB），LB通过http请求与鸿雁中台服务（以下简称LS）交互，完成注册token令牌，并返回token令牌给LB，进而转发给客户端。

2. 用户链接。用户通过WebSocket与Gatewayworker建立长连接。

3. 用户通信。消息从客户端发起请求至LaravelS服务，再通过API请求至Gateway（Gateway和业务相互独立，只用作接收和发送消息），Gatewayworker完成消息的发出。

## 服务部署

#### docker版本

为降低试用门槛，我们提供docker集成版本（LaravelS+Swoole+PHP+Gatewaywoker），鉴于开发环境差异，Mysql与Redis，我们极力推荐docker安装后与鸿雁做通信，通过简单配置，即可快速开始鸿雁体验之旅。以下为docker集成版本安装方法： 

1. clone 配置仓库（[https://github.com/addcnos/Hongyan-Docker-ENV.git](https://github.com/addcnos/Hongyan-Docker-ENV)）至本地。 

2. 进入配置仓库的根目录，执行以下两条命令，服务即运行成功。 

```bash
# 构建镜像
$ docker compose build
# 启动容器
$ docker compose up
```

3. 其他常用命令：

```bash
# 关闭
$ docker compose stop
# 重启
$ docker compose restart
# 删除
$ docker compose down
```

#### 云部署

如若进行生产环境使用，我们推荐部署在阿里云、AWS等云平台，可动态扩容缩容，按需收费。

PS：为响应客户端快速集成，我们提供多版本sdk，请按需选用。

## 接入步骤

0. 了解数睿即时通中台
1. 申请应用的App Key (注：docker体验版本自动生成)
2. 账号集成

![5031f69558a578fa458472e25dbafde9.png](https://picossali.oss-cn-beijing.aliyuncs.com/YD20210721-153255.png)

    2.1 App 向 App Server 发起注册请求. App Server 配置好申请的 app_key 和 secret,准备好注册的用户信息,向 IM 的 web 服务发起注册请求.

    2.2 IM web 服务注册成功后,会将 token 返回给 App Server,App Server 再返回给 App.

    2.3 App 将 token 保存在本地,然后每次请求 IM web 服务的接口,都带上 token

    2.4 连接 IM 的 websocket 服务,见 App 客户端集成

## 相关资料

##### [数睿鸿雁后端服务文档](https://github.com/addcnos/Hongyan-Server)
##### [数睿鸿雁SDK-flutter文档](https://github.com/addcnos/Hongyan-Flutter-SDK)
##### [数睿鸿雁SDK-Android文档](https://github.com/addcnos/Hongyan-Android-SDK)
##### [数睿鸿雁SDK-Objective-C文档](https://github.com/addcnos/Hongyan-IOS-SDK)
##### [数睿鸿雁SDK-Web文档](https://github.com/addcnos/Hongyan-Web-SDK)

## 加入我们

数睿鸿雁在实践中不断前行，如果你对即时通讯兴趣盎然，并且热衷开源，欢迎你加入我们！
