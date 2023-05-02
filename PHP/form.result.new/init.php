<?
/*
You can place here your functions and event handlers

AddEventHandler("module", "EventName", "FunctionName");
function FunctionName(params)
{
	//code
}
*/


//require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/dompdf/autoload.inc.php");

/*
AddEventHandler("main", "OnBeforeEventAdd", array("MailEventHandler","onBeforeEventAddHandler"));
class MailEventHandler
{

    static function onBeforeEventAddHandler(&$event, &$lid, &$arFields, &$message_id, &$files)
    {

        
        $filePath = $_SERVER['DOCUMENT_ROOT'] . "/log/123.txt";
        file_put_contents($filePath, serialize($arFields), FILE_APPEND | LOCK_EX);
            if ($event === 'FORM_FILLING_SIMPLE_FORM_1' && $message_id === '32') {}
                
            
    }
}*/

AddEventHandler("main", "OnBeforeEventAdd", array("MyClass", "OnBeforeEventAddHandler"));
class MyClass
{
    function OnBeforeEventAddHandler(&$event, &$lid, &$arFields)
    {
      /* 
      $prop = self::GetOrderProp(); // нужно описать
       
       foreach ($prop as $itemProp){
         ob_start();
         ?>
         <tr>
           <td>
               <?=$itemProp["NAME"]?>
           </td>
           <td>
               <?=$itemProp["VALUE"]?>
            </td>
         </tr>
         <?
    
         $arFields["ORDER_PROP_VALUE"] .= ob_get_clean();
      }*/
      $filePath = $_SERVER['DOCUMENT_ROOT'] . "/log/123.txt";
        file_put_contents($filePath, serialize($_POST), FILE_APPEND | LOCK_EX);
      foreach($_POST["form_dropdown_SIMPLE_QUESTION_933"] as $brand){
      	if($brand == "18"){
        	$brandtext = "Бренд 1 ";
        }
        if($brand == "19"){
        	$brandtext .= "Бренд 2";
        }
      }
      foreach($_POST["name"] as $name){
      	$nametext .= $name.' ';
      }
      foreach($_POST["form_text_13"] as $count){
      	$counttext .= $count.' ';
      }
      foreach($_POST["form_text_14"] as $pack){
      	$packtext .= $pack.' ';
      }
      foreach($_POST["form_text_15"] as $client){
      	$clienttext .= $client.' ';
      }
      $arFields["SIMPLE_QUESTION_933_RAW"] = $brandtext;
      $arFields["SIMPLE_QUESTION_933"] = $brandtext;
      $arFields["SIMPLE_QUESTION_787_RAW"] = $nametext;
      $arFields["SIMPLE_QUESTION_787"] = $nametext;
      $arFields["SIMPLE_QUESTION_272_RAW"] = $counttext;
      $arFields["SIMPLE_QUESTION_272"] = $counttext;
      $arFields["SIMPLE_QUESTION_751_RAW"] = $packtext;
      $arFields["SIMPLE_QUESTION_751"] = $packtext;
      $arFields["SIMPLE_QUESTION_535_RAW"] = $clienttext;
      $arFields["SIMPLE_QUESTION_535"] = $clienttext;
   }
}