class Reset {
    reset() {
        let empty = this.form.querySelectorAll(".empty");
        empty.forEach(ele => {
            let checkChoice = ele.hasAttribute("data-choice") ? true : false;
            if (checkChoice) {
                let nameSelect = ele.getAttribute('name');
                if (ele.hasAttribute("multiple")) {
                    this.choices[nameSelect]?.removeActiveItems();
                }else{
                    this.choices[nameSelect]?.setChoiceByValue("");
                }
            } else {
                ele.value = '';
            }
        });
        let listData = this.form.querySelectorAll('[class*="set"]');
        listData.forEach(element => {
            let classes = element.className.split(' ');
            classes.forEach(cls => {
                if (cls.includes('set')) {
                    let dataSet = cls.split('-');
                    if (dataSet.length > 1 && dataSet[0] == 'set') {
                        let checkChoice = element.hasAttribute("data-choice") ? true : false;
                        if (checkChoice) {
                            let nameSelect = element.getAttribute('name');
                            this.choices[nameSelect]?.setChoiceByValue(String(dataSet[1]));
                        }else{
                            element.value = dataSet[1];
                        }
                    }
                }
            });
        });
        removeAllValidationClasses(this.form);
    }
}

class Validator {
    static min(ele, value) {
        let tinh = removeCommas(ele.value.trim());
        if (isNaN(tinh) || tinh < value) {
            changeValidateMessage(ele, true, `${ele.placeholder} tối thiều là ${value}`);
            return false;
        }
        return true;
    }
    static max(ele, value) {
        let tinh = removeCommas(ele.value.trim());
        if (isNaN(tinh) || tinh > value) {
            changeValidateMessage(ele, true, `${ele.placeholder} tối đa là ${value}`);
            return false;
        }
        return true;
    }
    static length(ele,value) {
        let tinh = ele.value.trim();
        if (String(tinh.length) != value) {
            changeValidateMessage(ele, true, `${ele.placeholder} phải là ${value} ký tự`);
            return false;
        }
        return true;
    }
    static number(ele) {
        let value = ele.value.replace(/,/g, '');
        if (!/^[0-9]+$/.test(value)) {
            changeValidateMessage(ele, true, `${ele.placeholder} phải là số`);
            return false;
        }
        return true;
    }

    /**
     * @param formid nhận vào form
     * @param validations nhận vào bản thiết kế bao gồm
     * const validations = [
            {name: "tên thẻ", condition: value => điều kiện, message: "thông báo người dùng"},
        }
     */
    static validateAll(formId, validations) {
        const dom = {};
        const data = {};

        // Lấy ra form và kiểm tra xem form có tồn tại không
        const form = document.querySelector(formId);
        if (!form) {
            console.error(`Form with id ${formId} not found.`);
            return false; // Trả về false nếu không tìm thấy form
        }

        const selects = form.querySelectorAll("select");
        const inputs = form.querySelectorAll("input");
        const textareas = form.querySelectorAll("textarea");

        // Thu thập tất cả các thẻ select, input, textarea vào đối tượng dom
        const collectElements = (elements) => {
            elements.forEach(item => {
                let name = item.getAttribute("name");
                if (name) {
                    dom[name] = item;
                }
            });
        };

        collectElements(selects);
        collectElements(inputs);
        collectElements(textareas);

        // Kiểm tra dựa theo bảng thiết kế
        for (let { name, condition, message } of validations) {
            let field = dom[name];
            if (field && !condition(field.value)) {
                changeValidateMessage(field, true, message, ["p-2", "small"]);
                return false; // Trả về false để chỉ ra rằng kiểm tra không thành công
            }
            if (field) {
                changeValidateMessage(field, false, '', []);
            }
        }

        // Nếu tất cả các kiểm tra đều thành công, thu thập dữ liệu từ các trường
        for (let key in dom) {
            let item = dom[key];
            data[key] = item.value;
        }

        return data;
    }
}

