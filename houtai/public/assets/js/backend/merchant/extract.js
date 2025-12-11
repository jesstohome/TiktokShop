define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'merchant/extract/index' + location.search,
                    add_url: 'merchant/extract/add',
                    edit_url: 'merchant/extract/edit',
                    del_url: 'merchant/extract/del',
                    multi_url: 'merchant/extract/multi',
                    import_url: 'merchant/extract/import',
                    table: 'merchant_extract',
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
                        // {field: 'mer_id', title: __('Mer_id')},
                        {field: 'merchant.mer_name', title: __('Merchant.mer_name'), operate: 'LIKE'},
                        {field: 'extract_sn', title: __('Extract_sn'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'extract_type', title: __('Extract_type'), searchList: {"0":__('Extract_type 0'),"1":__('Extract_type 1'),"2":__('Extract_type 2'),"3":__('Extract_type 3')}, formatter: Table.api.formatter.normal},
                        {field: 'extract_price', title: __('Extract_price'), operate:'BETWEEN'},
                        {field: 'currency_type', title: __('Currency_type'), operate: 'LIKE'},
                        // {field: 'mark', title: __('Mark'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        // {field: 'alipay_code', title: __('Alipay_code'), operate: 'LIKE'},
                        // {field: 'wechat', title: __('Wechat'), operate: 'LIKE'},
                        // {field: 'real_name', title: __('Real_name'), operate: 'LIKE'},
                        // {field: 'bank_name', title: __('Bank_name'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        // {field: 'bank_code', title: __('Bank_code'), operate: 'LIKE'},
                        // {field: 'bank_address', title: __('Bank_address'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        // {field: 'blockchain', title: __('Blockchain'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        // {field: 'admin_id', title: __('Admin_id')},
                        // {field: 'admin_msg', title: __('Admin_msg'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        // {field: 'admin.username', title: __('审核员'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"-1":__('Status -1'),"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'merchant.mer_id', title: __('Merchant.mer_id')},
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
                                    url: 'merchant/extract/verify',
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
