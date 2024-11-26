<?php 
namespace App\Classes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Nestedsetbie {
    // Constructor class, được gọi khi một đối tượng của class này được tạo ra.
    function __construct($params = null) {
        // Gán các giá trị cho các thuộc tính của đối tượng.
        $this->params = $params;       // Lưu tham số truyền vào (có thể là mảng tham số).
        $this->checked = null;         // Đặt giá trị mặc định là null cho thuộc tính checked.
        $this->data = null;            // Đặt giá trị mặc định là null cho thuộc tính data.
        $this->count = 0;              // Khởi tạo biến đếm với giá trị ban đầu là 0.
        $this->count_level = 0;        // Khởi tạo biến đếm cấp độ với giá trị ban đầu là 0.
        $this->lft = null;             // Đặt giá trị mặc định là null cho thuộc tính lft (trái).
        $this->rgt = null;             // Đặt giá trị mặc định là null cho thuộc tính rgt (phải).
        $this->level = null;           // Đặt giá trị mặc định là null cho thuộc tính level (cấp độ).
    }

    // Phương thức Get để lấy dữ liệu từ cơ sở dữ liệu.
    public function Get() {
        $catalogue = (isset($this->params['isMenu']) && $this->params['isMenu'] === TRUE) ? '' : '_catalogue';
        
        // Xác định khóa ngoại nếu được truyền vào, nếu không thì mặc định là ''.
        $foreignkey = (isset($this->params['foreignkey'])) ? $this->params['foreignkey'] : 'post_catalogue_id';
        
        // Tách tên bảng thành mảng dựa trên dấu gạch dưới.
        $moduleExtract = explode('_', $this->params['table']);
        $join = (isset($this->params['isMenu']) && $this->params['isMenu'] == true) ? substr($moduleExtract[0], 0, -1) : $moduleExtract[0];

        // Tạo câu truy vấn để lấy dữ liệu từ bảng được chỉ định.
        $result = DB::table($this->params['table'] . ' as tb1')  // Chọn bảng chính với alias 'tb1'.
            ->select('tb1.id', 'tb2.name', 'tb1.parent_id', 'tb1.lft', 'tb1.rgt', 'tb1.level', 'tb1.order')  // Chọn các cột cần lấy.
            ->join($join . $catalogue . '_language as tb2', 'tb1.id', '=', 'tb2.' . $foreignkey)  // Thực hiện join với bảng dịch thuật (language).
            ->where([
                [
                    'tb2.language_id', '=', $this->params['language_id'],
                ],
                [
                    'tb1.deleted_at', '=', NULL
                ]
            ])
            ->orderBy('tb1.lft', 'asc')->get()->toArray();  // Sắp xếp theo cột 'left' theo thứ tự tăng dần và chuyển kết quả thành mảng.

        // Gán kết quả truy vấn vào thuộc tính data.
        $this->data = $result;
    }

    // Phương thức Set để xử lý và trả về dữ liệu đã được cấu trúc.
    public function Set() {
        // Kiểm tra xem data đã được thiết lập và có phải là một mảng hay không.
        if (isset($this->data) && is_array($this->data)) {
            $arr = null;  // Khởi tạo mảng kết quả.

            // Lặp qua từng phần tử trong data.
            foreach ($this->data as $key => $val) {
                // Tạo mối quan hệ cha-con giữa các phần tử.
                $arr[$val->id][$val->parent_id] = 1;  // Liên kết ID của phần tử với parent_id.
                $arr[$val->parent_id][$val->id] = 1;  // Liên kết parent_id của phần tử với ID của nó.
            }

            // Trả về mảng kết quả.
            return $arr;
        }
    }

