define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'merchant/level/index' + location.search,
                    add_url: 'merchant/level/add',
                    edit_url: 'merchant/level/edit',
                    del_url: 'merchant/level/del',
                    multi_url: 'merchant/level/multi',
                    import_url: 'merchant/level/import',
                    table: 'merchant_level',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'level_id',
                sortName: 'price',
                search: false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'level_id', title: __('Level_id')},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'name_en', title: __('Name_en'), operate: 'LIKE'},
                        {field: 'image', title: __('图标'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'product_num', title: __('Product_num')},
                        {field: 'rate', title: __('Rate'), operate:'BETWEEN'},
                        {field: 'price', title: __('Price'), operate:'BETWEEN'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
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
