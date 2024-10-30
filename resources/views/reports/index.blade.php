@extends('layouts.app')

@section('title')
Báo cáo tài chính
@endsection

@section('content')
<div class="content">
    <nav class="mb-2" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
            <li class="breadcrumb-item">Báo cáo</li>
        </ol>
    </nav>
    <h3 id='report-title' class="text-bold text-body-emphasis mb-5">Báo cáo tài chính</h3>
    <div>
        <!-- Search -->
        <div id="searchModel" class="d-none">
            <form class="d-flex align-items-center gap-3 flex-wrap mb-4" id="filter-form">
                <div>
                    <input type="text" name="dates" style="min-width: 350px" class="form-control value empty">
                </div>
                <div>
                    <select name="type" class="form-select value empty choice">
                        <option value="tongquan">Tổng quan</option>
                        <option value="kqkd">Báo cáo kết quả kinh doanh</option>
                        <option value="bangluong">Bảng lương</option>
                        <option value="congnoncc">Báo cáo công nợ NCC</option>
                        <option value="phanbochiphiloinhuan">Bảng phân bổ chi phí và lợi nhuận</option>
                    </select>
                </div>
                <div class="d-flex justify-content-start">
                    <button type="submit" class="btn btn-sm btn-phoenix-info btn-filter me-2" title="Lọc">
                        <span class="fas fa-filter text-info fs-9 me-2"></span>Lọc
                    </button>
                    <button button class="btn btn-sm btn-phoenix-warning" onclick="removeFilter(this)" type="button">Xoá
                        lọc</button>
                </div>
            </form>
        </div>

        <div id='report-content'></div>
    </div>
</div>

@endsection

@section('script')
<script>

    const route = '/api/finance';
    var searchModal = new HandleForm('#searchModel');
    searchModal.setChoice();
    async function getData(params = {}){
        try{
            let res = await axios.get(route,{params}).then(res => res);
            $('#report-content').html(res?.data);
            const urlParams = new URLSearchParams(params).toString();
            window.history.replaceState({}, '', `${window.location.pathname}?${urlParams}`);
        }catch(errors){
            console.log(errors);
        }
    }
    document.addEventListener('DOMContentLoaded', async function() {
        const urlParams = new URLSearchParams(window.location.search);
        let params = {};
        for (const [key, value] of urlParams.entries()) {
            params[key] = value;
        }
        let value = searchModal.value().get();
        value.dates = currentMonth();
        params = {...value,...params}
        await getData(params);
        searchModal.showValue(params);
    });

    searchModal.submit = async function(e) {
        e.preventDefault();
        this.loading(true);
        let value = this.value().get();
        await getData(value);
        this.loading(false, `<span class='fas fa-filter text-info fs-9 me-2'></span>Lọc`);
    }
    async function removeFilter(btn) {
        btnLoading(btn, true);
        const params = {};
        params.dates = currentMonth();
        await getData(params);
        searchModal.reset();
        searchModal.showValue(params);
        btnLoading(btn, false, 'Xóa lọc');
    }
</script>
@endsection