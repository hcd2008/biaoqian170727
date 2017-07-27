<?php
/**
 * 产品模型
 *
 */
namespace app\common\model;
use think\Db;
use think\Model;
class Jiancha extends Base{

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

    public function addJiancha($map){
        
        foreach ($map['userid'] as $k => $v) {
          foreach ($map['jcxiang'] as $kk => $vv) {
			  $a=['chanpinid'=>$map[chanpinid],'userid'=>$v,'jcxiang'=>$vv,'addtime'=>time()];
         $sql=Db::table('bq_jcbq')->insert($a);
		
            }
        }
      /*   $sql1=Db::table('bq_product')->where('pid',$map['chanpinid'])->update(['zhuangtai' => 1]);*/
       return $sql;
    }
    public function chanPinList(){
        $sql=Db::table('bq_product')->field('pid,proname,zhuangtai')->where('zhuangtai','1')->order('zhuangtai desc')->paginate(10);
       
       return $sql;
    }
    // public function chanPinWuList($chanpin){
    //     $str='';
    //     foreach ($chanpin as $k => $v) {
    //         $pid=$v['chanpinid'];
    //         $str.=','.$pid;
    //     }
    //     $str=substr($str,1);
    //      $sql=Db::table('bq_product')->field('pid,proname')->where('pid','not in',$str)->paginate(2);
    //     return $sql;
    // }
    public function cpList($id){
        $sql=Db::table('bq_jcbq')->alias('a')->join('bq_member b','a.userid=b.userid')->join('bq_product c','a.chanpinid=c.pid')->field('distinct a.itemid,a.chanpinid,a.userid,a.jcxiang,a.zhuangtai,a.addtime,b.realname,c.proname')->where('a.chanpinid',$id)->select();
       
    }
    public function fpUser($id){
        $sql=Db::table('bq_jcbq')->field('distinct userid')->where('chanpinid',$id)->select();
        $user=array_column($sql, 'userid','userid');
        
        return $user;
    }
    public function jcList($id){
        $sql=Db::table('bq_jcbq')->field('distinct jcxiang')->where('chanpinid',$id)->select();
       
        return $sql;
    }
    public function editUser($map){
        $chanpinid=$map['chanpinid'];
        $addtime=time();
        //把chanpinid传过来是这个的都删除，再添加
        $sql=Db::table('bq_jcbq')->where('chanpinid',$chanpinid)->where('userid',$map['userid'])->delete();
         
           foreach ($map['jcxiang'] as $kk=> $v) {
             $sql1=Db::table('bq_jcbq')->insert(['chanpinid'=>$chanpinid,'userid'=>$map['userid'],'jcxiang'=>$v,'addtime'=>$addtime]);
           } 
         
        return $sql1;
    }
    public function fenPeiList(){
         $count=Db::table('bq_jcbq')->alias('a')->join('bq_product b','a.chanpinid=b.pid')->distinct(true)->field(' b.pid,b.proname')->select();
        
         $sql=Db::table('bq_jcbq')->alias('a')->join('bq_product b','a.chanpinid=b.pid')->distinct(true)->field('b.pid,b.proname')->order('a.addtime desc')->paginate(10,count($count));
        // $sql=Db::table('bq_jcbq')->alias('a')->join('bq_product b','a.chanpinid=b.pid')->distinct(true)->field('b.pid,b.proname')->order('a.addtime desc')->page(1,10)->select();;
       $ss=['sql'=>$sql,'count'=>count($count)];

        return $ss;
    }
    public function jianChaXiang($userid,$chanpinid){
         $sql=Db::table('bq_jcbq')->field('jcxiang')->where('chanpinid',$chanpinid)->where('userid',$userid)->select();

         return $sql;
    }

}
?> 