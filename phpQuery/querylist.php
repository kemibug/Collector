<?php

/**
 *ͨ���б�ɼ���
 *�汾V1.3
 *����:SK
 *����:http://blog.superkemi.com
 */
require_once 'phpQuery-onefile.php';
class QueryList{

    private $pageURL;
    private $regArr = array();
    public $jsonArr = array();
    private $regRange;
    private $html;
    private $website;
    /************************************************
     * ����: ҳ���ַ ѡ�������� ��ѡ����
     * ��ѡ�������顿˵������ʽarray("����"=>array("ѡ����","����"),.......)
     * �����͡�˵����ֵ "text" ,"html" ,"����"
     *����ѡ��������ָ �Ȱ��չ��� ѡ�� ������� ��Ȼ���ٷֱ����ڿ����� ������ص�ѡ��
     * regRange ��Ҫ��ѯ�ķ�Χ
     *************************************************/
    function QueryList($pageURL,$regArr=array(),$regRange='',$website='')
    {
        $this->pageURL = $pageURL;

        //Ϊ���ܻ�ȡhttps://
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$this->pageURL);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $this->html = curl_exec($ch);
        curl_close($ch);

        if(!empty($regArr))
        {

            $this->regArr = $regArr;
            $this->regRange = $regRange;
            $this->website = $website;
            $this->getList();
        }

    }
    function setQuery($regArr,$regRange='')
    {
        $this->jsonArr=array();
        $this->regArr = $regArr;
        $this->regRange = $regRange;
        $this->getList();
    }
    private function getList()
    {

        $hobj = phpQuery::newDocumentHTML($this->html);
        if(!empty($this->regRange))
        {
            $robj = pq($hobj)->find($this->regRange);

            $i=0;
            foreach($robj as $item)
            {

                while(list($key,$reg_value)=each($this->regArr))
                {
                    $iobj = pq($item)->find($reg_value[0]);

                    switch($reg_value[1])
                    {
                        case 'text':
                            $this->jsonArr[$i][$key] = trim(pq($iobj)->text());
                            break;
                        case 'html':
                            $this->jsonArr[$i][$key] = trim(pq($iobj)->html());
                            break;
                        case 'href':
                            $this->jsonArr[$i][$key] = $this->website . pq($iobj)->attr($reg_value[1]);
                            break;
                        default:
                            $this->jsonArr[$i][$key] = pq($iobj)->attr($reg_value[1]);
                            break;
                    }
                }
                //��������ָ��
                reset($this->regArr);
                $i++;
            }
        }
        else
        {
            while(list($key,$reg_value)=each($this->regArr))
            {
                $lobj = pq($hobj)->find($reg_value[0]);
                $i=0;
                foreach($lobj as $item)
                {
                    switch($reg_value[1])
                    {
                        case 'text':
                            $this->jsonArr[$i++][$key] = trim(pq($item)->text());
                            break;
                        case 'html':
                            $this->jsonArr[$i++][$key] = trim(pq($item)->html());
                            break;
                        case 'href':
                            $this->jsonArr[$i++][$key] = $this->website . pq($item)->attr($reg_value[1]);
                            break;
                        default:
                            $this->jsonArr[$i++][$key] = pq($item)->attr($reg_value[1]);
                            break;
                    }
                }
            }
        }
    }
    function getJSON()
    {
        return json_encode($this->jsonArr);
    }

}