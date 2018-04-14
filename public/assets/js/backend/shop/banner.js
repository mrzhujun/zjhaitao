define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'shop/banner/index',
                    add_url: 'shop/banner/add',
                    edit_url: 'shop/banner/edit',
                    del_url: 'shop/banner/del',
                    multi_url: 'shop/banner/multi',
                    table: 'mall_banner',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'banner_id',
                sortName: 'banner_id',
                columns: [
                    [
                        {checkbox: true},
                        // {field: 'banner_id', title: __('Banner_id')},
                        {field: 'banner_in', title: __('Banner_in'), visible:false, searchList: {"0":__('Banner_in 0')}},
                        {field: 'banner_in_text', title: __('Banner_in'), operate:false},
                        // {field: 'banner_order', title: __('Banner_order')},
                        {field: 'banner_image', title: __('Banner_image'), formatter: Table.api.formatter.image},
                        {field: 'goods_id', title: __('Goods_id')},
                        {field: 'is_special', title: __('Is_special'), visible:false, searchList: {"0":__('Is_special 0'),"1":__('Is_special 1')}},
                        {field: 'is_special_text', title: __('Is_special'), operate:false},
                        {field: 'link', title: __('Link')},
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