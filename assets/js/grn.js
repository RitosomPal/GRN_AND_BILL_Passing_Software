function GrnListByGrnId(userId, grnId) {
    return new Promise((resolve, reject) => {
        $.post("./api/grn/GrnListByGrnId.php",{userId, grnId})
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

function GrnNosByBookId(userId, bookId) {
    return new Promise((resolve, reject) => {
        $.post("./api/grn/GrnNosByBookId.php",{userId, bookId})
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

function addRow() {
    return new Promise((resolve, reject) => {
        let Index = 1;
        let SI = 1;
        if($(`#item_list tr`).length > 0) {
            let lastIndex = $(`#item_list tr:last-child label`)[0].id
            let lastSI = $(`#item_list tr:last-child label`)[0].innerText
            Index =  parseInt(lastIndex.substring(2, lastIndex.length))+1
            SI = parseInt(lastSI.substring(0, lastSI.length-1))+1
        }
        $(`#item_list`).append(`
            <tr>
                <td>
                    <div class="form-check">
                        <input class="form-check-input" name="record" type="checkbox">
                    </div>
                </td>
                <td>
                    <label id="i-${Index}">${SI}.</label>
                </td>
                <td>
                    <input type="text" class="form-control i" id="iv${Index}" list="items" autocomplete="off">
                    <input type="hidden" id="i${Index}" name="l${Index}[i]">
                </td>
                <td>
                    <input type="number" step="any" class="form-control" id="q${Index}" name="l${Index}[q]" value="0">
                </td>
                <td colspan="2">
                    <input type="text" class="form-control u" id="uv${Index}" list="units" autocomplete="off">
                    <input type="hidden" id="u${Index}" name="l${Index}[u]">
                </td>
            </tr>
        `)
        resolve({Index})
    })
}

function deleteRow(ele) {
    return new Promise((resolve, reject) => {
        $("#item_list")
        .find('input[name="record"]')
        .each(function () {
            if($("#item_list tr").length > 1) {
                $(this).is(":checked") && $(this).parents("tr").remove();
            }
        });

        let c = 1;
        $("#item_list")
        .find('label')
        .each(function () {
            $(this).text(`${c++}.`)
        });
        resolve()
    })
}

function updateList(allList) {
    return new Promise(async (resolve, reject) =>{
        $("#item_list").empty()

        await Promise.all(allList.map(async (list) => {
            try {
                let Row = await addRow();
                let Index = Row.Index;
                $(`#iv${Index}`).val(list.item);
                $(`#i${Index}`).val(list.itemId);
                $(`#q${Index}`).val(parseInt(list.qtyRec));
                $(`#uv${Index}`).val(list.unit);
                $(`#u${Index}`).val(list.unitId);
            }
            catch (e) { console.error(e) }
        }))
        resolve()
    })
}