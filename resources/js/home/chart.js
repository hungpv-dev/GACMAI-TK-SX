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
    } while (currentColor.includes(randomColor) || !isBrightColor(randomColor) || isNearWhite(randomColor)); // Kiểm tra trùng màu và độ sáng

    return randomColor;
}


const chartAllGroup = (options) => {
    const {
        getColor,
        getData,
        rgbaColor
    } = window.phoenix.utils;
    const $chartEl = document.querySelector('.chart-all-group');
    let currenColor = [];
    let data = options.map((item) => {
        let cl = getColorRandom(getColor, currenColor);
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
};
const chartAllGroupTTD = (options) => {
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
};

const chartUserGroupTTD = (options) => {
    const {
        getColor,
        getData,
        rgbaColor
    } = window.phoenix.utils;
    const $chartEl = document.querySelector('.chart-all-user-tdd');
    let currenColor = [];
    let data = options.map((item) => {
        let cl = getColorRandom(getColor, currenColor);
        return {
            value: item.transactions_sum_amount,
            name: item.name,
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
};
const chartUserGroup = (options) => {
    const {
        getColor,
        getData,
        rgbaColor
    } = window.phoenix.utils;
    const $chartEl = document.querySelector('.chart-all-user');
    let currenColor = [];
    let data = options.filter(item => item.total_du_kien != 0).map((item) => {
        let cl = getColorRandom(getColor, currenColor);
        currenColor.push(cl)
        return {
            value: item.total_du_kien,
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
};
async function getData(params){
    let responseTotal = await axios.get('/api/home',{params}).then(res => res.data);
    $('#totalCustomer').text(responseTotal.customerCount);
    $('#totalOrder').text(responseTotal.orderCount);
    $('#totalPriceToday').text(formatCurrency(responseTotal.tienThuHomNay));
    $('#totalPriceByPeriod').text(formatCurrency(responseTotal.tienThuTheoKy));
    $('#totalPriceOrderSuccess').text(formatCurrency(responseTotal.orderThucThuSuccess));
    $('#totalCountOrderUnSuccess').text(responseTotal.orderUnSuccessCount);
    $('#totalCountOrderSuccess').text(responseTotal.orderSuccessCount);
    $('#totalPriceCongNo').text(formatCurrency(responseTotal.congNoCanThu));
    $('#orderBoCoc').text(formatCurrency(responseTotal.orderBoCoc));
    $('#totalPriceOrderDuKien').text(formatCurrency(responseTotal.totalPriceOrderDuKien));
    chartAllGroup(responseTotal.chartAllGroup);
    chartAllGroupTTD(responseTotal.chartAllGroupTTD);
    chartUserGroupTTD(responseTotal.chartUserGroupTTD);
    barTimelineChartInit(responseTotal.chartUserOK);
    chartUserGroup(responseTotal.orderDuKien);
}
function barTimelineChartInit(options){
    const { getColor, getData } = window.phoenix.utils;
    const $chartEl = document.querySelector('.echart-bar-timeline-chart-example');
    const months = [];
    const dataMap = {};

    const dataFormatter = obj => {
        return Object.keys(obj).reduce((acc, val) => {
            return {
                ...acc,
                [val]: obj[val].map((value, index) => ({
                    name: months[index],
                    value
                }))
            };
        }, {});
    };
    const customers = [];
    const orders = [];
    options.forEach(item => {
        customers.push(item.customer_count);
        orders.push(item.order_count);
        months.push(item.name);
    })

    dataMap.dataPI = dataFormatter({
        customers: customers,
    });

    dataMap.dataTI = dataFormatter({
        orders: orders,
    });

    if ($chartEl) {
        const userOptions = getData($chartEl, 'echarts');
        const chart = window.echarts.init($chartEl);
        const getDefaultOptions = () => ({
            baseOption: {
                timeline: {
                    axisType: 'category',
                    show: false,
                    autoPlay: false,
                    playInterval: 1000,
                    label: {
                        formatter: s => {
                            return new Date(s).getFullYear();
                        }
                    },
                    lineStyle: {
                        color: getColor('info')
                    },
                    itemStyle: {
                        color: getColor('secondary')
                    },
                    checkpointStyle: {
                        color: getColor('primary'),
                        shadowBlur: 0,
                        shadowOffsetX: 0,
                        shadowOffsetY: 0
                    },
                    controlStyle: {
                        color: getColor('info')
                    },
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
                    textStyle: { color: getColor('light-text-emphasis') },
                    borderWidth: 1,
                    transitionDuration: 0,
                    formatter: tooltipFormatter
                },
                legend: {
                    left: 'right',
                    data: ['Tổng số khách hàng', 'Tổng số đơn hàng'],
                    textStyle: {
                        color: getColor('tertiary-color')
                    }
                },
                calculable: true,
                xAxis: [
                    {
                        type: 'category',
                        data: months,
                        splitLine: { show: false },
                        axisLabel: {
                            color: getColor('quaternary-color')
                        },
                        axisLine: {
                            lineStyle: {
                                color: getColor('quaternary-color')
                            }
                        },
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        axisLabel: {
                            formatter: value => `${value}`,
                            color: getColor('quaternary-color')
                        },
                        splitLine: {
                            lineStyle: {
                                color: getColor('secondary-bg')
                            }
                        },
                    }
                ],
                series: [
                    {
                        name: 'Tổng số khách hàng',
                        type: 'bar',
                        itemStyle: {
                            color: getColor('primary'),
                            barBorderRadius: [3, 3, 0, 0]
                        },
                        label: {
                            show: true,
                            position: 'top',
                            color: getColor('light-text-emphasis'),
                            fontSize: '16px',
                            fontWeight: 'bold',
                        },
                        barMaxWidth: '80px'
                    },
                    {
                        name: 'Tổng số đơn hàng',
                        type: 'bar',
                        itemStyle: {
                            color: getColor('success'),
                            barBorderRadius: [3, 3, 0, 0]
                        },
                        label: {
                            show: true,
                            position: 'top',
                            color: getColor('light-text-emphasis'),
                            fontSize: '16px',
                            fontWeight: 'bold',
                        },
                        barMaxWidth: '80px'
                    }
                ],
                grid: {
                    top: '10%',
                    bottom: '15%',
                    left: 5,
                    right: 10,
                    containLabel: true
                }
            },
            options: [
                {
                    // title: { text: 'Biểu đồ thông kê đơn hàng trong kỳ' },
                    series: [
                        { data: dataMap.dataPI['customers'] },
                        { data: dataMap.dataTI['orders'] }
                    ]
                },
            ]
        });
        echartSetOption(chart, userOptions, getDefaultOptions);
    }
};