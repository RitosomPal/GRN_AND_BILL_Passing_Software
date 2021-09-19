function GrnListByBookAndSupplierId(userId, book, supplier) {
    return new Promise((resolve, reject) => {
        $.post("./api/grn/GrnListByBookAndSupplierId.php",{userId, book, supplier})
        .done(function(data) {
            if(data.success) {
                resolve(data.data)
            } else {
                reject(data.msg);
            }
        })
        .fail(function() {
            reject('Network Error!');
        })
    })
}

function GrnListBySupplierId(userId, supplier) {
    return new Promise((resolve, reject) => {
        $.post("./api/grn/GrnListBySupplierId.php",{userId, supplier})
        .done(function(data) {
            if(data.success) {
                resolve(data.data)
            } else {
                reject(data.msg);
            }
        })
        .fail(function() {
            reject('Network Error!');
        })
    })
}

function BpmListByBillId(userId, billId) {
    return new Promise((resolve, reject) => {
        $.post("./api/grn/BpmListByBillId.php",{userId, billId})
        .done(function(data) {
            if(data.success) {
                resolve(data.data)
            } else {
                reject(data.msg);
            }
        })
        .fail(function() {
            reject('Network Error!');
        })
    })
}
