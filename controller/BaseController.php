<?php
use ReallySimpleJWT\Token;
use ReallySimpleJWT\Exception\ValidateException;
use Symfony\Component\Debug\Exception\DummyException;

class BaseController
{
    public function validateToken()
    {
        global $token, $secret;

        $secret = MY_API_SECRET;

        $result = Token::validate($token, $secret);

        if(!$result)
        {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }

        try{
            $user_id = Token::getPayload($token, $secret)['user_id'];
        }
        catch (ValidateException $e) {
            echo 'Excepción capturada: ',  $e->getMessage(), "\n";
            echo 'Code: ',  $e->getCode(), "\n";
            exit;
        }

        return $user_id;
    }

    function getUserFromToken()
    {
        global $token, $secret;

        $secret = MY_API_SECRET;

        $result = Token::validate($token, $secret);

        if(!$result)
        {
            return 0;
        }

        try{
            $user_id = Token::getPayload($token, $secret)['user_id'];
        }
        catch (ValidateException $e) {
            echo 'Excepción capturada: ',  $e->getMessage(), "\n";
            echo 'Code: ',  $e->getCode(), "\n";
            exit;
        }

        return $user_id;
    }
}
?>