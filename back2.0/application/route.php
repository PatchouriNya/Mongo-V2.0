<?php

use think\Route;

Route::get('api/news','api/News/index');

Route::get('api/news/:id', 'api/News/detail');

Route::get('api/hot', 'api/Hot/hotlist');

Route::get('api/hot/:id', 'api/Hot/hot');

Route::get('api/per/:id', 'api/Vips/per');

Route::get('api/comments/:newsid', 'api/Comments/index');