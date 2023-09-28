<?php

class JWTCodec
{
  public function encode(array $payload): string
  {
    $header = json_encode([
      "typ" => "JWT",
      "alg" => "HS256"
    ]);
    $header = $this->base64urlEncode($header);

    $payload = json_encode($payload);
    $payload = $this->base64urlEncode($payload);

    $signature = hash_hmac("sha256",
      $header . "." 
      . $payload 
      . "." 
      . "b56f280890d88f1c89ffff4edb31ce2c1c9deca95f7aead1825eb6e58e2c25df", true);

    $signature = $this->base64urlEncode($signature);

    return $header . "." . $payload . "." . $signature;
  }

  public function decode(string $token): string
  {
    if (preg_match("/^(?<header>.+)\.(?<payload>.+)\.(?<signature>.+)$/", 
          $token, 
          $matches)) {
      throw new InvalidArgumentException("invalid token format");
    }

    $signature = hash_hmac("sha256",
      $matches["header"] . "." 
      . $matches["payload"] 
      . "." 
      . "b56f280890d88f1c89ffff4edb31ce2c1c9deca95f7aead1825eb6e58e2c25df", true);

    $signature_from_token = $this->base64urlDecode($matches["signature"]);

    if (!hash_equals($signature, $signature_from_token)) {
      throw new Exception("signature doesn't match");
    }

    $payload = json_decode($this->base64urlDecode($matches["payload"], true));

    return $payload;
  }

  private function base64urlEncode(string $text): string
  {
    return str_replace(
      ["+", "/", "="], 
      ["-". "_", "", ],
      base64_encode($text));
  }

  private function base64urlDecode(string $text): string
  {
    return base64_decode(str_replace(
      ["-", "_"],
      ["+", "/"],
      $text));
  }
}