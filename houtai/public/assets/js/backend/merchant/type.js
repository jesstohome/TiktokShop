define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'merchant/type/index' + location.search,
                    add_url: 'merchant/type/add',
                    edit_url: 'merchant/type/edit',
                    del_url: 'merchant/type/del',
                    multi_url: 'merchant/type/multi',
                    import_url: 'merchant/type/import',
                    table: 'merchant_type',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'mer_type_id',
                sortName: 'mer_type_id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'mer_type_id', title: __('Mer_type_id')},
                        {field: 'type_name', title: __('Type_name'), operate: 'LIKE'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
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
