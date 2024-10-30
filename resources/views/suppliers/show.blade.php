@extends('layouts.app')

@section('title')
    Chi tiết nhà cung cấp
@endsection

@section('content')
    <div class="content">
        <nav class="mb-2" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="/suppliers">Danh sách nhà cung cấp</a></li>
                <li class="breadcrumb-item">Chi tiết nhà cung cấp</li>
            </ol>
        </nav>
        <h3 class="text-bold text-body-emphasis mb-5">Chi tiết nhà cung cấp</h3>
        <div>
            <div class="row">
                <div class="col-sm-6 col-md-2 mb-4">
                    <h5>Tên nhà cung cấp</h5>
                    <i>{{ $supplier->name }}</i>
                </div>
                <div class="col-sm-6 col-md-2 mb-4">
                    <h5>Mã nhà cung cấp</h5>
                    <i>{{ $supplier->code }}</i>
                </div>
                <div class="col-sm-6 col-md-2 mb-4">
                    <h5>Loại nhà cung cấp</h5>
                    <i>{{ optional($supplier->type)->name ?? 'Chưa có' }}</i>
                </div>
                <div class="col-sm-6 col-md-2 mb-4">
                    <h5>Công nợ đầu kỳ</h5>
                    <i>{{ number_format($supplier->opening_amount) }} ₫</i>
                </div>
                <div class="col-sm-6 col-md-2 mb-4">
                    <h5>Công nợ hiện tại</h5>
                    <i id="total-hientai">{{ number_format($supplier->current_amount) }} ₫</i>
                </div>
                <div class="col-sm-6 col-md-2 mb-4">
                    <h5>Người tạo</h5>
                    <i>{{ $supplier->user->name ?? 'Chưa có' }}</i>
                </div>
            </div>
            <h4>Lịch sử thay đổi công nợ nhà cung cấp</h4>
            <div id="searchModel">
                <form class="d-flex align-items-center gap-3 flex-wrap my-3" id="filter-form">
                    <div>
                        <input name="dates" style="width: 350px" type="text" placeholder="Thời gian"
                            class="form-control empty value">
                    </div>
                    <div>
                        <input type="text" name="note" class='form-control empty value' placeholder="Ghi chú">
                    </div>
                    <div>
                        <input name="amount_min" type="text" placeholder="Số tiền tối thiểu"
                            class="form-control empty value" oninput="formatBalance(event)">
                    </div>
                    <div>
                        <select name="type" class='form-select empty value choice'>
                            <option value="">Loại</option>
                            <option value="7">Thanh toán công nợ nhà cung cấp</option>
                            <option value="16">Tăng công nợ nhà cung cấp</option>
                        </select>
                    </div>
                    <div>
                        <input name="amount_max" type="text" placeholder="Số tiền tối đa"
                            class="form-control empty value" oninput="formatBalance(event)">
                    </div>
                    <div class=" d-flex justify-content-start">
                        <button type="submit" class="btn btn-sm btn-phoenix-info btn-filter me-2" title="Lọc">
                            <span class="fas fa-filter text-info fs-9 me-2"></span>Lọc
                        </button>
                        <button button class="btn btn-sm btn-phoenix-warning" onclick="removeFilter(this)"
                            type="button">Xoá lọc</button>
                    </div>
                </form>
            </div>
            <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-y mt-2 position-relative top-1"
                id="list_users_container">
                <div class="table-responsive quote-table-container scrollbar ms-n1 ps-1">
                    <table class="table table-hover table-sm fs-9 mb-0">
                        <thead>
                            <tr>
                                <th class="align-middle text-start text-uppercase">Thời gian</th>
                                <th class="align-middle text-end text-uppercase">Số tiền</th>
                                <th class="align-middle text-end text-uppercase">Công nợ sau thực hiện</th>
                                <th class="align-middle text-start text-uppercase">Người thực hiện</th>
                                <th class="align-middle text-start text-uppercase">Ghi chú</th>
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
            <!-- Table -->
            <!-- Table -->
        </div>
    </div>
    <div class="modal fade" id="addAmount" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-dialog">
                <div class="modal-content form-open">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Tăng công nợ nhà cung cấp</h5>
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
                                    <input type="text" name="amount" oninput="formatBalance(event)"
                                        class="form-control validate empty value" placeholder="Số tiền">
                                    <label>Số tiền</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <div class="form-floating">
                                    <textarea name="note" style="height: 100px" class='form-control value empty' placeholder="Ghi chú"></textarea>
                                    <label>Ghi chú</label>
                                </div>
                            </div>
                            <div class="col-12 gy-6">
                                <div class="row g-3 justify-content-center">
                                    <div class="col-auto">
                                        <button type="button" onclick="addAmount.reset()"
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

    <div class="modal fade" id="addThanhToan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-dialog">
                <div class="modal-content form-open">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Thanh toán công nợ nhà cung cấp</h5>
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
                                    <input type="text" name="amount" oninput="formatBalance(event)"
                                        class="form-control validate empty value" placeholder="Số tiền">
                                    <label>Số tiền</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="fee" oninput="formatBalance(event)"
                                        class="form-control validate set-0 value" value="0"
                                        placeholder="Phí chuyển tiền">
                                    <label>Phí chuyển tiền</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <div class="form-floating">
                                    <select name="bank_account" class="form-select choice value validate"
                                        title="Tài khoản thanh toán">
                                        <option value="" hidden>Tài khoản thanh toán</option>
                                    </select>
                                    <label class="floating-label-cus">Tài khoản thanh toán</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <div class="form-floating">
                                    <textarea name="note" style="height: 100px" class='form-control value empty' placeholder="Ghi chú"></textarea>
                                    <label>Ghi chú</label>
                                </div>
                            </div>
                            <div class="col-12 gy-6">
                                <div class="row g-3 justify-content-center">
                                    <div class="col-auto">
                                        <button type="button" onclick="addThanhToan.reset()"
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
        var searchModal = new HandleForm('#searchModel');
        searchModal.setChoice();
        searchModal.submit = async function(e) {
            e.preventDefault();
            this.loading(true);
            let value = this.value().formatPrice(['amount_min','amount_max']).get();
            request.params = value;
            await request.get();
            this.loading(false, `<span class='fas fa-filter text-info fs-9 me-2'></span>Lọc`);
        }
        async function removeFilter(btn) {
            btnLoading(btn, true);
            searchModal.reset();
            request.params = {};
            await request.get();
            btnLoading(btn, false, 'Xóa lọc');
        }
        document.addEventListener('DOMContentLoaded', async function() {
            await request.get();
            searchModal.reset();
            searchModal.showValue(request.params);
        });
        const route = '/api/supplier-invoices?show_all=true&supplier_id={{ $supplier->id }}'
        var request = new RequestServer(route);
        request.tbody = 'data_table_body';
        request.paginations = '.paginations';
        request.colspan = 15;
        request.insert = function(data) {
            let connocuoiky = 0;
            let connodauky = 0;
            let content = data.map((item, key) => {
                    if (item.tran_type.type == 2) {
                        item.amount = -item.amount
                    }
                    let lastGD = parseFloat(item.current_amount) + parseFloat(item.amount);
                    if(key == 0){
                        connodauky = item.current_amount;
                    }
                    connocuoiky = lastGD;
                    return `
                    <tr class="${request.bold(item.id)}">
                        <td class='align-middle text-start'>${dateTimeFormat(item.created_at,'d-m-Y H:i:s')} - ${Log.info(time_ago(item.created_at))}</td>
                            <td class='align-middle text-end'>
                                <span class='fw-bold ${item.amount < 0 ? 'text-success' : 'text-danger'}'>
                                    ${formatNumber(item.amount)} ₫
                                </span>
                            </td>
                            <td class='align-middle text-end'>
                                <span class='fw-bold' style='color:red'>
                                    ${formatNumber(lastGD)} ₫
                                </span>
                            </td>
                        <td class='align-middle text-start'>${item.user?.name ?? Log.danger('Không rõ')}</td>
                        <td class='align-middle text-start'>${item?.note || Log.warning('Chưa có')}</td>
                    </tr>
                `;
                })
                .join('');
                content = `
                    <tr class='fw-bold none-data'>
                        <td class='text-start align-middle'>Công nợ đầu kỳ: </td>
                        <td></td>
                        <td class='text-end align-middle'>${Text.info(formatCurrency(connodauky))}</td>
                        <td></td>
                        <td></td>
                    </tr>
                ` + content;
                content += `
                    <tr class='fw-bold none-data'>
                        <td class='text-start align-middle'>Công nợ cuối kỳ: </td>
                        <td></td>
                        <td class='text-end align-middle'>${Text.info(formatCurrency(connocuoiky))}</td>
                        <td></td>
                        <td></td>
                    </tr>
                `;
            return content;
        }
    </script>
@endsection
