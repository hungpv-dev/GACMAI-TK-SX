<?php 
namespace App\Utils;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Dompdf\Dompdf;
use Dompdf\Options;

class Export
{
    public function excel($filename,$view,$data = []){
        // Load nội dung HTML từ view
        $content = view($view, $data);
    
        // Chuyển đổi HTML thành định dạng Excel
        $reader = IOFactory::createReader('Html');
        $spreadsheet = $reader->loadFromString($content);
    
        // Tạo một writer để xuất file Excel
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    
        // Đặt tiêu đề cho file và xuất nó về cho người dùng tải về
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$filename.'.xlsx"');
        $writer->save('php://output');
    }
    
    public function pdf($filename,$view,$data = [],$output = false){
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $dompdf = new Dompdf($options);

        // Đọc nội dung từ file HTML
        $content = view($view,$data);

        // Nạp HTML vào Dompdf
        $dompdf->loadHtml($content);

        // Cài đặt kích thước giấy và hướng (A4, Portrait)
        $dompdf->setPaper('A3', 'portrait');

        // Kết xuất PDF
        $dompdf->render();

        // Lưu file PDF
        $dompdf->stream("$filename.pdf", array("Attachment" => !$output));
    }
}