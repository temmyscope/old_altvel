<?php
namespace app\controller;

use app\model\Controller;

class SearchController extends Controller{
	

	public function IndexEndPoint(){
		view('search.index');
	}
	
}
