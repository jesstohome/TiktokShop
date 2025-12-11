define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'merchant/intention/index' + location.search,
                    add_url: 'merchant/intention/add',
                    edit_url: 'merchant/intention/edit',
                    del_url: 'merchant/intention/del',
                    multi_url: 'merchant/intention/multi',
                    import_url: 'merchant/intention/import',
                    table: 'merchant_intention',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'mer_intention_id',
                sortName: 'mer_intention_id',
                search: false,
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'mer_intention_id', title: __('Mer_intention_id')},
                        {field: 'mer_name', title: __('Mer_name'), operate: 'LIKE'},
                        {field: 'mer_id', title: __('Mer_id')},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        // {field: 'user_id', title: __('User_id')},
                        {field: 'phone', title: __('Phone'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'fail_msg', title: __('Fail_msg'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        // {field: 'mark', title: __('Mark'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        // {field: 'images', title: __('Images'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.images},
                        // {field: 'mer_type_id', title: __('Mer_type_id')},
                        // {field: 'user.username', title: __('User.username'), operate: 'LIKE'},
                        // {field: 'merchant.mer_name', title: __('Merchant.mer_name'), operate: 'LIKE'},
                        // {field: 'type.type_name', title: __('Type.type_name'), operate: 'LIKE'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate},
                        // {
                        //     field: 'operate',
                        //     title: __('Operate'),
                        //     table: table,
                        //     events: Table.api.events.operate,
                        //     buttons: [
                        //         {
                        //             name: '审核',
                        //             title: __('审核'),
                        //             classname: 'btn btn-xs btn-primary btn-dialog',
                        //             icon: '',
                        //             url: 'merchant/intention/edit',
                        //         },
                        //     ],
                        //     formatter: Table.api.formatter.operate
                        // },
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
