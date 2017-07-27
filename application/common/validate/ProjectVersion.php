<?php
namespace app\common\validate;
use think\Validate;
class ProjectVersion extends Validate
{
    protected $rule = [
        'real_length'  =>  'require|between:1,99999',
        'real_width'  =>  'require|between:1,99999',
    ];

    protected $message  =   [
        'real_length.require' => '请输入标签长度',
        'real_width.require' => '请输入标签宽度',
    ];
}
?>