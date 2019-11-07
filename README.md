# MJPHP
一个简单轻量级的PHP开发框架

### 环境要求
- PHP >= 7.0 （建议
- OpenSSL PHP 扩展
- PDO PHP 扩展
- Mbstring PHP 扩展
- JSON PHP 扩展

### web服务期配置
- Nginx
```
    server_name your_domain
    listen your_port
    root /yourProjectPath/public;
    location / {
        try_files $uri $uri/ /index.php;
    }
```
打开浏览器访问默认主页 http://your_domain:your_port 

### 数据库（MySQL）

#### 配置（database）
默认配置文件在根目录.env文件以json存在
```
    {
    "APP_ENV": "local",
    "APP_URL": "",
    "database": { //MySQL
        "master": {
            "host": "localhost",
            "port": 3306,
            "username": "",
            "dbname": "",
            "password": "",
            "charset": "utf8"
        },
        "slave": {
            "host": "localhost",
            "port": 3306,
            "username": "",
            "dbname": "",
            "password": "",
            "charset": "utf8"
        }
    },
    "redis": {
        "host": "127.0.0.1",
        "port": 6379,
        "database": 1
    },
    "LOGS": { //日志
        "prefix": "F.", //文件名前缀
        "split": "Ymd" //日期format格式
    }
}
```
配置好连接后使用Model调用

#### 使用
示例方法(所有方法都可链式调用)

```
UserModel::create([]); //插入一条数据或条数据
UserModel::select('id', 'name')->where(['id' => 1])->get(); //获取一条数据
UserModel::select('id', 'name')->where(['id' => 1])->gets(); //获取多条数据
UserModel::where(['id' => 1])->update([]); //修改数据
UserModel::where(['id' => 1])->del(); //删除数据
UserModel::query(sql, bindParams); //原生sql执行
```
#### select
```
select('*')
select('id', 'name', ...)
select(['id', 'name'])
```

#### where（条件）
```
where(['id' => 1])
where('id', '!=', 1)
where('id > ? AND id < ?', [1, 10])
```

#### whereIn（in查询）
```
whereIn('id', [1,2,3])
```

#### orderBy（排序）
```
orderBy('id', 'DESC')
orderBy(['id' => 'DESC', 'updated_at' => 'DESC'])
```

#### groupBy（分组）
```
groupBy('field')
groupBy(['field1', 'field2'])
```

#### limit（分页）
```
limit(1, 10)
```

#### count (计算总数)
```
count()
```

#### 事务
```
begin() //开启

commit() //提交

rollback() //回滚
```

#### sql日志
```
enableSqlLog() //开启sql日志记录
    //todo...
getSqlLog() //获取sql日志
```

#### 别名
1. get别名(getOne | first)
2. gets别名(getAll)
3. create别名(save)
4. update别名(modify)