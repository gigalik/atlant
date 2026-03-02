<?php
class Uptime
{
  public $url = 'https://uptime.support/api';
  private $token = null;

  public function __construct( $data=array() ) {
    if (isset($data['token'])) $this->token = $data['token'];
  }

  private function __clone(){ /* ... @return ClassName */}  // We protect from creation through "cloning"
  private function __wakeup(){ /* ... @return ClassName */}  // We protect from creation through "unserialize"

  public function storeFormValues($params)
  {
    $this->__construct($params);
  }

  public function query($action, $data, $user_data)
  {
    $arData = [
      'token' => $this->token,
      'params' => json_encode($data),
      'user' => json_encode($user_data)
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->url . '/' . $action);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $arData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
  }
}