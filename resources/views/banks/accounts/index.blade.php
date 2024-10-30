@extends('layouts.app')

@section('title')
    Tài khoản ngân hàng
@endsection

@section('content')
    <div class="content">
        <nav class="mb-2" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                <li class="breadcrumb-item">Tài khoản ngân hàng</li>
            </ol>
        </nav>
        <h2 class="text-bold text-body-emphasis mb-5">Danh sách tài khoản ngân hàng</h2>
        <div>
            <div class="mb-3 thongke">
                <div>
                    <div class="icon">
                        <div class="icon-border bg-primary-subtle">
                            <i class="fab fa-codepen text-primary-emphasis"></i>
                        </div>
                    </div>
                    <div class="body">
                        <p class="fw-bold m-0"><span class="thongke-sum" id='sum'>0</span> tài khoản</p>
                        <span class="fs-8 fw-bold text-body-highlight">Tổng số</span>
                    </div>
                </div>
                <div>
                    <div class="icon">
                        <div class="icon-border bg-success-subtle">
                            <i class="fab fa-codepen text-success-emphasis"></i>
                        </div>
                    </div>
                    <div class="body">
                        <p class="fw-bold m-0"><span class="thongke-sum-price">0</span></p>
                        <span class="fs-8 fw-bold text-body-highlight">Tổng số dư hiện tại</span>
                    </div>
                </div>
            </div>

            <!-- Search -->
            <div id="searchModel">
                <form class="d-flex align-items-center gap-3 flex-wrap mb-4" id="filter-form">
                    <div>
                        <input name="full_name" placeholder="Tên tài khoản" type="text"
                            class="form-control value empty">
                    </div>
                    <div>
                        <input name="bank_number" placeholder="Số tài khoản" type="text"
                            class="form-control value empty">
                    </div>
                    <div>
                        <select name="bank_id" class="form-select value empty choice">
                            <option value="">Ngân hàng</option>
                            @foreach ($banks as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="bank_account_type_id" class="form-select value empty choice">
                            <option value="">Loại tài khoản</option>
                            @foreach ($bank_type as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
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
            <div class="d-flex justify-content-end align-items-center">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModel">Thêm mới</button>
            </div>
            <!-- Table -->
            <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-y mt-2 position-relative top-1"
                id="list_users_container">
                <div class="table-responsive quote-table-container scrollbar ms-n1 ps-1">
                    <table class="table table-hover table-sm fs-9 mb-0">
                        <thead>
                            <tr>
                                <th class="align-middle text-center text-uppercase">stt</th>
                                <th class="align-middle text-start text-uppercase">ngân hàng</th>
                                <th class="align-middle text-start text-uppercase">số tài khoản</th>
                                <th class="align-middle text-start text-uppercase">tên tài khoản</th>
                                <th class="align-middle text-end text-uppercase">số dư hiện tại</th>
                                <th class="align-middle text-start text-uppercase">loại tài khoản</th>
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
    const route = '/api/bank-account';
    var request = new RequestServer(route);
    var searchModal = new HandleForm('#searchModel');
    request.colspan = 12;
    request.insert = function(data) {
        $("#sum").text(request.response.total);
        $('.thongke-sum-price').text(formatCurrency(request.response.sum));
        let total = 0;
        let content = data.map((item) => {
            total += parseFloat(item.current_balance);
                return `
                    <tr class="${request.bold(item.id)}">
                        <td class='align-middle text-center'>${request.index++}</td>
                        <td class='align-middle text-start'>${item.bank?.name}</td>
                        <td class='align-middle text-start'><a href='/bank-accounts/${item.id}'>${item.bank_number}</a></td>
                        <td class='align-middle text-start'>${item.full_name}</td>
                        <td class='align-middle text-end'>${Text.info(formatCurrency(item.current_balance))}</td>
                        <td class='align-middle text-start'>${item.bank_account_type?.name}</td>
                    </tr>
                    `;
            })
            .join('');
        content += `
            <tr class='fw-bold align-middle none-data'>
                <td class='text-center'>TỔNG: </td>
                <td colspan='3'></td>
                <td class='text-end'>${Text.success(formatCurrency(total))}</td>
                <td colspan='2'></td>
            </tr>
        `;
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
        btnLoading(btn, false, 'Xóa lọc');
    }
</script>
@endsection