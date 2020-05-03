<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:86:"F:\www\class\nj01\heima_mongodb_news\public/../application/admin\view\login\index.html";i:1588140973;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>用户登录</title>
    <style>
        body {
            font-size: 14px;
        }
        form{
            display: block;
            width: 800px;
            margin: 20px auto;
            text-align: center;
        }
        div{
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<form action="<?php echo url('login'); ?>" method="post">
    <div>
        <label>
            账号：
            <input type="text" name="username" value="admin">
        </label>
    </div>
    <div>
        <label>
            账号：
            <input type="text" name="password" value="admin888">
        </label>
    </div>
    <div>
        <input type="submit" value="进入系统">
    </div>
</form>
</body>
</html>