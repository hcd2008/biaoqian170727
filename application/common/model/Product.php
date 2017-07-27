<?php
/**
 * 产品模型
 *
 */
namespace app\common\model;
use think\Db;
use think\Model;
class Product extends Base{

	public $modelName = '产品';

	public $insertFields = [
		['name'=>'pid','type'=>'hidden','title'=>'','help'=>'','option'=>''],
		/*['name'=>'mark','type'=>'checkbox','title'=>'标志','help'=>'','option'=>'getMark'],*/
		['name'=>'proname','type'=>'text','title'=>'产品名称','help'=>'','option'=>'','required'=>true],
		/*['name'=>'bid','type'=>'select','title'=>'品牌','help'=>'','option'=>'getBrand'],*/
		['name'=>'cid','type'=>'select','title'=>'类别','help'=>'','option'=>'getCategory'],
		
		['name'=>'standard','type'=>'text','title'=>'产品标准','help'=>'','option'=>''],
		['name'=>'level','type'=>'text','title'=>'质量等级','help'=>'','option'=>''],
		['name'=>'peiliao','type'=>'text','title'=>'配料表','help'=>'','option'=>''],
		['name'=>'yingyang','type'=>'text','title'=>'营养成分表','help'=>'','option'=>''],
		['name'=>'jinghanliang','type'=>'text','title'=>'净含量','help'=>'','option'=>''],
		['name'=>'unit','type'=>'text','title'=>'单位','help'=>'','option'=>''],
		['name'=>'guige','type'=>'text','title'=>'规格','help'=>'','option'=>''],
		['name'=>'mfddate','type'=>'text','title'=>'生产日期','help'=>'','option'=>''],

		['name'=>'expdate','type'=>'text','title'=>'保质期','help'=>'','option'=>''],
		['name'=>'company','type'=>'select','title'=>'生产企业','help'=>'','option'=>'getCompany'],
		['name'=>'barcode','type'=>'text','title'=>'条形码','help'=>'','option'=>''],
		['name'=>'contact','type'=>'text','title'=>'联系方式','help'=>'','option'=>''],
		['name'=>'fenzhuang','type'=>'select','title'=>'分装食品','help'=>'','option'=>[0=>'否',1=>'是']],
		['name'=>'fenzhuangnote','type'=>'text','title'=>'分装信息','help'=>'','option'=>''],

		['name'=>'zhuanjiyin','type'=>'select','title'=>'转基因食品','help'=>'','option'=>[0=>'否',1=>'是']],
		['name'=>'fuzhao','type'=>'select','title'=>'辐照食品','help'=>'','option'=>[0=>'否',1=>'是']],
		['name'=>'lvse','type'=>'select','title'=>'绿色食品','help'=>'','option'=>[0=>'否',1=>'是']],
		['name'=>'teshu','type'=>'select','title'=>'特殊膳食','help'=>'','option'=>[0=>'否',1=>'是']],


		['name'=>'renqun','type'=>'text','title'=>'适宜人群','help'=>'','option'=>''],
		['name'=>'method','type'=>'text','title'=>'食用方法','help'=>'','option'=>''],

		['name'=>'liang','type'=>'text','title'=>'食用量','help'=>'','option'=>''],
		['name'=>'cpcode','type'=>'textarea','title'=>'产品编号','help'=>'','option'=>'','style'=>'width:518px;height:100px;border:solid 1px #a7b5bc;'],
		['name'=>'cccode','type'=>'textarea','title'=>'出厂编号','help'=>'','option'=>'','style'=>'width:518px;height:100px;border:solid 1px #a7b5bc;'],
	];

	public function getCompany(){
		$lists = [];
		$items = Db::name('company')->select();
		$lists[0] = '请选择';
		foreach ($items as $key => $value) {
			$lists[$value['com_id']] = $value['com_name'];
		}
		return $lists;
	}

	public function getCategory(){
		$lists = [];
		$items = Db::name('category')->select();
		$lists[0] = '请选择';
		foreach ($items as $key => $value) {
			$lists[$value['catid']] = $value['catname'];
		}
		
		return $lists;
	}

	public function getBrand(){
		$ST = cache('setting');
		return explode('|','请选择|'.$ST['product_brand']);
	}

	public function getMark(){
		$ST = cache('setting');
		return explode('|',$ST['product_mark']);
	}
	
	/**
     * 产品列表
     * @param array $condition
     * @param string $field
     * @param number $page
     * @param string $order
     */
	public function getProductList($condition = array(), $field = '*', $page = 0, $order = 'pid ASC', $limit = '100'){
		return $this->where($condition)->page($page)->order($order)->limit($limit)->select();
	}

	
}
?>