<?
/**
 * MysqliDb Class
 * @category Database library
 * @author junhyung Lee <dksms1@naver.com>
 * @version 1.0.0 ver
 */

class MysqliDb
{

    public function get($tableName, $where)
    {
        global $pdo, $my_db;
        
        $SQL="SELECT * FROM {$my_db}.{$tableName} WHERE {$where}";
        $stmt=$pdo->prepare($SQL);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function insert($tableName, $data)
    {
        global $pdo, $my_db;
        
        $k=implode(",",array_keys($data));
        $v=implode("','",array_values($data));
        $SQL="INSERT INTO {$my_db}.{$tableName}({$k}) VALUES('{$v}')";
        $stmt=$pdo->prepare($SQL);
        return $stmt->execute();
    }

    public function update($tableName, $data, $where)
    {
        global $pdo, $my_db;
        
        $val=array();
        foreach($data as $k=>$v)
        {
            $str="{$k}='{$v}'";
            array_push($val,$str);
        }
        $v=implode(",",$val);
        $SQL="UPDATE {$my_db}.{$tableName} SET {$v} WHERE {$where}";
        $stmt=$pdo->prepare($SQL);
        return $stmt->execute();
    }


    /**
     * @throws Exception
     */

     public function getInsertError()
     {
        // $this->insert();
     }

}

try
{
    $mdb=new MysqliDb();
}
catch(Exception $e)
{
    die("오류 : ".$e->getMessage());
    exit;
}
?>
