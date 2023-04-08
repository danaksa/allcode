<?php
namespace \Ims\Components;

use Bitrix\Crm\DealTable;
use Bitrix\Main\Entity;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\UserGroupTable;
use Bitrix\Main\UserTable;
use Bitrix\Tasks\Internals\Task\LogTable;
use Bitrix\Main\Engine\Contract\Controllerable;
use CSite;
use CUser;
use Ims\Helpers\EmploymentLog;
use Ims\Helpers\Incident\IncidentChatHelper;
use Ims\Helpers\Incident\IncidentPermissionsHelper;
use Ims\Helpers\Incident\Task\IncidentTaskHelper;
use Ims\Helpers\Unloading\UnloadingIncidentChatDataHelper;

use PhpOffice\PhpSpreadsheet\Spreadsheet;


use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use NotaTools\Helpers\LoggerHelper;


class ActivityComponent extends \CBitrixComponent
{
    const ACTIVITYGRID_ID = "users_activity";

    const MESSAGESGRID_ID = "activ_unread_messages";

    const ACTIVITY_FILTER = [
        "ACTIVITY_HISTORY" => "История активности пользователей",
        "INCIDENT_HISTORY" => "Участники инцидента"
    ];

    private $arFilter = [];
    private $filter = [];


    private $MESS =[
        "TASKS_LOG_NEW" => "Создана задача",
        "TASKS_LOG_TITLE" => "Изменено название задачи",
        "TASKS_LOG_DESCRIPTION" => "Обновлено описание",
        "TASKS_LOG_RESPONSIBLE_ID" => "Изменен исполнитель",
        "TASKS_LOG_FILES" => "Файлы",
        "TASKS_LOG_TAGS" => "Теги",
        "TASKS_LOG_PRIORITY" => "Изменена важность задачи",
        "TASKS_LOG_GROUP_ID" => "Группа",
        "TASKS_LOG_PARENT_ID" => "Базовая задача",
        "TASKS_LOG_DEPENDS_ON" => "Предыдущие задачи",
        "TASKS_LOG_STATUS" => "Изменен статус",
        "TASKS_LOG_MARK" => "Оценка",
        "TASKS_LOG_ADD_IN_REPORT" => "В отчете",
        "TASKS_LOG_WHEN" => "Дата",
        "TASKS_LOG_WHO" => "Автор",
        "TASKS_LOG_WHERE" => "Где изменилось",
        "TASKS_LOG_WHAT" => "Значение до изменения",
        "TASKS_LOG_WHAT_TO" => "Значение после изменения",
        "TASKS_LOG_RENEW" => "Восстановлено из корзины",
        "TASKS_LOG_DELETED_FILES" => "Удалены файлы",
        "TASKS_LOG_NEW_FILES" => "Добавлены файлы",
        "TASKS_LOG_COMMENT" => "Добавлен комментарий",
        "TASKS_LOG_COMMENT_EDIT" => "Изменен комментарий",
        "TASKS_LOG_COMMENT_DEL" => "Удален комментарий",
        "TASKS_LOG_START_DATE_PLAN" => "Планируемая дата начала",
        "TASKS_LOG_END_DATE_PLAN" => "Изменена планируемая дата окончания",
        "TASKS_LOG_DURATION_PLAN" => "Изменена планируемая длительность",
        "TASKS_LOG_DURATION_PLAN_SECONDS" => "Изменена планируемая длительность, секунды",
        "TASKS_LOG_DURATION_FACT" => "Изменено затраченое по задаче время",
        "TASKS_LOG_TIME_ESTIMATE" => "Предполагаемые затраты времени",
        "TASKS_LOG_TIME_SPENT_IN_LOGS" => "Реальные затраты времени",
        "TASKS_LOG_CHECKLIST_ITEM_CREATE" => "Добавлен пункт в чек-лист",
        "TASKS_LOG_CHECKLIST_ITEM_REMOVE" => "Удален пункт из чек-листа",
        "TASKS_LOG_CHECKLIST_ITEM_RENAME" => "Пункт чек-листа переименован",
        "TASKS_LOG_CHECKLIST_ITEM_UNCHECK" => "Пункт чек-листа стал невыполненным",
        "TASKS_LOG_CHECKLIST_ITEM_CHECK" => "Пункт чек-листа выполнен",
        "TASKS_LOG_CHECKLIST_ITEM_MAKE_IMPORTANT" => "Пункт чек-листа стал важным",
        "TASKS_LOG_CHECKLIST_ITEM_MAKE_UNIMPORTANT" => "Пункт чек-листа стал неважным",
        "TASKS_LOG_ALLOW_CHANGE_DEADLINE" => "Флаг изменения дедлайна"
    ];


