@extends('layouts.app')

@section('title')
    Khoản tiền đã thu theo kỳ
@endsection

@section('content')
    <div class="content">
        <nav class="mb-2" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                <li class="breadcrumb-item">Loại giao dịch</li>
            </ol>
        </nav>
        <h3 class="text-bold text-body-emphasis mb-5">Khoản tiền đã thu theo kỳ</h3>
        <div>
            <div class="mb-3 thongke">
                <div>
                    <div class="icon">
                        <div class="icon-border bg-primary-subtle">
                            <i class="fab fa-codepen text-primary-emphasis"></i>
                        </div>
                    </div>
                    <div class="body">
                        <p class="fw-bold m-0"><span class="thongke-sum" id="sum">0</span> giao dịch</p>
                        <span class="fs-8 fw-bold text-body-highlight">Tổng số</span>
                    </div>
                </div>
                <div>
                    <div class="icon">
                        <div class="icon-border bg-primary-subtle">
                            <i class="fab fa-codepen text-primary-emphasis"></i>
                        </div>
                    </div>
                    <div class="body">
                        <p class="fw-bold m-0"><span class="thongke-sum" id="sum-price">0</span></p>
                        <span class="fs-8 fw-bold text-body-highlight">Tổng số tiền đã thu</span>
                    </div>
                </div>
            </div>
            <!-- Search -->
            <div id="searchModel" class="d-none">
                <form class="d-flex align-items-center gap-3 flex-wrap mb-4" id="filter-form">
                    <div>
                        <input type="text" name="dates" style="min-width: 350px" class="form-control value empty"
                            placeholder="Ngày hoàn thành">
                    </div>
                    <div>
                        <input name="name" placeholder="Tên khách hàng" type="text" class="form-control value empty">
                    </div>
                    <div>
                        <input name="phone" placeholder="Số điện thoại" type="text" class="form-control value empty">
                    </div>
                    <div>
                        <select name="user_id" class="form-select value empty choice">
                            <option value="">Nhân sự</option>
                            @foreach ($users as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="group_id" class="form-select value empty choice">
                            <option value="">Nhóm nhân sự</option>
                            @foreach ($groups as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex justify-content-start">
                        <button type="submit" class="btn btn-sm btn-phoenix-info btn-filter me-2" title="Lọc">
                            <span class="fas fa-filter text-info fs-9 me-2"></span>Lọc
                        </button>
                        <button button class="btn btn-sm btn-phoenix-warning" onclick="removeFilter(this)"
                            type="button">Xoá
                            lọc</button>
                    </div>
                </form>
            </div>
            <!-- Table -->
            <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-y mt-2 position-relative top-1"
                id="list_users_container">
                <div class="table-responsive quote-table-container scrollbar ms-n1 ps-1">
                    <table class="table table-hover table-sm fs-9 mb-0">
                        <thead>
                            <tr>
                                <th class="align-middle text-start text-uppercase">Thời gian</th>
                                <th class="align-middle text-start text-uppercase">Đơn hàng</th>
                                <th class="align-middle text-end text-uppercase">Số tiền</th>
                                <th class="align-middle text-start text-uppercase">Tài khoản nhận</th>
                                <th class="align-middle text-start text-uppercase">Người tạo</th>
                                <th class="align-middle text-start text-uppercase">Nhóm nhân sự</th>
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
        </div>
    </div>
@endsection

@section('script')
    <script>
        const route = '/api/order-logs?status=2';
        var request = new RequestServer(route);
        var searchModal = new HandleForm('#searchModel');
        searchModal.setChoice();
        request.colspan = 12;
        request.insert = function(data) {
            $("#sum").text(request.response.total);
            $("#sum-price").text(formatCurrency(request.response.tongTien));
            let total = 0;
            let content = data.map((item) => {
                total += parseFloat(item.amount);
                    return `
                        <tr class="${request.bold(item.id)}">
                            <td class='align-middle text-start'>${dateTimeFormat(item.created_at,'d-m-Y H:i:s')}</td>
                            <td class='align-middle text-start'>
                                <div>
                                    <span>Mã ĐH: <a href='/orders/${item.order?.id}'>${item.order?.id}</a></span><br>
                                    <span>Tên khách hàng: <a href='/customers/${item.order?.customer?.id}'>${item.order.customer?.name}</a></span><br>
                                    <span>Số điện thoại: <a href='/customers/${item.order?.customer?.id}'>${item.order.customer?.phone}</a></span><br>    
                                </div>
                            </td>
                            <td class='align-middle text-end'>${Text.danger(formatCurrency(item.amount))}</td>
                            <td class='align-middle text-start'>
                                <div>
                                    <span>Ngân hàng: ${item.bank_account?.bank?.name}</span><br>
                                    <span>Số tài khoản: <a href='/bank-accounts/${item.bank_account?.id}'>${item.bank_account?.bank_number}</a></span><br>
                                    <span>Tên tài khoản: ${item.bank_account?.full_name}</span>    
                                </div>
                            </td>
                            <td class='align-middle text-start'>${item.user?.name}</td>
                            <td class='align-middle text-start'>${item.user?.group?.name}</td>
                            <td class='align-middle text-start'>${item.note}</td>
                        </tr>
                        `;
                })
                .join('');
            content += `
                <tr class='none-data fw-bold'>
                    <td class='align-middle text-start' colspan='2'>TỔNG:</td>
                    <td class='align-middle text-end'>${Text.info(formatCurrency(total))}</td>
                    <td class='align-middle text-start' colspan='5'></td>
                </tr>
            `;
            return content;
        }
        document.addEventListener('DOMContentLoaded', async function() {
            await request.get();
            searchModal.showValue(request.params);
        });
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
    </script>
@endsection
