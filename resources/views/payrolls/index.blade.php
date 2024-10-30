<h4 class="text-bold text-body-emphasis mb-5">Bảng lương tháng <span class='text-primary'
        id="roll-time">{{ $roll->month }} / {{ $roll->year }}</span></h4>
<div>

    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap my-4">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div>
                <select name="roll_id" class="form-select value empty choice" onchange="researchBangLuong(this.value)">
                    @foreach ($listPayRoll as $item)
                        <option value="{{ $item->id }}" {{ $item->id == $roll->id ? 'selected' : '' }}>Tháng
                            {{ $item->month }}, {{ $item->year }}</option>
                    @endforeach
                </select>
            </div>
            <div style="width: 300px">
                <input type="text" placeholder="Tên nhân sự..." class="form-control" oninput="searchFunction()"
                    id="search-name">
            </div>
        </div>
    </div>
    <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-y mt-2 position-relative top-1"
        id="list_users_container">
        <div class="table-responsive quote-table-container scrollbar ms-n1 ps-1">
            <table class="table-config table table-hover table-sm fs-9 mb-0">
                <thead>
                    <tr>
                        <th class="align-middle text-center text-uppercase">stt</th>
                        <th class="align-middle text-start text-uppercase">họ và tên</th>
                        <th class="align-middle text-end text-uppercase">lương chính</th>
                        <th class="align-middle text-end text-uppercase">phụ cấp</th>
                        <th class="align-middle text-end text-uppercase">tổng lương chính</th>
                        <th class="align-middle text-end text-uppercase">ngày công thực tế</th>
                        <th class="align-middle text-end text-uppercase" style="line-height: 1.5;">tổng lương
                            theo<br>ngày công thực tế</th>
                        <th class="align-middle text-end text-uppercase">lương phụ</th>
                        <th class="align-middle text-end text-uppercase">tổng lương</th>
                        <th class="align-middle text-end text-uppercase">chi phí bị trừ</th>
                        <th class="align-middle text-end text-uppercase">thực chi</th>
                        <th class="align-middle text-end text-uppercase">chi phí trừ</th>
                        <th class="align-middle text-end text-uppercase">thực lĩnh</th>
                    </tr>
                </thead>

                <tbody class="list-data fw-bold" id="data_table_body">
                    @php
                        $tongLuongChinh = 0;
                        $tongPhuCap = 0;
                        $tongLuongTheoNCThute = 0;
                        $tongLuongThem = 0;
                        $tongLuong = 0;
                        $tongThucLinh = 0;
                        $tongThucChi = 0;
                    @endphp
                    @foreach ($roll->details as $k => $item)
                        @php
                            $tongluongchinh = $item->phucap + $item->luongchinh;
                            $luongTheoNCThute = $item->luongchinh / $item->ngaycongpl * $item->ngaycongdl;
                            $tongluong = $luongTheoNCThute + $item->luongthem + $item->phucap;
                            $thuclinh = $tongluong - $item->bitru - $item->chiphitru;
                            $thucchi = $tongluong - $item->bitru;

                            $tongLuongChinh += $item->luongchinh;
                            $tongPhuCap += $item->phucap;
                            $tongLuongTheoNCThute += $luongTheoNCThute;
                            $tongLuongThem += $item->luongthem;
                            $tongLuong += $tongluong;
                            $tongThucChi += $thucchi;
                            $tongThucLinh += $thuclinh;
                        @endphp
                        <tr class="user-row">
                            <td class='align-middle text-center'>{{ $k + 1 }}</td>
                            <td class='align-middle text-start user-name' data-label="TÊN NGƯỜI DÙNG">{{ $item->user->name }}</td>
                            <td class='align-middle text-end' data-label="LƯƠNG CHÍNH">{!! textPrice($item->luongchinh, 'primary') !!}</td>
                            <td class='align-middle text-end' data-label="PHỤ CẤP"><a href='javascript:void(0)' data-id="{{ $item->id }}" data-type="1" onclick="showChiPhiTru({{ $item->id }},1)">{!! textPrice($item->phucap, 'default') !!}</a></td>
                            <td class='align-middle text-end' data-label="TỔNG LƯƠNG CHÍNH">{!! textPrice($tongluongchinh, 'default') !!}</td>
                            <td class='align-middle text-end fw-bold' data-label="NGÀY CÔNG">
                                {!! $item->ngaycongdl == $item->ngaycongpl ? textPrice($item->ngaycongdl, 'success') : textPrice($item->ngaycongdl, 'danger') !!} / {{ $item->ngaycongpl }}
                            </td>
                            <td class='align-middle text-end' data-label="LƯƠNG THEO NGÀY CÔNG THỰC TẾ">{!! textPrice($luongTheoNCThute, 'success') !!}</td>
                            <td class='align-middle text-end' data-label="LƯƠNG THÊM"><a href='javascript:void(0)' data-id="{{ $item->id }}" data-type="2" onclick="showChiPhiTru({{ $item->id }},2)">{!! textPrice($item->luongthem, 'default') !!}</a></td>
                            <td class='align-middle text-end' data-label="TỔNG LƯƠNG">{!! textPrice($tongluong, 'default') !!}</td>
                            <td class='align-middle text-end' data-label="BỊ TRỪ"><a href='javascript:void(0)' data-id="{{ $item->id }}" data-type="4" onclick="showChiPhiTru({{ $item->id }},4)">{!! textPrice($item->bitru, 'danger') !!}</a></td>
                            <td class='align-middle text-end' data-label="THUC CHI">{!! textPrice($thucchi, 'default') !!}</td>
                            <td class='align-middle text-end' data-label="CHI PHÍ TRỪ"><a href='javascript:void(0)' data-id="{{ $item->id }}" data-type="3" onclick="showChiPhiTru({{ $item->id }},3)">{!! textPrice($item->chiphitru, 'default') !!}</a></td>
                            <td class='align-middle text-end' data-label="THỰC LĨNH">{!! textPrice($thuclinh, 'primary') !!}</td>
                        </tr>
                    @endforeach
                    <tr class='fw-bold'>
                        <td class='text-center'>Tổng: </td>
                        <td></td>
                        <td class='align-middle text-end'>{!! textPrice($tongLuongChinh, 'primary') !!}</td>
                        <td class='align-middle text-end'>{!! textPrice($tongPhuCap, 'primary') !!}</td>
                        <td class='align-middle text-end'>{!! textPrice($tongLuongChinh + $tongPhuCap, 'default') !!}</td>
                        <td></td>
                        <td class='align-middle text-end'>{!! textPrice($tongLuongTheoNCThute, 'success') !!}</td>
                        <td class='align-middle text-end'>{!! textPrice($tongLuongThem, 'primary') !!}</td>
                        <td class='align-middle text-end'>{!! textPrice($tongLuong, 'default') !!}</td>
                        <td></td>
                        <td class='align-middle text-end'>{!! textPrice($tongThucChi, 'default') !!}</td>
                        <td></td>
                        <td class='align-middle text-end'>{!! textPrice($tongThucLinh, 'primary') !!}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="paginations"></div>
    </div>