    /**
     * @param $arParams
     * @return mixed
     */
    public function onPrepareComponentParams($arParams)
    {
        $arParams['INCIDENT_ID'] = substr($this->request->getQuery('ENTITY_ID'), 2);
        return $arParams;
    }



    /**
     * @return void
     */
    public function executeComponent(): void
    {
        try {
            if ($this->checkAccess()) {

                $this->arResult['INCIDENT'] = $this->getIncident();
                $this->arResult['tasks'] = $this->gettasksbyincident($this->arParams['INCIDENT_ID']);
                $this->arResult['ACTIVITIES'] = $this->getIncidentActivities();
                $this->addUsersInfoToActivities();
                $this->arResult['oivlist'] = $this->getOivList();
                $this->arResult['UNREAD_MESSAGE'] = self::getChatMessageViewStatistics();
                $this->addUsersInfoToUnreadMessages();
                $this->arResult['activities'] = $this->getActivitiesList();
                $this->arResult['unread_messages'] = $this->getMessagesList();
                $this->arResult['ACTIVITY_FILTER_FIELD'] = $this->getActivityFilterField();
                $this->arResult['ACTIVITY_GRID_FIELD'] = self::getActivityGridField();
                $this->arResult['MESSAGE_GRID_FIELD'] = self::getMessagesGridField();
                $this->arResult['ACTIVITYGRID_ID'] = self::ACTIVITYGRID_ID;
                $this->arResult['MESSAGEGRID_ID'] = self::MESSAGESGRID_ID;
                $this->arResult['ACTIVITYTOTAL_ROWS_COUNT'] = count($this->arResult['activities']);
                $this->arResult['MESSAGESTOTAL_ROWS_COUNT'] = count($this->arResult['unread_messages']);
                $this->arResult['NAV_ACTIVITIES'] = $this->getNavActivities();
                $this->arResult['NAV_MESSAGES'] = $this->getNavMessages();
                $this->isAjaxRequest();
                $this->includeComponentTemplate();

            }
            else {
                ShowError(Loc::getMessage("MONITORING_ACCESS_DENIED"));
            }
        } catch (Exception $e) {
            ShowError($e->getMessage());
        }
    }

    protected function isAjaxRequest()
    {
       if($this->request->isAjaxRequest() && $this->request['action'] == 'DownloadExcel'){
           $this->DownloadExcelAction();
       }
    }

    protected function listKeysSignedParameters()
    {
        //перечисляем те имена параметров, которые нужно использовать в аякс-действиях
        return [
            'activities' => $this->arParams['activities'],
            'unread_messages' => $this->arParams['unread_messages'],
            'INCIDENT_ID' => $this->arParams['INCIDENT_ID']
        ];
    }

