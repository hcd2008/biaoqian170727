<?php
namespace app\common\validate;
use think\Validate;
class Product extends Validate
{
    protected $rule = [
        'proname'  =>  'require|max:255',
    ];

    protected $message  =   [
        'proname.require' => '请填写产品名称',
    ];
}
?>