<div class="row">
    <div class='row mt-5'>
        <div class="col-md-12 mt-5 col-sm-12 m-auto">
            <h4 class='text-start'>Báo cáo công nợ nhà cung cấp theo kỳ</h4>
            <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-y mt-2 position-relative top-1">
                <div class="table-responsive quote-table-container scrollbar ms-n1 ps-1">
                    <table class="table table-hover table-sm fs-9 mb-0">
                        <thead>
                            <tr>
                                <th class="align-middle text-start text-uppercase">Nhà cung cấp</th>
                                <th class="align-middle text-end text-uppercase">Công nợ cuối kỳ</th>
                                <th class="align-middle text-end text-uppercase">Công nợ hiện tại</th>
                            </tr>
                        </thead>

                        <tbody class="list-data" id="data_table_body">
                            @php
                                $total_tt = 0;
                                $total_cn = 0;
                            @endphp
                            @foreach ($suppliers as $item)
                                @php
                                    $lastInvoice = $item->supplier_invoices()->where('created_at','<=',$dateQuery[1])->latest()->first();
                                    if($lastInvoice){
                                        if($lastInvoice->tran_type->type == 2){
                                            $cncuoiky = $lastInvoice->current_amount - $lastInvoice->amount;
                                        }else{
                                            $cncuoiky = $lastInvoice->current_amount + $lastInvoice->amount;
                                        }
                                    }else{
                                        $cncuoiky = $item->opening_amount;
                                    }
                                    $total_tt += $cncuoiky;
                                    $total_cn += $item->current_amount;
                                @endphp
                                <tr>
                                    <td data-label="Nhà cung cấp" class='align-middle fw-bold text-start'>
                                        <a href='/suppliers/{{ $item->id }}'>{{ $item->code }} -
                                            {{ $item->name }}</a>
                                    </td>
                                    <td data-label="Công nợ cuối kỳ" class='align-middle text-end'>
                                        <a href='/suppliers/{{ $item->id }}?created_at={{ $dates }}'>
                                            {!! textPrice($cncuoiky) !!}
                                        </a>
                                    </td>
                                    <td data-label="Công nợ hiện tại" class='align-middle text-end'>
                                        {!! textPrice($item->current_amount) !!}
                                    </td>
                                </tr>
                            @endforeach
                            <tr class='fw-bold none-data'>
                                <td class='align-middle text-start'>TỔNG:</td>
                                <td data-label="Công nợ cuối kỳ" class='align-middle text-end'>
                                    <span class='fw-bold'>{!! textPrice($total_tt, 'success') !!}</span>
                                </td>
                                <td data-label="Công nợ hiện tại" class='align-middle text-end'>
                                    <span class='fw-bold'>{!! textPrice($total_cn, 'danger') !!}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="paginations"></div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('js/config.js') }}"></script>
<script src="{{ asset('js/lodash.min.js') }}"></script>
<script src="{{ asset('js/phoenix.js') }}"></script>
<script src="{{ asset('js/echarts.min.js') }}"></script>
<script src="{{ asset('js/chart.js') }}"></script>
<script>
    function getColorRandom(getColor, currentColor) {
        let randomColor;
        let attempts = 0; // Biến đếm số lần thử

        const isBrightColor = (color) => {
            // Chuyển đổi mã màu hex thành giá trị RGB
            const r = parseInt(color.slice(1, 3), 16);
            const g = parseInt(color.slice(3, 5), 16);
            const b = parseInt(color.slice(5, 7), 16);
            // Tính tổng độ sáng, có thể điều chỉnh ngưỡng này
            return (r + g + b) > 382; // 255 * 3 / 2
        };
        const isNearWhite = (color) => {
            const r = parseInt(color.slice(1, 3), 16);
            const g = parseInt(color.slice(3, 5), 16);
            const b = parseInt(color.slice(5, 7), 16);
            const brightnessThreshold = 240; // Ngưỡng cho màu gần trắng
            return (r > brightnessThreshold && g > brightnessThreshold && b > brightnessThreshold);
        };
        do {
            // Tạo màu ngẫu nhiên theo định dạng hex
            randomColor = '#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0');
            attempts++;

            if (attempts >= 10) {
                // Nếu đã thử 10 lần nhưng không tìm được màu sáng, dừng lại
                return null; // Hoặc có thể xử lý khác
            }
        } while (currentColor.includes(randomColor) || !isBrightColor(randomColor) || isNearWhite(
                randomColor)); // Kiểm tra trùng màu và độ sáng

        return randomColor;
    }
