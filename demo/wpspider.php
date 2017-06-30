<?php
ini_set("memory_limit", "1024M");
require dirname(__FILE__).'/../core/init.php';

/* Do NOT delete this comment */
/* 不要删除这段注释 */


//获取所有分类 
// $html = requests::get('http://www.reportsexpress.com/index.php?route=information/sitemap');

// $note = selector::select( $html ,'///*[@id="content"]/div/div[1]/ul/li' ); 
// foreach( $note as $key => $data ){
	// $parent[$key]['url'] = selector::select( $note[$key],'/html/body/a/@href' ); 
	// $parent[$key]['name'] = selector::select( $note[$key],'/html/body/a/text()' ); 

	// $category = explode('&',$parent[$key]['url']); 
	// $parent[$key]['category_id'] = str_replace('path=','',$category[1]);  
	
	// $child = selector::select( $note[$key],'/html/body/ul/li' ); 
	// $url[] = '<a href="'.$parent[$key]['url'].'&page='.'"></a></br>'."\r\n" ;
	// foreach( $child as $key1 => $data1 ){
		// $parent[$key]['child'][$key1]['url'] = selector::select( $data1,'/html/body/a/@href' ); 
		// $parent[$key]['child'][$key1]['name'] = selector::select( $data1,'/html/body/a/text()' ); 
		// $url[] = '<a href="'.$parent[$key]['child'][$key1]['url'].'&page='.'"></a></br>'."\r\n" ;
		
		// $category = explode('&',$parent[$key]['child'][$key1]['url']); 
	    // $parent[$key]['child'][$key1]['category_id'] = str_replace('path=','',$category[1]);  //与商品中的category_id对应
	// }
// }
// file_put_contents('category.json',json_encode( $parent ) );

$configs = array(
    'name' => 'reportsexpress.com',
    'tasknum' => 100,
    'log_show' => false,
	'interval' => 0.1,
    
    'domains' => array(
        'www.reportsexpress.com',
    ),
    'scan_urls' => array(
        "http://www.reportsexpress.com/index.php?route=information/sitemap",
    ),
    'list_url_regexes' => array(
        "http://www.reportsexpress.com/index.php\?route=product/category&path=\S+",
    ),
    'content_url_regexes' => array(
        "http://www.reportsexpress.com/index.php\?route=product/product&path=\S+",
    ),
    'export' => array(
        'type' => 'db',
        'table' => 'my_goods',
		// 'file' => PATH_DATA.'/jd_goods_1.sql',
    ),
    'fields' => array(
        
        // 商品名
        array(
            'name' => "goods_name",
            'selector' => '//*[@id="content"]/div/div[2]/h1',
            'required' => false,
        ),
        // 商品URL
        array(
            'name' => "goods_url",
            'selector' => "/html/body/div[2]/ul/li[last()]/a/@href",
            'required' => false,
        ),
        
	    // 商品品牌
        array(
            'name' => "brand",
            'selector' => '//*[@id="content"]/div/div[2]/ul[1]/li[1]/a',
            'required' => false,
        ),
	    // 产品代码
        array(
            'name' => "productcode",
            'selector' => '//*[@id="content"]/div/div[2]/ul[1]/li[2]/text()',
            'required' => false,
        ),
	    // 可用性
        array(
            'name' => "availability",
            'selector' => '//*[@id="content"]/div/div[2]/ul[1]/li[3]/text()',
            'required' => false,
        ),
		array(
            'name' => "description",
            'selector' => '//*[@id="tab-description"]',
            'required' => false,
        ),
        array(
            'name' => "availableOptions",
            'selector' => '//*[@id="product"]/div[1]/select[1]',
            'required' => false,
        ),
		
        array(
            'name' => "category_id",
            'selector' => '//*[@id="product"]/div[1]/select[1]',
            'required' => false,
        ),
        array(
            'name' => "goods_id",
            'selector' => '//*[@id="product"]/div[1]/select[1]',
            'required' => false,
        ),
        
        array(
            'name' => "goods_price_list",
            'selector' => '//*[@id="content"]/div/div[2]/ul[2]',
            'required' => false,
        ),
        array(
            'name' => "goods_price",
            'selector' => '//*[@id="content"]/div/div[2]/ul[2]/li/h2',
            'required' => false,
        ),
        array(
            'name' => "brand_url",
            'selector' => '//*[@id="content"]/div/div[2]/ul[1]/li[1]/a/@href',
            'required' => false,
        ),
        array(
            'name' => "category_name",
            'selector' => '/html/body/div[2]/ul',
            'required' => false,
        ),
        
    ),
);

$spider = new phpspider($configs);
$spider->on_extract_field = function($fieldname, $data, $page)
{
    if($fieldname == 'goods_url')
    {
        $data = $page['request']['url'];
    }
	else if ( $fieldname == 'category_id' ) {
		$pathinfo = explode('&',$page['request']['url']);
		$data = str_replace('path=','',$pathinfo[1]); 
		
	}
	else if ( $fieldname == 'goods_id' ) {
		$pathinfo = explode('&',$page['request']['url']);
		$data = str_replace('product_id=','',$pathinfo[2]); 
		
	}
	else if ( $fieldname == 'goods_price_list' ) {
		log::info('price_list is :'.$data);
		$pathinfo = $data;
		$pathinfo = str_replace('&#13;','',$pathinfo); 
        $pathinfo = str_replace("\n",'',$pathinfo); 
        $data = str_replace("  ",'',$pathinfo); 
		
	}
    else if ( $fieldname == 'availableOptions' ) {
        if ( $data ){
            $pathinfo = $data;
            $pathinfo = str_replace('&#13;','',$pathinfo); 
            $pathinfo = str_replace("\n",'',$pathinfo); 
            $data = str_replace("  ",'',$pathinfo);
        }else{
            $data = '';
        }
        
        
    }
    else if ( $fieldname == 'goods_price' ) {
        if ( $data ){
            $pathinfo = $data;
            $data = ltrim($pathinfo, '$');
        }       
        
    }
    else if ( $fieldname =='brand_url' ) {
		if ( $data ){
			$pathinfo = $data;
			$pathinfo = explode('=', $pathinfo);
            $data = array_pop($pathinfo);
		}		
		
	}
	else if ( $fieldname == 'category_name' ) {
		if ( $data ){
			$pathinfo = $data;
			
            $data = categoryProduct($pathinfo);
		}		
		
	}
    return $data;
};

function categoryProduct($str)
{
	$str1 = trim($str, " \b\t\n\r\0\x0B");
	$arr = explode("\n", $str1);
	
	foreach($arr as $key => $val)
	{
		$arr[$key] = trim($val, " ");
	}
	$new = implode("_", $arr);
	return $new;

}

$spider->start();


