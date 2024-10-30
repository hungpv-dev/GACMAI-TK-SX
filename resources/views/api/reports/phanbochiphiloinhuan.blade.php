<style>
    @media (min-width: 768px){
        table{
            margin: auto;
            width: 1000px !important;
            max-width: 100%;
        }
    } 
</style>
<h4>Bảng phẩn bổ chi phí tháng <span class='text-info'>{{ dateFormat($reports->time, 'm/Y') }}</span></h4>
<div class="d-flex align-items-center justify-content-between gap-3 flex-wrap my-4">
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <div>
            <select name="search" id="searchchiphipb" class="form-select value empty validate">
                @foreach ($allocations as $item)
                    <option {{ $reports->id == $item->id ? 'selected' : '' }} value="{{ $item->id }}">
                        {{ dateFormat($item->time, 'm-Y') }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="d-flex justify-content-end align-item-center gap-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreateBangCP">Thêm bảng</button>
        <button class="btn btn-success" data-bs-toggle="modal" onclick="noneSetModalAddCP('ok')"
            data-bs-target="#modalCreateBangPhanBoChiPhi">Thêm chi tiết</button>
    </div>
</div>
<div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-y mt-2 position-relative top-1"
    id="list_users_container">
    <div class="table-responsive quote-table-container scrollbar ms-n1 ps-1">
        <table class="table table-config table-hover table-sm fs-9 mb-0">
            <thead>
                <tr>
                    <th class="align-middle border text-center text-uppercase">stt</th>
                    <th class="align-middle border text-start text-uppercase">loại chi phí</th>
                    <th class="align-middle border text-end text-uppercase">số tiền</th>
                    @foreach ($groups as $item)
                        <th class="align-middle border text-end text-uppercase">{{ $item->name }}</th>
                    @endforeach
                    <th class="align-middle border text-end text-uppercase"></th>
                </tr>
            </thead>

            <tbody class="list-data" id="user-table">
                @php
                    $total_amount_allocation = 0;
                @endphp
                @foreach ($cost_allocations as $k => $item)
                    @php
                        $total = 0;
                    @endphp
                    <tr class="user-row">
                        <td data-label="STT" class='align-middle border text-center'>{{ ++$k }}</td>
                        <td data-label="LOẠI CHI PHÍ"
                            class='align-middle border text-info fw-bold text-start user-name'>
                            {{ $item->name }}
                        </td>
                        <td data-label="SỐ TIỀN" class='align-middle border text-end'>{!! textPrice($item->total_amount) !!}
                        </td>
                        @foreach ($groups as $g)
                            @php
                                $total_group = $g
                                    ->allocations()
                                    ->where('name', $item->name)
                                    ->where('allocation_id', $reports->id)
                                    ->sum('amount');
                                $total += (float) $total_group;
                            @endphp
                            <td data-label="{{ $g->name }}" class="align-middle border text-end text-uppercase">
                                {!! textPrice($total_group) !!}
                            </td>
                        @endforeach
                    </tr>
                    @php
                        $total_amount_allocation += $total;
                    @endphp
                @endforeach
                <tr class="none-data fw-bold">
                    <td class="border"></td>
                    <td class='align-middle border text-start'>TỔNG</td>
                    <td data-label="TỔNG SỐ TIỀN" class='align-middle border text-end'>{!! textPrice($total_amount_allocation, 'info') !!}</td>
                    @foreach ($groups as $g)
                        <td data-label="TỔNG {{ $g->name }}" class="align-middle border text-end text-uppercase">
                            {!! textPrice($reports->tongchiphi['group_' . $g->id], 'info') !!}
                        </td>
                    @endforeach
                    <td class="border"></td>
                </tr>
                <tr class="none-data fw-bold">
                    <td class="border"></td>
                    <td class='align-middle border text-start'>CHIẾT KHẤU</td>
                    <td class="border"></td>
                    @foreach ($groups as $g)
                        <td data-label="TỔNG {{ $g->name }}" class="align-middle border text-end text-uppercase">
                            {!! textPrice($reports->chiet_khau['group_' . $g->id], 'success') !!}
                        </td>
                    @endforeach
                </tr>
                <tr class="none-data fw-bold">
                    <td class="border"></td>
                    <td class='align-middle border text-start'>LỢI NHUẬN GỘP</td>
                    <td class="border"></td>
                    @foreach ($groups as $g)
                        <td data-label="TỔNG {{ $g->name }}" class="align-middle border text-end text-uppercase">
                            {!! textPrice($reports->loinhuangop['group_' . $g->id], 'success') !!}
                        </td>
                    @endforeach
                </tr>
                <tr class="none-data fw-bold">
                    <td class="border"></td>
                    <td class='align-middle border text-start'>LÃI / LỖ</td>
                    <td class="border"></td>
                    @foreach ($groups as $g)
                        <td data-label="TỔNG {{ $g->name }}" class="align-middle border text-end text-uppercase">
                            {!! textPrice(
                                $reports->lailo['group_' . $g->id],
                                $reports->lailo['group_' . $g->id] >= 0 ? 'success' : 'danger',
                            ) !!}
                        </td>
                    @endforeach
                </tr>
                <tr class="none-data fw-bold">
                    <td class="border"></td>
                    <td class='align-middle border text-start'>LỖ KỲ TRƯỚC CHUYỂN SANG</td>
                    <td class="border"></td>
                    @foreach ($groups as $g)
                        <td data-label="TỔNG {{ $g->name }}" class="align-middle border text-end text-uppercase">
                            {!! textPrice($beforeReport->chuyenthangsau['group_' . $g->id] ?? 0, 'danger') !!}
                        </td>
                    @endforeach
                </tr>
                <tr class="none-data fw-bold">
                    <td class="border"></td>
                    <td class='align-middle border text-start'>LỢI NHUẬN SAU KHI TÍNH</td>
                    <td class="border"></td>
                    @foreach ($groups as $g)
                        @php
                            if ($reports->lailo['group_' . $g->id] >= 0) {
                                $lnst =
                                    $reports->lailo['group_' . $g->id] +
                                    $beforeReport->chuyenthangsau['group_' . $g->id];
                            } else {
                                $lnst =
                                    $reports->lailo['group_' . $g->id] +
                                    $beforeReport->chuyenthangsau['group_' . $g->id];
                            }
                        @endphp
                        <td data-label="TỔNG {{ $g->name }}" class="align-middle border text-end text-uppercase">
                            {!! textPrice($lnst, 'success') !!}
                        </td>
                    @endforeach
                </tr>
                <tr class="none-data fw-bold">
                    <td class="border"></td>
                    <td class='align-middle border text-start'>60% LỢI NHUẬN DOANH NGHIỆP</td>
                    <td class="border"></td>
                    @foreach ($groups as $g)
                        <td data-label="TỔNG {{ $g->name }}" class="align-middle border text-end text-uppercase">
                            {!! textPrice($reports->price_dn['group_' . $g->id] ?? 0, 'success') !!}
                        </td>
                    @endforeach
                </tr>
                <tr class="none-data fw-bold">
                    <td class="border"></td>
                    <td class='align-middle border text-start'>20% CHIA TRONG THÁNG</td>
                    <td class="border"></td>
                    @foreach ($groups as $g)
                        <td data-label="TỔNG {{ $g->name }}" class="align-middle border text-end text-uppercase">
                            {!! textPrice($reports->price_trongthang['group_' . $g->id], 'success') !!}
                        </td>
                    @endforeach
                </tr>
                <tr class="none-data fw-bold">
                    <td class="border"></td>
                    <td class='align-middle border text-start'>20% NHẬN CUỐI NĂM</td>
                    <td class="border"></td>
                    @foreach ($groups as $g)
                        <td data-label="TỔNG {{ $g->name }}" class="align-middle border text-end text-uppercase">
                            {!! textPrice($reports->price_cuoinam['group_' . $g->id], 'success') !!}
                        </td>
                    @endforeach
                </tr>
                <tr class="none-data fw-bold">
                    <td class="border"></td>
                    <td class='align-middle border text-start'>LỖ CHUYỂN THÁNG SAU</td>
                    <td class="border"></td>
                    @foreach ($groups as $g)
                        <td data-label="TỔNG {{ $g->name }}" class="align-middle border text-end text-uppercase">
                            {!! textPrice($reports->chuyenthangsau['group_' . $g->id], 'danger') !!}
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
    <div class="paginations"></div>
</div>
<script>
    $("#report_id").val('{{ $reports->id }}')
    $("#monthData").val('{{ dateFormat($reports->time, 'm-Y') }}')
    $(document).on('change', '#searchchiphipb', async function() {
        let id = $(this).val();
        let value = searchModal.value().get();
        value.search = id;
        await getData(value);
    })
</script>
