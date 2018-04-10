四种模式
-
-授权码模式(authorization code)
>最严密方式,支持refresh token

-简化模式（implicit）
>为web浏览器应用设计(不支持refresh token)

-密码模式（resource owner password credentials）
>最不推荐的一种模式，为遗留系统设计 支持refresh token

-客户端模式(client credentials)
>(为后台api服务消费者设计)(不支持refresh token)

refresh token
-
refresh token的初衷主要是为了用户体验不想用户重复输入账号密码来换取新token，因而设计了refresh token用于换取新token

如果模式由于没有用户参与，而且也不需要用户账号密码，仅仅根据自己的id和密钥就可以换取新token，因而没必要refresh token

参考阅读
--
[理解oAuth2.0](http://www.ruanyifeng.com/blog/2014/05/oauth_2_0.html)