</div>
<div class="modal fade" id="modelShowChiPhi" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content form-open">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Chi tiết chi phí</h5>
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
                    <table class="table">
                        <thead>
                            <th class='align-middle text-start'>Loại chi phí</th>
                            <th class='align-middle text-end'>Số tiền</th>
                            <th class='align-middle text-start'>Ghi chú</th>
                        </thead>
                        <tbody id='content-chiphi'>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    async function showChiPhiTru(id,type){
        try{
            let res = await axios.get('/api/payroll-detail/price',{
                params: {id,type}
            }).then(res => res.data);
            let content = '';
            if(res?.length > 0){
                content = res.map(item => {
                    return `
                        <tr>
                            <td data-label="Loại chi phí" class='align-middle text-start ps-2'>${item.type?.name}</td>
                            <td data-label="Số tiền" class='align-middle text-end'>${Text.info(formatCurrency(item.price))}</td>
                            <td data-label="Ghi chú" class='align-middle text-start'>${item.note || 'Không có ghi chú'}</td>
                        </tr>
                    `
                })
            }else{
                content = `
                    <tr class='none-set' style='min-width:320px'>
                        <td class='align-middle text-danger fw-bold fs-8 text-center'>
                            Chưa có chi phí nào
                        </td>
                    </tr>
                `
            }
            $('#content-chiphi').html(content);
            $('#modelShowChiPhi').modal('show');
        }catch(errors){
            console.log(errors);
        }
    }


    function removeDiacritics(str) {
        const diacriticsMap = {
            'a': 'áàảãạâấầẩẫậăắằẳẵặ',
            'e': 'éèẻẽẹêếềểễệ',
            'i': 'íìỉĩị',
            'o': 'óòỏõọôốồổỗộơớờởỡợ',
            'u': 'úùủũụưứừửữự',
            'y': 'ýỳỷỹỵ',
            'd': 'đ',
        };
        return str.split('').map(char => {
            for (let key in diacriticsMap) {
                if (diacriticsMap[key].includes(char)) {
                    return key;
                }
            }
            return char;
        }).join('');
    }

    function searchFunction() {
        const input = document.getElementById('search-name');
        const filter = removeDiacritics(input.value.toLowerCase());
        const rows = document.querySelectorAll('#data_table_body .user-row');
        rows.forEach(row => {
            const nameCell = row.querySelector('.user-name');
            let nameText = removeDiacritics(nameCell.textContent || nameCell.innerText).toLowerCase().replace(
                /đ/g, 'd');
            if (nameText.includes(filter)) {
                row.style.display = ""; // Hiện hàng
            } else {
                row.style.display = "none"; // Ẩn hàng
            }
        });
    }
    async function researchBangLuong(id){
        let value = searchModal.value().get();
        value.search = id;
        await getData(value);
    }
</script>
