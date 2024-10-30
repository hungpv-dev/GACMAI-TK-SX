<?php
session_start();
$image = $_GET['image'];
$type = $_GET['type'];
if(!isset($_SESSION['authentication']) || empty($_SESSION['authentication'])){
    header('Location: /login');
    exit();
}
if($type && $image){
    $filePath = '/public/assets/uploads/'.$type.'/'.$image;
    $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
    echo '<div class="file-container" style="height: 100vh;width: 100vw">';
    if(in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'mp4', 'avi'])){ // Thêm video
        echo '<img src="'.$filePath.'">'; // Hiển thị ảnh
    } elseif ($fileExtension === 'pdf') { // Thêm điều kiện cho PDF
        echo '<iframe src="'.$filePath.'" width="100%" height="100%"></iframe>'; // Hiển thị PDF
    } else {
        header('Content-Disposition: attachment; filename="'.basename($filePath).'"'); // Thiết lập tải xuống
        header('Content-Type: application/octet-stream'); // Loại tệp
        readfile($filePath); // Đọc tệp và gửi đến trình duyệt
        exit(); // Dừng thực thi
    }
    echo '</div>';
}
?>
<style>
.file-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #cdcdcd;
    width: 100%;
}

.file-container img {
    max-width: 100%; 
    max-height: 100%; 
}
</style>
