define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/order_product/index' + location.search,
                    add_url: 'order/order_product/add',
                    edit_url: 'order/order_product/edit',
                    del_url: 'order/order_product/del',
                    multi_url: 'order/order_product/multi',
                    import_url: 'order/order_product/import',
                    table: 'order_product',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'order_product_id',
                sortName: 'order_product_id',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'order_product_id', title: __('Order_product_id')},
                        {field: 'order_id', title: __('Order_id')},
                        {field: 'user_id', title: __('User_id')},
                        {field: 'cart_id', title: __('Cart_id')},
                        {field: 'product_id', title: __('Product_id')},
                        {field: 'extension_one', title: __('Extension_one'), operate:'BETWEEN'},
                        {field: 'extension_two', title: __('Extension_two'), operate:'BETWEEN'},
                        {field: 'integral', title: __('Integral')},
                        {field: 'integral_price', title: __('Integral_price'), operate:'BETWEEN'},
                        {field: 'integral_total', title: __('Integral_total')},
                        {field: 'coupon_price', title: __('Coupon_price'), operate:'BETWEEN'},
                        {field: 'platform_coupon_price', title: __('Platform_coupon_price'), operate:'BETWEEN'},
                        {field: 'svip_discount', title: __('Svip_discount'), operate:'BETWEEN'},
                        {field: 'postage_price', title: __('Postage_price'), operate:'BETWEEN'},
                        {field: 'product_sku', title: __('Product_sku')},
                        {field: 'is_refund', title: __('Is_refund')},
                        {field: 'product_num', title: __('Product_num')},
                        {field: 'product_type', title: __('Product_type')},
                        {field: 'activity_id', title: __('Activity_id')},
                        {field: 'refund_num', title: __('Refund_num')},
                        {field: 'is_reply', title: __('Is_reply')},
                        {field: 'cost', title: __('Cost'), operate:'BETWEEN'},
                        {field: 'product_price', title: __('Product_price'), operate:'BETWEEN'},
                        {field: 'total_price', title: __('Total_price'), operate:'BETWEEN'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'order.order_id', title: __('Order.order_id')},
                        {field: 'order.order_sn', title: __('Order.order_sn'), operate: 'LIKE'},
                        {field: 'user.id', title: __('User.id')},
                        {field: 'user.username', title: __('User.username'), operate: 'LIKE'},
                        {field: 'product.product_id', title: __('Product.product_id')},
                        {field: 'product.title', title: __('Product.title'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
