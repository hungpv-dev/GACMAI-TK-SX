@extends('layouts.app')

@section('title')
@endsection

@section('content')
    <div class="content">
        <nav class="mb-2" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                <li class="breadcrumb-item">Danh sách</li>
            </ol>
        </nav>
        <h2 class="text-bold text-body-emphasis mb-5">Danh sách</h2>
        <div>
            <div class="mb-3 thongke">
                <div>
                    <div class="icon">
                        <div class="icon-border bg-primary-subtle">
                            <i class="fab fa-codepen text-primary-emphasis"></i>
                        </div>
                    </div>
                    <div class="body">
                        <p class="fw-bold m-0"><span class="thongke-sum">0</span> vật tư</p>
                        <span class="fs-8 fw-bold text-body-highlight">Tổng số</span>
                    </div>
                </div>
            </div>
            <!-- Search -->
            <div id="searchModel">
                <form class="d-flex align-items-center gap-3 flex-wrap mb-4" id="filter-form">
                    <div>
                        <input name="created_at" type="text" placeholder="Ngày tạo"
                            data-options='{"mode":"range","disableMobile":true,"dateFormat":"d-m-Y","maxDate": "today","locale":"vn","shorthandCurrentMonth": true}'
                            class="form-control empty data-value datetimepicker">
                    </div>
                    <div>
                        <input name="input" placeholder="Input search" type="text"
                            class="form-control data-value empty">
                    </div>
                    <div>
                        <select name="select" class="form-select data-value empty choice">
                            <option value="">Select search</option>
                        </select>
                    </div>
                    <div class=" d-flex justify-content-start">
                        <button button class="btn btn-sm btn-phoenix-warning me-2" onclick="removeFilter(this)"
                            type="button">Xoá lọc</button>
                        <button type="submit" class="btn btn-sm btn-phoenix-info btn-filter" title="Lọc">
                            <span class="fas fa-filter text-info fs-9 me-2"></span>Lọc
                        </button>
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
                                <th class="align-middle text-center text-uppercase"></th>
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
    <div class="modal fade" id="addModel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-dialog">
                <div class="modal-content form-open">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Thêm</h5>
                        <button class="btn p-1 closeButton" type="button" data-bs-dismiss="modal"
                            aria-label="Close">
                            <svg class="svg-inline--fa fa-xmark fs-9" aria-hidden="true" focusable="false"
                                data-prefix="fas" data-icon="xmark" role="img"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg="">
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
                                    <select name="name"
                                        title="title" class="form-select choice data-value validate empty">
                                        <option value="">Select form</option>
                                    </select>
                                    <label class="floating-label-cus">Select lable</label>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="input" value="0" class="form-control validate set-0 data-value" placeholder="Chậm thanh toán">
                                    <label>Chậm thanh toán</label><span class="floating-unit">NGÀY</span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="prepay" value="0"
                                        class="form-control set-0 validate data-value" oninput="formatBalance(event)"
                                        placeholder="Price input">
                                    <label>Price input</label>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6">
                                <div class="form-floating">
                                    <input type="text" placeholder="Dateinput" name="start_date"
                                        data-options='{"minDate":"today","dateFormat":"d-m-Y", "locale": "vn", "shorthandCurrentMonth": true}'
                                        class="form-control datetimepicker data-value validate">
                                    <label>Dateinput</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <div class="form-floating">
                                    <textarea style="height: 80px;" name="note" class="data-value empty form-control" placeholder="Note input"></textarea>
                                    <label>Note input</label>
                                </div>
                            </div>
                            <div class="col-12 gy-6">
                                <div class="row g-3 justify-content-center">
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-close-model btn-secondary mx-1"
                                            data-bs-dismiss="modal">Huỷ
                                        </button>
                                        <button type="submit" class="btn btn-primary btn-submit mx-1"
                                            title="Thêm mới">Thêm mới</button>
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
@endsection