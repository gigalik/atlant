<?php
class Telegram {
  private $token = null;
  private $chat_id = null;
  private $host = null;

	public function __construct($data=array()){
    if(isset($data['host'])) $this->host = $data['host'];
    if(isset($data['token'])) $this->token = $data['token'];
		if(isset($data['chat_id'])) $this->chat_id = $data['chat_id'];
  }

  private function __clone(){ /* ... @return ClassName */}  // We protect from creation through "cloning"
  private function __wakeup(){ /* ... @return ClassName */}  // We protect from creation through "unserialize"

  public function storeFormValues($params){
		$this->__construct( $params );
  }

  public static function getUpdates($token)
	{
		return file_get_contents('https://api.telegram.org/bot'. $token .'/getUpdates');
	}

  public function post($data)
  {
    $query = http_build_query([
      'chat_id' => $this->chat_id,
      'parse_mode' => 'HTML',
      'text' => $data['text'],
    ]);

    $tbot = file_get_contents($this->host . $this->token . '/sendMessage?' . $query);

    // return $tbot;
  }

  public function sendLocation($data)
  {
    $query = http_build_query([
      'chat_id' => $this->chat_id,
      'longitude' => $data['lon'],
      'latitude' => $data['lat'],
    ]);

    $tbot = file_get_contents($this->host . $this->token . '/sendLocation?' . $query);

    // return $tbot;
  }

  public function InputMediaVideo($data)
  {
    $query = http_build_query([
      'chat_id' => $this->chat_id,
      'type' => 'video',
      'media' => $data,
    ]);

    $tbot = file_get_contents($this->host . $this->token . '/InputMediaVideo?' . $query);

    // return $tbot;
  }
}
