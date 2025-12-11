define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/order/index' + location.search,
                    add_url: 'order/order/add',
                    // edit_url: 'order/order/edit',
                    // del_url: 'order/order/del',
                    multi_url: 'order/order/multi',
                    import_url: 'order/order/import',
                    table: 'order',
                }
            });

            var auth_edit = Config.auth_edit;

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'order_id',
                sortName: 'order_id',
                search: false,
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        // {field: 'order_id', title: __('Order_id')},
                        // {field: 'group_order_id', title: __('Group_order_id')},
                        {field: 'order_sn', title: __('Order_sn'), operate: 'LIKE'},
                        {field: 'merchant.mer_name', title: __('所属商户'), operate: false},
                        {field: 'user.nickname', title: __('User.nickname'), operate: 'LIKE'},
                        // {field: 'user_id', title: __('User_id')},
                        {field: 'status', title: __('Status'), searchList: {"-2":__('Status -2'),"-1":__('Status -1'),"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2'),"3":__('Status 3'),"4":__('Status 4')}, table: table, formatter: Table.api.formatter.status},
                        // {field: 'paid', title: __('Paid'), searchList: {"0":__('Paid 0'),"1":__('Paid 1')}, table: table, formatter: Table.api.formatter.status},
                        // {field: 'spread_uid', title: __('Spread_uid')},
                        // {field: 'real_name', title: __('Real_name'), operate: 'LIKE'},
                        // {field: 'user_phone', title: __('User_phone'), operate: 'LIKE'},
                        // {field: 'user_address', title: __('User_address'), operate: 'LIKE'},
                        // {field: 'cart_id', title: __('Cart_id'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        // {field: 'total_num', title: __('Total_num')},
                        // {field: 'total_cost', title: __('Cost'), operate:false},
                        {field: 'total_price', title: __('Total_price'), operate:false},
                        {field: 'total_profit', title: __('总利润'), operate:false},
                        // {field: 'total_postage', title: __('Total_postage'), operate:'BETWEEN'},
                        {field: 'pay_price', title: __('Pay_price'), operate:false},
                        // {field: 'pay_postage', title: __('Pay_postage'), operate:'BETWEEN'},
                        // {field: 'extension_one', title: __('Extension_one'), operate:'BETWEEN'},
                        // {field: 'extension_two', title: __('Extension_two'), operate:'BETWEEN'},
                        // {field: 'commission_rate', title: __('Commission_rate'), operate:'BETWEEN'},
                        // {field: 'integral', title: __('Integral')},
                        // {field: 'integral_price', title: __('Integral_price'), operate:'BETWEEN'},
                        // {field: 'give_integral', title: __('Give_integral')},
                        // {field: 'coupon_id', title: __('Coupon_id'), operate: 'LIKE'},
                        // {field: 'coupon_price', title: __('Coupon_price'), operate:'BETWEEN'},
                        // {field: 'platform_coupon_price', title: __('Platform_coupon_price'), operate:'BETWEEN'},
                        // {field: 'svip_discount', title: __('Svip_discount'), operate:'BETWEEN'},
                        // {field: 'order_type', title: __('Order_type')},
                        // {field: 'paid', title: __('Paid')},
                        // {field: 'pay_time', title: __('Pay_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'pay_type', title: __('Pay_type')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'delivery_type', title: __('Delivery_type'), operate: 'LIKE'},
                        // {field: 'is_virtual', title: __('Is_virtual')},

                        {field: 'is_pick', title: __('商家提货状态'), searchList: {"0":__('待提货'),"1":__('已提货')}, table: table, formatter: Table.api.formatter.status},
                        // {field: 'delivery_name', title: __('Delivery_name'), operate: 'LIKE'},
                        // {field: 'delivery_id', title: __('Delivery_id'), operate: 'LIKE'},
                        // {field: 'mark', title: __('Mark'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        // {field: 'remark', title: __('Remark'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        // {field: 'admin_mark', title: __('Admin_mark'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        // {field: 'verify_time', title: __('Verify_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'activity_type', title: __('Activity_type')},
                        // {field: 'order_extend', title: __('Order_extend'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        // {field: 'mer_id', title: __('Mer_id')},
                        // {field: 'is_del', title: __('Is_del')},
                        // {field: 'is_system_del', title: __('Is_system_del')},
                        // {field: 'order.group_order_id', title: __('Order.group_order_id')},
                        // {field: 'order.group_order_sn', title: __('Order.group_order_sn'), operate: 'LIKE'},
                        // {field: 'user.id', title: __('User.id')},
                        // {field: 'user.username', title: __('User.username'), operate: 'LIKE'},
                        // {field: 'user.nickname', title: __('User.nickname'), operate: 'LIKE'},
                        // {field: 'merchant.mer_id', title: __('Merchant.mer_id'), operate: false},
                        // {field: 'merchant.mer_name', title: __('Merchant.mer_name'), operate: false},
                        // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate},
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            align: 'center',
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: '物流信息',
                                    text: __('物流信息'),
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: '',
                                    hidden: function (row) {
                                        // console.log(row);
                                        if(auth_edit === true){
                                            if(row.status <= 1){
                                                return true;
                                            }else{
                                                return false;
                                            }
                                        }else{
                                            return true;
                                        }

                                    },
                                    url: 'order/order/delivery' + '?type=' + 'info',

                                },
                                {
                                    name: '详情',
                                    text: __('详情'),
                                    classname: 'btn btn-xs btn-info btn-dialog',
                                    icon: '',
                                    url: 'order/order/product',
                                },
                                {
                                    name: '取消',
                                    text: __('取消'),
                                    classname: 'btn btn-xs btn-danger btn-ajax',
                                    icon: '',
                                    hidden: function (row) {
                                        //console.log(row);
                                        if(auth_edit === true) {
                                            if (row.paid == 0 && row.status == 0) {
                                                return false;
                                            } else {
                                                return true;
                                            }
                                        }else{
                                            return true;
                                        }
                                    },
                                    confirm: '确认取消订单？',
                                    url: 'order/order/status' + '?status=' + -1,
                                    success: function (data, ret) {
                                        //Layer.alert('');
                                        table.bootstrapTable('refresh');
                                        //如果需要阻止成功提示，则必须使用return false;
                                        //return false;
                                    },
                                    error: function (data, ret) {
                                        //console.log(data, ret);
                                        Layer.alert(ret.msg);
                                        return false;
                                    }
                                },
                                {
                                    name: '发货',
                                    text: __('发货'),
                                    classname: 'btn btn-xs btn-default btn-dialog',
                                    icon: '',
                                    hidden: function (row) {
                                        if(auth_edit === true) {
                                            if (row.paid === 1 && row.status === 1 && row.is_pick === 1) {
                                                return false;
                                            } else {
                                                return true;
                                            }
                                        }else{
                                            return true;
                                        }
                                    },
                                    url: 'order/order/delivery' + '?type=' + 'delivery',
                                },
                                {
                                    name: '确认收货',
                                    text: __('确认收货'),
                                    classname: 'btn btn-xs btn-success btn-ajax',
                                    icon: '',
                                    hidden: function (row) {
                                        if(auth_edit === true) {
                                            if (row.status == 2) {
                                                return false;
                                            } else {
                                                return true;
                                            }
                                        }else{
                                            return true;
                                        }
                                    },
                                    confirm: '是否确认收货？',
                                    url: 'order/order/received',
                                    success: function (data, ret) {
                                        //Layer.alert('');
                                        table.bootstrapTable('refresh');
                                        //如果需要阻止成功提示，则必须使用return false;
                                        //return false;
                                    },
                                    error: function (data, ret) {
                                        //console.log(data, ret);
                                        Layer.alert(ret.msg);
                                        return false;
                                    }
                                },
                                {
                                    name: '完成',
                                    text: __('完成'),
                                    classname: 'btn btn-xs btn-success btn-ajax',
                                    icon: '',
                                    hidden: function (row) {
                                        if(auth_edit === true) {
                                            if (row.status == 3) {
                                                return false;
                                            } else {
                                                return true;
                                            }
                                        }else{
                                            return true;
                                        }
                                    },
                                    confirm: '确认完成订单？',
                                    url: 'order/order/status' + '?status=' + 4,
                                    success: function (data, ret) {
                                        //Layer.alert('');
                                        table.bootstrapTable('refresh');
                                        //如果需要阻止成功提示，则必须使用return false;
                                        //return false;
                                    },
                                    error: function (data, ret) {
                                        //console.log(data, ret);
                                        Layer.alert(ret.msg);
                                        return false;
                                    }
                                },
                                {
                                    name: '退款',
                                    text: __('退款'),
                                    classname: 'btn btn-xs btn-warning btn-ajax',
                                    icon: '',
                                    hidden: function (row) {
                                        if(auth_edit === true) {
                                            if (row.paid == 1 && row.status == 4) {
                                                return false;
                                            } else {
                                                return true;
                                            }
                                        }else{
                                            return true;
                                        }
                                    },
                                    confirm: '确认是否退款？',
                                    url: 'order/order/status' + '?status=' + -2,
                                    success: function (data, ret) {
                                        //Layer.alert('');
                                        table.bootstrapTable('refresh');
                                        //如果需要阻止成功提示，则必须使用return false;
                                        //return false;
                                    },
                                    error: function (data, ret) {
                                        //console.log(data, ret);
                                        Layer.alert(ret.msg);
                                        return false;
                                    }
                                },
                                {
                                    name: '状态修改',
                                    text: __('状态修改'),
                                    classname: 'btn btn-xs btn-default btn-dialog',
                                    icon: '',
                                    hidden: function (row) {
                                        if(auth_edit === true) {
                                            if (row.status == 2) {
                                                return false;
                                            } else {
                                                return true;
                                            }
                                        }else{
                                            return true;
                                        }
                                    },
                                    url: 'order/order/change_status',
                                },
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);

            $(document).on('click', '.delivery_batch', function () {
                layer.confirm('确认是否批量发货？',{
                    btn:['确定','取消'],
                }, function (data, ret) {
                    // 确认回调函数
                    var ids = Table.api.selectedids(table);
                    //console.log(ids);
                    Fast.api.ajax({
                        type: "POST",
                        dataType: "json",
                        url: 'order/order/delivery_batch', // 替换为你的实际URL
                        data: {
                            ids:ids
                        }
                    }, function (data, ret) {
                        // 成功回调函数
                        table.bootstrapTable('refresh');
                    }, function (data, ret) {
                        // 失败回调函数
                        console.error(ret);
                    });
                    layer.close(data);
                });
            });
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        change_status: function () {
            Controller.api.bindevent();
        },
        product: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/order/product' + location.search,
                    table: 'product',
                }
            });
            var table = $("#table");

            var order_id = Config.order_id;

            table.bootstrapTable({
                url: 'order/order/product' + '?order_id=' + order_id,
                sortName: 'createtime',
                search: false,
                columns: [
                    [
                        // {field: 'state', checkbox: true,},
                        {field: 'order.order_sn', title: __('Order_sn')},
                        {field: 'product.title', title: __('Product.title'), table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'product.image', title: __('Product.image'), operate: false, formatter: Table.api.formatter.image},
                        {field: 'product_num', title: __('数量'), operate: false},
                        {field: 'total_cost', title: __('成本'), operate: false},
                        {field: 'total_price', title: __('售价'), operate: false},
                        {field: 'total_profit', title: __('利润'), operate: false},
                        {field: 'createtime', title: __('Createtime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                    ]
                ]
            });
            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        delivery: function () {
            $("input:radio[name='row[use]']").click(function () {
                var use = $(this).val();
                if(use == 1){
                    $("#delivery").addClass('hidden');
                }else{
                    $("#delivery").removeClass('hidden');
                }
            });

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
