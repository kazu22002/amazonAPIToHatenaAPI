<?php

namespace App\Usecases;

/**
 * Class HatenaPost
 * @package App\Usecases
 */
class HatenaPost{

    const NODE_COMIC = 2278488051;
    const NODE_SYONEN = 2430812051;
    const NODE_SEINEN = 2430869051;
    const NODE_SYOUJO = 2430765051;
    const NODE_JOSEI = 2430737051;
    const NODE_RANOBE = 2410280051;

    protected $nodeTerm = [
        2278488051 => "コミックス"
        ,2430812051 => "少年漫画"
        ,2430869051 => "青年漫画"
        ,2430765051 => "少女漫画"
        ,2430737051 => "女性漫画"
        ,2410280051 => "ライトノベル"
    ];

// 2278488051 コミックス
// 2430812051 少年漫画
// 2430869051 青年漫画
// 2430765051 少女漫画
// 2430737051 女性漫画
// 2410280051 ラノベ

    /**
     * @param $node_id
     * @param $products
     * @return mixed
     */
    public function bodyProduct($node_id, $products){
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/../Templates');
        $twig = new \Twig\Environment($loader);

        $body = $twig->render('HatenaAtom.html', ['products' => $products] );

        $product = [
            'title' => "ランキング (".$this->nodeTerm[$node_id].") 【".date('Y年m月d日')."】"
            ,'body' => $body
            ,'public_time' => date('Y-m-d H:i:00')
            ,'term' => implode( " " , [$this->nodeTerm[$node_id], "ランキング"] )
        ];

        return $twig->render('HatenaPost.html', ['product' => $product] );
    }

    /**
     * @param $data
     */
    public function post( $data ){
        if( empty($data) ) return ;

        $curl = curl_init();
        $url = getenv('HATENA_ATOMPUB');
        $name = getenv('HATENA_NAME');
        $password = getenv('HATENA_APIKEY');

        curl_setopt($curl, CURLOPT_URL, $url."/entry");
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERPWD, $name . ":" . $password);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // jsonデータを送信

        $buf = curl_exec($curl);
        curl_close($curl);

        var_dump($buf);
    }

    /**
     * @param $name
     * @param $password
     * @return string
     */
    function basicEncode($name,$password){
        return base64_encode($name.":".$password);
    }
}