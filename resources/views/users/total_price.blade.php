@extends('layouts.app')

@section('title')
    Thống kê doanh thu nhân viên
@endsection

@section('content')
    <div class="content">
        <nav class="mb-2" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                <li class="breadcrumb-item">Thống kê doanh thu nhân sự</li>
            </ol>
        </nav>
        <h3 class="text-bold text-body-emphasis mb-5">Thống kê doanh thu nhân sự</h3>
        <div>
            <div class="mb-3 thongke">
                <div>
                    <div class="icon">
                        <div class="icon-border bg-primary-subtle">
                            <i class="fab fa-codepen text-primary-emphasis"></i>
                        </div>
                    </div>
                    <div class="body">
                        <p class="fw-bold m-0"><span class="thongke-sum" id="sum">0</span> nhân sự</p>
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
                        <p class="fw-bold m-0"><span class="thongke-sum" id="sum-price">0</span> ₫</p>
                        <span class="fs-8 fw-bold text-body-highlight">Tổng thu</span>
                    </div>
                </div>
            </div>
            <!-- Search -->
            <div id="searchModel" class="d-none">
                <form class="d-flex align-items-center gap-3 flex-wrap mb-4" id="filter-form">
                    <div>
                        <input name="name" placeholder="Tên nhân sự" type="text" class="form-control value empty">
                    </div>
                    <div>
                        <input name="finish_at" type="text" placeholder="Ngày hoàn thành"
                            data-options='{"mode":"range","disableMobile":true,"dateFormat":"d-m-Y","maxDate": "today","locale":"vn","shorthandCurrentMonth": true}'
                            class="form-control value empty datetimepicker" id='date-finish'>
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
                                <th class="align-middle text-center text-uppercase">STT</th>
                                <th class="align-middle text-start text-uppercase">Họ tên nhân sự</th>
                                <th class="align-middle text-start text-uppercase">Nhóm</th>
                                <th class="align-middle text-end text-uppercase">Số đơn hàng</th>
                                <th class="align-middle text-end text-uppercase">Doanh thu</th>
                                <th class="align-middle text-end text-uppercase">Lần cập nhật gần nhất</th>
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
        const route = '/api/users/total-price';
        var request = new RequestServer(route);
        var searchModal = new HandleForm('#searchModel');
        request.colspan = 12;
        request.insert = function(data) {
            document.querySelector("#sum").textContent = request.response.total;
            document.querySelector("#sum-price").textContent = formatNumber(request.response.total_price);
            return data.map((item) => {
                    return `
                        <tr class="${request.bold(item.id)}">
                            <td class='align-middle text-center'>${request.index++}</td>
                            <td class='align-middle text-start'>${item.name}</td>
                            <td class='align-middle text-start'>${item.group.name}</td>
                            <td class='align-middle text-end'>${item.orders_count}</td>
                            <td class='align-middle text-end'><a href='#' onclick='handleRedirect(${item.id})'>${formatNumber(item.orders_sum_thuc_thu ?? 0)} ₫</a></td>
                            <td class='align-middle text-end'>${item.latest_log ? '<a href="/logs?user_id='+item.id+'">'+time_ago(item.latest_log?.created_at)+'</a>' : Log.danger('Chưa có hoạt động nào')}</td>
                        </tr>
                        `;
                })
                .join('');
        }
        function handleRedirect(user_id){
            location.href = `/orders?user_id=${user_id}&finish_at=${document.querySelector('#date-finish').value}`;
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