</script>
<script>
    ((options) => {
        const {
            getColor,
            getData,
            rgbaColor
        } = window.phoenix.utils;
        const $chartEl = document.querySelector('.chart-all-group');
        let currenColor = [];
        let data = options.map((item) => {
            let cl = getColorRandom(getColor, currenColor);
            currenColor.push(cl)
            return {
                value: item.total_thuc_thu,
                name: item.group_name,
                itemStyle: {
                    color: cl
                }
            }
        });

        if ($chartEl) {
            const userOptions = getData($chartEl, 'echarts');
            const chart = window.echarts.init($chartEl);
            const getDefaultOptions = () => ({
                legend: {
                    left: 'left',
                    textStyle: {
                        color: getColor('tertiary-color')
                    }
                },
                series: [{
                    type: 'pie',
                    radius: window.innerWidth < 530 ? '45%' : '60%',
                    label: {
                        color: getColor('tertiary-color')
                    },
                    center: ['50%', '55%'],
                    data: data,
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: rgbaColor(getColor('tertiary-color'), 0.5)
                        }
                    }
                }],
                tooltip: {
                    trigger: 'item',
                    padding: [7, 10],
                    backgroundColor: getColor('body-highlight-bg'),
                    borderColor: getColor('border-color'),
                    textStyle: {
                        color: getColor('light-text-emphasis')
                    },
                    borderWidth: 1,
                    transitionDuration: 0,
                    axisPointer: {
                        type: 'none'
                    }
                }
            });

            const responsiveOptions = {
                xs: {
                    series: [{
                        radius: '45%'
                    }]
                },
                sm: {
                    series: [{
                        radius: '60%'
                    }]
                }
            };
            echartSetOption(chart, userOptions, getDefaultOptions, responsiveOptions);
        }
    })(@json($chartAllGroup));
    ((options) => {
        const {
            getColor,
            getData,
            rgbaColor
        } = window.phoenix.utils;
        const $chartEl = document.querySelector('.chart-all-group-ttd');
        let currenColor = [];
        let data = options.map((item) => {
            let cl = getColorRandom(getColor, currenColor);
            currenColor.push(cl)
            return {
                value: item.total_thuc_thu,
                name: item.group_name,
                itemStyle: {
                    color: cl
                }
            }
        });

        if ($chartEl) {
            const userOptions = getData($chartEl, 'echarts');
            const chart = window.echarts.init($chartEl);
            const getDefaultOptions = () => ({
                legend: {
                    left: 'left',
                    textStyle: {
                        color: getColor('tertiary-color')
                    }
                },
                series: [{
                    type: 'pie',
                    radius: window.innerWidth < 530 ? '45%' : '60%',
                    label: {
                        color: getColor('tertiary-color')
                    },
                    center: ['50%', '55%'],
                    data: data,
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: rgbaColor(getColor('tertiary-color'), 0.5)
                        }
                    }
                }],
                tooltip: {
                    trigger: 'item',
                    padding: [7, 10],
                    backgroundColor: getColor('body-highlight-bg'),
                    borderColor: getColor('border-color'),
                    textStyle: {
                        color: getColor('light-text-emphasis')
                    },
                    borderWidth: 1,
                    transitionDuration: 0,
                    axisPointer: {
                        type: 'none'
                    }
                }
            });

            const responsiveOptions = {
                xs: {
                    series: [{
                        radius: '45%'
                    }]
                },
                sm: {
                    series: [{
                        radius: '60%'
                    }]
                }
            };
            echartSetOption(chart, userOptions, getDefaultOptions, responsiveOptions);
        }
    })(@json($chartAllGroupTTD));
</script>