    // Duyệt qua cấu trúc phân cấp và gắn giá trị left right
    public function Recursive($start = 0, $arr = null) {
        // Gán giá trị biên trái cho nút hiện tại
        $this->lft[$start] = ++$this->count;
    
        // Gán mức độ sâu hiện tại của nút
        $this->level[$start] = $this->count_level;
    
        // Kiểm tra nếu mảng được đặt và là một mảng
        if (isset($arr) && is_array($arr)) {
            // Duyệt qua từng nút con tiềm năng
            foreach ($arr as $key => $value) {
                // Kiểm tra nếu có kết nối giữa nút hiện tại và nút con
                // và đảm bảo rằng kết nối này chưa được xử lý trước đó
                if (
                    (isset($arr[$start][$key]) || isset($arr[$key][$start])) &&
                    (!isset($this->checked[$key][$start]) && !isset($this->checked[$start][$key]))
                ) {
                    // Tăng mức độ sâu cho nút con
                    $this->count_level++;
    
                    // Đánh dấu kết nối là đã được kiểm tra để tránh duyệt lại
                    $this->checked[$start][$key] = 1;
                    $this->checked[$key][$start] = 1;
    
                    // Đệ quy xử lý nút con
                    $this->Recursive($key, $arr);
    
                    // Sau khi quay lại từ đệ quy, giảm mức độ sâu
                    $this->count_level--;
                }
            }
        }
    
        // Gán giá trị biên phải cho nút hiện tại
        $this->rgt[$start] = ++$this->count;
    }
    
    public function Action() {
        // Kiểm tra xem các thuộc tính `level`, `lft`, và `rgt` có tồn tại và là mảng hay không
        if (isset($this->level) && is_array($this->level) && 
            isset($this->lft) && is_array($this->lft) && 
            isset($this->rgt) && is_array($this->rgt)) {
            
            // Khởi tạo mảng dữ liệu trống
            $data = null;
    
            // Duyệt qua từng phần tử trong mảng `level`
            foreach ($this->level as $key => $val) {
                // Bỏ qua phần tử đầu tiên với `key` là 0 (thường là gốc cây hoặc phần tử đặc biệt)
                if ($key == 0) continue;
    
                // Gán giá trị vào mảng `$data` với các thuộc tính `id`, `level`, `lft`, và `rgt`
                $data[] = array(
                    'id' => $key,               // ID của nút hiện tại
                    'level' => $val,            // Mức độ sâu của nút hiện tại
                    'lft' => $this->lft[$key],  // Giá trị trái của nút hiện tại
                    'rgt' => $this->rgt[$key],  // Giá trị phải của nút hiện tại
                    'user_id' => Auth::id()
                );
            }
    
            // Nếu mảng `$data` tồn tại và là mảng với ít nhất một phần tử
            if (isset($data) && is_array($data) && count($data)) {
                // Sử dụng phương thức `upsert` để chèn hoặc cập nhật dữ liệu trong bảng
                DB::table($this->params['table'])->upsert($data, 'id', ['level', 'lft', 'rgt']);
                // `upsert`: Cập nhật nếu ID đã tồn tại, chèn mới nếu chưa tồn tại
                // `id`: Cột dùng để kiểm tra trùng lặp
                // `['level', 'lft', 'rgt']`: Các cột sẽ được cập nhật nếu có trùng lặp
            }
        }
    }

    public function Dropdown($param = null) {
        // Gọi hàm `Get()` để lấy dữ liệu phân cấp từ cơ sở dữ liệu
        $this->Get();
    
        // Kiểm tra nếu dữ liệu đã được lấy và là một mảng
        if (isset($this->data) && is_array($this->data)) {
            // Khởi tạo mảng tạm thời để lưu trữ các mục cho dropdown
            $temp = null;
    
            // Mục gốc (Root) được thêm vào với giá trị là 0
            // Nếu `param['text']` được cung cấp và không rỗng, sử dụng nó làm text, nếu không thì mặc định là `[Root]`
            $temp[0] = (isset($param['text']) && !empty($param['text'])) ? $param['text'] : '[Root]';
    
            // Duyệt qua từng phần tử trong dữ liệu đã lấy
            foreach($this->data as $key => $val) {
                // Tạo chuỗi đại diện cho mỗi mục, với dấu gạch `|-----` được lặp lại theo cấp độ của mục
                // Ví dụ: |-----Category, |-----|-----Subcategory
                $temp[$val->id] = str_repeat('|-----', (($val->level > 0) ? ($val->level - 1) : 0)) . $val->name;
            }
    
            // Trả về mảng đã định dạng để dùng cho dropdown
            return $temp;
        }
    }
    
    
}