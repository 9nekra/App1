<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use phpDocumentor\Reflection\Types\Nullable;
require 'C:\Users\MagmaDiver\app\vendor/autoload.php'; //надо PATH поковырять
use Elasticsearch\ClientBuilder;

ini_set('max_execution_time', 36000);

class InputController extends Controller
{
    //
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
    function array_sort($array, $on, $order=SORT_ASC)
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


    public function index()
    {
        //
        return view('welcome');
    }

    public function process()
    {
        //ввожу данные
        $input = $_POST["input"];
        $inputstr = explode("\n", $input);
        $inputstr = array_filter($inputstr);
        foreach ($inputstr as $inputline) {

            $items = multiexplode(array(' '), rtrim($inputline));

            //считаю возможные комбинации слов
            $num_words = count($items);
            for ($i = 0; $i < $num_words; $i++) {
                for ($j = $i; $j < $num_words; $j++) {
                    $combinations = array();
                    for ($k = $i; $k <= $j; $k++) {
                        array_push($combinations, $items[$k]);
                    }
                    $combinations2[] = implode(' ', $combinations);
                }
            }

        }


        //вывод полученных комбинаций и удаление повторов
//        echo 'Перебор комбинаций в процессе' . "<br/>";
        $uniqueArr = array_unique($combinations2);

        $SortArr = setOrder($uniqueArr);
        $SortUniqueArr = super_unique($SortArr);
        $filtSortUniqueArr=array_filter($SortUniqueArr);

//        echo 'Начало нового перебора' . "<br/>";
//        print_r($filtSortUniqueArr);
        echo "<br/>";

        //задаю ассоциативный массив комбинаций
        $combs = array();
        foreach ($filtSortUniqueArr as $temp) {
            $count = count(multiexplode(array(' '), $temp));
            $combs[] = array('words' => $temp, 'count' => $count, 'reps'=> 1,'weight'=>1);
        }

//        Вывод введенного текста
        echo 'Введенный текст' . "<br/>";
        foreach ($inputstr as $inputline) {
            echo $inputline . "<br/>";
        }
        echo "<br/>";

        //счетчик совпадений - не сильно нужен
        $simcount = 0;

        echo "<br/>";

//   работает
        //подсчет повторений для каждой комбинации
        foreach ($combs as &$comblines) {
            $findme = $comblines['words'];
            $comblines['reps']=0;
            foreach ($inputstr as $inputline) {
                $pos = strpos($inputline, $findme);
                if ($pos === false) {
//                    echo $findme . ' не равно ' . $inputline . "<br/>";
                } else {
//                    echo $findme . ' равно ' . $inputline . "<br/>";
                    $simcount++;
                    ++$comblines['reps'];
//                    Тут формула для веса
                    $comblines['weight']=$comblines['reps']*$comblines['count'];
                    }
            }
        }
//        Выдача информации по повторам
//        foreach ($combs as $comblines)
//        {
//            echo $comblines['words'].' повторяется '.$comblines['reps'].' раз и имеет вес = '.$comblines['weight']."<br/>";
//        }
//
//        echo "<br/>";
//        echo $simcount.' совпадений';



//        Сортировка массива для вывода на экран

                $sorted2=array_sort($combs,'weight',SORT_DESC);


        return view('process',compact('sorted2') );
    }

}
