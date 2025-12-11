define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'merchant/merchant/index' + location.search,
                    add_url: 'merchant/merchant/add',
                    edit_url: 'merchant/merchant/edit',
                    del_url: 'merchant/merchant/del',
                    multi_url: 'merchant/merchant/multi',
                    import_url: 'merchant/merchant/import',
                    table: 'merchant',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'mer_id',
                sortName: 'mer_id',
                search: false,
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'mer_id', title: __('Mer_id')},
                        // {field: 'type_id', title: __('Type_id')},
                        {field: 'mer_name', title: __('Mer_name'), operate: 'LIKE'},
                        {field: 'mer_avatar', title: __('Mer_avatar'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'real_name', title: __('真实姓名'), operate: 'LIKE'},
                        {field: 'mer_phone', title: __('Mer_phone'), operate: 'LIKE'},
                        // {field: 'mer_address', title: __('Mer_address'), operate: 'LIKE'},
                        {field: 'mer_money', title: __('Mer_money'), operate: false},
                        // {field: 'level.name', title: __('Level.name'), operate: false, formatter: Table.api.formatter.flag},
                        // {field: 'follow_count', title: __('Follow_count'), operate: false},
                        {field: 'grade', title: __('Grade'), operate: false},
                        {field: 'credit', title: __('Credit'), operate: false},
                        {field: 'good_rate', title: __('Good_rate'), operate: false},
                        // {field: 'mer_keyword', title: __('Mer_keyword'), operate: 'LIKE'},
                        // {field: 'mer_avatar', title: __('Mer_avatar'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        // {field: 'mark', title: __('Mark'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        // {field: 'mer_info', title: __('Mer_info'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        // {field: 'service_phone', title: __('Service_phone'), operate: 'LIKE'},
                        // {field: 'mer_level', title: __('Mer_level'), operate: false},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'type.mer_type_id', title: __('Type.mer_type_id')},
                        // {field: 'type.type_name', title: __('Type.type_name'), operate: 'LIKE'},
                        // {field: 'level.level_id', title: __('Level.level_id')},

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