class Form extends Reset {
    data = {};
    choices = {};
    validate = {};
    submit = () => { };
    isValidate = true;
    constructor() {
        super();
    }
    addValidate(arrays) {
        for (let arr of arrays) {
            this.validate[arr[0]] = arr[1];
        }
    }
    handleValidate(item) {
        let name = this.validate[item.name];
        let check = true;
        if (name) {
            for (let vl of name) {
                let arr = vl.split(":");
                let validatorType = arr[0];
                let value = arr[1];
                switch (validatorType) {
                    case "min": {
                        let validatorValue = parseFloat(value);
                        if (item.value !== '') {
                            let test = Validator.min(item, validatorValue);
                            if (!test) {
                                check = false;
                            }
                        }
                        break;
                    }
                    case "max": {
                        let validatorValue = parseFloat(value);
                        if (item.value !== '') {
                            let test = Validator.max(item, validatorValue);
                            if (!test) {
                                check = false;
                            }
                        }
                        break;
                    }
                    case "number": {
                        if (item.value !== '') {
                            let test = Validator.number(item);
                            if (!test) {
                                check = false;
                            }
                        }
                        break;
                    }
                    case "length": {
                        if (item.value !== '') {
                            let test = Validator.length(item,value);
                            if (!test) {
                                check = false;
                            }
                        }
                        break;
                    }
                    default:
                        break;
                }
            }
        }
        return check;
    }
    checkValidate() {
        let eles = this.form.querySelectorAll(".validate");
        let check = true;
        eles.forEach((item) => {
            let test;

            if (item.tagName.toLowerCase() === "select") {
                let checkChoice = item.hasAttribute("data-choice") ? true : false;
                test = validateSelectOption(item, item.title, checkChoice);
            } else {
                test = validateNotEmpty(item);
                if (test) {
                    test = this.handleValidate(item);
                }
            }
            if (!test) check = false;
        });
        return check;
    }
    removeCommas(numberString) {
        const cleanedString = numberString.replace(/,/g, "");
        const number = parseInt(cleanedString, 10);
        return number;
    };
    dateTimeFormat(date, format = "d-m-Y") {
        let currentDate;
        if (date == "0000-00-00 00:00:00" || date == "0000-00-00" || date == "") {
            return "";
        }
        if (typeof date === "string" || typeof date === "number") {
            currentDate = new Date(date);
        } else {
            return "";
        }

        let seconds = currentDate.getSeconds().toString().padStart(2, "0"); // Add leading zero if needed
        let minutes = currentDate.getMinutes().toString().padStart(2, "0"); // Add leading zero if needed
        let hours = currentDate.getHours().toString().padStart(2, "0"); // Add leading zero if needed
        let day = currentDate.getDate().toString().padStart(2, "0"); // Add leading zero if needed
        let month = (currentDate.getMonth() + 1).toString().padStart(2, "0"); // Month is zero-based
        let year = currentDate.getFullYear();
        let result = format.replace("i", minutes);
        result = result.replace("s", seconds);
        result = result.replace("H", hours);
        result = result.replace("d", day);
        result = result.replace("m", month);
        result = result.replace("Y", year);
        return result;
    };
    formatNumber(numberString, max = 0, groupSeparator = ',', decimalSeparator = '.') {
        const number = parseFloat(numberString);
        if (isNaN(number)) {
            throw new Error("Invalid number string");
        }
        const options = {
            minimumFractionDigits: 0,
            maximumFractionDigits: max,
            useGrouping: true,
        };
        const formattedNumber = number.toLocaleString("en-US", options);

        const customFormattedNumber = formattedNumber
            .replace(/,/g, groupSeparator)
            .replace(/\./g, decimalSeparator);

        return customFormattedNumber;
    };
    setValidate() {
        let elementValidator = this.form.querySelectorAll(".validate");
        elementValidator.forEach((item) => {
            if (item.tagName.toLowerCase() === "select") {
                item.addEventListener('change', (e) => {
                    if (this.isValidate) {
                        let checkChoice = item.hasAttribute("data-choice") ? true : false;
                        validateSelectOption(item, item.title, checkChoice);
                    }
                });
            } else {
                item.addEventListener('input', (e) => {
                    if (this.isValidate) {
                        validateNotEmpty(item);
                        this.handleValidate(item);
                    }
                });
            }
        });
    }
    value() {
        let listData = [...this.form.querySelectorAll(".value")];
        let data = listData.reduce((acc, item) => {
            let checkChoice = item.hasAttribute("data-choice");
            if (checkChoice && item.hasAttribute("multiple")) {
                acc[item.name] = this.choices[item?.name]?.getValue()?.map(item => item.value) ?? [];
            } else {
                acc[item.name] = item.value.trim();
            }
            return acc;
        }, {});
        this.data = data;
        return this;
    }
    get() {
        return this.data;
    }
    logError(errors) {
        for (let name in errors) {
            let ele = this.form.querySelector(`[name="${name}"]`);
            if (ele) {
                if (ele.tagName.toLowerCase() == "select") {
                    let checkChoice = ele.hasAttribute("data-choice");
                    changeValidateMessage(ele, true, errors[name][0], ["p-2", "small"], checkChoice);
                } else {
                    changeValidateMessage(ele, true, errors[name][0]);
                }
            }
        }
    }
    setChoice() {
        let listSelect = [...this.form.querySelectorAll(".choice")];
        let data = listSelect.reduce((acc, item) => {
            acc[item.name] = new Choices(item, choiceOption);
            return acc;
        }, {});
        this.choices = data;
    }
    showValue(value, format = { price: [], date: [] }) {
        format.date = format.date || [];
        format.price = format.price || [];
        for (let name in value) {
            let ele = this.form.querySelector(`[name="${name}"]`);
            if (ele) {
                if (ele.tagName.toLowerCase() == "select") {
                    let checkChoice = ele.hasAttribute("data-choice");
                    if (checkChoice && Object.keys(this.choices).length > 0) {
                        let dataSet = value[name];
                        if (ele.hasAttribute("multiple")) {
                            dataSet = dataSet.map(item => {
                                return String(item);
                            })
                        }else{
                            dataSet = String(dataSet);
                        }
                        this.choices[name].setChoiceByValue(dataSet);
                    } else {
                        ele.value = value[name];
                    }
                } else {
                    if (format.price.includes(name)) {
                        ele.value = value[name] ? this.formatNumber(value[name]) : 0;
                    } else if (format.date.includes(name)) {
                        ele.value = this.dateTimeFormat(value[name], 'd-m-Y');
                    } else {
                        ele.value = value[name];
                    }
                }
            }
        }

    }
    formatPrice(array = []) {
        for (let name in this.data) {
            if (array.includes(name)) {
                let value = this.removeCommas(this.data[name]);
                this.data[name] = isNaN(value) ? '' : value;
            }
        }
        return this;
    }

