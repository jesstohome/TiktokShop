define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/user/index',
                    add_url: 'user/user/add',
                    edit_url: 'user/user/edit',
                    del_url: 'user/user/del',
                    multi_url: 'user/user/multi',
                    table: 'user',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'user.id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), sortable: true},
                        // {field: 'group.name', title: __('Group')},
                        {field: 'username', title: __('Username'), operate: 'LIKE'},
                        {field: 'nickname', title: __('Nickname'), operate: 'LIKE'},
                        {field: 'email', title: __('Email'), operate: 'LIKE'},
                        {field: 'mobile', title: __('Mobile'), operate: 'LIKE'},
                        {field: 'avatar', title: __('Avatar'), events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                        // {field: 'level', title: __('Level'), operate: 'BETWEEN', sortable: true},
                        {field: 'gender', title: __('Gender'), visible: false, searchList: {1: __('Male'), 0: __('Female')}},
                        // {field: 'score', title: __('Score'), operate: 'BETWEEN', sortable: true},
                        {field: 'successions', title: __('Successions'), visible: false, operate: 'BETWEEN', sortable: true},
                        {field: 'maxsuccessions', title: __('Maxsuccessions'), visible: false, operate: 'BETWEEN', sortable: true},
                        {field: 'logintime', title: __('Logintime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'loginip', title: __('Loginip'), formatter: Table.api.formatter.search},
                        {field: 'jointime', title: __('Jointime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        // {field: 'joinip', title: __('Joinip'), formatter: Table.api.formatter.search},
                        {field: 'status', title: __('Status'), formatter: Table.api.formatter.status, searchList: {normal: __('Normal'), hidden: __('Hidden')}},
                        {field: 'is_virtual', title: __('Status'), formatter: Table.api.formatter.normal, searchList: {1: __('虚拟用户'), 0: __('普通用户')}},
                        // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            align: 'center',
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: '收货地址',
                                    text: __('收货地址'),
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-list',
                                    hidden: function (row) {
                                        if(row.is_virtual == 1){
                                            return true;
                                        }else{
                                            return false;
                                        }

                                    },
                                    url: 'user/user/address',
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
        address: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/user/address' + location.search,
                    table: 'product',
                }
            });
            var table = $("#table");

            var user_id = Config.user_id;

            table.bootstrapTable({
                url: 'user/user/address' + '?user_id=' + user_id,
                sortName: 'address_id',
                search: false,
                columns: [
                    [
                        // {field: 'state', checkbox: true,},
                        {field: 'address_id', title: __('地址ID')},
                        {field: 'name', title: __('姓名')},
                        {field: 'mobile', title: __('电话')},
                        {field: 'country', title: __('国家')},
                        {field: 'detail', title: __('详细地址'), table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        // {field: 'province.name', title: __('省(国内)'), operate: false},
                        // {field: 'city.name', title: __('市(国内)'), operate: false},
                        // {field: 'area.name', title: __('区县(国内)'), operate: false},
                        {field: 'tag', title: __('标签'), operate: false, class: 'flag', formatter: Table.api.formatter.flag},
                        {field: 'is_default', title: __('默认地址'), formatter: Table.api.formatter.status, searchList: {'0': __('否'), '1': __('是')}},
                        {field: 'createtime', title: __('Createtime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                    ]
                ]
            });
            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});