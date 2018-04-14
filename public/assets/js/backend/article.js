define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'article/index',
                    add_url: 'article/add',
                    edit_url: 'article/edit',
                    del_url: 'article/del',
                    multi_url: 'article/multi',
                    table: 'mall_article',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'article_id',
                sortName: 'article_id',
                columns: [
                    [
                        {checkbox: true},
                        // {field: 'article_id', title: __('Article_id')},
                        {field: 'title', title: __('Title')},
                        {field: 'cover_image', title: __('Cover_image'), formatter: Table.api.formatter.image},
                        // {field: 'from2', title: __('From2'), visible:false, searchList: {"0":__('From2 0'),"1":__('From2 1')}},
                        // {field: 'from2_text', title: __('From2'), operate:false},
                        {field: 'intro', title: __('Intro')},
                        {field: 'goods_id', title: __('Goods_id')},
                        {field: 'top_image', title: __('Top_image'), formatter: Table.api.formatter.image},
                        // {field: 'from', title: __('From'), visible:false, searchList: {"0":__('From 0'),"1":__('From 1')}},
                        // {field: 'from_text', title: __('From'), operate:false},
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