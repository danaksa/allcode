<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
?>
<div class="container-my">
<div class="search-page">
	<?if($arParams["SHOW_TAGS_CLOUD"] == "Y")
	{
		$arCloudParams = Array(
			"SEARCH" => $arResult["REQUEST"]["~QUERY"],
			"TAGS" => $arResult["REQUEST"]["~TAGS"],
			"CHECK_DATES" => $arParams["CHECK_DATES"],
			"arrFILTER" => $arParams["arrFILTER"],
			"SORT" => $arParams["TAGS_SORT"],
			"PAGE_ELEMENTS" => $arParams["TAGS_PAGE_ELEMENTS"],
			"PERIOD" => $arParams["TAGS_PERIOD"],
			"URL_SEARCH" => $arParams["TAGS_URL_SEARCH"],
			"TAGS_INHERIT" => $arParams["TAGS_INHERIT"],
			"FONT_MAX" => $arParams["FONT_MAX"],
			"FONT_MIN" => $arParams["FONT_MIN"],
			"COLOR_NEW" => $arParams["COLOR_NEW"],
			"COLOR_OLD" => $arParams["COLOR_OLD"],
			"PERIOD_NEW_TAGS" => $arParams["PERIOD_NEW_TAGS"],
			"SHOW_CHAIN" => "N",
			"COLOR_TYPE" => $arParams["COLOR_TYPE"],
			"WIDTH" => $arParams["WIDTH"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"RESTART" => $arParams["RESTART"],
		);

		if(is_array($arCloudParams["arrFILTER"]))
		{
			foreach($arCloudParams["arrFILTER"] as $strFILTER)
			{
				if($strFILTER=="main")
				{
					$arCloudParams["arrFILTER_main"] = $arParams["arrFILTER_main"];
				}
				elseif($strFILTER=="forum" && IsModuleInstalled("forum"))
				{
					$arCloudParams["arrFILTER_forum"] = $arParams["arrFILTER_forum"];
				}
				elseif(mb_strpos($strFILTER,"iblock_")===0)
				{
					foreach($arParams["arrFILTER_".$strFILTER] as $strIBlock)
						$arCloudParams["arrFILTER_".$strFILTER] = $arParams["arrFILTER_".$strFILTER];
				}
				elseif($strFILTER=="blog")
				{
					$arCloudParams["arrFILTER_blog"] = $arParams["arrFILTER_blog"];
				}
				elseif($strFILTER=="socialnetwork")
				{
					$arCloudParams["arrFILTER_socialnetwork"] = $arParams["arrFILTER_socialnetwork"];
				}
			}
		}
		$APPLICATION->IncludeComponent("bitrix:search.tags.cloud", ".default", $arCloudParams, $component, array("HIDE_ICONS" => "Y"));
	}
	?>

	<form action="" method="get">
        <div class="row searchjob">
            <noindex>
                <input type="hidden" name="tags" value="<?echo $arResult["REQUEST"]["TAGS"]?>" />
                <input type="hidden" name="how" value="<?echo $arResult["REQUEST"]["HOW"]=="d"? "d": "r"?>" />

                <div class="col-md-10 col-sm-10 float-left">
                    <input class="search-query" type="text" name="q" placeholder="<?=GetMessage("BSF_T_SEARCH_BUTTON");?>" size="50" maxlength="100"value="<?=$arResult["REQUEST"]["QUERY"]?>" />
                </div>
                <div class="col-md-2 col-sm-2 float-left">
                    <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
                </div>
		    </noindex>
        </div>
		<div class="search-advanced">
			<div class="search-advanced-result">
				<?if(is_object($arResult["NAV_RESULT"]) && $arResult["NAV_RESULT"]->SelectedRowsCount() > 0):?>
					<div class="search-result"><?echo GetMessage("CT_BSP_FOUND")?>: <?echo $arResult["NAV_RESULT"]->SelectedRowsCount()?></div>
				<?endif;?>
				<?
				$arWhere = array();

				if(!empty($arResult["TAGS_CHAIN"]))
				{
					$tags_chain = '';
					foreach($arResult["TAGS_CHAIN"] as $arTag)
					{
						$tags_chain .= ' '.$arTag["TAG_NAME"].' [<a href="'.$arTag["TAG_WITHOUT"].'" class="search-tags-link" rel="nofollow">x</a>]';
					}

					$arWhere[] = GetMessage("CT_BSP_TAGS").' &mdash; '.$tags_chain;
				}

				if($arParams["SHOW_WHERE"])
				{
					$where = GetMessage("CT_BSP_EVERYWHERE");
					foreach($arResult["DROPDOWN"] as $key=>$value)
						if($arResult["REQUEST"]["WHERE"]==$key)
							$where = $value;

					$arWhere[] = GetMessage("CT_BSP_WHERE").' &mdash; '.$where;
				}

				if($arParams["SHOW_WHEN"])
				{
					if($arResult["REQUEST"]["FROM"] && $arResult["REQUEST"]["TO"])
						$when = GetMessage("CT_BSP_DATES_FROM_TO", array("#FROM#" => $arResult["REQUEST"]["FROM"], "#TO#" => $arResult["REQUEST"]["TO"]));
					elseif($arResult["REQUEST"]["FROM"])
						$when = GetMessage("CT_BSP_DATES_FROM", array("#FROM#" => $arResult["REQUEST"]["FROM"]));
					elseif($arResult["REQUEST"]["TO"])
						$when = GetMessage("CT_BSP_DATES_TO", array("#TO#" => $arResult["REQUEST"]["TO"]));
					else
						$when = GetMessage("CT_BSP_DATES_ALL");

					$arWhere[] = GetMessage("CT_BSP_WHEN").' &mdash; '.$when;
				}

				if(count($arWhere))
					echo GetMessage("CT_BSP_WHERE_LABEL"),': ',implode(", ", $arWhere);
				?>
			</div><?//div class="search-advanced-result"?>
			<?if($arParams["SHOW_WHERE"] || $arParams["SHOW_WHEN"]):?>
				<script>
				function switch_search_params()
				{
					var sp = document.getElementById('search_params');
					if(sp.style.display == 'none')
					{
						disable_search_input(sp, false);
						sp.style.display = 'block'
					}
					else
					{
						disable_search_input(sp, true);
						sp.style.display = 'none';
					}
					return false;
				}

				function disable_search_input(obj, flag)
				{
					var n = obj.childNodes.length;
					for(var j=0; j<n; j++)
					{
						var child = obj.childNodes[j];
						if(child.type)
						{
							switch(child.type.toLowerCase())
							{
								case 'select-one':
								case 'file':
								case 'text':
								case 'textarea':
								case 'hidden':
								case 'radio':
								case 'checkbox':
								case 'select-multiple':
									child.disabled = flag;
									break;
								default:
									break;
							}
						}
						disable_search_input(child, flag);
					}
				}
				</script>
				<div class="search-advanced-filter"><a href="#" onclick="return switch_search_params()"><?echo GetMessage('CT_BSP_ADVANCED_SEARCH')?></a></div>
		</div><?//div class="search-advanced"?>
				<div id="search_params" class="search-filter" style="display:<?echo $arResult["REQUEST"]["FROM"] || $arResult["REQUEST"]["TO"] || $arResult["REQUEST"]["WHERE"]? 'block': 'none'?>">
					<h2><?echo GetMessage('CT_BSP_ADVANCED_SEARCH')?></h2>
					<table class="search-filter" cellspacing="0"><tbody>
						<?if($arParams["SHOW_WHERE"]):?>
						<tr>
							<td class="search-filter-name"><?echo GetMessage("CT_BSP_WHERE")?></td>
							<td class="search-filter-field">
								<select class="select-field" name="where">
									<option value=""><?=GetMessage("CT_BSP_ALL")?></option>
									<?foreach($arResult["DROPDOWN"] as $key=>$value):?>
										<option value="<?=$key?>"<?if($arResult["REQUEST"]["WHERE"]==$key) echo " selected"?>><?=$value?></option>
									<?endforeach?>
								</select>
							</td>
						</tr>
						<?endif;?>
						<?if($arParams["SHOW_WHEN"]):?>
						<tr>
							<td class="search-filter-name"><?echo GetMessage("CT_BSP_WHEN")?></td>
							<td class="search-filter-field">
								<?$APPLICATION->IncludeComponent(
									'bitrix:main.calendar',
									'',
									array(
										'SHOW_INPUT' => 'Y',
										'INPUT_NAME' => 'from',
										'INPUT_VALUE' => $arResult["REQUEST"]["~FROM"],
										'INPUT_NAME_FINISH' => 'to',
										'INPUT_VALUE_FINISH' =>$arResult["REQUEST"]["~TO"],
										'INPUT_ADDITIONAL_ATTR' => 'class="input-field" size="10"',
									),
									null,
									array('HIDE_ICONS' => 'Y')
								);?>
							</td>
						</tr>
						<?endif;?>
						<tr>
							<td class="search-filter-name">&nbsp;</td>
							<td class="search-filter-field"><input class="search-button" value="<?echo GetMessage("CT_BSP_GO")?>" type="submit"></td>
						</tr>
					</tbody></table>
				</div>
			<?else:?>
		</div><?//div class="search-advanced"?>
			<?endif;//if($arParams["SHOW_WHERE"] || $arParams["SHOW_WHEN"])?>
		</noindex>
	</form>

<?
unset($arResult["REQUEST"]["ORIGINAL_QUERY"]);//не вывожу доп надписи
if(isset($arResult["REQUEST"]["ORIGINAL_QUERY"])):
	?>
	<div class="search-language-guess">
		<?echo GetMessage("CT_BSP_KEYBOARD_WARNING", array("#query#"=>'<a href="'.$arResult["ORIGINAL_QUERY_URL"].'">'.$arResult["REQUEST"]["ORIGINAL_QUERY"].'</a>'))?>
	</div><br /><?
endif;?>

	<div class="search-result">

	<?if($arResult["REQUEST"]["QUERY"] === false && $arResult["REQUEST"]["TAGS"] === false && $arResult["ERROR_CODE"] == 0):?>
	<?elseif(count($arResult["SEARCH"])>0):?>
		<?if($arParams["DISPLAY_TOP_PAGER"] != "N") echo $arResult["NAV_STRING"]?>
		<?foreach($arResult["SEARCH"] as $arItem):
            $properties = [];
            $fields = [];
            $res = CIBlockElement::GetList(Array(), Array("IBLOCK_ID"=>43, "ID"=>$arItem['ITEM_ID']));
            if ($ob = $res->GetNextElement()){

                //$fields = $ob->GetFields();
                $properties = $ob->GetProperties();
                if(LANGUAGE_ID == "ru"){
                    $category = $properties["CATEGORY"]["VALUE"];
                    $area = $properties["AREA"]["VALUE"];
                }
                if(LANGUAGE_ID == "en"){
                    $category = $properties["CATEGORY_EN"]["VALUE"];
                    $area = $properties["AREA_EN"]["VALUE"];
                    //var_dump($category);
                }

                $active = explode(' ',$arItem["FULL_DATE_CHANGE"])[0];
            }

            ?>
        <div class="jobs">
            <div class="jobs-block">
			<div class="row search-item">
                <div class="company-logo col-md-1 col-sm-2 hidden-xs">
                    <img src="/images/logo_jobs.png">
                </div>
                <div class="jobtitle col-md-9 col-sm-9">
                    <h4><a href="<?echo $arItem["URL_WO_PARAMS"]?>"><?
                            if(LANGUAGE_ID == "ru"){
                                echo $arItem["TITLE"];
                            }else{
                                echo $properties["NAME_EN"]["VALUE"];
                            }
                            ?></a></h4>
                </div>
                <div class="properties row">
										<div class="property city col-md-4 col-sm-4 col-xs-12">
											<span class="value font_upper col-7">
											<i class="fa fa-list-alt" aria-hidden="true"></i><?=GetMessage("CATEGORY")?>:<br>
											</span>
											<span class="value value_text">
												<?echo $category?>									</span>
			</div>
                    <div class="property quality col-md-4 col-sm-4 col-xs-12">
											<span class="value font_upper col-7">
											<i class="fa fa-check-square-o" aria-hidden="true"></i><?=GetMessage("PROFAREA")?>:<br>
											</span>
											<span class="value value_text">
												<?echo $area?>
											</span>
			</div>
                    <div class="property work_type col-md-4 col-sm-4 col-xs-12">
											<span class="value font_upper col-7">
											<i class="fa fa-calendar" aria-hidden="true"></i><?=GetMessage("DATEPUBLIC")?>:<br>
											</span>
											<span class="value value_text">
												<?echo $active?>
										    </span>
			        </div>
                </div>

                <div class="row marginjob">
                    <div class="col-xs-5">
                        <?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
                            <div class="bx-newslist-more"><a class="btn btn-primary" href="<?echo $arItem["URL_WO_PARAMS"]?>"><?echo GetMessage("CT_BNL_GOTO_DETAIL")?></a></div>
                        <?endif;?>
                    </div>
                </div>
			</div>
            </div>
        </div>
		<?endforeach;?>
		<?if($arParams["DISPLAY_BOTTOM_PAGER"] != "N") echo $arResult["NAV_STRING"];?>

	<?else:?>
        <div class="row no-job">
            <!-- start Jobs Listing: List View -->
            <div class="no-job-listening">
                <img src="/images/not-found.png" alt="No jobs found" id="not-found-v1">
                <p class="no-job-listing-text">No jobs found</p>
                <p><a href="/vacancy/" class="btn btn-primary"><?=GetMessage("NO_JOBS_FOUND")?></a></p>
            </div> <!-- end Jobs Listing: List View -->
        </div>
	<?endif;?>

    </div>
</div>
</div>