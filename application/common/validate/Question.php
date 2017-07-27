<?php
namespace app\common\validate;
use think\Validate;
class Question extends Validate
{
    protected $rule = [
        'title'  =>  'require|max:255',
    ];

    protected $message  =   [
        'title.require' => '请填写问题名称',
    ];
}
?>