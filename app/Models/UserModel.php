<?php

declare(strict_types=1);
/**
 * This file is part of RCS.
 *
 * @link     https://github.com
 * @document https://github.com/addcnos/hongyan/blob/master/README.md
 * @license  https://github.com/addcnos/hongyan/blob/master/LICENSE
 * @author   Addcn.Inc
 * @contact  huangdijia@gmail.com
 * @contact  365039476@qq.com
 */
namespace App\Models;

class UserModel extends Model
{
    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $fillable = ['app_id', 'uid', 'token', 'nickname', 'avatar', 'extend'];
}
