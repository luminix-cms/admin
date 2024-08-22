<?php

namespace Luminix\Admin\Support;

use Luminix\Admin\AdminServiceProvider;

class Unpkg
{
    public static function url($file)
    {
        return 'https://unpkg.com/@luminix/mui-cms@' . AdminServiceProvider::CMS_VERSION . '/' . $file;
    }
}
