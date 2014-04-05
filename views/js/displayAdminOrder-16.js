var sellermania_status_update_result = '';
var sellermania_error_result = '';
var sellermania_block_product_general_legend = '';
var sellermania_block_products_list = new Array();
var sellermania_right_column = '';

$(document).ready(function() {

    // Hide template
    $('#sellermania-template').hide();

    // Retrieve data
    var sellermania_title = $('#sellermania-template-title').html();
    var sellermania_customer = $('#sellermania-template-customer').html();
    var sellermania_order_summary = $('#sellermania-template-order-summary').html();
    sellermania_status_update_result = $('#sellermania-template-status-update').html();
    sellermania_error_result = $('#sellermania-template-error').html();

    // Get block identifiers
    var sellermania_block_order_state = $('#id_order_state');
    var sellermania_block_order_state_button = $('#status button[name="submitState"]');
    $('.icon-user').each(function() {
        if ($(this).parent().is('h3'))
            sellermania_right_column = $(this).parent().parent();
    });
    var sellermania_block_button_prev_next = $('#content .row .col-lg-6 .panel h3');
    var sellermania_block_order_actions = sellermania_block_button_prev_next.next();
    var sellermania_block_shipping_title = $('#myTab');
    var sellermania_block_shipping = $('#myTab').next();
    var sellermania_block_payment = $('#formAddPayment').parent().parent().parent();
    var sellermania_block_documents = $('#tabOrder li').next();
    var sellermania_add_voucher = $('#add_voucher');
    var sellermania_add_product = $('#add_product');
    var sellermania_panel_voucher = $('.panel-vouchers');
    var sellermania_panel_total = $('.panel-vouchers').next();
    var sellermania_toolbar = $('.icon-calendar-empty').parent().parent().parent();

    // Get products list block identifier
    var sellermania_order_line = 0;
    sellermania_block_products_list = new Array();
    sellermania_block_product_general_legend = $('#orderProducts').next().next().find('div:first');
    $('#orderProducts tr').each(function() {
        if (sellermania_order_line > 0)
        {
            var sellermania_order_row = 0;
            $(this).find('td').each(function() {
                if (sellermania_order_row == 1 && $(this).is(':visible'))
                    sellermania_block_products_list[sellermania_order_line] = $(this);
                sellermania_order_row++;
            });
        }
        sellermania_order_line++;
    });



    // Replace status order selection
    sellermania_block_order_state.after(sellermania_title);
    sellermania_block_order_state.hide();
    sellermania_block_order_state_button.hide();

    // Replace right column
    sellermania_right_column.html(sellermania_order_summary);
    sellermania_block_shipping.html(sellermania_customer)

    // Hide order actions
    sellermania_block_order_actions.hide();
    sellermania_block_shipping_title.hide();
    sellermania_block_payment.hide();
    sellermania_block_documents.hide();
    sellermania_add_voucher.hide();
    sellermania_add_product.hide();
    sellermania_panel_voucher.hide();
    sellermania_panel_total.hide();
    sellermania_toolbar.hide();
    $('.product_action').hide();

    // Legend
    sellermania_block_product_general_legend.hide();
});