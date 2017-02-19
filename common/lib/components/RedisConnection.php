<?php
namespace common\lib\components;

use \yii\redis\Connection;
use yii\db\Exception;
/**
 * 重写redis连接组件, 主要修改getIsActive和open方法. 因为框架默认getIsActive方法并不能检查
 * redis服务器是否正常, 所以将open方法处检查逻辑放到了getIsActive中进行了对组件的重写
 * @author Yong
 */
class RedisConnection extends Connection
{
    /**
     * @var string 当前redis unixSocket或者tcp连接串
     */
    private $connection;
    /**
     * @var int socket流连接失败错误码
     */
    private $errorNumber;
    /**
     * @var string socket流连接失败错误描述
     */
    private $errorDescription;
    /**
     * @var resource redis socket connection
     */
    private $_socket = false;
    
    /**
     * Returns a value indicating whether the DB connection is established.
     * @return boolean whether the DB connection is established
     */
    public function getIsActive()
    {
        if ($this->_socket === false) {
            $this->connection = ($this->unixSocket ?: $this->hostname . ':' . $this->port) . ', database=' . $this->database;
            \Yii::trace('Opening redis DB connection: ' . $this->connection, __METHOD__);
            $this->_socket = @stream_socket_client(
                $this->unixSocket ? 'unix://' . $this->unixSocket : 'tcp://' . $this->hostname . ':' . $this->port,
                $this->errorNumber,
                $this->errorDescription,
                $this->connectionTimeout ? $this->connectionTimeout : ini_get("default_socket_timeout"),
                $this->socketClientFlags
            );
        } 
        return $this->_socket;
    }
    
    /**
     * Establishes a DB connection.
     * It does nothing if a DB connection has already been established.
     * @throws Exception if connection fails
     */
    public function open()
    {
        if ($this->_socket !== false) {
            return;
        }
        
        if ($this->getIsActive()) {
            if ($this->dataTimeout !== null) {
                stream_set_timeout($this->_socket, $timeout = (int) $this->dataTimeout, (int) (($this->dataTimeout - $timeout) * 1000000));
            }
            if ($this->password !== null) {
                $this->executeCommand('AUTH', [$this->password]);
            }
            $this->executeCommand('SELECT', [$this->database]);
            $this->initConnection();
        } else {
            \Yii::error("Failed to open redis DB connection ({$this->connection}): {$this->errorNumber} - {$this->errorDescription}", __CLASS__);
            $message = YII_DEBUG ? "Failed to open redis DB connection ({$this->connection}): {$this->errorNumber} - {$this->errorDescription}" : 'Failed to open DB connection.';
            throw new Exception($message, $this->errorDescription, (int) $this->errorNumber);
        }
    }
    
    
    /**
     * Executes a redis command.
     * For a list of available commands and their parameters see http://redis.io/commands.
     *
     * @param string $name the name of the command
     * @param array $params list of parameters for the command
     * @return array|boolean|null|string Dependent on the executed command this method
     * will return different data types:
     *
     * - `true` for commands that return "status reply" with the message `'OK'` or `'PONG'`.
     * - `string` for commands that return "status reply" that does not have the message `OK` (since version 2.0.1).
     * - `string` for commands that return "integer reply"
     *   as the value is in the range of a signed 64 bit integer.
     * - `string` or `null` for commands that return "bulk reply".
     * - `array` for commands that return "Multi-bulk replies".
     *
     * See [redis protocol description](http://redis.io/topics/protocol)
     * for details on the mentioned reply types.
     * @throws Exception for commands that return [error reply](http://redis.io/topics/protocol#error-reply).
     */
    public function executeCommand($name, $params = [])
    {
        $this->open();
    
        array_unshift($params, $name);
        $command = '*' . count($params) . "\r\n";
        foreach ($params as $arg) {
            $command .= '$' . mb_strlen($arg, '8bit') . "\r\n" . $arg . "\r\n";
        }
    
        \Yii::trace("Executing Redis Command: {$name}", __METHOD__);
        fwrite($this->_socket, $command);
    
        return $this->parseResponse(implode(' ', $params));
    }
    
    /**
     * @param string $command
     * @return mixed
     * @throws Exception on error
     */
    private function parseResponse($command)
    {
        if (($line = fgets($this->_socket)) === false) {
            throw new Exception("Failed to read from socket.\nRedis command was: " . $command);
        }
        $type = $line[0];
        $line = mb_substr($line, 1, -2, '8bit');
        switch ($type) {
            case '+': // Status reply
                if ($line === 'OK' || $line === 'PONG') {
                    return true;
                } else {
                    return $line;
                }
            case '-': // Error reply
                throw new Exception("Redis error: " . $line . "\nRedis command was: " . $command);
            case ':': // Integer reply
                // no cast to int as it is in the range of a signed 64 bit integer
                return $line;
            case '$': // Bulk replies
                if ($line == '-1') {
                    return null;
                }
                $length = $line + 2;
                $data = '';
                while ($length > 0) {
                    if (($block = fread($this->_socket, $length)) === false) {
                        throw new Exception("Failed to read from socket.\nRedis command was: " . $command);
                    }
                    $data .= $block;
                    $length -= mb_strlen($block, '8bit');
                }
    
                return mb_substr($data, 0, -2, '8bit');
            case '*': // Multi-bulk replies
                $count = (int) $line;
                $data = [];
                for ($i = 0; $i < $count; $i++) {
                    $data[] = $this->parseResponse($command);
                }
    
                return $data;
            default:
                throw new Exception('Received illegal data from redis: ' . $line . "\nRedis command was: " . $command);
        }
    }
    
    /**
     * Closes the currently active DB connection.
     * It does nothing if the connection is already closed.
     */
    public function close()
    {
        if ($this->_socket !== false) {
            $connection = ($this->unixSocket ?: $this->hostname . ':' . $this->port) . ', database=' . $this->database;
            \Yii::trace('Closing DB connection: ' . $connection, __METHOD__);
            $this->executeCommand('QUIT');
            stream_socket_shutdown($this->_socket, STREAM_SHUT_RDWR);
            $this->_socket = null;
        }
    }
    
    
}
