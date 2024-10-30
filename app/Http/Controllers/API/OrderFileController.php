<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\OrderFile;
use AsfyCode\Utils\Request;
use Exception;

class OrderFileController extends Controller{
    public function store(Request $request){
        if(!$request->file('file')){
            return response()->json(['message' => 'No file uploaded'], 400);
        }
        $file = $request->file('file');
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return response()->json(['message' => 'Có lỗi trong quá trình tải lên'], 400);
        }
        try {
            // Kiểm tra MIME type thực sự của file
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if (!$finfo) {
                throw new Exception('Không thể mở finfo');
            }
            
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            if (!$mimeType) {
                throw new Exception('Không thể lấy MIME type của file');
            }
            
            finfo_close($finfo);
        
            // Danh sách MIME types hợp lệ
            // $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/webp'];
            // if (!in_array($mimeType, $allowedMimes)) {
            //     return response()->json(['message' => 'File không có định dạng hợp lệ'], 400);
            // }
        
            // Kiểm tra magic bytes của file JPEG (ffd8ffe0)
            $fileContent = file_get_contents($file['tmp_name'], false, null, 0, 4);
            if ($fileContent === false) {
                throw new Exception('Không thể đọc nội dung file');
            }
            
            if (bin2hex($fileContent) !== 'ffd8ffe0' && strtolower($mimeType) === 'image/jpeg') {
                return response()->json(['message' => 'File không phải là ảnh JPEG hợp lệ'], 400);
            }
        } catch (Exception $e) {
            // Xử lý lỗi và trả về thông báo
            return response()->json(['message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }



        $fileName = basename(time() . '_' . $file['name']);
        $pathUpload = './public/assets/uploads/hopdong/'.$fileName;
        try{
            if (move_uploaded_file($file['tmp_name'], $pathUpload)) {
                $orderFile = OrderFile::create([
                    'order_id' => $request->order_id,
                    'image' => '?image='.$fileName.'&type=hopdong',
                    'created_at' => now(),
                ]);
                return $this->sendResponse([
                    'message' => 'Upload file thành công!',
                    'id' => $orderFile->id,
                ], 201);
            } else {
                return response()->json(['message' => 'Không thể di chuyển file tải lên'], 500);
            }
        } catch (\Exception $e) {
            return $this->sendResponse([
                'message' => 'Upload file thất bại!',
            ], 500);
        }
    }
}