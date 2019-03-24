<?php

namespace App\Usecases;

/**
 * Class AmazonEcGoods
 * @package App\Usecases
 */
class AmazonEcGoods {

    // sort の使用できるパラメータ
    // salesrank','pricerank','inverse-pricerank','daterank','titlerank','-titlerank','price','-price','-publication_date','-unit-sales'
    /**
     * @param $node_id
     * @return array|\SimpleXMLElement
     */
    public function getXmlToArray($node_id)
    {
        // Your Access Key ID, as taken from the Your Account page
        $access_key_id = getenv('ACCESS_KEY');

        // Your Secret Key corresponding to the above ID, as taken from the Your Account page
        $secret_key = getenv("SECRET_KEY");

        $associate_tag = getenv("ASSOCIATE_TAG");

        if( !$access_key_id && $secret_key ) return [];

        // The region you are interested in
        $endpoint = "webservices.amazon.co.jp";

        $uri = "/onca/xml";

        $params = array(
            "Service" => "AWSECommerceService",
            "Operation" => "BrowseNodeLookup",
            "AWSAccessKeyId" => $access_key_id,
            "AssociateTag" => $associate_tag,
            "BrowseNodeId" => $node_id,
            "ResponseGroup" => "TopSellers"
        );

        // Set current timestamp if not set
        if (!isset($params["Timestamp"])) {
            $params["Timestamp"] = gmdate('Y-m-d\TH:i:s\Z');
        }

        // Sort the parameters by key
        ksort($params);

        $pairs = array();

        foreach ($params as $key => $value) {
            array_push($pairs, rawurlencode($key)."=".rawurlencode($value));
        }

        // Generate the canonical query
        $canonical_query_string = join("&", $pairs);

        // Generate the string to be signed
        $string_to_sign = "GET\n".$endpoint."\n".$uri."\n".$canonical_query_string;

        // Generate the signature required by the Product Advertising API
        $signature = base64_encode(hash_hmac("sha256", $string_to_sign, $secret_key, true));

        // Generate the signed URL
        $request_url = 'http://'.$endpoint.$uri.'?'.$canonical_query_string.'&Signature='.rawurlencode($signature);

        return simplexml_load_string(preg_replace('/&(?!;{6})/', '&amp;', file_get_contents($request_url)));
    }

}