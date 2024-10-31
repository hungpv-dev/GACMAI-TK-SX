@extends('layouts.xuong')

@section('title')
    Đơn hàng sản xuất
@endsection

@section('content')
    <div class="content">
        <nav class="mb-2" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                <li class="breadcrumb-item">Đơn hàng</li>
            </ol>
        </nav>
        <h3 class="text-bold text-body-emphasis mb-5">Danh sách đơn hàng sản xuất</h3>
        <div>
            <div class="mb-3 thongke">

            </div>
            <!-- Search -->
            <div id="searchModel" class="d-none">
                <form class="d-flex align-items-center gap-3 flex-wrap mb-4" id="filter-form">
                    <div>
                        <select name="customer_id" class="form-select value empty choice">
                            <option value="">Khách hàng</option>
                            @foreach ($customersSearch as $item)
                                <option value="{{ $item->id }}">{{ $item->phone }} - {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="city_id" class="form-select value empty choice">
                            <option value="">Khu vực</option>
                            @foreach ($provinces as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="type_status" class="form-select value empty choice">
                            <option value="">Trạng thái sản xuất</option>
                            @foreach ($statusOrder as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="user_id" class="form-select value empty choice">
                            <option value="">Xưởng sản xuất</option>
                            @foreach ($factories as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="expried" class="form-select value empty choice">
                            <option value="">Tất cả</option>
                            <option value="1">Quá hạn</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-start">
                        <button type="submit" class="btn btn-lg btn-phoenix-info btn-filter me-2" title="Lọc">
                            <span class="fas fa-filter text-info fs-9 me-2"></span>Lọc
                        </button>
                        <button button class="btn btn-lg btn-phoenix-warning" onclick="removeFilter(this)"
                            type="button">Xoá
                            lọc</button>
                    </div>
                </form>
            </div>
            
            @if(user()->checkAdminXuong())
                <div class="d-flex justify-content-end mb-4">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModel">
                        Thêm đơn hàng
                    </button>
                </div>
            @endif
            <!-- Table -->
            <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-y mt-2 position-relative top-1"
                id="list_users_container">
                <div class="table-responsive quote-table-container scrollbar ms-n1 ps-1">
                    <table class="table table-hover table-sm fs-9 mb-0">
                        <thead>
                            <tr>
                                <th class="align-middle text-center text-uppercase">stt</th>
                                <th class="align-middle text-start text-uppercase">Mã</th>
                                <th class="align-middle text-start text-uppercase">Tên khách hàng</th>
                                <th class="align-middle text-start text-uppercase">Địa chỉ</th>
                                <th class="align-middle text-start text-uppercase">Trạng thái</th>
                                <th class="align-middle text-start text-uppercase">Sản phẩm</th>
                                <th class="align-middle text-start text-uppercase">Thời gian dự kiến</th>
                                <th class="align-middle text-start text-uppercase">Xưởng sản xuất</th>
                                <th class="align-middle text-start text-uppercase">Ghi chú</th>
                                <th class="align-middle text-center text-uppercase">Thao tác</th>
                            </tr>
                        </thead>

                        <tbody class="list-data" id="data_table_body">
                            <tr class="loading-data">
                                <td class="text-center" colspan="15">
                                    <div class="spinner-border text-info spinner-border-sm" role="status"><span
                                            class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="paginations"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-dialog">
                <div class="modal-content form-open">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Cập nhật đơn hàng</h5>
                        <button class="btn p-1 closeButton" type="button" data-bs-dismiss="modal" aria-label="Close">
                            <svg class="svg-inline--fa fa-xmark fs-9" aria-hidden="true" focusable="false" data-prefix="fas"
                                data-icon="xmark" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"
                                data-fa-i2svg="">
                                <path fill="currentColor"
                                    d="M310.6 361.4c12.5 12.5 12.5 32.75 0 45.25C304.4 412.9 296.2 416 288 416s-16.38-3.125-22.62-9.375L160 301.3L54.63 406.6C48.38 412.9 40.19 416 32 416S15.63 412.9 9.375 406.6c-12.5-12.5-12.5-32.75 0-45.25l105.4-105.4L9.375 150.6c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L160 210.8l105.4-105.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-105.4 105.4L310.6 361.4z">
                                </path>
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form class="row g-3" method="POST">
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <select name="customer_id" disabled class="form-select choice value validate">
                                        <option value="">Chọn khách hàng</option>
                                    </select>
                                    <label class="floating-label-cus">Khách hàng</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <select name="province_id" disabled class="form-select choice value validate"
                                        title="Tỉnh thành">
                                        <option value="">Chọn tỉnh thành</option>
                                    </select>
                                    <label class="floating-label-cus">Tỉnh thành</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <select name="district_id" disabled class="form-select choice value validate"
                                        title="Quận huyện">
                                        <option value="">Chọn quận huyện</option>
                                    </select>
                                    <label class="floating-label-cus">Quận huyện</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <select name="ward_id" disabled class="form-select choice value validate"
                                        title="Phường xã">
                                        <option value="">Chọn phường xã</option>
                                    </select>
                                    <label class="floating-label-cus">Phường xã</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <div class="form-floating">
                                    <input type="text" disabled name="address" class="form-control empty value"
                                        placeholder="Địa chỉ chi tiết">
                                    <label>Địa chỉ chi tiết</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <select name="current_status" class="form-select choice value validate"
                                        title="Trạng thái sản xuất">
                                        <option value="">Chọn trạng thái</option>
                                        @foreach (status('order_xuong') as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <label class="floating-label-cus">Trạng thái sản xuất</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <input type="text" disabled placeholder="Thời gian dự kiến" name="du_kien_time"
                                        data-options='{"minDate":"today","dateFormat":"d-m-Y", "locale": "vn", "shorthandCurrentMonth": true}'
                                        class="form-control datetimepicker value empty validate">
                                    <label>Thời gian dự kiến</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <div class="form-floating">
                                    <textarea name="note" disabled style="height: 80px" class='form-control value empty' placeholder="Ghi chú"></textarea>
                                    <label>Ghi chú</label>
                                </div>
                            </div>
                            <div class="col-12 gy-6">
                                <div class="row g-3 justify-content-center">
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-close-model btn-secondary mx-1"
                                            data-bs-dismiss="modal">Huỷ
                                        </button>
                                        <button type="submit" class="btn btn-primary btn-submit mx-1"
                                            title="Cập nhật">Cập
                                            nhật</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addModel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-dialog">
                <div class="modal-content form-open">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Thêm mới đơn hàng</h5>
                        <button class="btn p-1 closeButton" type="button" data-bs-dismiss="modal" aria-label="Close">
                            <svg class="svg-inline--fa fa-xmark fs-9" aria-hidden="true" focusable="false" data-prefix="fas"
                                data-icon="xmark" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"
                                data-fa-i2svg="">
                                <path fill="currentColor"
                                    d="M310.6 361.4c12.5 12.5 12.5 32.75 0 45.25C304.4 412.9 296.2 416 288 416s-16.38-3.125-22.62-9.375L160 301.3L54.63 406.6C48.38 412.9 40.19 416 32 416S15.63 412.9 9.375 406.6c-12.5-12.5-12.5-32.75 0-45.25l105.4-105.4L9.375 150.6c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L160 210.8l105.4-105.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-105.4 105.4L310.6 361.4z">
                                </path>
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form class="row g-3" method="POST">
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <select name="customer_id" onchange="showCustomer(this)"
                                        class="form-select choice value validate">
                                        <option value="">Chọn khách hàng</option>
                                        @foreach ($customers as $item)
                                            <option value="{{ $item->id }}">{{ $item->phone }} - {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label class="floating-label-cus">Khách hàng</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <select name="province_id" class="form-select choice value validate"
                                        title="Tỉnh thành">
                                        <option value="">Chọn tỉnh thành</option>
                                    </select>
                                    <label class="floating-label-cus">Tỉnh thành</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <select name="district_id" class="form-select choice value validate"
                                        title="Quận huyện">
                                        <option value="">Chọn quận huyện</option>
                                    </select>
                                    <label class="floating-label-cus">Quận huyện</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <select name="ward_id" class="form-select choice value validate" title="Phường xã">
                                        <option value="">Chọn phường xã</option>
                                    </select>
                                    <label class="floating-label-cus">Phường xã</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <div class="form-floating">
                                    <input type="text" name="address" class="form-control empty value"
                                        placeholder="Địa chỉ chi tiết">
                                    <label>Địa chỉ chi tiết</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="du_kien" oninput="formatBalance(event)"
                                        class="form-control validate empty value" placeholder="Chi phí dự kiến">
                                    <label>Chi phí dự kiến</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="thuc_thu" value=0
                                        oninput="formatBalance(event)"
                                        class="form-control validate set-0 value" placeholder="Đã thanh toán">
                                    <label>Đã thanh toán</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <select name="status_id" class="form-select empty choice value validate"
                                        title="Trạng thái">
                                        @foreach ($statusadd as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <label class="floating-label-cus">Trạng thái</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <input type="text" placeholder="Ngày dự kiến" name="du_kien_time"
                                        data-options='{"maxDate":"today","dateFormat":"d-m-Y", "locale": "vn", "shorthandCurrentMonth": true}'
                                        class="form-control datetimepicker value empty validate">
                                    <label>Ngày dự kiến</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <select name="factory_id" class="form-select validate empty choice value" title="Xưởng">
                                        <option value="">Chọn xưởng</option>
                                        @foreach ($factories as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <label class="floating-label-cus">Xưởng</label>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6">
                                <div class="form-floating">
                                    <select name="user_id" class="form-select validate empty choice value" title="Nhân sự phụ trách">
                                        <option value="">Nhân sự phụ trách</option>
                                        @foreach ($users as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <label class="floating-label-cus">Nhân sự phụ trách</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <div class="form-floating">
                                    <textarea name="note" style="height: 80px" class='form-control value empty' placeholder="Ghi chú"></textarea>
                                    <label>Ghi chú</label>
                                </div>
                            </div>
                            <div class="col-12 gy-6">
                                <div class="row g-3 justify-content-center">
                                    <div class="col-auto">
                                        <button type="button" onclick="addModel.reset()"
                                            class="btn btn-close-model btn-secondary mx-1" data-bs-dismiss="modal">Huỷ
                                        </button>
                                        <button type="submit" class="btn btn-primary btn-submit mx-1"
                                            title="Thêm mới">Thêm
                                            mới</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addPriceModel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-dialog">
                <div class="modal-content form-open">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Thêm chi phí cho đơn hàng</h5>
                        <button class="btn p-1 closeButton" type="button" data-bs-dismiss="modal" aria-label="Close">
                            <svg class="svg-inline--fa fa-xmark fs-9" aria-hidden="true" focusable="false" data-prefix="fas"
                                data-icon="xmark" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"
                                data-fa-i2svg="">
                                <path fill="currentColor"
                                    d="M310.6 361.4c12.5 12.5 12.5 32.75 0 45.25C304.4 412.9 296.2 416 288 416s-16.38-3.125-22.62-9.375L160 301.3L54.63 406.6C48.38 412.9 40.19 416 32 416S15.63 412.9 9.375 406.6c-12.5-12.5-12.5-32.75 0-45.25l105.4-105.4L9.375 150.6c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L160 210.8l105.4-105.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-105.4 105.4L310.6 361.4z">
                                </path>
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form class="row g-3" method="POST">
                            <div class="col-sm-6 col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="order_id" disabled class="form-control validate empty value"
                                        placeholder="Mã đơn hàng">
                                    <label>Mã đơn hàng</label>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6">
                                <div class="form-floating">
                                    <select name="tran_type_id" class="form-select validate value choice"
                                        title="Loại chi phí">
                                        <option value="">Chọn loại chi phí</option>
                                        @foreach ($type as $item)
                                        <option {{ $item->id == 2 ? 'selected' : '' }} value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <label class='floating-label-cus'>Loại chi phí</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <div class="form-floating">
                                    <input type="text" name="amount" oninput="formatBalance(event)"
                                        class="form-control validate empty value" placeholder="Số tiền">
                                    <label>Số tiền</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <div class="form-floating">
                                    <textarea name="note" class="form-control empty value" style="height: 100px;"
                                        placeholder="Ghi chú"></textarea>
                                    <label class='floating-label-cus'>Ghi chú</label>
                                </div>
                            </div>
                            <div class="col-12 gy-6">
                                <div class="row g-3 justify-content-center">
                                    <div class="col-auto">
                                        <button type="button" onclick="addModel.reset()"
                                            class="btn btn-close-model btn-secondary mx-1" data-bs-dismiss="modal">Huỷ
                                        </button>
                                        <button type="submit" class="btn btn-primary btn-submit mx-1" title="Thêm mới">Thêm
                                            mới</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        async function showCustomer(input) {
            let customer_id = input.value;
            let res = await axios.get(`/api/customers/${customer_id}`).then(res => res);
            if (res.status == 200) {
                VN.show(res.data, addModel);
                addModel.showValue(res.data);
            }
        }

        var addModel = new HandleForm('#addModel');
        addModel.setChoice();
        VN.render(addModel);
        addModel.addValidate(
            [
                ['du_kien', ['number', 'min:0']]
            ]
        );
        addModel.submit = async function(e) {
            e.preventDefault();
            let check = this.checkValidate();
            if (check) {
                this.loading(true);
                let value = this.value().formatPrice(['du_kien', 'thuc_thu']).get();
                try {
                    let res = await axios.post('/api/orders-xuong', value).then(res => res);
                    if (res.status == 201) {
                        addModel.reset();
                        addModel.hideModal();
                        showMessageMD(res.data.message);
                        request.id = res.data.id;
                        request.get();
                    }
                } catch (error) {
                    let res = error.response;
                    if (res.status == 422) {
                        this.logError(res.data);
                    } else {
                        showErrorMD('Có lỗi xảy ra, vui lòng thử lại sau!');
                        addModel.hideModal();
                        addModel.reset();
                    }
                }
                this.loading(false);
            }
        }
    </script>
    <script>
        const route = '/api/orders-xuong?status=10';
        var request = new RequestServer(route);
        var searchModal = new HandleForm('#searchModel');
        request.colspan = 14;

        function showStatus(status) {
            let content = '';
            let total = 0;
            for (const name in status) {
                let item = status[name];
                total += parseInt(item.count_order);
                content += `
                    <div class='redirectHome' data-status='${item.id}'>
                        <div class="icon">
                            <div class="icon-border" style="background-color: ${item.bg};">
                                <i class="fab fa-codepen" style="color: ${item.color}"></i>
                            </div>
                        </div>
                        <div class="body">
                            <p class="fw-bold m-0"><span>${item.count_order}</span> ${item.name}</p>
                            <span class="fs-8 fw-bold text-body-highlight">Tổng số</span>
                        </div>
                    </div>
                `;
            }
            content = `
                <div class='redirectHome'>
                    <div class="icon">
                        <div class="icon-border bg-primary-subtle">
                            <i class="fab fa-codepen text-primary-emphasis"></i>
                        </div>
                    </div>
                    <div class="body">
                        <p class="fw-bold m-0"><span>${total}</span> đơn hàng</p>
                        <span class="fs-8 fw-bold text-body-highlight">Tổng số đơn hàng</span>
                    </div>
                </div>
            ` + content;

            $('.thongke').html(content);
        }
        request.insert = function(data) {
            let dataStatus = request.response.data_status;
            showStatus(dataStatus);
            let content = data.map((item) => {
                    let time = day_between("", item.du_kien_time, false);
                    let timeView = time + ' Ngày';
                    if (time < 0) {
                        timeView = Log.danger(timeView);
                    } else {
                        timeView = Log.info(timeView);
                    }
                    return `
                        <tr class="${request.bold(item.id)}">
                            <td class='align-middle text-center'>${request.index++}</td>
                            <td class='align-middle text-start'><a href='/orders/${item.id}'>${item.id}</a></td>
                            <td class='align-middle text-start'>${item.customer.name}</td>
                            <td class='align-middle text-start' style="max-width: 200px;">${item.areas ?? Log.info('Chưa cập nhật')}</td>
                            <td class='align-middle text-start'>
                                 ${(item.status.id == 9 || item.status.id == 10) ? Status.status(item.current_status) : ''}
                            </td>
                            <td class='align-middle text-start'>${item.category?.name ?? Log.info('Chưa có')}</td>
                            <td class='align-middle text-start'>${item.du_kien_time ? dateTimeFormat(item.du_kien_time) + ' - ' + timeView : Log.danger('Chưa có')}</td> 
                            <td class='align-middle text-start'>${item.xuong?.name ?? Log.danger('Chưa có')}</td>
                            <td class='align-middle text-start'>${item?.note ?? Log.warning('Chưa có')}</td>
                            <td class='align-middle text-center'>
                                <div class='position-relative'>
                                    <button onclick='showOne(${item.id})' class='btn btn-edit-show btn-sm btn-phoenix-secondary text-info me-1 fs-10' title='Cập nhật' type='button' data-bs-toggle='modal' data-bs-target='#editModel'>
                                        <span class='fas far fa-edit'></span>
                                    </button>
                                    @if(user()->checkAdminXuong())
                                        <button onclick='showOne(${item.id})' class='btn btn-edit-show btn-sm btn-phoenix-secondary text-info me-1 fs-10' title='Thêm chi phí'      type='button' data-bs-toggle='modal' data-bs-target='#addPriceModel'>
                                            <span class='fas fa-plus'></span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    `;
                })
                .join('');
            return content;
        }
        document.addEventListener('DOMContentLoaded', async function() {
            await request.get();
            searchModal.showValue(request.params);
        });
        searchModal.setChoice();
        searchModal.submit = async function(e) {
            e.preventDefault();
            this.loading(true);
            let value = this.value().get();
            request.params = value;
            await request.get();
            this.loading(false, `<span class='fas fa-filter text-info fs-9 me-2'></span>Lọc`);
        }
        async function removeFilter(btn) {
            btnLoading(btn, true);
            searchModal.reset();
            request.params = {};
            await request.get();
            searchModal.showValue(request.params);
            btnLoading(btn, false, 'Xóa lọc');
        }

        var editModel = new HandleForm('#editModel');
        var addPriceModel = new HandleForm('#addPriceModel');
        editModel.closeReset();
        editModel.setChoice();
        async function showOne(id) {
            addPriceModel.form.querySelector("[name='order_id']").value = id;
            try {
                let fetch = await axios.get(`/api/orders/${id}`).then(res => res);
                if (fetch.status == 200) {
                    fetch.data.user_tk = fetch.data.rela;
                    fetch.data.factory_id = fetch.data.rela;
                    editModel.showValue(fetch.data, {
                        price: ['du_kien'],
                        date: ['du_kien_time']
                    });
                    editModel.choices.customer_id.setChoices([{
                        label: fetch.data.customer.name,
                        value: fetch.data.customer.id,
                        selected: true,
                    }], 'value', 'label', true)
                    VN.show(fetch.data, editModel);
                    editModel.id = fetch.data.id;
                }
            } catch (error) {
                let response = error.response;
                if (response.status == 404) {
                    showErrorMD(response.data.message);
                    editModel.hideModal();
                    editModel.reset();
                } else {
                    showErrorMD('Có lỗi xảy ra, vui lòng thử lại sau!');
                    editModel.hideModal();
                    editModel.reset();
                }
            }
        }
        addPriceModel.closeReset();
        addPriceModel.setChoice();
        addPriceModel.submit = async function(e) {
            e.preventDefault();
            let check = this.checkValidate();
            if (check) {
                this.loading(true);
                let value = this.value().formatPrice(['amount']).get();
                try {
                    let res = await axios.post('/api/order-expenses', value).then(res => res);
                    if (res.status == 201) {
                        addPriceModel.reset();
                        addPriceModel.hideModal();
                        showMessageMD(res.data.message);
                        request.id = res.data.id;
                        request.get();
                    }
                } catch (error) {
                    let res = error.response;
                    if (res.status == 422) {
                        this.logError(res.data);
                    } else {
                        showErrorMD('Có lỗi xảy ra, vui lòng thử lại sau!');
                        addPriceModel.hideModal();
                        addPriceModel.reset();
                    }
                }
                this.loading(false);
            }
        }
        editModel.submit = async function(e) {
            e.preventDefault();
            let check = this.checkValidate();
            if (check) {
                this.loading(true);
                let value = this.value().formatPrice(['du_kien', 'thu_them']).get();
                try {
                    let res = await axios.put(`/api/orders/${editModel.id}`, value).then(res => res);
                    if (res.status == 200) {
                        editModel.reset();
                        editModel.hideModal();
                        showMessageMD(res.data.message);
                        request.id = res.data.id;
                        request.get();
                    }
                } catch (error) {
                    let res = error.response;
                    if (res.status == 422) {
                        this.logError(res.data);
                    } else {
                        showErrorMD('Có lỗi xảy ra, vui lòng thử lại sau!');
                        editModel.hideModal();
                        editModel.reset();
                    }
                }
                this.loading(false);
            }
        }
        $(document).on('click', '.redirectHome', async function(e) {
            let status = $(this).attr('data-status');
            if (status) {
                request.params.status = status;
            } else {
                delete request.params.status;
            }
            await request.get();
            searchModal.showValue(request.params);
        })

        editModel.form.querySelector('[name="current_status"]').addEventListener('addItem', function() {
            let id = parseInt(this.value);
            let product = editModel.form.querySelector('.container-product');
            switch (id) {
                case 18: {
                    product.classList.remove('d-none');
                    product.querySelector('select').classList.add('validate');
                    break;
                }
                default: {
                    product.classList.add('d-none');
                    product.querySelector('select').classList.remove('validate');
                }
            }
        });
    </script>
@endsection
