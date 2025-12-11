define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'product/category/index' + location.search,
                    add_url: 'product/category/add',
                    edit_url: 'product/category/edit',
                    del_url: 'product/category/del',
                    multi_url: 'product/category/multi',
                    import_url: 'product/category/import',
                    table: 'product_category',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'category_id',
                sortName: 'weigh',
                search: false,
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'category_id', title: __('Category_id')},
                        // {field: 'pid', title: __('Pid')},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'name_en', title: __('Name_en'), operate: 'LIKE'},
                        {field: 'pic', title: __('Pic'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {
                            field: 'is_show',
                            title: __('Is_show'),
                            align: 'center',
                            table: table,
                            formatter: Table.api.formatter.toggle
                        },
                        // {field: 'level', title: __('Level')},
                        {field: 'weigh', title: __('Weigh'), operate: false},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'merchant.mer_name', title: __('Merchant.mer_name'), operate: 'LIKE'},
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
