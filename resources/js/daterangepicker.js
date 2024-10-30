$(document).ready(function () {

    // Cài đặt locale tiếng Việt cho moment
    moment.locale('vi');

    $(function () {
        var start = moment().startOf('month');
        var end = moment();
        const typeFormat = 'D [Tháng] M, YYYY';

        function cb(start, end) {
            let content = 'Tháng này: '+start.format(typeFormat) + ' - ' + end.format(typeFormat);
            let cont = start.format(typeFormat) + ' - ' + end.format(typeFormat);
            if(start.format(typeFormat) == end.format(typeFormat)){
                cont = start.format(typeFormat);
            }
            $('input[name="dates"]').on('apply.daterangepicker', function(ev, picker) {
                if(!cont.includes(picker.chosenLabel)){
                    cont = picker.chosenLabel+': '+cont;
                }
                $('input[name="dates"]').val(cont);
            });
            $('input[name="dates"]').val(content);
        }

        $('input[name="dates"]').daterangepicker({
            startDate: start,
            endDate: end,
            locale: {
                format: typeFormat,
                applyLabel: "Áp dụng",
                cancelLabel: "Hủy",
                fromLabel: "Từ",
                toLabel: "Đến",
                customRangeLabel: "Tùy chỉnh",
                daysOfWeek: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
                monthNames: ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5",
                    "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11",
                    "Tháng 12"],
                firstDay: 1
            },
            ranges: {
                'Hôm nay': [moment(), moment()],
                'Hôm qua': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '7 ngày qua': [moment().subtract(7, 'days'), moment()],
                '30 ngày qua': [moment().subtract(29, 'days'), moment()],
                'Tháng này': [moment().startOf('month'), moment()],
                'Tháng trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Tất cả thời gian': [moment().subtract(10, 'year').startOf('day'), moment().endOf('day')]
            }
        }, cb);
        
        cb(start, end);
    });
});
