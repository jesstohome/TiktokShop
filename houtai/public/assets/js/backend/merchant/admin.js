define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'merchant/admin/index' + location.search,
                    add_url: 'merchant/admin/add',
                    edit_url: 'merchant/admin/edit',
                    del_url: 'merchant/admin/del',
                    multi_url: 'merchant/admin/multi',
                    import_url: 'merchant/admin/import',
                    table: 'merchant_admin',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'mer_admin_id',
                sortName: 'mer_admin_id',
                search: false,
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'mer_admin_id', title: __('Mer_admin_id')},
                        {field: 'mer_id', title: __('Mer_id')},
                        {field: 'account', title: __('Account'), operate: 'LIKE'},
                        {field: 'pwd', title: __('Pwd')},
                        {field: 'real_name', title: __('Real_name'), operate: 'LIKE'},
                        {field: 'phone', title: __('Phone'), operate: 'LIKE'},
                        {field: 'last_ip', title: __('Last_ip'), operate: 'LIKE'},
                        {field: 'last_time', title: __('Last_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), searchList: {"1":__('Status 1'),"0":__('Status 0')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'merchant.mer_id', title: __('Merchant.mer_id')},
                        {field: 'merchant.mer_name', title: __('Merchant.mer_name'), operate: 'LIKE'},
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
