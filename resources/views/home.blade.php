@extends('layouts.app')
@section('title')
Trang chủ
@endsection
@section('content')
<div class="content">
    <div class="mb-5">
        @foreach ($dt as $item)
        <div class="alert alert-outline-warning d-flex align-items-center" role="alert">
            <span class="fas fa-info-circle text-warning fs-5 me-3"></span>
            <p class="mb-0 flex-1">
                <a href="/customers/expired?status={{ $item->id }}{{ $item->id == status('customer_schelude') ? '&schedule=1' : '' }}" class="text-warning">
                    Trạng thái: <span class='text-primary fw-bold'>{{ $item->name }}</span> có <span class='text-danger fw-bold'>{{ $item->count_old }}</span> khách hàng quá hạn!
                </a>
            </p>
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endforeach
    </div>
    <h2 class='mb-5'>Báo cáo tổng quan</h2>
    <div id="searchModel" class="d-none">
        <form class="d-flex align-items-center gap-3 flex-wrap mb-4" id="filter-form">
            <div class='col-9 col-sm-7 col-md-5 col-lg-3 col-xl-2'>
                <input type="text" name="dates" class="form-control value empty">
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
    <div class="row-config">
        <div class="row justify-content-start my-5">
            <div
                class="col-6 col-md-4 mb-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-xxl-0 border-bottom-xxl-0 border-end border-bottom pb-2 pt-2 pb-xxl-0 redirectHome" data-type="1">
                <span class="uil fs-6 lh-1 fas fa-user-friends text-success"></span>
                <h4 class="fs-8 pt-3" id="totalCustomer">
                    0 khách hàng
                </h4>
                <a href="javascript:void(0);" data-type="1" class="">
                    <p class="fs-9 mb-0">Tổng số khách hàng theo kỳ</p>
                </a>
            </div>

            <div
                class="col-6 col-md-4 mb-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-xxl-0 border-bottom-xxl-0 border-end-md border-bottom pb-2 pt-2 pb-xxl-0 redirectHome" data-type="2">
                <span class="uil fs-5 lh-1 uil-usd-circle text-primary"></span>
                <h4 id="totalOrder" class="fs-8 pt-3">
                    0 đơn
                </h4>
                <a href="javascript:void(0);" data-type="2" class="">
                    <p class="fs-9 mb-0">Tổng số đơn hàng theo kỳ</p>
                </a>
            </div>
            <div
                class="col-6 col-md-4 mb-4 col-xxl-2 text-center border-translucent border-start-xxl border-bottom-xxl-0 border-bottom border-end border-end-md-0 pb-xxl-0 pb-2 pt-2 pt-xxl-0 redirectHome" data-type="3">
                <span class="uil fs-5 lh-1 uil-usd-circle text-info"></span>
                <h4 id="totalPriceToday" class="fs-8 pt-3">
                    0 ₫
                </h4>
                <a href="javascript:void(0);" data-type="3" class="">
                    <p class="fs-9 mb-0">Tiền thu được hôm nay</p>
                </a>
            </div>

            <div
                class="col-6 col-md-4 mb-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-md border-end-xxl-0 border-bottom border-bottom-md-0 pb-2 pb-xxl-0 pt-2 pt-xxl-0 redirectHome" data-type="4">
                <span class="uil fs-5 lh-1 uil-usd-circle text-success" data-bs-toggle="modal"
                    data-bs-target="#detail_currentAssets"></span>
                <h4 id="totalPriceByPeriod" class="fs-8 pt-3">
                    0 ₫
                </h4>
                <a href="javascript:void(0);" data-type="4" class="">
                    <p class="fs-9 mb-0">Tiền thu được theo kỳ</p>
                </a>
            </div>
            
            <div
                class="col-6 col-md-4 mb-4 col-xxl-2 text-center border-translucent border-start-xxl border-end border-end-xxl-0 pb-xxl-0 pb-2 pt-2 pt-xxl-0 redirectHome" data-type="5">
                <span class="uil fs-5 lh-1 uil-notes text-warning"></span>
                <h4 id="totalPriceOrderSuccess" class="fs-8 pt-3">
                    0 ₫
                </h4>
                <a href="javascript:void(0);" data-type="5" class="">
                    <p class="fs-9 mb-0">Doanh thu đơn hàng đã hoàn thành theo kỳ</p>
                </a>
            </div>

            <div
                class="col-6 col-md-4 mb-4 col-xxl-2 text-center border-translucent border-start-xxl border-end border-end-xxl-0 pb-xxl-0 pb-2 pt-2 pt-xxl-0 redirectHome" data-type="15">
                <span class="uil fs-5 lh-1 uil-notes text-warning"></span>
                <h4 id="orderBoCoc" class="fs-8 pt-3">
                    0 ₫
                </h4>
                <a href="javascript:void(0);" data-type="15" class="">
                    <p class="fs-9 mb-0">Doanh thu đơn hàng bỏ cọc theo kỳ</p>
                </a>
            </div>

            <div
                class="col-6 col-md-4 mb-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-xxl pb-xxl-0 pb-2 pt-2 pt-xxl-0 redirectHome" data-type="6">
                <span class="uil fs-5 lh-1 uil-briefcase-alt text-danger" data-bs-toggle="modal"
                    data-bs-target="#detail_loanAmount"></span>
                <h4 id="totalCountOrderSuccess" class="fs-8 pt-3">
                    0 đơn
                </h4>
                <a href="javascript:void(0);" data-type="6" class="">
                    <p class="fs-9 mb-0">Đơn hàng đã hoàn thành theo kỳ</p>
                </a>
            </div>
            <div
                class="col-6 col-md-4 mb-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-xxl pb-xxl-0 pb-2 pt-2 pt-xxl-0 redirectHome" data-type="7">
                <span class="uil fs-5 lh-1 uil-briefcase-alt text-danger" data-bs-toggle="modal"
                    data-bs-target="#detail_loanAmount"></span>
                <h4 id="totalCountOrderUnSuccess" class="fs-8 pt-3">
                    0 đơn
                </h4>
                <a href="javascript:void(0);" data-type="7" class="">
                    <p class="fs-9 mb-0">Đơn hàng chưa hoàn thành theo kỳ</p>
                </a>
            </div>
            <div
                class="col-6 col-md-4 mb-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-md border-end-xxl-0 border-bottom border-bottom-md-0 pb-2 pb-xxl-0 pt-2 pt-xxl-0 redirectHome" data-type="8">
                <span class="uil fs-5 lh-1 uil-usd-circle text-success" data-bs-toggle="modal"
                    data-bs-target="#detail_currentAssets"></span>
                <h4 id="totalPriceCongNo" class="fs-8 pt-3">
                    0 ₫
                </h4>
                <a href="javascript:void(0);" data-type="8" class="">
                    <p class="fs-9 mb-0">Tổng số công nợ cần thu</p>
                </a>
            </div>
            <div
                class="col-6 col-md-4 mb-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-md border-end-xxl-0 border-bottom border-bottom-md-0 pb-2 pb-xxl-0 pt-2 pt-xxl-0 redirectHome" data-type="12">
                <span class="uil fs-5 lh-1 uil-usd-circle text-warning" data-bs-toggle="modal"
                    data-bs-target="#detail_orderDuKien"></span>
                <h4 id="totalPriceOrderDuKien" class="fs-8 pt-3">
                    0 ₫
                </h4>
                <a href="javascript:void(0);">
                    <p class="fs-9 mb-0">Tổng doanh thu dự kiến trong kỳ</p>
                </a>
            </div>
        </div>
        <div class="col-md-6 col-sm-12 m-auto">
            <h4 class='text-center'>Thống kê doanh thu theo nhóm kinh doanh trong kỳ</h4>
            <div class="chart-all-group" style="min-height:320px"></div>
        </div>
        <div class="col-md-6 col-sm-12 m-auto">
            <h4 class='text-center'>Thống kê doanh thu dự kiến theo nhóm kinh doanh trong kỳ</h4>
            <div class="chart-all-user" style="min-height:320px"></div>
        </div>
        <div class="col-md-6 my-5 col-sm-12 m-auto">
            <h4 class='text-center'>Thống kê tiền thu được theo nhóm kinh doanh trong kỳ</h4>
            <div class="chart-all-group-ttd" style="min-height:320px"></div>
        </div>
        <div class="col-md-6 my-5 col-sm-12 m-auto">
            <h4 class='text-center'>Thống kê tiền thu được theo nhân sự kinh doanh trong kỳ</h4>
            <div class="chart-all-user-tdd" style="min-height:320px"></div>
        </div>
    </div>
    <div class="col-md-12 mt-10 col-sm-12 m-auto">
        <h4 class='text-center'>Biểu đồ thống kê đơn hàng trong kỳ</h4>
        <div class="echart-bar-timeline-chart-example" style="min-height:450px"></div>
    </div>
    <div class="col-md-12 mt-5 col-sm-12 m-auto">
        <h4 class='text-center'>Thống kê doanh thu theo nhân sự kinh doanh trong kỳ</h4>
        <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-y mt-2 position-relative top-1"
            id="list_users_container">
            <div class="table-responsive quote-table-container scrollbar ms-n1 ps-1">
                <table class="table table-hover table-sm fs-9 mb-0">
                    <thead>
                        <tr>
                            <th class="align-middle text-center text-uppercase">STT</th>
                            <th class="align-middle text-start text-uppercase">Họ tên nhân sự</th>
                            <th class="align-middle text-start text-uppercase">Nhóm</th>
                            <th class="align-middle text-end text-uppercase">Doanh thu</th>
                            <th class="align-middle text-end text-uppercase">Số đơn hàng</th>
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
<script src='{{ js('home/users') }}'></script>
<script src='{{ js('home/chart') }}'></script>
<script>
    var searchModal = new HandleForm('#searchModel');
    document.addEventListener('DOMContentLoaded', async function () {
        await request.get();
        console.log(request.params);
        await getData(request.params);
        resetRedirect(request.params);
        searchModal.showValue(request.params);
    });
    searchModal.submit = async function(e) {
        e.preventDefault();
        this.loading(true);
        let value = this.value().get();
        request.params = value;
        await request.get();
        await getData(request.params);
        resetRedirect(request.params);
        this.loading(false, `<span class='fas fa-filter text-info fs-9 me-2'></span>Lọc`);
    }
    async function removeFilter(btn) {
        btnLoading(btn, true);
        searchModal.reset();
        request.params = {};
        await request.get();
        await getData(request.params);
        resetRedirect(request.params);
        searchModal.showValue(request.params);
        btnLoading(btn, false, 'Xóa lọc');
    }
</script>
<script>
    var datesGlobal = '';
    function resetRedirect(params) {
        let dates = params.dates;
        datesGlobal = dates;
    }
    $(document).ready(function() {
        $(document).on('click', '.redirectHome', function() {
            let dataHref = {
                'type-1': '/customers?dates=',
                'type-2': '/orders?dates=',
                'type-3': '/customers/price-thu-duoc?dates=',
                'type-4': '/customers/price-thu-duoc?dates=',
                'type-5': 'orders?status={{ status('order_success') }}&dates=',
                'type-6': 'orders?status={{ status('order_success') }}&dates=',
                'type-7': 'orders?nostatus={{ status('order_success') }}&dates=',
                'type-8': '/debt?dates=',
                'type-12': 'orders?dates=',
                'type-15': 'orders?status={{ status('order_back') }}&dates=',
            }
            if($(this).attr('data-type') == 3){
                datesGlobal = '{{ now('d-m-Y 00:00:00') }} đến {{ now('d-m-Y 23:59:59') }}';
            }
            let href = dataHref['type-' + $(this).attr('data-type')]+datesGlobal;
            window.location.href = href;
        });
    });
</script>
@endsection