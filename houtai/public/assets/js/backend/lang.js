define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'lang/index' + location.search,
                    add_url: 'lang/add',
                    edit_url: 'lang/edit',
                    del_url: 'lang/del',
                    multi_url: 'lang/multi',
                    import_url: 'lang/import',
                    table: 'lang',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'language_name', title: __('Language_name'), operate: 'LIKE'},
                        {field: 'chinese_name', title: __('Chinese_name'), operate: 'LIKE'},
                        {field: 'file_name', title: __('File_name'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, table: table, formatter: Table.api.formatter.toggle},
                        {field: 'is_default', title: __('Is_default'), searchList: {"0":__('Is_default 0'),"1":__('Is_default 1')}, table: table, formatter: Table.api.formatter.status},
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
