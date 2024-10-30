@extends('layouts.app')

@section('title')
Quản lý thông báo
@endsection

@section('content')
    <div class="content">
        <nav class="mb-2" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                <li class="breadcrumb-item">Quản lý thông báo theo trạng thái</li>
            </ol>
        </nav>
        <h2 class="text-bold text-body-emphasis mb-5">Danh sách trạng thái thông báo</h2>
        <div>
            <!-- Search -->
            <div id="searchModel">
                <form class="d-flex align-items-center gap-3 flex-wrap mb-4" id="filter-form">
                    <div>
                        <select name="status_id" class="form-select value empty choice">
                            <option value="">Trạng thái</option>
                            @foreach ($status as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="group_id" class="form-select value empty choice">
                            <option value="">Nhóm kinh doanh</option>
                            @foreach ($groups as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
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
            <div class="d-flex justify-content-end align-items-center">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModel">Cập nhật</button>
            </div>
            <!-- Table -->
            <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-y mt-2 position-relative top-1"
                id="list_users_container">
                <div class="table-responsive quote-table-container scrollbar ms-n1 ps-1">
                    <table class="table table-hover table-sm fs-9 mb-0">
                        <thead>
                            <tr>
                                <th class="align-middle text-start text-uppercase">Nhóm kinh doanh</th>
                                <th class="align-middle text-start text-uppercase">Trạng thái</th>
                                <th class="align-middle text-start text-uppercase">Thời gian thông báo</th>
                                <th class="align-middle text-center text-uppercase">Thao tác</th>
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
                        <h5 class="modal-title" id="exampleModalLabel">Cập nhật thông báo</h5>
                        <button class="btn p-1 closeButton" type="button" data-bs-dismiss="modal" aria-label="Close">
                            <svg class="svg-inline--fa fa-xmark fs-9" aria-hidden="true" focusable="false" data-prefix="fas"
                                data-icon="xmark" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"
                                data-fa-i2svg="">
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
                                    <select name="group_id" title="Nhóm nhân sự"
                                        class="form-select choice value validate empty">
                                        <option value="">Chọn nhóm nhân sự</option>
                                        @foreach ($groups as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <label class="floating-label-cus">Nhóm nhân sự</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <select name="status_id" title="Trạng thái"
                                        class="form-select choice value validate empty">
                                        <option value="">Chọn trạng thái</option>
                                        @foreach ($status as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <label class="floating-label-cus">Trạng thái</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <div class="form-floating">
                                    <input type="number" name="time" min="1" class="form-control empty validate value"
                                        placeholder="Thời gian thông báo">
                                    <label>Thời gian thông báo</label><span class='floating-unit fw-bold fs-10'>NGÀY</span>
                                </div>
                            </div>
                            <div class="col-12 gy-6">
                                <div class="row g-3 justify-content-center">
                                    <div class="col-auto">
                                        <button type="button" onclick="addModel.reset()" class="btn btn-close-model btn-secondary mx-1"
                                            data-bs-dismiss="modal">Huỷ
                                        </button>
                                        <button type="submit" class="btn btn-primary btn-submit mx-1"
                                            title="Cập nhật">Cập nhật</button>
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
    <script>
        const route = '/api/customer-notification';

        var request = new RequestServer(route);
        var searchModal = new HandleForm('#searchModel');
        searchModal.setChoice();
        request.colspan = 12;
        request.insert = function(data) {
            let group = '';
            return data.map((item,k) => {
                    if(k == 0){
                        group = item.group?.name;
                    }
                    let content = ``;
                    if(item.group?.name != group){
                        group = item.group?.name;
                        content += '<tr><td style="height: 30px" colspan="4"></td></tr>'
                    }
                    content += `
                        <tr class="${request.bold(item.id)}">
                            <td class='align-middle text-start'>${Log.info(item.group?.name)}</td>
                            <td class='align-middle text-start'>${Status.status(item.status)}</td>
                            <td class='align-middle text-start'>${Text.info(item.time_notify+' ngày')}</td>
                            <td class='align-middle text-center'>
                                <div class='position-relative'>
                                    <button onclick='showOne(${item.id})' class='btn btn-edit-show btn-sm btn-phoenix-secondary text-danger me-1 fs-10' title='Xoá trạng thái'>
                                        <span class='fas fa-trash-alt'></span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                    return content;
                })
                .join('');
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
            btnLoading(btn, false, 'Xóa lọc');
        }

        async function showOne(id){
            if(confirm('Xoá trạng thái thông báo này!')){
                try{
                    let response = await axios.delete(`${route}/${id}`).then(res => res);
                    if(response.status == 204){
                        showMessageMD('Xoá trạng thái thành công!');
                        request.get();
                    }
                    console.log(response)
                }catch(error){
                    console.log(error);
                }
            }
        }



        var addModel = new HandleForm('#addModel');
        addModel.setChoice();
        addModel.addValidate([
            ['time',['number','min:1']]
        ])
        addModel.submit = async function(e) {
            e.preventDefault();
            let check = this.checkValidate();
            if (check) {
                this.loading(true);
                let value = this.value().get();
                try {
                    let res = await axios.post(route, value).then(res => res);
                    if (res.status == 201) {
                        addModel.reset();
                        addModel.hideModal();
                        showMessageMD(res.data.message);
                        request.id = res.data.id;
                        request.get();
                    }
                } catch (error) {
                    let res = error.response;
                    if (res.status == 422) {
                        this.logError(res.data);
                    } else {
                        showErrorMD('Có lỗi xảy ra, vui lòng thử lại sau!');
                        addModel.hideModal();
                        addModel.reset();
                    }
                }
                this.loading(false);
            }
        }
    </script>
@endsection