    execute() {
        this.form.addEventListener("submit", (e) => this.submit(e));
    }
}


class File {
    /**
     * Hàm xuất file excel.
     * 
     * @param {btn} element Button của thẻ muốn làm hiệu ứng loading
     * @param {Config} {
     *      route: 'đường dẫn tới api xử lý',
     *      method: 'method của route - mặc định POST',
     *      filename: 'Tên của file',
     *      btnText: 'Text của btn khi loading'
     *      data: {} Data của bản muốn đẩy lên để lọc - nếu có
     * } 
     * @returns {number} Số ngày giữa hai ngày
     */
    static async export(btn, paramConfig = {}) {
        const defaultConfig = {
            route: '',
            method: 'POST',
            filename: 'filename',
            btnText: '',
            data: {},
        };
        const mergedConfig = Object.assign({}, defaultConfig, paramConfig);

        let config = {
            method: mergedConfig.method,
            url: mergedConfig.route,
            data: mergedConfig.data,
            responseType: "blob",
        };

        btnLoading(btn, true, mergedConfig.btnText);
        try {
            let fetch = await axios(config).then((res) => res);
            const url = window.URL.createObjectURL(new Blob([fetch.data]));
            const link = document.createElement("a");
            link.href = url;
            link.setAttribute("download", mergedConfig.filename + ".xlsx"); // Tên file tải về
            document.body.appendChild(link);
            link.click();
            link.parentNode.removeChild(link);
        } catch (error) {
            showErrorMD('Đã xảy ra lỗi, vui lòng thử lại sau!');
        }
        btnLoading(btn, false, mergedConfig.btnText);
    }
}


