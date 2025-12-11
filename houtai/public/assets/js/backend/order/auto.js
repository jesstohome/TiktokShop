define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/auto/index' + location.search,
                    add_url: 'order/auto/add',
                    // edit_url: 'order/auto/edit',
                    del_url: 'order/auto/del',
                    multi_url: 'order/auto/multi',
                    import_url: 'order/auto/import',
                    table: 'auto_order',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                search: false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        // {field: 'mer_id', title: __('Mer_id')},
                        {field: 'merchant.mer_name', title: __('Mer_id')},
                        {field: 'user.username', title: __('User_id')},
                        // {field: 'user_id', title: __('User_id')},
                        // {field: 'product_id', title: __('Product_id')},
                        {field: 'num', title: __('Num')},
                        {field: 'start_time', title: __('Start_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2')}, table: table, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            align: 'center',
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: '商品详情',
                                    text: __('商品详情'),
                                    classname: 'btn btn-xs btn-info btn-dialog',
                                    icon: '',
                                    url: function (row) {
                                        return 'order/auto/product' + '?product_id=' + row.product_id + '&num=' + row.num;
                                    },
                                }

                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            $("#c-mer_id").change(function (){
                var url = 'product/product/select?include=in&mer_id=';
                var newUrl = url + $("#c-mer_id").val();
                $("#product-prop").attr('data-url',newUrl);
            });
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        product: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/auto/product' + location.search,
                    table: 'product',
                }
            });
            var table = $("#table");

            var product_id = Config.product_id;
            var num = Config.num;

            table.bootstrapTable({
                url: 'order/auto/product' + '?product_id=' + product_id + '&num=' + num,
                sortName: 'product_id',
                search: false,
                columns: [
                    [
                        // {field: 'state', checkbox: true,},
                        {field: 'product_id', title: __('商品ID')},
                        {field: 'title', title: __('商品名称'), table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'image', title: __('图片'), operate: false, formatter: Table.api.formatter.image},
                        {field: 'num', title: __('数量'), operate: false},
                        {field: 'cost_price', title: __('成本'), operate: false},
                        {field: 'sales_price', title: __('售价'), operate: false},
                        {field: 'profit', title: __('利润'), operate: false}
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
