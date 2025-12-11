define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'merchant/product/index' + location.search,
                    add_url: 'merchant/product/add',
                    edit_url: 'merchant/product/edit',
                    del_url: 'merchant/product/del',
                    multi_url: 'merchant/product/multi',
                    import_url: 'merchant/product/import',
                    table: 'merchant_product',
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
                        // {field: 'mer_id', title: __('Mer_id')},
                        {field: 'merchant.mer_name', title: __('Merchant.mer_name'), operate: 'LIKE'},
                        // {field: 'product_id', title: __('Product_id')},
                        {field: 'sales', title: __('Sales')},
                        {field: 'click', title: __('Click')},
                        {field: 'predict_sales', title: __('Predict_sales')},
                        {field: 'predict_click', title: __('Predict_click')},
                        // {field: 'is_ad', title: __('Is_ad'), searchList: {"0":__('Is_ad 0'),"1":__('Is_ad 1')}, formatter: Table.api.formatter.normal},
                        // {field: 'ad_end_time', title: __('Ad_end_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        // {field: 'weigh', title: __('Weigh'), operate: false},
                        {field: 'switch', title: __('Switch'), searchList: {"0":__('Switch 0'),"1":__('Switch 1')}, table: table, formatter: Table.api.formatter.toggle},
                        // {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'merchant.mer_id', title: __('Merchant.mer_id')},
                        // {field: 'merchant.mer_name', title: __('Merchant.mer_name'), operate: 'LIKE'},
                        // {field: 'goods.product_id', title: __('Product.product_id')},
                        {field: 'goods.title', title: __('Product.title'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'goods.image', title: __('Product.image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'goods.sales_price', title: __('Product.sales_price'), operate:'BETWEEN'},
                        {field: 'goods.cost_price', title: __('Product.cost_price'), operate:'BETWEEN'},
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
                $("#c-mer_id").change(function (){
                    var url = 'product/product/select?include='+'not'+'&mer_id=';
                    var newUrl = url + $("#c-mer_id").val();
                    $("#product-prop").attr('data-url',newUrl);
                });
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
