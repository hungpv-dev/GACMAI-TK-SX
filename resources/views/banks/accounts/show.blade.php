@extends('layouts.app')

@section('title')
    Chi tiết tài khoản ngân hàng
@endsection

@section('content')
    <div class="content">
        <nav class="mb-2" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="/bank-accounts">Tài khoản ngân hàng</a></li>
                <li class="breadcrumb-item">Chi tiết tài khoản</li>
            </ol>
        </nav>
        <h2 class="text-bold text-body-emphasis mb-5">Chi tiết tài khoản ngân hàng</h2>
        <div>
            <div class="row">
                <div class="col-sm-6 col-md-2 mb-4">
                    <h5>Ngân hàng</h5>
                    <i>{{ optional($bankAccount->bank)->name ?? 'Chưa có' }}</i>
                </div>
                <div class="col-sm-6 col-md-2 mb-4">
                    <h5>Số tài khoản</h5>
                    <i>{{ $bankAccount->bank_number }}</i>
                </div>
                <div class="col-sm-6 col-md-2 mb-4">
                    <h5>Tên tài khoản</h5>
                    <i>{{ $bankAccount->full_name }}</i>
                </div>
                <div class="col-sm-6 col-md-2 mb-4">
                    <h5>Loại tài khoản</h5>
                    <i>{{ optional($bankAccount->bank_account_type)->name }}</i>
                </div>
                <div class="col-sm-6 col-md-2 mb-4">
                    <h5>Số dư đầu kì</h5>
                    <i>{{ number_format($bankAccount->opening_balance) }} ₫</i>
                </div>
                <div class="col-sm-6 col-md-2 mb-4">
                    <h5>Số dư hiện tại</h5>
                    <i>{{ number_format($bankAccount->current_balance) }} ₫</i>
                </div>
            </div>
            
            <div class="row my-10">
                <h5 class='text-center'>Lịch sử biến động số dư tài khoản trong 15 ngày gần nhất</h5>
                <div class="echart-line-chart-example" data-options='{{ $biendongsodu15Day }}' style="min-height:300px"></div>
            </div>

            <h4>Lịch sử biến động số dư</h4>
            <div id="searchModel">
                <form class="d-flex align-items-center gap-3 flex-wrap my-3" id="filter-form">
                    <div>
                        <input name="created_at" style="width: 350px" type="text" placeholder="Thời gian" class="form-control empty value">
                    </div>
                    <div>
                        <input type="text" name="note" class='form-control empty value' placeholder="Ghi chú">
                    </div>
                    <div>
                        <input name="amount_min" type="text" placeholder="Số tiền tối thiểu"
                            class="form-control empty value" oninput="formatBalance(event)">
                    </div>
                    <div>
                        <input name="amount_max" type="text" placeholder="Số tiền tối đa"
                            class="form-control empty value" oninput="formatBalance(event)">
                    </div>
                    <div class=" d-flex justify-content-start">
                        <button type="submit" class="btn btn-sm btn-phoenix-info btn-filter me-2" title="Lọc">
                            <span class="fas fa-filter text-info fs-9 me-2"></span>Lọc
                        </button>
                        <button button class="btn btn-sm btn-phoenix-warning" onclick="removeFilter(this)"
                            type="button">Xoá lọc</button>
                    </div>
                </form>
            </div>
            <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-y mt-2 position-relative top-1"
                id="list_users_container">
                <div class="table-responsive quote-table-container scrollbar ms-n1 ps-1">
                    <table class="table table-hover table-sm fs-9 mb-0">
                        <thead>
                            <tr>
                                <th class="align-middle text-start text-uppercase">Thời gian giao dịch</th>
                                <th class="align-middle text-end text-uppercase">Tiền vào</th>
                                <th class="align-middle text-end text-uppercase">Tiền ra</th>
                                <th class="align-middle text-end text-uppercase">Phí giao dịch</th>
                                <th class="align-middle text-end text-uppercase">Số tiền sau giao dịch</th>
                                <th class="align-middle text-start text-uppercase">Loại giao dịch</th>
                                <th class="align-middle text-start text-uppercase">chi tới</th>
                                <th class="align-middle text-start text-uppercase">Người thực hiện</th>
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
        var idSelectBank = undefined
    </script>
    <script>
        (() => {
            const {
                getColor,
                getData
            } = window.phoenix.utils;
            const $chartEl = document.querySelector('.echart-line-chart-example');
            const options = JSON.parse($chartEl.getAttribute('data-options'));
            const months = [],
                data = [];
            options.forEach(item => {
                months.push(dateTimeFormat(item.date));
                data.push(item.total);
            });

            const tooltipFormatter = params => {
                return `
                    <div>
                        <h6 class="fs-9 text-body-tertiary mb-0">
                            <span class="fas fa-circle me-1" style='color:${params[0].borderColor}'></span>
                            ${params[0].name} : ${formatCurrency(params[0].value)}
                        </h6>
                    </div>
                `;
            };

            if ($chartEl) {
                const userOptions = getData($chartEl, 'echarts');
                const chart = window.echarts.init($chartEl);
                const getDefaultOptions = () => ({
                    tooltip: {
                        trigger: 'axis',
                        padding: [7, 10],
                        backgroundColor: getColor('body-highlight-bg'),
                        borderColor: getColor('border-color'),
                        textStyle: {
                            color: getColor('light-text-emphasis')
                        },
                        borderWidth: 1,
                        transitionDuration: 0,
                        formatter: tooltipFormatter,
                        axisPointer: {
                            type: 'none'
                        }
                    },
                    xAxis: {
                        type: 'category',
                        data: months,
                        boundaryGap: false,
                        axisLine: {
                            lineStyle: {
                                color: getColor('tertiary-bg')
                            }
                        },
                        axisTick: {
                            show: false
                        },
                        axisLabel: {
                            color: getColor('quaternary-color'),
                            formatter: value => value,
                            margin: 15
                        },
                        splitLine: {
                            show: false
                        }
                    },
                    yAxis: {
                        type: 'value',
                        max: Math.ceil(Math.max(...data) * 2),
                        splitLine: {
                            lineStyle: {
                                type: 'dashed',
                                color: getColor('secondary-bg')
                            }
                        },
                        boundaryGap: false,
                        axisLabel: {
                            show: true,
                            color: getColor('quaternary-color'),
                            margin: 15
                        },
                        axisTick: {
                            show: false
                        },
                        axisLine: {
                            show: false
                        },
                    },
                    series: [{
                        type: 'line',
                        data,
                        itemStyle: {
                            color: getColor('body-highlight-bg'),
                            borderColor: getColor('primary'),
                            borderWidth: 2
                        },
                        lineStyle: {
                            color: getColor('primary')
                        },
                        showSymbol: false,
                        symbol: 'circle',
                        symbolSize: 10,
                        smooth: false,
                        hoverAnimation: true
                    }],
                    grid: {
                        right: '3%',
                        left: '5%',
                        bottom: '10%',
                        top: '5%'
                    }
                });
                echartSetOption(chart, userOptions, getDefaultOptions);
            }
        })();



        const route = '/api/transactions?show_all=true&order=asc&bank_account_id={{ $bankAccount->id }}';
        var searchModal = new HandleForm('#searchModel');
        searchModal.submit = async function(e) {
            e.preventDefault();
            this.loading(true);
            let value = this.value().formatPrice(['amount_min', 'amount_max']).get();
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
        var request = new RequestServer(route);
        request.colspan = 12;
        request.insert = function(data) {
            $("#sum").text(request.response.total);
            let connocuoiky = 0;
            let connodauky = 0;
            let sumtienvao = 0;
            let sumtienra = 0;
            $('.thongke-sum-price').text(formatCurrency(request.response.sum));
            let content = data.map((item, key) => {
                    if (key == 0) {
                        connodauky = item.current_balance
                    }
                    if (item.type?.type == 2) {
                        if (item.amount > 0) {
                            item.amount = -(parseFloat(item.amount));
                        }
                    }
                    let amount = formatCurrency(item.amount);
                    let lastGD = parseFloat(item.current_balance) + parseFloat(item.amount) - parseFloat(item.fee);
                    connocuoiky = lastGD
                    let tienvao = 0; tienra = 0;
                    if(item.amount < 0){
                        tienra = item.amount;
                    }else{
                        tienvao = item.amount
                    }
                    sumtienvao += parseFloat(tienvao)
                    sumtienra += parseFloat(tienra)
                    return `
                    <tr class="${request.bold(item.id)}">
                        <td class='align-middle text-start'>${dateTimeFormat(item.created_at,'d-m-Y H:i:s')} - ${Log.info(time_ago(item.created_at))}</td>
                        <td class='align-middle text-end'>${Text.success(formatCurrency(tienvao))}</td>
                        <td class='align-middle text-end'>${Text.danger(formatCurrency(tienra))}</td>
                        <td class='align-middle text-end'>${Text.warning(formatCurrency(item.fee))}</td>
                        <td class='align-middle text-end'>${Text.success(formatCurrency(lastGD))}</td>
                        <td class='align-middle text-start'>${item.type?.name ?? Log.danger('Không rõ')}</td>
                        <td class='align-middle text-start'>
                            ${showDetailTransaction(item)}
                        </td>
                        <td class='align-middle text-start'>${item.user?.name}</td>
                        <td class='align-middle text-start'>${item.note}</td>
                    </tr>
                    `;
                })
                .join('');
            content = `
                    <tr class='fw-bold none-data'>
                        <td class='text-start align-middle'>Số dư đầu kỳ: </td>
                        <td colspan='3'></td>
                        <td class='text-end align-middle'>${Text.info(formatCurrency(connodauky))}</td>
                        <td colspan='3'></td>
                    </tr>
                ` + content;
            content += `
                    <tr class='fw-bold none-data'>
                        <td class='text-start align-middle'>Số dư cuối kỳ: </td>
                        <td data-label="Tổng tiền vào" class='text-end align-middle'>${Text.success(formatCurrency(sumtienvao))}</td>
                        <td data-label="Tổng tiền ra" class='text-end align-middle'>${Text.danger(formatCurrency(sumtienra))}</td>
                        <td colspan='1'></td>
                        <td data-label="Số dư cuối kỳ" class='text-end align-middle'>${Text.info(formatCurrency(connocuoiky))}</td>
                        <td colspan='3'></td>
                    </tr>
                `;
            return content;
        }
        document.addEventListener('DOMContentLoaded', async function() {
            await request.get();
            searchModal.showValue(request.params);
        });
    </script>
@endsection
