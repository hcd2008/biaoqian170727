<?php
namespace app\common\validate;
use think\Validate;
class Company extends Validate
{
    protected $rule = [
        'com_name'  =>  'require|max:255',
    ];

    protected $message  =   [
        'com_name.require' => '请填写企业名称',
    ];
}
?>