    /**
     * @return bool
     */
    private function checkAccess(): bool
    {
        $roles = Ims\Helpers\Incident\IncidentUserRolesHelper::getRoles($this->arParams['INCIDENT_ID'],CUser::GetID());
        $access = Ims\Helpers\Incident\IncidentPermissionsHelper::checkReadPermissions($this->arParams['INCIDENT_ID'],CUser::GetID());
        $baseroles = ['owner','duty_officers', 'OPERATIVE_EMPLOYE','approving', 'RECONCILING'];

        if (!empty(array_intersect($baseroles, $roles))) {
            if ($access){
                return true;
            }else{
                return false;
            }
        } elseif(CSite::InGroup(13)) {
            if ($access){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }


    /**
     * @return array|false
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private function getIncident(){

        $incident = DealTable::query()
            ->setSelect(['*', 'UF_*'])
            ->where('ID', $this->arParams['INCIDENT_ID'])
            ->fetch();

        return $incident;
    }

    /**
     *
     */
    public function DownloadExcelAction(){
        try{

            $activity = $this->arResult['activities'];
            $messages = $this->arResult['unread_messages'];
            $incident = $this->getIncident();
            global $APPLICATION;
            $APPLICATION->RestartBuffer();

            $logger = new LoggerHelper();
            $spreadsheet = new Spreadsheet();
            $sheet1 = $spreadsheet->getActiveSheet();
            $sheet1->setTitle('Участники инцидента');
            $rowArray = [];
            $mesgridfield = [
                [
                    'id' => 'userFio',
                    'sort' => 'userFio',
                    'name' => "ФИО",
                    'default' => true
                ],
                [
                    'id' => 'oiv',
                    'sort' => 'oiv',
                    'name' => "Наименование ОИВ/организации",
                    'default' => true
                ],
                [
                    'id' => 'position',
                    'sort' => 'position',
                    'name' => "Должность",
                    'default' => true
                ],

                [
                    'id' => 'email',
                    'sort' => 'email',
                    'name' => "E-mail пользователя",
                    'default' => true
                ],
                [
                    'id' => 'count',
                    'sort' => 'count',
                    'name' => "Количество непрочитанных сообщений",
                    'default' => true
                ]
            ];
            foreach ($mesgridfield as $field){
                $header = $field['name'];
                $rowArray[] = $header;
            }
            $sheet1->setCellValue('A1', 'Непрочитанные сообщения в чате по инциденту');
            $sheet1->getStyle('A1:E1')->applyFromArray([
                'font' => [
                    'name' => 'Arial',
                    'bold' => true,
                    'italic' => false,
                    'size' => 14,
                    'strikethrough' => false,
                    'color' => [
                        'rgb' => '000000'
                    ]
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => [
                            'rgb' => '000000'
                        ]
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ]
            ]);
            $sheet1->getStyle('A2:E2')->applyFromArray([
                'font' => [
                    'name' => 'Arial',
                    'bold' => true,
                    'italic' => false,
                    'size' => 12,
                    'strikethrough' => false,
                    'color' => [
                        'rgb' => '000000'
                    ]
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => [
                            'rgb' => '000000'
                        ]
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ]
            ]);
            $sheet1->getStyle('A:E')->getAlignment()->setWrapText(true);
            $sheet1->mergeCells('A1:E1');
            $sheet1->fromArray($rowArray,NULL,'A2');
            $i = 2;
            foreach ($messages as $task) {
                $i++;
                $rowArray = [];
                foreach ($mesgridfield as $field) {
                    //$prefix = '';
                    $columnValue = $task['data'][$field['id']];

                    $rowArray[] = $columnValue;
                }
                $sheet1->fromArray(
                    $rowArray,   // The data to set
                    '',        // Array values with this value will not be set
                    'A' . $i         // Top left coordinate of the worksheet range where
                );
            }

            $sheet1->getColumnDimension('A')->setWidth(43);
            $sheet1->getColumnDimension('B')->setWidth(31);
            $sheet1->getColumnDimension('C')->setWidth(39);
            $sheet1->getColumnDimension('D')->setWidth(33);
            $sheet1->getColumnDimension('E')->setWidth(33);


            $sheet2=$spreadsheet->createSheet();
            $sheet2->setTitle("История активности инцидента");
            $activitiesfield = [[
                    'id' => 'date',
                    'sort' => 'date',
                    'name' => "Дата",
                    'default' => true
                ],
                [
                    'id' => 'oiv',
                    'sort' => 'oiv',
                    'name' => "Наименование ОИВ/организации",
                    'default' => true
                ],
                [
                    'id' => 'Fio',
                    'sort' => 'Fio',
                    'name' => "ФИО",
                    'default' => true
                ],
                [
                    'id' => 'position',
                    'sort' => 'position',
                    'name' => "Должность",
                    'default' => true
                ],
                [
                    'id' => 'eventLangType',
                    'sort' => 'eventLangType',
                    'name' => "Вид активности в ИМС",
                    'default' => true
                ]
            ];
            $rowArray = [];
            foreach ($activitiesfield as $field){
                $header = $field['name'];
                $rowArray[] = $header;
            }

            $sheet2->setCellValue('A1', 'Сведения об активности пользователей (Инцидент № '.$incident['UF_SERIAL_NUMBER'].' - '.$incident['UF_SHORT_NAME'].')');
            $sheet2->getStyle('A1:E1')->applyFromArray([
                'font' => [
                    'name' => 'Calibri',
                    'bold' => true,
                    'italic' => false,
                    'size' => 20,
                    'strikethrough' => false,
                    'color' => [
                        'rgb' => '000000'
                    ]
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => [
                            'rgb' => '000000'
                        ]
                    ],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => [
                        'rgb' => 'bfbfbf'
                    ]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ]
            ]);
            $sheet2->getStyle('A2:E2')->applyFromArray([
                'font' => [
                    'name' => 'Calibri',
                    'bold' => true,
                    'italic' => false,
                    'size' => 11,
                    'strikethrough' => false,
                    'color' => [
                        'rgb' => '000000'
                    ]
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => [
                            'rgb' => '000000'
                        ]
                    ],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => [
                        'rgb' => 'd9d9d9'
                    ]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ]
            ]);
            $sheet2->getStyle('A:E')->getAlignment()->setWrapText(true);
            $sheet2->setAutoFilter('A2:E2');

            $sheet2->mergeCells('A1:E1');
            $sheet2->fromArray($rowArray,NULL,'A2');
            $i = 2;
            foreach ($activity as $task) {
                $i++;
                $rowArray = [];
                foreach ($activitiesfield as $field) {
                    //$prefix = '';
                    $columnValue = $task['data'][$field['id']];

                    $rowArray[] = $columnValue;
                }
                $sheet2->fromArray(
                    $rowArray,   // The data to set
                    '',        // Array values with this value will not be set
                    'A' . $i         // Top left coordinate of the worksheet range where
                );
            }
//            foreach (range('A','E') as $col) {
//                $sheet1->getColumnDimension($col)->setAutoSize(true);
//                $sheet2->getColumnDimension($col)->setAutoSize(true);
//            }
            $sheet2->getRowDimension(1)->setRowHeight(60);
            $sheet2->getColumnDimension('A')->setWidth(15);
            $sheet2->getColumnDimension('B')->setWidth(25);
            $sheet2->getColumnDimension('C')->setWidth(25);
            $sheet2->getColumnDimension('D')->setWidth(25);
            $sheet2->getColumnDimension('E')->setWidth(91);

            $writer = new Xlsx($spreadsheet);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="test.xlsx"');
            header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0

            ob_start();
            $writer->save("php://output");
            $xlsData = ob_get_contents();
            ob_end_clean();

            $response =  array(
                'status' => 'success',
                'file' => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
            );

            die(json_encode($response));
        }
         catch (\Exception $e) {
            $logger::logComponent($e->getMessage(),'ActivityMonitoring');
        }
    }


