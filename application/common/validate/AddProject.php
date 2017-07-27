<?php
namespace app\common\validate;
use think\Validate;
class AddProject extends Validate
{
    protected $rule = [
        'title'  =>  'require|max:255',
    ];

    protected $message  =   [
        'title.require' => '请输入项目名称',
    ];
}
?>