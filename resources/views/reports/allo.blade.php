@extends('layouts.app')

@section('title')
    Chi tiết chi phí phân bổ
@endsection

@section('content')
    <div class="content">
        <nav class="mb-2" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                <li class="breadcrumb-item">Phân bổ chi phí</li>
            </ol>
        </nav>
        <h3 class="text-bold text-body-emphasis mb-5">Chi tiết chi phí phân bổ</h3>
        <div>
            <div class="mb-3 thongke">
                <div>
                    <div class="icon">
                        <div class="icon-border bg-primary-subtle">
                            <i class="fab fa-codepen text-primary-emphasis"></i>
                        </div>
                    </div>
                    <div class="body">
                        <p class="fw-bold m-0"><span class="thongke-sum" id="sum">0</span> bản ghi</p>
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
                        <span class="fs-8 fw-bold text-body-highlight">Tổng số tiền</span>
                    </div>
                </div>
            </div>
            <!-- Search -->
            <div id="searchModel" class="d-none">
                <form class="d-flex align-items-center gap-3 flex-wrap mb-4" id="filter-form">
                    <div>
                        <select name="report" class="form-select value empty choice">
                            <option value="">Bảng chi phí tháng</option>
                            @foreach ($allo as $item)
                                <option value="{{ $item->id }}">{{ dateFormat($item->time,'m-Y') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="tran" class="form-select value empty choice">
                            <option value="">Loại chi phí</option>
                            @foreach ($transtype as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="group_id" class="form-select value empty choice">
                            <option value="">Nhóm nhân sự</option>
                            @foreach ($group as $item)
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
                                <th class="align-middle text-center text-uppercase">stt</th>
                                <th class="align-middle text-start text-uppercase">Loại chi phí</th>
                                <th class="align-middle text-end text-uppercase">số tiền</th>
                                <th class="align-middle text-start text-uppercase">nhóm nhân sự</th>
                                <th class="align-middle text-start text-uppercase">thời gian nhập</th>
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
        const route = '/api/cost-allocation';
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
                            <td class='align-middle text-center'>${request.index++}</td>
                            <td class='align-middle text-start'>${item.tran_type?.name}</td>
                            <td class='align-middle text-end'>${Text.info(formatCurrency(item.amount))}</td>
                            <td class='align-middle text-start'>${item.group?.name}</td>
                            <td class='align-middle text-start'>${dateTimeFormat(item.created_at,'d-m-Y H:i:s')}</td>
                            <td class='align-middle text-start'>${item.note || Log.info('Không có ghi chú')}</td>
                        </tr>
                        `;
                })
                .join('');
            content += `
                <tr class='none-data fw-bold'>
                    <td class='align-middle text-center'>TỔNG:</td>
                    <td class='align-middle text-start'></td>
                    <td class='align-middle text-end'>${Text.success(formatCurrency(total))}</td>
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
