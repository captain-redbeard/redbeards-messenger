<?php
/**
 * @author captain-redbeard
 * @since 06/12/16
 */
namespace Redbeard\Controllers;

use Redbeard\Core\Database;
use Endroid\QrCode\QrCode;

class Mfa extends Controller
{
    public function __construct()
    {
        $this->requiresLogin();
    }
    
    public function enable($error = '')
    {
        $user = $this->getSecretKey();
        $qrCode = $this->getQrCode($user);
        
        $this->view(
            ['enable-mfa'],
            [
                'page' => 'enable-mfa',
                'page_title' => 'Enable MFA - ' . SITE_NAME,
                'qr_code' => $qrCode,
                'secret_key' => $user[0]['secret_key'],
                'token' => $_SESSION['token'],
                'error' => $error !== '' ? $this->getErrorMessage($error) : $error
            ]
        );
    }
    
    public function disable()
    {
        $_SESSION[USESSION]->disableMfa();
        $this->redirect('settings');
    }
    
    public function activate($parameters)
    {
        $error = $this->activateMfa($parameters);
        
        if ($error === 0) {
            $this->redirect('settings');
        } else {
            $this->enable($error);
        }
    }
    
    private function activateMfa($parameters)
    {
        if ($this->checkToken()) {
            return $_SESSION[USESSION]->enableMfa(
                $parameters['code1'],
                $parameters['code2']
            );
        } else {
            return -1;
        }
    }
    
    private function getSecretKey()
    {
        return Database::select(
            "SELECT secret_key, mfa_enabled FROM users WHERE user_guid = ?;",
            [$_SESSION[USESSION]->user_guid]
        );
    }
    
    private function getQrCode($user)
    {
        $qrCode = new QrCode();
        $qrCode
            ->setText("otpauth://totp/" .
                      SITE_NAME . ":" .
                      $_SESSION[USESSION]->username . "?secret=" .
                      $user[0]['secret_key'] . "&issuer=" .
                      SITE_NAME)
            ->setSize(200)
            ->setPadding(0)
            ->setErrorCorrection('high')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setImageType(QrCode::IMAGE_TYPE_PNG)
        ;
        
        return $qrCode;
    }
    
    private function getErrorMessage($code)
    {
        switch ($code) {
            case -1:
                return "Invalid token.";
            case 1:
                return "You must provide two consecutive codes.";
            case 2:
                return "Invalid codes.";
            default:
                return "Unknown error.";
        }
    }
}
