<?php
namespace app\common\validate;
use think\Validate;
class Test extends Validate
{
    protected $rule = [
        'xmname'  =>  'require|max:255',
    ];

    protected $message  =   [
        'xmname.require' => '请输入项目名称',
    ];
}
?>