    private function getNavActivities(){
        $nav = new \Bitrix\Main\UI\PageNavigation(self::ACTIVITYGRID_ID);
        $grid_options = new \Bitrix\Main\Grid\Options(self::ACTIVITYGRID_ID);
        $nav_params = $grid_options->GetNavParams();
        $nav->allowAllRecords(true)
            ->setPageSize($nav_params['nPageSize'])
            ->initFromUri();
        $nav->setRecordCount($this->arResult['ACTIVITYTOTAL_ROWS_COUNT']);
        return $nav;
    }

    private function getNavMessages(){
        $nav = new \Bitrix\Main\UI\PageNavigation(self::MESSAGESGRID_ID);
        $grid_options = new \Bitrix\Main\Grid\Options(self::MESSAGESGRID_ID);
        $nav_params = $grid_options->GetNavParams();
        $nav->allowAllRecords(true)
            ->setPageSize($nav_params['nPageSize'])
            ->initFromUri();
        $nav->setRecordCount($this->arResult['MESSAGESTOTAL_ROWS_COUNT']);
        return $nav;
    }

    private function getIncidentActivities() : array
    {
        global $DB;

        $result = [];


        if(!empty($this->arResult['tasks'])){
          $result = array_merge($result, EmploymentLog::getLog(false, false, $this->arParams['INCIDENT_ID']));
        }



        $result = array_merge($result, UnloadingIncidentChatDataHelper::getDataByIncidentId($this->arParams['INCIDENT_ID']));
        //$result = array_merge($result, UnloadingIncidentTaskViewedHelper::getTaskViewedById($this->arParams['INCIDENT_ID']));

        usort($result, function($a, $b) {
            return ($a['date'] > $b['date']);
        });

        foreach ($result as $key => $item) {
            $result[$key]['date'] = $DB->FormatDate($item["date"], "YYYY-MM-DD HH:MI:SS", "DD.MM.YYYY HH:MI");
        }

        return $result;
    }

    private function addUsersInfoToUnreadMessages() : void
    {
        $usersIds = array_column($this->arResult['UNREAD_MESSAGE'], 'userId');

        $usersInfo = self::getUsersInfo($usersIds);

        foreach ($usersInfo as $user) {
            foreach ($this->arResult['UNREAD_MESSAGE'] as $key => $activity) {
                if ($user['ID'] == $activity['userId']) {
                    $this->arResult['UNREAD_MESSAGE'][$key]['userFio'] = '';

                    if (!empty($user['LAST_NAME'])) {
                        $this->arResult['UNREAD_MESSAGE'][$key]['userFio'] .= $user['LAST_NAME'];
                    }

                    if (!empty($user['NAME'])) {
                        $this->arResult['UNREAD_MESSAGE'][$key]['userFio'] .= ' '.$user['NAME'];
                    }

                    if (!empty($user['SECOND_NAME'])) {
                        $this->arResult['UNREAD_MESSAGE'][$key]['userFio'] .= ' '.$user['SECOND_NAME'];
                    }

                    $this->arResult['UNREAD_MESSAGE'][$key]['email'] = $user['EMAIL'];

                    $this->arResult['UNREAD_MESSAGE'][$key]['userFio'] = trim($this->arResult['UNREAD_MESSAGE'][$key]['userFio']);
                    $this->arResult['UNREAD_MESSAGE'][$key]['position'] = $user['WORK_POSITION'];
                    $this->arResult['UNREAD_MESSAGE'][$key]['oiv'] = \Nota\Ims\Service\UserService::getUserDepartment($activity['userId']);
                }
            }
        }
    }

