// ====================================
// Variables
let itemCount = 0
let manualIndex = 0
let total = 0
let receiptBody = $('#js--receipt-body')
let finalTotal = $('#js--final-total')
let totalLabel = $('#js--total-label')

$('#manual_price').number(true, 0, ',', '.')
$('#manual_quantity').number(true, 0, ',', '.')
$('#js--receipt-form').hide()
// ====================================
// Functions
function resetManualForm() {
    $('#manual_product').val('')
    $('#manual_quantity').val('')
    $('#manual_unit').val('')
    $('#manual_price').val('')
}

function setupNumberFormat() {
    $('.js--quantity').number(true, 0, ',', '.')
    $('.js--price').number(true, 0, ',', '.')
    $('.js--subtotal').number(true, 0, ',', '.')
}

function renderItemHtml(type, item) {
    return `
        <tr id="${type}-${item.id}">
            <td><input type="text" name="${type}[${item.id}][product]" value="${item.product}" required></td>
            <td><input type="text" name="${type}[${item.id}][quantity]" placeholder="0" value="${item.quantity}" class="js--quantity" required></td>
            <td><input type="text" name="${type}[${item.id}][unit]" value="${item.unit}" required></td>
            <td><input type="text" name="${type}[${item.id}][price]" value="${item.price}" placeholder="0" class="js--price" required></td>
            <td><input type="text" name="${type}[${item.id}][subtotal]" value="${item.quantity * item.price}" class="js--subtotal" readonly required></td>
            <td class="collapsing">
                <button class="circular ui icon button js--remove-button">
                    <i class="icon minus"></i>
                </button>
            </td>
        </tr>
    `
}

function addItem(type, item) {
    let itemHtml = renderItemHtml(type, item)
    receiptBody.append(itemHtml)
    setupNumberFormat()

    itemCount++
}

function removeItem(type, id) {
    if (type === 'workOrder') {
        $(`#check-${id}`).prop('checked', false)
        $(`#${type}-${id}`).remove()
    } else if (type === 'manual') {
        $(`#${type}-${id}`).remove()
    }

    itemCount--
}

function countTotal() {
    total = 0
    $('.js--subtotal').each(function(index) {
        total += parseInt($(this).val())
    })
    finalTotal.val(total)
    
    totalLabel.text(finalTotal.val()).number(true, 0, ',', '.')
}

function countSubTotal(elm, quantity, price) {
    elm.val(quantity * price)
}

function checkItemCount() {
    if (itemCount === 6) {
        $('.js--toggle-work').attr('disabled', true)
        $('#js--manual-button').attr('disabled', true)
    } else {
        $('.js--toggle-work').removeAttr('disabled')
        $('#js--manual-button').removeAttr('disabled', true)
    }
}

// ====================================
// Event listener
// 1. Toggle WorkOrder (Checked)
$('.js--toggle-work').on('change', function () {
    if ($(this)[0].checked) {
        const parentElm = $(this).parent()
        let item = {
            'id': parentElm.siblings('.js--workOrder-id').val(),
            'product': parentElm.siblings('.js--workOrder-product').val(),
            'quantity': parseInt(parentElm.siblings('.js--workOrder-quantity').val()),
            'unit': parentElm.siblings('.js--workOrder-unit').val(),
            'price': 0
        }

        if (itemCount === 0) {
            $('#js--receipt-form').show()
            $('#js--message').hide()
        }

        addItem('workOrder', item)
        countTotal()
        checkItemCount()
    }
})

// 2. Toggle WorkOrder (Unchecked)
$('.js--toggle-work').on('change', function () {
    if (!$(this)[0].checked) {
        removeItem('workOrder', $(this).val())

        if (itemCount === 0) {
            $('#js--receipt-form').hide()
            $('#js--message').show()
        }

        countTotal()
        checkItemCount()
    }
})

// 3. Add button (Manual work)
$('#js--manual-form').submit(function() {
    let item = {
        'id': manualIndex,
        'product': $('#manual_product').val(),
        'quantity': parseInt($('#manual_quantity').val()),
        'unit': $('#manual_unit').val(),
        'price': parseInt($('#manual_price').val())
    }

    manualIndex++

    if (itemCount === 0) {
        $('#js--receipt-form').show()
        $('#js--message').hide()
    }

    addItem('manual', item)
    countTotal()
    checkItemCount()
    $(this).trigger('reset')
})

// 4. Remove button
$(document).on('click', '.js--remove-button', function (event) {
    event.preventDefault()

    let itemRow = $(this).parent().parent()
    let type = itemRow.attr('id').split('-')[0]
    let id = itemRow.attr('id').split('-')[1]

    removeItem(type, id)

    if (itemCount === 0) {
        $('#js--receipt-form').hide()
        $('#js--message').show()
    }

    countTotal()
    checkItemCount()
})

// 5. Keyup on quantity and price
$('#js--receipt-body').on('keyup', '.js--quantity', function() {
    let quantity = $(this).val()
    let price = $(this).parent().next().next().find('.js--price').val()
    let subtotalElm = $(this).parent().next().next().next().find('.js--subtotal')

    countSubTotal(subtotalElm, quantity, price)
    countTotal()
})

$('#js--receipt-body').on('keyup', '.js--price', function() {
    let price = $(this).val()
    let quantity = $(this).parent().prev().prev().find('.js--quantity').val()
    let subtotalElm = $(this).parent().next().find('.js--subtotal')
   
    countSubTotal(subtotalElm, quantity, price)
    countTotal()
})

