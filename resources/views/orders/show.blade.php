@extends('layouts.app')

@section('title')
    Chi tiết đơn hàng
@endsection

@section('content')
    <div class="content">
        <nav class="mb-2" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="/orders">Danh sách đơn hàng</a></li>
                <li class="breadcrumb-item">Chi tiết đơn hàng</li>
            </ol>
        </nav>
        <h3 class="text-bold text-body-emphasis mb-5">Chi tiết đơn hàng</h3>
        <div>
            <div class="row">
                <div class="col-sm-6 col-md-3 mb-4">
                    <h5>Tên khách hàng</h5>
                    <i>{{ $order->customer->name }}</i>
                </div>
                <div class="col-sm-6 col-md-3 mb-4">
                    <h5>Khu vực</h5>
                    <i>{{ $order->category->name ?? 'Chưa có' }}</i>
                </div>
                <div class="col-sm-6 col-md-3 mb-4">
                    <h5>Mã đơn hàng</h5>
                    <i>{{ $order->id }}</i>
                </div>
                <div class="col-sm-6 col-md-3 mb-4">
                    <h5>Khu vực</h5>
                    <i>{{ $order->getAreasAttribute() }}</i>
                </div>
                <div class="col-sm-6 col-md-3 mb-4">
                    <h5>Nhân viên phụ trách</h5>
                    <i>{{ optional($order->user)->name ?? 'Chưa có' }}</i>
                </div>
                <div class="col-sm-6 col-md-3 mb-4">
                    <h5>Ngày hoàn thành</h5>
                    <i>{{ $order->finish_at ? dateFormat($order->finish_at, 'd-m-Y') : 'Chưa hoàn thành' }}</i>
                </div>
                <div class="col-sm-6 col-md-3 mb-4">
                    <h5>Doanh thu dự kiến</h5>
                    <i>{{ number_format($order->du_kien) }} ₫</i>
                </div>
                <div class="col-sm-6 col-md-3 mb-4">
                    <h5>Số tiền đã thu</h5>
                    <i class='da-thu'>{{ number_format($order->thuc_thu) }} ₫</i>
                </div>
                @if ($order->file_latest)
                    <div class="col-sm-6 col-md-3 mb-4">
                        <h5>Hợp đồng</h5>
                        <i><a href="{{ $order->file_latest->url }}" target="_blank">Xem</a></i>
                    </div>
                @endif
            </div>

            <div class="d-flex justify-content-end gap-3 mt-4">
                <button class='btn btn-success' data-bs-toggle="modal" data-bs-target="#addFile">Tải lên hợp đồng</button>
                <button class='btn btn-info' data-bs-toggle="modal" data-bs-target="#addModel">Thêm thanh toán</button>
            </div>

            <!-- Table -->
            <h4 class="my-3">Lịch sử thanh toán</h4>
            <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-y mt-2 position-relative top-1"
                id="list_users_container">
                <div class="table-responsive quote-table-container scrollbar ms-n1 ps-1">
                    <table class="table table-hover table-sm fs-9 mb-0">
                        <thead>
                            <tr>
                                <th class="align-middle text-start text-uppercase">Thời gian</th>
                                <th class="align-middle text-start text-uppercase">Tài khoản kế toán</th>
                                <th class="align-middle text-end text-uppercase">Số tiền</th>
                                <th class="align-middle text-end text-uppercase">Còn lại</th>
                                <th class="align-middle text-start text-uppercase">Người thu</th>
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
                    <span class="fw-bold text-center">{{ $order->customer->name }}</span>
                    <div class='text-end fw-bold'>
                        <span>Số tiền đã thu</span>
                    </div>
                </div>
                <div class="typeTotalStatus mb-3">
                    <span class="fw-bold">Trạng thái</span>
                    <span class="fw-bold text-center">
                        <span class="btn btn-xs btn-round text-white"
                            style="background-color:{{ $order->status->bg }};color:{{ $order->status->color }} !important">{{ $order->status->name }}
                        </span>
                        <br>
                    </span>
                    <div class='text-end fw-bold'>
                        <span class="total-order text-success fs-7 da-thu"
                            id="total">{{ number_format($order->thuc_thu) }}
                            ₫</span>
                    </div>
                </div>
                <p><span class='fw-bold'>Ghi chú:
                    </span>{{ $order->updateLogLastest ? $order->updateLogLastest->note : 'Chưa có ghi chú' }}</p>
            </div>

            <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-y mt-2 position-relative top-1"
                id="list_users_container">
                <div class="table-responsive quote-table-container scrollbar ms-n1 ps-1">
                    <table class="table-config table table-hover table-sm fs-9 mb-0">
                        <thead>
                            <tr>
                                <th class="align-middle text-star text-uppercase">Doanh thu dự kiến</th>
                                <th class="align-middle text-start text-uppercase">Đã thu</th>
                                <th class="align-middle text-start text-uppercase">Còn lại</th>
                                <th class="align-middle text-end text-uppercase">Chi phí</th>
                                <th class="align-middle text-end text-uppercase">Lợi nhuận</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td data-label="DOANH THU DỰ KIẾN" class='align-middle text-start fw-bold'>
                                    <div>
                                        <span class='fw-bold text-info'>{{ number_format($order->du_kien) }} ₫</span>
                                    </div>
                                </td>
                                <td data-label="ĐÃ THU" class='align-middle text-start fw-bold'>
                                    <div>
                                        <span class='fw-bold text-success'>
                                            {{ number_format($order->thuc_thu) }} ₫
                                        </span>
                                    </div>
                                </td>
                                <td data-label="CÒN LẠI" class='align-middle text-start fw-bold'>
                                    <div>
                                        <span class='fw-bold text-danger'>
                                            {{ number_format($order->du_kien - $order->thuc_thu) }} ₫
                                        </span>
                                    </div>
                                </td>
                                @php
                                $chiphi = $order->expenses()->sum('amount');
                                @endphp
                                <td data-label="CHI PHÍ" class='align-middle text-end fw-bold'>
                                    <div>
                                        <span class='fw-bold text-warning'>
                                            {{ number_format($chiphi) }} ₫
                                        </span>
                                    </div>
                                </td>
                                <td data-label="LỢI NHUẬN" class='align-middle text-end fw-bold'>
                                    <div>
                                        <span class='fw-bold text-success'>
                                            {{ number_format($order->thuc_thu - $chiphi) }} ₫
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <h4>Chi tiết chi phí</h4>
            <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-y mt-2 position-relative top-1"
                id="list_users_container">
                <div class="table-responsive quote-table-container scrollbar ms-n1 ps-1">
                    <table class="table table-hover table-sm fs-9 mb-0">
                        <thead>
                            <tr>
                                <th class="align-middle text-start text-uppercase">thời gian</th>
                                <th class="align-middle text-start text-uppercase">Loại chi phí</th>
                                <th class="align-middle text-end text-uppercase">Số tiền</th>
                                <th class="align-middle text-start text-uppercase">Người thêm</th>
                                <th class="align-middle text-start text-uppercase">Ghi chú</th>
                            </tr>
                        </thead>

                        <tbody class="list-data" id="data_table_body3">
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
            </div>

            <h4>Lịch sử cập nhật</h4>
            <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-y mt-2 position-relative top-1"
                id="list_users_container">
                <div class="table-responsive quote-table-container scrollbar ms-n1 ps-1">
                    <table class="table table-hover table-sm fs-9 mb-0">
                        <thead>
                            <tr>
                                <th class="align-middle text-start text-uppercase">Thời gian</th>
                                <th class="align-middle text-start text-uppercase">Cập nhật</th>
                                <th class="align-middle text-start text-uppercase">Từ</th>
                                <th class="align-middle text-start text-uppercase">Tới</th>
                                <th class="align-middle text-start text-uppercase">Người cập nhật</th>
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
        </div>
    </div>
    <div class="modal fade" id="addFile" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-dialog">
                <div class="modal-content form-open">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Tải lên hợp đồng</h5>
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
                        <form class="row g-3" method="POST" enctype="multipart/form-data">
                            <div class="col-sm-12 col-md-12">
                                <div class="input-group mb-3">
                                    <input type="file" class="form-control value validate empty" name='file'
                                        id="hop_dong" accept="image/*">
                                    <label class="input-group-text" for="hop_dong">Hợp đồng</label>
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
    <div class="modal fade" id="addModel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-dialog">
                <div class="modal-content form-open">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Thêm mới thanh toán</h5>
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
                            <div class="col-sm-12 col-md-12">
                                <div class="form-floating">
                                    <select name="bank_account_id" class="form-select validate value choice"
                                        title="Tài khoản nhận">
                                        @foreach ($account as $item)
                                            <option value="{{ $item->id }}">{{ $item->bank->name }} -
                                                {{ $item->full_name }} -
                                                {{ $item->bank_number }}</option>
                                        @endforeach
                                    </select>
                                    <label class='floating-label-cus'>Tài khoản nhận</label>
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
                                    <textarea name="note" class="form-control empty value" style="height: 100px;" placeholder="Ghi chú"></textarea>
                                    <label class='floating-label-cus'>Ghi chú</label>
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
@endsection