    public static function getTasksByIncident($incidentId) {
        $taskHelper = new IncidentTaskHelper;
        return $taskHelper->getTasks($incidentId, [], false);
    }

    /**
     * @return array
     */
    private function getOivList(){
        $oivlist = [];
        foreach($this->arResult['ACTIVITIES'] as $item){
            if ($item['eventType'] == "NOTHING"
                || $item['eventType'] == "CREATED_BY"
                || $item['eventType'] == "ACCOMPLICES"
                || $item['eventType'] == "AUDITORS"
                || $item['eventType'] == "DEADLINE"
            ) {
                continue;
            }
            if(!empty($item['oiv'])&&!in_array($item['oiv'],$oivlist)){
                $oivlist[] = $item['oiv'];
            }
        }
        return $oivlist;
    }

    private function addUsersInfoToActivities() : void
    {
        $usersIds = array_column($this->arResult['ACTIVITIES'], 'userId');

        $usersInfo = self::getUsersInfo($usersIds);

        foreach ($usersInfo as $user) {
            foreach ($this->arResult['ACTIVITIES'] as $key => $activity) {
                if ($user['ID'] == $activity['userId']) {
                    $this->arResult['ACTIVITIES'][$key]['userFio'] = '';
                    $this->arResult['ACTIVITIES'][$key]['Fio'] = '';
                    if (!empty($user['LAST_NAME'])) {
                        $this->arResult['ACTIVITIES'][$key]['userFio'] .= $user['LAST_NAME'];
                        $this->arResult['ACTIVITIES'][$key]['Fio'] .= $user['LAST_NAME'];
                    }

                    if (!empty($user['NAME'])) {
                        $this->arResult['ACTIVITIES'][$key]['userFio'] .= ' '.$user['NAME'];
                        $this->arResult['ACTIVITIES'][$key]['Fio'] .= ' '.$user['NAME'];
                    }

                    if (!empty($user['SECOND_NAME'])) {
                        $this->arResult['ACTIVITIES'][$key]['userFio'] .= ' '.$user['SECOND_NAME'];
                        $this->arResult['ACTIVITIES'][$key]['Fio'] .= ' '.$user['SECOND_NAME'];
                    }

                    if(!empty($user['WORK_POSITION'])){
                        $this->arResult['ACTIVITIES'][$key]['userFio'] .= '. '.$user['WORK_POSITION'];
                        $this->arResult['ACTIVITIES'][$key]['position'] = $user['WORK_POSITION'];
                    }
                    $this->arResult['ACTIVITIES'][$key]['userFio'] = trim($this->arResult['ACTIVITIES'][$key]['userFio']);

                    $this->arResult['ACTIVITIES'][$key]['oiv'] = Nota\Ims\Service\UserService::getUserDepartment($activity['userId']);
                }
            }
        }

    }

