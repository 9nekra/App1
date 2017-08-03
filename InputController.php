<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;


class InputController extends Controller
{
    //

    public function index()
    {
        //
        return view('welcome');
    }

    public function process()
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
            $countX=0;
            $resultT=array();
            foreach ($array as $temp1)
            {
                $resultT[$countX]=rtrim($temp1);
                $countX++;
            }
            return $resultT;

        }
        // функция для удаления повторов из 2d массива
        function super_unique($array)
        {
            $result = array_map("unserialize", array_unique(array_map("serialize", $array)));

            foreach ($result as $key => $value)
            {
                if ( is_array($value) )
                {
                    $result[$key] = super_unique($value);
                }
            }

            return $result;
        }


        //ввожу данные
        $input = $_POST["input"];
        $inputstr = explode("\n", $input);
        foreach ($inputstr as $inputline) {

            $items = multiexplode(array(' ', '-'), $inputline);

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
        echo 'Перебор комбинаций в процессе' . "<br/>";
        $result1 = array_unique($combinations2);

        $result2=setOrder($result1);
        $result2=super_unique($result2);

        echo 'Начало нового перебора'."<br/>";
        print_r($result2);
        echo "<br/>";

//        $countX=0;
//        $result2=array();
//        foreach ($result1 as $temp1)
//        {
//            $result2[$countX]=$temp1;
//            $countX++;
//        }

//        function compareValues($array1,$array2)
//        {
//            foreach ($array1 as $tempA)
//            {
//                if ($tempA==$array2)
//                    return 1;
//            }
//        }
//        foreach ($result2 as $temp2)
//        {
//            if (compareValues($result2,$temp2)==1)
//                echo 'Совпадение!';
//        }
//


        //считаю повторения во введенном тексте
//        $simcount = 0;
//        foreach ($inputstr as $inputline) {
//            foreach ($result1 as $comblines) {
//                $mystring = $inputline;
//                $findme = $comblines;
//                $pos = strpos($mystring, $findme);
//
//                if ($pos === false) {
//
//                } else {
//                    $simcount++;
//                    echo $comblines . ' ';
//
//                }
//                echo "<br/>";
//            }
//        }
//        echo "<br/>";
//        echo ($simcount) . ' совпадений';

        return view('process', compact('combinations2'));
    }

}
