@extends('layouts.app')

@section('title')
Trạng thái đơn hàng
@endsection

@section('content')
<div class="content">
    <nav class="mb-2" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
            <li class="breadcrumb-item">Trang thái đơn hàng</li>
        </ol>
    </nav>
    <h3 class="text-bold text-body-emphasis mb-5">Danh sách trạng thái</h3>
    <div>
        <div class="mb-3 thongke">
            <div>
                <div class="icon">
                    <div class="icon-border bg-primary-subtle">
                        <i class="fab fa-codepen text-primary-emphasis"></i>
                    </div>
                </div>
                <div class="body">
                    <p class="fw-bold m-0"><span class="thongke-sum">{{ $status->count() }}</span> trạng thái</p>
                    <span class="fs-8 fw-bold text-body-highlight">Tổng số</span>
                </div>
            </div>
        </div>
        <div class="col-8 m-auto">
            <h5 class='text-danger'>Lưu ý: </h5>
            <p><i>- Ô chọn là trạng thái <b class='text-info'>hoàn thành</b>, chuyển sang trạng thái này sẽ không thể chỉnh sửa nữa</i></p>
            <p><i><b>- Kéo thả để thay thứ tự hiển thị trạng thái!</b></i></p>
        </div>

        <!-- Table -->
        <div class="list-data" id="data_table_body">
            <div class="row">
                <div class="col-8 m-auto" data-sortable="data-sortable">
                    @foreach ($status as $k => $item)
                        <div class="sortable-item-wrapper mb-3" data-id="{{ $item->id }}">
                            <div class="input-group">
                                <div title="Khi chuyển sang trạng thái này sẽ không thể chỉnh sửa" class="input-group-text">
                                  <input class="form-check-input mt-0" {{ $item->type == 1 ? 'checked' : '' }} name="order" style="cursor: pointer;" type="radio">
                                </div>
                                <input type="text" class="form-control" name="name" value="{{ $item->name }}" style="background-color: {{ !$item->bg || $item->bg == ''  ? '#FFFFFF' : $item->bg}}; color: {{ !$item->color || $item->color == ''  ? '#000000' : $item->color }}" onfocus="this.style.backgroundColor=this.value; this.style.boxShadow='none';">
                                <input type="color" name='color' class="form-control-color col-2" value="{{ !$item->color || $item->color == ''  ? '#000000' : $item->color }}"
                                title="Chọn màu chứ" style="width: 50px;" oninput="this.previousElementSibling.style.color = this.value">
                                <input type="color" class="form-control-color col-2" value="{{ !$item->bg || $item->bg == '' ? '#FFFFFF' : $item->bg }}"
                                title="Chọn màu nền" name='bg' style="width: 50px;" oninput="this.previousElementSibling.previousElementSibling.style.backgroundColor = this.value">
                                <button class="btn btn-danger btn-delete" type="button">Xoá</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="d-flex m-auto col-8 justify-content-end">
                <button id="add_new" class="btn btn-secondary me-2">Thêm mới</button>
                <button id="update" class="btn btn-primary">Xác nhận</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('js/Sortable.min.js') }}"></script>
<script>
    document.getElementById('add_new').addEventListener('click', function() {
        const newItem = document.createElement('div');
        newItem.classList.add('sortable-item-wrapper', 'mb-3');
        newItem.innerHTML = `
            <div class="input-group">
                <div class="input-group-text" title="Khi chuyển sang trạng thái này sẽ không thể chỉnh sửa">
                    <input class="form-check-input mt-0" name="order" style="cursor: pointer;" type="radio">
                </div>
                <input type="text" class="form-control" name="name" style="background-color: #FFFFFF; color: #000000" autofocus onfocus="this.style.backgroundColor=this.value;this.style.boxShadow='none';">
                <input type="color" name='color' class="form-control-color col-2" value="#000000"
                title="Chọn màu chữ" style="width: 50px;" oninput="this.previousElementSibling.style.color = this.value">
                <input type="color" class="form-control-color col-2" value="#FFFFFF"
                title="Chọn màu nền" name='bg' style="width: 50px;" oninput="this.previousElementSibling.previousElementSibling.style.backgroundColor = this.value">
                <button class="btn btn-danger btn-delete" type="button">Xoá</button>
            </div>
        `;
        document.querySelector('.list-data .row .col-8').appendChild(newItem);
        newItem.querySelectorAll('input')[1].focus();
    });

    document.getElementById('update').addEventListener('click', function() {
        let btn = this;
        btnLoading(btn,true);
        const statusData = [];
        document.querySelectorAll('.sortable-item-wrapper').forEach((item, index) => {
            const id = item.getAttribute('data-id');
            const name = item.querySelector('input[name="name"]').value;
            const color = item.querySelector('input[name="color"]').value;
            const bg = item.querySelector('input[name="bg"]').value;
            let type = item.querySelector('input[name="order"]').checked ? 1 : null;
            statusData.push({ id, name,bg,color, type, sort: index + 1 });
        });
        axios.post('/api/orders-status', statusData, {
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => {
            if (response.status == 200) {
                showMessageMD(response.data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra, vui lòng thử lại');
        }).finally(()=> {
            btnLoading(btn,false,'Xác nhận');
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('btn-delete')) {
            e.target.closest('.sortable-item-wrapper').remove();
        }
    });

    let modal = document.getElementById("modalSuccessNotification");
    modal.addEventListener('hidden.bs.modal', function () {
        location.reload();
    });
</script>
@endsection