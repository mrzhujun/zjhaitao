define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'attr/index',
                    add_url: 'attr/add',
                    edit_url: 'attr/edit',
                    del_url: 'attr/del',
                    multi_url: 'attr/multi',
                    table: 'mall_attr',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'attr_id',
                sortName: 'attr_id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'attr_id', title: __('Attr_id')},
                        {field: 'goods_id', title: __('Goods_id')},
                        {field: 'attr_name', title: __('Attr_name')},
                        {field: 'attr_price', title: __('Attr_price'), operate:'BETWEEN'},
                        {field: 'attr_image', title: __('Attr_image'), formatter: Table.api.formatter.image},
                        {field: 'goods_number', title: __('Goods_number')},
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