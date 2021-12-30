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
 * @contact  imtoogle@gmail.com
 */
namespace App\Models;

class ConfigModel extends Model
{
    protected $table = 'config';

    protected $primaryKey = 'id';

    protected $guarded = [];
}
