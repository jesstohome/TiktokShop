define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'product/reply/index' + location.search,
                    add_url: 'product/reply/add',
                    edit_url: 'product/reply/edit',
                    del_url: 'product/reply/del',
                    multi_url: 'product/reply/multi',
                    import_url: 'product/reply/import',
                    table: 'product_reply',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'reply_id',
                sortName: 'weigh',
                search: false,
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'reply_id', title: __('Reply_id')},
                        {field: 'user.username', title: __('评论用户'), operate: 'LIKE'},
                        {field: 'product.title', title: __('Product.title'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        // {field: 'user_id', title: __('User_id')},
                        // {field: 'mer_id', title: __('Mer_id')},
                        // {field: 'order_product_id', title: __('Order_product_id')},
                        // {field: 'unique', title: __('Unique')},
                        // {field: 'product_id', title: __('Product_id')},
                        // {field: 'product_type', title: __('Product_type')},
                        {field: 'product_score', title: __('Product_score')},
                        {field: 'service_score', title: __('Service_score')},
                        {field: 'postage_score', title: __('Postage_score')},
                        // {field: 'rate', title: __('Rate'), operate:'BETWEEN'},
                        // {field: 'comment', title: __('Comment'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'merchant_reply_content', title: __('Merchant_reply_content'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        // {field: 'merchant_reply_time', title: __('Merchant_reply_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'weigh', title: __('Weigh'), operate: false},
                        // {field: 'is_reply', title: __('Is_reply')},
                        // {field: 'is_virtual', title: __('Is_virtual')},
                        // {field: 'nickname', title: __('Nickname'), operate: 'LIKE'},
                        // {field: 'avatar', title: __('Avatar'), operate: 'LIKE', events: Table.api.events.image, formatter: Table.api.formatter.image},
                        // {field: 'user.id', title: __('User.id')},
                        // {field: 'merchant.mer_id', title: __('Merchant.mer_id')},
                        // {field: 'merchant.mer_name', title: __('Merchant.mer_name'), operate: 'LIKE'},
                        // {field: 'product.order_product_id', title: __('Product.order_product_id')},
                        // {field: 'product.product_id', title: __('Product.product_id')},
                        // {field: 'product.title', title: __('Product.title'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
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
