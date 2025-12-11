define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/recharge/index' + location.search,
                    add_url: 'user/recharge/add',
                    edit_url: 'user/recharge/edit',
                    // del_url: 'user/recharge/del',
                    multi_url: 'user/recharge/multi',
                    import_url: 'user/recharge/import',
                    table: 'user_recharge',
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
                        // {field: 'user_id', title: __('User_id')},
                        {field: 'user.username', title: __('User.username'), operate: 'LIKE'},
                        {field: 'order_sn', title: __('Order_sn'), operate: 'LIKE'},
                        {field: 'price', title: __('Price'), operate:'BETWEEN'},
                        // {field: 'give_price', title: __('Give_price'), operate:'BETWEEN'},
                        {field: 'recharge_type', title: __('Recharge_type'), searchList: {"0":__('Recharge_type 0'),"1":__('Recharge_type 1')}, formatter: Table.api.formatter.normal},
                        // {field: 'paid', title: __('Paid'), searchList: {"0":__('Paid 0'),"1":__('Paid 1')}, formatter: Table.api.formatter.status},
                        // {field: 'pay_time', title: __('Pay_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'image', title: __('充值凭证'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'status', title: __('Status'), searchList: {"-1":__('Status -1'),"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'user.id', title: __('User.id')},
                        // {field: 'user.username', title: __('User.username'), operate: 'LIKE'},
                        // {field: 'user.nickname', title: __('User.nickname'), operate: 'LIKE'},
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
                                    url: 'user/recharge/verify',
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
