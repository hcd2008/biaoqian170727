<?php
namespace app\common\validate;
use think\Validate;
class Project extends Validate
{
    protected $rule = [
        'title'  =>  'require|max:255',
        'type'  =>  'require|number',
        'product_id'  =>  'require|number',
        'starttime'  =>  'require',
        'endtime'  =>  'require',
        'remindtime'  =>  'require',
        'real_length'  =>  'require',
        'real_width'  =>  'require',
        'unit'  =>  'require',
    ];

    protected $message  =   [
        'title.require' => '请输入项目名称',
    ];
}
?>