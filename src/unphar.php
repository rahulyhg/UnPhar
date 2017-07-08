<?php

class UnPhar {

    const NAME = "UnPhar";
    const VERSION = "v1.1";
    const AUTHOR = "KennFatt";

    const INVALID_MESSAGE = 0;
    const INVALID_INPUT = 1;

    /** @var \Phar|null */
    private $pharFile = null;

    /** @var string */
    private $outputPath = "";
    private $tempInput = "";

    public function __construct()
    {
        $this->init();
    }

    /**
     * Initiate program
     */
    public function init()
    {
        cli_set_process_title(UnPhar::NAME . " - " . UnPhar::VERSION . " @" . UnPhar::AUTHOR);

        $this->sendMessage("
        Hello! This project is used for extracting Phar files (PHP Archiver) to source code.
        Creator: @KennFatt
        Github: https://www.github.com/KennFatt
        ");

        $this->sendMessage("
        Do you want execute this program? (Type y for yes).
        ");

        if (strtolower($this->readLine()) === "y") {
            $this->processExecute();
        } else {
            $this->close("Ignoring agreement!");
        }
    }

    /**
     * Close the program.
     * 
     * @return mixed
     */
    public function close(string $message = "")
    {
        if (isset($this->pharFile)) $this->pharFile = null;
        if (isset($this->outputPath)) $this->outputPath = "";

        if ($message !== "") {
            $this->sendMessage($message);
        }

        $this->sendMessage("Thank you for using " . UnPhar::NAME . "!");
        exit;
    }

    /**
     * Extracting process.
     *
     * @return mixed
     */
    public function processExecute()
    {
        $pharName = "";

        $this->sendMessage("Please insert Phar name (Ex: Lib.phar): ");
        
        if ($this->readLine() !== "" and strpos($this->tempInput, ".phar")) {

            if (!is_file($this->tempInput)) {
                $this->close("Invalid Phar file!");
            }

            $this->pharFile = new Phar($this->tempInput);
            $pharName = explode(".", $this->tempInput)[0];
        } else {
            $this->errorCause(UnPhar::INVALID_INPUT);
        }

        $this->sendMessage("Please insert a path for extracting data (Ex: C:\Users\KENNAN\Desktop\): ");
        
        if ($this->readLine() !== "") {
            if (is_dir($this->tempInput)) {
                $this->outputPath = $this->tempInput;

                if (!is_dir($this->outputPath.$pharName."-master")) @mkdir($this->outputPath.$pharName."-master");
                $this->outputPath = $this->outputPath.$pharName."-master";

            } else {
                $this->close("Invalid directory!");
            }
        } else {
            $this->errorCause(UnPhar::INVALID_INPUT);
        }

        $this->sendMessage("Extracting a phar, please wait...");
        $this->pharFile->extractTo($this->outputPath, null, true);
        $this->sendMessage("Extracting succeed! File located at " . $this->outputPath);
    }

    /**
     * Sending message to client.
     *
     * @param string $message
     *
     * @return mixed
     */
    public function sendMessage(string $message)
    {
        $message = $message !== "" ? $message : $this->errorCause(UnPhar::INVALID_MESSAGE);
        echo $message . PHP_EOL;
    }

    /**
     * Get input from STDIN.
     *
     * @return string
     */
    public function readLine() : string
    {
        $input = trim((string) fgets(STDIN));
        $this->tempInput = $input;
        return $input;
    }

    /**
     * A Function to handle error.
     *
     * @param int $cause
     *
     * @return bool
     */
    public function errorCause(int $cause) : bool
    {
        switch ($cause) {
            case UnPhar::INVALID_INPUT:
                $this->close("Invalid input! Input must be a string and not null!");
                return true;
            
            case UnPhar::INVALID_MESSAGE:
                $this->close("Invalid message! Message must be a string and not null!");
                return true;
            
            default:
                $this->close("[Error] @param $cause is unknown.");
                return false;
        }
    }
}

function run() {
    $class = new UnPhar();
}

run();