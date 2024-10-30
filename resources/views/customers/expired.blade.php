@extends('layouts.app')

@section('title')
    Danh sách khách hàng quá hạn
@endsection

@section('content')
    <div class="content">
        <nav class="mb-2" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                <li class="breadcrumb-item">Khách hàng quá hạn</li>
            </ol>
        </nav>
        <h3 class="text-bold text-body-emphasis mb-5">Danh sách khách hàng quá hạn</h3>
        <div>
            <div class="mb-3 thongke">
                <div>
                    <div class="icon">
                        <div class="icon-border bg-primary-subtle">
                            <i class="fab fa-codepen text-primary-emphasis"></i>
                        </div>
                    </div>
                    <div class="body">
                        <p class="fw-bold m-0"><span id="sum">0</span> khách hàng quá hạn</p>
                        <span class="fs-8 fw-bold text-body-highlight">Tổng số</span>
                    </div>
                </div>
            </div>
            <!-- Search -->
            <div id="searchModel" class="d-none">
                <form class="d-flex align-items-center gap-3 flex-wrap mb-4" id="filter-form">
                    <div>
                        <input name="name" placeholder="Tên khách hàng" type="text" class="form-control value empty">
                    </div>
                    <div>
                        <input name="phone" placeholder="Số điện thoại" type="text" class="form-control value empty">
                    </div>
                    <div>
                        <select name="status" class="form-select value choice empty">
                            <option value="">Trạng thái</option>
                            @foreach ($status as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
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
                        <select name="schedule" class="form-select value empty choice">
                            <option value="">Tất cả</option>
                            <option value="1">Quá hạn</option>
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
                                <th class="align-middle text-center text-uppercase">stt</th>
                                <th class="align-middle text-start text-uppercase">Tên khách hàng</th>
                                <th class="align-middle text-start text-uppercase">Số điện thoại</th>
                                <th class="align-middle text-start text-uppercase">Địa chỉ</th>
                                <th class="align-middle text-start text-uppercase">Nhu cầu</th>
                                <th class="align-middle text-start text-uppercase">Kênh</th>
                                <th class="align-middle text-start text-uppercase">Ghi chú</th>
                                <th class="align-middle text-start text-uppercase">Trạng thái</th>
                                <th class="align-middle text-start text-uppercase">Nhân sự phụ trách</th>
                                <th class="align-middle text-start text-uppercase">Nhóm nhân sự</th>
                                <th class="align-middle text-end text-uppercase">Lần hoạt động cuối</th>
                                <th class="align-middle text-center text-uppercase">Hành động</th>
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

    <div class="modal fade" id="editModel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-dialog">
                <div class="modal-content form-open">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Cập nhật khách hàng</h5>
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
                            <div class="col-sm-6 col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="name" class="form-control validate empty value"
                                        placeholder="Tên khách hàng">
                                    <label>Tên khách hàng</label>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6">
                                <div class="form-floating">
                                    <select name="province_id" class="form-select value choice" title="Tỉnh thành">
                                        <option value="">Chọn tỉnh thành</option>
                                    </select>
                                    <label class='floating-label-cus'>Tỉnh thành</label>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6">
                                <div class="form-floating">
                                    <select name="district_id" class="form-select value choice" title="Quận huyện">
                                        <option value="">Chọn Quận huyện</option>
                                    </select>
                                    <label class='floating-label-cus'>Quận huyện</label>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6">
                                <div class="form-floating">
                                    <select name="ward_id" class="form-select value choice" title="Xã phường">
                                        <option value="">Chọn xã phường</option>
                                    </select>
                                    <label class='floating-label-cus'>Xã phường</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <div class="form-floating">
                                    <input type="text" name="address" class="form-control empty value"
                                        placeholder="Địa chỉ chi tiết">
                                    <label>Địa chỉ chi tiết</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="phone" class="form-control validate empty value"
                                        placeholder="Số điện thoại">
                                    <label>Số điện thoại</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="phone_2" class="form-control empty value"
                                        placeholder="Số điện thoại phụ">
                                    <label>Số điện thoại phụ</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <select name="category_id" class="form-select validate value choice" title="Nhu cầu">
                                        @foreach ($categories as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <label class='floating-label-cus'>Nhu cầu</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <select name="sale_channel_id" class="form-select validate value choice" title="Nguồn khách">
                                        <option value="">Chọn nguồn khách</option>
                                        @foreach ($sale_channel as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <label class='floating-label-cus'>Nguồn khách</label>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6">
                                <div class="form-floating">
                                    <select name="status_id" class="form-select value validate choice" title="Trạng thái">
                                        @foreach ($status as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <label class='floating-label-cus'>Trạng thái</label>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6">
                                <div class="form-floating">
                                    <select name="user_id" class="form-select validate value choice" title="Nhân sự">
                                        @foreach ($users as $item)
                                            <option {{ $item->id == user()->id ? 'selected' : '' }} value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <label class='floating-label-cus'>Nhân sự phụ trách</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <div class="form-floating">
                                    <textarea name="note" class="form-control empty value" style="height: 80px;" placeholder="Ghi chú"></textarea>
                                    <label class='floating-label-cus'>Ghi chú</label>
                                </div>
                            </div>
                            <div class="col-12 gy-6">
                                <div class="row g-3 justify-content-center">
                                    <div class="col-auto">
                                        <button type="button" onclick="editModel.reset()" class="btn btn-close-model btn-secondary mx-1"
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
        const route = '/api/customers';
        const routeGet = '/api/customers-expired';
        var request = new RequestServer(routeGet);
        var searchModal = new HandleForm('#searchModel');
        searchModal.setChoice();
        request.colspan = 12;
        request.insert = function(data) {
            document.querySelector("#sum").textContent = request.response.total;
            return data.map((item) => {
                    return `
                        <tr class="${request.bold(item.id)}">
                            <td class='align-middle text-center'>${request.index++}</td>
                            <td class='align-middle text-start'><a href='/customers/${item.id}'>${item.name}</a></td>
                            <td class='align-middle text-start'>${item.phone}</td>
                            <td class='align-middle text-start'>${item.areas ?? Log.info('Chưa có')}</td>
                            <td class='align-middle text-start'>${item.category?.name ?? Log.info("Chưa có")}</td>
                            <td class='align-middle text-start'>${item.sale_channel?.name ?? Log.info("Chưa có")}</td>
                            <td class='align-middle text-start'>${item.latest_log?.content ?? Log.info("Chưa có ghi chú")}</td>
                            <td class='align-middle text-start'>
                                ${Status.status(item.status)}
                                ${item.status.id == {{ status('customer_schelude') }} ?
                                Log.info(dateTimeFormat(item.schedule)) : '' }
                            </td>
                            <td class='align-middle text-start'>${item.user?.name ?? Log.danger('Không rõ')}</td>
                            <td class='align-middle text-start'>${item.user?.group?.name ?? Log.danger('Không rõ')}</td>
                            <td class='align-middle text-end'>${item.latest_log ? (dateTimeFormat(item.latest_log?.created_at,'d-m-Y H:i:s') +' - '+ Log.info(time_ago(item.latest_log?.created_at))) : Log.danger('Chưa có')}</td>
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
            request.params = searchModal.value().get();
            await request.get();
            searchModal.showValue(request.params);
            btnLoading(btn, false, 'Xóa lọc');
        }

        var editModel = new HandleForm('#editModel');
        editModel.addValidate(
            [
                ['phone',['number']]
            ]
        );
        editModel.closeReset();
        editModel.setChoice();
        VN.render(editModel);
        async function showOne(id) {
            try {
                let fetch = await axios.get(`${route}/${id}`).then(res => res);
                if (fetch.status == 200) {
                    editModel.id = fetch.data.id;
                    VN.show(fetch.data,editModel);
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
                        if(res.data.check){
                            editModel.redirectMD(`/customers/${res.data.id}?model=show`);
                        }
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
    </script>
@endsection