function calcRemainingPayment(po_number) {
    $.ajax({
        type: "GET",
        data: {
            po_invoice: po_number,
        },
        dataType: 'json',
        url: "/po_remaining_payment",
        success: function(r) {
            console.log(r);
            $('#remaining_payment').val(r.remaining_payment);
        }
    });
}