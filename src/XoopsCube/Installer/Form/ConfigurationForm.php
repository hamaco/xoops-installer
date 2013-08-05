<?php

namespace XoopsCube\Installer\Form;

class ConfigurationForm
{
    private $url;
    private $xoops_trust_path;
    private $dbHost = 'localhost';
    private $dbUser = 'root';
    private $dbPassword;
    private $dbName = 'xoops';
    private $dbPrefix = 'xoops';
    private $adminUsername = 'admin';
    private $adminEmail = 'admin@example.com';
    private $adminPassword = 'admin';
    private $adminPasswordVerify = 'admin';
    private $salt;
    private $errors = array();

    public function __construct()
    {
        $this->salt = sha1(uniqid());
    }

    public function isMethod($method)
    {
        return ( $_SERVER['REQUEST_METHOD'] === $method );
    }

    public function setXoopsTrustPath($path)
    {
        $this->xoops_trust_path = $path;

        return $this;
    }

    public function getXoopsTrustPath()
    {
        return $this->xoops_trust_path;
    }

    public function setURL($url)
    {
        $this->url = $url;

        return $this;
    }

    public function getURL()
    {
        return $this->url;
    }

    public function getDBHost()
    {
        return $this->dbHost;
    }

    public function getDBUser()
    {
        return $this->dbUser;
    }

    public function getDBPassword()
    {
        return $this->dbPassword;
    }

    public function getDBName()
    {
        return $this->dbName;
    }

    public function getDBPrefix()
    {
        return $this->dbPrefix;
    }

    public function getAdminUsername()
    {
        return $this->adminUsername;
    }

    public function getAdminEmail()
    {
        return $this->adminEmail;
    }

    public function getAdminPassword()
    {
        return $this->adminPassword;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function fetch(array $data)
    {
        $this->dbHost = $data['dbHost'];
        $this->dbUser = $data['dbUser'];
        $this->dbPassword = $data['dbPassword'];
        $this->dbName = $data['dbName'];
        $this->dbPrefix = $data['dbPrefix'];
        $this->url = $data['url'];
        $this->xoops_trust_path = $data['xoops_trust_path'];
        $this->adminUsername = $data['adminUsername'];
        $this->adminEmail = $data['adminEmail'];
        $this->adminPassword = $data['adminPassword'];
        $this->adminPasswordVerify = $data['adminPasswordVerify'];
    }

    public function isValid()
    {
        $this->errors = array();

        // TODO >> 形式的な書き方を宣言的な書き方に変える
        if (empty($this->dbHost)) {
            $this->errors[] = "データベースのホスト名を入力してください";
        }

        if (empty($this->dbUser)) {
            $this->errors[] = "データベースユーザ名を入力してください";
        }

        if (empty($this->dbName)) {
            $this->errors[] = "データベース名を入力してください";
        }

        if (empty($this->dbPrefix)) {
            $this->errors[] = "テーブル接頭語を入力してください";
        }

        if (empty($this->adminUsername)) {
            $this->errors[] = "管理者ユーザ名を入力してください";
        }

        if (empty($this->adminEmail)) {
            $this->errors[] = "管理者メールアドレスを入力してください";
        }

        if (empty($this->adminPassword)) {
            $this->errors[] = "管理者パスワードを入力してください";
        }

        if ($this->adminPassword != $this->adminPasswordVerify) {
            $this->errors[] = "管理者パスワードが一致しません";
        }

        return ( count($this->errors) === 0 );
    }

    public function addError($error)
    {
        $this->errors[] = $error;
    }

    public function hasError()
    {
        return ( count($this->errors) > 0 );
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
