<?php


namespace App\Http\Controllers;

//use phpDocumentor\Reflection\Types\Nullable;
//use Illuminate\Http\Request;
//require 'C:\Users\MagmaDiver\app\vendor/autoload.php'; //надо PATH поковырять
//use App\Http\Requests;
use Elasticsearch\Namespaces;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Namespaces\IndicesNamespace;
//analyze метод тут
require_once 'C:\Users\MagmaDiver\app\vendor\elasticsearch\elasticsearch\src\Elasticsearch\Namespaces\IndicesNamespace.php';

ini_set('max_execution_time', 36000);

class InputController extends Controller
{
//  функция для деления строки на слова по пробелам и тире
    function multiexplode($delimiters, $string)
    {

        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return $launch;
    }

    // функция для восстановки нормальных ключей при выборочном заполнении массива
    function setOrder($array)
    {
        $countX = 0;
        $resultT = array();
        foreach ($array as $temp1) {
            $resultT[$countX] = rtrim($temp1);
            $countX++;
        }
        return $resultT;

    }
    // функция для удаления повторов из 2d массива
    function super_unique($array)
    {
        $result = array_map("unserialize", array_unique(array_map("serialize", $array)));

        foreach ($result as $key => $value) {
            if (is_array($value)) {
                $result[$key] = super_unique($value);
            }
        }

        return $result;
    }
    // функция для сортировки массива по ключу
    function array_sort($array, $on, $order = SORT_ASC)
    {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }
    //функция для вызова аналайзера через cURL
    function SendToAnalyzer($stringToAnalyze)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "localhost:9200/_analyze?pretty");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "\n{\n  \"text\" : \"$stringToAnalyze\"\n}\n");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        $headers = array();
        $headers[] = "Content-Type: application/json";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return $result;
    }

    public function index()
    {
        //
        return view('welcome');
    }

    public function process()
    {
        $client = ClientBuilder::create()->build();
        if ($client->indices()->exists(['index' => 'stork'])) {
            $client->indices()->delete(['index' => 'stork']);
        }
        $params = [
            'index' => 'stork',
            'body' => [
                'mappings' => [
                    'product' => [
                        'properties' => [
                            'product_name' => [
                                'type' => 'text',
                                'analyzer' => 'my_analyzer',
                            ],
                        ],
                    ]
                ],
                "settings" => [
                    "analysis" => [
                        "analyzer" => [
                            "my_analyzer" => [
                                "tokenizer" => "standard",
                                "char_filter" => [
                                    "xEnd_Replace",
                                    "xRus_Replace",
                                    "eRus_Replace",
                                    "dot_Replace",
                                ],
                                "filter" => [
                                    "lowercase"
                                ],
                            ],
                        ],
                        "char_filter" => [
                            "xEnd_Replace" => [
                                "type" => "pattern_replace",
                                "pattern" => "(\\d+)x(?=\\d)",
                                "replacement" => "$1 ",
                                "flags" => 'CASE_INSENSITIVE',
                            ],
                            "xRus_Replace" => [
                                "type" => "pattern_replace",
                                "pattern" => "(\\d+)х(?=\\d)",
                                "replacement" => "$1 ",
                                "flags" => 'CASE_INSENSITIVE|UNICODE_CASE',
                            ],
                            "eRus_Replace" => [
                                "type" => "pattern_replace",
                                "pattern" => "ё",
                                "replacement" => "е",
                                "flags" => 'CASE_INSENSITIVE|UNICODE_CASE',
                            ],
                            "dot_Replace" => [
                                "type" => "pattern_replace",
                                "pattern" => "(\\d+),(?=\\d)",
                                "replacement" => "$1.",
                                "flags" => 'CASE_INSENSITIVE|UNICODE_CASE',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $result = $client->indices()->create($params);
        if ($result['acknowledged'] != true) {
            //TODO что-то не то
            echo 'Что-то не так с еластиком';
            dd($result);
        }
        echo "</br>";
        print_r($result);
        echo "</br>";
        //ввожу данные
        $input = $_POST["input"];
        $inputstr = explode("\n", $input);
        $inputstr = array_filter($inputstr);
        $cycleCount=0;
        $tokensStorage=array();
        foreach ($inputstr as $inputline) {
            $params = [
                'index' => 'stork',
                'type' => 'product',
                'id'=>$cycleCount,
                'body' => ['product_name' => $inputline]
            ];
            $indexedDoc = $client->index($params);
            echo "</br>";
            $params = [
                'index' => 'stork',
                'type' => 'product',
                'id'=>$cycleCount
            ];
            $IndexResponse = $client->get($params);
            $indexedName=trim($IndexResponse['_source']['product_name']);
            $analyzedName=$this->SendToAnalyzer($indexedName);
//            $a1=$this->analyze($indexedName);
            dd($analyzedName);
            $cycleCount++;
        }








//
//        //вывод полученных комбинаций и удаление повторов
////        echo 'Перебор комбинаций в процессе' . "<br/>";
//        $uniqueArr = array_unique($combinations2);
//
//        $SortArr = setOrder($uniqueArr);
//        $SortUniqueArr = super_unique($SortArr);
//        $filtSortUniqueArr=array_filter($SortUniqueArr);
//
////        echo 'Начало нового перебора' . "<br/>";
////        print_r($filtSortUniqueArr);
//        echo "<br/>";
//
//        //задаю ассоциативный массив комбинаций
//        $combs = array();
//        foreach ($filtSortUniqueArr as $temp) {
//            $count = count(multiexplode(array(' '), $temp));
//            $combs[] = array('words' => $temp, 'count' => $count, 'reps'=> 1,'weight'=>1);
//        }
//
////        Вывод введенного текста
//        echo 'Введенный текст' . "<br/>";
//        foreach ($inputstr as $inputline) {
//            echo $inputline . "<br/>";
//        }
//        echo "<br/>";
//
//        //счетчик совпадений - не сильно нужен
//        $simcount = 0;
//
//        echo "<br/>";
//
////   работает
//        //подсчет повторений для каждой комбинации
//        foreach ($combs as &$comblines) {
//            $findme = $comblines['words'];
//            $comblines['reps']=0;
//            foreach ($inputstr as $inputline) {
//                $pos = strpos($inputline, $findme);
//                if ($pos === false) {
////                    echo $findme . ' не равно ' . $inputline . "<br/>";
//                } else {
////                    echo $findme . ' равно ' . $inputline . "<br/>";
//                    $simcount++;
//                    ++$comblines['reps'];
////                    Тут формула для веса
//                    $comblines['weight']=$comblines['reps']*$comblines['count'];
//                    }
//            }
//        }
////        Выдача информации по повторам
////        foreach ($combs as $comblines)
////        {
////            echo $comblines['words'].' повторяется '.$comblines['reps'].' раз и имеет вес = '.$comblines['weight']."<br/>";
////        }
////
////        echo "<br/>";
////        echo $simcount.' совпадений';
//
//
//
////        Сортировка массива для вывода на экран
//
//                $sorted2=array_sort($combs,'weight',SORT_DESC);


        return view('process');
    }
}
    