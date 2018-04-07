#创建商品品牌表
create table fa_brand(
    brand_id smallint unsigned not null auto_increment primary key comment '商品品牌ID',
    brand_name varchar(30) not null default '' comment '商品品牌名称',
    brand_desc varchar(255) not null default '' comment '商品品牌描述',
    url varchar(100) not null default '' comment '商品品牌网址',
    logo_image varchar(80) not null default '' comment '品牌logo',
    sort_order tinyint unsigned not null default 50 comment '填数字从小到大排序',
    is_show tinyint not null default 1 comment '是否显示，默认显示:0=否,1=是'
)engine=MyISAM charset=utf8;

#创建商品类别表
create table fa_category(
    cat_id smallint unsigned not null auto_increment primary key comment '商品类别ID',
    cat_name varchar(30) not null default '' comment '商品类别名称',
    image varchar(80) not null default '' comment '商品类别图片',
    cat_desc varchar(255) not null default '' comment '商品类别描述',
    sort_order tinyint not null default 50 comment '填数字从小到大排序',
    is_show tinyint not null default 1 comment '是否显示，默认显示:0=否,1=是',
)engine=MyISAM charset=utf8;

#创建商品属性表
create table fa_attribute(
    attr_id smallint unsigned not null auto_increment primary key comment '商品属性ID',
    attr_name varchar(50) not null default '' comment '商品属性名称',
    attr_value varchar(255) not null default '' comment '属性值',
    attr_price decimal(10,2) not null default 0 comment '属性价格',
    goods_id int unsigned not null default 0 comment '商品ID',
    attr_value text comment '属性的值',
    sort_order tinyint not null default 50 comment '属性排序依据',
    index goods_id(goods_id),
)engine=MyISAM charset=utf8;

#创建商品表
create table fa_goods(
    goods_id int unsigned not null auto_increment primary key comment '商品ID',
    goods_sn varchar(30) not null default '' comment '商品货号',
    goods_name varchar(100) not null default '' comment '商品名称',
    goods_brief varchar(255) not null default '' comment '商品简单描述',
    goods_desc text comment '商品详情',
    cat_id smallint unsigned not null default 0 comment '商品所属类别ID',
    brand_id smallint unsigned not null default 0 comment '商品所属品牌ID',
    market_price decimal(10,2) not null default 0 comment '市场价',
    shop_price decimal(10,2) not null default 0 comment '本店价格',
    promote_price decimal(10,2) not null default 0 comment '促销价格',
    promote_start_time int unsigned not null default 0 comment '促销起始时间',
    promote_end_time int unsigned not null default 0 comment '促销截止时间',
    goods_img varchar(50) not null default '' comment '商品图片',
    goods_thumb varchar(50) not null default '' comment '商品缩略图',
    goods_number smallint unsigned not null default 0 comment '商品库存',
    sell_count int unsigned not null default 0 comment '销售数量',
    click_count int unsigned not null default 0 comment '点击次数',
    type_id smallint unsigned not null default 0 comment '商品类型ID',
    is_promote tinyint unsigned not null default 0 comment '是否促销，默认为0不促销',
    is_limit tinyint unsigned not null default 0 comment '是否限定,默认为0:0=否,1=是',
    is_best tinyint unsigned not null default 0 comment '是否精品,默认为0:0=否,1=是',
    is_new tinyint unsigned not null default 0 comment '是否新品，默认为0:0=否,1=是',
    is_hot tinyint unsigned not null default 0 comment '是否热卖,默认为0:0=否,1=是',
    is_onsale tinyint unsigned not null default 1 comment '是否上架,默认为1:0=否,1=是',
    add_time int unsigned not null default 0 comment '添加时间',
    index cat_id(cat_id),
    index brand_id(brand_id),
    index type_id(type_id)
)engine=MyISAM charset=utf8;

#创建商品相册表
create table fa_goods_photo(
    img_id int unsigned not null auto_increment primary key comment '图片编号',
    goods_id int unsigned not null default 0 comment '商品ID',
    image varchar(80) not null default '' comment '图片URL',
    thumb_image varchar(80) not null default '' comment '缩略图URL',
    img_desc varchar(80) not null default '' comment '图片描述',
    index goods_id(goods_id)
)engine=MyISAM charset=utf8;

