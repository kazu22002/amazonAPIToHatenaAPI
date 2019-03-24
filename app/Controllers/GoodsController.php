<?php

namespace App\Controllers;

use App\Usecases\AmazonEcGoods;
use App\Usecases\HatenaPost;

use App\Util\Slack;

/**
 * Class GoodsController
 * @package App\Controllers
 */
class GoodsController {

    /**
     * @param $node_id
     * @return bool
     */
    public function post($node_id){

        libxml_use_internal_errors(true);

        // $xmlParam = simplexml_load_string(preg_replace('/&(?!;{6})/', '&amp;', file_get_contents(__DIR__."/../../sample3.xml")));
        $ec = new AmazonEcGoods();
        $xmlParam = $ec->getXmlToArray($node_id);

        if(!$xmlParam) {
            (new Slack)->send( $this->errorMessage($node_id));
            return false;
        }

        $file = __DIR__."/../../tmp/".$node_id.".txt";
        $tmpFile = @file($file);

        $products = [];
        $number = 1;
        $data = "";
        foreach($xmlParam->BrowseNodes->BrowseNode->TopSellers->TopSeller as $item){
            // 雑誌を除く
            if( preg_match('/雑誌/', $item->Title ) ) continue;

            $x = array();
            $x['number'] = $number++;
            $x['ASIN'] = (string)$item->ASIN;
            $x['Title'] = (string)$item->Title;
            $products[] = $x;

            $data .= (string)$item->ASIN."\n";
        }

        // チェック
        $isPost = false;
        if($tmpFile){
            foreach( $products as $val ){
                if( !in_array( $val, $tmpFile ) ){
                    $isPost = true;
                }
            }

            if( !$isPost) return false;
        }

        // 保存
        file_put_contents($file, $data);

        $hatena = new HatenaPost();
        $hatena->post( $hatena->bodyProduct($node_id, $products));
    }

    /**
     * @param $node_id
     * @return array
     */
    protected function errorMessage( $node_id ){
        $message = array(
          'username' => 'Bot',
          'text' => 'Hatena-post-ec Error :'.$node_id. "  time:" .date('Y-m-d H:i:s'),
        );

        return $message;
    }
}



