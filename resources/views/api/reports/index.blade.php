<div class="row">
    <div class="row justify-content-start my-5">
        <div
            class="col-6 col-md-4 mb-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-xxl-0 border-bottom-xxl-0 border-end-md border-bottom pb-2 pt-2 pb-xxl-0">
            <a href="/bank-accounts" data-type="2" class="redirectHome">
                <span class="uil fs-5 lh-1 uil-usd-circle text-primary"></span>
                <h4 id="totalOrder" class="fs-8 pt-3">
                    {{ number_format($ketoanSum) }} ₫
                </h4>
                <p class="fs-9 mb-0">Tổng số dư tài khoản</p>
            </a>
        </div>

        <div
            class="col-6 col-md-4 mb-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-xxl-0 border-bottom-xxl-0 border-end-md border-bottom pb-2 pt-2 pb-xxl-0">
            <a href="/debt">
                <span class="uil fs-5 lh-1 uil-usd-circle text-primary"></span>
                <h4 id="totalPriceToday" class="fs-8 pt-3">
                    {{ number_format($customerDebt) }} ₫
                </h4>
                <p class="fs-9 mb-0">Tổng công nợ khách hàng</p>
            </a>
        </div>
        <div
            class="col-6 col-md-4 mb-4 col-xxl-2 text-center border-translucent border-start-xxl border-end-xxl-0 border-bottom-xxl-0 border-end-md border-bottom pb-2 pt-2 pb-xxl-0">
            <a href="/suppliers">
                <span class="uil fs-5 lh-1 uil-usd-circle text-success"></span>
                <h4 class="fs-8 pt-3">
                    {{ number_format($supplierDebt) }} ₫
                </h4>
                <p class="fs-9 mb-0">Tổng công nợ nhà cung cấp</p>
            </a>
        </div>
    </div>
</div>
<div class="row">
    <h5 class='text-center'>Biểu đồ biến động số dư tài khoản trong 15 ngày qua</h5>
    <div class="echart-line-chart-example" data-options='{{ $biendongsodu15Day }}' style="min-height:300px"></div>
</div>
<script>
    (() => {
    const { getColor, getData } = window.phoenix.utils;
    const $chartEl = document.querySelector('.echart-line-chart-example');
    let options = JSON.parse($chartEl.dataset.options);
    const months = [],data = [];
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
                textStyle: { color: getColor('light-text-emphasis') },
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
                axisTick: { show: false },
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
                axisTick: { show: false },
                axisLine: { show: false },
            },
            series: [
                {
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
                }
            ],
            grid: { right: '3%', left: '5%', bottom: '10%', top: '5%' }
        });
        echartSetOption(chart, userOptions, getDefaultOptions);
    }
})();
</script>