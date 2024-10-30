@extends('layouts.app')

@section('title')
    Chi tiết khách hàng
@endsection

@section('content')
    <div class="content">
        <nav class="mb-2" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="/customers">Danh sách khách hàng</a></li>
                <li class="breadcrumb-item">Chi tiết khách hàng</li>
            </ol>
        </nav>
        <h3 class="text-bold text-body-emphasis mb-5">Chi tiết khách hàng</h3>  
        <div>
            <div class="d-flex justify-content-end gap-3 mb-4">
                <button class='btn btn-info' data-customer={{ $customer->id }} data-bs-toggle="modal"
                    data-bs-target="#logModel">Thêm liên hệ</button>
                    @if(in_array(optional($customer->status)->id,[status('order_customer'),status('customer_success')]))
                    <button class='btn btn-primary' data-bs-toggle="modal"
                            data-bs-target="#addModel">Thêm
                            đơn hàng</button>
                @endif
            </div>

            <div class="row">
                <div class="col-sm-6 col-md-2 mb-4">
                    <h5>Tên khách hàng</h5>
                    <i>{{ $customer->name }}</i>
                </div>
                <div class="col-sm-6 col-md-2 mb-4">
                    <h5>Số điện thoại</h5>
                    <i>{{ $customer->phone }}</i>
                </div>
                <div class="col-sm-6 col-md-2 mb-4">
                    <h5>Nhu cầu</h5>
                    <i>{{ $customer->category->name ?? 'Chưa có' }}</i>
                </div>
                <div class="col-sm-6 col-md-2 mb-4">
                    <h5>Địa chỉ</h5>
                    <i>{{ $customer->getAreasAttribute() }}</i>
                </div>
                <div class="col-sm-6 col-md-2 mb-4">
                    <h5>Nhân viên phụ trách</h5>
                    <i>{{ $customer->user->name ?? 'Chưa có' }}</i>
                </div>
                <div class="col-sm-6 col-md-2 mb-4">
                    <h5>Lịch hẹn</h5>
                    <i>{{ $customer->status_id == status('customer_schelude') ? dateFormat($customer->schedule) : 'Không có' }}</i>
                </div>
            </div>

            <h4 class="mb-3">Đơn hàng đã đặt</h4>
            <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-y mt-2 position-relative top-1"
                id="list_users_container">
                <div class="table-responsive quote-table-container scrollbar ms-n1 ps-1">
                    <table class="table table-hover table-sm fs-9 mb-0">
                        <thead>
                            <tr>
                                <th class="align-middle text-center text-uppercase">stt</th>
                                <th class="align-middle text-start text-uppercase">Mã đơn hàng</th>
                                <th class="align-middle text-start text-uppercase">Địa chỉ</th>
                                <th class="align-middle text-end text-uppercase">Doanh thu dự kiến</th>
                                <th class="align-middle text-end text-uppercase">Đã thu</th>
                                <th class="align-middle text-end text-uppercase">Chờ duyệt</th>
                                <th class="align-middle text-end text-uppercase">Còn lại</th>
                                <th class="align-middle text-start text-uppercase">Trạng thái</th>
                                <th class="align-middle text-start text-uppercase">Thời gian dự kiến</th>
                                <th class="align-middle text-start text-uppercase">Người tạo</th>
                                <th class="align-middle text-start text-uppercase">Ngày tạo</th>
                                <th class="align-middle text-start text-uppercase">Ghi chú</th>
                                <th class="align-middle text-center text-uppercase">Thao tác</th>
                            </tr>
                        </thead>

                        <tbody class="list-data" id="data_table_body2">
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
                <div class="paginations2"></div>
            </div>
            <!-- Table -->
            <!-- Table -->
            <h4>Lịch sử liên hệ khách hàng</h4>
            <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-y mt-2 position-relative top-1"
                id="list_users_container">
                <div class="table-responsive quote-table-container scrollbar ms-n1 ps-1">
                    <table class="table table-hover table-sm fs-9 mb-0">
                        <thead>
                            <tr>
                                <th class="align-middle text-start text-uppercase">Thời gian</th>
                                <th class="align-middle text-start text-uppercase">Nhu cầu</th>
                                <th class="align-middle text-start text-uppercase">Từ trạng thái</th>
                                <th class="align-middle text-start text-uppercase">Đến trạng thái</th>
                                <th class="align-middle text-start text-uppercase">Nhân sự</th>
                                <th class="align-middle text-start text-uppercase">Nội dung</th>
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
                <hr>
                <div class="typeTotalStatus mb-2">
                    <span class="fw-bold">Tên khách hàng</span>
                    <span class="fw-bold text-center">{{ $customer->name }}</span>
                    <span class="fw-bold text-end">Tổng số đơn đã đặt</span>
                </div>
                <div class="typeTotalStatus mb-3">
                    <span class="fw-bold">Trạng thái</span>
                    <span class="fw-bold text-center">
                        <span class="btn btn-xs btn-round text-white"
                            style="background-color:{{ $customer->status->bg }};color:{{ $customer->status->color }} !important">{{ $customer->status->name }}</span>
                        <br>
                    </span>
                    <span class="fw-bold text-end total-order text-primary fs-7"
                        id="total">{{ $customer->orders()->count() }} đơn</span>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="logModel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-dialog">
                <div class="modal-content form-open">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Log lại lịch sử</h5>
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
                            <div class="col-sm-12 col-md-12">
                                <div class="form-floating">
                                    <select name="customer_id" class="form-select validate empty value choice"
                                        title="Khách hàng">
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    </select>
                                    <label class='floating-label-cus'>Khách hàng</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <div class="form-floating">
                                    <textarea name="content" class='form-control empty value' style="height: 100px" placeholder="Ghi chú"></textarea>
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
                                            title="Lưu">Lưu</button>
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
                            <svg class="svg-inline--fa fa-xmark fs-9" aria-hidden="true" focusable="false"
                                data-prefix="fas" data-icon="xmark" role="img" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 320 512" data-fa-i2svg="">
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
                                    <select name="customer_id" class="form-select choice value validate">
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
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
                                        class="form-control validate empty value" placeholder="Doanh thu dự kiến">
                                    <label>Doanh thu dự kiến</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="thuc_thu" value=0
                                        oninput="formatBalance(event),setBankAccount(this.value)"
                                        class="form-control validate set-0 value" placeholder="Đã thanh toán">
                                    <label>Đã thanh toán</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 d-none" id="bank_account_id">
                                <div class="form-floating">
                                    <select name="bank_account_id" class="form-select choice value"
                                        title="Tài khoản nhận">
                                        @foreach ($account as $item)
                                            <option value="{{ $item->id }}">{{ $item->bank->name }} -
                                                {{ $item->full_name }} -
                                                {{ $item->bank_number }}</option>
                                        @endforeach
                                    </select>
                                    <label class="floating-label-cus">Tài khoản nhận</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 d-none" id="note_giao_dich">
                                <div class="form-floating">
                                    <textarea name="note_giao_dich" style="height: 80px" class='form-control value empty'
                                        placeholder="Ghi chú giao dịch"></textarea>
                                    <label>Ghi chú giao dịch</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <select name="status_id" class="form-select choice value validate"
                                        title="Trạng thái">
                                        @foreach (status('order') as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <label class="floating-label-cus">Trạng thái</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <input type="text" placeholder="Thời gian dự kiến" name="du_kien_time"
                                        data-options='{"minDate":"today","dateFormat":"d-m-Y", "locale": "vn", "shorthandCurrentMonth": true}'
                                        class="form-control datetimepicker value empty validate">
                                    <label>Thời gian dự kiến</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 d-none container-xuong">
                                <div class="form-floating">
                                    <select name="factory_id" class="form-select empty choice value" title="Xưởng">
                                        <option value="">Xưởng</option>
                                        @foreach ($factories as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <label class="floating-label-cus">Xưởng</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 d-none container-user">
                                <div class="form-floating">
                                    <select name="user_tk" class="form-select empty choice value" title="Người thiết kế">
                                        <option value="">Người thiết kế</option>
                                        @foreach ($usertk as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <label class="floating-label-cus">Người thiết kế</label>
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
                                        <button type="button" onclick="addModel.reset()" class="btn btn-close-model btn-secondary mx-1"
                                            data-bs-dismiss="modal">Huỷ
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
    <div class="modal fade" id="editModel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-dialog">
                <div class="modal-content form-open">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Cập nhật đơn hàng</h5>
                        <button class="btn p-1 closeButton" type="button" data-bs-dismiss="modal" aria-label="Close">
                            <svg class="svg-inline--fa fa-xmark fs-9" aria-hidden="true" focusable="false"
                                data-prefix="fas" data-icon="xmark" role="img" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 320 512" data-fa-i2svg="">
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
                                    <select name="customer_id" class="form-select choice value validate">
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
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
                                        class="form-control validate empty value" placeholder="Doanh thu dự kiến">
                                    <label>Doanh thu dự kiến</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="thu_them" value=0
                                        oninput="formatBalance(event),setBankAccountEdit(this.value)"
                                        class="form-control validate set-0 value" placeholder="Thu thêm">
                                    <label>Thu thêm</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 d-none" id="bank_a_id">
                                <div class="form-floating">
                                    <select name="bank_account_id" class="form-select choice value"
                                        title="Tài khoản nhận">
                                        @foreach ($account as $item)
                                        <option value="{{ $item->id }}">{{ $item->bank->name }} -
                                            {{ $item->full_name }} -
                                            {{ $item->bank_number }}</option>
                                        @endforeach
                                    </select>
                                    <label class="floating-label-cus">Tài khoản nhận</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 d-none" id="note_a">
                                <div class="form-floating">
                                    <textarea name="note_giao_dich" style="height: 80px" class='form-control value empty'
                                        placeholder="Ghi chú giao dịch"></textarea>
                                    <label>Ghi chú giao dịch</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <select name="status_id" class="form-select choice value validate"
                                        title="Trạng thái">
                                        <option value="">Chọn trạng thái</option>
                                        @foreach (status('order') as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <label class="floating-label-cus">Trạng thái</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <input type="text" placeholder="Thời gian dự kiến" name="du_kien_time"
                                        data-options='{"minDate":"today","dateFormat":"d-m-Y", "locale": "vn", "shorthandCurrentMonth": true}'
                                        class="form-control datetimepicker value empty validate">
                                    <label>Thời gian dự kiến</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 d-none container-xuong">
                                <div class="form-floating">
                                    <select name="factory_id" class="form-select empty choice value" title="Xưởng">
                                        <option value="">Xưởng</option>
                                        @foreach ($factories as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <label class="floating-label-cus">Xưởng</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 d-none container-user">
                                <div class="form-floating">
                                    <select name="user_tk" class="form-select empty choice value" title="Người thiết kế">
                                        <option value="">Người thiết kế</option>
                                        @foreach ($usertk as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <label class="floating-label-cus">Người thiết kế</label>
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
@endsection

@section('script')
    <script>
        function setBankAccountEdit(value) {
            if (value != 0) {
                $("#bank_a_id").removeClass('d-none');
                $("#note_a").removeClass('d-none');
            } else {
                $("#bank_a_id").addClass('d-none');
                $("#note_a").addClass('d-none');
            }
        }
        function setBankAccount(value) {
            if (value != 0) {
                $("#bank_account_id").removeClass('d-none');
                $("#note_giao_dich").removeClass('d-none');
            } else {
                $("#bank_account_id").addClass('d-none');
                $("#note_giao_dich").addClass('d-none');
            }
        }
    </script>
    <script>
        const logModel = new HandleForm('#logModel');
        logModel.setChoice();
        logModel.closeReset();
        logModel.submit = async function(e) {
            e.preventDefault();
            let check = this.checkValidate();
            if (check) {
                this.loading(true);
                let value = this.value().get();
                try {
                    let res = await axios.post('/api/logs', value).then(res => res);
                    if (res.status == 201) {
                        logModel.reset();
                        logModel.hideModal();
                        showMessageMD(res.data.message);
                        if (this.request) {
                            this.request.id = logModel.id;
                            this.request.get()
                        }
                    }
                } catch (error) {
                    let res = error.response;
                    if (res.status == 422) {
                        this.logError(res.data);
                    } else {
                        logModel.hideModal();
                        logModel.reset();
                        showErrorMD('Có lỗi xảy ra, vui lòng thử lại sau!');
                    }
                }
                this.loading(false);
            }
        }
    </script>
    <script>
        const route = '/api/logs?customer_id={{ $customer->id }}&limit=5';
        var request = new RequestServer(route);
        request.colspan = 12;
        request.insert = function(data) {
            return data.map((item, key) => {
                    return `
                <tr class="${request.bold(item.id)}">
                    <td class='align-middle text-start'>${dateTimeFormat(item.created_at,'d-m-Y H:i:s')} - ${Log.info(time_ago(item.created_at))}</td>
                    <td class='align-middle text-start'>${item.category?.name ?? Log.danger('Chưa có')}</td>
                    <td class='align-middle text-start'>${item.from_status?.name ?? Log.danger('Chưa có')}</td>
                    <td class='align-middle text-start'>${item.to_status?.name ?? Log.danger('Chưa có')}</td>
                    <td class='align-middle text-start'>${item.user?.name ?? Log.danger('Không tồn tại')}</td>
                    <td class='align-middle text-start'>${item.content ?? Log.danger('Không có nội dung')}</td>
                </tr>
            `;
                })
                .join('');
        }

        function showMD() {
            const urlParams = new URLSearchParams(window.location.search);
            const modal = urlParams.get('model');
            if (modal && modal == 'show') {
                addModel.showModal();
                urlParams.delete('model');
                window.history.replaceState({}, document.title, window.location.pathname + '?' + urlParams.toString());
            }
        }
        document.addEventListener('DOMContentLoaded', async function() {
            showMD();
            request.get();
            request2.get();
        });

        const routeOrder = '/api/orders?nosearchdate=true&show_all=true&customer_id={{ $customer->id }}'
        var request2 = new RequestServer(routeOrder);
        request2.tbody = 'data_table_body2';
        request2.paginations = '.paginations2';
        request2.colspan = 15;
        request2.insert = function(data) {
            let sumconno = 0;
            let content = data.map((item) => {
                sumconno += item.du_kien - item.thuc_thu;
                    return `
                    <tr class="${request2.bold(item.id)}">
                        <td class='align-middle text-center'>${request2.index++}</td>
                        <td class='align-middle text-start'><a href='/orders/${item.id}'>${item.id}</a></td>
                        <td class='align-middle text-start'>${item.areas ?? Log.danger('Chưa có')}</td>
                        <td class='align-middle text-end'>
                                <span class='fw-bold text-primary'>
                                    ${formatNumber(item.du_kien)} ₫
                                </span>
                            </td>
                            <td class='align-middle text-end'>
                                <span class='fw-bold text-success'>
                                    ${formatNumber(item.thuc_thu)} ₫
                                </span>
                            </td>
                            <td class='align-middle text-end'>
                                <span class='fw-bold text-warning'>
                                    ${formatNumber(item.price_pending)} ₫
                                </span>
                            </td>
                            <td class='align-middle text-end'>
                                <span class='fw-bold' style='color:red'>
                                    ${formatNumber(item.du_kien - item.thuc_thu)} ₫
                                </span>
                            </td>
                            
                        <td class='align-middle text-start'>
                            ${Status.status(item.status)}
                             ${(item.status.id == 9 || item.status.id == 10) ? Status.status(item.current_status) : ''}
                        </td>
                        <td class='align-middle text-start'>${dateTimeFormat(item.du_kien_time) ?? Log.danger('Chưa có')}</td>
                        <td class='align-middle text-start'>${item.user.name}</td>
                        <td class='align-middle text-start'>${dateTimeFormat(item.created_at)}</td>
                        <td class='align-middle text-start'>${item?.note ?? Log.warning('Chưa có')}</td>
                        <td class='align-middle text-center'>
                            <div class='position-relative'>
                                <button onclick='showOne(${item.id})' class='btn btn-edit-show btn-sm btn-phoenix-secondary text-info me-1 fs-10' title='Cập nhật' type='button' data-bs-toggle='modal' data-bs-target='#editModel'>
                                    <span class='fas far fa-edit'></span>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                })
                .join('');

                content += `
                    <tr class='none-data'>
                        <td class='fw-bold text-center'>Tổng công nợ: </td>
                        <td class='fw-bold' colspan='5'></td>
                        <td class='align-middle text-end'>
                            <span class='fw-bold' style='color:red'>
                                ${formatNumber(sumconno)} ₫
                            </span>
                        </td>
                        <td class='fw-bold' colspan='6'></td>
                    </tr>
                `;

            return content;
        }

        let modal = document.getElementById("modalSuccessNotification");
        modal.addEventListener('hide.bs.modal', function() {
            request.get();
            request2.get();
        });

        $('#addModel').on('show.bs.modal', function (event) {
            showOrder();
        });

        var addModel = new HandleForm('#addModel');
        addModel.setChoice();
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
                    let res = await axios.post(routeOrder, value).then(res => res);
                    if (res.status == 201) {
                        addModel.reset();
                        addModel.hideModal();
                        showMessageMD(res.data.message);
                        request2.id = res.data.id;
                        request2.get();
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
        async function showOrder() {
            let res = await axios.get('/api/customers/{{ $customer->id }}').then(res => res);
            if (res.status == 200) {
                VN.show(res.data, addModel);
                addModel.showValue(res.data);
            }
        }

        var editModel = new HandleForm('#editModel');
        editModel.closeReset();
        editModel.setChoice();
        async function showOne(id) {
            try {
                let fetch = await axios.get(`/api/orders/${id}`).then(res => res);
                if (fetch.status == 200) {
                    fetch.data.user_tk = fetch.data.rela;
                    fetch.data.factory_id = fetch.data.rela;
                    editModel.showValue(fetch.data,{
                        price: ['du_kien'],
                        date: ['du_kien_time']
                    });
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
        editModel.submit = async function(e) {
            e.preventDefault();
            let check = this.checkValidate();
            if (check) {
                this.loading(true);
                let value = this.value().formatPrice(['du_kien','thu_them']).get();
                try {
                    let res = await axios.put(`/api/orders/${editModel.id}`, value).then(res => res);
                    if (res.status == 200) {
                        editModel.reset();
                        editModel.hideModal();
                        showMessageMD(res.data.message);
                        request2.id = res.data.id;
                        request2.get();
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
    </script>
    
    <script>
        function handleSelectStatus(modal, id) {
            let xuong = modal.form.querySelector('.container-xuong');
            let user = modal.form.querySelector('.container-user');
            switch (id) {
                case 10: {
                    xuong.classList.remove('d-none');
                    xuong.querySelector('select').classList.add('validate');
                    user.classList.add('d-none');
                    user.querySelector('select').classList.remove('validate');
                    break;
                }
                case 9: {
                    user.classList.remove('d-none');
                    user.querySelector('select').classList.add('validate');
                    xuong.classList.add('d-none');
                    xuong.querySelector('select').classList.remove('validate');
                    break;
                }
                default: {
                    user.classList.add('d-none');
                    user.querySelector('select').classList.remove('validate');
                    xuong.classList.add('d-none');
                    xuong.querySelector('select').classList.remove('validate');
                }
            }
        }
        addModel.form.querySelector('[name="status_id"]').addEventListener('addItem', function() {
            let id = parseInt(this.value);
            handleSelectStatus(addModel,id);
        });
        editModel.form.querySelector('[name="status_id"]').addEventListener('addItem', function() {
            let id = parseInt(this.value);
            handleSelectStatus(editModel,id);
        });
    </script>
@endsection
