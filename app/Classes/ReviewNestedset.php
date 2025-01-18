<?php 
namespace App\Classes;
use Illuminate\Support\Facades\DB;

class ReviewNestedset{

	function __construct($params = NULL){
		$this->params = $params;
		$this->checked = NULL;
		$this->data = NULL;
		$this->count = 0;
		$this->count_level = 0;
		$this->lft = NULL;
		$this->rgt = NULL;
		$this->level = NULL;
	}

    public function Get() {
        $result = DB::table($this->params['table'])  // Chọn bảng chính với alias 'tb1'.
            ->select('id', 'parent_id', 'lft', 'rgt', 'level')  // Chọn các cột cần lấy.
            ->where([
                [
                    'reviewable_type', '=', $this->params['reviewable_type']
                ]
            ])
            ->orderBy('lft', 'asc')->get()->toArray();  // Sắp xếp theo cột 'left' theo thứ tự tăng dần và chuyển kết quả thành mảng.

        // Gán kết quả truy vấn vào thuộc tính data.
        $this->data = $result;
    }


	public function Set(){
		if(isset($this->data) && is_array($this->data)){
			$arr = NULL;
			foreach($this->data as $key => $val){
				$arr[$val->id][$val->parent_id] = 1;
				$arr[$val->parent_id][$val->id] = 1;
			}
			return $arr;
		}
	}

	public function Recursive($start = 0, $arr = NULL){
		$this->lft[$start] = ++$this->count;
		$this->level[$start] = $this->count_level;
		if(isset($arr) && is_array($arr)){
			foreach($arr as $key => $val){
				if((isset($arr[$start][$key]) || isset($arr[$key][$start])) &&(!isset($this->checked[$key][$start]) && !isset($this->checked[$start][$key]))){
					$this->count_level++;
					$this->checked[$start][$key] = 1;
					$this->checked[$key][$start] = 1;
					$this->recursive($key, $arr);
					$this->count_level--;
				}
			}
		}
		$this->rgt[$start] = ++$this->count;
	}

    public function Action(){
		if(isset($this->level) && is_array($this->level) && isset($this->lft) && is_array($this->lft) && isset($this->rgt) && is_array($this->rgt)){

			$data = NULL;
			foreach($this->level as $key => $val){
				if($key == 0) continue;
				$data[] = array(
					'id' => $key,
					'level' => $val,
					'lft' => $this->lft[$key],
					'rgt' => $this->rgt[$key],
				);
			}
			if(isset($data) && is_array($data) && count($data)){
				DB::table($this->params['table'])->upsert($data, 'id', ['level','lft','rgt']);
			}
		}
    }
}