<script>
    ((options) => {
        const {
            getColor,
            getData
        } = window.phoenix.utils;
        const $chartEl = document.querySelector('.doanh-thu-tt-gv-ln');
        const months = [];
        const dataMap = {};

        const dataFormatter = obj => {
            return Object.keys(obj).reduce((acc, val) => {
                return {
                    ...acc,
                    [val]: obj[val].map((value, index) => {
                        return {
                            name: months[index],
                            value
                        }
                    })
                };
            }, {});
        };
        const tattoan = [];
        const giavon = [];
        const loinhuangop = [];
        options.forEach(item => {
            tattoan.push(item.total_thuc_thu);
            giavon.push(item.total_chi_phi);
            loinhuangop.push(item.total_thuc_thu - item.total_chi_phi);
            months.push(item.group_name);
        })

        dataMap.dataPI = dataFormatter({
            tattoan: tattoan,
        });
        dataMap.dataTI = dataFormatter({
            giavon: giavon,
        });
        dataMap.dataDI = dataFormatter({
            loinhuangop: loinhuangop,
        });

        if ($chartEl) {
            const userOptions = getData($chartEl, 'echarts');
            const chart = window.echarts.init($chartEl);
            const getDefaultOptions = () => ({
                baseOption: {
                    timeline: {
                        show: false,
                        axisType: 'category',
                        autoPlay: false,
                        playInterval: 1000,
                        label: {
                            formatter: s => {
                                return new Date(s).getFullYear();
                            }
                        },
                        lineStyle: {
                            color: 'transparent'
                        },
                        itemStyle: {
                            color: 'transparent'
                        },
                        checkpointStyle: {
                            color: 'transparent',
                            shadowBlur: 0,
                            shadowOffsetX: 0,
                            shadowOffsetY: 0
                        },
                        controlStyle: {
                            color: 'transparent'
                        }
                    },
                    title: {
                        textStyle: {
                            color: getColor('tertiary-color')
                        }
                    },
                    tooltip: {
                        trigger: 'axis',
                        axisPointer: {
                            type: 'shadow'
                        },
                        padding: [7, 10],
                        backgroundColor: getColor('body-highlight-bg'),
                        borderColor: getColor('border-color'),
                        textStyle: {
                            color: getColor('light-text-emphasis')
                        },
                        borderWidth: 1,
                        transitionDuration: 0,
                        formatter: tooltipFormatter
                    },
                    legend: {
                        left: 'right',
                        data: ['Doanh thu tất toán', 'Giá vốn', 'Lợi nhận gộp'],
                        textStyle: {
                            color: getColor('tertiary-color')
                        },
                    },
                    calculable: true,
                    xAxis: [{
                        type: 'category',
                        data: months,
                        splitLine: {
                            show: false
                        },
                        axisLabel: {
                            color: getColor('quaternary-color')
                        },
                        axisLine: {
                            lineStyle: {
                                color: getColor('quaternary-color')
                            }
                        }
                    }],
                    yAxis: [{
                        type: 'value',
                        axisLabel: {
                            formatter: value => `${value}`,
                            color: getColor('quaternary-color')
                        },
                        splitLine: {
                            lineStyle: {
                                color: getColor('secondary-bg')
                            },
                        }
                    }],
                    series: [{
                        name: 'Doanh thu tất toán',
                        type: 'bar',
                        itemStyle: {
                            color: getColor('primary'),
                            barBorderRadius: [3, 3, 0, 0]
                        },
                        label: {
                            show: true,
                            position: 'top',
                            color: getColor('light-text-emphasis'),
                            fontSize: '12px',
                            fontWeight: 'bold',
                            formatter: params => formatCurrency(params.value)
                        },
                        barMaxWidth: '80px'
                    }, {
                        name: 'Giá vốn',
                        type: 'bar',
                        itemStyle: {
                            color: getColor('warning'),
                            barBorderRadius: [3, 3, 0, 0]
                        },
                        label: {
                            show: true,
                            position: 'top',
                            color: getColor('light-text-emphasis'),
                            fontSize: '12px',
                            fontWeight: 'bold',
                            formatter: params => formatCurrency(params.value)
                        },
                        barMaxWidth: '80px'
                    }, {
                        name: 'Lợi nhận gộp',
                        type: 'bar',
                        itemStyle: {
                            color: getColor('success'),
                            barBorderRadius: [3, 3, 0, 0]
                        },
                        label: {
                            show: true,
                            position: 'top',
                            color: getColor('light-text-emphasis'),
                            fontSize: '12px',
                            fontWeight: 'bold',
                            formatter: params => formatCurrency(params.value)
                        },
                        barMaxWidth: '80px'
                    }],
                    grid: {
                        top: '10%',
                        bottom: '15%',
                        left: 5,
                        right: 10,
                        containLabel: true
                    }
                },
                options: [{
                    // title: { text: 'Biểu đồ thông kê đơn hàng trong kỳ' },
                    series: [{
                        data: dataMap.dataPI['tattoan']
                    }, {
                        data: dataMap.dataTI['giavon']
                    }, {
                        data: dataMap.dataDI['loinhuangop']
                    }]
                }, ]
            });
            echartSetOption(chart, userOptions, getDefaultOptions);
        }
    })(@json($chartAllGroup));
</script>
