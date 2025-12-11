define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/extract/index' + location.search,
                    add_url: 'user/extract/add',
                    edit_url: 'user/extract/edit',
                    del_url: 'user/extract/del',
                    multi_url: 'user/extract/multi',
                    import_url: 'user/extract/import',
                    table: 'user_extract',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'extract_id',
                sortName: 'extract_id',
                search: false,
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'extract_id', title: __('Extract_id')},
                        {field: 'user_id', title: __('User_id')},
                        {field: 'user.username', title: __('User.username'), operate: 'LIKE'},
                        {field: 'extract_sn', title: __('Extract_sn'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'extract_type', title: __('Extract_type'), searchList: {"0":__('Extract_type 0'),"1":__('Extract_type 1'),"2":__('Extract_type 2'),"3":__('Extract_type 3')}, formatter: Table.api.formatter.normal},
                        {field: 'extract_price', title: __('Extract_price'), operate:'BETWEEN'},
                        // {field: 'mark', title: __('Mark'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        // {field: 'alipay_code', title: __('Alipay_code'), operate: 'LIKE'},
                        // {field: 'wechat', title: __('Wechat'), operate: 'LIKE'},
                        // {field: 'real_name', title: __('Real_name'), operate: 'LIKE'},
                        // {field: 'bank_name', title: __('Bank_name'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        // {field: 'bank_code', title: __('Bank_code'), operate: 'LIKE'},
                        // {field: 'bank_address', title: __('Bank_address'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        // {field: 'admin_id', title: __('Admin_id')},
                        // {field: 'admin.username', title: __('审核员'), operate: 'LIKE'},
                        // {field: 'admin_msg', title: __('Admin_msg'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'status', title: __('Status'), searchList: {"-1":__('Status -1'),"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'user.id', title: __('User.id')},
                        // {field: 'user.username', title: __('User.username'), operate: 'LIKE'},
                        // {field: 'user.nickname', title: __('User.nickname'), operate: 'LIKE'},
                        // {field: 'admin.id', title: __('Admin.id')},
                        // {field: 'admin.username', title: __('Admin.username'), operate: 'LIKE'},
                        // {field: 'admin.nickname', title: __('Admin.nickname'), operate: 'LIKE'},
                        // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate},
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
                                    url: 'user/extract/verify',
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
