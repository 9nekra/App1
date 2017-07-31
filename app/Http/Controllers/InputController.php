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
        function multiexplode ($delimiters,$string) {

	    $ready = str_replace($delimiters, $delimiters[0], $string);
	    $launch = explode($delimiters[0], $ready);
	    return  $launch;
		}



            //ввожу данные
            $input=$_POST["input"];
			$inputstr=explode("\n", $input);
			foreach ($inputstr as $inputline)
			{
			  
			      $items = multiexplode(array(' ','-'),$inputline);

			      //считаю возможные комбинации слов
			      $num_words = count($items);
			      for ($i = 0; $i < $num_words; $i++) {
			        for ($j = $i; $j < $num_words; $j++) {
			        	$combinations=array();
			        	for ($k = $i; $k <= $j; $k++) {
			                   array_push($combinations, $items[$k]);

			          }
			          $combinations2[]=implode(' ',$combinations);

			          
			        }
			      }
			    
			}
			//вывод полученных комбинаций и удаление повторов (ПОВТОРЫ УДАЛЯЮТСЯ НЕ ВСЕ)
			echo 'Перебор комбинаций в процессе'."<br/>";
			  $result1 = array_unique($combinations2);
			    
			  foreach ($result1 as $lists)
			  {
			    echo $lists."<br/>";
			   
			  }
			  //считаю повторения во введенном тексте
			$simcount=0;
			foreach ($inputstr as $inputline)
			{
				foreach($result1 as $comblines)
				{
					$mystring = $inputline;
					$findme   = $comblines;
					$pos = strpos($mystring, $findme);

					if ($pos === false) {
					    
					} else {
					    $simcount++;
					    echo $comblines.' ';

					}
					echo "<br/>";
				}
			}
			echo "<br/>";
			echo ($simcount).' совпадений';

        return view('process',compact('combinations2'));
    }

}
