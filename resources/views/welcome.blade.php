<!doctype html>
 <html>
  <head>
   <title>
   Brya
   </title>
  </head>
 <body>
  Brykva
  <form method="post" action="{{ route('process') }}" >
    Текст:  <textarea name="input" style="width:1000px;height:200px;"></textarea>
    <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
    <input type="submit" name="submit" value="БРЯ!" />
  </form>

 </body>
</html>
