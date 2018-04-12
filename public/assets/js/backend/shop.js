define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'shop/index',
                    add_url: 'shop/add',
                    edit_url: 'shop/edit',
                    del_url: 'shop/del',
                    multi_url: 'shop/multi',
                    table: 'mall_shop',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name')},
                        {field: 'head_image', title: __('Head_image'), formatter: Table.api.formatter.image},
                        {field: 'intro_image', title: __('Intro_image'), formatter: Table.api.formatter.image},
                        {field: 'ewm_image', title: __('Ewm_image'), formatter: Table.api.formatter.image},
                        {field: 'xin_image', title: __('Xin_image'), formatter: Table.api.formatter.image},
                        {field: 'xin_nei_image', title: __('Xin_nei_image'), formatter: Table.api.formatter.image},
                        {field: 'jian_image', title: __('Jian_image'), formatter: Table.api.formatter.image},
                        {field: 'temai_image', title: __('Temai_image'), formatter: Table.api.formatter.image},
                        {field: 'temai_start_time', title: __('Temai_start_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'temai_end_time', title: __('Temai_end_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
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