define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'active/xinren/index',
                    add_url: 'active/xinren/add',
                    edit_url: 'active/xinren/edit',
                    del_url: 'active/xinren/del',
                    multi_url: 'active/xinren/multi',
                    table: 'mall_active',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'active_id',
                sortName: 'active_id',
                columns: [
                    [
                        {checkbox: true},
                        // {field: 'active_id', title: __('Active_id')},
                        {field: 'active_type', title: __('Active_type'), visible:false, searchList: {"0":__('Active_type 0'),"1":__('Active_type 1'),"2":__('Active_type 2'),"3":__('Active_type 3'),"4":__('Active_type 4')}},
                        {field: 'active_type_text', title: __('Active_type'), operate:false},
                        {field: 'goods_image', title: __('Goods_image'), formatter: Table.api.formatter.image},
                        // {field: 'active_image2', title: __('Active_image2')},
                        {field: 'goods_name', title: __('Goods_id')},
                        {field: 'active_price', title: __('Active_price')},
                        {field: 'shop_price', title: __('Shop_price')},
                        // {field: 'start_time', title: __('Start_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'end_time', title: __('End_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
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