    /**
     * @return array
     */
    private function getActivitiesList(){
//        \Bitrix\Main\Loader::includeModule('main');
//        \Bitrix\Main\Loader::includeModule('tasks');

//        $tasks = self::getTasksByIncident($this->arParams['INCIDENT_ID']);

        //$arFilter = [];
        $result = [];
        $id = 1;
        $this->setActivityFilter();
        global $DB;
	    //создаем объект пагинации
        $nav = new \Bitrix\Main\UI\PageNavigation(self::ACTIVITYGRID_ID);
        $grid_options = new \Bitrix\Main\Grid\Options(self::ACTIVITYGRID_ID);
        $nav_params = $grid_options->GetNavParams();
        $nav->allowAllRecords(true)
            ->setPageSize($nav_params['nPageSize'])
            ->initFromUri();

        $sort = $grid_options->GetSorting(['sort' => ['date' => 'ASC'], 'vars' => ['by' => 'by', 'order' => 'order']]);

//        $where_str2=$this->getActivityFilter();
//        $arFilter = ["TASK_ID" => $tasks];
//        $arFilter = array_merge($arFilter,$where_str2);
//        $CTaskLog = 'b_tasks_log';
//        $CUser = 'b_user';
        $where_str = '';
        // в sql вставляем limit и Offset
        //$strSql = 'SELECT b.task_id as taskid, b.FIELD as eventLangType, b.CREATED_DATE as eventLangType, c.name as name, c.last_name as last_name, c.second_name as second_name, c.work_position as work_position, c.work_department as oiv FROM '.$CTaskLog.' b LEFT JOIN '.$CUser.' c on b.user_id = с.id '.$where_str.' '.'order by '.key($sort['sort']).' '.current($sort['sort']).' LIMIT '.$nav->getLimit().'  OFFSET '.$nav->getOffset(); //
        //$UsersActivity = $DB->Query($strSql, false, $err_mess.__LINE__);

        $key = key($sort['sort']);

        $activities = $this->arResult['ACTIVITIES'];
        if(current($sort['sort']) == 'asc'){
            switch ($key){
                case "userFio":{
                    uasort($activities, function ($a, $b) {
                        return strcmp($a["userFio"], $b["userFio"]);
                    });
                    break;
                }
                case "date":{
                    uasort($activities, function ($a, $b) {
                        return (strtotime($a["date"]) > strtotime($b["date"]));
                    });
                    break;
                }
                case "eventLangType":{
                    uasort($activities, function ($a, $b) {
                        return strcmp($a["eventLangType"],$b["eventLangType"]);
                    });
                }
                case "oiv":{
                    uasort($activities, function ($a, $b) {
                        return strcmp($a["oiv"],$b["oiv"]);
                    });
                }
            }
        }else{
            switch ($key){
                case "userFio":{
                    uasort($activities, function ($a, $b) {
                        return strcmp($b["userFio"], $a["userFio"]);
                    });
                    break;
                }
                case "date":{
                    uasort($activities, function ($a, $b) {
                        return (strtotime($a["date"]) < strtotime($b["date"]));
                    });
                    break;
                }
                case "eventLangType":{
                    uasort($activities, function ($a, $b) {
                        return strcmp($b["eventLangType"],$a["eventLangType"]);
                    });
                }
                case "oiv":{
                    uasort($activities, function ($a, $b) {
                        return strcmp($b["oiv"],$a["oiv"]);
                    });
                }
            }
        }


        foreach($activities as $item){
            if ($item['eventType'] == "NOTHING"
                || $item['eventType'] == "CREATED_BY"
                || $item['eventType'] == "ACCOMPLICES"
                || $item['eventType'] == "AUDITORS"
                || $item['eventType'] == "DEADLINE"
            ) {
                continue;
            }
            $result[]['data'] = ['id' => $id, 'userId' => $item['userId'], 'date' => $item['date'], 'userFio' => $item['userFio'], 'Fio' => $item['Fio'], 'position' => $item['position'], 'oiv' => $item['oiv'], 'eventLangType' => $item['eventLangType']];

            $id++;
        }

        return $this->filter($result);
    }

    /**
     * @param $result
     * @return mixed
     */
    private function filter($result){
        $filter = $this->getActivityFilter();

        $filterresult = $result;

        $i =0;
        foreach($result as $item) {
            if (!empty($filter['date'])) {
                if(empty($item['data']['date'])){
                    $include = true;
                }else{
                    if(strtotime($item['data']['date']) >= strtotime(current($filter['date'][0])) && strtotime($item['data']['date']) <= strtotime(current($filter['date'][1]))){
                        $include = true;
                    }else{
                        $include = false;
                    }
                }
                if (!$include) {
                    unset($filterresult[$i]);
                }
            }
            if (!empty($filter['userFio'])) {
                 if (!in_array('U'.$item['data']['userId'],$filter['userFio'])) {
                     unset($filterresult[$i]);
                 }
            }
            if (!empty($filter['eventLangType'])) {
                if (!in_array(array_search($item['data']['eventLangType'], $this->MESS) , $filter['eventLangType'])) {
                    unset($filterresult[$i]);
                }
            }

            if (!empty($filter['oiv'])) {
                if (!in_array(array_search($item['data']['oiv'],$this->arResult['oivlist']),$filter['oiv'])) {
                    unset($filterresult[$i]);
                }
            }
            $i++;
        }
        return $filterresult;
    }

