define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/refund/index' + location.search,
                    add_url: 'order/refund/add',
                    edit_url: 'order/refund/edit',
                    del_url: 'order/refund/del',
                    multi_url: 'order/refund/multi',
                    import_url: 'order/refund/import',
                    table: 'order_refund',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'refund_id',
                sortName: 'refund_id',
                search: false,
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'refund_id', title: __('Refund_id')},
                        // {field: 'user_id', title: __('User_id')},
                        // {field: 'order_id', title: __('Order_id')},
                        {field: 'user.nickname', title: __('User.nickname'), operate: 'LIKE'},
                        {field: 'refund_sn', title: __('Refund_sn'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'receiving_status', title: __('Receiving_status'), searchList: {"0":__('Receiving_status 0'),"1":__('Receiving_status 1')}, formatter: Table.api.formatter.normal},
                        {field: 'service_type', title: __('Service_type'), searchList: {"0":__('Service_type 0'),"1":__('Service_type 1')}, formatter: Table.api.formatter.normal},
                        {field: 'reason_type', title: __('Reason_type'), operate: false},
                        // {field: 'refund_num', title: __('Refund_num')},
                        {field: 'amount', title: __('Amount'), operate:'BETWEEN'},
                        {field: 'refund_explain', title: __('Refund_explain'), operate: 'LIKE'},
                        // {field: 'express_number', title: __('Express_number'), operate: 'LIKE'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'mer_id', title: __('Mer_id')},
                        // {field: 'cart_info', title: __('Cart_info')},
                        {field: 'status', title: __('Status'), searchList: {"-1":__('Status -1'),"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        // {field: 'admin_id', title: __('Admin_id')},
                        // {field: 'admin_msg', title: __('Admin_msg'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        // {field: 'refunded_time', title: __('Refunded_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'user.id', title: __('User.id')},
                        // {field: 'user.username', title: __('User.username'), operate: 'LIKE'},
                        // {field: 'user.nickname', title: __('User.nickname'), operate: 'LIKE'},
                        // {field: 'order.order_id', title: __('Order.order_id')},
                        // {field: 'order.order_sn', title: __('Order.order_sn'), operate: 'LIKE'},
                        // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            align: 'center',
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: '审核',
                                    text: __('审核'),
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: '',
                                    hidden: function (row) {
                                        //console.log(row);
                                        if (row.status === 0) {
                                            return false;
                                        } else {
                                            return true;
                                        }
                                    },
                                    url: 'order/refund/verify',
                                },
                                {
                                    name: '详情',
                                    text: __('详情'),
                                    classname: 'btn btn-xs btn-info btn-dialog',
                                    icon: '',
                                    url: 'order/refund/product',
                                },

                            ],
                            formatter: Table.api.formatter.operate
                        },
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
        verify: function () {
            Controller.api.bindevent();
        },
        product: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/refund/product' + location.search,
                    table: 'product',
                }
            });
            var table = $("#table");

            // var product_ids = Config.product_ids;
            var order_product_ids = Config.order_product_ids;

            table.bootstrapTable({
                url: 'order/refund/product' + '?order_product_ids=' + order_product_ids,
                sortName: 'createtime',
                search: false,
                columns: [
                    [
                        // {field: 'state', checkbox: true,},
                        {field: 'product.title', title: __('商品名称'), table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'product.image', title: __('图片'), operate: false, formatter: Table.api.formatter.image},
                        {field: 'product_num', title: __('数量'), operate: false},
                        {field: 'cost', title: __('成本'), operate: false},
                        {field: 'product_price', title: __('售价'), operate: false},
                        {field: 'profit', title: __('利润'), operate: false},
                        {field: 'createtime', title: __('Createtime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                    ]
                ]
            });
            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