class HandleForm extends Form {
    modal = null;
    modalBT = null;
    form = null;
    constructor(modal) {
        super();
        this.modal = document.querySelector(modal);
        this.form = this.modal.querySelector("form");
        this.modalBT = new bootstrap.Modal(this.modal);
        this.setValidate();
        this.execute();
    }

    closeReset() {
        this.modal.addEventListener('hide.bs.modal', () => {
            this.reset();
        });
    }

    setBackModal(modal) {
        let btnBack = this.form.querySelector(".btn-prev-modal");
        if (btnBack) {
            btnBack.addEventListener("click", () => {
                this.hideModal();
                modal.showModal();
            });
        }
    }

    loading(type = false, text = '') {
        btnLoading(this.form.querySelector("button[type='submit']"), type, text);
    }

    redirectMD(url) {
        let modal = document.getElementById("modalSuccessNotification");
        modal.addEventListener('hide.bs.modal', function () {
            window.location.href = url;
        });
    }

    hideModal() {
        this.modalBT.hide();
    }
    showModal() {
        this.modalBT.show();
    }

}

class RequestServer {
    constructor(route) {
        this.index = 1;
        this.route = route;
        this.response = null;
        this.colspan = 5;
        this.id = 0;
        this.tbody = "data_table_body";
        this.paginations = ".paginations";
        this.content = "";
        this.init = false;
        this.params = {};
        this.totalContent = null;
        this.insert = () => { };
    }

    bold(id) {
        return this.id == id ? 'tr-bold' : '';
    }
    async get(url = "") {
        let links = url === "" ? this.route : `${url}`;

        let params = {};
        if (!this.init) {
            params = {...this.params,...this.getParams()};
        } else {
            params = this.params;
        }
        for (let i in params) {
            if(typeof params[i] === 'string'){
                if (params[i].trim() === '') {
                    delete params[i];
                }
            }
        }
        if(!params.dates){
            const datesCookie = document.cookie.split('; ').find(row => row.startsWith('dates='));
            if (datesCookie) {
                const datesValue = datesCookie.split('=')[1];
                params['dates'] = datesValue;
            }
        }

        try {
            let response = await axios.get(links, { params }).then(res => res);
            if (response.status === 200) {
                this.index = response.data.from;
                this.response = response.data;
                this.setParams(response.data);
                // this.setCookie(response.data);
                this.showData();
                this.id = 0;
                this.init = true;
            }
        } catch (error) {
            console.log(error);
        }
    }

    setLabel(){
        let tbodyElement = document.getElementById(this.tbody);
        let tableElement = tbodyElement.closest('table');
        if (tableElement) {
            tableElement.classList.add('table-config');
            let headers = tableElement.querySelectorAll('thead th'); 
            let rows = tableElement.querySelectorAll('tbody tr');
            rows.forEach(function(row) {
                if (!row.classList.contains('none-data')) {
                    let cells = row.querySelectorAll('td'); 
                    cells.forEach(function(cell, index) {
                        let label = headers[index].textContent.trim();
                        label = label.toUpperCase();
                        cell.setAttribute('data-label', label); 
                    });
                }
            });
        }
    }

    getParams(query = '') {
        var searchParams = null;
        if (query === '') {
            searchParams = new URLSearchParams(window.location.search);
        } else {
            searchParams = new URLSearchParams(query);
        }
        searchParams = [...searchParams];
        let params = searchParams.reduce((acc, item) => {
            if (item[0].endsWith('[]')) {
                let key = item[0].slice(0, -2);
                if (!acc[key]) {
                    acc[key] = [];
                }
                acc[key].push(item[1]);
            } else {
                acc[item[0]] = item[1];
            }
            return acc;
        }, {});
        return params;
    }

