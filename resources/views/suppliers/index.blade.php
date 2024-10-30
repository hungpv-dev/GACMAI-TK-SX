@extends('layouts.app')

@section('title')
    Danh sách nhà cung cấp
@endsection

@section('content')
    <div class="content">
        <nav class="mb-2" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                <li class="breadcrumb-item">Nhà cung cấp</li>
            </ol>
        </nav>
        <h3 class="text-bold text-body-emphasis mb-5">Danh sách nhà cung cấp</h3>
        <div>
            <div class="mb-3 thongke">
                <div>
                    <div class="icon">
                        <div class="icon-border bg-primary-subtle">
                            <i class="fab fa-codepen text-primary-emphasis"></i>
                        </div>
                    </div>
                    <div class="body">
                        <p class="fw-bold m-0"><span class="thongke-sum" id="sum">0</span> nhà cung cấp</p>
                        <span class="fs-8 fw-bold text-body-highlight">Tổng số</span>
                    </div>
                </div>
                <div>
                    <div class="icon">
                        <div class="icon-border bg-success-subtle">
                            <i class="far fa-money-bill-alt text-success-emphasis"></i>
                        </div>
                    </div>
                    <div class="body">
                        <p class="fw-bold m-0"><span class="thongke-sum" id="sum-price">0</span></p>
                        <span class="fs-8 fw-bold text-body-highlight">Tổng số công nợ</span>
                    </div>
                </div>
            </div>
            <!-- Search -->
            <div id="searchModel" class="d-none">
                <form class="d-flex align-items-center gap-3 flex-wrap mb-4" id="filter-form">
                    <div>
                        <input name="code" placeholder="Mã nhân sự" type="text" class="form-control value empty">
                    </div>
                    <div>
                        <input name="name" placeholder="Tên nhân sự" type="text" class="form-control value empty">
                    </div>
                    <div>
                        <select name="type" id="" class="form-select value empty choice">
                            <option value="">Loại nhà cung cấp</option>
                            @foreach ($types as $item)
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
                                <th class="align-middle text-center text-uppercase">STT</th>
                                <th class="align-middle text-start text-uppercase">Mã NCC</th>
                                <th class="align-middle text-start text-uppercase">Tên NCC</th>
                                <th class="align-middle text-end text-uppercase">Tổng tiền hàng</th>
                                <th class="align-middle text-end text-uppercase">Đã thanh toán</th>
                                <th class="align-middle text-end text-uppercase">Công nợ hiện tại</th>
                                <th class="align-middle text-start text-uppercase">Loại nhà cung cấp</th>
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
        const route = '/api/suppliers';
        var request = new RequestServer(route);
        var searchModal = new HandleForm('#searchModel');
        request.colspan = 12;
        request.insert = function(data) {
            document.querySelector("#sum").textContent = request.response.total;
            document.querySelector("#sum-price").textContent = formatCurrency(request.response.sum);
            let total_cn = 0;
            let content = data.map((item) => {
                total_cn += parseFloat(item.current_amount);
                return `
                    <tr class="${request.bold(item.id)}">
                        <td class='align-middle text-center'>${request.index++}</td>
                        <td class='align-middle text-start'><a href='/suppliers/${item.id}'>${item.code}</a></td>
                        <td class='align-middle text-start'>${item.name}</td>
                        <td class='align-middle text-end'>${Text.warning(formatCurrency(item.sum_price_product))}</td>
                        <td class='align-middle text-end'>${Text.success(formatCurrency(item.sum_price_product - item.current_amount))}</td>
                        <td class='align-middle text-end'>${item.current_amount < 0 ? Text.danger(formatCurrency(item.current_amount)) : Text.info(formatCurrency(item.current_amount))}</td>
                        <td class='align-middle text-start'>${item.type ? Log.info(item.type?.name) : Log.danger('Không rõ')}</td>
                        <td class='align-middle text-start'>${item.note || Log.info('Không có ghi chú')}</td>
                    </tr>
                    `;
                })
                .join('');
            content += `
                <tr class='fw-bold align-middle none-data'>
                    <td class='text-center'>TỔNG: </td>
                    <td colspan='4'></td>
                    <td class='text-end'>${Text.info(formatCurrency(total_cn))}</td>
                    <td colspan='3'></td>
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
