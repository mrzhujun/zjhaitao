define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'goods/index',
                    add_url: 'goods/add',
                    edit_url: 'goods/edit',
                    del_url: 'goods/del',
                    multi_url: 'goods/multi',
                    table: 'mall_goods',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'goods_id',
                sortName: 'goods_id',
                columns: [
                    [
                        {checkbox: true},
                        // {field: 'goods_id', title: __('Goods_id')},
                        {field: 'goods_sn', title: __('Goods_sn')},
                        {field: 'goods_name', title: __('Goods_name')},
                        // {field: 'goods_brief', title: __('Goods_brief')},
                        {field: 'cat_id', title: __('Cat_id')},
                        {field: 'brand_id', title: __('Brand_id')},
                        // {field: 'market_price', title: __('Market_price'), operate:'BETWEEN'},
                        {field: 'shop_price', title: __('Shop_price'), operate:'BETWEEN'},
                        {field: 'promote_price', title: __('Promote_price'), operate:'BETWEEN'},
                        // {field: 'promote_start_time', title: __('Promote_start_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'promote_end_time', title: __('Promote_end_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'goods_images', title: __('Goods_images'), formatter: Table.api.formatter.images},
                        {field: 'sell_count', title: __('Sell_count')},
                        {field: 'click_count', title: __('Click_count')},
                        {field: 'is_promote', title: __('Is_promote'), visible:false, searchList: {"0":__('Is_promote 0'),"1":__('Is_promote 1')}},
                        {field: 'is_promote_text', title: __('Is_promote'), operate:false},
                        // {field: 'is_new', title: __('Is_new'), visible:false, searchList: {"0":__('Is_new 0'),"1":__('Is_new 1')}},
                        // {field: 'is_new_text', title: __('Is_new'), operate:false},
                        // {field: 'is_onsale', title: __('Is_onsale'), visible:false, searchList: {"0":__('Is_onsale 0'),"1":__('Is_onsale 1')}},
                        // {field: 'is_onsale_text', title: __('Is_onsale'), operate:false},
                        // {field: 'is_jifen', title: __('Is_jifen'), visible:false, searchList: {"0":__('Is_jifen 0'),"1":__('Is_jifen 1')}},
                        // {field: 'is_jifen_text', title: __('Is_jifen'), operate:false},
                        // {field: 'need_jifen', title: __('Need_jifen')},
                        {field: 'goods_count', title: __('Goods_count')},
                        // {field: 'add_time', title: __('Add_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
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