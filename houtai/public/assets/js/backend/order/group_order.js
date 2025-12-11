define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/group_order/index' + location.search,
                    add_url: 'order/group_order/add',
                    edit_url: 'order/group_order/edit',
                    del_url: 'order/group_order/del',
                    multi_url: 'order/group_order/multi',
                    import_url: 'order/group_order/import',
                    table: 'group_order',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'group_order_id',
                sortName: 'group_order_id',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'group_order_id', title: __('Group_order_id')},
                        {field: 'group_order_sn', title: __('Group_order_sn'), operate: 'LIKE'},
                        {field: 'user_id', title: __('User_id')},
                        {field: 'total_postage', title: __('Total_postage'), operate:'BETWEEN'},
                        {field: 'total_price', title: __('Total_price'), operate:'BETWEEN'},
                        {field: 'total_num', title: __('Total_num')},
                        {field: 'integral', title: __('Integral')},
                        {field: 'integral_price', title: __('Integral_price'), operate:'BETWEEN'},
                        {field: 'give_integral', title: __('Give_integral')},
                        {field: 'coupon_price', title: __('Coupon_price'), operate:'BETWEEN'},
                        {field: 'pay_price', title: __('Pay_price'), operate:'BETWEEN'},
                        {field: 'pay_postage', title: __('Pay_postage'), operate:'BETWEEN'},
                        {field: 'cost', title: __('Cost'), operate:'BETWEEN'},
                        {field: 'coupon_id', title: __('Coupon_id'), operate: 'LIKE'},
                        {field: 'give_coupon_ids', title: __('Give_coupon_ids'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'paid', title: __('Paid')},
                        {field: 'pay_time', title: __('Pay_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'pay_type', title: __('Pay_type'), searchList: {"0":__('Pay_type 0'),"1":__('Pay_type 1'),"2":__('Pay_type 2'),"3":__('Pay_type 3')}, formatter: Table.api.formatter.normal},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'is_combine', title: __('Is_combine')},
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