    reset() {
        this.params = {};
        this.get(this.route);
    }

    setParams(data) {
        const urlParams = new URLSearchParams(data.params);
        const dates = urlParams.get('dates');
        if (dates) {
            document.cookie = `dates=${dates}; path=/;`;
            urlParams.delete('dates');
        }
        const page = urlParams.get('page');
        if(page == 1){
            urlParams.delete('page');
        }

        let url = new URL(data.url + '?' + urlParams.toString());
        this.params = this.getParams(data.params);
        const newUrl = `${window.location.protocol}//${window.location.host}${window.location.pathname}${url.search}`;
        window.history.replaceState({ path: newUrl }, '', newUrl);
    }

    setCookie(data){
        console.log(data);
    }

    showData() {
        let data = this.response.data;
        this.content = this.insert(data);
        let contentLoading = "";
        if (data.length <= 0) {
            contentLoading = `<tr><td colspan="${this.colspan}" class="text-center fw-bold fs-7 text-danger">Chưa có dữ liệu</td></tr>`;
            document.getElementById(this.tbody).innerHTML = contentLoading;
        } else {
            document.getElementById(this.tbody).innerHTML = this.content;
            this.setLabel();
        }

        let paginations = document.querySelector(this.paginations);
        this.setPaginations(this.response, paginations);
    }

    setPaginations = (data, element) => {
        if (this.totalContent) {
            this.totalContent.textContent = data.total;
        }
        let pathHere = new URL(data.url + data.params);
        pathHere.searchParams.get('page');
        let pathALl = pathHere.toString();
        pathHere.searchParams.delete('show_all');
        let pathNotALl = pathHere.toString();
        let html = `<div class="row align-items-center justify-content-between py-2 pe-0 fs-9">
            <div class="col-auto d-flex">
                <p class="mb-0 d-none d-sm-block me-3 fw-semibold text-body">
                ${data.from} đến ${data.to}<span class="text-body-tertiary"> Trong </span> ${data.total}
                </p>
                <a class="btn-link" href="javascript:" title="Tất cả" ${data.all ? "hidden" : ""
            } data-pagelinks="${pathALl + "&show_all=true"
            }">Tất cả<span class="fas fa-angle-right ms-1"></span></a>
                <a class="btn-link" href="javascript:" title="Thu gọn" ${data.all ? "" : "hidden"
            } data-pagelinks="${pathNotALl}">
                    Thu gọn<span class="fas fa-angle-left ms-1"></span>
                </a>
            </div>
                `;
        if (!data.all) {
            let urlNP = new URL(data.url + data.params);
            urlNP.searchParams.set('page', parseInt(data.currentPage) - 1);
            let prePage = urlNP.toString();
            urlNP.searchParams.set('page', parseInt(data.currentPage) + 1);
            let nextPage = urlNP.toString();
            html += `
                <nav class="col-auto d-flex">
                    <ul class="mb-0 pagination justify-content-end">
                        <li class="page-item ${data.currentPage <= 1 ? "disabled" : ""
                }">
                            <a class="page-link ${data.currentPage <= 1 ? "disabled" : ""
                }" ${data.currentPage <= 1 ? 'disabled=""' : ""
                } href="javascript:" title="Trang trước" data-pagelinks="${prePage.toString()}">
                                <span class="fas fa-chevron-left"></span>
                            </a>
                        </li>`;
            html += getPage(data);
            html += `
                <li class="page-item ${data.currentPage >= data.totalPages ? "disabled" : ""
                }">
                            <a class= "page-link ${data.currentPage >= data.totalPages ? "disabled" : ""
                }"  href="javascript:" title="Trang sau"  data-pagelinks="${nextPage.toString()
                }" >
                            <span class= "fas fa-chevron-right"></span>
                            </a>
                        </li>
                    </ul>
                </nav>
                `;
        }
        html += `
            </div>
            `;
        if (element) {
            element.innerHTML = html;
        }

        function getPage(data) {
            let html = "";
            let start = +data.currentPage - 3;
            let max = +data.currentPage + 3;

            if (start > 1) {
                html += `<li class="page-item disabled"><a class="page-link" disabled="" title="" type="button" href="javascript:">...</a>`;
            }

            for (let index = start; index <= max; index++) {
                if (index > 0 && index <= +data.totalPages) {
                    if (index == +data.currentPage) {
                        html += `<li class="page-item active"><a class="page-link" title="Trang ${index}" href="javascript:" type="button">${index}</a></li>`;
                    } else {
                        let pathUrl = new URL(data.url + data.params);
                        pathUrl.searchParams.set('page', index);
                        html += `<li class="page-item"><a class="page-link" type="button" title="Trang ${index}" href="javascript:" data-pagelinks="${pathUrl.toString()}">${index}</a></li>`;
                    }
                }
            }
            if (max < +data.totalPages) {
                html += `<li class="page-item disabled"><a class="page-link" disabled="" title="" type="button" href="javascript:">...</a></li>`;
            }
            return html;
        }

        if (element) {
            let pageLinks = element.querySelectorAll("a[data-pageLinks]");
            pageLinks.forEach((link) => {
                link.addEventListener("click", async () => {
                    btnLoading(link, true, link.textContent);
                    let pageLinks = link.getAttribute("data-pageLinks");
                    this.params = {};
                    await this.get(pageLinks);
                    btnLoading(link, false, link.textContent);
                });
            });
        }
    };
}
class HandleConfirm {

