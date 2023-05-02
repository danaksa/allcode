<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
// Подключаем модуль веб-форм
$filelog = 'resultlog_'.$_POST['form_hidden_58'].'_'.date('d.m.Y').'.txt';

CModule::IncludeModule("form");

// Проверка валидности отправки формы
if (check_bitrix_sessid()) {
    $formErrors = CForm::Check($_POST['WEB_FORM_ID'], $_REQUEST, false, "Y", 'Y');
// Если не все обязательные поля заполнены
if (count($formErrors)) {
    echo json_encode(['success' => false, 'formerror' => true, 'errorsform' => $formErrors]);
} elseif ($RESULT_ID = CFormResult::Add($_POST['WEB_FORM_ID'], $_REQUEST)) {
// Отправляем все события как в компоненте веб форм
    CFormCRM::onResultAdded($_POST['WEB_FORM_ID'], $RESULT_ID);

    CFormResult::SetEvent($RESULT_ID);
    CFormResult::Mail($RESULT_ID);

    //huntflow start

    try{
        //var_dump($_POST);
        function filter_filename($name) {
            $name = str_replace(array_merge(
                array_map('chr', range(0, 31)),
                array('<', '>', ':', '"', '/', '\\', '|', '?', '*')
            ), '', $name);
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $name= mb_strcut(pathinfo($name, PATHINFO_FILENAME), 0, 255 - ($ext ? strlen($ext) + 1 : 0), mb_detect_encoding($name)) . ($ext ? '.' . $ext : '');
            return transliterate($name);
        }

        function RandomString()
        {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randstring = '';
            for ($i = 0; $i < 5; $i++) {
                $randstring .= $characters[rand(0, strlen($characters))];
            }
            return $randstring;
        }

        function transliterate($textcyr = null, $textlat = null) {
            $cyr = [
                'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п',
                'р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
                'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П',
                'Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я'
            ];
            $lat = [
                'a','b','v','g','d','e','io','zh','z','i','y','k','l','m','n','o','p',
                'r','s','t','u','f','h','ts','ch','sh','sht','a','i','y','e','yu','ya',
                'A','B','V','G','D','E','Io','Zh','Z','I','Y','K','L','M','N','O','P',
                'R','S','T','U','F','H','Ts','Ch','Sh','Sht','A','I','Y','e','Yu','Ya'
            ];
            $textcyr = str_replace($cyr, $lat, $textcyr);
            $textcyr = str_replace(" ", "_", $textcyr);

            return $textcyr;
        }

        function uploadToHF($FILE,$filelog){
            $curl = curl_init();

            $filename = "";
            $tmp_filename_parts = explode(".", filter_filename($FILE['name']));
            for($i=0; $i<count($tmp_filename_parts); $i++){
                if($i < (count($tmp_filename_parts)-1) ){
                    $filename .= $tmp_filename_parts[$i];
                }else{
                    $filename .= "_".RandomString().".".$tmp_filename_parts[$i];
                }
            }

            $path = "/var/tmp/".$filename;
            rename($FILE['tmp_name'], $path);
            $file = new CURLFILE($path, mime_content_type($path), $filename);

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.huntflow.ru/v2/accounts/23158/upload',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0, //
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('file'=> $file),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer 5d039df3b906efb13f3c62a5744cc10716287634f91ba3c6d191ff595b1135a0',
                    'X-File-Parse: true',
                    'Content-Type: multipart/form-data'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            //var_dump($response);
            //file_put_contents($filelog, PHP_EOL . 'Загрузка резюме в хф: '.json_encode(json_decode($response), JSON_UNESCAPED_UNICODE),FILE_APPEND | LOCK_EX);
            return json_decode($response, true);
        }


        if ($_FILES['form_file_57']) {
            $result = uploadToHF($_FILES['form_file_57'],$filelog);
            //echo 'файл в хф загружен: id = '.$result["id"];
            file_put_contents($filelog,PHP_EOL . 'файл в хф загружен: '.$result,FILE_APPEND | LOCK_EX);
        }

        $paramsPostField = array();
        $paramsPostField['first_name'] = strip_tags($_POST['form_text_52']);//
        $paramsPostField['last_name'] = strip_tags($_POST['form_text_61']);//
        $paramsPostField['middle_name'] = strip_tags($_POST['form_text_62']);//
        $paramsPostField['phone'] = $_POST['form_text_53'];//
        $paramsPostField['email'] = $_POST['form_email_54'];//
        $paramsPostField['position'] = $_POST['form_text_63'];//
        $paramsPostField['idHf'] = $_POST['form_hidden_58'];//
        $paramsPostField['externals'][0]['auth_type'] = "NATIVE";
        $paramsPostField['externals'][0]['files'][] = $result["id"];

        file_put_contents($filelog,PHP_EOL . 'параметры '.serialize($paramsPostField),FILE_APPEND | LOCK_EX);
        //'параметры: ';
        //print_r($paramsPostField);
        //echo json_encode($paramsPostField);


        if ($_POST["form_hidden_58"]) {//id вакансии в скрытом в форме
            $paramsPostField = json_encode($paramsPostField);
            $url_data = "https://api.huntflow.ru/v2/accounts/23158/applicants";

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url_data,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $paramsPostField,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer 5d039df3b906efb13f3c62a5744cc10716287634f91ba3c6d191ff595b1135a0',
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            //echo 'кандидат в хф добавлен: ';
            //echo $response;

            $response2 = json_decode($response);
            $idApplicant = $response2->{'id'};

            //file_put_contents('candidateadd.json',json_encode($response2, JSON_UNESCAPED_UNICODE));
            file_put_contents($filelog, PHP_EOL . 'Загрузка кандидата в хф: '.json_encode($response2, JSON_UNESCAPED_UNICODE),FILE_APPEND | LOCK_EX);

            if ($idApplicant) {
                echo $idApplicant;
                $paramsPostFieldVacancy = array();
                $paramsPostFieldVacancy["vacancy"] = $_POST["form_hidden_58"];
                $paramsPostFieldVacancy["status"] = 54971; //новые
                $paramsPostFieldVacancy["files"] = array($result["id"]);
                $paramsPostFieldVacancy = json_encode($paramsPostFieldVacancy);
                $url_data = "https://api.huntflow.ru/v2/accounts/23158/applicants/".$idApplicant."/vacancy";

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url_data,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $paramsPostFieldVacancy,
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: Bearer 5d039df3b906efb13f3c62a5744cc10716287634f91ba3c6d191ff595b1135a0',
                        'Content-Type: application/json'
                    ),
                ));

                $response1 = curl_exec($curl);
                curl_close($curl);

                //echo 'кандидат в хф на вакансию и с резюме добавлен: ';
                //echo $response1;
                //file_put_contents('candidatevacancyadd.json',json_encode(json_decode($response1), JSON_UNESCAPED_UNICODE));
                file_put_contents($filelog,PHP_EOL . 'кандидат в хф на вакансию и с резюме добавлен: '.json_encode(json_decode($response1), JSON_UNESCAPED_UNICODE),FILE_APPEND | LOCK_EX);
                return json_encode(['success' => true, 'errors' => []]);
            }else{
                file_put_contents($filelog,'errors: Ошибка добавления кандидата в хф',FILE_APPEND | LOCK_EX);
                echo json_encode(['success' => false, 'error' => true, 'errors' => []]);
            }
        }
    }catch(Exception $e){
        file_put_contents($filelog,PHP_EOL . 'errors: '.$e->getMessage(),FILE_APPEND | LOCK_EX);
        echo json_encode(['success' => false, 'error' => true]);
    }
    //var_dump($_FILES['form_file_57']);

} else {
    file_put_contents($filelog,PHP_EOL . 'errors: '.$GLOBALS["strError"],FILE_APPEND | LOCK_EX);
// Какие-то еще ошибки произошли
    echo json_encode(['success' => false, 'error' => true]);
}
} else {
// Предотвратили CSRF атаку
    file_put_contents($filelog,PHP_EOL . 'errors: Не верная сессия. Попробуйте обновить страницу',FILE_APPEND | LOCK_EX);
    echo json_encode(['success' => false, 'error' => true]);
}

// Файл ниже подключать обязательно, там закрытие соединения с базой данных
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';