
<?php
class logService {
	private $ip;
	private $time;
	private $site;
	private $year;
	private $month;
	private $date;
	private $hour;
	private $minute;
	private $day;
	private $doc;
	private $timeThreshold=5;
	/**
	 * 建構子會取得使用者的IP位置，並儲存至$ip中
	 */
	public function __construct() {
		// 取得連線端的IP位置
		if (! empty ( $_SERVER ['HTTP_CLIENT_IP'] )) {
			$this->ip = $_SERVER ['HTTP_CLIENT_IP'];
			echo "if";
		} elseif (! empty ( $_SERVER ['HTTP_X_FORWARDED_FOR'] )) {
			$this->ip = $_SERVER ['HTTP_X_FORWARDED_FOR'];
			echo "elseif";
		} else {
			$this->ip = $_SERVER ['REMOTE_ADDR'];
			echo "else<br>";
		}
		echo $this->ip;
	}
	/**
	 * 設定使用者目前所在的頁面
	 * */
	public function setsite($site) {
		$this->site = $site;
	}
	public function getsite() {
		return $this->site;
	}
	/**將使用者 的時間資訊諸存在對應的var中
	 * */
	public function setTime() {
		$date = date ( 'Y/m/d H:i', time () );
		$pattern = "#[ :/]#";
		$timeArray = preg_split ( $pattern, $date );
		$this->year = $timeArray [0];
		$this->month = $timeArray [1];
		$this->day = $timeArray [2];
		$this->hour = $timeArray [3];
		$this->minute = $timeArray [4];
	}
	/**
	 * 取得使用者連上頁面的時間
	 */
	public function getTime() {
		return $this->time;
	}
	
	/**
	 * 將本次瀏覽記錄在XML中
	 */
	public function record() {
		$dir=".";
		$file=scandir($dir);
		print_r($file);
		$fileName="$this->year$this->month.xml";
		$ifLogExist=array_search($fileName, $file);
		echo "./$fileName\n";
		if($ifLogExist){
			//load
			$this->doc = new DOMDocument();
			$this->doc->load($fileName);
			
			
			echo $this->doc->saveXML();
		}
		else{
			//create
		}
	}
	public function getRecentUserList(){
		;
	}
	public function getUserListbyMonthy($month ){
		;
	}
	
}



$date = date ( 'Y/m/d H:i', time () );
$pattern = "#[ :/]#";
$timeArray = preg_split ( $pattern, $date );
$a = preg_split ( $pattern, $date );
//print_r ( $a );
$a = basename ( $_SERVER ['PHP_SELF']);
$logService = new logService ();
$logService->setTime();
$logService->record();
?>