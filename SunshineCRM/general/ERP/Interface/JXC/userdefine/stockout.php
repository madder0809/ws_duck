<?php
/*
版权归属:郑州单点科技软件有限公司;
联系方式:0371-69663266;
公司地址:河南郑州经济技术开发区第五大街经北三路通信产业园四楼西南;
公司简介:郑州单点科技软件有限公司位于中国中部城市-郑州,成立于2007年1月,致力于把基于先进信息技术（包括通信技术）的最佳管理与业务实践普及到教育行业客户的管理与业务创新活动中，全面提供具有自主知识产权的教育管理软件、服务与解决方案，是中部最优秀的高校教育管理软件及中小学校管理软件提供商。目前己经有多家高职和中职类院校使用通达中部研发中心开发的软件和服务;

软件名称:单点科技软件开发基础性架构平台,以及在其基础之上扩展的任何性软件作品;
发行协议:数字化校园产品为商业软件,发行许可为LICENSE方式;单点CRM系统即SunshineCRM系统为GPLV3协议许可,GPLV3协议许可内容请到百度搜索;
特殊声明:软件所使用的ADODB库,PHPEXCEL库,SMTARY库归原作者所有,余下代码沿用上述声明;
*/

function stockout_value( $fieldvalue, $fields, $i )
{
				if($fieldvalue=="未出库")
					$fieldvalue="<font color=red>未出库</font>";
				else if  ($fieldvalue=="已出库")
					$fieldvalue="<font color=blue>已出库</font>";
				else if  ($fieldvalue=="已发货")
					$fieldvalue="<font color=green>已发货</font>";
				return $fieldvalue;
}

function stockout_value_PRIV( $fieldvalue, $fields, $i )
{
				global $db;
				$storeid = returntablefield( "stockoutmain", "billid", $fields['value'][$i]['billid'], "storeid" );
				$userid = returntablefield( "stock", "rowid", $storeid, "user_id" );
				$useridArray=explode(",", $userid);
				$SYSTEM_STOP_ROW['shenhe_priv'] = 1;
				$SYSTEM_STOP_ROW['flow_priv'] = 1;
				$SYSTEM_STOP_ROW['delete_priv'] = 1;
				switch ( $fieldvalue )
				{
				case "未出库" :
								if(in_array($_SESSION['LOGIN_USER_ID'], $useridArray))
								{
									$SYSTEM_STOP_ROW['shenhe_priv'] = 0;
									$SYSTEM_STOP_ROW['delete_priv'] = 0;
								}
								break;
				case "已出库" :
								if(in_array($_SESSION['LOGIN_USER_ID'], $useridArray))
								{
									$SYSTEM_STOP_ROW['delete_priv'] = 0;
									$SYSTEM_STOP_ROW['flow_priv']=0;
									if($fields['value'][$i]['outtype']=='销售出库')
									{
										$dingdanbillid=returntablefield("stockoutmain", "billid", $fields['value'][$i]['billid'], "dingdanbillid");
										$fahuostate=returntablefield("sellplanmain", "billid", $dingdanbillid, "fahuostate");
										
										if($fahuostate==-1)
										{
											$SYSTEM_STOP_ROW['flow_priv']=1;
										}
										
									}
									if($fields['value'][$i]['outtype']=='积分兑换出库' )
									{
										$SYSTEM_STOP_ROW['delete_priv'] = 1;
										$SYSTEM_STOP_ROW['flow_priv']=1;
									}
								}
								break;
				}
				
				return $SYSTEM_STOP_ROW;
}

?>
