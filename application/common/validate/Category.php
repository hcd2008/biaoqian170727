<?php
namespace app\common\validate;
use think\Validate;
class Category extends Validate
{
    protected $rule = [
        'catname'  =>  'require|max:255',
    ];

    protected $message  =   [
        'catname.require' => '请填写分类名称',
    ];
}
?>