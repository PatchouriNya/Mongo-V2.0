# 完成 nginx虚拟主机配置

```
配置多站点

黑马新闻 后台和前台   phpmyadmin
```

# 熟悉nosql数据库

```
把redis和memcached以及mongodb的命令熟悉一下
```

# 完成黑马新闻

```
# 数据库要求 mongodb、redis和memcached
### 后台功能
1、后台登录
	session写入memcached服务中					 √
	用户登录失败次数据限制一天只能失败10次   redis     √
	用户登录信息账号和密码  mongodb				√
	用户登录成功后记录登录日志记录 mongodb			√
2、个人信息修改
	修改密码√，添加头像 √，查看登录日志√
3、新闻 mongodb
	列表√  搜索√，分页√
	添加√  表单验证√ 入库√ 
	修改√
	删除√和选择删除√
4、热门新闻排行  redis√
5、新闻评论 mongodb √
	查看评论功能√  删除评论√  回复评论√
	
### 前台
1、vue展示√
2、使用vue-router√
3、评论√ 登录√
	只能登录后才能评论√ 一个用户只能对一篇文章评论一次√
```

