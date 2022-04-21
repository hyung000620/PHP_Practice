<?
class Login
{
    private $naver_secret ="wHJEK2bRFQ";
    public function __construct($naver_api_key,$kakao_api_key,$redirect_url)
    {
        $this->naver_api_key=$naver_api_key;
        $this->kakao_api_key=$kakao_api_key;
        $this->redirect_url=urlencode($redirect_url);
    }
    public function setting($naver_secret)
    {
        $this->naver_secret=$naver_secret;
    }
    public function getLoginUrl($social)
    {
        $social=strtolower($social);
        switch($social)
        {
            case "naver":
            {
                $state= mt_rand(1000,9999);
                $apiURL="https://nid.naver.com/oauth2.0/authorize?response_type=code&client_id=";
                $apiURL.=$this->naver_api_key."&redirect_uri=".$this->redirect_url."&state=".$state;
                return $apiURL;
            }break;
            case "kakao":
            {
                $apiURL="https://kauth.kakao.com/oauth/authorize?client_id=";
                $apiURL.=$this->kakao_api_key."&redirect_uri=".$this->redirect_url."&response_type=code";
                return $apiURL;
            }break;
        }
    }
    
    public function getCurlInfo($code, $state=null)
    {   
        $url="";
        $getProfileUrl="";
        if($state==null)
        {
            $url.="https://kauth.kakao.com/oauth/token?grant_type=authorization_code&client_id=";
            $url.=$this->kakao_api_key."&redirect_uri=".$this->redirect_url."&code=".$code;
            $getProfileUrl.="https://kapi.kakao.com/v2/user/me";
        }
        else
        {
            $url.="https://nid.naver.com/oauth2.0/token?grant_type=authorization_code&client_id=";
            $url.=$this->naver_api_key."&client_secret=".$this->naver_secret."&redirect_uri=".$this->redirect_url;
            $url.="&code=".$code."&state=".$state;
            $getProfileUrl="https://openapi.naver.com/v1/nid/me";
        }

        $isPost = false;
        $ch = curl_init();                                    
        curl_setopt($ch, CURLOPT_URL, $url);          
        curl_setopt($ch, CURLOPT_POST, $isPost);              
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);      
        $headers = array();                                  
        $response = curl_exec ($ch);                               
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
        curl_close ($ch);
        
        if($status_code==200)
        {
            $response=json_decode($response,true);
            $headers = array('Content-Type: application/json', sprintf('Authorization: Bearer %s',$response['access_token']));
            $is_post = false;
            $ch= curl_init();
            curl_setopt($ch, CURLOPT_URL, $getProfileUrl); 
            curl_setopt($ch, CURLOPT_POST, $is_post ); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            $response = curl_exec ($ch); 
            curl_close ($ch);
            
            return json_decode($response,true);
        }
    }
}
try
{
    $login=new Login("PoskWV9GbR_omejAmIRx","2168332007ec077a155573867ada4195","https://kb.tankauction.com/kko/callback.php");
}
catch(Exception $e){

}
?>