    success = () => { };

    config = {
        text: 'Bạn có chắc chắn muốn xóa không?',
        btnText: 'Xóa',
        btnBg: 'red',
    }

    loading(type = false, text = '') {
        btnLoading(this.btnConfirm, type, text);
    }


    constructor() {
        this.modal = document.getElementById("modalConfirmDelete");
        this.btnConfirm = this.modal.getElementsByClassName("btn-confirm")[0];
        this.modalBT = new bootstrap.Modal(this.modal, { keyboard: false });
        this.element = this.modal.getElementsByClassName("confirm-message")[0];
        this.btnConfirm.addEventListener("click", (e) => { this.success(e) });
    }

    hide() {
        this.modalBT.hide();
    }

    show() {
        this.btnConfirm.style.backgroundColor = this.config.btnBg;
        this.element.innerHTML = this.config.text;
        this.btnConfirm.textContent = this.config.btnText;
        this.modalBT.show();
    }

}

class Status {
    static user(status) {
        if (status == 1) {
            return `<span class='badge fs-10 bg-primary-subtle text-primary-emphasis'>Hoạt động</span>`;
        }
        return `<span class='badge fs-10 bg-danger-subtle text-danger-emphasis'>Ngừng hoạt động</span>`;
    }

    static status(status) {
        if (status) {
            return `<span class='badge fs-10'
            style='background-color:${status?.bg};color: ${status?.color}'
            >${status?.name}</span>`;
        } else {
            return `<span class='badge fs-10 bg-warning'>Không xác định</span>`;
        }
    }

    static order_log(status) {
        if (status == 1) {
            return `<span class='badge fs-10 bg-primary-subtle text-primary-emphasis'>Chưa duyệt</span>`;
        } else if (status == 2) {
            return `<span class='badge fs-10 bg-success-subtle text-success-emphasis'>Đã duyệt</span>`;
        }
        return `<span class='badge fs-10 bg-danger-subtle text-danger-emphasis'>Đã hủy</span>`;
    }
}

class Log {
    static danger(text) {
        return `<span class='badge fs-10 bg-danger-subtle text-danger-emphasis'>${text}</span>`;
    }
    static info(text) {
        return `<span class='badge fs-10 bg-info-subtle text-info-emphasis'>${text}</span>`;
    }
    static success(text) {
        return `<span class='badge fs-10 bg-success-subtle text-success-emphasis'>${text}</span>`;
    }
    static warning(text) {
        return `<span class='badge fs-10 bg-warning-subtle text-warning-emphasis'>${text}</span>`;
    }
    static primary(text) {
        return `<span class='badge fs-10 bg-primary-subtle text-primary-emphasis'>${text}</span>`;
    }
}

