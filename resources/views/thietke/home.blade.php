@extends('layouts.thietke')
@section('title')
Trang chủ
@endsection
@section('content')
<div class="content">
    <div class="mb-5">
        <div class="alert alert-outline-warning d-flex align-items-center" role="alert">
            <span class="fas fa-info-circle text-warning fs-5 me-3"></span>
            <p class="mb-0 flex-1">
                <a href="/orders?type_status=16" class="text-warning">
                    <span class='text-primary fw-bold'>Có <span class='text-danger fw-bold'>{{ status('count_start_tk') }}</span> đơn mới!
                </a>
            </p>
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    <h2 class='mb-5'>Báo cáo tổng quan</h2>
    <div class="row-config my-5">
        <div
            class="col-6 col-md-4 mb-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-xxl-0 border-bottom-xxl-0 border-end-md border-bottom pb-2 pt-2 pb-xxl-0" data-type="2">
            <span class="uil fs-5 lh-1 uil-usd-circle text-primary"></span>
            <h4 id="totalOrder" class="fs-8 pt-3">
                {{ (clone $orders)->count() }} đơn
            </h4>
            <a href="/orders" data-type="2" class="">
                <p class="fs-9 mb-0">Đơn hàng thiết kế</p>
            </a>
        </div>
        <div
            class="col-6 col-md-4 mb-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-xxl-0 border-bottom-xxl-0 border-end-md border-bottom pb-2 pt-2 pb-xxl-0" data-type="3">
            <span class="uil fs-5 lh-1 uil-usd-circle text-info"></span>
            <h4 id="totalPriceToday" class="fs-8 pt-3">
                {{ (clone $orders)->where('du_kien_time','<',now())->count() }} đơn
            </h4>
            <a href="/orders?expried=1" data-type="3" class="">
                <p class="fs-9 mb-0">Đơn hàng quá hạn</p>
            </a>
        </div>
        @foreach (status('order') as $item)
            <div
                class="col-6 col-md-4 mb-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-xxl-0 border-bottom-xxl-0 border-end-md border-bottom pb-2 pt-2 pb-xxl-0" data-type="4">
                <span class="uil fs-5 lh-1 uil-usd-circle text-success" data-bs-toggle="modal"
                    data-bs-target="#detail_currentAssets"></span>
                <h4 id="totalPriceByPeriod" class="fs-8 pt-3">
                    {{ (clone $orders)->where('current_status',$item->id)->count() }} đơn
                </h4>
                <a href="/orders?type_status={{ $item->id }}" data-type="4" class="">
                    <p class="fs-9 mb-0">Đơn hàng {{ $item->name }}</p>
                </a>
            </div>
        @endforeach
    </div>
    
   
</div>
@endsection