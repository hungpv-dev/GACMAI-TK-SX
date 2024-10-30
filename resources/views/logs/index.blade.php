@extends('layouts.app')

@section('title')
Lịch sử liên hệ khách hàng
@endsection

@section('content')
    <div class="content">
        <nav class="mb-2" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                <li class="breadcrumb-item">Lịch sử liên hệ khách hàng</li>
            </ol>
        </nav>
        <h3 class="text-bold text-body-emphasis mb-5">Lịch sử liên hệ khách hàng</h3>
        <div>
            <div class="mb-3 thongke">
                <div>
                    <div class="icon">
                        <div class="icon-border bg-primary-subtle">
                            <i class="fab fa-codepen text-primary-emphasis"></i>
                        </div>
                    </div>
                    <div class="body">
                        <p class="fw-bold m-0"><span id="sum">0</span> bản ghi</p>
                        <span class="fs-8 fw-bold text-body-highlight">Tổng số</span>
                    </div>
                </div>
            </div>
            <!-- Search -->
            <div id="searchModel" class="d-none">
                <form class="d-flex align-items-center gap-3 flex-wrap mb-4" id="filter-form">
                    <div>
                        <input name="content" placeholder="Nội dung" type="text"
                            class="form-control value empty">
                    </div>
                    <div>
                        <select name="customer_id" class="form-select value empty choice">
                            <option value="">Chọn khách hàng</option>
                            @foreach ($customers as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="user_id" class="form-select value empty choice">
                            <option value="">Chọn nhân sự</option>
                            @foreach ($users as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
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
                                <th class="align-middle text-start text-uppercase">Thời gian</th>
                                <th class="align-middle text-start text-uppercase">Nhân sự</th>
                                <th class="align-middle text-start text-uppercase">Khách hàng</th>
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
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const route = '/api/logs';
        var request = new RequestServer(route);
        var searchModal = new HandleForm('#searchModel');
        request.colspan = 12;
        request.insert = function(data) {
            document.querySelector("#sum").textContent = request.response.total;
            return data.map((item) => {
                    return `
                        <tr class="${request.bold(item.id)}">
                            <td class='align-middle text-start'>${dateTimeFormat(item.created_at,'d-m-Y H:i:s')} ${Log.info(time_ago(item.created_at))}</td>
                            <td class='align-middle text-start'>${item.user?.name ?? Log.danger('Không tồn tại')}</td>
                            <td class='align-middle text-start'>${item.customer?.name ?? Log.danger('Không tồn tại')}</td>
                            <td class='align-middle text-start'>${item.content ?? Log.danger('Không có nội dung')}</td>
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
            btnLoading(btn, false, 'Xóa lọc');
        }
    </script>
@endsection