class VN {
    static async render(modal) {
        let province = modal.choices.province_id;
        let district = modal.choices.district_id;
        let ward = modal.choices.ward_id;
        let provinceData = await axios.get(`/api/vn/provinces`).then(res => res.data);
        let data = [{
            label: 'Chọn tỉnh thành',
            value: ''
        }];
        provinceData.forEach(item => {
            data.push({
                label: item.name,
                value: item.id
            });
        });
        province.setChoices(data, 'value', 'label', true);
        province.setChoiceByValue('');

        province.passedElement.element.addEventListener('change', async (e) => {
            let districtData = await axios.get(`/api/vn/districts/${e.target.value}`).then(res => res.data);
            let data = [{
                label: 'Chọn quận huyện',
                value: ''
            }];
            districtData.forEach(item => {
                data.push({
                    label: item.name,
                    value: item.id
                });
            });
            district.setChoices(data, 'value', 'label', true);
            district.setChoiceByValue('');
            ward.setChoiceByValue('');
            removeAllValidationClasses(district.passedElement.element.closest('.form-floating'))
            removeAllValidationClasses(ward.passedElement.element.closest('.form-floating'))
        });

        district.passedElement.element.addEventListener('change', async (e) => {
            let wardData = await axios.get(`/api/vn/wards/${e.target.value}`).then(res => res.data);
            let data = [{
                label: 'Chọn thị xã',
                value: ''
            }];
            wardData.forEach(item => {
                data.push({
                    label: item.name,
                    value: item.id
                });
            });
            ward.setChoices(data, 'value', 'label', true);
            ward.setChoiceByValue('');
            removeAllValidationClasses(ward.passedElement.element.closest('.form-floating'))
        });
    }
    static async show(data, modal) {
        let province_id = data.province_id;
        let province = modal.choices.province_id;
        let provinceData = await axios.get(`/api/vn/provinces`).then(res => res.data);
        let dataProvince = [{
            label: 'Chọn tỉnh thành',
            value: ''
        }];
        provinceData.forEach(item => {
            dataProvince.push({
                label: item.name,
                value: item.id,
                selected: item.id == province_id
            });
        });
        province.setChoices(dataProvince, 'value', 'label', true);

        let district = modal.choices.district_id;
        let district_id = data.district_id;

        let districtData = await axios.get(`/api/vn/districts/${province_id}`).then(res => res.data);
        let dataDistrict = [{
            label: 'Chọn quận huyện',
            value: ''
        }];
        districtData.forEach(item => {
            dataDistrict.push({
                label: item.name,
                value: item.id,
                selected: item.id == district_id
            });
        });
        district.setChoices(dataDistrict, 'value', 'label', true);

        let ward = modal.choices.ward_id;
        let ward_id = data.ward_id;
        let wardData = await axios.get(`/api/vn/wards/${district_id}`).then(res => res.data);
        let dataWard = [{
            label: 'Chọn thị xã',
            value: ''
        }];
        wardData.forEach(item => {
            dataWard.push({
                label: item.name,
                value: item.id,
                selected: item.id == ward_id
            });
        });
        ward.setChoices(dataWard, 'value', 'label', true);
    }
}

class Text {
    static info = (text) => `<span class='fw-bold text-info'>${text}</span>`;
    static success = (text) => `<span class='fw-bold text-success'>${text}</span>`;
    static primary = (text) => `<span class='fw-bold text-primary'>${text}</span>`;
    static warning = (text) => `<span class='fw-bold text-warning'>${text}</span>`;
    static danger = (text) => `<span class='fw-bold' style='color:red'>${text}</span>`;
    static default = (text) => `<span class='fw-bold'>${text}</span>`;
}
