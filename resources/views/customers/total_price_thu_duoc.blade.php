@extends('layouts.app')

@section('title')
    Số tiền thu được
@endsection

@section('content')
    <div class="content">
        <nav class="mb-2" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                <li class="breadcrumb-item">Số tiền thu được</li>
            </ol>
        </nav>
        <h3 class="text-bold text-body-emphasis mb-5">Danh sách số tiền thu được</h3>
        <div>
            <div class="mb-3 thongke">
                <div>
                    <div class="icon">
                        <div class="icon-border bg-primary-subtle">
                            <i class="fab fa-codepen text-primary-emphasis"></i>
                        </div>
                    </div>
                    <div class="body">
                        <p class="fw-bold m-0"><span class="thongke-sum">0</span></p>
                        <span class="fs-8 fw-bold text-body-highlight">Tổng số tiền đã thu</span>
                    </div>
                </div>
                <div>
                    <div class="icon">
                        <div class="icon-border bg-warning-subtle">
                            <i class="fab fa-codepen text-warning-emphasis"></i>
                        </div>
                    </div>
                    <div class="body">
                        <p class="fw-bold m-0"><span class="thongke-choduyet">0</span></p>
                        <span class="fs-8 fw-bold text-body-highlight">Tổng số tiền chờ duyệt</span>
                    </div>
                </div>
            </div>
            <!-- Search -->
            <div id="searchModel" class="d-none">
                <form class="d-flex align-items-center gap-3 flex-wrap mb-4" id="filter-form">
                    <div>
                        <input type="text" name="dates" class="form-control value empty">
                    </div>
                    <div>
                        <select name="customer_id" class="form-select value empty choice">
                            <option value="">Khách hàng</option>
                            @foreach ($customers as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="bank_account_id" class="form-select value empty choice">
                            <option value="">Tài khoản ngân hàng</option>
                            @foreach ($bank_accounts as $item)
                                <option value="{{ $item->id }}">{{ $item->bank->name }} - {{ $item->full_name }} - {{ $item->bank_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex justify-content-start">
                        <button type="submit" class="btn btn-lg btn-phoenix-info btn-filter me-2" title="Lọc">
                            <span class="fas fa-filter text-info fs-9 me-2"></span>Lọc
                        </button>
                        <button button class="btn btn-lg btn-phoenix-warning" onclick="removeFilter(this)"
                            type="button">Xoá lọc</button>
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
                                <th class="align-middle text-start text-uppercase">thời gian</th>
                                <th class="align-middle text-start text-uppercase">khách hàng</th>
                                <th class="align-middle text-start text-uppercase">đơn hàng</th>
                                <th class="align-middle text-end text-uppercase">số tiền</th>
                                <th class="align-middle text-start text-uppercase">tài khoản ngân hàng</th>
                                <th class="align-middle text-start text-uppercase">trạng thái</th>
                                <th class="align-middle text-start text-uppercase">ghi chú</th>
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
        var request = new RequestServer('/api/order-logs');
        var searchModal = new HandleForm('#searchModel');
        request.colspan = 12;
        request.insert = function(data) {
            $('.thongke-sum').text(formatCurrency(request.response.tongTien));
            $('.thongke-choduyet').text(formatCurrency(request.response.chuaDuyet));
            return data.map((item) => {
                    return `
                        <tr>
                            <td class='align-middle text-start'>${dateTimeFormat(item.created_at,'d-m-Y H:i:s')} ${Log.info(time_ago(item.created_at))}</td>
                            <td class='align-middle text-start'><a href='/customers/${item.order?.customer_id}'>${item.order?.customer?.name}</a></td>
                            <td class='align-middle text-start'><a href='/orders/${item.order_id}'>${item.order_id}</a></td>
                            <td class='align-middle text-end'>
                                <span class='fw-bold ${item.amount < 0 ? 'text-danger' : 'text-info'}'>
                                    ${formatCurrency(item.amount)}
                                </span>
                            </td>
                            <td class='align-middle text-start'>
                                <div>
                                    <span>Ngân hàng: ${item.bank_account?.bank?.name}</span>
                                    <br>
                                    <span>Tên tài khoản: ${item.bank_account?.full_name}</span>
                                    <br>
                                    <span>Số tài khoản: ${item.bank_account?.bank_number}</span>    
                                </div>
                            </td>
                            <td class='align-middle text-start'>${Status.order_log(item.status)}</td>
                            <td class='align-middle text-start'>${item.note}</td>
                        </tr>
                    `;
                })
                .join('');
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
    </script>
@endsection
