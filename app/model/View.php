<?php
namespace app\model;

use app\lib\HTML;

class View{
	public $dataSource, $_Page = ['ajax' => false];

	public function __construct(){}

	public function render($viewName, $data = []){
		global $blade;
		
		$viewString= implode(DS, explode('.', $viewName));
		
		if(view_exists($viewString)){
			echo $blade->render($viewName, [ 'dataSource' => $data ]);
			if(isset($this->_Page['per_page'])){
				if ($this->_Page['ajax'] === true){
					HTML::AjaxPaginate($this->_Page['per_page']);
				}else{
					if(count($data) == $this->_Page['per_page']){
						HTML::paginate($this->_Page['per_page']);	
					}else{
						HTML::paginate($this->_Page['per_page'], true);
					}
				}
			}
		}else{
			echo HTML::card('View Error');
				print("The view does not exist");
		}
	}

	public function paginate(int $perPage){
		$this->_Page['per_page'] = $perPage;
		return $this;
	}

	public function AjaxPaginate(int $perPage){
		$this->_Page['per_page'] = $perPage;
		$this->_Page['ajax'] = true;
		return $this;
	}
}