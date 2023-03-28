<?


class MailEventHandler
{

    static function onBeforeEventAddHandler(&$event, &$lid, &$arFields, &$message_id, &$files)
    {

        /* Тут пишем наш Тип почтового события и ID Почтового шаблона */
        //$filePath = $_SERVER['DOCUMENT_ROOT'] . "/log/123.txt";
        //file_put_contents($filePath, serialize($arFields), FILE_APPEND | LOCK_EX);
        if ($event === 'ASPRO_SEND_FORM_ADMIN_39' && $message_id === '42') {
            //AddMessage2Log(serialize($files), "main");
            if (!is_array($files)) $files = [];
            $pdfid = self::getpdfdom($arFields);
            if($pdfid){
                $files[] = $pdfid;
            }
        }
    }

    static function getpdfdom($arFields){
        try{
            //$options = new \Dompdf\Options();
            //$options->set('defaultFont', 'times');
            $dompdf = new \Dompdf\Dompdf();

            //$options = $dompdf->getOptions();
            //$options->setDefaultFont('times');
            //$dompdf->setOptions($options);

            //AddMessage2Log(serialize($arFields), "main");
            foreach($arFields as $key => $field){
                if($key !== 'SITE_NAME' && $key !== 'FORM_NAME' && $key !== 'ADMIN_RESULT_URL' && $key !== 'PAGE_LINK' && $key !== 'ST_FILES'){
                    switch ($key) {
                        case "ST1":
                            $name = "Период прохождения практики";
                            break;
                        case "ST2":
                            $name = "Специальность";
                            break;
                        case "ST3":
                            $name = "Предпочтительная локация";
                            break;
                        case "ST4":
                            $name = "Рассматриваю все регионы";
                            break;
                        case "ST5":
                            $name = "Текущий курс обучения";
                            break;
                        case "ST31":
                            $name = "Какую цель на время прохождения практики вы ставите перед собой";
                            break;
                        case "ST32":
                            $name = "Из каких источников вы получили информацию о прохождении практики";
                            break;
                        case "ST6":
                            $name = "ФИО";
                            break;
                        case "ST7":
                            $name = "Дата рождения";
                            break;
                        case "ST12":
                            $name = "Место жительства";
                            break;
                        case "ST15":
                            $name = "Мобильный телефон";
                            break;
                        case "ST16":
                            $name = "Email";
                            break;
                        case "ST26":
                            $name = ["Название организации, в которой проходила практика", "Специальность, профессия, по которой проходила практика","Дата поступления","Дата окончания"];
                            break;
                        case "ST17":
                            $name = "Образование";
                            break;
                        case "ST19":
                            $name = ["Наименование учебного заведения (институт, техникум и т.д.)", "Направление или специальность, квалификация","Дата поступления","Дата окончания"];
                            break;
                        case "ST28":
                            $name = "Средний балл";
                            break;
                        case "ST18":
                            $name = "Знание иностранных языков";
                            break;
                        case "ST29":
                            $name = "Какими компьютерными программами владеете?";
                            break;
                    }

                    if($key == "ST1"){
                        $data .= '<p class="bold">'.'Практика'.'</p>';
                    }
                    if($key == "ST17"){
                        $data .= '<p class="bold">'.'Образование и навыки'.'</p>';
                    }
                    if($key == "ST6"){
                        $data .= '<p class="bold">'.'Личные данные'.'</p>';
                    }
                    if($key == "ST15"){
                        $data .= '<p class="bold">'.'Контакты'.'</p>';
                    }
                    if($key == "ST26"){
                        $data .= '<p class="bold">'.'Ранее пройденная практика'.'</p>';
                    }

                    if(is_array($field)){
                        $i = 0;
                        foreach($field as $fieldi){
                            if($i == 4){
                                $i = 0;
                            }
                            $data .= '<p>'.$name[$i].': '.$fieldi.'</p>';
                            $i++;
                        }
                    }else{
                        $data .= '<p>'.$name.': '.$field.'</p>';
                    }
                }

            }
            $html = '<html lang=ru><meta charset=utf-8><meta http-equiv=X-UA-Compatible content="IE=edge"><body>
                    <style type="text/css">
                            .container {
                            width: 100%;border-collapse: collapse;border-spacing: 0; font-family: "DejaVu Sans"; font-size:14px;}
                            
                    html{
                    margin: 50px;
                    }
                    html, body{
            box-sizing: border-box;
            padding:0;
          }
          h1{
          font-size: 14px;
          }
                    table, th, td {
                      border: .1rem solid #999;
                      padding: .2em;
                        font-size: 14px;
                        font-weight: 400;
                        text-align: left;
                    }
                    .bold{
                    font-weight: bold;
                    }
                    
                    
                    th:nth-child(1){
                    width: 30%;
                    }
                    th:nth-child(2){
                    width:50%;
                    }
                    
                    </style>
                    <div class="container">'.'<h1>АНКЕТА КАНДИДАТА ДЛЯ ПРОХОЖДЕНИЯ ПРАКТИКИ</h1>'.'<br>'.'<p class="bold">'.$arFields['ST6'].'<p>'.$data.'</div></body></html>';
            $dompdf->loadHtml($html, 'UTF-8');

// (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
            $dompdf->render();
            //$dompdf->stream();
// Output the generated PDF to Browser

            $doc = $dompdf->output(0);
            //AddMessage2Log($doc, "main");
            $filename = $arFields['ST6'].'.pdf';
            $filePath = $_SERVER['DOCUMENT_ROOT'] . "/upload/tmp/".$filename;

            //AddMessage2Log($doc, "main");
            file_put_contents($filePath, $doc);

// готовим массив
            $arFile = CFile::MakeFileArray($filePath);

            $id = CFile::SaveFile($arFile, "pdf");
// сохраняем в таблице b_file
            unlink($filePath);

            return $id;
        }catch(Exception $e){
            //AddMessage2Log($e->getMessage(), "main");
        }
    }

    static function RandomString()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < 9; $i++) {
            $randstring .= $characters[rand(0, strlen($characters))];
        }
        return $randstring;
    }
}
?>