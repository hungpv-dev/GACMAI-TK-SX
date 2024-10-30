const route = '/api/users/total-price';
var request = new RequestServer(route);
request.colspan = 12;
request.insert = function (data) {
    let content =  data.map((item) => {
        if(item.orders_sum_thuc_thu && item.orders_sum_thuc_thu != 0){
            return `
            <tr class="${request.bold(item.id)}">
                <td class='align-middle text-center'>${request.index++}</td>
                <td class='align-middle text-start'>${item.name}</td>
                <td class='align-middle text-start'>${item.group.name}</td>
                <td class='align-middle text-end'><a href='#' onclick='handleRedirect(${item.id})'>${formatNumber(item.orders_sum_thuc_thu ?? 0)} ₫</a></td>
                <td class='align-middle text-end'>${item.orders_count}</td>
            </tr>
            `;
        }
    })
    .join('');
    return content;
}
function handleRedirect(user_id){
    location.href = `/orders?user_id=${user_id}&finish_at=${document.querySelector('[name="dates"]').value}`;
}