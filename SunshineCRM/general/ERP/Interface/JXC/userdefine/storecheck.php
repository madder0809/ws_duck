<?php
/*
��Ȩ����:֣�ݵ���Ƽ�������޹�˾;
��ϵ��ʽ:0371-69663266;
��˾��ַ:����֣�ݾ��ü��������������־�����·ͨ�Ų�ҵ԰��¥����;
��˾���:֣�ݵ���Ƽ�������޹�˾λ���й��в�����-֣��,������2007��1��,�����ڰѻ����Ƚ���Ϣ����������ͨ�ż���������ѹ�����ҵ��ʵ���ռ���������ҵ�ͻ��Ĺ�����ҵ���»�У�ȫ���ṩ��������֪ʶ��Ȩ�Ľ�����������������������������в�������ĸ�У���������������СѧУ��������ṩ�̡�Ŀǰ�����ж�Ҹ�ְ����ְ��ԺУʹ��ͨ���в��з����Ŀ���������ͷ���;

�������:����Ƽ�������������Լܹ�ƽ̨,�Լ��������֮����չ���κ��������Ʒ;
����Э��:���ֻ�У԰��ƷΪ��ҵ���,�������ΪLICENSE��ʽ;����CRMϵͳ��SunshineCRMϵͳΪGPLV3Э�����,GPLV3Э����������뵽�ٶ�����;
��������:�����ʹ�õ�ADODB��,PHPEXCEL��,SMTARY���ԭ��������,���´���������������;
*/

function storecheck_value( $fieldvalue, $fields, $i )
{
				if($fieldvalue=="�̵���")
					$fieldvalue="<font color=red>�̵���</font>";
				else if  ($fieldvalue=="�̵����")
					$fieldvalue="<font color=green>�̵����</font>";
				return $fieldvalue;
}

function storecheck_value_PRIV( $fieldvalue, $fields, $i )
{
				global $db;
				$storeid = returntablefield( "storecheck", "billid", $fields['value'][$i]['billid'], "storeid" );
				
				$userid = returntablefield( "stock", "rowid", $storeid, "user_id" );
				$useridArray=explode(",", $userid);
				$SYSTEM_STOP_ROW['flow_priv'] = 1;
				$SYSTEM_STOP_ROW['delete_priv'] = 1;
				$SYSTEM_STOP_ROW['edit_priv'] = 1;
				switch ( $fieldvalue )
				{
				case "�̵���" :
								$color = "red";
								if(in_array($_SESSION['LOGIN_USER_ID'], $useridArray))
								{
									$SYSTEM_STOP_ROW['flow_priv'] = 0;
									$SYSTEM_STOP_ROW['edit_priv'] = 0;
									$SYSTEM_STOP_ROW['delete_priv'] = 0;
								}
								break;
				case "�̵����" :
								$color = "green";
								if(in_array($_SESSION['LOGIN_USER_ID'], $useridArray))
								{
									$SYSTEM_STOP_ROW['delete_priv'] = 0;
								}
								
				}
				return $SYSTEM_STOP_ROW;
}

?>
