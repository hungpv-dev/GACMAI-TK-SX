@extends('layouts.app')

@section('title')
Danh sách nhân sự
@endsection

@section('content')
    <div class="content">
        <nav class="mb-2" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                <li class="breadcrumb-item">Nhân sự</li>
            </ol>
        </nav>
        <h3 class="text-bold text-body-emphasis mb-5">Danh sách nhân sự</h3>
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
            </div>
            <!-- Search -->
            <div id="searchModel" class="d-none">
                <form class="d-flex align-items-center gap-3 flex-wrap mb-4" id="filter-form">
                    <div>
                        <input name="name" placeholder="Tên nhân sự" type="text" class="form-control value empty">
                    </div>
                    <div>
                        <select name="group" class="form-select value empty choice">
                            <option value="">Nhóm nhân sự</option>
                            @foreach ($groups as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="status" class="form-select value empty choice">
                            <option value="">Trạng thái</option>
                            @foreach (status('user') as $k => $item)
                                <option value="{{ $k }}">{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="role_id" class="form-select value empty choice" id="roleSelect">
                            <option value="">Loại nhân sự</option>
                            @foreach ($roles as $item)
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
                                <th class="align-middle text-center text-uppercase">STT</th>
                                <th class="align-middle text-start text-uppercase">Họ tên</th>
                                <th class="align-middle text-start text-uppercase">Email</th>
                                <th class="align-middle text-start text-uppercase">Lần truy cập cuối</th>
                                <th class="align-middle text-start text-uppercase">Trạng thái</th>
                                <th class="align-middle text-start text-uppercase">Nhân sự</th>
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
                        <h5 class="modal-title" id="exampleModalLabel">Thêm mới nhân sự</h5>
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
                            <div class="col-sm-12 col-md-12">
                                <div class="form-floating">
                                    <input type="text" name="name" class="form-control validate empty value"
                                        placeholder="Tên nhân sự">
                                    <label>Tên nhân sự</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <div class="form-floating">
                                    <input type="text" name="email" class="form-control validate empty value"
                                        placeholder="Email">
                                    <label>Email</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <select name="status" class="form-select value validate choice" title="Trạng thái">
                                        @foreach (status('user') as $k => $item)
                                            <option value="{{ $k }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    <label class='floating-label-cus'>Trạng thái</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <select name="role_id" class="form-select value empty validate choice" title="Loại nhân sự" id="addRoleSelect">
                                        <option value="">Chọn loại nhân sự</option>
                                        @foreach ($roles as $k => $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <label class='floating-label-cus'>Loại nhân sự</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 d-none" id="addGroupSelectContainer">
                                <div class="form-floating">
                                    <select name="group_id" class="form-select value empty choice" title="Nhóm nhân sự">
                                        <option value="">Chọn nhóm</option>
                                        @foreach ($groups as $k => $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <label class='floating-label-cus'>Nhóm nhân sự</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 d-none" id="addGroupSelectContainerFac">
                                <div class="form-floating">
                                    <select name="factory_id" class="form-select value empty choice" title="Xưởng">
                                        <option value="">Chọn xưởng</option>
                                        @foreach ($factories as $k => $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <label class='floating-label-cus'>Xưởng</label>
                                </div>
                            </div>
                            <div class="col-12 gy-6">
                                <div class="row g-3 justify-content-center">
                                    <div class="col-auto">
                                        <button type="button" onclick="addModel.reset()" class="btn btn-close-model btn-secondary mx-1"
                                            data-bs-dismiss="modal">Huỷ
                                        </button>
                                        <button type="submit" class="btn btn-primary btn-submit mx-1" title="Thêm mới">Thêm
                                            mới</button>
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
    <div class="modal fade" id="editModel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-dialog">
                <div class="modal-content form-open">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Cập nhật nhân sự</h5>
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
                            <div class="col-sm-12 col-md-12">
                                <div class="form-floating">
                                    <input type="text" name="name" class="form-control validate empty value"
                                        placeholder="Tên nhân sự">
                                    <label>Tên nhân sự</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <div class="form-floating">
                                    <input type="text" name="email" class="form-control validate empty value"
                                        placeholder="Email">
                                    <label>Email</label>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6">
                                <div class="form-floating">
                                    <select name="status" class="form-select value validate choice" title="Trạng thái">
                                        @foreach (status('user') as $k => $item)
                                            <option value="{{ $k }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    <label class='floating-label-cus'>Trạng thái</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <select name="role_id" class="form-select value empty validate choice" title="Loại nhân sự" id="editGroupSelect">
                                        <option value="">Chọn loại nhân sự</option>
                                        @foreach ($roles as $k => $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <label class='floating-label-cus'>Loại nhân sự</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 d-none" id="editGroupSelectContainer">
                                <div class="form-floating">
                                    <select name="group_id" class="form-select value empty choice" title="Nhóm nhân sự">
                                        <option value="">Chọn nhóm</option>
                                        @foreach ($groups as $k => $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <label class='floating-label-cus'>Nhóm nhân sự</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 d-none" id="editGroupSelectContainerFac">
                                <div class="form-floating">
                                    <select name="factory_id" class="form-select value empty choice" title="Xưởng">
                                        <option value="">Chọn xưởng</option>
                                        @foreach ($factories as $k => $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <label class='floating-label-cus'>Xưởng</label>
                                </div>
                            </div>
                            <div class="col-12 gy-6">
                                <div class="row g-3 justify-content-center">
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-close-model btn-secondary mx-1"
                                            data-bs-dismiss="modal">Huỷ
                                        </button>
                                        <button type="submit" class="btn btn-primary btn-submit mx-1" title="Cập nhật">Cập nhật</button>
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
        const route = '/api/users';
        var request = new RequestServer(route);
        var searchModal = new HandleForm('#searchModel');
        request.colspan = 12;
        request.insert = function(data) {
            $("#sum").text(request.response.total);
            return data.map((item) => {
                    return `
                        <tr class="${request.bold(item.id)}">
                            <td class='align-middle text-center'>${request.index++}</td>
                            <td class='align-middle text-start'>${item.name}</td>
                            <td class='align-middle text-start'>
                                <a href='#' onclick='sendLoginRequest("${item.id}")' title='Đăng nhập'>
                                    ${item.email}
                                </a>
                            </td>
                            <td class='align-middle text-start'>${item.last_active ? '<a href="/logs?user_id='+item.id+'">'+dateTimeFormat(item.last_active,'d-m-Y H:i:s')+'</a>' : '<span class="text-danger">Chưa có hoạt động nào</span>'}</td>
                            <td class='align-middle text-start'>${Status.user(item.status)}</td>
                            <td class='align-middle text-start'>
                                <div>
                                    ${item.role ? Log.success(item.role.name) : Log.danger('Không xác định')}
                                    ${item.role.id == 3 ? Log.primary(item.group.name) : ''}
                                    ${item.role.id == 7 ? Log.primary(item.factory.name) : ''}
                                </div>
                            </td>
                            <td class='align-middle text-center'>
                                <div class='position-relative'>
                                    <button onclick='showOne(${item.id})' class='btn btn-edit-show btn-sm btn-phoenix-secondary text-info me-1 fs-10' title='Cập nhật' type='button' data-bs-toggle='modal' data-bs-target='#editModel'>
                                        <span class='fas far fa-edit'></span>
                                    </button>
                                </div>
                            </td>
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

        var addModel = new HandleForm('#addModel');
        addModel.setChoice();
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
                    console.log(res);
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
        var editModel = new HandleForm('#editModel');
        editModel.closeReset();
        editModel.setChoice();
        async function showOne(id) {
            try {
                let fetch = await axios.get(`${route}/${id}`).then(res => res);
                if (fetch.status == 200) {
                    editModel.id = fetch.data.id;
                    fetch.data.factory_id = fetch.data.group_id;
                    editModel.showValue(fetch.data);
                }
            } catch (error) {
                let response = error.response;
                if (response.status == 404) {
                    showErrorMD(response.data.message);
                    editModel.hideModal();
                    editModel.reset();
                } else {
                    showErrorMD('Có lỗi xảy ra, vui lòng thử lại sau!');
                    editModel.hideModal();
                    editModel.reset();
                }
            }
        }
        editModel.submit = async function(e) {
            e.preventDefault();
            let check = this.checkValidate();
            if (check) {
                this.loading(true);
                let value = this.value().get();
                try {
                    let res = await axios.put(`${route}/${this.id}`, value).then(res => res);
                    if (res.status == 200) {
                        editModel.reset();
                        editModel.hideModal();
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
                        editModel.hideModal();
                        editModel.reset();
                    }
                }
                this.loading(false);
            }
        }

        // Bắt sự kiện thay đổi của chọn loại nhân sự để ẩn/hiện nhóm nhân sự
        document.getElementById('addRoleSelect').addEventListener('addItem', function() {
            var addGroupSelectContainerFac = document.getElementById('addGroupSelectContainerFac');
            var groupSelectContainer = document.getElementById('addGroupSelectContainer');
            console.log(this.value);
            switch(parseInt(this.value)){
                case 3: {
                    groupSelectContainer.classList.remove('d-none');
                    groupSelectContainer.querySelector('select').classList.add('validate');
                    addGroupSelectContainerFac.classList.add('d-none');
                    addGroupSelectContainerFac.querySelector('select').classList.remove('validate');
                    break;
                }
                case 7: {
                    addGroupSelectContainerFac.classList.remove('d-none');
                    addGroupSelectContainerFac.querySelector('select').classList.add('validate');
                    groupSelectContainer.classList.add('d-none');
                    groupSelectContainer.querySelector('select').classList.remove('validate');
                    break;
                }
                default: {
                    groupSelectContainer.classList.add('d-none');
                    groupSelectContainer.querySelector('select').classList.remove('validate');
                    addGroupSelectContainerFac.classList.remove('d-none');
                    addGroupSelectContainerFac.querySelector('select').classList.add('validate');
                    break;
                }

            }
        });

        document.getElementById('editGroupSelect').addEventListener('addItem', function() {
            var groupSelectContainer = document.getElementById('editGroupSelectContainer');
            var editGroupSelectContainerFac = document.getElementById('editGroupSelectContainerFac');
            switch(parseInt(this.value)){
                case 3: {
                    groupSelectContainer.classList.remove('d-none');
                    groupSelectContainer.querySelector('select').classList.add('validate');
                    editGroupSelectContainerFac.classList.add('d-none');
                    editGroupSelectContainerFac.querySelector('select').classList.remove('validate');
                    break;
                }
                case 7: {
                    groupSelectContainer.classList.add('d-none');
                    groupSelectContainer.querySelector('select').classList.remove('validate');
                    editGroupSelectContainerFac.classList.remove('d-none');
                    editGroupSelectContainerFac.querySelector('select').classList.add('validate');
                    break;
                }
                default: {
                    groupSelectContainer.classList.add('d-none');
                    groupSelectContainer.querySelector('select').classList.remove('validate');
                    editGroupSelectContainerFac.classList.add('d-none');
                    editGroupSelectContainerFac.querySelector('select').classList.remove('validate');
                    break;
                }

            }
        });

        async function sendLoginRequest(id) {
            try {
                let response = await axios.post('/api/login-code', { id: id });
                if (response.status === 200) {
                    window.location.href = response.data.link;
                }
            } catch (error) {
                showErrorMD('Failed to send login request.');
            }
        }
    </script>
@endsection
