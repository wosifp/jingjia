<?php
namespace Portal\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        echo "<script>window.location.href='/index.php?g=admin&m=public&a=login'</script>";
    }
}