@section('script')
    <script>
        var addFile = new HandleForm('#addFile');
        addFile.setChoice();
        addFile.submit = async function(e) {
            e.preventDefault();
            let check = this.checkValidate();
            if (check) {
                this.loading(true);
                const file = document.getElementById('hop_dong').files[0];
                if (file) {
                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('order_id', '{{ $order->id }}');
                    try {
                        let res = await axios.post(`/api/order-files`, formData).then(res => res);
                        if (res.status == 201) {
                            addFile.reset();
                            addFile.hideModal();
                            showMessageMD(res.data.message);
                            addFile.redirectMD(`/orders/{{ $order->id }}`);
                        }
                    } catch (error) {
                        let res = error.response;
                        if (res.status == 422) {
                            this.logError(res.data);
                        } else {
                            showErrorMD(res.data.message);
                            addFile.hideModal();
                            addFile.reset();
                        }
                    }
                }
                this.loading(false);
            }
        }
    </script>
    <script>
        const route = '/api/order-logs?order_id={{ $order->id }}&show_all=true&searchnone=true&orderby=asc';
        var request = new RequestServer(route);
        request.colspan = 12;
        request.insert = function(data) {
            $('#chuaduyet').html(formatCurrency(request.response.chuaDuyet));
            let cndk = {{ $order->du_kien }};
            let cndkbn = cndk;
            let content = `
                <tr class='fw-bold none-data'>
                    <td class='align-middle text-start'>Công nợ đầu kì<td>
                    <td></td>
                    <td class='align-middle text-end'>
                        <span class='fw-bold text-info'>
                            ${formatCurrency(cndk)}
                        </span>
                    <td>
                    <td colspan='2'></td>
                </tr>
            `;
            content += data.map((item, key) => {
                    let cl = Log.warning('Chưa được duyệt!');
                    if (item.status == 2) {
                        cndk = cndk - item.amount;
                        cl = formatCurrency(cndk);
                    }
                    return `
                        <tr class="${request.bold(item.id)}">
                            <td class='align-middle text-start'>${dateTimeFormat(item.created_at,'d-m-Y H:i:s')}</td>
                            <td class='align-middle text-start'>
                                <div>
                                    <span>Ngân hàng: ${item.bank_account?.bank?.name}</span>
                                    <br>
                                    <span>Tên tài khoản: ${item.bank_account?.full_name}</span>
                                    <br>
                                    <span>Số tài khoản: ${item.bank_account?.bank_number}</span>
                                </div>
                            </td>
                            <td class='align-middle text-end fw-bold'>
                                <span class='text-${item.amount < 0 ? 'danger' : 'info'}'>
                                    ${formatCurrency(item.amount)}
                                </span>
                            </td>
                            <td class='align-middle text-end'>
                                <span class='fw-bold' style='color:red'>
                                    ${cl}
                                </span>
                            </td>
                            <td class='align-middle text-start'>${item.user?.name}</td>
                            <td class='align-middle text-start'>${item.note}</td>
                        </tr>
                    `;
                })
                .join('');
            content += `
                <tr class='fw-bold none-data'>
                    <td class='align-middle text-start'>Công nợ hiện tại<td>
                    <td></td>
                    <td class='align-middle text-end'>
                        <span class='fw-bold' style='color:${cndk >= 0 ? 'red' : 'green'}'>
                            ${formatCurrency(cndk)}
                        </span>
                        
                    <td>
                    <td colspan='2'></td>
                </tr>
            `;
            $('.da-thu').html(formatCurrency(cndkbn - cndk));
            return content;
        }
        var request2 = new RequestServer('/api/order-update-logs?order_id={{ $order->id }}&limit=10');
        request2.colspan = 12;
        request2.tbody = "data_table_body2";
        request2.paginations = ".paginations2";
        request2.insert = function(data) {
            return data.map((item, key) => {
                    return `
                <tr class="${request2.bold(item.id)}">
                    <td class='align-middle text-start'>${dateTimeFormat(item.created_at,'d-m-Y H:i:s')}</td>
                    <td class='align-middle text-start'>${item.type ?? Log.danger('Không rõ')}</td>
                    <td class='align-middle text-start'>${item.from ?? Log.info('Chưa có')}</td>
                    <td class='align-middle text-start'>${item.to ?? Log.info('Chưa có')}</td>
                    <td class='align-middle text-start'>${item.user?.name}</td>
                </tr>
            `;
                })
                .join('');
        }
        document.addEventListener('DOMContentLoaded', async function() {
            request.get();
            request2.get();
            request3.get();
        });

        var addModel = new HandleForm('#addModel');
        addModel.setChoice();
        addModel.submit = async function(e) {
            e.preventDefault();
            let check = this.checkValidate();
            if (check) {
                this.loading(true);
                let value = this.value().formatPrice(['amount']).get();
                value.order_id = '{{ $order->id }}';
                try {
                    let res = await axios.post(`/api/order-logs`, value).then(res => res);
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
        var request3 = new RequestServer('/api/order-expenses');
        request3.tbody = 'data_table_body3'
        request3.colspan = 12;
        request3.insert = function(data) {
            let total = 0;
            let content = data.map((item, key) => {
                    total += parseFloat(item.amount);
                    return `
                        <tr class="${request3.bold(item.id)}">
                            <td class='align-middle text-start'>${dateTimeFormat(item.created_at,'d-m-Y H:i:s')} - ${Log.info(time_ago(item.created_at))}</td>
                            <td class='align-middle text-start'>${item.type?.name ?? Log.danger('Không rõ')}</td>
                            <td class='align-middle text-end fw-bold'>
                                ${Text.info(formatCurrency(item.amount))}
                            </td>
                            <td class='align-middle text-starts'>${item.user?.name}</td>
                            <td class='align-middle text-start'>${item.note || Log.info('Không có ghi chú')}</td>
                        </tr>
                    `;
                })
                .join('');
            content += `
                <tr class='fw-bold'>
                    <td class='align-middle text-start'>Tổng:</td>
                    <td></td>
                    <td class='align-middle text-end'>${Text.danger(formatCurrency(total))}</td>
                </tr>
            `;
            return content;
        }
    </script>
@endsection
