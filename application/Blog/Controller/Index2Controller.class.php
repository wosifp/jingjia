<?php 
namespace Blog\Controller;

use Common\Controller\HomebaseController;

class Index2Controller extends HomebaseController{
	
	
	public function index2(){
		echo "this is class Index2 extends AdminbaseController<br>";
		$menumodel=DD("Common/Menu");
		/* $menumodel2 = D("Common/Menu"); */
		echo'<br>';
		var_dump($menumodel->select()) ;
		echo $menumodel->$__CLASS__;
	}
}