    /**
     * @return array
     */
    private function getMessagesList(){
        $result = [];
        $id = 1;

        $nav = new \Bitrix\Main\UI\PageNavigation(self::MESSAGESGRID_ID);
        $grid_options = new \Bitrix\Main\Grid\Options(self::MESSAGESGRID_ID);
        $sort = $grid_options->GetSorting(['sort' => ['userFio' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);
        $key = key($sort['sort']);
        $messages = $this->arResult['UNREAD_MESSAGE'];
        if(current($sort['sort']) == 'asc'){
            switch ($key){
                case "userFio":{
                    uasort($messages, function ($a, $b) {
                        return strcmp($a["userFio"], $b["userFio"]);
                    });
                    break;
                }
                case "count":{
                    uasort($messages, function ($a, $b) {
                        return ($a["count"] > $b["count"]);
                    });
                    break;
                }
                case "position":{
                    uasort($messages, function ($a, $b) {
                        return strcmp($a["position"],$b["position"]);
                    });
                }
                case "email":{
                    uasort($messages, function ($a, $b) {
                        return strcmp($a["email"],$b["email"]);
                    });
                }
                case "oiv":{
                    uasort($messages, function ($a, $b) {
                        return strcmp($a["oiv"],$b["oiv"]);
                    });
                }
            }
        }else {
            switch ($key) {
                case "userFio":
                {
                    uasort($messages, function ($a, $b) {
                        return strcmp($b["userFio"], $a["userFio"]);
                    });
                    break;
                }
                case "count":
                {
                    uasort($messages, function ($a, $b) {
                        return ($a["count"] < $b["count"]);
                    });
                    break;
                }
                case "position":
                {
                    uasort($messages, function ($a, $b) {
                        return strcmp($b["position"], $a["position"]);
                    });
                }
                case "oiv":
                {
                    uasort($messages, function ($a, $b) {
                        return strcmp($b["oiv"], $a["oiv"]);
                    });
                }
                case "email":
                {
                    uasort($messages, function ($a, $b) {
                        return strcmp($b["email"], $a["email"]);
                    });
                }
            }
        }
       foreach ($messages as $item){
            $result[]['data'] = ['id' => $id, 'userFio' => $item['userFio'], 'oiv' => $item['oiv'], 'count' => $item['count'], 'email' => $item['email'], 'position' => $item['position']];
            $id++;
       }

        return $result;
    }



    /**
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private function getChatMessageViewStatistics() : array
    {
        Loader::includeModule('im');
        $result = [];

        $chat = IncidentChatHelper::getChatByIncidentId($this->arParams['INCIDENT_ID']);

        if (!$chat) {
            return [];
        }

        $unreadMessages = [];

        $incidentMembers = IncidentPermissionsHelper::getUsersWhoCanEdit($this->arResult['INCIDENT']);

        foreach ($incidentMembers as $userId) {
            $CIMChat = new \CIMChat($userId);
            $arMessage = $CIMChat->GetUnreadMessage([
                'USE_TIME_ZONE' => 'N',
                'USER_LOAD' => 'N',
                'LOAD_DEPARTMENT' => 'N',
                'FILE_LOAD' => 'N',
                'SPEED_CHECK' => 'Y',
                'MESSAGE_TYPE' => IM_MESSAGE_CHAT
            ]);

            if ($arMessage['result']
                && array_key_exists($chat['ID'], $arMessage['unreadMessage'])
            ) {
                $unreadMessages[$userId] = count($arMessage['unreadMessage'][$chat['ID']]);
            } else {
                $unreadMessages[$userId] = 0;
            }
        }

        foreach ($unreadMessages as $userId => $countUnreadMessages) {
            $result[$userId] = [
                'userId' => $userId,
                'eventType' => 'unreadChatMessages',
                'count' => $countUnreadMessages,
                'date' => '',
            ];
        }

        return $result;
    }
    /**
     * @return void
     */
    private function setActivityFilter(): void
    {
        $this->filter = $this->request->getQuery('filter') ? $this->request->getQuery('filter') : [];

        $filterOption = new \Bitrix\Main\UI\Filter\Options(self::ACTIVITYGRID_ID . '_filter');
        $filterData = $filterOption->getFilter();

        $allowedFilters = [
            "date",
            "date_to",
            "date_from",
            "userFio",
            "oiv",
            "eventLangType"
        ];

        foreach ($filterData as $k => $v) {
            if ($v) {
                if (array_key_exists($k, self::getActivitySearchFields())
                    || in_array($k, $allowedFilters)
                ) {
                    switch ($k) {
                        case "date_from": {
                            $dateFilter = ["LOGIC" => "AND"];
                            $dateFilter[] = [
                                ">=date" => $v
                            ];

                            if (!empty($filterData['date_to'])) {
                                $dateFilter[] = [
                                    "<=date" => $filterData['date_to']
                                ];
                            }

                            $this->arFilter['date'] = $dateFilter;
                            break;
                        }
                        case "userFio": {
                            $this->arFilter[$k] = $v;
                            break;
                        }
                        case "oiv": {
                            $this->arFilter[$k] = $v;
                            break;
                        }
                        case "eventLangType": {
                            $this->arFilter[$k] = $v;
                            break;
                        }
                    }
                }
            }
        }
    }

    /**
     * @return array
     */
    private function getActivityFilter(): array
    {
        return $this->arFilter;
    }

    /**
     * @return array
     */
    public static function getActivitySearchFields(): array
    {

        $gridFields = self::getActivityGridField();

        //unset($gridFields[2]);

        $searchFields = array_combine(array_column($gridFields, 'id'), array_column($gridFields, 'name'));

        return $searchFields;
    }


    private function getUsersInfo(array $usersIds) : array
    {
        $result = \CUser::GetList(
            ($by = 'LAST_NAME'),
            ($order = 'ASC'),
            [
                'ID' => $usersIds,
            ],
            [
                'SELECT' => [
                    'ID', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'UF_DEPARTMENT'
                ]
            ]
        );


        $users = [];

        while ($user = $result->Fetch()) {
            $users[$user['ID']] = $user;
        }

        return $users;
    }

    /**
     * @return array
     */
    private function getActivityFilterField(): array
    {
        $filterField = [];

        foreach (self::getActivitySearchFields() as $field => $name) {
            if ($field == "ID") {
                continue;
            }

            $arFilterData = [
                "id" => $field,
                "default" => true,
                "name" => $name
            ];
            switch ($field) {
                case "date":
                {
                    $arFilterData["type"] = 'date';
                    break;
                }
                case "userFio":
                {
                    $arFilterData["type"] = 'dest_selector';
                    $arFilterData["params"] = [
                        'context' => 'CRM_FILTER_USER',
                        'multiple' => 'Y',
                    ];
                    break;
                }
                case "eventLangType":
                {
                    $arFilterData["type"] = 'list';

                    $arFilterData["items"] = $this->MESS;
                    $arFilterData["params"] = ['multiple' => 'Y'];
                    break;
                }
                case "oiv":
                {
                    $arFilterData["type"] = 'list';
                    $arFilterData["items"] = $this->arResult['oivlist'];
                    $arFilterData["params"] = [    'multiple' => 'Y'];
                    break;
                }
            }
            $filterField[] = $arFilterData;
        }
        return $filterField;
    }

    /**
     * @return array
     */
    private static function getActivityGridField()
    {
        return [
            [
                'id' => 'date',
                'sort' => 'date',
                'name' => "Дата и время события",
                'default' => true
            ],
            [
                'id' => 'userFio',
                'sort' => 'userFio',
                'name' => "ФИО пользователя",
                'default' => true
            ],
            [
                'id' => 'oiv',
                'sort' => 'oiv',
                'name' => "ОИВ/организация пользователя",
                'default' => true
            ],
            [
                'id' => 'eventLangType',
                'sort' => 'eventLangType',
                'name' => "Вид активности в ИМС",
                'default' => true
            ],
        ];
    }

    /**
     * @return array
     */
    private static function getMessagesGridField()
    {
        return [
            [
                'id' => 'userFio',
                'sort' => 'userFio',
                'name' => "ФИО пользователя",
                'default' => true
            ],
            [
                'id' => 'position',
                'sort' => 'position',
                'name' => "Должность",
                'default' => true
            ],
            [
                'id' => 'oiv',
                'sort' => 'oiv',
                'name' => "ОИВ/организация пользователя",
                'default' => true
            ],
            [
                'id' => 'email',
                'sort' => 'email',
                'name' => "E-mail пользователя",
                'default' => true
            ],
            [
                'id' => 'count',
                'sort' => 'count',
                'name' => "Количество непрочитанных сообщений в чате инцидента",
                'default' => true
            ]
        ];
    }

    public function configureActions()
    {
        // Сбрасываем фильтры по-умолчанию (ActionFilter\Authentication и ActionFilter\HttpMethod)
        // Предустановленные фильтры находятся в папке /bitrix/modules/main/lib/engine/actionfilter/
        return [
            'DownloadExcel' => [ // Ajax-метод
                'prefilters' => [],
            ],
        ];
    }
}
