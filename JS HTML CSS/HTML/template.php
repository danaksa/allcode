<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->addExternalJS(SITE_TEMPLATE_PATH."/js/jquery.maskedinput.min.js");
$this->addExternalJS(SITE_TEMPLATE_PATH."/js/jquery.inputmask.min.js");
$this->addExternalCss("https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css");
$this->addExternalJS("https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js");
$this->addExternalJS(SITE_TEMPLATE_PATH."/js/bootstrap-filestyle.min.js");

?>
<div class="modal fade" id="modal-success" tabindex="-1" role="dialog" aria-labelledby="modal-successCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-successCenterTitle">Отправка резюме</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success" role="alert">
                    <?if(LANGUAGE_ID == 'ru'){
                        echo("Ваше резюме успешно отправлено");
                    }elseif(LANGUAGE_ID == 'en'){
                        echo("Your resume has been successfully sent");
                    }?>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-fail" class="alert alert-danger" role="alert" style="display:none;max-width:500px;">
    <p class="modal-title">
        <?if(LANGUAGE_ID == 'ru'){
            echo("При отправке произошла ошибка, обратитесь к системному администратору");
        }elseif(LANGUAGE_ID == 'en'){
            echo("An error occurred while submitting, contact your system administrator");
        }?>
    </p>
</div>

<?
//$arResult["isUseCaptcha"] = "Y";
?><div class="title"><h3><?=GetMessage("MESSONLAIN");?></h3></div><?
if ($arResult["isFormErrors"] == "Y"):?><?=$arResult["FORM_ERRORS_TEXT"];?><?endif;?>

<?=$arResult["FORM_NOTE"]?>
<?if ($arResult["isFormNote"] != "Y")
{
?>
<?=$arResult["FORM_HEADER"]?>

<br/>
<div class="row">
<div class="col-md-12 col-lg-6 col-sm-12">

	<?
    $i = 0;

	foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion)
	{
        $lablename = GetMessage("FORM_".$FIELD_SID);
        if(strpos($arQuestion["HTML_CODE"],'form_checkbox_AGREEMENT[]') !== false){
            $arQuestion["HTML_CODE"] = str_replace('form_checkbox_AGREEMENT[]','form_checkbox_AGREEMENT',$arQuestion["HTML_CODE"]);
        }

        $i++;
        if($i == 6){?>
          </div>
          <div class="col-md-12 col-lg-6 col-sm-12">
        <?}
        if(isset($arParams[$FIELD_SID]) && $arParams[$FIELD_SID]['VALUE'] && $arParams[$FIELD_SID]['AUTOCOMPLETE'] == 'Y') {
            $arQuestion['HTML_CODE'] = str_replace('name=', 'value="'.$arParams[$FIELD_SID]['VALUE'].'" name=', $arQuestion['HTML_CODE']);
        }
		if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'hidden')
		{
			echo $arQuestion["HTML_CODE"];
		}

		else
		{?>


          <?if($i == 9){
               ?>
                <div class="row">
                    <div class="form-group uploader col-md-6 col-sm-6 col-xs-10">
                        <label for="<?=$FIELD_SID?>">
                            <?if (is_array($arResult["FORM_ERRORS"]) && array_key_exists($FIELD_SID, $arResult['FORM_ERRORS'])):?>
                                <span class="error-fld" title="<?=htmlspecialcharsbx($arResult["FORM_ERRORS"][$FIELD_SID])?>"></span>
                            <?endif;?>
                            <?=$lablename?><?if ($arQuestion["REQUIRED"] == "Y"):?><?=$arResult["REQUIRED_SIGN"];?><?endif;?>
                        </label>

                        <?=$arQuestion["HTML_CODE"]?>
                    </div>
                    <div class="form group col-md-6 col-sm-6 col-xs-6 float-right">
                        <input <?=(intval($arResult["F_RIGHT"]) < 10 ? "disabled=\"disabled\"" : "");?> type="submit" name="web_form_submit" class="btn btn-primary" value="<?=GetMessage("FORM_CONFIRM");?>" />
                        <?if ($arResult["F_RIGHT"] >= 15):?>
                        <input type="hidden" name="web_form_apply" value="Y" />
                        <?endif;?>
                    </div>
                </div>
            <?
          }
          elseif($FIELD_SID == "AGREEMENT"){
              ?>
                  <div class="form-group">
                      <?=$arQuestion["HTML_CODE"]?>
                      <label for="<?=$FIELD_SID?>">
                          <?if (is_array($arResult["FORM_ERRORS"]) && array_key_exists($FIELD_SID, $arResult['FORM_ERRORS'])):?>
                              <span class="error-fld" title="<?=htmlspecialcharsbx($arResult["FORM_ERRORS"][$FIELD_SID])?>"></span>
                          <?endif;?>
                          <?=$lablename?><?if ($arQuestion["REQUIRED"] == "Y"):?><?=$arResult["REQUIRED_SIGN"];?><?endif;?>
                      </label>
                      
                      <p>
                          <?=$arResult["REQUIRED_SIGN"];?> - <?=GetMessage("FORM_REQUIRED_FIELDS")?>
                      </p>
                  </div>
              <?
          }
          else{?>
              <div class="form-group">
                        <label for="<?=$FIELD_SID?>">
                            <?if (is_array($arResult["FORM_ERRORS"]) && array_key_exists($FIELD_SID, $arResult['FORM_ERRORS'])):?>
                                <span class="error-fld" title="<?=htmlspecialcharsbx($arResult["FORM_ERRORS"][$FIELD_SID])?>"></span>
                            <?endif;?>
                            <?=$lablename?><?if ($arQuestion["REQUIRED"] == "Y"):?><?=$arResult["REQUIRED_SIGN"];?><?endif;?>
                        </label>
                        <?=$arQuestion["HTML_CODE"]?>
               </div>
        <?}

        ?>

	<?
		}
	} //endwhile
?>
</div>
</div>



<?=$arResult["FORM_FOOTER"]?>
<?
} //endif (isFormNote)?>

<script>

    //$("input.phone").mask("8(999) 999-9999");
    $("input.email").inputmask({
        mask: "*{1,20}[.*{1,20}][.*{1,20}][.*{1,20}]@*{1,20}[.*{2,6}][.*{1,2}]",
        greedy: false,
        onBeforePaste: function (pastedValue, opts) {
            pastedValue = pastedValue.toLowerCase();
            return pastedValue.replace("mailto:", "");
        },
        definitions: {
            '*': {
                validator: "[0-9A-Za-z!#$%&'*+/=?^_`{|}~\-]",
                cardinality: 1,
                casing: "lower"
            }
        }
    });


</script>