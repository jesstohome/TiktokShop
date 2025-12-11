define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'merchant/recharge/index' + location.search,
                    add_url: 'merchant/recharge/add',
                    edit_url: 'merchant/recharge/edit',
                    // del_url: 'merchant/recharge/del',
                    multi_url: 'merchant/recharge/multi',
                    import_url: 'merchant/recharge/import',
                    table: 'merchant_recharge',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'recharge_id',
                sortName: 'recharge_id',
                search: false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'recharge_id', title: __('Recharge_id')},
                        // {field: 'mer_id', title: __('Mer_id')},
                        {field: 'merchant.mer_name', title: __('Merchant.mer_name'), operate: 'LIKE'},
                        {field: 'order_id', title: __('Order_id'), operate: 'LIKE'},
                        {field: 'price', title: __('Price'), operate:false},
                        {field: 'recharge_type', title: __('Recharge_type'), searchList: {"0":__('Recharge_type 0'),"1":__('Recharge_type 1'),"2":__('Recharge_type 2'),"3":__('Recharge_type 3')}, formatter: Table.api.formatter.normal},
                        {field: 'currency_type', title: __('Currency_type'), operate: 'LIKE'},
                        // {field: 'paid', title: __('Paid'), searchList: {"0":__('Paid 0'),"1":__('Paid 1')}, formatter: Table.api.formatter.normal},
                        // {field: 'pay_time', title: __('Pay_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'image', title: __('充值凭证'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'status', title: __('Status'), searchList: {"-1":__('Status -1'),"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'merchant.mer_id', title: __('Merchant.mer_id')},
                        // {field: 'merchant.mer_name', title: __('Merchant.mer_name'), operate: 'LIKE'},
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
                                    url: 'merchant/recharge/verify',
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
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
