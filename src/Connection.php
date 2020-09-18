<?php

namespace CincoDeMauro\LaravelFilebase;

use Illuminate\Database\Connection as BaseConnection;
use Illuminate\Support\Arr;

use InvalidArgumentException;

class Connection extends BaseConnection
{
    /**
     * The database handler.
     * @var \Database
     */
    protected $db;

    /**
     * The connection handler.
     * @var \Client
     */
    protected $connection;

    /**
     * Create a new database connection instance.
     * @param array $config
     */
    public function __construct(array $config)
    {
        // Create the connection
        $this->db = $this->createConnection($config);

        // Get default database name
//        $default_db = $this->getDefaultDatabaseName($dsn, $config);

        // Select database
//        $this->db = $this->connection->selectDatabase($default_db);

        $this->useDefaultPostProcessor();

        $this->useDefaultSchemaGrammar();

        $this->useDefaultQueryGrammar();
    }

    public function selectUsingColumns($columns, $wheres = [])
    {
        $query = $this->db;

        if($columns[0] != '*'){
            $query = $query->select($columns);
        }

        foreach ($wheres as $where){
            $query = $query->where($where['column'], strtoupper($where['operator']), $where['value']);
        }

        return $query->results();
    }

    public function insert($query, $bindings = [])
    {
        return $this->statement($query, $bindings);
    }

    /**
     * Begin a fluent query against a database collection.
     * @param string $collection
     * @return Query\Builder
     */
    public function collection($collection)
    {
        $query = new \CincoDeMauro\LaravelFilebase\Query\Builder($this, $this->getDefaultQueryGrammar(), $this->getDefaultPostProcessor());

        return $query->from($collection);
    }

    /**
     * Begin a fluent query against a database collection.
     * @param string $table
     * @param string|null $as
     * @return Query\Builder
     */
    public function table($table, $as = null)
    {
        return $this->collection($table);
    }

    /**
     * Get a collection.
     * @param string $name
     * @return Collection
     */
    public function getCollection($name)
    {
        return $this->db;
    }

    /**
     * @inheritdoc
     */
    public function getSchemaBuilder()
    {
        return new Schema\Builder($this);
    }

    /**
     * Get the database object.
     * @return \Database
     */
    public function ge()
    {
        return $this->db;
    }

    /**
     * {@inheritdoc}
     */
    public function getDatabaseName()
    {
        return $this->ge()->getDatabaseName();
    }

    /**
     * Create a new connection.
     * @param string $dsn
     * @param array $config
     * @param array $options
     * @return \Client
     */
    protected function createConnection(array $config)
    {
        $default = [
            'dir'            => 'path/to/database/dir',
            'backupLocation' => 'path/to/database/backup/dir',
            'format'         => \Filebase\Format\Json::class,
            'cache'          => true,
            'cache_expires'  => 1800,
            'pretty'         => true,
            'safe_filename'  => true,
            'read_only'      => false
        ];

        $config = config('database.connections.filebase', []);

        $config = array_merge($default, $config);

        return new \CincoDeMauro\LaravelFilebase\Firebase\Client($config);
    }

    /**
     * @inheritdoc
     */
    public function disconnect()
    {
        unset($this->connection);
    }

    /**
     * Determine if the given configuration array has a dsn string.
     * @param array $config
     * @return bool
     */
    protected function hasDsnString(array $config)
    {
        return isset($config['dsn']) && !empty($config['dsn']);
    }

    /**
     * Get the DSN string form configuration.
     * @param array $config
     * @return string
     */
    protected function getDsnString(array $config)
    {
        return $config['dsn'];
    }

    /**
     * @inheritdoc
     */
    public function getElapsedTime($start)
    {
        return parent::getElapsedTime($start);
    }

    /**
     * @inheritdoc
     */
    public function getDriverName()
    {
        return 'filebase';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultPostProcessor()
    {
        return new \CincoDeMauro\LaravelFilebase\Query\Processor();
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultQueryGrammar()
    {
        return new \CincoDeMauro\LaravelFilebase\Query\Grammar();
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultSchemaGrammar()
    {
        return new \CincoDeMauro\LaravelFilebase\Schema\Grammar();
    }

    /**
     * Dynamically pass methods to the connection.
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->db, $method], $parameters);
    }
}
