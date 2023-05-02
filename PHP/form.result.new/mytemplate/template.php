<?

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->addExternalCss("https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css");
$this->addExternalJS("https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js");

$this->addExternalJS("https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js");

//$arResult["isUseCaptcha"] = "Y";
?>
<div class="container">
<div class="title"><?=GetMessage("MESS");?></div><?
if ($arResult["isFormErrors"] == "Y"):?><?=$arResult["FORM_ERRORS_TEXT"];?><?endif;?>

<?=$arResult["FORM_NOTE"]?>
<?if ($arResult["isFormNote"] != "Y")
{
?>
<?=$arResult["FORM_HEADER"]?>

<br/>
<div class="formblock">
	<?

	$i = 0;
	foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion)
	{

      
		if($i == 4){
			?>
  <div class="form-group">
              <label class="labelblock">Состав заявки</label>              
  </div>
  <div class="row block"><?
		}

		if($i >= 4 && $i <= 8){
			?>
			<div class="col-md-2 col-lg-2 col-sm-2">
              <label class="labelblock app" for="<?=$FIELD_SID?>">
                            <?if (is_array($arResult["FORM_ERRORS"]) && array_key_exists($FIELD_SID, $arResult['FORM_ERRORS'])):?>
                                <span class="error-fld" title="<?=htmlspecialcharsbx($arResult["FORM_ERRORS"][$FIELD_SID])?>"></span>
                            <?endif;?>
                            <?=$arQuestion["CAPTION"]?><?if ($arQuestion["REQUIRED"] == "Y"):?><?=$arResult["REQUIRED_SIGN"];?><?endif;?>
                        </label>
                        <?=$arQuestion["HTML_CODE"]?>
               </div>
			<?}
		else{
			?>
			<div class="form-group">
              <label class="labelblock <?=($FIELD_SID == "SIMPLE_QUESTION_377" || $FIELD_SID == "SIMPLE_QUESTION_417") ? "small" : ""?>"  for="<?=$FIELD_SID?>">
                            <?if (is_array($arResult["FORM_ERRORS"]) && array_key_exists($FIELD_SID, $arResult['FORM_ERRORS'])):?>
                                <span class="error-fld" title="<?=htmlspecialcharsbx($arResult["FORM_ERRORS"][$FIELD_SID])?>"></span>
                            <?endif;?>
                            <?=$arQuestion["CAPTION"]?><?if ($arQuestion["REQUIRED"] == "Y"):?><?=$arResult["REQUIRED_SIGN"];?><?endif;?>
              </label>
                        <?=$arQuestion["HTML_CODE"]?>
               </div>
			<?
			}?>

                        
        <?
			if($i == 8){
		?>	
              <div class="but col-md-2">
              <button type="button" class="btn btn-info del_b">-</button>
      		<button type="button" class="btn btn-info add_b">+</button>
              </div> 
			</div><?
			}
        $i++;
	}
?>
<div class="form-groupt">
                        <input <?=(intval($arResult["F_RIGHT"]) < 10 ? "disabled=\"disabled\"" : "");?> type="submit" name="web_form_submit" value="<?=GetMessage("FORM_CONFIRM");?>" />
                        <?if ($arResult["F_RIGHT"] >= 15):?>
                        <input type="hidden" name="web_form_apply" value="Y" />
                        <?endif;?>
</div>
</div>
</div>
<?=$arResult["FORM_FOOTER"]?>
<?
} //endif (isFormNote)?>

<script>
  	$(document ).ready(function() {
      
      $('.formblock').on('click', '.add_b', function(e) {
      
  e.preventDefault();
  let $parent = $(this).parent().parent();
	
  let $clone = $parent.clone();
      
  $parent.after($clone);
  $clone.find('input').val('').focus();
});


$('.formblock').on('click', '.del_b', function(e) {
  e.preventDefault();

  let $parent = $(this).parent().parent();
	
 
  if ($('.row.block').length > 1) {
    $parent.remove();
  }
});
	});
 
</script>