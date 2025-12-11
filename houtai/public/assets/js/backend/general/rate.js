define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'general/rate/index' + location.search,
                    add_url: 'general/rate/add',
                    edit_url: 'general/rate/edit',
                    del_url: 'general/rate/del',
                    multi_url: 'general/rate/multi',
                    import_url: 'general/rate/import',
                    table: 'exchange_rate',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                search: false,
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'country', title: __('Country'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'price', title: __('Price'), operate:'BETWEEN'},
                        {field: 'national_flag', title: __('National_flag'), events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                        {field: 'coin_name', title: __('Coin_name'), operate: 'LIKE'},
                        {field: 'coin_sign', title: __('Coin_sign'), operate: 'LIKE'},
                        {field: 'exchange_min', title: __('Exchange_min'), operate: 'LIKE'},
                        {field: 'exchange_max', title: __('Exchange_max'), operate: 'LIKE'},
                        // {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {
                            field: 'status',
                            title: __('Status'),
                            align: 'center',
                            table: table,
                            formatter: Table.api.formatter.toggle
                        },
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
